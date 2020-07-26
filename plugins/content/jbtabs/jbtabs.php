<?php
/**
 * @package     Alias2id.Plugin
 * @subpackage  Content.alias2id
 * @author		Priya Bose
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined ( '_JEXEC' ) or die ();

/**
 * Plugin to convert the article alias to id
 *
 * @since 1.6
 */
class PlgContentJBtabs extends JPlugin {
	
	/**
	 * Example after save content method
	 * Article is passed by reference, but after the save, so no changes will be saved.
	 * Method is called right after the content is saved
	 *
	 * @param string $context
	 *        	The context of the content passed to the plugin (added in 1.6)
	 * @param object $article
	 *        	A JTableContent object
	 * @param boolean $isNew
	 *        	If the content is just about to be created
	 *        	
	 * @return boolean true if function not enabled, is in front-end or is new. Else true or
	 *         false depending on success of save function.
	 *        
	 * @since 1.6
	 */
 
	function onContentPrepareForm($form, $data) {
		$app = JFactory::getApplication ();
		$option = $app->input->get ( 'option' );
		switch ($option) {
			case 'com_content' :
				if ($app->isAdmin ()) {
					JForm::addFormPath ( __DIR__ . '/forms' );
					$form->loadFile ( 'content', false );
				}
				return true;
		}
		return true;
	}
}
