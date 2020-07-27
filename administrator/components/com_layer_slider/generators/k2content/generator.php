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

class OfflajnGenerator_K2Content extends OfflajnGenerator {

	/*Check if component is installed*/
	static $path= "/components/com_k2/";

	static $name= "K2 Content";

	function __construct($data) {
		parent::__construct($data);
		$this->_variables = array(
			'id' => 'ID of the item',
			'title' => 'Title of the item',
			'url' => 'Url of the item',
			'alias' => 'Alias of the item',
			'introtext' => 'Intro of the item',
			'fulltext' => 'Text of the item',
			'catid' => 'Id of the item\'s category',
			'cat_title' => 'Title of the item\'s category',
			'categorylisturl' => 'Url to the item\'s category',
			'created_by' => 'Id of the item\'s creator',
			'created_by_alias' => 'Name of the article\'s creator',
			'image' => 'Image for the item',
			'image_caption' => 'Image caption for the item',
			'image_credits' => 'Image credits for the item',
			'hits' => 'Hits of the item',
			'content_image' => 'It contains the first image from the article\'s content'
		);
		$this->_orderbys = array(
			array('id' => 'con.created', 'name' => 'Date Created'),
			array('id' => 'con.modified', 'name' => 'Last Modified'),
			array('id' => 'con.id', 'name' => 'ID'),
			array('id' => 'con.title', 'name' => 'Title'),
			array('id' => 'con.hits', 'name' => 'Hits'),
			array('id' => 'RAND()', 'name' => 'Random')
		);
	}

	function getFilters(){
		$db = JFactory::getDbo();

		/*Categories list*/
		$query = 'SELECT c.id AS id, c.name AS name
			FROM #__k2_categories AS c
			WHERE c.published = 1 ';
		$db->setQuery( $query );
		$categories = $db->loadAssocList();

		/*Author's list who wrote articles*/
		$query = $db->getQuery(true);
		$query = "SELECT DISTINCT user.id AS id, user.name AS name
			FROM #__users AS user
			LEFT JOIN #__k2_items AS content
			ON content.created_by = user.id
			WHERE content.trash <> '1'";
		$db->setQuery( $query );
		$authors = $db->loadAssocList();

		$filters="";
		$filters.=$this->multiSelect("categories",$categories,"categories");
		$filters.=$this->multiSelect("authors",$authors,"authors");

		return $filters;
	}

	function getOrderBys(){
		return $this->simpleSelect("orderby",$this->_orderbys);
	}

	function getData() {
		$db = JFactory::getDbo();

		$categories = $this->getMultiSelectIntData("categories");
		$authors = $this->getMultiSelectIntData("authors");
		$orderby = isset($this->_data['post_orderby']) ? $this->_data['post_orderby'] : '';
		$ordering = isset($this->_data['post_order']) ? $this->_data['post_order'] : 'ASC';

		$query = 'SELECT ';
		$query .= 'con.id, ';
		$query .= 'con.title, ';
		$query .= 'con.alias, ';
		$query .= 'con.introtext, ';
		$query .= 'con.fulltext, ';
		$query .= 'con.catid, ';
		$query .= 'cat.name AS cat_title, ';
		$query .= 'cat.alias AS cat_alias, ';
		$query .= 'con.created_by, ';
		$query .= 'usr.name AS created_by_alias, ';
		$query .= 'con.hits, ';
		$query .= 'con.image_caption, ';
		$query .= 'con.image_credits, ';
		$query .= 'con.extra_fields ';

		$query .= 'FROM #__k2_items AS con ';
		$query .= 'LEFT JOIN #__users AS usr ON usr.id = con.created_by ';
		$query .= 'LEFT JOIN #__k2_categories AS cat ON cat.id = con.catid ';

		if (!in_array(0,$categories)){
			$query .= 'WHERE con.catid IN (' . implode(',', $categories) . ') ';
		}else{
			$query .= 'WHERE 1=1 ';
		}

		if (!in_array(0,$authors)){
			$query .= 'AND con.created_by IN (' . implode(',', $authors) . ') ';
		}

		$query .= 'AND con.trash <> 1 ';

		$orderbys = array();
		foreach ($this->_orderbys as &$item) {
			$orderbys[ $item['id'] ] = $item['name'];
		}
		if ($orderby && isset($orderbys[$orderby]))
			$query .= 'ORDER BY ' . $orderby . ' ' . $ordering . ' ';

		$query .= ' LIMIT 0, 30';


		$db->setQuery($query);

		$result = $db->loadAssocList();

		for ($i = 0; $i < count($result); $i++) {
			$result[$i]['url'] = JRoute::_('index.php?option=com_k2&view=item&id=' . $result[$i]['id'] . ':' . $result[$i]['alias']);
			$result[$i]['categorylisturl'] = JRoute::_('index.php?option=com_k2&view=itemlist&task=category&id=' . $result[$i]['catid'] . ':' . $result[$i]['cat_alias']);

			$result[$i]['image'] = "";
			if (JFile::exists(JPATH_SITE.'/media/k2/items/cache/'.md5("Image".$result[$i]['id']).'_XL.jpg')) {
				$result[$i]['image'] = $this->url.'media/k2/items/cache/'.md5("Image".$result[$i]['id']).'_XL.jpg';
			} else if(JFile::exists(JPATH_SITE.'/media/k2/items/cache/'.md5("Image".$result[$i]['id']).'_XL.png')) {
				$result[$i]['image'] = $this->url.'media/k2/items/cache/'.md5("Image".$result[$i]['id']).'_XL.png';
			}

			//$result[$i]['image'] = "media/k2/items/cache/" . md5("Image" . $result[$i]['id']) . "_XL.jpg";

			/*get image from content*/
			$result[$i]['content_image'] = "";
			$result[$i]['content_image_url'] = "";
			preg_match_all('/<img.*?src=["\'](.*?((jpg)|(png)|(jpeg)|(gif)))["\'].*?\>/i',$result[$i]['introtext'], $res);
			if (isset($res[1]) && isset($res[1][0])){
				$result[$i]['content_image'] = '<img src="'.$this->url.$res[1][0].'" alt="">';
				$result[$i]['content_image_url'] = $this->url.$res[1][0];
			}

			$result[$i]['introtext'] = strip_tags($result[$i]['introtext']);
			$result[$i]['fulltext'] = strip_tags($result[$i]['fulltext']);

			/*Need to declare for previews*/
			$result[$i]['thumbnail'] = isset($result[$i]['image']) && strlen($result[$i]['image'])>0 ? $result[$i]['image'] : (strlen($result[$i]['content_image_url'])>0 ? $result[$i]['content_image_url'] : LS_ROOT_URL . '/static/admin/img/blank.gif');
			$result[$i]['image-url'] = $result[$i]['thumbnail'];
			$result[$i]['content'] = $result[$i]['introtext'];
			// $result[$i]['date-published'] = $result[$i]['publish_up'];
			$result[$i]['author'] = $result[$i]['created_by'];

			unset($result[$i]['images']);
		}

		return $result;
	}
}