<?php
/**  
 * @package mod_jbvertical_tabs
 * @copyright Copyright (C) 2015 JoomlaBuff http://joomlabuff.com. All rights reserved.
 * @license GNU General Public License version 2 or later; see LICENSE.txt
 * @websites http://joomlabuff.com
 * @support Forum - http://joomlabuff.com/support
 */
defined ('_JEXEC') or die ();

jimport('joomla.filesystem.file');
class Mod_Jbvertical_tabsInstallerScript {

	public function postflight($type, $parent)
	{
		$db = JFactory::getDBO();
		$app = JFactory::getApplication('site');
		$status = new stdClass;
		$status->plugins = array();
		$src = $parent->getParent()->getPath('source');
		$manifest = $parent->getParent()->manifest;
		$plugins = $manifest->xpath('plugins/plugin');

		foreach ($plugins as $plugin)
		{
			$name = (string)$plugin->attributes()->plugin;
			$group = (string)$plugin->attributes()->group;
			$path = $src.'/plugins/'.$group;
			if (JFolder::exists($src.'/plugins/'.$group.'/'.$name))
			{
				$path = $src.'/plugins/'.$group.'/'.$name;
			}
			$installer = new JInstaller;
			$result = $installer->install($path);

			if($type !='update') {
				$query = "UPDATE #__extensions SET enabled=1 WHERE type='plugin' AND element=".$db->Quote($name)." AND folder=".$db->Quote($group);
				$db->setQuery($query);
				$db->query();
			}

			$status->plugins[] = array('name' => $name, 'group' => $group, 'result' => $result);
		}

		
	}


 


	 

}