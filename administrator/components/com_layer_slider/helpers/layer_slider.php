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
$revision = '6.6.053';

defined('_JEXEC') or die;
?><?php

require_once dirname(__FILE__).'/wp_helper.php';

class LS_Img {
	protected $fixes = array();
	protected $action = 'get';

	protected function checkURL(&$array, $key) {
		static $imgs = '/images/';
		if (!empty($array[$key])) {
			$p = explode($imgs, $array[$key], 2);
			if (!empty($p[1]) && $p[0] != JURI_ROOT && $p[0] != JURI_ROOT_FULL && file_exists(JPATH_SITE.$imgs.$p[1])) {
				$this->fixes[ $array[$key] ] = JURI_ROOT_FULL.$imgs.$p[1];
				if ($this->action == 'fix') {
					$array[$key] = JURI_ROOT_FULL.$imgs.$p[1];
					if (isset($array[$key.'Thumb'])) $array[$key.'Thumb'] = $array[$key];
				}
			}
		}
	}

	protected function wrongPaths(&$slider) {
		if (!empty($slider['properties'])) {
			$this->checkURL($slider['properties'], 'yourlogo');
			$this->checkURL($slider['properties'], 'backgroundimage');
			$this->checkURL($slider['properties'], 'preview');
		}
		if (!empty($slider['layers'])) foreach ($slider['layers'] as &$slide) {
			if (!empty($slide['properties'])) {
				$this->checkURL($slide['properties'], 'background');
				$this->checkURL($slide['properties'], 'thumbnail');
			}
			if (!empty($slide['sublayers'])) foreach ($slide['sublayers'] as &$layer) {
				$this->checkURL($layer, 'image');
				$this->checkURL($layer, 'poster');
			}
		}
	}

	public function getWrongPaths(&$slider) {
		$this->action = 'get';
		$this->wrongPaths($slider);
		return $this->fixes;
	}

	public function fixWrongPaths(&$slider) {
		$this->action = 'fix';
		$this->wrongPaths($slider);
		return $this->fixes;
	}
}

function ls_init_screen_meta() {
	?>
	<div id="screen-meta" class="metabox-prefs">
		<div id="contextual-help-wrap" class="hidden no-sidebar" tabindex="-1" aria-label="Contextual Help Tab">
			<div id="contextual-help-back"></div>
			<div id="contextual-help-columns">
				<div class="contextual-help-tabs"><ul></ul></div>
				<div class="contextual-help-tabs-wrap"></div>
			</div>
		</div>
	</div>
	<div id="screen-meta-links"></div>
	<?php
}

function ls_before_die() {
	$headers = headers_list();
	foreach ($headers as $header) {
		if (strpos($header, 'Location: admin.php') === 0) {
			$location = str_replace(array(
				'admin.php?page=layerslider',
				'admin.php?page=ls-style-editor',
				'admin.php?page=ls-skin-editor',
				'admin.php?page=ls-revisions',
			), array(
				'index.php?option=com_layer_slider',
				'index.php?option=com_layer_slider&view=customstyleseditor',
				'index.php?option=com_layer_slider&view=skineditor',
				'index.php?option=com_layer_slider&view=revisions',
			), $header);
			header($location);
			break;
		}
	}
}

function ls_replace_url($match) {
	$url = str_replace(array(
		'page=layerslider',
		'page=ls-skin-editor',
		'page=ls-style-editor',
		'page=ls-transition-builder',
		'page=ls-revisions'
	), array(
		'option=com_layer_slider',
		'option=com_layer_slider&amp;view=skineditor',
		'option=com_layer_slider&amp;view=customstyleseditor',
		'option=com_layer_slider&amp;view=transitionbuilder',
		'option=com_layer_slider&amp;view=revisions',
	), $match[1]);
	return 'href="'. $url .'"';
}

function ls_get_modules() {
	$db = \JFactory::getDbo();

	// get extension id
	$q = $db->getQuery(true);
	$q->select($db->quoteName('extension_id'));
	$q->from($db->quoteName('#__extensions'));
	$q->where($db->quoteName('element') .' = '. $db->quote('mod_layer_slider'));
	$db->setQuery($q);
	$eid = $db->loadResult();

	// get modules
	$q = $db->getQuery(true);
	$q->select($db->quoteName(array('id', 'title', 'params', 'published')));
	$q->from($db->quoteName('#__modules'));
	$q->where($db->quoteName('module') .' = '. $db->quote('mod_layer_slider'));
	$db->setQuery($q);
	$modules = $db->loadObjectList();

	foreach ($modules as &$module) {
		$module->params = $module->params ? json_decode($module->params) : new \stdClass();
	}
	echo json_encode(array( 'eid' => $eid, 'modules' => $modules ));
}
add_action('wp_ajax_ls_get_modules', 'ls_get_modules');

function ls_get_advanced_settings() {
	?>
	<tr class="ls-cache-options">
		<td><?php _e('Use slider markup caching', 'LayerSlider') ?></td>
		<td><input type="checkbox" name="ls_use_cache" <?php echo get_option('ls_use_cache', true) ? 'checked="checked"' : '' ?>></td>
		<td class="desc">
			<?php _e('Enabled caching can drastically increase the plugin performance and spare your server from unnecessary load.', 'LayerSlider') ?>
			<a href="<?php echo wp_nonce_url('?option=com_layer_slider&action=empty_caches', 'empty_caches') ?>" class="button button-small"><?php _e('Empty caches') ?></a>
		</td>
	</tr>
	<tr>
		<td><?php _e('Load jQuery on frontend', 'LayerSlider') ?></td>
		<td><input type="checkbox" name="load_jquery" <?php echo get_option('load_jquery', false) ? 'checked="checked"' : '' ?>></td>
		<td class="desc"><?php _e('Switch this option off if jQuery is already loaded on your site.', 'LayerSlider') ?></td>
	</tr>
	<tr>
		<td><?php _e('Save slide histories', 'LayerSlider') ?></td>
		<td><input type="checkbox" name="save_history" <?php echo get_option('save_history', false) ? 'checked="checked"' : '' ?>></td>
		<td class="desc"><?php _e("Save slide histories (undo, redo) with slider data. Isn't recommanded when post_max_size is small.", 'LayerSlider') ?></td>
	</tr>
	<tr>
		<td><?php _e('Load Google fonts dynamically', 'LayerSlider') ?></td>
		<td><input type="checkbox" name="load_fonts_dynamic" <?php echo get_option('load_fonts_dynamic', false) ? 'checked="checked"' : '' ?>></td>
		<td class="desc"><?php _e('Switch this option on if you want to load only those Google fonts which are used in your slider(s).', 'LayerSlider') ?></td>
	</tr>
	<tr>
		<td><?php _e('Enable load module', 'LayerSlider') ?></td>
		<td><input type="checkbox" name="load_module" <?php echo get_option('load_module', false) ? 'checked="checked"' : '' ?>></td>
		<td class="desc"><?php _e('Within layer\'s content this option accepts module positions, Syntax: {loadposition user1} or Modules by name, Syntax: {loadmodule mod_login}. Optionally can specify module style and for loadmodule a specific module title.', 'LayerSlider') ?></td>
	</tr>
	<tr>
		<td><?php _e('Load uncompressed JS files', 'LayerSlider') ?></td>
		<td><input type="checkbox" name="load_uncompressed" <?php echo get_option('load_uncompressed', false) ? 'checked="checked"' : '' ?>></td>
		<td class="desc"><?php _e('Switch this option on if you want to debug the code', 'LayerSlider') ?></td>
	</tr>
	<tr>
		<td><?php _e('RocketScript compatibility', 'LayerSlider') ?></td>
		<td><input type="checkbox" name="rocketscript_ignore" <?php echo get_option('rocketscript_ignore', false) ? 'checked="checked"' : '' ?>></td>
		<td class="desc"><?php _e('Enable this option to ignore CreativeSlider files by CloudFront’s Rocket Loader, which can help overcoming potential issues.', 'LayerSlider') ?></td>
	</tr>
	<?php
}
add_action('wp_ajax_ls_get_advanced_settings', 'ls_get_advanced_settings');

// override ls_get_post_details
function ls_get_post_results() {
	$input = \JFactory::getApplication()->input;
	$params = $input->post->get('params', null, null);
	$generator_name = !empty($params['generator_type']) ? $params['generator_type'] : 'imagesfromfolder';

	if (strpos($generator_name, '.') !== false)
		die();

	if(is_dir(JPATH_COMPONENT.'/generators/'.$generator_name)){
		if (is_file(JPATH_COMPONENT.'/generators/'.$generator_name.'/generator.php')){
			require_once JPATH_COMPONENT.'/generators/'.$generator_name.'/generator.php';
			$class = '\OfflajnGenerator_'.$generator_name;
		}
	}

	$generator = new $class($params);
	die( json_encode($generator->getData()) );
}
add_action('wp_ajax_ls_get_post_details', 'ls_get_post_results');

function ls_get_generators() {
	$avaible_generators = array();
	if ($handle = opendir(JPATH_COMPONENT.'/generators/')) {
		while (false !== ($entry = readdir($handle))) {
			if ($entry != "." && $entry != ".." && is_dir(JPATH_COMPONENT.'/generators/'.$entry)) {
				if (is_file(JPATH_COMPONENT.'/generators/'.$entry.'/generator.php')){
					require_once JPATH_COMPONENT.'/generators/'.$entry.'/generator.php';
					$class = '\OfflajnGenerator_'.$entry;
					if(is_dir(JPATH_ROOT.$class::$path)){
						$avaible_generators[$entry] = $class::$name;
					}
				}
			}
		}
		closedir($handle);
	}
	die( json_encode(array(
		'header' => __('Dynamic generator content', 'LayerSlider'),
		'subheader' => __('Generator Settings', 'LayerSlider'),
		'options' => $avaible_generators,
	)) );
}
add_action('wp_ajax_ls_get_generators', 'ls_get_generators');

function ls_get_dynamic_filters() {
	$input = \JFactory::getApplication()->input;
	$params = $input->post->get('params', null, null);
	$generator_name = !empty($params['generator_type']) ? $params['generator_type'] : 'imagesfromfolder';

	if (strpos($generator_name, '.') !== false)
		die();

	if(is_dir(JPATH_COMPONENT.'/generators/'.$generator_name)){
		if (is_file(JPATH_COMPONENT.'/generators/'.$generator_name.'/generator.php')){
			require_once JPATH_COMPONENT.'/generators/'.$generator_name.'/generator.php';
			$class = '\OfflajnGenerator_'.$generator_name;
		}
	}

	$generator = new $class($params);
	$result = array(
		"filters" => $generator->getFilters(),
		"orderby" => $generator->getOrderBys(),
		"taglist" => $generator->generateList()
	);
	die( json_encode($result) );
}
add_action('wp_ajax_ls_get_dynamic_filters', 'ls_get_dynamic_filters');

function ls_download_slider() {
	$app = \JFactory::getApplication();
	$id = $app->input->get('id', '', 'STRING');
	$source = 'http://offlajn.com/index2.php?option=com_ls_import&task=download&id='.$id;
	$error = '';
	$curl = function_exists('curl_version');
	$allow_url_fopen = ini_get('allow_url_fopen');

	if ($curl || $allow_url_fopen) {
		set_time_limit(300);
		// create file
		$destination = \JFactory::getConfig()->get('tmp_path', sys_get_temp_dir()) .'/'. basename($id) .'.zip';
		$file = fopen($destination, 'wb');
		$data = false;

		if ($curl) {
			$ch = curl_init($source);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_TIMEOUT, 300);
			curl_setopt($ch, CURLOPT_FILE, $file);
			$data = curl_exec($ch);
			$error = curl_error($ch);
			curl_close($ch);
		}
		if ($data === false && $allow_url_fopen) {
			$data = @file_get_contents($source);
			fputs($file, $data);
		}

		fclose($file);

		if ($data) {
			if (file_exists($destination)) {
				// import file
				include JPATH_SITE.'/components/com_layer_slider/base/layerslider.php';
				include JPATH_SITE.'/components/com_layer_slider/base/classes/class.ls.importutil.php';
				$import = new LS_ImportUtil($destination);
				@unlink($destination);
				// redirect after import
				$app->redirect('index.php?option=com_layer_slider&action=edit&id='.$import->lastImportId);
			} else {
				$error = 'Unable to write file: '.$destination;
			}
		}
	} else {
		$error = 'Please install cURL or enable allow_url_fopen for importing!';
	}
	// display error
	$app->enqueueMessage($error ? $error : 'Unknown error!', 'error');
	$app->redirect('index.php?option=com_layer_slider');
}
if (isset($_REQUEST['task']) && $_REQUEST['task'] == 'download_slider') ls_download_slider();

function ls_save_advanced() {
	$input = \JFactory::getApplication()->input;
  $options = array(
    'ls_use_cache',
    'load_jquery',
    'save_history',
    'load_fonts_dynamic',
    'load_module',
    'load_uncompressed',
    'rocketscript_ignore',
  );
  foreach ($options as $name) {
    update_option($name, $input->get($name) ? 1 : 0);
  }
  \JFactory::getApplication()->redirect('index.php?option=com_layer_slider&message=generalUpdated');
}
if (isset($_REQUEST['ls-save-advanced-settings'])) ls_save_advanced();

function ls_img_path_fix() {
	define('LS_ROOT_PATH', JPATH_SITE.'/components/com_layer_slider/base');
	define('LS_DB_TABLE', 'layerslider');
	require LS_ROOT_PATH.'/classes/class.ls.sliders.php';
	$id = (int) $_REQUEST['id'];
	$slider = LS_Sliders::find($id);
	if (is_array($slider)) {
		$data = json_encode($slider['data']);
		$img = new LS_Img();
		$res = $img->fixWrongPaths($slider['data']);
		if (!empty($res)) {
			require LS_ROOT_PATH.'/classes/class.ls.revisions.php';
			LS_Revisions::add($id, $data);
			LS_Sliders::update($id, $slider['name'], $slider['data'], $slider['slug']);
		}
	}
	\JFactory::getApplication()->redirect('index.php?option=com_layer_slider&action=edit&id='.$id);
}
if (isset($_REQUEST['ls-img-path-fix'])) ls_img_path_fix();

// Joomla doesn't have image id
function ls_get_image_fix($id = null, $url = null) {
	return null;
}
add_filter('ls_get_image', 'ls_get_image_fix');
add_filter('ls_get_thumbnail', 'ls_get_image_fix');

// Fix for empty caches
if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'empty_caches') {
	$GLOBALS['ls_cache']->clean('com_layer_slider');
	wp_redirect('admin.php?page=layerslider&message=cacheEmpty');
}

function ls_save_admin_skin() {
	$skin = \JRequest::getCmd('skin');
	update_option('ls-admin-layout', $skin);
}
add_action('wp_ajax_ls_save_admin_skin', 'ls_save_admin_skin');
${'_GET'}['layout'] = get_option('ls-admin-layout', 'light');
