<?php
/**
 * @package J2Store
 * @subpackage plg_j2store_app_emailtofriend
 * @copyright Copyright (c)2015 JoomlaBuff - joomlabuff.org
 * @license GNU GPL v3 or later
 */
/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');
class plgJ2Storeapp_emailtofriendInstallerScript {
	function preflight($type, $parent) {
		if (! JComponentHelper::isEnabled ( 'com_j2store' )) {
			Jerror::raiseWarning ( null, 'J2Store not found. Please install J2Store before installing this plugin' );
			return false;
		}
		jimport ( 'joomla.filesystem.file' );
		$version_file = JPATH_ADMINISTRATOR . '/components/com_j2store/version.php';
		if (JFile::exists ( $version_file )) {
			require_once ($version_file);
			if (version_compare ( J2STORE_VERSION, '3.1.6', 'lt' )) {
				Jerror::raiseWarning ( null, 'This plugin is not compatiable with J2Store version 3. Please install compatiable plugin' );
				return false;
			}
		} else {
			Jerror::raiseWarning ( null, 'J2Store not found or the version file is not found. Make sure that you have installed J2Store before installing this plugin' );
			return false;
		}
	}
}
