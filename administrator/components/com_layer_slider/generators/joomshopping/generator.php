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

class OfflajnGenerator_JoomShopping extends OfflajnGenerator {

	/*Check if component is installed*/
	static $path= "/components/com_jshopping/";

	static $name= "JoomShopping Products";

	function __construct($data) {
		parent::__construct($data);

		require_once (JPATH_SITE.'/components/com_jshopping/lib/factory.php');
		require_once (JPATH_SITE.'/components/com_jshopping/lib/functions.php');

		$this->_variables = array(
			'id' => 'ID of the product',
			'title' => 'Name of the product',
			'url' => 'Url of the product',
			'introtext' => 'Short description of the product',
			'fulltext' => 'Description of the article',
			'catid' => 'Id of the product\'s category',
			'cat_title' => 'Title of the product\'s category',
			'categorylisturl' => 'Url to the product\'s category ',
			'created_by' => 'Id of the product\'s creator',
			'created_by_alias' => 'Name of the product\'s creator',
			'created' => 'Creation date of the product',
			'publish_up' => 'Publication date of the product',
			'modified' => 'Last modification date of the product',
			'hits' => 'Total hits the product',
			'price' => 'Price of the product',
			'sku' => 'SKU of the product',
			'image' => '',
			'content_image' => 'It contains the first image from the article\'s content'
		);
		$this->_orderbys = array(
			array('id' => 'prod.product_date_added', 'name' => 'Date Created'),
			array('id' => 'prod.date_modify', 'name' => 'Last Modified'),
			array('id' => 'prod.product_id', 'name' => 'Product ID'),
			//array('id' => "prod.`name_$this->lang`", 'name' => 'Product Title'),
			array('id' => 'prod.hits', 'name' => 'Hits'),
			array('id' => 'RAND()', 'name' => 'Random')
		);
		$this->lang = JFactory::getLanguage()->getTag();
		$this->lang = "hu-HU"; //only for dev purposes on this server, should remove
	}

	function getFilters(){
		$db = JFactory::getDbo();

		/*Categories list*/
		$query = "SELECT m.category_id AS id, m.`name_$this->lang` AS title, m.`name_$this->lang` AS name
			FROM #__jshopping_categories AS m
			WHERE m.category_publish = 1
			ORDER BY ordering";

		$db->setQuery( $query );
		$categories = $db->loadAssocList();

		$filters="";
		//$filters.=$this->multiSelect("manufacturers",$manufacturers,"manufacturers");
		$filters.=$this->multiSelect("categories",$categories,"categories");
		//$filters.=$this->multiSelect("tags",$tags,"tags");

		return $filters;
	}

	function getOrderBys(){
		return $this->simpleSelect("orderby",$this->_orderbys);
	}

	function getData() {
		$db = JFactory::getDbo();

		//$manufacturers = $this->getMultiSelectIntData("manufacturers");
		$categories = $this->getMultiSelectIntData("categories");
		$orderby = isset($this->_data['post_orderby']) ? $this->_data['post_orderby'] : '';
		$ordering = isset($this->_data['post_order']) ? $this->_data['post_order'] : 'ASC';

		$query = "SELECT DISTINCT ";
		$query .= "prod.product_id AS id, ";
		$query .= "prod.`name_$this->lang` AS title, ";
		$query .= "prod.`short_description_$this->lang` AS shortdesc, ";
		$query .= "prod.`description_$this->lang` AS fulldesc, ";
		$query .= "prod.product_date_added AS created, ";
		$query .= "prod.date_modify AS modified, ";
		$query .= "prod.hits AS hits, ";
		$query .= "prod.product_ean AS sku, ";
		$query .= "pr_cat.category_id AS catid, ";
		$query .= "cat.`name_$this->lang` AS cat_title, ";
		$query .= "prod.product_full_image AS image ";

		$query .= 'FROM #__jshopping_products AS prod ';

		$query .= 'LEFT JOIN #__jshopping_products_to_categories AS pr_cat ON pr_cat.product_id = prod.product_id ';

		$query .= 'LEFT JOIN #__jshopping_categories AS cat ON pr_cat.category_id = cat.category_id ';

		if (!in_array(0,$categories)){
			$query .= 'WHERE pr_cat.category_id IN (' . implode(',', $categories) . ') ';
		}else{
			$query .= 'WHERE 1=1 ';
		}

		$query .= " AND prod.product_publish = '1' AND cat.category_publish='1' GROUP BY prod.product_id ";

		$orderbys = array();
		foreach ($this->_orderbys as &$item) {
			$orderbys[ $item['id'] ] = $item['name'];
		}
		if ($orderby && isset($orderbys[$orderby]))
			$query .= 'ORDER BY ' . $orderby . ' ' . $ordering . ' ';

		$query .= 'LIMIT 0, 30';

		$db->setQuery($query);
		$result = $db->loadAssocList();


		for ($i = 0; $i < count($result); $i++) {
			$result[$i]['url'] = JRoute::_('index.php?option=com_jshopping&controller=product&task=view&product_id='.$result[$i]['id'].'&category_id='.$result[$i]['catid']);
			$result[$i]['categorylisturl'] = JRoute::_('index.php?option=com_jshopping&controller=category&task=view&category_id='.$result[$i]['catid']);
			/*get image from content*/
			$result[$i]['content_image'] = "";
			$result[$i]['content_image_url'] = "";
			$result[$i]['product_image'] = 'components/com_jshopping/files/img_products/'.$result[$i]['image'];
			$result[$i]['introtext'] = strip_tags($result[$i]['shortdesc']);
			$result[$i]['fulltext'] = strip_tags($result[$i]['fulldesc']);
			preg_match_all('/<img.*?src=["\'](.*?((jpg)|(png)|(jpeg)|(gif)))["\'].*?\>/i',$result[$i]['shortdesc'].$result[$i]['fulldesc'], $res);
			if (isset($res[1]) && isset($res[1][0])){
				$result[$i]['content_image'] = '<img src="'.$this->url.$res[1][0].'" alt="">';
				$result[$i]['content_image_url'] = $this->url.$res[1][0];
			}

			$result[$i]['price'] = $this->getPrice($result[$i]['id']);

			/*Need to declare for previews*/
			$result[$i]['thumbnail'] = isset($result[$i]['product_image']) && strlen($result[$i]['product_image'])>0 ? $this->url.$result[$i]['product_image'] : (isset($images['image_fulltext']) && strlen($images['image_fulltext'])>0 ? $this->url.$images['image_fulltext'] : (strlen($result[$i]['content_image_url'])>0 ? $result[$i]['content_image_url'] : LS_ROOT_URL . '/static/admin/img/blank.gif'));
			$result[$i]['image-url'] = $result[$i]['thumbnail'];
			$result[$i]['content'] = ($result[$i]['description']) ? $result[$i]['introtext'] : $result[$i]['fulltext'];
			$result[$i]['date-published'] = $result[$i]['created'];
			$result[$i]['author'] = '';
			$result[$i]['title'] = $result[$i]['title']." - ".$result[$i]['price'];

			unset($result[$i]['images']);
		}
		return $result;
	}

	function getPrice($pid) {
		$product = JTable::getInstance('product', 'jshop');
		$product->load($pid);
		$p = formatprice($product->getPrice(1, 1, 1, 1));

		return $p;
	}

}
