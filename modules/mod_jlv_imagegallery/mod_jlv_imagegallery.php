<?php
/**
 * @version     $Id$
 * @package     JLVExtension
 * @subpackage  JLV ImageGallery
 * @author      JLV Extension Team <admin@jlvextension.com>
 * @copyright   Copyright (C) 2013 JLV Extension. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 **/

// No direct access to this file
defined('_JEXEC') or die;
require_once __DIR__ . '/core/helper.php';
$cacheparams = new stdClass;
$cacheparams->cachemode = 'id';
$cacheparams->class = 'JLVImageGallery';
$cacheparams->method = 'getList';
$cacheparams->methodparams = $params;
$items = JLVImageGallery::getList($params);
require JModuleHelper::getLayoutPath('mod_jlv_imagegallery', $params->get('layout', 'default'));?>