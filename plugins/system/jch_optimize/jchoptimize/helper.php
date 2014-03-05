<?php
/**
 * JCH Optimize - Joomla! plugin to aggregate and minify external resources for
 * optmized downloads
 * @author Samuel Marshall <sdmarshall73@gmail.com>
 * @copyright Copyright (c) 2010 Samuel Marshall
 * @license GNU/GPLv3, See LICENSE file
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * If LICENSE file missing, see <http://www.gnu.org/licenses/>.
 *
 * This plugin, inspired by CssJsCompress <http://www.joomlatags.org>, was
 * created in March 2010 and includes other copyrighted works. See individual
 * files for details.
 */
defined('_JEXEC') or die('Restricted access');

/**
 * Some helper functions
 * 
 */
class JchOptimizeHelper
{
        /**
         * Checks if file (can be external) exists
         * 
         * @param type $sPath
         * @return boolean
         */
        public static function fileExists($sPath)
        {
                return (file_exists($sPath) || @fopen($sPath,"r") != FALSE);
        }
        
        
        /**
         * Get local path of file from the url if internal
         * If external or php file, the url is returned
         *
         * @param string  $sUrl  Url of file
         * @return string       File path
         */
        public static function getFilePath($sUrl)
        {
                $sUriBase = JUri::base();
                $sUriPath = JUri::base(TRUE);

                $oUri = clone JUri::getInstance($sUriBase);

                if (JchOptimizeHelper::isInternal($sUrl) && !preg_match('#\.php#i', $sUrl))
                {
                        $sUrl = preg_replace(
                                array(
                                '#^' . preg_quote($sUriBase, '#') . '#',
                                '#^' . preg_quote($sUriPath, '#') . '/#',
                                '#\?.*?$#'
                                ), '', $sUrl);

                        return JPATH_ROOT . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $sUrl);
                }
                else
                {
                        switch (TRUE)
                        {
                                case preg_match('#://#', $sUrl):

                                        break;

                                case (substr($sUrl, 0, 2) == '//'):

                                        $sUrl = $oUri->toString(array('scheme')) . substr($sUrl, 2);
                                        break;

                                case (substr($sUrl, 0, 1) == '/'):

                                        $sUrl = $oUri->toString(array('scheme', 'host')) . $sUrl;
                                        break;

                                default:

                                        $sUrl = $sUriBase . $sUrl;
                                        break;
                        }

                        return html_entity_decode($sUrl);
                }
        }
                
        
        /**
         * Gets the name of the current Editor
         * 
         * @staticvar string $sEditor
         * @return string
         */
        public static function getEditorName()
        {
                static $sEditor;

                if (!isset($sEditor))
                {
                        $sEditor = JFactory::getUser()->getParam('editor');
                        $sEditor = !isset($sEditor) ? JFactory::getConfig()->get('editor') : $sEditor;
                }

                return $sEditor;
        }
        
        /**
         * Determines if file is internal
         * 
         * @param string $sUrl  Url of file
         * @return boolean
         */
        public static function isInternal($sUrl)
        {
                $oUrl = JUri::getInstance($sUrl);
                //trying to resolve bug in php with parse_url before 5.4.7
                if (preg_match('#^//([^/]+)(/.*)$#i', $oUrl->getPath(), $aMatches))
                {
                        if (!empty($aMatches))
                        {
                                $oUrl->setHost($aMatches[1]);
                                $oUrl->setPath($aMatches[2]);
                        }
                        $sUrl = $oUrl->toString();
                }

                return JUri::isInternal($sUrl);
        }

}