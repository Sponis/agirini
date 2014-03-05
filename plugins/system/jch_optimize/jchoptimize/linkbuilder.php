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
 * 
 * 
 */
class JchOptimizeLinkBuilder extends JchOptimizeBase
{

        /** @var JchOptimizeParser Object       Parser object */
        protected $oParser;

        /** @var string         Document line end */
        protected $sLnEnd;

        /** @var string         Document tab */
        protected $sTab;

        /** @var object   Clone of JCACHE object */
        protected $oCache = null;

        /** @var string   Cache group */
        protected $sCacheGroup = 'plg_jch_optimize';

        /**
         * Constructor
         * 
         * @param JchOptimizeParser Object  $oParser
         */
        public function __construct($oParser)
        {
                $this->oParser = $oParser;
                $this->params  = $oParser->params;

                $oDocument    = JFactory::getDocument();
                $this->sLnEnd = $oDocument->_getLineEnd();
                $this->sTab   = $oDocument->_getTab();
        }

        /**
         * Prepare links for the combined files and insert them in the processed HTML
         * 
         */
        public function insertJchLinks()
        {
                $aLinks = $this->oParser->getReplacedFiles();
                $params = $this->params;

                if ($params->get('javascript', 1))
                {
                        $sLink = '<script type="text/javascript" src="URL"';
                        $sLink .= $params->get('defer_js', 0) ?
                                ($this->isXhtml() ? ' defer="defer"' : ' defer' ) :
                                '';
                        $sLink .= $this->getAsyncAttribute();
                        $sLink .= '></script>';

                        $sNewJsLink = $params->get('bottom_js', 1) ? $this->sTab . $sLink . $this->sLnEnd . '</body>' : $sLink;

                        if (!empty($aLinks['js']))
                        {
                                $this->processLink('js', $sNewJsLink, $this->oParser->aCallbackArgs['counter']);
                        }
                }

                if ($params->get('css', 1) || $params->get('csg_enable', 0))
                {
                        $sNewCssLink = '</title>' . $this->sLnEnd . $this->sTab . '<link rel="stylesheet" type="text/css" ';
                        $sNewCssLink .= 'href="URL"/>';

                        if (!empty($aLinks['css']))
                        {
                                $this->processLink('css', $sNewCssLink);
                        }
                }
        }

        /**
         * Use generated id to cache aggregated file
         *
         * @param string $sType           css or js
         * @param string $sLink           Url for aggregated file
         */
        protected function processLink($sType, $sLink, $iCnt = '')
        {
                $aLinks    = $this->oParser->getReplacedFiles();
                $sId       = md5(serialize($aLinks[$sType]) . $this->params);
                $aArgs     = array($aLinks[$sType], $sType, $this->params);
                $aFunction = array('JchOptimizeCombiner', 'getContents');

                $bCached = $this->loadCache($aFunction, $aArgs, $sId);

                if ($bCached === FALSE)
                {
                        throw new Exception(JText::_('Error creating cache file'));
                }

                $sFileName = $this->getFilename($sId);
                $iTime     = (int) $this->params->get('lifetime', '30');
                $sUrl      = $this->buildUrl($sFileName, $sType, $iTime, $this->isGZ());
                $sNewLink  = str_replace('URL', $sUrl, $sLink);
                $this->replaceLink($sNewLink, $sType, $iCnt);

//print_r($sFile);
                return $sId;
        }

        /**
         * Returns url of aggregated file
         *
         * @param string $sFile		Aggregated file name
         * @param string $sType		css or js
         * @param mixed $bGz		True (or 1) if gzip set and enabled
         * @param number $sTime		Expire header time
         * @return string			Url of aggregated file
         */
        protected function buildUrl($sFile, $sType, $iTime, $bGz = false)
        {
                $oUri = clone JUri::getInstance(JUri::base());
                $sPath = JUri::base(true) . '/plugins/system/jch_optimize/assets/';
                
                if ($this->params->get('htaccess', 0))
                {
                        $sUrl = $sPath . ($bGz ? 'gz/' : 'nz/') . $iTime . '/' . $sFile . '.' . $sType;
                }
                else
                {
                        $oUri->setPath($sPath . 'jscss.php');

                        $aVar         = array();
                        $aVar['f']    = $sFile;
                        $aVar['type'] = $sType;
                        if ($bGz)
                        {
                                $aVar['gz'] = 'gz';
                        }
                        $aVar['d'] = $iTime;
                        $oUri->setQuery($aVar);

                        $sUrl = htmlentities($oUri->toString(array('path', 'query')));
                }
                
                return ($sUrl);
        }

        /**
         * Insert url of aggregated file in html
         *
         * @param string $sNewLink   Url of aggregated file
         */
        protected function replaceLink($sNewLink, $sType, $iCnt = '')
        {
                $sSearchArea = $this->oParser->getSearchArea();

                if ($sType == 'css')
                {
                        $sSearchArea = str_replace('</title>', $sNewLink, $sSearchArea);
                }

                if ($sType == 'js')
                {
                        switch ($this->params->get('bottom_js', 1))
                        {
                                case 0: //First found javascript tag
                                        $sSearchArea = preg_replace('#<JCH_SCRIPT>#', $sNewLink, $sSearchArea, 1);
                                        $sSearchArea = str_replace('<JCH_SCRIPT>', '', $sSearchArea);

                                        break;

                                case 2: //Last found javascript tag
                                        $iCnt--;
                                        $sSearchArea = preg_replace('#<JCH_SCRIPT>#', '', $sSearchArea, $iCnt);
                                        $sSearchArea = str_replace('<JCH_SCRIPT>', $sNewLink, $sSearchArea);

                                        break;

                                case 1: //Bottom of page
                                        $sSearchArea          = str_replace('<JCH_SCRIPT>', '', $sSearchArea);
                                        $sSearchArea          = str_replace('</body>', $sNewLink, $sSearchArea);
                                        $this->oParser->sHtml = str_replace('</body>', $sNewLink, $this->oParser->sHtml);

                                        break;

                                default:
                                        JchOptimizeLogger::log(JText::_('Unknown value for position javascript type parameter'));
                                        $sSearchArea = preg_replace('#<JCH_SCRIPT>#', $sNewLink, $sSearchArea, 1);
                                        $sSearchArea = str_replace('<JCH_SCRIPT>', '', $sSearchArea);

                                        break;
                        }
                }

                $this->oParser->setSearchArea($sSearchArea);
        }

        /**
         * Create and cache aggregated file if it doesn't exists, file will have
         * lifetime set in global configurations.
         *
         * @param array $aFunction    Name of function used to aggregate files
         * @param array $aArgs        Arguments used by function above
         * @param string $sId         Generated id to identify cached file
         * @return boolean           True on success
         */
        protected function loadCache($aFunction, $aArgs, $sId)
        {
                $oCache  = $this->getCache();
                $bCached = $oCache->get($aFunction, $aArgs, $sId);

                if ($bCached !== FALSE)
                {
                        return TRUE;
                }

                return FALSE;
        }

        /**
         * Gets name of aggregated file
         *
         * @param string $sId			Id of cached file
         * @param string $sCacheGroup	Name of cache group
         * @return string				Cache file name
         */
        protected function getFilename($sId)
        {
                $oCache   = $this->getCache();
                $oStorage = $oCache->_getStorage();
                $sName1   = md5($oStorage->_application . '-' . $sId . '-' . $oStorage->_language);
                $sName    = $oStorage->_hash . '-cache-' . $this->sCacheGroup . '-' . $sName1;

                return $sName;
        }

        /**
         * Gets Joomla cache object
         * 
         * @return object         Cache object
         */
        protected function getCache()
        {
                if (is_null($this->oCache))
                {
                        $this->oCache = JFactory::getCache($this->sCacheGroup, 'callback', 'file');
                        $this->oCache->setCaching(1);
                        $this->oCache->setLifetime((int) $this->params->get('lifetime', '30') * 24 * 60 * 60);
                }

                return $this->oCache;
        }

        /**
         * Check if gzip is set or enabled
         *
         * @return boolean   True if gzip parameter set and server is enabled
         */
        public function isGZ()
        {
                $iIsGz = $this->params->get('gzip', 0);

                switch (TRUE)
                {
                        case (!$iIsGz):
                        case (!isset($_SERVER['HTTP_ACCEPT_ENCODING'])):
                        case (!extension_loaded('zlib') || ini_get('zlib.output_compression')):

                                return false;

                        case (false !== strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')):

                                return true;

                        default:

                                JchOptimizeLogger::log(JText_('Can\'t tell if gzip is enabled'));

                                return false;
                }
        }

        /**
         * Determine if document is of XHTML doctype
         * 
         * @return boolean
         */
        protected function isXhtml()
        {
                if (preg_match('#^<!DOCTYPE(?=[^>]+XHTML)#i', trim($this->oParser->sHtml)) === 1)
                {
                        return true;
                }
                else
                {
                        return false;
                }
        }

        
}
