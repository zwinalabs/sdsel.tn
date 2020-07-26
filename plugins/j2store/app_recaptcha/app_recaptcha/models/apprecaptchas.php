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
defined('_JEXEC') or die('Restricted access');
require_once (JPATH_ADMINISTRATOR . '/components/com_j2store/library/appmodel.php');
class J2StoreModelAppRecaptchas extends J2StoreAppModel
{
    public $_element = 'app_recaptcha';
}