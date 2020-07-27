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

define('JURI_ROOT', rtrim(\JURI::root(true), '/'));
define('JURI_ROOT_FULL', rtrim(\JURI::root(), '/'));

defined('ABSPATH') or define('ABSPATH', JPATH_ADMINISTRATOR.'/components/com_layer_slider/ls');
defined('LS_ROOT_URL') or define('LS_ROOT_URL', \JURI::root().'components/com_layer_slider/base');
// module fix
define('WP_CONTENT_DIR', basename(JPATH_COMPONENT) == 'com_layer_slider' ? JPATH_SITE : realpath(__DIR__.'/../../site/base'));

$GLOBALS['ls_xml'] = file_get_contents(JPATH_SITE.'/modules/mod_layer_slider/mod_layer_slider.xml');

function wp_create_nonce($action = -1) {
	return '1';
}

function wp_nonce_field($action = -1, $name = "_wpnonce", $referer = true , $echo = true) {
	return '';
}

function check_admin_referer($action = -1, $query_arg = '_wpnonce') {
	return true;
}

function wp_nonce_url( $actionurl, $action = -1, $name = '_wpnonce' ) {
	return $actionurl . (strpos($actionurl, '?') === false ? '?' : '&amp;') . $name . '=' . wp_create_nonce( $action );
}

function current_user_can( $capability ) {
	return \JFactory::getApplication()->isAdmin();
}

function is_multisite() {
	return false;
}

function plugin_dir_path($file) {
	return dirname($file).'/';
}

function plugin_basename($file) {
	return basename(dirname($file)).'/'.basename($file);
}

function plugin_dir_url($file) {
	$url = str_replace(JPATH_SITE, '', dirname($file));
	$url = \JURI::root() . substr($url, 1);
	return str_replace('\\', '/', $url) . '/';
}

function plugins_url($path = '', $plugin = '') {
	return rtrim(\JURI::root().'components/com_layer_slider/base/'.$path, '/');
}

function content_url($path = '') {
	return rtrim(\JURI::root().'components/com_layer_slider/base/'.$path, '/');
}

function wp_get_attachment_thumb_url($attachment_id) {
	return false;
}

class WP_Widget {
	// TODO
}

function get_posts($args = null) {
	return array();
}
function get_post_type($post = null) {
	return false;
}
function get_post_types() {
	return array();
}
function get_categories() {
	return array();
}
function get_tags() {
	return array();
}
function get_taxonomies() {
	return array();
}
function get_post_thumbnail_id($post_id) {
	return $post_id;
}
function wp_get_attachment_image($attachment_id, $size = 'thumbnail', $icon = false, $attr = '') {
	$attrs = array(
		'src' => $attachment_id,
		'alt' => 'Slide background'
	);
	if (is_array($attr)) {
		$attrs = array_merge($attrs, $attr);
	}
	$img = '<img';
	foreach ($attrs as $key => &$value) {
		$img .= ' '.$key.'="'.$value.'"';
	}
	$img .= '>';
	return $img;
}
function wp_get_attachment_url($id) {
	return false;
}
function wp_get_attachment_image_url($attachment_id, $size = 'thumbnail', $icon = false) {
  return false;
}
function wp_get_attachment_image_src($attachment_id, $size = 'thumbnail', $icon = false) {
  return array('');
}

$GLOBALS['ls_action'] = array();

function add_action($tag, $func) {
	if (!isset($GLOBALS['ls_action'][$tag]))
		$GLOBALS['ls_action'][$tag] = array();
	$GLOBALS['ls_action'][$tag][] = $func;
}

function do_action($tag, $arg = array()) {
	if (isset($GLOBALS['ls_action'][$tag])) {
		foreach ($GLOBALS['ls_action'][$tag] as $func) {
			call_user_func_array(__NAMESPACE__.'\\'.$func, $arg);
		}
	}
}

function has_action($tag, $func_to_check = '') {
	return isset($GLOBALS['ls_action'][$tag]);
}

$GLOBALS['ls_filter'] = array();

function add_filter($tag, $func) {
	if (!isset($GLOBALS['ls_filter'][$tag]))
		$GLOBALS['ls_filter'][$tag] = array();
	$GLOBALS['ls_filter'][$tag][] = $func;
}

function apply_filters($tag, $value, $var = null) {
	if (isset($GLOBALS['ls_filter'][$tag])) {
		foreach ($GLOBALS['ls_filter'][$tag] as $func) {
			if ($var === null) {
				$value = is_string($func) ? call_user_func(__NAMESPACE__.'\\'.$func, $value) : $func[0]->{$func[1]}($value);
			} else {
				$value = is_string($func) ? call_user_func(__NAMESPACE__.'\\'.$func, $value, $var) : $func[0]->{$func[1]}($value, $var);
			}
		}
	}
	return $value;
}

function has_filter($tag, $func_to_check = '') {
	return isset($GLOBALS['ls_filter'][$tag]);
}

$GLOBALS['ls_shortcode'] = array();

function add_shortcode($tag, $func) {
	$GLOBALS['ls_shortcode'][$tag] = $func;
}

function shortcode_parse_atts($text) {
	$atts = array();
	$pattern = '/(\w+)\s*=\s*"([^"]*)"(?:\s|$)|(\w+)\s*=\s*\'([^\']*)\'(?:\s|$)|(\w+)\s*=\s*([^\s\'"]+)(?:\s|$)|"([^"]*)"(?:\s|$)|(\S+)(?:\s|$)/';
	$text = preg_replace("/[\x{00a0}\x{200b}]+/u", " ", $text);
	if ( preg_match_all($pattern, $text, $match, PREG_SET_ORDER) ) {
		foreach ($match as $m) {
			if (!empty($m[1]))
				$atts[strtolower($m[1])] = stripcslashes($m[2]);
			elseif (!empty($m[3]))
				$atts[strtolower($m[3])] = stripcslashes($m[4]);
			elseif (!empty($m[5]))
				$atts[strtolower($m[5])] = stripcslashes($m[6]);
			elseif (isset($m[7]) and strlen($m[7]))
				$atts[] = stripcslashes($m[7]);
			elseif (isset($m[8]))
				$atts[] = stripcslashes($m[8]);
		}
	} else {
		$atts = ltrim($text);
	}
	return $atts;
}

function do_shortcode_tag( $m ) {
	// allow [[foo]] syntax for escaping a tag
	if ( $m[1] == '[' && $m[6] == ']' ) {
		return substr($m[0], 1, -1);
	}

	$tag = $m[2];
	$attr = shortcode_parse_atts( $m[3] );

	if ( isset( $m[5] ) ) {
		// enclosing tag - extra parameter
		return $m[1] . call_user_func( $GLOBALS['ls_shortcode'][$tag], $attr, $m[5], $tag ) . $m[6];
	} else {
		// self-closing tag
		return $m[1] . call_user_func( $GLOBALS['ls_shortcode'][$tag], $attr, null,  $tag ) . $m[6];
	}
}

function do_shortcode($content) {
	if (false === strpos($content, '['))
		return $content;

	if (empty($GLOBALS['ls_shortcode']))
		return $content;

	$pattern = '\[(\[?)(' . addcslashes(implode('|', array_keys($GLOBALS['ls_shortcode'])), '-') .
		')(?![\w-])([^\]\/]*(?:\/(?!\])[^\]\/]*)*?)(?:(\/)\]|\](?:([^\[]*+(?:\[(?!\/\2\])[^\[]*+)*+)\[\/\2\])?)(\]?)';
	return preg_replace_callback("/$pattern/s", 'do_shortcode_tag', $content);
}

function shortcode_exists($tag) {
	return isset($GLOBALS['ls_shortcode'][$tag]);
}

function wp_insert_attachment($attachment, $filename, $parent_post_id) {
	return 0;
}

function wp_generate_attachment_metadata( $attachment_id, $file ) {
	return array();
}

defined('OBJECT') or define('OBJECT', 'OBJECT');
defined('OBJECT_K') or define('OBJECT_K', 'OBJECT_K');
defined('ARRAY_A') or define('ARRAY_A', 'ARRAY_A');
defined('ARRAY_N') or define('ARRAY_N', 'ARRAY_N');

class WP_DB {

	var $prefix = '#__';
	var $options = '#__layerslider_options';

	var $insert_id = 0;
	var $result = null;

	function prepare($str, $arg1) {
		if (stripos($str, '#__layerslider') === false) {
			return 'SELECT 0';
		}
		return sprintf($str, $arg1);
	}

	function query($q) {
		$db = \JFactory::getDbo();
		$db->setQuery($q);
		return $db->execute();
	}

	function get_var($q) {
		$db = \JFactory::getDbo();
		$db->setQuery($q);
		list($res) = $db->loadRow();
		return $res;
	}

	function _real_escape( $string ) {
		$db = \JFactory::getDbo();
		return $db->escape($string);
	}

	function _escape( $data ) {
		if ( is_array( $data ) ) {
			foreach ( $data as $k => $v ) {
				if ( is_array($v) )
					$data[$k] = $this->_escape( $v );
				else
					$data[$k] = $this->_real_escape( $v );
			}
		} else {
			$data = $this->_real_escape( $data );
		}

		return $data;
	}

	function get_results( $query = null, $output = OBJECT ) {
		$db = \JFactory::getDbo();
		$db->setQuery($query);
		if ($output == ARRAY_A) {
			$res = $db->loadAssocList();
		} else {
			$res = $db->loadObjectList();
		}
		return $res;
	}

	function get_row($query = null, $output = OBJECT, $y = 0) {
		$db = \JFactory::getDbo();
		$db->setQuery($query);
		if ($output == ARRAY_A) {
			$this->result = $db->loadAssoc();
		} else {
			$this->result = $db->loadObject();
		}
		return $this->result;
	}

	function get_col($query = null, $x = 0) {
		$db = \JFactory::getDbo();
		$db->setQuery($query);
		$res = $db->loadRow();
		return $res;
	}

	function insert($table, $data, $format) {
		$obj = new \stdClass();
		if (is_string($format)) {
			$format = array_fill(0, count($data), $format);
		}
		$i = 0;
		foreach ($data as $key => $value) {
			$obj->{$key} = sprintf($format[$i++], $value);
		}
		$db = \JFactory::getDbo();
		$res = $db->insertObject($table, $obj);
		$this->insert_id = $db->insertid();
		return $res ? 1 : false;
	}

	function update($table, $data, $where, $format, $format_where = '%d') {
		$obj = new \stdClass();
		if (is_string($format)) {
			$format = array_fill(0, count($data), $format);
		}
		if (is_string($format_where)) {
			$format_where = array_fill(0, count($where), $format_where);
		}
		$i = 0;
		foreach ($data as $key => $value) {
			// $obj->{$key} = sprintf($format[$i++], $value);
			$obj->{$key} = $format[$i++] == '%s' ? (string)$value : (int)$value;
		}
		$i = 0;
		foreach ($where as $key => $value) {
			// $obj->{$key} = sprintf($format_where[$i++], $value);
			$obj->{$key} = $format_where[$i++] == '%s' ? (string)$value : (int)$value;
		}
		$db = \JFactory::getDbo();
		$where_keys = array_keys($where);
		$res = $db->updateObject($table, $obj, empty($where_keys[1]) ? $where_keys[0] : $where_keys);
		return $res ? 1 : false;
	}

	function delete($table, $where, $where_format) {
		if (is_string($where_format)) {
			$where_format = array_fill(0, count($where), $where_format);
		}
		$db = \JFactory::getDbo();
		$conditions = array();
		$i = 0;
		foreach ($where as $key => $value) {
			$f = $where_format[$i++];
			$c = $db->quoteName($key) .' = '. ($f == '%s' ? $db->quote(sprintf($f, $value)) : sprintf($f, $value));
			$conditions[] = $c;
		}
		$query = $db->getQuery(true);
		$query->delete($db->quoteName($table));
		$query->where($conditions);
		$db->setQuery($query);
		$res = $db->execute();
		return $res ? 1 : false;
	}

}

global $lsdb;
$lsdb = new WP_DB();

function esc_sql( $data ) {
	global $lsdb;
	return $lsdb->_escape($data);
}

function get_userdata( $userid ) {
	$user = \JFactory::getUser($userid);
	$user->user_nicename = $user->name;
	// TODO
	return $user;
}

function get_avatar_url( $id_or_email, $args = null ) {
	// TODO
	return 'data:image/gif;base'.'64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==';
}

function get_user_option( $option, $user = 0 ) {
	// TODO
	return true;
}

function get_bloginfo($show = '', $filter = 'raw') {
	// TODO
	return '';
}

function add_menu_page($page_title, $menu_title, $capability, $menu_slug, $function = '', $icon_url = '', $position = null) {
	$screen_id = 'toplevel_page_' . $menu_slug;
	add_action($screen_id, $function);
	return $screen_id;
}

function add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function ) {
	$screen_id = $parent_slug . '_page_' . $menu_slug;
	add_action($screen_id, $function);
	return $screen_id;
}

function menu_page_url($menu_slug, $echo) {
	return '?option=com_layer_slider';
}

function is_ssl() {
	return !!preg_match('/^https/i', \JURI::root());
}

define( 'MINUTE_IN_SECONDS', 60 );
define( 'HOUR_IN_SECONDS',   60 * MINUTE_IN_SECONDS );
define( 'DAY_IN_SECONDS',    24 * HOUR_IN_SECONDS   );
define( 'WEEK_IN_SECONDS',    7 * DAY_IN_SECONDS    );
define( 'MONTH_IN_SECONDS',  30 * DAY_IN_SECONDS    );
define( 'YEAR_IN_SECONDS',  365 * DAY_IN_SECONDS    );

function human_time_diff( $from, $to = '' ) {
	if ( empty( $to ) ) {
		$to = time();
	}

	$diff = (int) abs( $to - $from );

	if ( $diff < HOUR_IN_SECONDS ) {
		$mins = round( $diff / MINUTE_IN_SECONDS );
		if ( $mins <= 1 )
			$mins = 1;
		/* translators: min=minute */
		$since = sprintf( _n( '%s min', '%s mins', $mins ), $mins );
	} elseif ( $diff < DAY_IN_SECONDS && $diff >= HOUR_IN_SECONDS ) {
		$hours = round( $diff / HOUR_IN_SECONDS );
		if ( $hours <= 1 )
			$hours = 1;
		$since = sprintf( _n( '%s hour', '%s hours', $hours ), $hours );
	} elseif ( $diff < WEEK_IN_SECONDS && $diff >= DAY_IN_SECONDS ) {
		$days = round( $diff / DAY_IN_SECONDS );
		if ( $days <= 1 )
			$days = 1;
		$since = sprintf( _n( '%s day', '%s days', $days ), $days );
	} elseif ( $diff < MONTH_IN_SECONDS && $diff >= WEEK_IN_SECONDS ) {
		$weeks = round( $diff / WEEK_IN_SECONDS );
		if ( $weeks <= 1 )
			$weeks = 1;
		$since = sprintf( _n( '%s week', '%s weeks', $weeks ), $weeks );
	} elseif ( $diff < YEAR_IN_SECONDS && $diff >= MONTH_IN_SECONDS ) {
		$months = round( $diff / MONTH_IN_SECONDS );
		if ( $months <= 1 )
			$months = 1;
		$since = sprintf( _n( '%s month', '%s months', $months ), $months );
	} elseif ( $diff >= YEAR_IN_SECONDS ) {
		$years = round( $diff / YEAR_IN_SECONDS );
		if ( $years <= 1 )
			$years = 1;
		$since = sprintf( _n( '%s year', '%s years', $years ), $years );
	}

	return apply_filters( 'human_time_diff', $since, $diff, $from, $to );
}

function get_permalink($post = 0, $leavename = false) {
	return '';
}

function add_user_meta($user_id, $key, $value, $unique = false) {
	return add_option('u'.(int)$user_id.'_'.$key, $value);
}

function get_user_meta($user_id, $key = '', $single = false) {
	if ('ls-sliders-layout' == $key && isset(${'_GET'}['view']) && ${'_GET'}['view'] == 'sliderlist') return 'list'; // fix for sliderlist view
	return get_option('u'.(int)$user_id.'_'.$key);
}

function update_user_meta($user_id, $key, $value, $prev_value = '') {
	return update_option('u'.(int)$user_id.'_'.$key, $value);
}

function wp_remote_retrieve_body($response) {
	return $response['body'];
}

function wp_remote_get($url, $args = array()) {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	$resp = curl_exec($ch);
	$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);
	return array('body' => $resp);
}

function get_current_screen() {
	return isset($GLOBALS['ls_screen']) ? $GLOBALS['ls_screen'] : (object) array('id' => '', 'base' => '');
}

function register_activation_hook($file, $func) {
	// TODO
}
function register_deactivation_hook($file, $func) {
	// TODO
}
function register_uninstall_hook($file, $func) {
	// TODO
}

function wp_die($string) {
	jexit($string);
}

function wp_translate($text, $domain) {
	$key = 'LS_'.strtoupper( rtrim(preg_replace('/\W+/', '_', $text), '_') );
	$out = \JText::_($key);
	return $key == $out ? $text : $out;
}
function __($text, $domain = 'default' ) {
	return wp_translate( $text, $domain );
}
function _e( $text, $domain = 'default' ) {
	echo wp_translate( $text, $domain );
}
function _n( $single, $plural, $number, $domain = 'default' ) {
	return wp_translate($number > 1 ? $plural : $single, $domain);
}
function _x( $text, $context, $domain = 'default' ) {
	// TODO
	return wp_translate( $text, $domain );
}
function _ex( $text, $context, $domain = 'default' ) {
	// TODO
	echo wp_translate( $text, $domain );
}

function get_allowed_mime_types() {
	return apply_filters('allowed_mime_types', array(
	// Image formats
	'jpg|jpeg|jpe'                 => 'image/jpeg',
	'gif'                          => 'image/gif',
	'png'                          => 'image/png',
	'bmp'                          => 'image/bmp',
	'tif|tiff'                     => 'image/tiff',
	'ico'                          => 'image/x-icon',

	// Video formats
	'asf|asx'                      => 'video/x-ms-asf',
	'wmv'                          => 'video/x-ms-wmv',
	'wmx'                          => 'video/x-ms-wmx',
	'wm'                           => 'video/x-ms-wm',
	'avi'                          => 'video/avi',
	'divx'                         => 'video/divx',
	'flv'                          => 'video/x-flv',
	'mov|qt'                       => 'video/quicktime',
	'mpeg|mpg|mpe'                 => 'video/mpeg',
	'mp4|m4v'                      => 'video/mp4',
	'ogv'                          => 'video/ogg',
	'webm'                         => 'video/webm',
	'mkv'                          => 'video/x-matroska',

	// Text formats
	'txt|asc|c|cc|h'               => 'text/plain',
	'csv'                          => 'text/csv',
	'tsv'                          => 'text/tab-separated-values',
	'ics'                          => 'text/calendar',
	'rtx'                          => 'text/richtext',
	'css'                          => 'text/css',
	'htm|html'                     => 'text/html',

	// Audio formats
	'mp3|m4a|m4b'                  => 'audio/mpeg',
	'ra|ram'                       => 'audio/x-realaudio',
	'wav'                          => 'audio/wav',
	'ogg|oga'                      => 'audio/ogg',
	'mid|midi'                     => 'audio/midi',
	'wma'                          => 'audio/x-ms-wma',
	'wax'                          => 'audio/x-ms-wax',
	'mka'                          => 'audio/x-matroska',

	// Misc application formats
	'rtf'                          => 'application/rtf',
	'js'                           => 'application/javascript',
	'pdf'                          => 'application/pdf',
	'swf'                          => 'application/x-shockwave-flash',
	'class'                        => 'application/java',
	'tar'                          => 'application/x-tar',
	'zip'                          => 'application/zip',
	'gz|gzip'                      => 'application/x-gzip',
	'rar'                          => 'application/rar',
	'7z'                           => 'application/x-7z-compressed',
	'exe'                          => 'application/x-msdownload',

	// MS Office formats
	'doc'                          => 'application/msword',
	'pot|pps|ppt'                  => 'application/vnd.ms-powerpoint',
	'wri'                          => 'application/vnd.ms-write',
	'xla|xls|xlt|xlw'              => 'application/vnd.ms-excel',
	'mdb'                          => 'application/vnd.ms-access',
	'mpp'                          => 'application/vnd.ms-project',
	'docx'                         => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
	'docm'                         => 'application/vnd.ms-word.document.macroEnabled.12',
	'dotx'                         => 'application/vnd.openxmlformats-officedocument.wordprocessingml.template',
	'dotm'                         => 'application/vnd.ms-word.template.macroEnabled.12',
	'xlsx'                         => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
	'xlsm'                         => 'application/vnd.ms-excel.sheet.macroEnabled.12',
	'xlsb'                         => 'application/vnd.ms-excel.sheet.binary.macroEnabled.12',
	'xltx'                         => 'application/vnd.openxmlformats-officedocument.spreadsheetml.template',
	'xltm'                         => 'application/vnd.ms-excel.template.macroEnabled.12',
	'xlam'                         => 'application/vnd.ms-excel.addin.macroEnabled.12',
	'pptx'                         => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
	'pptm'                         => 'application/vnd.ms-powerpoint.presentation.macroEnabled.12',
	'ppsx'                         => 'application/vnd.openxmlformats-officedocument.presentationml.slideshow',
	'ppsm'                         => 'application/vnd.ms-powerpoint.slideshow.macroEnabled.12',
	'potx'                         => 'application/vnd.openxmlformats-officedocument.presentationml.template',
	'potm'                         => 'application/vnd.ms-powerpoint.template.macroEnabled.12',
	'ppam'                         => 'application/vnd.ms-powerpoint.addin.macroEnabled.12',
	'sldx'                         => 'application/vnd.openxmlformats-officedocument.presentationml.slide',
	'sldm'                         => 'application/vnd.ms-powerpoint.slide.macroEnabled.12',
	'onetoc|onetoc2|onetmp|onepkg' => 'application/onenote',

	// OpenOffice formats
	'odt'                          => 'application/vnd.oasis.opendocument.text',
	'odp'                          => 'application/vnd.oasis.opendocument.presentation',
	'ods'                          => 'application/vnd.oasis.opendocument.spreadsheet',
	'odg'                          => 'application/vnd.oasis.opendocument.graphics',
	'odc'                          => 'application/vnd.oasis.opendocument.chart',
	'odb'                          => 'application/vnd.oasis.opendocument.database',
	'odf'                          => 'application/vnd.oasis.opendocument.formula',

	// WordPerfect formats
	'wp|wpd'                       => 'application/wordperfect',

	// iWork formats
	'key'                          => 'application/vnd.apple.keynote',
	'numbers'                      => 'application/vnd.apple.numbers',
	'pages'                        => 'application/vnd.apple.pages',
	));
}

function wp_get_mime_types() {
	return apply_filters( 'mime_types', array(
	// Image formats.
	'jpg|jpeg|jpe' => 'image/jpeg',
	'gif' => 'image/gif',
	'png' => 'image/png',
	'bmp' => 'image/bmp',
	'tiff|tif' => 'image/tiff',
	'ico' => 'image/x-icon',
	// Video formats.
	'asf|asx' => 'video/x-ms-asf',
	'wmv' => 'video/x-ms-wmv',
	'wmx' => 'video/x-ms-wmx',
	'wm' => 'video/x-ms-wm',
	'avi' => 'video/avi',
	'divx' => 'video/divx',
	'flv' => 'video/x-flv',
	'mov|qt' => 'video/quicktime',
	'mpeg|mpg|mpe' => 'video/mpeg',
	'mp4|m4v' => 'video/mp4',
	'ogv' => 'video/ogg',
	'webm' => 'video/webm',
	'mkv' => 'video/x-matroska',
	'3gp|3gpp' => 'video/3gpp', // Can also be audio
	'3g2|3gp2' => 'video/3gpp2', // Can also be audio
	// Text formats.
	'txt|asc|c|cc|h|srt' => 'text/plain',
	'csv' => 'text/csv',
	'tsv' => 'text/tab-separated-values',
	'ics' => 'text/calendar',
	'rtx' => 'text/richtext',
	'css' => 'text/css',
	'htm|html' => 'text/html',
	'vtt' => 'text/vtt',
	'dfxp' => 'application/ttaf+xml',
	// Audio formats.
	'mp3|m4a|m4b' => 'audio/mpeg',
	'ra|ram' => 'audio/x-realaudio',
	'wav' => 'audio/wav',
	'ogg|oga' => 'audio/ogg',
	'mid|midi' => 'audio/midi',
	'wma' => 'audio/x-ms-wma',
	'wax' => 'audio/x-ms-wax',
	'mka' => 'audio/x-matroska',
	// Misc application formats.
	'rtf' => 'application/rtf',
	'js' => 'application/javascript',
	'pdf' => 'application/pdf',
	'swf' => 'application/x-shockwave-flash',
	'class' => 'application/java',
	'tar' => 'application/x-tar',
	'zip' => 'application/zip',
	'gz|gzip' => 'application/x-gzip',
	'rar' => 'application/rar',
	'7z' => 'application/x-7z-compressed',
	'exe' => 'application/x-msdownload',
	'psd' => 'application/octet-stream',
	'xcf' => 'application/octet-stream',
	// MS Office formats.
	'doc' => 'application/msword',
	'pot|pps|ppt' => 'application/vnd.ms-powerpoint',
	'wri' => 'application/vnd.ms-write',
	'xla|xls|xlt|xlw' => 'application/vnd.ms-excel',
	'mdb' => 'application/vnd.ms-access',
	'mpp' => 'application/vnd.ms-project',
	'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
	'docm' => 'application/vnd.ms-word.document.macroEnabled.12',
	'dotx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.template',
	'dotm' => 'application/vnd.ms-word.template.macroEnabled.12',
	'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
	'xlsm' => 'application/vnd.ms-excel.sheet.macroEnabled.12',
	'xlsb' => 'application/vnd.ms-excel.sheet.binary.macroEnabled.12',
	'xltx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.template',
	'xltm' => 'application/vnd.ms-excel.template.macroEnabled.12',
	'xlam' => 'application/vnd.ms-excel.addin.macroEnabled.12',
	'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
	'pptm' => 'application/vnd.ms-powerpoint.presentation.macroEnabled.12',
	'ppsx' => 'application/vnd.openxmlformats-officedocument.presentationml.slideshow',
	'ppsm' => 'application/vnd.ms-powerpoint.slideshow.macroEnabled.12',
	'potx' => 'application/vnd.openxmlformats-officedocument.presentationml.template',
	'potm' => 'application/vnd.ms-powerpoint.template.macroEnabled.12',
	'ppam' => 'application/vnd.ms-powerpoint.addin.macroEnabled.12',
	'sldx' => 'application/vnd.openxmlformats-officedocument.presentationml.slide',
	'sldm' => 'application/vnd.ms-powerpoint.slide.macroEnabled.12',
	'onetoc|onetoc2|onetmp|onepkg' => 'application/onenote',
	'oxps' => 'application/oxps',
	'xps' => 'application/vnd.ms-xpsdocument',
	// OpenOffice formats.
	'odt' => 'application/vnd.oasis.opendocument.text',
	'odp' => 'application/vnd.oasis.opendocument.presentation',
	'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
	'odg' => 'application/vnd.oasis.opendocument.graphics',
	'odc' => 'application/vnd.oasis.opendocument.chart',
	'odb' => 'application/vnd.oasis.opendocument.database',
	'odf' => 'application/vnd.oasis.opendocument.formula',
	// WordPerfect formats.
	'wp|wpd' => 'application/wordperfect',
	// iWork formats.
	'key' => 'application/vnd.apple.keynote',
	'numbers' => 'application/vnd.apple.numbers',
	'pages' => 'application/vnd.apple.pages',
	) );
}

function wp_check_filetype( $filename, $mimes = null ) {
	if ( empty($mimes) )
		$mimes = get_allowed_mime_types();
	$type = false;
	$ext = false;

	foreach ( $mimes as $ext_preg => $mime_match ) {
		$ext_preg = '!\.(' . $ext_preg . ')$!i';
		if ( preg_match( $ext_preg, $filename, $ext_matches ) ) {
			$type = $mime_match;
			$ext = $ext_matches[1];
			break;
		}
	}

	return compact( 'ext', 'type' );
}

function sanitize_file_name( $filename ) {
	$filename_raw = $filename;
	$special_chars = array("?", "[", "]", "/", "\\", "=", "<", ">", ":", ";", ",", "'", "\"", "&", "$", "#", "*", "(", ")", "|", "~", "`", "!", "{", "}", "%", "+", chr(0));
	$special_chars = apply_filters( 'sanitize_file_name_chars', $special_chars, $filename_raw );
	$filename = preg_replace( "#\x{00a0}#siu", ' ', $filename );
	$filename = str_replace( $special_chars, '', $filename );
	$filename = str_replace( array( '%20', '+' ), '-', $filename );
	$filename = preg_replace( '/[\r\n\t -]+/', '-', $filename );
	$filename = trim( $filename, '.-_' );

	if ( false === strpos( $filename, '.' ) ) {
		$mime_types = wp_get_mime_types();
		$filetype = wp_check_filetype( 'test.' . $filename, $mime_types );
		if ( $filetype['ext'] === $filename ) {
			$filename = 'unnamed-file.' . $filetype['ext'];
		}
	}

	// Split the filename into a base and extension[s]
	$parts = explode('.', $filename);

	// Return if only one extension
	if ( count( $parts ) <= 2 ) {
		/**
		 * Filter a sanitized filename string.
		 *
		 * @since 2.8.0
		 *
		 * @param string $filename     Sanitized filename.
		 * @param string $filename_raw The filename prior to sanitization.
		 */
		return apply_filters( 'sanitize_file_name', $filename, $filename_raw );
	}

	// Process multiple extensions
	$filename = array_shift($parts);
	$extension = array_pop($parts);
	$mimes = get_allowed_mime_types();

	/*
	 * Loop over any intermediate extensions. Postfix them with a trailing underscore
	 * if they are a 2 - 5 character long alpha string not in the extension whitelist.
	 */
	foreach ( (array) $parts as $part) {
		$filename .= '.' . $part;

		if ( preg_match("/^[a-zA-Z]{2,5}\d?$/", $part) ) {
			$allowed = false;
			foreach ( $mimes as $ext_preg => $mime_match ) {
				$ext_preg = '!^(' . $ext_preg . ')$!i';
				if ( preg_match( $ext_preg, $part ) ) {
					$allowed = true;
					break;
				}
			}
			if ( !$allowed )
				$filename .= '_';
		}
	}
	$filename .= '.' . $extension;
	/** This filter is documented in wp-includes/formatting.php */
	return apply_filters('sanitize_file_name', $filename, $filename_raw);
}

/* MAGIC QUOTES */
function map_deep( $value, $callback ) {
	if ( is_array( $value ) ) {
		foreach ( $value as $index => $item ) {
			$value[ $index ] = map_deep( $item, $callback );
		}
	} elseif ( is_object( $value ) ) {
		$object_vars = get_object_vars( $value );
		foreach ( $object_vars as $property_name => $property_value ) {
			$value->$property_name = map_deep( $property_value, $callback );
		}
	} else {
		$value = call_user_func( __NAMESPACE__.'\\'.$callback, $value );
	}

	return $value;
}
function stripslashes_from_strings_only( $value ) {
	return is_string( $value ) ? stripslashes( $value ) : $value;
}
function stripslashes_deep( $value ) {
	return map_deep( $value, 'stripslashes_from_strings_only' );
}
function add_magic_quotes( $array ) {
	foreach ( (array) $array as $k => $v ) {
		if ( is_array( $v ) ) {
			$array[$k] = add_magic_quotes( $v );
		} else {
			$array[$k] = addslashes( $v );
		}
	}
	return $array;
}
function wp_magic_quotes() {
	// If already slashed, strip.
	if ( get_magic_quotes_gpc() ) {
		${'_GET'}    = stripslashes_deep( ${'_GET'}    );
		${'_POST'}   = stripslashes_deep( ${'_POST'}   );
		${'_COOKIE'} = stripslashes_deep( ${'_COOKIE'} );
	}

	// Escape with wpdb.
	${'_GET'}    = add_magic_quotes( ${'_GET'}    );
	${'_POST'}   = add_magic_quotes( ${'_POST'}   );
	${'_COOKIE'} = add_magic_quotes( ${'_COOKIE'} );
	$_SERVER = add_magic_quotes( $_SERVER );

	// Force REQUEST to be GET + POST.
	$_REQUEST = array_merge( ${'_GET'}, ${'_POST'} );
}

if(!defined('NL')) { define("NL", "\r\n"); }
if(!defined('TAB')) { define("TAB", "\t"); }

function load_plugin_textdomain( $domain, $abs_rel_path, $plugin_rel_path ) {
	// TODO
}

function is_admin() {
	return \JFactory::getApplication()->isAdmin();
}
function is_front_page() {
	$menu = \JFactory::getApplication()->getMenu();
	return $menu->getActive() == $menu->getDefault();
}

function wp_get_current_user() {
	return \JFactory::getUser();
}
function get_current_user_id() {
	return \JFactory::getUser()->id;
}

function date_i18n($dateformatstring, $unixtimestamp, $gmt = false) {
	$date = (array) new \JDate($unixtimestamp);
	return $date['date'];
}

$GLOBALS['ls_option'] = array(
	'layerslider-authorized-site' => true,
	'timezone_string' => date_default_timezone_get()
);

function get_option( $option, $default = false) {
	if (isset($GLOBALS['ls_option'][$option])) {
		return $GLOBALS['ls_option'][$option];
	}
	$db = \JFactory::getDbo();
	$query = $db->getQuery(true);
	$query->select("option_value");
	$query->from($db->quoteName('#__layerslider_options'));
	$query->where($db->quoteName('option_name')." = '".$option."'");
	$db->setQuery($query);

	if ($row = $db->loadRow()) {
		$default = json_decode($row[0], true);
	}
	$GLOBALS['ls_option'][$option] = &$default;
	return $default;
}

function add_option($option, $value) {
	$data = new \stdClass();
	$data->option_name = $option;
	$data->option_value = json_encode($value);

	if ($res = \JFactory::getDbo()->insertObject('#__layerslider_options', $data)) {
		$GLOBALS['ls_option'][$option] = &$value;
	}
	return $res;
}

function update_option($option, $value) {
	$db = \JFactory::getDbo();
	$query = $db->getQuery(true);
	$query->select('*');
	$query->from($db->quoteName('#__layerslider_options'));
	$query->where($db->quoteName('option_name')." = '".$option."'");
	$db->setQuery($query);
	$update = $db->loadRow();

	$data = new \stdClass();
	$data->option_name = $option;
	$data->option_value = json_encode($value);

	if ($update) {
		$res = $db->updateObject('#__layerslider_options', $data, 'option_name');
	} else {
		$res = $db->insertObject('#__layerslider_options', $data);
	}
	if ($res) {
		$GLOBALS['ls_option'][$option] = &$value;
	}
	return $res;
}

function delete_option($option) {
	$db = \JFactory::getDbo();
	$query = $db->getQuery(true);
	$query->delete($db->quoteName('#__layerslider_options'));
	$query->where($db->quoteName('option_name')." = '".$option."'");
	$db->setQuery($query);
	if ($res = $db->execute()) {
		unset($GLOBALS['ls_option'][$option]);
	}
	return $res;
}

$GLOBALS['ls_style'] = array();

function wp_enqueue_style( $handle, $src = false, $deps = array(), $ver = false, $media = 'all' ) {
	if ($src) {
		if ($ver) $src.= (strpos($src, '?') === false ? '?' : '&') .'ver='. $ver;
		$GLOBALS['ls_style'][$handle] = $src;
		\JFactory::getDocument()->addStyleSheet($src);
	} elseif (isset($GLOBALS['ls_style'][$handle])) {
		\JFactory::getDocument()->addStyleSheet($GLOBALS['ls_style'][$handle]);
	}
}

function wp_register_style( $handle, $src, $deps = array(), $ver = false, $in_footer = false ) {
	if ($ver) $src.= (strpos($src, '?') === false ? '?' : '&') .'ver='. $ver;
	$GLOBALS['ls_style'][$handle] = $src;
}

$GLOBALS['ls_unpacked'] = get_option('load_uncompressed', false);
$GLOBALS['ls_script'] = array();

function wp_enqueue_script( $handle, $src = false, $deps = array(), $ver = false, $in_footer = false ) {
	if ($src) {
		if ($GLOBALS['ls_unpacked'] && preg_match('#layerslider/(js|plugins/\w+)/layerslider\.#', $src)) $src = str_replace('.js', '.unpacked.js', $src);
		if ($ver) $src.= (strpos($src, '?') === false ? '?' : '&') .'ver='. $ver;
		$GLOBALS['ls_script'][$handle] = $src;
		\JFactory::getDocument()->addScript($src);
	} elseif (isset($GLOBALS['ls_script'][$handle])) {
		\JFactory::getDocument()->addScript($GLOBALS['ls_script'][$handle]);
	}
}

function wp_register_script( $handle, $src, $deps = array(), $ver = false, $in_footer = false ) {
	if ($GLOBALS['ls_unpacked'] && preg_match('#layerslider/(js|plugins/\w+)/layerslider\.#', $src)) $src = str_replace('.js', '.unpacked.js', $src);
	if ($ver) $src.= (strpos($src, '?') === false ? '?' : '&') .'ver='. $ver;
	$GLOBALS['ls_script'][$handle] = $src;
}

function wp_localize_script($handle, $name, $data) {
	\JFactory::getDocument()->addScriptDeclaration("\nvar $name = ".json_encode($data).";\n");
}

function convert2jurl($path) {
	$path = str_replace('admin.php', 'index.php', $path);
	return preg_replace_callback('/page=([-\w]+)/', __NAMESPACE__.'\convert2jurl_helper', $path);
}
function convert2jurl_helper($match) {
	$page = 'com_'.str_replace('-', '_', $match[1]);
	@list(, $view) = explode(${'_GET'}['option'].'_', $page);
	return 'option=' . ${'_GET'}['option'] . ($view ? '&view='.$view : '');
}

function admin_url( $path = '', $scheme = 'admin' ) {
	return $path;
	// return \JRoute::_(convert2jurl($path));
}


function wp_parse_str( $string, &$array ) {
	parse_str( $string, $array );
	if ( get_magic_quotes_gpc() )
		$array = stripslashes_deep( $array );

	$array = apply_filters( 'wp_parse_str', $array );
}

function urlencode_deep($value) {
	$value = is_array($value) ? array_map(__NAMESPACE__.'\urlencode_deep', $value) : urlencode($value);
	return $value;
}

function add_query_arg() {
	$args = func_get_args();
	if ( is_array( $args[0] ) ) {
		if ( count( $args ) < 2 || false === $args[1] )
			$uri = $_SERVER['REQUEST_URI'];
		else
			$uri = $args[1];
	} else {
		if ( count( $args ) < 3 || false === $args[2] )
			$uri = $_SERVER['REQUEST_URI'];
		else
			$uri = $args[2];
	}

	if ( $frag = strstr( $uri, '#' ) )
		$uri = substr( $uri, 0, -strlen( $frag ) );
	else
		$frag = '';

	if ( 0 === stripos( $uri, 'http://' ) ) {
		$protocol = 'http://';
		$uri = substr( $uri, 7 );
	} elseif ( 0 === stripos( $uri, 'https://' ) ) {
		$protocol = 'https://';
		$uri = substr( $uri, 8 );
	} else {
		$protocol = '';
	}

	if ( strpos( $uri, '?' ) !== false ) {
		list( $base, $query ) = explode( '?', $uri, 2 );
		$base .= '?';
	} elseif ( $protocol || strpos( $uri, '=' ) === false ) {
		$base = $uri . '?';
		$query = '';
	} else {
		$base = '';
		$query = $uri;
	}

	wp_parse_str( $query, $qs );
	$qs = urlencode_deep( $qs ); // this re-URL-encodes things that were already in the query string
	if ( is_array( $args[0] ) ) {
		$kayvees = $args[0];
		$qs = array_merge( $qs, $kayvees );
	} else {
		$qs[ $args[0] ] = $args[1];
	}

	foreach ( $qs as $k => $v ) {
		if ( $v === false )
			unset( $qs[$k] );
	}

	$ret = build_query( $qs );
	$ret = trim( $ret, '?' );
	$ret = preg_replace( '#=(&|$)#', '$1', $ret );
	$ret = $protocol . $base . $ret . $frag;
	$ret = rtrim( $ret, '?' );
	return $ret;
}

function _http_build_query( $data, $prefix = null, $sep = null, $key = '', $urlencode = true ) {
	$ret = array();

	foreach ( (array) $data as $k => $v ) {
		if ( $urlencode)
			$k = urlencode($k);
			if ( is_int($k) && $prefix != null )
			$k = $prefix.$k;
		if ( !empty($key) )
			$k = $key . '%5B' . $k . '%5D';
		if ( $v === null )
			continue;
		elseif ( $v === FALSE )
			$v = '0';

		if ( is_array($v) || is_object($v) )
			array_push($ret,_http_build_query($v, '', $sep, $k, $urlencode));
		elseif ( $urlencode )
			array_push($ret, $k.'='.urlencode($v));
		else
			array_push($ret, $k.'='.$v);
	}

	if ( null === $sep )
		$sep = '&';

	return implode($sep, $ret);
}

function build_query( $data ) {
	return _http_build_query( $data, null, '&', '', false );
}

function wp_redirect($location, $status = 302) {
	\JFactory::getApplication()->redirect(convert2jurl($location));
	return true;
}

$GLOBALS['ls_cache'] = \JCacheStorage::getInstance('file', array(
	'cachebase' => JPATH_SITE.'/'.basename(JPATH_CACHE),
	'lifetime' => 60,
	'language' => '',
	'storage' => 'file',
	'defaultgroup' => 'com_layer_slider',
	'locking' => 1,
	'locktime' => 15,
	'checkTime' => 1,
	'caching' => true
));

function set_transient( $transient, $value, $expiration = 0 ) {
	if (strpos($transient, 'ls-slider-data-') === 0 && isset($GLOBALS['ls-cur-fonts'])) {
		$value['fonts'] = $GLOBALS['ls-cur-fonts'];
	}
	return $GLOBALS['ls_cache']->store($transient, 'com_layer_slider', serialize($value));
}

function get_transient( $transient ) {
	$res = unserialize($GLOBALS['ls_cache']->get($transient, 'com_layer_slider'));

	if (strpos($transient, 'ls-slider-data-') === 0 && isset($res['fonts']) && $res) {
		$GLOBALS['ls-fonts'] = array_merge($GLOBALS['ls-fonts'], $res['fonts']);
	}
	return $res;
}

function delete_transient( $transient ) {
	return $GLOBALS['ls_cache']->remove($transient, 'com_layer_slider');
}

function wp_upload_dir() {
	return array("basedir" => JPATH_SITE."/images", "baseurl" => \JURI::root()."images");
}

/* ADD ACTIONS / FILTERS */

// extend post defaults
function ls_extend_defaults($defaults) {
	$defaults['generatorType'] = array(
		'value' => '',
		'keys' => 'generator_type',
		'props' => array( 'meta' => true )
	);
	$defaults['postPath'] = array(
		'value' => '',
		'keys' => 'post_path',
		'props' => array( 'meta' => true )
	);
	$defaults['postLanguages'] = array(
		'value' => '',
		'keys' => 'post_languages',
		'props' => array( 'meta' => true )
	);
	$defaults['postAuthors'] = array(
		'value' => '',
		'keys' => 'post_authors',
		'props' => array( 'meta' => true )
	);
	$defaults['postManufacturers'] = array(
		'value' => '',
		'keys' => 'post_manufacturers',
		'props' => array( 'meta' => true )
	);
	$defaults['postApps'] = array(
		'value' => '',
		'keys' => 'post_apps',
		'props' => array( 'meta' => true )
	);

	return $defaults;
}
add_filter('layerslider_override_defaults', 'ls_extend_defaults');

function ls_parse_date_override() {
	die('{"errorCount":1,"dateStr":""}');
}
add_action('wp_ajax_ls_parse_date', 'ls_parse_date_override');
