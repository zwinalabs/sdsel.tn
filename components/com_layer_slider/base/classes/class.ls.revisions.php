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



class LS_Revisions {

	public static $active 	= false;
	public static $enabled 	= true;
	public static $limit 	= 100;
	public static $interval = 10;



	private function __construct() {

	}




	public static function init() {
		if( get_option('layerslider-authorized-site', false) &&
			get_option('ls-revisions-enabled', true) ) {
				self::$active = true;
		}

		$option 		= get_option('ls-revisions-enabled', true);
		self::$enabled 	= ! empty( $option );
		self::$limit 	= get_option('ls-revisions-limit', 100);
		self::$interval = get_option('ls-revisions-interval', 10);
	}






	public static function count( $sliderId ) {

		global $lsdb;
		$sliderId = (int)$sliderId;

		if( empty( $sliderId ) || ! is_numeric($sliderId) ) {
			return false;
		}

		$result = $lsdb->get_col( $lsdb->prepare("
			SELECT COUNT(*) FROM {$lsdb->prefix}layerslider_revisions
			WHERE slider_id = %d
			LIMIT 1
		", $sliderId));


		return (int) $result[0];
	}






	public static function snapshots( $sliderId ) {

		global $lsdb;
		$sliderId = (int)$sliderId;

		if( empty( $sliderId ) || ! is_numeric($sliderId) ) {
			return false;
		}

		return $lsdb->get_results( $lsdb->prepare("
			SELECT * FROM {$lsdb->prefix}layerslider_revisions
			WHERE slider_id = %d
			ORDER BY id ASC
			LIMIT 500
		", $sliderId));
	}






	public static function get( $revisionId ) {

		global $lsdb;
		$revisionId = (int)$revisionId;

		if( empty( $revisionId ) || ! is_numeric($revisionId) ) {
			return false;
		}

		return $lsdb->get_row( $lsdb->prepare("
			SELECT * FROM {$lsdb->prefix}layerslider_revisions
			WHERE id = %d
			ORDER BY id ASC
			LIMIT 1
		", $revisionId));
	}






	public static function last( $sliderId ) {

		global $lsdb;
		$sliderId = (int)$sliderId;

		if( empty( $sliderId ) || ! is_numeric($sliderId) ) {
			return false;
		}

		return $lsdb->get_row( $lsdb->prepare("
			SELECT * FROM {$lsdb->prefix}layerslider_revisions
			WHERE slider_id = %d
			ORDER BY id DESC
			LIMIT 1
		", $sliderId));
	}






	public static function add( $sliderId, $sliderData ) {

		global $lsdb;
		$sliderId = (int)$sliderId;

		if( empty( $sliderId ) || ! is_numeric($sliderId) || empty( $sliderData ) )  {
			return false;
		}

		$lsdb->insert( $lsdb->prefix.'layerslider_revisions',
			array(
				'slider_id' => $sliderId,
				'author' => get_current_user_id(),
				'data' => $sliderData,
				'date_c' => time()
			),
			array(
				'%d',
				'%d',
				'%s',
				'%d'
			)
		);

		return $lsdb->insert_id;
	}






	public static function remove( $revisionId ) {

		global $lsdb;
		$revisionId = (int)$revisionId;

		if( empty( $revisionId ) || ! is_numeric($revisionId) ) {
			return false;
		}

		return $lsdb->delete( $lsdb->prefix.'layerslider_revisions',
			array( 'id' => $revisionId ),
			array( '%d' )
		);
	}





	public static function shift( $sliderId ) {

		global $lsdb;
		$sliderId = (int)$sliderId;

		if( empty( $sliderId ) || ! is_numeric($sliderId) ) {
			return false;
		}

		return $lsdb->query( $lsdb->prepare("
			DELETE FROM {$lsdb->prefix}layerslider_revisions
			WHERE slider_id = %d
			ORDER BY id ASC
			LIMIT 1
		", $sliderId ) );
	}





	public static function clear( $sliderId ) {

		global $lsdb;
		$sliderId = (int)$sliderId;

		if( empty( $sliderId ) || ! is_numeric($sliderId) ) {
			return false;
		}

		return $lsdb->delete( $lsdb->prefix.'layerslider_revisions',
			array( 'slider_id' => $sliderId ),
			array( '%d' )
		);
	}






	public static function truncate( ) {

		global $lsdb;

		return $lsdb->query("TRUNCATE {$lsdb->prefix}layerslider_revisions;");
	}






	public static function revert( $sliderId, $revisionId ) {

		global $lsdb;
		$sliderId = (int)$sliderId;
		$revisionId = (int)$revisionId;

		if( empty( $sliderId ) || ! is_numeric($sliderId) ||
			empty( $revisionId )|| ! is_numeric($revisionId) ) {
			return false;
		}


		$slider 	= LS_Sliders::find( $sliderId );
		$revision 	= self::get( $revisionId );
		$data 		= $revision->data;

		if( $revision &&  $data ) {
			self::add( $sliderId, $data );
			LS_Sliders::update( $sliderId, $slider['name'], json_decode($data, true), $slider['slug']);
		}

		return true;
	}
}

