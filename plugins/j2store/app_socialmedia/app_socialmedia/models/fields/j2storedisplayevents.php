<?php
/**
 * @package J2Store
 * @copyright Copyright (c)2014-17 Ramesh Elamathi / J2Store.org
 * @license GNU GPL v3 or later
 */
// No direct access to this file
defined('_JEXEC') or die;
/* class JFormFieldFieldtypes extends JFormField */

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

require_once JPATH_ADMINISTRATOR.'/components/com_j2store/helpers/j2html.php';
class JFormFieldJ2storedisplayevents extends JFormFieldList {

	public $type = 'J2storedisplayevents';

/**
	 * Method to get the field input markup for a generic list.
	 * Use the multiple attribute to enable multiselect.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   11.1
	 */
	protected function getInput()
	{
		$html = array();
		$attr = '';

		// Initialize some field attributes.
		$attr .= !empty($this->class) ? ' class="' . $this->class . '"' : '';
		$attr .= !empty($this->size) ? ' size="' . $this->size . '"' : '';
		$attr .= $this->multiple ? ' multiple' : '';
		$attr .= $this->required ? ' required aria-required="true"' : '';
		$attr .= $this->autofocus ? ' autofocus' : '';

		// To avoid user's confusion, readonly="true" should imply disabled="true".
		if ((string) $this->readonly == '1' || (string) $this->readonly == 'true' || (string) $this->disabled == '1'|| (string) $this->disabled == 'true')
		{
			$attr .= ' disabled="disabled"';
		}

		// Initialize JavaScript field attributes.
		$attr .= $this->onchange ? ' onchange="' . $this->onchange . '"' : '';

		// Get the field options.
		$options = (array) $this->getOptions();

		// Create a read-only list (no name) with a hidden input to store the value.
		if ((string) $this->readonly == '1' || (string) $this->readonly == 'true')
		{
			$html[] = JHtml::_('select.genericlist', $options, '', trim($attr), 'value', 'text', $this->value, $this->id);
			$html[] = '<input type="hidden" name="' . $this->name . '" value="' . htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8') . '"/>';
		}
		else
		// Create a regular list.
		{
			$html[] = JHtml::_('select.genericlist', $options, $this->name, trim($attr), 'value', 'text', $this->value, $this->id);
		}

		return implode($html);
	}

	/**
	 * Method to get the field options.
	 *
	 * @return  array  The field option objects.
	 *
	 * @since   11.1
	 */
	protected function getOptions()
	{
		$options = array(
					'afterDisplayTitle' 			=> JText::_('PLG_J2STORE_APP_SOCIALMEDIA_DISPLAY_SOCIAL_MEDIA_AFTER_PRODUCT_TITLE'),
					'beforeDisplayContent'			=> JText::_('PLG_J2STORE_APP_SOCIALMEDIA_DISPLAY_SOCIAL_MEDIA_BEFORE_PRODUCT_CONTENT'),
					'afterDisplayContent'			=> JText::_('PLG_J2STORE_APP_SOCIALMEDIA_DISPLAY_SOCIAL_MEDIA_AFTER_PRODUCT_CONTENT'),
					'AfterAddToCartButton'			=> JText::_('PLG_J2STORE_APP_SOCIALMEDIA_DISPLAY_SOCIAL_MEDIA_AFTER_ADDTOCART_BUTTON'),
					'BeforeAddToCartButton'			=> JText::_('PLG_J2STORE_APP_SOCIALMEDIA_DISPLAY_SOCIAL_MEDIA_BEFORE_ADDTOCART_BUTTON'),
					'BeforeRenderingProductPrice'	=> JText::_('PLG_J2STORE_APP_SOCIALMEDIA_DISPLAY_SOCIAL_MEDIA_BEFORE_PRODUCT_PRICE'),
					'AfterRenderingProductPrice'	=>JText::_('PLG_J2STORE_APP_SOCIALMEDIA_DISPLAY_SOCIAL_MEDIA_AFTER_PRODUCT_PRICE'),
				);
		return $options;
	}

}