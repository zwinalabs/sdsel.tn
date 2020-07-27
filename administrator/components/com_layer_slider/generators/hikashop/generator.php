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

class OfflajnGenerator_HikaShop extends OfflajnGenerator {

	/*Check if component is installed*/
	static $path = "/components/com_hikashop/";

	static $name= "HikaShop Products";

	function __construct($data) {
		parent::__construct($data);

		if (!file_exists(JPATH_ADMINISTRATOR.'/components/com_hikashop/helpers/helper.php')
		||  !file_exists(JPATH_ADMINISTRATOR.'/components/com_hikashop/classes/currency.php')) {
			$this->notInstalled = true;
			return;
		}
		require_once (JPATH_ADMINISTRATOR.'/components/com_hikashop/helpers/helper.php');
		require_once (JPATH_ADMINISTRATOR.'/components/com_hikashop/classes/currency.php');

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
			array('id' => 'a.product_created', 'name' => 'Date Created'),
			array('id' => 'a.product_modified', 'name' => 'Last Modified'),
			array('id' => 'a.product_id', 'name' => 'Product ID'),
			array('id' => 'a.product_name', 'name' => 'Product Title'),
			array('id' => 'a.product_hits', 'name' => 'Hits'),
			array('id' => 'RAND()', 'name' => 'Random')
		);
	}

	function getFilters(){
		if (isset($this->notInstalled)) return '';
		$db = JFactory::getDbo();

		/*Categories list*/
		$query = 'SELECT m.category_id AS id, m.category_name AS name, m.category_name as title
			FROM #__hikashop_category m
			WHERE (m.category_published =1 AND m.category_type="product" AND m.category_id <> 2)';

		$db->setQuery( $query );
		$categories = $db->loadAssocList();

		$filters="";
		//$filters.=$this->multiSelect("manufacturers",$manufacturers,"manufacturers");
		$filters.=$this->multiSelect("categories",$categories,"categories");
		//$filters.=$this->multiSelect("tags",$tags,"tags");

		return $filters;
	}

	function getOrderBys(){
		if (isset($this->notInstalled)) return '';
		return $this->simpleSelect("orderby",$this->_orderbys);
	}

	function getData() {
		if (isset($this->notInstalled)) return array();
		$db = JFactory::getDbo();

		//$manufacturers = $this->getMultiSelectIntData("manufacturers");
		$categories = $this->getMultiSelectIntData("categories");
		$orderby = isset($this->_data['post_orderby']) ? $this->_data['post_orderby'] : '';
		$ordering = isset($this->_data['post_order']) ? $this->_data['post_order'] : 'ASC';

		$query = 'SELECT DISTINCT ';
		$query .= 'a.product_id AS id, ';
		$query .= 'a.product_name AS title, ';
		$query .= 'a.product_alias AS alias, ';
		$query .= 'a.product_description AS description, ';
		$query .= 'a.product_created AS created, ';
		$query .= 'a.product_modified AS modified, ';
		$query .= 'a.product_hit AS hits, ';
		$query .= 'a.product_code AS sku, ';
		$query .= 'b.category_id AS catid, ';
		$query .= 'c.category_name AS cat_title, ';
		$query .= 'f.file_path AS image, ';
		$query .= 'a.product_tax_id AS tax ';

		$query .= 'FROM #__hikashop_product AS a ';

		$query .= 'LEFT JOIN #__hikashop_product_category AS b ON a.product_id = b.product_id ';

		$query .= 'LEFT JOIN #__hikashop_category AS c ON b.category_id = c.category_id ';

		$query .= 'LEFT JOIN #__hikashop_file AS f ON f.file_ref_id = a.product_id ';

		if (!in_array(0,$categories)){
			$query .= 'WHERE b.category_id IN (' . implode(',', $categories) . ') ';
		}else{
			$query .= 'WHERE 1=1 ';
		}

		$query .= 'AND a.product_published = 1 AND f.file_type="product" GROUP BY a.product_id ';

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
			$result[$i]['url'] = JRoute::_('index.php?option=com_hikashop&ctrl=product&task=show&cid='.$result[$i]['id'].'&name='.$result[$i]["alias"]);
			$result[$i]['categorylisturl'] = JRoute::_('index.php?option=com_hikashop&ctrl=category&task=show&cid='.$result[$i]['catid']);
			/*get image from content*/
			$result[$i]['content_image'] = "";
			$result[$i]['content_image_url'] = "";
			$result[$i]['product_image'] = '/media/com_hikashop/upload/'.$result[$i]['image'];
			$result[$i]['created'] = date("Y-m-d H:i:s", $result[$i]['created']);
			$result[$i]['modified'] = date("Y-m-d H:i:s", $result[$i]['modified']);
			preg_match_all('/<img.*?src=["\'](.*?((jpg)|(png)|(jpeg)|(gif)))["\'].*?\>/i',$result[$i]['description'], $res);
			if (isset($res[1]) && isset($res[1][0])){
				$result[$i]['content_image'] = '<img src="'.$this->url.$res[1][0].'" alt="">';
				$result[$i]['content_image_url'] = $this->url.$res[1][0];
			}

			$result[$i]['introtext'] = $result[$i]['fulltext'] = strip_tags($result[$i]['description']);
			$result[$i]['price'] = $this->getPrice($result[$i]['id'], $result[$i]['tax']);

			/*Need to declare for previews*/
			$result[$i]['thumbnail'] = isset($result[$i]['product_image']) && strlen($result[$i]['product_image'])>0 ? $this->url.$result[$i]['product_image'] : (isset($images['image_fulltext']) && strlen($images['image_fulltext'])>0 ? $this->url.$images['image_fulltext'] : (strlen($result[$i]['content_image_url'])>0 ? $result[$i]['content_image_url'] : LS_ROOT_URL . '/static/admin/img/blank.gif'));
			$result[$i]['image-url'] = $result[$i]['thumbnail'];
			$result[$i]['content'] = ($result[$i]['description']) ? $result[$i]['description'] : '';
			$result[$i]['date-published'] = $result[$i]['created'];
			$result[$i]['author'] = '';
			$result[$i]['title'] = $result[$i]['title']." - ".$result[$i]['price'];

			unset($result[$i]['images']);
		}
		return $result;
	}

	function getPrice($pid, $tax) {
		$arr = array();
		$arr[0] = new stdClass();
		$arr[0]->product_id = $pid;
		$arr[0]->product_tax_id = $tax;
		$currency = hikashop_get('type.currency');
		$currencyClass = hikashop_get('class.currency');
		$zone = hikashop_getZone();
		$cur = hikashop_getCurrency();
		$currencyClass->getListingPrices($arr,$zone,$cur);
		$price = "";
		/*if(!$this->_params->get('pricetype')) {
			$price = $arr[0]->prices[0]->price_value;
		} else {  */
			@$price = $arr[0]->prices[0]->price_value_with_tax;
		// }
		return $currencyClass->format($price, $cur);
	}
}
