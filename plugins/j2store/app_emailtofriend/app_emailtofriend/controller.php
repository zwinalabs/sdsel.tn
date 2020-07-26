<?php
/**
 * @package J2Store
 * @subpackage plg_j2store_app_emailtofriend
 * @copyright Copyright (c)2015 JoomlaBuff - joomlabuff.org
 * @license GNU GPL v3 or later
 */
/**
 * ensure this file is being included by a parent file
 */
defined ( '_JEXEC' ) or die ( 'Restricted access' );
require_once (JPATH_ADMINISTRATOR . '/components/com_j2store/library/appcontroller.php');
class J2StoreControllerAppemailtofriend extends J2StoreAppController {
	var $_element = 'app_emailtofriend';
	function __construct($config = array()) {
		parent::__construct ( $config );
		// there is problem in loading of language
		// this code will fix the language loading problem
		$language = JFactory::getLanguage ();
		$extension = 'plg_j2store' . '_' . $this->_element;
		$language->load ( $extension, JPATH_ADMINISTRATOR, 'en-GB', true );
		$language->load ( $extension, JPATH_ADMINISTRATOR, null, true );
	}
	protected function onBeforeGenericTask($task) {
		$privilege = $this->configProvider->get ( $this->component . '.views.' . F0FInflector::singularize ( $this->view ) . '.acl.' . $task, '' );
		return $this->allowedTasks ( $task );
	}
	function allowedTasks($task) {
		$allowed = array (
				'emailToMyFriend' 
		);
		if (in_array ( $task, $allowed )) {
			return true;
		}
		return false;
	}
	function emailToMyFriend() {
		// get the application object
		$app = JFactory::getApplication ();
		// create an empty array
		$json = array ();
		// get the config class obj
		$config = JFactory::getConfig ();
		// get the component params
		$params = J2Store::config ();
		// get the mailer class object
		$mailer = JFactory::getMailer ();
		$selectableBase = J2Store::getSelectableBase ();
		
		// get the post data
		$data = $app->input->getArray ( $_POST );
		
		// check sender name is not empty
		if (isset ( $data ['sender_name'] ) && empty ( $data ['sender_name'] )) {
			$json ['error'] ['sender_name'] = JText::_ ( 'J2STORE_APP_EMAILTOFRIEND_SENDER_NAME_REQUIRED' );
		}
		
		// check sender email exists
		if (isset ( $data ['sender_email'] ) && empty ( $data ['sender_email'] )) {
			$json ['error'] ['sender_email'] = JText::_ ( 'J2STORE_APP_EMAILTOFRIEND_SENDER_EMAIL_REQUIRED' );
		} elseif (! JMail::ValidateAddress ( $data ['sender_email'] )) {
			$json ['error'] ['sender_email'] = JText::_ ( 'J2STORE_APP_EMAILTOFRIEND_SENDER_EMAIL_REQUIRED' );
		}
		
		// check receiver email is not empty
		if (isset ( $data ['receiver_email'] ) && empty ( $data ['receiver_email'] )) {
			$json ['error'] ['receiver_email'] = JText::_ ( 'J2STORE_APP_EMAILTOFRIEND_RECEIPIENT_EMAIL_REQUIRED' );
		}
		
		// check the message not empty
		if (isset ( $data ['message'] ) && empty ( $data ['message'] )) {
			$json ['error'] ['message'] = JText::_ ( 'J2STORE_APP_EMAILTOFRIEND_MESSAGE_REQUIRED' );
		}
		
		if (empty ( $json )) {
			$json = $this->sendEmail ( $data );
		}
		echo json_encode ( $json );
		$app->close ();
	}
	
	/**
	 * Method to send Email
	 * 
	 * @param array $data        	
	 * @return array
	 */
	function sendEmail($data) {
		$json = array ();
		$vars = new JObject ();
		// get the config class obj
		$config = JFactory::getConfig ();
		// get the component params
		$params = J2Store::config ();
		$vars->data = $data;
		// get the mailer class object
		$mailer = JFactory::getMailer ();
		$product = J2Store::product ()->setId ( $data ['product_id'] )->getProduct ();
		F0FModel::getTmpInstance ( 'Products', 'J2StoreModel' )->runMyBehaviorFlag ( true )->getProduct ( $product );
		$vars->product = $product;
		$body = $this->_getLayout ( 'body', $vars );
		
		$name = $data ['sender_name'];
		$sitename = $config->get ( 'sitename' );
		$mailfrom = $config->get ( 'mailfrom' );
		$fromname = $config->get ( 'fromname' );
		
		$subject = JText::sprintf ( 'J2STORE_APP_EMAILTOFRIEND_MESSAGE_SUBJECT', $name, $sitename );
		$admin_emails = $params->get ( 'admin_email' );
		$admin_emails = explode ( ',', $admin_emails );
		if (! isset ( $data ['receiver_email'] ) || empty ( $data ['receiver_email'] )) {
			// email is not present. So just send email to the admins
			$mailer->addRecipient ( $admin_emails );
			$recipient_email = $admin_emails;
		} else {
			$recipent_emailids = explode ( ',', $data ['receiver_email'] );
			$mailer->addRecipient ( $recipent_emailids );
			$mailer->addCC ( $admin_emails );
			$recipient_email = $data ['receiver_email'];
		}
		
		$mailer->setSubject ( $subject );
		$mailer->setBody ( $body );
		$mailer->IsHTML ( 1 );
		$mailer->setSender ( array (
				$mailfrom,
				$fromname 
		) );
		
		if (! $mailer->send ()) {
			$json ['error'] ['msg'] = JText::_ ( 'J2STORE_APP_EMAILTOFRIEND_SENDING_FAILED' );
		} else {
			$json ['success'] ['msg'] = JText::sprintf ( 'J2STORE_APP_EMAILTOFRIEND_SENDING_SUCCESS', $recipient_email );
		}
		return $json;
	}
	
	/**
	 * Gets the parsed layout file
	 *
	 * @param string $layout
	 *        	The name of the layout file
	 * @param object $vars
	 *        	Variables to assign to
	 * @param string $plugin
	 *        	The name of the plugin
	 * @param string $group
	 *        	The plugin's group
	 * @return string
	 * @access protected
	 */
	function _getLayout($layout, $vars = false, $plugin = '', $group = 'j2store') {
		if (empty ( $plugin )) {
			$plugin = $this->_element;
		}		
		ob_start ();
		$layout = $this->_getLayoutPath ( $plugin, $group, $layout );
		include ($layout);
		$html = ob_get_contents ();
		ob_end_clean ();		
		return $html;
	}
	
	/**
	 * Get the path to a layout file	  
	 * @param string $plugin The name of the plugin file
	 * @param string $group The plugin's group
	 * @param string $layout The name of the plugin layout file
	 * @return string The path to the plugin layout file
	 * @access protected
	 */
	function _getLayoutPath($plugin, $group, $layout = 'default') {
		$app = JFactory::getApplication ();		
		// get the template and default paths for the layout
		$templatePath = JPATH_SITE . '/templates/' . $app->getTemplate () . '/html/plugins/' . $group . '/' . $plugin . '/' . $layout . '.php';
		$defaultPath = JPATH_SITE . '/plugins/' . $group . '/' . $plugin . '/' . $plugin . '/tmpl/' . $layout . '.php';
		// if the site template has a layout override, use it
		jimport ( 'joomla.filesystem.file' );
		if (JFile::exists ( $templatePath )) {
			return $templatePath;
		} else {
			return $defaultPath;
		}
	}
}


 