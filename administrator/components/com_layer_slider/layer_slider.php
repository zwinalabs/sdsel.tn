<?php
/*-------------------------------------------------------------------------
# com_layer_slider - Creative Slider
# -------------------------------------------------------------------------
# @ author    John Gera, George Krupa, Janos Biro, Balint Polgarfi
# @ copyright Copyright (C) 2017 Offlajn.com  All Rights Reserved.
# @ license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @ website   http://www.offlajn.com
-------------------------------------------------------------------------*/
$revision = '6.6.053';
?><?php
// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');
jimport('joomla.application.component.view');
jimport('joomla.application.component.model');

if (!class_exists('JControllerLegacy')) {
	class JControllerLegacy extends JController {}
	class JViewLegacy extends JView {}
	class JModelLegacy extends JModel {}
}

// missing ZipArchive fix
if (!class_exists('ZipArchive')) {
	jimport( 'joomla.filesystem.archive' );
	class ZipArchive {

		const CREATE = 1;
		const OVERWRITE = 8;

		private $archive;
		private $mode;
		private $files;

		public function open($archive, $mode = 0) {
			$this->archive = $archive;
			$this->mode = $mode;
			$this->files = array();
			if (!$mode) return file_exists($archive);
		}

		public function extractTo($tmpDir) {
			if (!$this->mode) {
				$zip = JArchive::getAdapter('zip');
				return $zip->extract($this->archive, $tmpDir);
			}
		}

		public function addFromString($file, $data) {
			$this->files[] = array('name' => $file, 'data' => $data);
		}

		public function addFile($filepath, $file) {
			$this->files[] = array('name' => $file, 'data' => file_get_contents($filepath));
		}

		public function close() {
			if ($this->mode) {
				$zip = JArchive::getAdapter('zip');
				$zip->create($this->archive, $this->files);
			}
		}

	}
}

$GLOBALS['j25'] = version_compare(JVERSION, '3.0.0', 'l');

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_layer_slider'))
{
	throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'));
}

// Include dependancies
jimport('joomla.application.component.controller');

$controller	= JControllerLegacy::getInstance('Layer_slider');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
