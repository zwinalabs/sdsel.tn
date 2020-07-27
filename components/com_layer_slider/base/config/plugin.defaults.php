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

$lsPluginDefaults = array(
	
	// PATHS
	'paths' => array(
		'rootUrl' => plugins_url('', __FILE__),
		'skins' => plugins_url('', __FILE__).'/skins/',
		'transitions' => plugins_url('', __FILE__).'/demos/transitions.json'
	),

	'features' => array(
		'autoupdate' => true
	),

	// INTERFACE
	'interface' => array(

		'settings' => array(

		),

		'fonts' => array(

		),

		'news' => array(
			'display' => true,
			'collapsed' => false
		),

	),

	// Settings
	'settings' => array(
		'scriptsInFooter' => false,
		'conditionalScripts' => false,
		'concatenateOutput' => true,
		'cacheOutput' => false
	)
);

?>