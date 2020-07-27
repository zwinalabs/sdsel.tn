<?php
/*-------------------------------------------------------------------------
# com_creative_slider - Creative Slider
# -------------------------------------------------------------------------
# @ author    John Gera, George Krupa, Janos Biro, Balint Polgarfi
# @ copyright Copyright (C) 2018 Offlajn.com  All Rights Reserved.
# @ license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @ website   http://www.offlajn.com
-------------------------------------------------------------------------*/
namespace CreativeSlider;
use stdClass, ZipArchive;
defined('_JEXEC') or die;
?><?php



require_once dirname(__FILE__) . '/class.km.autoupdate.v3.php';

class KM_ThemeUpdatesV3 extends KM_UpdatesV3 {

	public function __construct($config) {

		// Set up auto updater
		parent::__construct($config);

	}
}
