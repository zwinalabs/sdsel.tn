<?php
/**  
 * JoomlaBuff Responsive Testimonals
 * @package      mod_jb_responsive_testimonals
 * @copyright    Copyright (C) 2015 JoomlaBuff http://joomlabuff.com. All rights reserved.
 * @license      GNU General Public License version 2 or later; see LICENSE.txt
 * @websites     http://joomlabuff.com
 * @support      Forum - http://joomlabuff.com/forum/index.html
 */
defined('_JEXEC') or die;

// Include the syndicate functions only once
require_once __DIR__ . '/helper.php';

$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));
 
$transition_effect =$params->get('transition_type','fade');
$list =  $params->get('testimonals',array());

JHtml::_('jquery.framework');
$doc = JFactory::getDocument();
$doc->addScript( JURI::root ( ) . 'modules/mod_jb_responsive_testimonals/assets/js/jbrt.js');
$doc->addStyleSheet( JURI::root ( ) . 'modules/mod_jb_responsive_testimonals/assets/css/style.css');
$doc->addStyleSheet(JUri::root().'modules/mod_jb_responsive_testimonals/assets/css/font-awesome.min.css');

require JModuleHelper::getLayoutPath('mod_jb_responsive_testimonals', $params->get('layout', 'default'));

?>


