<?php
/*-------------------------------------------------------------------------
# com_layer_slider - Creative Slider
# -------------------------------------------------------------------------
# @ author    John Gera, George Krupa, Janos Biro, Balint Polgarfi
# @ copyright Copyright (C) 2017 Offlajn.com  All Rights Reserved.
# @ license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @ website   http://www.offlajn.com
-------------------------------------------------------------------------*/
?><?php
// No direct access
defined('_JEXEC') or die;

class Layer_sliderController extends JControllerLegacy
{

	public function display($cachable = false, $urlparams = false)
	{
		$view   = JFactory::getApplication()->input->getCmd('view', 'sliders');
		JFactory::getApplication()->input->set('view', $view);

		if ($view != 'media') {
			require_once JPATH_COMPONENT.'/helpers/layer_slider.php';
		}

		parent::display($cachable, $urlparams);
		return $this;
	}

	public function download_slider($cachable = false, $urlparams = false)
	{
		return $this->display($cachable, $urlparams);
	}

}
