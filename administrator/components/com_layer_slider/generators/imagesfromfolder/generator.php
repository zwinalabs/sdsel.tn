<?php
/*-------------------------------------------------------------------------
# com_layer_slider - Creative Slider
# -------------------------------------------------------------------------
# @ author    John Gera, George Krupa, Janos Biro, Balint Polgarfi
# @ copyright Copyright (C) 2017 Offlajn.com  All Rights Reserved.
# @ license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @ website   http://www.offlajn.com
-------------------------------------------------------------------------*/
?><?php
defined('_JEXEC') or die('Restricted access');

require_once JPATH_ADMINISTRATOR.'/components/com_layer_slider/helpers/generator_abstract.php';

class OfflajnGenerator_ImagesFromFolder extends OfflajnGenerator {

	/*Check if component is installed*/
	static $path= "/components/";

	static $name= "Images from folder";

	var $imgroot = "";

	function __construct($data) {
		parent::__construct($data);
		$this->_variables = array(
			'image-url' => 'Url to the image',
			'image' => 'Url to the image',
			'file-name' => 'Name of the image file'
		);
		$this->_orderbys = array(
			array('id' => 'name', 'name' => 'File name'),
			array('id' => 'creation-date-timestamp', 'name' => 'Date Created'),
			array('id' => 'modification-date-timestamp', 'name' => 'Last Modified'),
			array('id' => 'file-size', 'name' => 'File size'),
			array('id' => 'RAND', 'name' => 'Random')
		);
		$this->imgroot= JPATH_ROOT.DS.'images'.DS;
	}

	function getFilters(){

		$iter = new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator($this->imgroot, RecursiveDirectoryIterator::SKIP_DOTS),
			RecursiveIteratorIterator::SELF_FIRST,
			RecursiveIteratorIterator::CATCH_GET_CHILD // Ignore "Permission denied"
		);

		$paths = array();
		$paths[0]['id'] = "";
		$paths[0]['name'] = str_replace("\\", '/', str_replace(JPATH_ROOT, "" , $this->imgroot));
		$i = 1;
		foreach ($iter as $path => $dir) {
			if ($dir->isDir()) {
				$paths[$i]['id'] = str_replace("\\", '/', str_replace( $this->imgroot , "" , $path));
				$paths[$i]['name'] = str_replace("\\", '/', str_replace(JPATH_ROOT, "" , $path));
				$i++;
			}
		}

		$filters="";
		$filters.=$this->simpleSelect("path", $paths);

		return $filters;
	}

	function getOrderBys(){
		return $this->simpleSelect("orderby",$this->_orderbys);
	}

	function getData() {
		$paths = isset($this->_data['post_path']) ? $this->_data['post_path'] : '';
		$orderbys = array();
		foreach ($this->_orderbys as &$item) {
			$orderbys[ $item['id'] ] = $item['name'];
		}
		$orderby = isset($this->_data['post_orderby']) && isset($orderbys[ $this->_data['post_orderby'] ]) ? $this->_data['post_orderby'] : '';
		$ordering = isset($this->_data['post_order']) ? $this->_data['post_order'] : 'ASC';
		$result = array();
		$ordercol = array();

		if ($handle = opendir($this->imgroot.$paths)) {
			$i = 0;
			while (($entry = readdir($handle)) !== false) {
				if (preg_match('/\.(jpe?g|png|gif)$/i', $entry)) {
					$result[$i]['name'] = $entry;
					$result[$i]['url'] = JRoute::_($this->url."images/".$paths."/".$entry);
					$result[$i]['creation-date-timestamp'] = filectime($this->imgroot.$paths."/".$entry);
					$result[$i]['modification-date-timestamp'] = filemtime($this->imgroot.$paths."/".$entry);
					$result[$i]['file-size'] = filesize($this->imgroot.$paths."/".$entry);

					$result[$i]['url'] = str_replace("\\","/", $result[$i]['url']);
					$result[$i]['image'] = '<img src="'.$result[$i]['url'].'" alt="">';
					list($width, $height, $type, $attr) = @getimagesize($this->imgroot.$paths."/".$entry);
					/*Need to declare for previews*/
					$result[$i]['thumbnail'] = $result[$i]['url'];
					$result[$i]['image-url'] = $result[$i]['thumbnail'];
					$result[$i]['file-name'] = basename($result[$i]['thumbnail']);
					$result[$i]['title'] = $result[$i]['name'];
					$result[$i]['content'] = "Image dimensions: ".$width."x".$height;
					$result[$i]['date-published'] = date("F d Y H:i:s.", $result[$i]['creation-date-timestamp']);
					$result[$i]['author'] = "";
					$result[$i]['image_url'] = $result[$i]['image-url'];

					/*Ordering*/
					if (isset($result[$i][$orderby]))
						$ordercol[$i]['name'] = $result[$i][$orderby];
					$i++;
				}
			}
			closedir($handle);
		}

		if ($orderby !="RAND"){
			if ($ordering=="ASC")
				$sort = SORT_ASC;
			else
				$sort = SORT_DESC;

			if(sizeof($ordercol))
				array_multisort($ordercol, $sort, $result);
		}else{
			$result = $this->shuffle_assoc($result);
		}
		return $result;
	}

	function shuffle_assoc($list) {
		if (!is_array($list)) return $list;

		$keys = array_keys($list);
		shuffle($keys);
		$random = array();
		foreach ($keys as $key)
			$random[] = $list[$key];

		return $random;
	}

}