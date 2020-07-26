<?php
/**  
 * JoomlaBuff Responsive Testimonals
 * @package      mod_jb_responsive_testimonals
 * @copyright    Copyright (C) 2014 - 2019 JoomlaBuff http://joomlabuff.com. All rights reserved.
 * @license      GNU General Public License version 2 or later; see LICENSE.txt
 * @websites     http://joomlabuff.com
 * @support      Forum - http://joomlabuff.com/forum/index.html
 */

defined('_JEXEC') or die;

/**
 * Form Field class for the Joomla CMS.
 * Provides a modal media selector including upload mechanism
 *
 * @since 1.6
 */
class JFormFieldJBMedia extends JFormField {
	/**
	 * The form field type.
	 *
	 * @var string
	 * @since 1.6
	 */
	protected $type = 'Jbmedia';
	
	/**
	 * The initialised state of the document object.
	 *
	 * @var boolean
	 * @since 1.6
	 */
	protected static $initialised = false;
	
	/**
	 * The authorField.
	 *
	 * @var string
	 * @since 3.2
	 */
	protected $authorField;
	
	/**
	 * The asset.
	 *
	 * @var string
	 * @since 3.2
	 */
	protected $asset;
	
	/**
	 * The link.
	 *
	 * @var string
	 * @since 3.2
	 */
	protected $link;
	
	/**
	 * The authorField.
	 *
	 * @var string
	 * @since 3.2
	 */
	protected $preview;
	
	/**
	 * The preview.
	 *
	 * @var string
	 * @since 3.2
	 */
	protected $directory;
	
	/**
	 * The previewWidth.
	 *
	 * @var int
	 * @since 3.2
	 */
	protected $previewWidth;
	var $columns ;
	var $rows ;
	
	/**
	 * The previewHeight.
	 *
	 * @var int
	 * @since 3.2
	 */
	protected $previewHeight;
	var $_counter = 0;
	
	/**
	 * Method to get certain otherwise inaccessible properties from the form field object.
	 *
	 * @param string $name
	 *        	The property name for which to the the value.
	 *        	
	 * @return mixed The property value or null.
	 *        
	 * @since 3.2
	 */
	public function __get($name) {
		switch ($name) {
			case 'authorField' :
			case 'asset' :
			case 'link' :
			case 'preview' :
			case 'directory' :
			case 'previewWidth' :
			case 'previewHeight' :
				return $this->$name;
		}
		
		return parent::__get ( $name );
	}
	
	/**
	 * Method to set certain otherwise inaccessible properties of the form field object.
	 *
	 * @param string $name
	 *        	The property name for which to the the value.
	 * @param mixed $value
	 *        	The value of the property.
	 *        	
	 * @return void
	 *
	 * @since 3.2
	 */
	public function __set($name, $value) {
		switch ($name) {
			case 'authorField' :
			case 'asset' :
			case 'link' :
			case 'preview' :
			case 'directory' :
				$this->$name = ( string ) $value;
				break;
			
			case 'previewWidth' :
			case 'previewHeight' :
				$this->$name = ( int ) $value;
				break;
			
			default :
				parent::__set ( $name, $value );
		}
	}
	
	/**
	 * Method to attach a JForm object to the field.
	 *
	 * @param SimpleXMLElement $element
	 *        	The SimpleXMLElement object representing the <field /> tag for the form field object.
	 * @param mixed $value
	 *        	The form field value to validate.
	 * @param string $group
	 *        	The field name group control value. This acts as as an array container for the field.
	 *        	For example if the field has name="foo" and the group value is set to "bar" then the
	 *        	full field name would end up being "bar[foo]".
	 *        	
	 * @return boolean True on success.
	 *        
	 * @see JFormField::setup()
	 * @since 3.2
	 */
	public function setup(SimpleXMLElement $element, $value, $group = null) {
		$result = parent::setup ( $element, $value, $group );
		
		if ($result == true) {
			$assetField = $this->element ['asset_field'] ? ( string ) $this->element ['asset_field'] : 'asset_id';
			
			$this->authorField = $this->element ['created_by_field'] ? ( string ) $this->element ['created_by_field'] : 'created_by';
			$this->asset = $this->form->getValue ( $assetField ) ? $this->form->getValue ( $assetField ) : ( string ) $this->element ['asset_id'];
			$this->link = ( string ) $this->element ['link'];
			$this->preview = ( string ) $this->element ['preview'];
			$this->directory = ( string ) $this->element ['directory'];
			$this->previewWidth = isset ( $this->element ['preview_width'] ) ? ( int ) $this->element ['preview_width'] : 200;
			$this->previewHeight = isset ( $this->element ['preview_height'] ) ? ( int ) $this->element ['preview_height'] : 200;
		}
		
		return $result;
	}
	
	/**
	 * Method to get the field input markup for a media selector.
	 * Use attributes to identify specific created_by and asset_id fields
	 *
	 * @return string The field input markup.
	 *        
	 * @since 1.6
	 */
	protected function getInput() {
		$this->columns = 'columns="20"';
		$this->rows = 'rows="5"';
		$class = 'jbmedia-controls';
		$html = array ();
		$html [] = '<div id="jbmedia-container" class="jbmedia-main-wrapper">';
		
		$html [] = '<a href="javascript:void(0);"  onclick="addJBMedia();"  class="btn btn-success"><i class="icon icon-plus"></i>  '.JText::_('MOD_JB_RESPONSIVE_TESTIMONALS_ADD').'</a>';
		if (! empty ( $this->value )) {
			if (is_array ( $this->value )) {
				
				foreach ( $this->value as $item ) {
					if (! empty ( $item )) {
						if (is_object ( $item )) {
							$item = JArrayHelper::fromObject ( $item );
						}
							
						$html [] = $this->getHtml ( $item );
						$this->_counter ++;
					}
				}
			}
		} else {
			$array = array (
					'company' => '',
					'quote' => '',
					'image' => '' 
			);
			$html [] = $this->getHtml ( $array );
		}
		$html [] = '</div>';
	
		
		
		JFactory::getDocument ()->addStyleDeclaration ( '.jbmedia{ margin:15px 5px; padding:15px 5px;  }' );
		JFactory::getDocument ()->addScript ( JURI::root ( true ) . '/modules/mod_jb_responsive_testimonals/elements/script.js' );
		return implode ( "\n", $html );
	}
	function getHtml($item) {
		$html = array ();
		$company = ! empty ( $item ['company'] ) ? $item ['company'] : '';
		$quote = ! empty ( $item ['quote'] ) ? $item ['quote'] : '';
		$image = ! empty ( $item ['image'] ) ? $item ['image'] : '';
		$name = !empty($item['name']) ? $item['name'] : '' ;
					
		
		$html [] = '<div class="jbmedia">';
		$html [] = '<div class="row-fluid">';
		$html [] = '<div class="span6">';
		
		$html [] = '<div class="control-group">';
		$html [] = '<label class="control-label">';
		$html [] = JText::_ ( 'MOD_JB_RESPONSIVE_TESTIMONALS_NAME' );
		$html [] = '</label>';
		$html [] = '<div class="controls">';
		
		$html [] = '<input data-input="name" type="text" name="' . $this->name . '[' . $this->_counter . '][name]' . '" id="jform_params_input_jbmedia_name_' . $this->_counter . '"   value="' . htmlspecialchars ( $name, ENT_COMPAT, 'UTF-8' ) . '"  />';
		$html [] = '</div>';
		$html [] = '</div>';
		
		
		$html [] = '<div class="control-group">';
		$html [] = '<label class="control-label">';
		$html [] = JText::_ ( 'MOD_JB_RESPONSIVE_TESTIMONALS_COMPANY' );
		$html [] = '</label>';
		$html [] = '<div class="controls">';
		
		$html [] = '<input data-input="company" type="text" name="' . $this->name . '[' . $this->_counter . '][company]' . '" id="jform_params_input_jbmedia_company_' . $this->_counter . '"   value="' . htmlspecialchars ( $company, ENT_COMPAT, 'UTF-8' ) . '"  />';
		$html [] = '</div>';
		$html [] = '</div>';
		

		
		$html [] = '<div class="control-group">';
		$html [] = '<label class="control-label">';
		$html [] = JText::_ ( 'MOD_JB_RESPONSIVE_TESTIMONALS_MEDIA' );
		$html [] = '</label>';
		$html [] = '<div class="controls">';
		$html [] = $this->getMedia ( 'jform_params_input_jbmedia_media_' . $this->_counter, $image );
		$html [] = '</div>';
		$html [] = '</div>';
		
		$html [] = '<div class="control-group">';
		$html [] = '<label class="control-label">';
		$html [] = JText::_ ( 'MOD_JB_RESPONSIVE_TESTIMONALS_QUOTE' );
		$html [] = '</label>';
		
		$html [] = '<div class="controls">';
		$html [] = '<textarea data-input="quote" name="' . $this->name . '[' . $this->_counter . '][quote]' . '" id="jform_params_input_jbmedia_quote_' . $this->_counter . '"' . $this->columns . $this->rows . ' >' . htmlspecialchars ( $quote, ENT_COMPAT, 'UTF-8' ) . '</textarea>';
		$html [] = '</div>';
		$html [] = '</div>';
		
		$html [] = '</div>';
		
		$html [] = '<div class="span6">';		
		$html [] = '<a href="javascript:void(0);" class="btn btn-danger btn-mini" onclick="deleteJBMedia(this);"><i class="icon icon-trash"></i></a>';
		// The text field.
		$html [] = '</div>';
		
		$html [] = '</div>';
		$html [] = '</div>';
		return implode ( "\n", $html );
	}
	function getMedia($id, $value = '') {
		$asset = $this->asset;
		
		if ($asset == '') {
			$asset = JFactory::getApplication ()->input->get ( 'option' );
		}
		
		if (! self::$initialised) {
			// Load the modal behavior script.
			JHtml::_ ( 'behavior.modal' );
			
			// Include jQuery
			JHtml::_ ( 'jquery.framework' );
			
			// Build the script.
			$script = array ();
			$script [] = '	function jInsertFieldValue(value, id) {';
			$script [] = '		var $ = jQuery.noConflict();';
			$script [] = '		var old_value = $("#" + id).val();';
			$script [] = '		if (old_value != value) {';
			$script [] = '			var $elem = $("#" + id);';
			$script [] = '			$elem.val(value);';
			$script [] = '			$elem.trigger("change");';
			$script [] = '			if (typeof($elem.get(0).onchange) === "function") {';
			$script [] = '				$elem.get(0).onchange();';
			$script [] = '			}';
			$script [] = '			jMediaRefreshPreview(id);';
			$script [] = '		}';
			$script [] = '	}';
			
			$script [] = '	function jMediaRefreshPreview(id) {';
			$script [] = '		var $ = jQuery.noConflict();';
			$script [] = '		var value = $("#" + id).val();';
			$script [] = '		var $img = $("#" + id + "_preview");';
			$script [] = '		if ($img.length) {';
			$script [] = '			if (value) {';
			$script [] = '				$img.attr("src", "' . JUri::root () . '" + value);';
			$script [] = '				$("#" + id + "_preview_empty").hide();';
			$script [] = '				$("#" + id + "_preview_img").show()';
			$script [] = '			} else { ';
			$script [] = '				$img.attr("src", "")';
			$script [] = '				$("#" + id + "_preview_empty").show();';
			$script [] = '				$("#" + id + "_preview_img").hide();';
			$script [] = '			} ';
			$script [] = '		} ';
			$script [] = '	}';
			
			$script [] = '	function jMediaRefreshPreviewTip(tip)';
			$script [] = '	{';
			$script [] = '		var $ = jQuery.noConflict();';
			$script [] = '		var $tip = $(tip);';
			$script [] = '		var $img = $tip.find("img.media-preview");';
			$script [] = '		$tip.find("div.tip").css("max-width", "none");';
			$script [] = '		var id = $img.attr("id");';
			$script [] = '		id = id.substring(0, id.length - "_preview".length);';
			$script [] = '		jMediaRefreshPreview(id);';
			$script [] = '		$tip.show();';
			$script [] = '	}';
			
			// JQuery for tooltip for INPUT showing whole image path
			$script [] = '	function jMediaRefreshImgpathTip(tip)';
			$script [] = '	{';
			$script [] = '		var $ = jQuery.noConflict();';
			$script [] = '		var $tip = $(tip);';
			$script [] = '		$tip.css("max-width", "none");';
			$script [] = '		var $imgpath = $("#" + "' . $id . '").val();';
			$script [] = '		$("#TipImgpath").html($imgpath);';
			$script [] = '		if ($imgpath.length) {';
			$script [] = '		 $tip.show();';
			$script [] = '		} else {';
			$script [] = '		 $tip.hide();';
			$script [] = '		}';
			$script [] = '	}';
			
			// Add the script to the document head.
			JFactory::getDocument ()->addScriptDeclaration ( implode ( "\n", $script ) );
			
			self::$initialised = true;
		}
		
		$html = array ();
		$attr = '';
		
		// Tooltip for INPUT showing whole image path
		$options = array (
				'onShow' => 'jMediaRefreshImgpathTip' 
		);
		JHtml::_ ( 'behavior.tooltip', '.hasTipImgpath', $options );
		
		if (! empty ( $this->class )) {
			$this->class .= ' hasTipImgpath';
		} else {
			$this->class = 'hasTipImgpath';
		}
		
		$attr .= ' title="' . htmlspecialchars ( '<span id="TipImgpath"></span>', ENT_COMPAT, 'UTF-8' ) . '"';
		
		// Initialize some field attributes.
		$attr .= ! empty ( $this->class ) ? ' class="input-small ' . $this->class . '"' : ' class="input-small"';
		$attr .= ! empty ( $this->size ) ? ' size="' . $this->size . '"' : '';
		
		// Initialize JavaScript field attributes.
		$attr .= ! empty ( $this->onchange ) ? ' onchange="' . $this->onchange . '"' : '';
		
		// The text field.
		$html [] = '<div class="input-prepend input-append">';
		
		// The Preview.
		$showPreview = true;
		$showAsTooltip = false;
		
		switch ($this->preview) {
			case 'no' : // Deprecated parameter value
			case 'false' :
			case 'none' :
				$showPreview = false;
				break;
			
			case 'yes' : // Deprecated parameter value
			case 'true' :
			case 'show' :
				break;
			
			case 'tooltip' :
			default :
				$showAsTooltip = true;
				$options = array (
						'onShow' => 'jMediaRefreshPreviewTip' 
				);
				JHtml::_ ( 'behavior.tooltip', '.hasTipPreview', $options );
				break;
		}
		
		if ($showPreview) {
			if ($value && file_exists ( JPATH_ROOT . '/' . $value )) {
				$src = JUri::root () . $value;
			} else {
				$src = '';
			}
			
			$width = $this->previewWidth;
			$height = $this->previewHeight;
			$style = '';
			$style .= ($width > 0) ? 'max-width:' . $width . 'px;' : '';
			$style .= ($height > 0) ? 'max-height:' . $height . 'px;' : '';
			
			$imgattr = array (
					'id' => $id . '_preview',
					'class' => 'media-preview',
					'style' => $style 
			);
			
			$img = JHtml::image ( $src, JText::_ ( 'JLIB_FORM_MEDIA_PREVIEW_ALT' ), $imgattr );
			$previewImg = '<div id="' . $id . '_preview_img"' . ($src ? '' : ' style="display:none"') . '>' . $img . '</div>';
			$previewImgEmpty = '<div id="' . $id . '_preview_empty"' . ($src ? ' style="display:none"' : '') . '>' . JText::_ ( 'JLIB_FORM_MEDIA_PREVIEW_EMPTY' ) . '</div>';
			
			if ($showAsTooltip) {
				$html [] = '<div class="media-preview add-on">';
				$tooltip = $previewImgEmpty . $previewImg;
				$options = array (
						'title' => JText::_ ( 'JLIB_FORM_MEDIA_PREVIEW_SELECTED_IMAGE' ),
						'text' => '<i class="icon-eye"></i>',
						'class' => 'hasTipPreview' 
				);
				
				$html [] = JHtml::tooltip ( $tooltip, $options );
				$html [] = '</div>';
			} else {
				$html [] = '<div class="media-preview add-on" style="height:auto">';
				$html [] = ' ' . $previewImgEmpty;
				$html [] = ' ' . $previewImg;
				$html [] = '</div>';
			}
		}
		
		$html [] = '	<input data-input="image" type="text" name="' . $this->name . '[' . $this->_counter . '][image]' . '" image_id="" id="' . $id . '" class="jbmedia-media" value="' . htmlspecialchars ( $value, ENT_COMPAT, 'UTF-8' ) . '" readonly="readonly"' . $attr . ' />';
		
		if ($value && file_exists ( JPATH_ROOT . '/' . $value )) {
			$folder = explode ( '/', $value );
			$folder = array_diff_assoc ( $folder, explode ( '/', JComponentHelper::getParams ( 'com_media' )->get ( 'image_path', 'images' ) ) );
			array_pop ( $folder );
			$folder = implode ( '/', $folder );
		} elseif (file_exists ( JPATH_ROOT . '/' . JComponentHelper::getParams ( 'com_media' )->get ( 'image_path', 'images' ) . '/' . $this->directory )) {
			$folder = $this->directory;
		} else {
			$folder = '';
		}
		
		// The button.
		if ($this->disabled != true) {
			JHtml::_ ( 'bootstrap.tooltip' );
			
			$html [] = '<a class="modal jbmedia-btn btn" data-image_input="'. $id .'" title="' . JText::_ ( 'JLIB_FORM_BUTTON_SELECT' ) . '" href="' . ($this->readonly ? '' : ($this->link ? $this->link : 'index.php?option=com_media&amp;view=images&amp;tmpl=component&amp;asset=' . $asset . '&amp;author=' . $this->form->getValue ( $this->authorField )) . '&amp;fieldid=' . $id . '&amp;folder=' . $folder) . '"' . ' rel="{handler: \'iframe\', size: {x: 800, y: 500}}">';
			$html [] = JText::_ ( 'JLIB_FORM_BUTTON_SELECT' ) . '</a><a class="btn jbmedia-remove-btn hasTooltip" title="' . JText::_ ( 'JLIB_FORM_BUTTON_CLEAR' ) . '" href="#" onclick="';
			$html [] = 'jInsertFieldValue(\'\', \'' . $id . '\');';
			$html [] = 'return false;';
			$html [] = '">';
			$html [] = '<i class="icon-remove"></i></a>';
		}
		
		$html [] = '</div>';
		
		return implode ( "\n", $html );
	}
}
