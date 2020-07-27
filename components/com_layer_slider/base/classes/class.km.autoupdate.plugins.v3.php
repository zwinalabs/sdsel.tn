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

class KM_PluginUpdatesV3 extends KM_UpdatesV3 {

	public function __construct($config) {

		// Set up auto updater
		parent::__construct($config);

		// Hook into Plugins API to provide update info
		add_filter('pre_set_site_transient_update_plugins', array(&$this, 'set_update_transient'));
		add_filter('plugins_api', array(&$this, 'set_updates_api_results'), 10, 3);

		// Add notices about plugin activation
		add_filter('upgrader_pre_download', array(&$this, 'pre_download_filter' ), 10, 4);
		add_action('in_plugin_update_message-'.plugin_basename($config['root']), array(&$this, 'update_message'));


		// AJAX actions for site authorization
		add_action('wp_ajax_layerslider_authorize_site', array(&$this, 'handleActivation'));
		add_action('wp_ajax_layerslider_deauthorize_site', array(&$this, 'handleDeactivation'));
	}
}
