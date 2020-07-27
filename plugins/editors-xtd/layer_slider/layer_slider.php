<?php
/*-------------------------------------------------------------------------
# layer_slider - Creative Slider editor extend
# -------------------------------------------------------------------------
# @ author    Janos Biro, Polgarfi Balint
# @ copyright Copyright (C) 2017 Offlajn.com  All Rights Reserved.
# @ license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @ website   http://www.offlajn.com
-------------------------------------------------------------------------*/
$revision = '6.6.053';
?><?php
defined('_JEXEC') or die;

class PlgButtonLayer_Slider extends JPlugin
{
	protected $autoloadLanguage = true;

	public function onDisplay($name)
	{
		JHtml::_('behavior.modal');

		$button = new JObject;
		$button->modal = true;
		$button->class = 'btn';
		$button->link = 'index.php?option=com_layer_slider&view=sliderlist&tmpl=component';
		$button->text = 'Creative Slider';
		$button->title = 'Insert Creative Slider';
		$button->name = 'article icon-pictures';
		$button->options = "{handler: 'iframe', size: {x: 800, y: 500}}";

		return $button;
	}
}
