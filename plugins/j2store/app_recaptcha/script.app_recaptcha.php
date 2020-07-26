<?php
/**
 * --------------------------------------------------------------------------------
 * App Plugin - Recaptcha for J2Store Resigister
 * --------------------------------------------------------------------------------
 * @package     Joomla 2.5 -  3.x
 * @subpackage  J2 Store
 * @author      Alagesan, J2Store <support@j2store.org>
 * @copyright   Copyright (c) 2016 J2Store . All rights reserved.
 * @license     GNU General Public License v or later
 * @link        http://j2store.org
 * --------------------------------------------------------------------------------
 *
 * */
defined ( '_JEXEC' ) or die ( 'Restricted access' );
class plgJ2StoreApp_recaptchaInstallerScript {
    function preflight($type, $parent) {
        if (! JComponentHelper::isEnabled ( 'com_j2store' )) {
            Jerror::raiseWarning ( null, 'J2Store not found. Please install J2Store before installing this plugin' );
            return false;
        }

        jimport ( 'joomla.filesystem.file' );
        $version_file = JPATH_ADMINISTRATOR . '/components/com_j2store/version.php';
        if (JFile::exists ( $version_file )) {
            require_once ($version_file);
            // abort if the current J2Store release is older
            if (version_compare ( J2STORE_VERSION, '3.2.9', 'lt' )) {
                Jerror::raiseWarning ( null, 'You are using an old version of J2Store. Please upgrade to the latest version' );
                return false;
            }
        } else {
            Jerror::raiseWarning ( null, 'J2Store not found or the version file is not found. Make sure that you have installed J2Store before installing this plugin' );
            return false;
        }
    }
}