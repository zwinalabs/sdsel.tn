<?php 
/**
 * @version    CVS: 1.0.0
 * @package    JB Masshead
 * @author     Priya Bose <support@joomlabuff.com>
 * @copyright  2016 www.joomlabuff.com
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */


defined( '_JEXEC' ) or die( 'Restricted access' );
require_once (dirname(__FILE__).'/helper.php');
$helper = ModJBMassheadHelper::getInstance();
// Get masshead information
$masshead = $helper->getMasshead($params);

// Display
require JModuleHelper::getLayoutPath($module->module, $params->get('layout', 'default'));