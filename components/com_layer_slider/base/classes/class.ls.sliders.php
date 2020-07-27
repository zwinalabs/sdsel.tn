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

class LS_Sliders {


	public static $results = array();




	public static $count = null;




	private function __construct() {

	}




	public static function count() {
		return self::$count;
	}




	public static function find($args = array()) {

		// Find by slider ID
		if(is_numeric($args) && intval($args) == $args) {
			return self::_getById( (int) $args );

		// Random slider
		} elseif($args === 'random') {
			return self::_getRandom();

		// Find by slider slug
		} elseif(is_string($args)) {
			return self::_getBySlug($args);

		// Find by list of slider IDs
		} elseif(is_array($args) && isset($args[0]) && is_numeric($args[0])) {
			return self::_getByIds($args);

		// Find by query
		} else {

			// Defaults
			$defaults = array(
				'columns' => '*',
				'where' => '',
				'exclude' => array('removed'),
				'orderby' => 'date_c',
				'order' => 'DESC',
				'limit' => 10,
				'page' => 1,
				'data' => true
			);

			// Merge user data with defaults
			foreach($defaults as $key => $val) {
				if(!isset($args[$key])) { $args[$key] = $val; }
			}

			// Escape user data
			foreach($args as $key => $val) {
				if( $key !== 'where' ) {
					$args[$key] = esc_sql($val);
				}
			}

			// Due to the nature of dynamically built queries we can't
			// use prepared statements or $lsdb::prepare(). By keeping
			// this function backwards compatible, we have even less
			// options to completely eliminate potential issues caused
			// by unhandled data.

			// In addition of using esc_sql(), we're performing some
			// further tests trying to filter out user data that might
			// not be handled properly prior to this function call.
			$columns = array('id', 'author', 'name', 'slug', 'data', 'date_c', 'date_m', 'flag_hidden', 'flag_deleted', 'schedule_start', 'schedule_end');

			$args['orderby'] 	= in_array($args['orderby'], $columns) ? $args['orderby'] : 'date_c';
			$args['order'] 		= ($args['order'] === 'DESC') ? 'DESC' : 'ASC';
			$args['limit'] 		= (int)  $args['limit'];
			$args['page'] 		= (int)  $args['page'];
			$args['data'] 		= (bool) $args['data'];




			// Exclude
			if(!empty($args['exclude'])) {
				if(in_array('hidden', $args['exclude'])) {
					$exclude[] = "flag_hidden = '0'"; }

				if(in_array('removed', $args['exclude'])) {
					$exclude[] = "flag_deleted = '0'"; }

				$args['exclude'] = implode(' AND ', $exclude);
			}

			// Where
			$where = '';
			if(!empty($args['where']) && !empty($args['exclude'])) {
				$where = "WHERE ({$args['exclude']}) AND ({$args['where']}) ";

			} elseif(!empty($args['where'])) {
				$where = "WHERE {$args['where']} ";

			} elseif(!empty($args['exclude'])) {
				$where = "WHERE {$args['exclude']} ";
			}

			// Some adjustments
			$args['limit'] = ($args['limit'] * $args['page'] - $args['limit']).', '.$args['limit'];

			// Build the query
			global $lsdb;
			$table = $lsdb->prefix.LS_DB_TABLE;
			$sliders = $lsdb->get_results("
				SELECT SQL_CALC_FOUND_ROWS {$args['columns']}
				FROM $table
				$where
				ORDER BY `{$args['orderby']}` {$args['order']}
				LIMIT {$args['limit']}
			", ARRAY_A);

			// Set counter
			$found = $lsdb->get_col("SELECT FOUND_ROWS()");
			self::$count = (int) $found[0];

			// Return original value on error
			if(!is_array($sliders)) { return $sliders; };

			// Parse slider data
			if($args['data']) {
				foreach($sliders as $key => $val) {
					$sliders[$key]['data'] = json_decode($val['data'], true);
				}
			}

			// Return sliders
			return $sliders;
		}
	}




	public static function add($title = 'Unnamed', $data = array(), $slug = '') {

		global $lsdb;

		// Slider data
		$data = !empty($data) ? $data : array(
			'properties' => array(
				'createdWith' => LS_PLUGIN_VERSION,
				'sliderVersion' => LS_PLUGIN_VERSION,
				'title' => $title,
				'new' => true,
			),
			'layers' => array(array()),
		);

		// Fix WP 4.2 issue with longer varchars
		// than the column length
		if(strlen($title) > 99) {
			$title = substr($title, 0, (99-strlen($title)) );
		}

		// Popup?
		$popup = 0;
		if( ! empty($data['properties']['type']) && $data['properties']['type'] == 'popup') {
			$popup = 1;
		}

		// Insert slider, WPDB will escape data automatically
		$lsdb->insert($lsdb->prefix.LS_DB_TABLE, array(
			'author' => get_current_user_id(),
			'name' => $title,
			'slug' => $slug,
			'data' => json_encode($data),
			'date_c' => time(),
			'date_m' => time(),
			'flag_popup' => $popup
		), array(
			'%d', '%s', '%s', '%s', '%d', '%d', '%d'
		));

		// Return insert database ID
		return $lsdb->insert_id;
	}




	public static function update($id = 0, $title = 'Unnamed', $data = array(), $slug = '') {

		global $lsdb;

		// Slider data
		$data = !empty($data) ? $data : array(
			'properties' => array('title' => $title),
			'layers' => array(array()),
		);

		// Fix WP 4.2 issue with longer varchars
		// than the column length
		if(strlen($title) > 99) {
			$title = substr($title, 0, (99-strlen($title)) );
		}

		// Status
		$status = 0;
		if( empty($data['properties']['status']) || $data['properties']['status'] === 'false') {
			$status = 1;
		}

		// Schedule
		$schedule = array('schedule_start' => 0, 'schedule_end' => 0);
		foreach($schedule as $key => $val) {
			if( ! empty($data['properties'][$key]) ) {
				if( is_numeric($data['properties'][$key]) ) {
					$schedule[$key] = (int) $data['properties'][$key];
				} else {
					$tz = date_default_timezone_get();
					date_default_timezone_set( get_option('timezone_string') );
					$schedule[$key] = (int) strtotime($data['properties'][$key]);
					date_default_timezone_set( $tz );
				}
			}
		}

		// Popup?
		$popup = 0;
		if( ! empty($data['properties']['type']) && $data['properties']['type'] == 'popup') {
			$popup = 1;
		}

		// Insert slider, WPDB will escape data automatically
		$lsdb->update($lsdb->prefix.LS_DB_TABLE, array(
				'name' => $title,
				'slug' => $slug,
				'data' => json_encode($data),
				'schedule_start' => $schedule['schedule_start'],
				'schedule_end' => $schedule['schedule_end'],
				'date_m' => time(),
				'flag_hidden' => $status,
				'flag_popup' => $popup
			),
			array('id' => $id),
			array('%s', '%s', '%s', '%d', '%d', '%d', '%d', '%d')
		);

		// Return insert database ID
		return true;
	}



	public static function remove($id = null) {

		// Check ID
		if(!is_int($id)) { return false; }

		// Remove
		global $lsdb;
		$lsdb->update($lsdb->prefix.LS_DB_TABLE,
			array('flag_deleted' => 1),
			array('id' => $id),
			'%d', '%d'
		);

		return true;
	}



	public static function delete($id = null) {

		// Check ID
		if(!is_int($id)) { return false; }

		// Delete
		global $lsdb;
		$lsdb->delete($lsdb->prefix.LS_DB_TABLE, array('id' => $id), '%d');

		return true;
	}




	public static function restore($id = null) {

		// Check ID
		if(!is_int($id)) { return false; }

		// Remove
		global $lsdb;
		$lsdb->update($lsdb->prefix.LS_DB_TABLE,
			array('flag_deleted' => 0),
			array('id' => $id),
			'%d', '%d'
		);

		return true;
	}




	private static function _getById($id = null) {

		// Check ID
		if(!is_int($id)) { return false; }

		// Get Sliders
		global $lsdb;
		$table = $lsdb->prefix.LS_DB_TABLE;
		$result = $lsdb->get_row("SELECT * FROM $table WHERE id = '$id' ORDER BY id DESC LIMIT 1", ARRAY_A);

		// Check return value
		if(!is_array($result)) { return false; }

		// Return result
		$result['data'] = json_decode($result['data'], true);
		return $result;
	}



	private static function _getByIds($ids = null) {

		// Check ID
		if(!is_array($ids)) { return false; }

		// DB stuff
		global $lsdb;
		$table = $lsdb->prefix.LS_DB_TABLE;
		$limit = count($ids);

		// Collect IDs
		if(is_array($ids) && !empty($ids)) {
			$tmp = array();
			foreach($ids as $id) {
				$tmp[] = 'id = \''.intval($id).'\'';
			}
			$ids = implode(' OR ', $tmp);
			unset($tmp);
		}

		// Make the call
		$result = $lsdb->get_results("SELECT * FROM $table WHERE $ids ORDER BY id DESC LIMIT $limit", ARRAY_A);

		// Decode slider data
		if(is_array($result) && !empty($result)) {
			foreach($result as $key => $slider) {
				$result[$key]['data'] = json_decode($slider['data'], true);
			}

			return $result;

		// Failed query
		} else {
			return false;
		}
	}





	private static function _getBySlug($slug) {

		// Check slug
		if(empty($slug)) { return false; }
			else { $slug = esc_sql($slug); }

		// Get DB stuff
		global $lsdb;
		$table = $lsdb->prefix.LS_DB_TABLE;

		// Make the call
		$result = $lsdb->get_row("SELECT * FROM $table WHERE slug = '$slug' ORDER BY id DESC LIMIT 1", ARRAY_A);

		// Check return value
		if(!is_array($result)) { return false; }

		// Return result
		$result['data'] = json_decode($result['data'], true);
		return $result;
	}



	private static function _getRandom() {

		// Get DB stuff
		global $lsdb;
		$table = $lsdb->prefix.LS_DB_TABLE;

		// Make the call
		$result = $lsdb->get_row("SELECT * FROM $table WHERE flag_hidden = '0' AND flag_deleted = '0' ORDER BY RAND() LIMIT 1", ARRAY_A);

		// Check return value
		if(!is_array($result)) { return false; }

		// Return result
		$result['data'] = json_decode($result['data'], true);
		return $result;
	}
}
