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

class LS_Sources {

	// handle => path
	public static $skins = array();
	public static $sliders = array();
	public static $transitions = array();

	private function __construct() {

	}

	
	public static function addSkins($path) {

		$skinsPath = $skins = array();
		$path = rtrim($path, '/\\');

		// It's a direct skin folder
		if(file_exists($path.'/skin.css')) {
			$skinsPath = array($path);

		}  else { // Get all children if it's a parent directory
			$skinsPath = glob($path.'/*', GLOB_ONLYDIR);
		}

		// Iterate over the skins
		foreach($skinsPath as $key => $path) {

			// Exclude non-valid skins
			if( !file_exists($path.'/skin.css') ) { continue; }

			// Gather skin data
			$handle = strtolower(basename($path));
			$skins[$handle] = array(
				'name' => $handle,
				'handle' => $handle,
				'dir' => $path,
				'file' => $path.DIRECTORY_SEPARATOR.'skin.css'
			);

			// Get skin info (if any)
			if(file_exists($path.'/info.json')) {
				$skins[$handle]['info'] = json_decode(file_get_contents($path.'/info.json'), true);
				$skins[$handle]['name'] = $skins[$handle]['info']['name'];

				if( ! empty( $skins[$handle]['info']['requires'] ) ) {
					$skins[$handle]['requires'] = $skins[$handle]['info']['requires'];
				}
			}
		}

		self::$skins = array_merge(self::$skins, $skins);
		ksort( self::$skins );
	}



	
	public static function removeSkin($handle) {
		unset( self::$skins[ strtolower($handle) ] );
	}



	
	public static function getSkin($handle) {
		return self::$skins[ strtolower($handle) ];
	}



	
	public static function getSkins() {
		return self::$skins;
	}



	
	public static function pathForSkin($handle) {
		return self::$skins[ strtolower($handle) ]['dir'] . DIRECTORY_SEPARATOR;
	}



	
	public static function urlForSkin($handle) {
		$path = self::$skins[ strtolower($handle) ]['dir'];
		$url = content_url() . str_replace(realpath(WP_CONTENT_DIR), '', realpath($path)).'/';
		return str_replace('\\', '/', $url);
	}




	// ---------------------------------------------




	
	public static function addDemoSlider( $path ) {

		$slidersPath = $sliders = array();
		$path = rtrim($path, '/\\');

		// It's a direct slider folder
		if(file_exists($path.'/slider.zip')) {
			$slidersPath = array($path);

		}  else { // Get all children if it's a parent directory
			$slidersPath = glob($path.'/*', GLOB_ONLYDIR);
		}

		// Iterate over the sliders
		if( ! empty( $slidersPath ) ) {
			foreach($slidersPath as $key => $path) {

				// Exclude non-valid demo sliders
				if( !file_exists($path.'/slider.zip') ) { continue; }

				// Gather slider data
				$handle = strtolower(basename($path));
				$sliders[$handle] = array(
					'name' => $handle,
					'handle' => $handle,
					'dir' => $path,
					'file' => $path.DIRECTORY_SEPARATOR.'slider.zip',
					'bundled' => true,
				);

				// Get skin info (if any)
				if(file_exists($path.'/info.json')) {
					$sliders[$handle]['info'] = json_decode(file_get_contents($path.'/info.json'), true);
					$sliders[$handle]['name'] = $sliders[$handle]['info']['name'];

					$sliders[$handle]['groups'] = 'free,bundled,';
					if( ! empty( $sliders[$handle]['info']['groups'] ) ) {
						$sliders[$handle]['groups'] .= $sliders[$handle]['info']['groups'];
					}

					$sliders[$handle]['url'] = '#';
					if( ! empty($sliders[$handle]['info']['url']) ) {
						$sliders[$handle]['url'] = $sliders[$handle]['info']['url'];
					}

					if( ! empty( $sliders[$handle]['info']['requires'] ) ) {
						$sliders[$handle]['requires'] = $sliders[$handle]['info']['requires'];
					}
				}

				// Get preview (if any)
				if(file_exists($path.'/preview.png')) {
					$url = content_url() . str_replace(realpath(WP_CONTENT_DIR), '', $path).'/preview.png';
					$sliders[$handle]['preview'] = str_replace('\\', '/', $url);
				}
			}
		}

		if( ! empty( $sliders ) ) {
			self::$sliders = array_merge(self::$sliders, $sliders);
			ksort( self::$sliders );
		}
	}


	
	public static function removeDemoSlider( $handle ) {
		unset( self::$sliders[ strtolower($handle) ] );
	}



	
	public static function getDemoSlider( $handle ) {
		return self::$sliders[ strtolower($handle) ];
	}



	
	public static function getDemoSliders() {
		return self::$sliders;
	}


	
	public static function pathForDemoSlider( $handle ) {
		return self::$sliders[ strtolower($handle) ]['dir'] . DIRECTORY_SEPARATOR;
	}



	// ---------------------------------------------
}

?>
