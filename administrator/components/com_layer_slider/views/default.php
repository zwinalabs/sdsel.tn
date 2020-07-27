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

defined('_JEXEC') or die;
?><?php
register_shutdown_function(__NAMESPACE__.'\ls_before_die');

wp_magic_quotes(); // magic quotes fix

add_action(get_current_screen()->id, 'ls_init_screen_meta');

ob_start();
require_once(JPATH_SITE.'/components/com_layer_slider/base/layerslider.php');

// Import CSS
$document = \JFactory::getDocument();
$document->addStyleSheet('components/com_layer_slider/assets/css/wp-pointer.min.css?ver='.LS_PLUGIN_VERSION);
$document->addStyleSheet('components/com_layer_slider/assets/css/wp_specs.css?ver='.LS_PLUGIN_VERSION);

// Import JS
if (version_compare(JVERSION,'3.0.0','l')) {
  $document->addScript('https://ajax.googleapis.com/ajax/libs/jquery/1.8/jquery.min.js');
} else {
  \JHtml::_('jquery.framework');
}
if (isset(${'_GET'}['action']) && ${'_GET'}['action'] == 'edit') {
	\JHtml::_('behavior.modal');
}

$document->addScript('https://cdnjs.cloudflare.com/ajax/libs/gsap/1.19.0/TweenMax.min.js');
$document->addScript('components/com_layer_slider/assets/js/jquery-ui.min.js');
$document->addScript('components/com_layer_slider/assets/js/wp-pointer.min.js?ver='.LS_PLUGIN_VERSION);
$document->addScript('components/com_layer_slider/assets/js/wp_specs.js?ver='.LS_PLUGIN_VERSION);

$ajaxurl = 'index.php?ajax=1&option=' . ${'_GET'}['option'] . (isset(${'_GET'}['view']) ? '&view='.${'_GET'}['view'] : '');
$lsVersion = LS_PLUGIN_VERSION;
$userSettings = json_encode(array(
	'time' => time(),
	'uid' => \JFactory::getUser()->id,
	'url' => rtrim(\JURI::root(true), '/') . '/'
));
$_wpPluploadSettings = json_encode(array(
	'defaults' => array(
		'multipart_params' => array(
			'_wpnonce' => \JSession::getFormToken()
		)
	)
));
$document->addScriptDeclaration("
	ajaxurl = '$ajaxurl';
	lsVersion = '$lsVersion';
	userSettings = $userSettings;
	_wpPluploadSettings = $_wpPluploadSettings;
");

do_action('init');

if (isset(${'_GET'}['ajax']) && isset($_REQUEST['action'])) {
	// handle AJAX requests
	if ($_REQUEST['action'] == 'upload-attachment') {
		// handle AJAX image upload
		require_once JPATH_BASE . '/components/com_layer_slider/controllers/file.php';
		$fileController = new \Layer_SliderControllerFile();
		$fileController->upload();
	}
	do_action('wp_ajax_'.$_REQUEST['action']);
	echo ob_get_clean();
	jexit();
}

do_action('admin_menu');
do_action('admin_init');
do_action('admin_enqueue_scripts');
do_action('wp_enqueue_scripts');
do_action('admin_notices');
do_action(get_current_screen()->id);

$content = ob_get_clean();

echo preg_replace_callback('/href="(?:admin\.php)?(\?page=.*?)"/', __NAMESPACE__.'\ls_replace_url', $content);