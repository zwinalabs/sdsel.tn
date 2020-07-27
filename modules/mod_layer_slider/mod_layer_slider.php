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
$revision = '6.6.053';

defined('_JEXEC') or die;
?><?php
require_once JPATH_ADMINISTRATOR.'/components/com_layer_slider/helpers/wp_helper.php';
require_once __DIR__.'/layer_slider_helper.php';

$id = $params->get('slider', 0);

if (isset(LS_Popups::$index[$id])) {
	$module->title = '';
	$attribs['style'] = 'none';

	$display = false;
	foreach (LS_Popups::$popups as $k => &$popup) {
		if ($popup['id'] == $id) {
			$display = true;
			break;
		}
	}
	if (!$display) return;
}

$data = LS_Shortcode::handleShortcode(array('id' => $id));
echo LS_LOAD_MODULE ? \JHtml::_( 'content.prepare', $data) : $data;

// dynamic google font loading
ls_add_google_fonts();

// Conditionally load LayerSlider plugins
if( ! empty( $GLOBALS['lsLoadPlugins'] ) ) {

	// Filter out duplicates
	$GLOBALS['lsLoadPlugins'] = array_unique($GLOBALS['lsLoadPlugins']);

	// Load plugins
	foreach( $GLOBALS['lsLoadPlugins'] as $item ) {
		wp_enqueue_script('layerslider-'.$item);
		wp_enqueue_style('layerslider-'.$item);
	}
}
