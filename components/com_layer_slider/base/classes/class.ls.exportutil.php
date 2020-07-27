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



class LS_ExportUtil {


	private $zip;


	private $file;



	private $imageList;



	public function __construct() {

		// Check for ZipArchieve
		if(class_exists('ZipArchive')) {

			// Temporary directory for file operations
			$upload_dir = wp_upload_dir();
			$tmp_dir = $upload_dir['basedir'];

			// Prepare ZIP to work with
			$this->file = tempnam($tmp_dir, "zip");
			$this->zip = new ZipArchive;
			$this->zip->open($this->file, ZIPARCHIVE::CREATE | ZIPARCHIVE::OVERWRITE);
		}
	}



	public function addSettings($data, $folder = '') {
		$folder = !empty($folder) ? $folder.'/' : '';
		$this->zip->addFromString($folder.'settings.json', $data);
	}



	public function addImage($files, $folder = '') {

		// Check file
		if(empty($files)) { return false; }

		// Check file type
		if(!is_array($files)) { $files = array($files); }

		// Check folder
		$folder = is_string($folder) ? $folder.'/uploads/' : 'uploads/';

		// Add contents to ZIP
		foreach($files as $file) {
			if(!empty($file) && is_string($file)) {
				$this->zip->addFile($file,
					$folder.sanitize_file_name(basename($file))
				);
			}
		}
	}



	public function download() {

		// Close ZIP operations
		$this->zip->close();

		// Set headers and to user
		header('Content-Type: application/zip');
		header('Content-Disposition: attachment; filename="CreativeSlider_Export_'.date('Y-m-d').'_at_'.date('H.i.s').'.zip"');
		header("Content-length: " . filesize($this->file));
		header('Pragma: no-cache');
		header('Expires: 0');
		readfile($this->file);

		// Remove temporary file
		unlink($this->file);
		die();
	}


	public function getImagesForSlider($data) {

		// Array to hold image URLs
		$this->imageList = array();

		// Slider Preview
		if( ! empty($data['meta'] ) ) {
			$this->_addImageToList( $data['meta'], 'previewId', 'preview' );
		}

		$this->_addImageToList( $data['properties'], 'backgroundimageId', 'backgroundimage' );
		$this->_addImageToList( $data['properties'], 'yourlogoId', 'yourlogo' );


		// Slides
		if(!empty($data['layers']) && is_array($data['layers'])) {
		foreach($data['layers'] as $slide) {

				$this->_addImageToList( $slide['properties'], 'backgroundId', 'background' );
				$this->_addImageToList( $slide['properties'], 'thumbnailId', 'thumbnail' );


				// Layers
				if(!empty($slide['sublayers']) && is_array($slide['sublayers'])) {
					foreach($slide['sublayers'] as $layer) {

						$this->_addImageToList( $layer, 'imageId', 'image' );
						$this->_addImageToList( $layer, 'posterId', 'poster' );
					}
				}
			}
		}

		return $this->imageList;
	}



	public function fontsForSlider( $data ) {

		$ret = array();
		$usedFonts = array();
		$googleFonts = get_option('ls-google-fonts', array());

		if( !empty($data['layers']) && is_array($data['layers'])) {
			foreach($data['layers'] as $slide) {

				if( !empty($slide['sublayers']) && is_array($data['layers'])) {
					foreach($slide['sublayers'] as $layer) {

						if( !empty($layer['styles']) ) {

							// Ensure that magic quotes will not mess with JSON data
							if(function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc()) {
								$layer['styles'] = stripslashes($layer['styles']);
							}

							$styles = !empty($layer['styles']) ? json_decode(stripslashes($layer['styles']), true) : new stdClass;

							if( !empty($styles['font-family']) ) {
								$families = explode(',', $styles['font-family']);
								foreach( $families as $family ) {
									$family = trim( $family, " \"'\t\n\r\0\x0B");

									if( !empty($family) ) {
										$usedFonts[] = strtolower($family);
									}
								}
							}
						}
					}
				}
			}
		}

		foreach( $googleFonts as $font ) {
			list($family, $weights) = explode(':', $font['param']);
			$family = strtolower( str_replace('+', ' ', $family) );

			if( array_search($family, $usedFonts) !== false) {
				$font['admin'] = false;
				$ret[] = $font;
			}
		}

		return $ret;
	}


	public function getFSPaths($urls) {

		if(!empty($urls) && is_array($urls)) {

			$paths 		= array();
			$upload 	= wp_upload_dir();
			$uploadDir 	= basename($upload['basedir']);


			foreach($urls as $url) {

				// Get URL relative to the uploads folder
				$urlPath = parse_url($url, PHP_URL_PATH);
				$urlPath = explode("/$uploadDir/", $urlPath);

				if( empty($urlPath[1]) ) {
					continue;
				}

				$urlPath = $urlPath[1];

				// Get file path
				$filePath = $upload['basedir'] .'/'. $urlPath;
				$filePath = realpath($filePath);

				// Add to array
				if( file_exists($filePath) && is_file($filePath) ) {
					$paths[] = $filePath;
				}
			}

			return $paths;
		}

		return array();
	}

	protected function _addImageToList( $data, $idKey = '', $urlKey = '' ) {

		if( ! empty( $data[ $urlKey ] ) ) {
			$src = $data[ $urlKey ];
		}

		if( ! empty( $data[ $idKey ] ) ) {
			if( $result = wp_get_attachment_image_url( $data[ $idKey ], 'full' ) ) {
				$src = $result;
			}
		}

		if( ! empty( $src) ) {
			$this->imageList[] = $src;
		}
	}
}