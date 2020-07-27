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
defined('_JEXEC') or die;

class Layer_SliderController extends JControllerLegacy
{
	public function test()
	{
		$url = 'http://offlajn.com/test.php';
		$addr = '';

		if (function_exists('curl_version')) {
			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_TIMEOUT, 10);
			$addr = curl_exec($ch);
			curl_close($ch);
		} else {
			$addr = @file_get_contents($url);
		}

		jexit($addr);
	}
}
