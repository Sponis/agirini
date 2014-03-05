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
class JchOptimizeCssParser
{
        const ROOT   = 1;
        const ATRULE = 2;
        const EOF    = 9;

        public $aIndexStack = array();
        public $aParsedCss  = array();
        private $sCssInput  = '';
        private $iCssLength = 0;
        private $iPos = -1;

        public function __construct($sCssInput)
        {
                $this->sCssInput  = $sCssInput;
                $this->iCssLength = strlen($sCssInput);

                $this->parse();
        }

        public function getChar()
        {
                $this->iPos++;
                
                if ($this->iPos >= $this->iCssLength)
                {
                        return FALSE;
                }

                $sChar = $this->sCssInput[$this->iPos];
                
                if($sChar == '/' && $this->peek('*'))
                {
                        $this->consumeComments($sChar);
                }

                return $sChar;
        }

        public function consumeComments()
        {
                
        }
        
        public function nextBlock()
        {
                while (!is_bool($sChar = $this->getChar()))
                {
                        if ($sChar != '@')
                        {
                                $this->aBlock = $this->consumeRoot();
                        }
                        else
                        {
                                $this->aBlock = $this->consumeAtRule();
                        }

                        return $this->aBlock;
                }

                return self::EOF;
        }

        public function peek($sWord)
        {
                return ($sWord == substr($this->sCssInput, $this->iPos, strlen($sWord)));
        }

        public function consumeRoot()
        {
                $this->pushBackChar();
                $aBlock            = array();
                $aBlock['type']    = self::ROOT;
                $aBlock['content'] = '';

                $this->aIndexStack[] = 0;

                while ($this->insideBlock())
                {
                        $aBlock['content'] .= $sChar;
                }

                array_pop($this->aIndexStack);

                return $aBlock;
        }

        public function consumeAtRule()
        {
                $this->pushBackChar();
                $aBlock          = array();
                $aBlock['type']  = self::ATRULE;
                $aBlock['rule']  = $this->getRule();
                $aBlock['query'] = $this->getQuery();

                $this->aIndexStack[] = 0;

                while ($this->insideBlock())
                {
                        $aBlock['content'][] = $this->nextBlock();
                }

                array_pop($this->aIndexStack);

                return $aBlock;
        }
        
        public function getRule()
        {
                return FALSE;
        }
        
        public function getQuery()
        {
                return FALSE;
        }

        public function advanceToChar($sRegex)
        {
                $sContent = '';

                while (!preg_match('#' . $sRegex . '#', ($sChar = $this->getChar())))
                {
                        $sContent.= $sChar;
                }

                $this->iPos = $this->iPos - strlen($sContent);

                return $sContent;
        }

        public function pushBackChar()
        {
                $this->iPos--;
        }

        public function insideBlock()
        {
                end($this->aIndexStack);
                $iIndex = &$this->aIndexStack[key($this->aIndexStack)];

                $sChar = $this->getChar();
                
                if ($sChar == '{')
                {
                        ++$iIndex;
                }

                if ($sChar == '}')
                {
                        --$iIndex;
                }

                return !(($iIndex == 0) && $this->peek('@'));
        }

        public function parse()
        {
                while ($this->nextBlock() != self::EOF)
                {
                        $this->aParsedCss[] = $this->aBlock;
                }
        }

}
