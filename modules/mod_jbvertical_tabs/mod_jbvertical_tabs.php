<?php
/**  
 * @package mod_jbvertical_tabs
 * @copyright Copyright (C) 2015 JoomlaBuff http://joomlabuff.com. All rights reserved.
 * @license GNU General Public License version 2 or later; see LICENSE.txt
 * @websites http://joomlabuff.com
 * @support Forum - http://joomlabuff.com/support
 */
defined('_JEXEC') or die;

// Include the syndicate functions only once
require_once __DIR__ . '/helper.php';
$doc = JFactory::getDocument();
$doc->addStyleSheet(JUri::root().'modules/mod_jbvertical_tabs/assets/css/custom.css');
$list            = ModJBVerticalTabsHelper::getList($params);
$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));
 
$app = JFactory::getApplication();
$current_menu_id =0;
if($app->getMenu()->getActive()) {
	$current_menu_id = $app->getMenu()->getActive()->id;
}
require JModuleHelper::getLayoutPath('mod_jbvertical_tabs', $params->get('layout', 'vertical'));
