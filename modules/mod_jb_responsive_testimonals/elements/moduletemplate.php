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
jimport('joomla.form.formfield');
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
class JFormFieldModuleTemplate extends JFormField
{

	protected $type = 'ModuleTemplate';

	protected function getInput()
	{

		$moduleName = $this->element->attributes()->modulename;

		$moduleTemplatesPath = JPATH_SITE.'/modules/'.$moduleName.'/tmpl';
		$moduleTemplatesFolders = JFolder::folders($moduleTemplatesPath);

		$db = JFactory::getDBO();
		$query = "SELECT template FROM #__template_styles WHERE client_id = 0 AND home = 1";
		$db->setQuery($query);
		$defaultemplate = $db->loadResult();
		$templatePath = JPATH_SITE.'/templates/'.$defaultemplate.'/html/'.$moduleName;

		if (JFolder::exists($templatePath))
		{
			$templateFolders = JFolder::folders($templatePath);
			$folders = @array_merge($templateFolders, $moduleTemplatesFolders);
			$folders = @array_unique($folders);
		}
		else
		{
			$folders = $moduleTemplatesFolders;
		}

		$exclude = 'Default';
		$options = array();

		foreach ($folders as $folder)
		{
			if (preg_match(chr(1).$exclude.chr(1), $folder))
			{
				continue;
			}
			$options[] = JHTML::_('select.option', $folder, $folder);
		}

		array_unshift($options, JHTML::_('select.option', 'Default', JText::_('MOD_J2STORE_PRODUCTS_USE_DEFAULT')));

		$fieldName = $this->name;

		return JHTML::_('select.genericlist', $options, $this->fieldname, 'class="inputbox"', 'value', 'text', $this->value, $this->options['control'].$this->name);

	}

}