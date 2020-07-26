<?php
/**
 * @package J2Store
 * @subpackage plg_j2store_app_emailtofriend
 * @copyright Copyright (c)2015 JoomlaBuff - joomlabuff.org
 * @license GNU GPL v3 or later
 */
/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');
jimport('joomla.plugin.plugin');
class plgJ2Storeapp_emailtofriend extends J2StoreAppPlugin
{
	/**
	 * @var $_element  string  Should always correspond with the plugin's filename,
	 *                         forcing it to be unique
	 */
    var $_element   = 'app_emailtofriend';

	/**
	 * Overriding
	 * @param $options
	 * @return unknown_type
	 */
    function onJ2StoreGetAppView( $row )
    {
    	if (!$this->_isMe($row)) return null;
    	return $html = $this->viewList();
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
    	$ns = $option.'.app.'.$this->_element;
    	$html = "";
    	JToolBarHelper::title(JText::_('J2STORE_APP').'-'.JText::_('PLG_J2STORE_'.strtoupper($this->_element)),'j2store-logo');
    	JToolBarHelper::apply('apply');
    	JToolBarHelper::save();
    	JToolBarHelper::back('PLG_J2STORE_BACK_TO_APPS', 'index.php?option=com_j2store&view=apps');
    	JToolBarHelper::back('J2STORE_BACK_TO_DASHBOARD', 'index.php?option=com_j2store');
    	$vars = new JObject();
    	//model should always be a plural
    	$this->includeCustomModel('AppEmailtofriend');

    	$model = F0FModel::getTmpInstance('AppEmailtofriend', 'J2StoreModel');

    	$data = $this->params->toArray();
    	$newdata = array();
    	$newdata['params'] = $data;
    	$form = $model->getForm($newdata);
    	$vars->form = $form;

    	$this->includeCustomTables();

    	$id = $app->input->getInt('id', '0');
    	$vars->id = $id;
    	$vars->action = "index.php?option=com_j2store&view=app&task=view&id={$id}";
    	$html = $this->_getLayout('default', $vars);
    	return $html;
    }
	/**
	 * Method to display addtocart before cart button
	 * @param Object $product
	 * @param string $context
	 */
	public function onJ2StoreAfterAddToCartButton($product, $context) {
		$vars = new JObject ();
		F0FTable::addIncludePath ( JPATH_ADMINISTRATOR . '/components/com_j2store/tables' );
		$app_table = F0FTable::getInstance ( 'App', 'J2StoreTable' );
		$app_table->load ( array (
				'element' => $this->_element,
				'folder' => 'j2store' 
		) );
		$vars->app_id = $app_table->extension_id;
		$vars->product = $product;
		$vars->plugin_params = $this->params;
		//print_r($this->params->get('display_text','J2STORE_APP_EMAILTOFRIEND_MESSAGE_EMAIL_TO_FRIEND'));
		
		$view_pos = (strpos ( $context, 'view_cart' ) || strpos ( $context, 'article.item_cart' ));
		if ($view_pos !== false) {
			return $this->_getLayout ( 'recommend', $vars );
		}
	}
}
