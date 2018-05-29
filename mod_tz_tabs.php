<?php
/**
 * @package Tz Tabs
 * @version 2.1
 * @author ThemeZart http://www.themezart.com
 * @copyright Copyright (C) 2009 - 2011 ThemeZart
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 */
 
// no direct access
defined('_JEXEC') or die('Restricted accessd');


// Include the syndicate functions only once
require_once (dirname(__FILE__).'/helper.php');

$list = modTzTabsHelper::getLists($params);
$module_id = 'tz'.$module->id;

modTzTabsHelper::loadStyles($params);
modTzTabsHelper::loadScripts($params, $module_id);

$content_source = $params->get('content_source','mods');

require JModuleHelper::getLayoutPath('mod_tz_tabs', $params->get('layout', 'default'));