<?php
/*-------------------------------------------------------------------------
# mod_creative_slider - Creative Slider
# -------------------------------------------------------------------------
# @ author    John Gera, George Krupa, Janos Biro, Balint Polgarfi
# @ copyright Copyright (C) 2018 Offlajn.com  All Rights Reserved.
# @ license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @ website   http://www.offlajn.com
-------------------------------------------------------------------------*/
namespace CreativeSlider;

defined('_JEXEC') or die;
?><?php

define('LS_ROOT_FILE', JPATH_SITE.'/components/com_layer_slider/base/layerslider.php');
define('LS_ROOT_PATH', JPATH_SITE.'/components/com_layer_slider/base');
define('LS_DB_TABLE', 'layerslider');

require LS_ROOT_PATH.'/classes/class.ls.sources.php';
require LS_ROOT_PATH.'/classes/class.ls.sliders.php';
require LS_ROOT_PATH.'/classes/class.ls.popups.php';
require LS_ROOT_PATH.'/includes/slider_utils.php';
require LS_ROOT_PATH.'/wp/shortcodes.php';
require LS_ROOT_PATH.'/wp/scripts.php';
require LS_ROOT_PATH.'/wp/hooks.php';

if (empty(LS_Sources::$skins)) {
	LS_Sources::addSkins(LS_ROOT_PATH.'/static/layerslider/skins/');
}

// Popup
LS_Popups::init();
foreach (LS_Popups::$index as &$popup) {
	unset($popup['pages']);
}
LS_Popups::setup();

class LS_Posts {

	function __construct() {
		$this->post = new \stdClass();
	}

	static function find($args = array()) {
		$props = $GLOBALS['ls-post'];
		$props['offset'] = $args['offset'];

		if (isset($props['generator_type'])) {
			$gentype = basename($props['generator_type']);
			$genpath = JPATH_ADMINISTRATOR.'/components/com_layer_slider/generators/'.$gentype.'/generator.php';
			if (file_exists($genpath)) {
				require_once $genpath;
				$genclass = 'OfflajnGenerator_'.$gentype;
				return new $genclass($props);
			}
		}
		return new self();
	}

	function getWithFormat($str, $textlength = 0) {
		return $str;
	}

}

function ls_add_google_fonts() {
	if (!isset($GLOBALS['ls-fonts'])) return;
	$fonts = array();
	foreach ($GLOBALS['ls-google-fonts'] as $font) {
		if ($font['admin']) continue;
		list($family) = explode(':', $font['param']);
		$family = strtolower($family);

		foreach (array_keys($GLOBALS['ls-fonts']) as $fontfamily) {
			if ($fontfamily == $family) {
				$fonts[] = $font;
				break;
			}
		}
	}
	if (empty($fonts)) return;

	$lsFonts = array();
	foreach($fonts as $item) {
		if(!$item['admin']) {
			$lsFonts[] = $item['param'];
		} else {
			$lsFonts[] = $item['param'];
		}
	}
	$lsFonts = implode('%7C', $lsFonts);
	$protocol = \JURI::getInstance()->getScheme();
	$query_args = array(
		'family' => $lsFonts,
		'subset' => implode('%2C', $GLOBALS['ls-google-subsets']),
	);

	$doc = \JFactory::getDocument();
	$exists = false;
	$href = $protocol.'://fonts.googleapis.com/css?family='.$query_args['family'].'&subset='.$query_args['subset'];
	$link = '<link id="ls-google-fonts-css" media="all" type="text/css" href="'.$href.'" rel="stylesheet">';
	foreach ($doc->_custom as &$custom) {
		if (substr($custom, 10, 19) == 'ls-google-fonts-css') {
			$exists = true;
			$custom = $link;
		}
	}
	if (!$exists) $doc->addCustomTag($link);
}

function ls_img_path_fix(&$array, $key) {
	static $imgs = '/images/';
	if (!empty($array[$key])) {
		$p = explode($imgs, $array[$key], 2);
		if (!empty($p[1]) && $p[0] != JURI_ROOT && $p[0] != JURI_ROOT_FULL && file_exists(JPATH_SITE.$imgs.$p[1])) {
			$array[$key] = JURI_ROOT.$imgs.$p[1];
		}
	}
}

function ls_pre_parse_defaults($data) {
	// get post options
	$GLOBALS['ls-post'] = array();
	if (isset($data['properties'])) {
		if (isset($data['properties']['generator_type'])) {
			$GLOBALS['ls-post']['generator_type'] = $data['properties']['generator_type'];
		}
		foreach ($data['properties'] as $key => &$value) {
			if (strpos($key, 'post_') === 0) {
				$GLOBALS['ls-post'][$key] = $value;
			}
		}
	}
	// v5.x compatibility fix: add root to image URLs
	$v5_x = $posFix = $parallax = false;
	if (isset($data['properties'])) {
		$v5_x = version_compare(isset($props['sliderVersion']) ? $props['sliderVersion'] : '5.0.0', '6.0.0', '<');
		$props = &$data['properties'];
		ls_img_path_fix($props, 'yourlogo');
		ls_img_path_fix($props, 'backgroundimage');
		if (isset($props['pauseonhover'])) {
			if ($props['pauseonhover'] === true) $props['pauseonhover'] = 'enabled';
			if ($props['pauseonhover'] === false) $props['pauseonhover'] = 'disabled';
		}
		// compatibility fixes: slider background, maxRatio, fullsize, parallax
		if ($v5_x) {
			if (isset($props['background_size'])) $props['globalBGSize'] = $props['background_size'];
			if (isset($props['background_repeat'])) $props['globalBGRepeat'] = $props['background_repeat'];
			if (isset($props['background_position'])) $props['globalBGPosition'] = $props['background_position'];
			if (isset($props['background_behaviour'])) $props['globalBGAttachment'] = $props['background_behaviour'];
			if (isset($props['parallaxtype'])) {
				$parallax = $props['parallaxtype'];
				$props['parallaxScrollReverse'] = 1;
			}
			if (isset($props['parallaxorigin'])) $props['parallaxCenterLayers'] = $props['parallaxorigin'];
			$posFix = isset($props['forceresponsive']);
			if ($posFix) {
				$props['maxRatio'] = 1;
				if (isset($props['fullpage']) && !isset($props['responsive'])) $props['type'] = 'fullsize';
			}
		}
	}
	if ($font = isset($GLOBALS['ls-fonts'])) {
		$GLOBALS['ls-cur-fonts'] = array();
	}
	if (isset($data['layers'])) {
		foreach ($data['layers'] as &$slide) {
			if (isset($slide['properties']) && is_array($slide['properties'])) {
				ls_img_path_fix($slide['properties'], 'background');
				ls_img_path_fix($slide['properties'], 'thumbnail');
				if ($parallax) {
					$slide['properties']['parallaxdistance'] = 40;
					if ($parallax == 'scroll') {
						$slide['properties']['parallaxevent'] = $parallax;
						$slide['properties']['parallaxdurationmove'] = $data['properties']['parallaxscrollduration'];
					} else {
						$slide['properties']['parallaxevent'] = 'cursor';
						$slide['properties']['parallaxdurationmove'] = 450;
						$slide['properties']['parallaxdurationleave'] = 450;
					}
				}
			}
			if (isset($slide['properties']) && !empty($slide['properties']['layer_link'])) {
				$slide['properties']['layer_link'] = str_ireplace('[url]', '[post-url]', $slide['properties']['layer_link']);
			}
			if (isset($slide['sublayers'])) {
				foreach ($slide['sublayers'] as &$layer) {
					$styles = array();
					if ($font && isset($layer['styles']) && $layer['styles']) {
						$styles = json_decode($layer['styles'][1] == '\\' ? stripslashes($layer['styles']) : $layer['styles'], true);
					}
					if ($posFix && isset($layer['transition']) && $layer['transition'] &&
							isset($layer['top']) && strpos($layer['top'], '%') &&
							isset($layer['left']) && strpos($layer['left'], '%')) {
						$postfix = ',"position":"fixed"}';
						if ($layer['transition'][1] == '\\') $postfix = addslashes($postfix);
						$layer['transition'] = substr($layer['transition'], 0, -1).$postfix;
					}
					if (isset($styles['font-family'])) {
						list($family) = explode(',', $styles['font-family']);
						$family = preg_replace('/[^\w ]/', '', trim($family) );
						$family = strtolower( str_replace(' ', '+', $family) );
						if ($family) $GLOBALS['ls-cur-fonts'][$family] = $GLOBALS['ls-fonts'][$family] = true;
					}
					ls_img_path_fix($layer, 'image');
					ls_img_path_fix($layer, 'poster');
					if (!empty($layer['url'])) {
						$layer['url'] = str_ireplace('[url]', '[post-url]', $layer['url']);
					}
				}
			}
		}
	}
	return $data;
}
add_filter('layerslider_pre_parse_defaults', 'ls_pre_parse_defaults');

function ls_post_parse_defaults($data) {
	if (isset($data['properties']) && isset($data['properties']['attrs'])) {
		$data['properties']['attrs']['skinsPath'] = \JURI::root().'components/com_layer_slider/base/static/layerslider/skins/';
	}
	return $data;
}
add_filter('layerslider_post_parse_defaults', 'ls_post_parse_defaults');

if (get_option('load_fonts_dynamic', 0)) {
	$GLOBALS['ls-google-fonts'] = get_option('ls-google-fonts', array());
	$GLOBALS['ls-google-subsets'] = get_option('ls-google-font-scripts', array('latin', 'latin-ext'));
	$GLOBALS['ls-fonts'] = array();
	// remove ls_load_google_fonts from wp_enqueue_scripts actions
	foreach ($GLOBALS['ls_action']['wp_enqueue_scripts'] as $i => $fn) {
		if ('ls_load_google_fonts' == $fn) unset($GLOBALS['ls_action']['wp_enqueue_scripts'][$i]);
	}
}

define('LS_LOAD_MODULE', get_option('load_module', 0));
define('LS_PLUGIN_VERSION', get_option('ls-plugin-version', '6.1.0'));

$document = \JFactory::getDocument();
$document->addScript('https://cdnjs.cloudflare.com/ajax/libs/gsap/1.19.0/TweenMax.min.js');

if (version_compare(JVERSION, '3.0.0', 'l')) {
  if (get_option('load_jquery', false)) {
    $document->addScript(\JURI::getInstance()->getScheme() . '://ajax.googleapis.com/ajax/libs/jquery/1.8/jquery.min.js');
    $document->addScript(\JURI::root() . 'media/offlajn/jquery.noconflict.js');
  }
}
else \JHtml::_('jquery.framework');

$root = rtrim(\JURI::root(true), '/').'/';
$document->addScriptDeclaration("jQuery(function($) {
  $('a[target=ls-scroll]').each(function() {
    var href = this.getAttribute('href'), root = '$root';
    if (href.indexOf(root) === 0) this.setAttribute('href', href.substr(root.length));
  });
});");

do_action('wp_enqueue_scripts');