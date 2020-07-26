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
require_once(JPATH_ADMINISTRATOR.'/components/com_j2store/library/plugins/app.php');

class plgJ2StoreApp_recaptcha extends J2StoreAppPlugin
{
    /**
     * @var $_element  string  Should always correspond with the plugin's filename,
     *                         forcing it to be unique
     */
    var $_element   = 'app_recaptcha';

    /**
     * Overriding
     *
     * @param $options
     * @return unknown_type
     */
    function onJ2StoreGetAppView( $row )
    {

        if (!$this->_isMe($row))
        {
            return null;
        }

        $html = $this->viewList();


        return $html;
    }

    /**
     * Validates the data submitted based on the suffix provided
     * A controller for this plugin, you could say
     *
     * @param $task
     * @return html
     */
    function viewList()
    {
        $app = JFactory::getApplication();
        $option = 'com_j2store';
        $ns = $option . '.app.' . $this->_element;
        $html = "";
        JToolBarHelper::title(JText::_('J2STORE_APP') . '-' . JText::_('PLG_J2STORE_' . strtoupper($this->_element)), 'j2store-logo');
        JToolBarHelper::apply('apply');
        JToolBarHelper::save();
        JToolBarHelper::back('PLG_J2STORE_BACK_TO_APPS', 'index.php?option=com_j2store&view=apps');
        JToolBarHelper::back('J2STORE_BACK_TO_DASHBOARD', 'index.php?option=com_j2store');

        $vars = new JObject();
        //model should always be a plural
        $this->includeCustomModel('AppRecaptchas');

        $model = F0FModel::getTmpInstance('AppRecaptchas', 'J2StoreModel');

        $data = $this->params->toArray();
        $newdata = array();
        $newdata['params'] = $data;
        $form = $model->getForm($newdata);
        $vars->form = $form;
        $id = $app->input->getInt('id', '0');
        $vars->id = $id;
        $vars->action = "index.php?option=com_j2store&view=app&task=view&id={$id}";
        return $this->_getLayout('default', $vars);
    }

    function onJ2StoreCheckoutRegister($view){
        $vars = new stdClass();
        $vars->id = "j2store_recaptcha_register";
        $vars->pubkey = $this->params->get('public_key','');
        $vars->theme = $this->params->get('theme','dark');
        return $this->_getLayout('register', $vars);
    }

    function onJ2StoreBeforeCheckout($order){
        $document = JFactory::getDocument();
        $file  = 'https://www.google.com/recaptcha/api.js?hl=' . JFactory::getLanguage()->getTag() . '&amp;render=explicit';
        $document->addScript($file);
    }

    function onJ2StoreCheckoutValidateRegister(&$json){
        $app = JFactory::getApplication();
        $response = $app->input->get('g-recaptcha-response', '', 'string');
        $privatekey = $this->params->get('private_key','');
        $remoteip   = $app->input->server->get('REMOTE_ADDR', '', 'string');
        $spam      = ($response == null || strlen($response) == 0);
        $challenge = null;
        if (empty($remoteip))
        {
            $json['error']['j2store_recaptcha_register'] = JText::_('J2STORE_APP_RECAPTCHA_ERROR_NO_IP');
        }
        // Discard spam submissions
        if ($spam)
        {
            $json['error']['j2store_recaptcha_register'] = JText::_('J2STORE_APP_RECAPTCHA_ERROR_EMPTY_SOLUTION');
        }
        // Check for Private Key
        if (empty($privatekey))
        {
            $json['error']['recaptcha'] = JText::_('J2STORE_APP_RECAPTCHA_ERROR_NO_PRIVATE_KEY');
        }

        if(!$json){
            require_once JPATH_SITE.'/plugins/j2store/app_recaptcha/app_recaptcha/lib/recaptchalib.php';

            $reCaptcha = new JReCaptcha($privatekey);
            $response  = $reCaptcha->verifyResponse($remoteip, $response);

            if ( !isset($response->success) || !$response->success)
            {
                $json['error']['recaptcha'] = "";
                foreach ($response->errorCodes as $error)
                {
                    $json['error']['recaptcha'] .= $error."<br>";

                }
            }
        }
    }
}