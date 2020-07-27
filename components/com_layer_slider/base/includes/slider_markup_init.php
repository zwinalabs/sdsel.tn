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

// Get init code
foreach($slides['properties']['attrs'] as $key => $val) {

	if(is_bool($val)) {
		$val = $val ? 'true' : 'false';
		$init[] = $key.': '.$val;
	} elseif(is_numeric($val)) { $init[] = $key.': '.$val;
	} else { $init[] = "$key: '$val'"; }
}

// Full-size sliders
if( ( !empty($slides['properties']['attrs']['type']) && $slides['properties']['attrs']['type'] === 'fullsize' ) && ( empty($slides['properties']['attrs']['fullSizeMode']) || $slides['properties']['attrs']['fullSizeMode'] !== 'fitheight' ) ) {
	$init[] = 'height: '.$slides['properties']['props']['height'].'';
}

// Popup
if( !empty($slides['properties']['attrs']['type']) && $slides['properties']['attrs']['type'] === 'popup' ) {
	$lsPlugins[] = 'popup';
}

if( ! empty( $lsPlugins ) ) {
	$init[] = 'plugins: ' . json_encode( array_unique( $lsPlugins ) );
}

$init = implode(', ', $init);
$attr = get_option('rocketscript_ignore', false) ? ' data-cfasync="false"' : '';

$lsInit[] = "<script$attr>" . NL;
	$lsInit[] = 'jQuery(document).ready(function() {' . NL;
		$lsInit[] = 'if(typeof jQuery.fn.layerSlider == "undefined") {' . NL;
			$lsInit[] = 'if( window._layerSlider && window._layerSlider.showNotice) { ' . NL;
				$lsInit[] = 'window._layerSlider.showNotice(\''.$sliderID.'\',\'jquery\');' . NL;
			$lsInit[] = '}' . NL;
		$lsInit[] = '} else {' . NL;
			$lsInit[] = 'jQuery("#'.$sliderID.'")';
			if( !empty($slides['callbacks']) && is_array($slides['callbacks']) ) {
				foreach($slides['callbacks'] as $event => $function) {
					$lsInit[] = '.on(\''.$event.'\', '.stripslashes($function).')';
				}
			}
			$lsInit[] = '.layerSlider({'.$init.'});' . NL;
		$lsInit[] = '}' . NL;
	$lsInit[] = '});' . NL;
$lsInit[] = '</script>';
