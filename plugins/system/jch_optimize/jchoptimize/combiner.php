<?php

use JchOptimize\JSMin;
use JchOptimize\Minify_CSS;
use JchOptimize\lessc;

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
class JchOptimizeCombiner extends JchOptimizeBase
{

        public $params            = null;
        protected $sLnEnd         = '';
        protected $sTab           = '';
        protected $aCallBackArgs  = array();
        public static $bLogErrors = false;

        /**
         * Constructor
         * 
         * @param type $params
         */
        public function __construct($params)
        {
                $this->params = $params;

                $oDocument    = JFactory::getDocument();
                $this->sLnEnd = $oDocument->_getLineEnd();
                $this->sTab   = $oDocument->_getTab();

                self::$bLogErrors = $this->params->get('jsmin_log', 0) ? TRUE : FALSE;
        }

        public function getLogParam()
        {
                if (self::$bLogErrors == '')
                {
                        self::$bLogErrors = $this->params->get('log', 0);
                }

                return self::$bLogErrors;
        }

        /**
         * Get aggregated and possibly minified content from js and css files
         *
         * @param array $aUrlArray      Array of urls of css or js files for aggregation
         * @param string $sType         css or js
         * @return string               Aggregated (and possibly minified) contents of files
         */
        public static function getContents($aUrlArray, $sType, $params)
        {
                $oCombiner = new JchOptimizeCombiner($params);

                $sContents = $oCombiner->combineFiles($aUrlArray, $sType);
                $sContents = $oCombiner->prepareContents($sContents, $sType);

                return $sContents;
        }

        /**
         * Aggregate contents of CSS and JS files
         *
         * @param array $aUrlArray      Array of links of files
         * @param string $sType          CSS or js
         * @return string               Aggregarted contents
         * @throws Exception
         */
        public function combineFiles($aUrlArray, $sType)
        {
                global $_PROFILER;
                JCH_DEBUG ? $_PROFILER->mark('beforeCombineFiles - ' . $sType . ' plgSystem (JCH Optimize)') : null;

                $sContents = '';
                $bAsync    = false;
                $sAsyncUrl = '';

                foreach ($aUrlArray as $sUrl)
                {
                        $sContent = '';

                        if (isset($sUrl['url']))
                        {
                                if ($sType == 'js' && $sAsyncUrl != '')
                                {
                                        $sContents .= ' COMMENT_START ' . $sAsyncUrl . ' COMMENT_END 
                                                loadScript("' . $sAsyncUrl . '", function(){});  DELIMITER';
                                        $sAsyncUrl = '';
                                }

                                $sPath = $sUrl['path'];

                                if ($sType == 'js' && $this->loadAsync($sPath))
                                {
                                        $sAsyncUrl = $sUrl['url'];
                                        $bAsync    = true;

                                        continue;
                                }
                                else
                                {
                                        if (!JchOptimizeHelper::fileExists($sPath))
                                        {
                                                if ($this->params->get('log', 0))
                                                {
                                                        JchOptimizeLogger::log(JText::_('File not found: ' . $sPath));
                                                }

                                                continue;
                                        }

                                        $sFileContents = file_get_contents($sPath);

                                        if ($sFileContents === FALSE)
                                        {
                                                throw new Exception(JText::_('Failed getting file contents from ' . $sPath));
                                        }

                                        $sContent .= $sFileContents;
                                        unset($sFileContents);
                                }
                        }
                        else
                        {
                                if ($sType == 'js' && $sAsyncUrl != '')
                                {
                                        $sContents .= ' COMMENT_START ' . $sAsyncUrl . ' COMMENT_END 
                                                loadScript("' . $sAsyncUrl . '", function(){' . $this->sLnEnd . $sUrl['content'] . $this->sLnEnd . '});  
                                        DELIMITER';
                                        $sAsyncUrl = '';
                                }
                                else
                                {
                                        $sContent .= $sUrl['content'];
                                }
                        }

                        $sContent = trim($sContent);

                        if ($sType == 'css')
                        {
                                unset($this->aCallbackArgs['css_url']);
                                $this->aCallbackArgs['css_url'] = isset($sUrl['url']) ? $sUrl['url'] : '/';

                                $sContent = preg_replace('#@import (?:url\()?[\'"]([^\'"]+)[\'"](?:\))?#', '@import url($1)', $sContent);
                                $sContent = $this->correctUrl($sContent);
                                $sContent = $this->replaceImports($sContent, $sUrl);
                                $sContent = $this->handleMediaQueries($sContent, $sUrl);

                                if ($this->params->get('css_minify', 0))
                                {
                                        if (($sMinContent = $this->isMinified($sContent)) === FALSE)
                                        {
                                                JCH_DEBUG ? $_PROFILER->mark('beforeMinifyCss - "' . $sUrl['url'] . '" plgSystem (JCH Optimize)') : null;

                                                $sContent = Minify_CSS::process($sContent);

                                                JCH_DEBUG ? $_PROFILER->mark('afterMinifyCss - "' . $sUrl['url'] . '" plgSystem (JCH Optimize)') : null;
                                        }
                                        else
                                        {
                                                $sContent = $sMinContent;
                                                unset($sMinContent);
                                        }
                                }
                        }

                        if ($sType == 'js' && $sContent != '')
                        {
                                if ($this->params->get('js_minify', 0))
                                {
                                        if (($sMinContent = $this->isMinified($sContent)) === FALSE)
                                        {
                                                $sJsUrl = isset($sUrl['url']) ? $sUrl['url'] : 'Script Declaration';
                                                JCH_DEBUG ? $_PROFILER->mark('beforeMinifyJs - "' . $sJsUrl . '" plgSystem (JCH Optimize)') : null;

                                                $sContent = $this->minifyJS($sContent, $sJsUrl);

                                                JCH_DEBUG ? $_PROFILER->mark('afterMinifyJs - "' . $sJsUrl . '" plgSystem (JCH Optimize)') : null;
                                        }
                                        else
                                        {
                                                $sContent = $sMinContent;
                                                unset($sMinContent);
                                        }
                                }

                                $sContent = $this->addSemiColon($sContent);
                        }

                        $sContents .= ' COMMENT_START ' . (isset($sUrl['url']) ? $sUrl['url'] : 'script declaration') . ' COMMENT_END ' . trim($sContent) . 'DELIMITER';
                        unset($sContent);
                }

                if ($bAsync)
                {
                        $sContents = $this->getLoadScript() . $sContents;

                        if ($sAsyncUrl != '')
                        {
                                $sContents .= ' COMMENT_START ' . $sAsyncUrl . ' COMMENT_END loadScript("' . $sAsyncUrl . '", function(){});  DELIMITER';
                                $sAsyncUrl = '';
                        }
                }

                JCH_DEBUG ? $_PROFILER->mark('afterCombineFiles - ' . $sType . ' plgSystem (JCH Optimize)') : null;

                return $sContents;
        }

        /**
         * Add semi-colon to end of js files if non exists;
         * 
         * @param string $sContent
         * @return string
         */
        public function addSemiColon($sContent)
        {
                if (substr(trim($sContent), -1) != ';')
                {
                        $sContent = $sContent . ';';
                }

                return $sContent;
        }

        /**
         * Checks if content is already minified
         * 
         * @param type $sContent
         * @return boolean
         */
        public function isMinified($sContent)
        {
                if (strlen($sContent) == 0)
                {
                        return $sContent;
                }

                $sContent     = trim(preg_replace('#^(?:\s*/(?:\*.*?\*/|/.*?$))+#sm', '', $sContent));
                $iWhiteSpaces = preg_match_all('#[\s\r\n]#', $sContent, $aMatches);

                return ($iWhiteSpaces / strlen($sContent) < 0.09) ? $sContent : FALSE;
        }

        /**
         * Remove placeholders from aggregated file for caching
         * 
         * @param string $sContents       Aggregated file contents
         * @param string $sType           js or css
         * @return string
         */
        public function prepareContents($sContents, $sType)
        {
                $sContents = str_replace(
                        array(
                        'COMMENT_START',
                        'COMMENT_IMPORT_START',
                        'COMMENT_END',
                        'DELIMITER'
                        ),
                        array(
                        $this->sLnEnd . $this->sLnEnd . '/** ',
                        $this->sLnEnd . $this->sLnEnd . '/** @import url',
                        ' */' . $this->sLnEnd . $this->sLnEnd,
                        ''
                        ), $sContents);

                if ($sType == 'css')
                {
                        $sContents = trim($this->sortImports($sContents));

                        if ($this->params->get('csg_enable', 0))
                        {
                                try
                                {
                                        $oSpriteGenerator = new JchOptimizeSpriteGenerator($this->params);
                                        $sCssContents     = $oSpriteGenerator->getSprite($sContents);
                                }
                                catch (Exception $ex)
                                {
                                        if ($this->params->get('log', 0))
                                        {
                                                JchOptimizeLogger::log($ex->getMessage());
                                        }

                                        return $sContents;
                                }

                                $sContents = $sCssContents;
                        }

                        if (function_exists('mb_convert_encoding'))
                        {
                                $sContents = '@charset "utf-8";' . $this->sLnEnd . $sContents;
                                //$sContents = mb_convert_encoding($sContents, 'utf-8');
                        }
                }

                return trim($sContents);
        }

        //regex for media query: /@media[^{]*\{(?:(?!\}\s*\}).)\}\s*\}*/gis
        protected function handleMediaQueries($sContent, $sUrl)
        {
                $sCommentRegex = '#COMMENT_START.*?COMMENT_END#';

                if (isset($sUrl['media']) && ($sUrl['media'] != ''))
                {
                        $sContent = preg_replace(array($sCommentRegex, '#DELIMITER#'), '', $sContent);
                        $sContent = '@media ' . $sUrl['media'] . ' {' . $this->sLnEnd . $sContent . $this->sLnEnd . ' }';
                        $sContent = $this->lessCompile($sContent, $sUrl);
//                        $sContent = $this->removeNestedMediaQueries($sContent, $sUrl);
                        $sContent = $this->removeAtRules($sContent, '#@[^{]*(?:font-face|page|keyframes|supports|document)[^{]*({(?>[^{}]|(?1))*})#',
                                                         $sUrl);
                }

                return $sContent;
        }

        public function removeAtRules($sContent, $sAtRulesRegex, $sUrl = array('url' => 'CSS'))
        {
                if (preg_match_all($sAtRulesRegex, $sContent, $aMatches) === FALSE)
                {
                        if ($this->params->get('log', 0))
                        {
                                JchOptimizeLogger::log(JText::_('Error parsing for at rules in ' . $sUrl['url']));
                        }

                        return $sContent;
                }

                $sAtRules = '';

                foreach ($aMatches[0] as $sAtRule)
                {
                        $sAtRule = (strpos($sAtRule, 'charset') === FALSE) ? $sAtRule : '';
                        $sAtRules .= $sAtRule . $this->sLnEnd;
                }

                $sContentReplaced = str_replace($aMatches[0], '', $sContent);

                if (is_null($sContentReplaced))
                {
                        if ($this->params->get('log', 0))
                        {
                                JchOptimizeLogger::log(JText::_('Error removing at rules from content in ' . $sUrl['url']));
                        }

                        return $sContent;
                }

                $sContent = $sAtRules . $sContentReplaced;

                return $sContent;
        }

        /**
         * Pass CSS contents containing media queries through a lesscss compiler
         * to get rid of nested queries as not supported by every browser
         * 
         * @param type $sContent
         * @param type $sUrl
         * @return type
         * @throws Exception
         */
        public function lessCompile($sContent, $sUrl)
        {
                try
                {
                        $less         = new lessc;
                        $slessContent = $less->compile($sContent);
                }
                catch (Exception $ex)
                {
                        if ($this->params->get('log', 0))
                        {
                                JchOptimizeLogger::log($ex->getMessage(), $sUrl['url']);
                        }

                        return $sContent;
                }

                $sContent = $slessContent;

                return $sContent;
        }

        /**
         * Minify js files, will write exceptions to log
         * 
         * @param string $sContent
         * @param string $sUrl
         * @return type
         */
        public static function minifyJs($sContent, $sUrl = '')
        {
                $sMessage = '';

                try
                {
                        $sContentMin = JSMin::minify($sContent);
                }
                catch (JSMin_UnterminatedStringException $e)
                {
                        $sMessage    = 'Caught JSMin_UnterminatedStringException: ' . $e->getMessage();
                        $sContentMin = $sContent;
                }
                catch (JSMin_UnterminatedCommentException $e)
                {
                        $sMessage    = 'Caught JSMin_UnterminatedCommentException: ' . $e->getMessage();
                        $sContentMin = $sContent;
                }
                catch (JSMin_UnterminatedRegExpException $e)
                {
                        $sMessage    = 'Caught JSMin_UnterminatedRegExpException: ' . $e->getMessage();
                        $sContentMin = $sContent;
                }
                catch (Exception $e)
                {
                        $sMessage    = 'Caught Exception: ' . $e->getMessage();
                        $sContentMin = $sContent;
                }

                if (static::$bLogErrors && $sMessage != '')
                {
                        $sUrl = $sUrl != '' ? $sUrl : 'script';
                        JchOptimizeLogger::log($sMessage, $sUrl);
                }

                return $sContentMin;
        }

        /**
         * Converts url of background images in css files to absolute path
         * 
         * @param string $sContent
         * @return string
         */
        protected function correctUrl($sContent)
        {
                $sCorrectedContent = preg_replace_callback('#url\([\'"]?([^\'"\)]+)[\'"]?\)#i', array(__CLASS__, 'correctUrlCallback'), $sContent);

                if (is_null($sCorrectedContent))
                {
                        throw new Exception(JText::_('Failed correcting urls of background images'));
                }

                $sContent = $sCorrectedContent;

                return $sContent;
        }

        /**
         * Callback function to correct urls in aggregated css files
         *
         * @param array $aMatches Array of all matches
         * @return string         Correct url of images from aggregated css file
         */
        protected function correctUrlCallback($aMatches)
        {
                $oUri        = JUri::getInstance(JUri::base());
                $sUriBase    = JUri::base(TRUE);
                $sImageUrl   = $aMatches[1];
                $sCssFileUrl = $this->aCallbackArgs['css_url'];

                if (!preg_match('#^/|://#', $sImageUrl))
                {
                        $aCssUrlArray = explode('/', $sCssFileUrl);
                        array_pop($aCssUrlArray);
                        $sCssRootPath = implode('/', $aCssUrlArray) . '/';
                        $sImagePath   = $sCssRootPath . $sImageUrl;
                        $oUri         = JURI::getInstance($sImagePath);
                        $sUriPath     = preg_replace('#^' . preg_quote($sUriBase, '#') . '/#', '', $oUri->getPath());
                        $oUri->setPath($sUriBase . '/' . $sUriPath);
                        $sImageUrl    = $oUri->toString();
                }

                if (JchOptimizeHelper::isInternal($sCssFileUrl))
                {
                        if (JchOptimizeHelper::isInternal($sImageUrl))
                        {
                                $oUri      = JURI::getInstance($sImageUrl);
                                $sImageUrl = $oUri->toString(array('path', 'query', 'fragment'));
                        }
                }

                return 'url(' . $sImageUrl . ')';
        }

        /**
         * Sorts @import and @charset as according to w3C <http://www.w3.org/TR/CSS2/cascade.html> Section 6.3
         *
         * @param string $sCss       Combined css
         * @return string           CSS with @import and @charset properly sorted
         * @todo                     replace @imports with media queries
         */
        public function sortImports($sCss)
        {
                $sCssMediaImports = preg_replace_callback('#@media\s?([^{]+){(?=[^}]*@import)([^}]+)}#i', array(__CLASS__, 'sortImportsCallback'),
                                                          $sCss);

                if (is_null($sCssMediaImports))
                {
                        if ($this->params->get('log', 0))
                        {
                                JchOptimizeLogger::log('Failed matching for imports within media queries in css');
                        }

                        return $sCss;
                }

                $sCss = $sCssMediaImports;

                $sCss = $this->removeAtRules($sCss, '#@(?:import|charset)[^;}]+(?:;|.(?=\}))#i');

                return $sCss;
        }

        /**
         * Callback function for sort Imports
         * 
         * @param type $aMatches
         * @return string
         */
        protected function sortImportsCallback($aMatches)
        {
                $sImports = preg_replace_callback('#(@import\surl\([^)]+\))([^;]*);#',
                                                  function($aM)
                {
                        if (!empty($aM[2]))
                        {
                                return $aM[1] . $aM[2] . ' and MEDIA;';
                        }
                        else
                        {
                                return $aM[1] . ' MEDIA;';
                        }
                }, $aMatches[2]);

                $sImports = str_replace('MEDIA', trim($aMatches[1]), $sImports);
                $sCss     = str_replace($aMatches[2], $sImports, $aMatches[0]);

                return $sCss;
        }

        
}
