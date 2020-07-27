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

class OfflajnGenerator_Virtuemart extends OfflajnGenerator {

	/*Check if component is installed*/
	static $path= "/components/com_virtuemart/";

	static $name= "Virtuemart Products";

	function __construct($data) {
		parent::__construct($data);

		if (!class_exists( 'VmConfig' )) require(JPATH_ADMINISTRATOR .'/components/com_virtuemart/helpers/config.php');
		$config= VmConfig::loadConfig();
		/*Manufacturer list*/
		if(!class_exists('TableManufacturers')) require(JPATH_VM_ADMINISTRATOR .'/tables/manufacturers.php');
		if (!class_exists( 'VirtueMartModelManufacturer' )){
			JLoader::import( 'manufacturer', JPATH_ADMINISTRATOR .'/components/com_virtuemart/models' );
		}

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
			array('id' => 'p.created_on', 'name' => 'Date Created'),
			array('id' => 'p.modified_on', 'name' => 'Last Modified'),
			array('id' => 'pr.virtuemart_product_id', 'name' => 'Product ID'),
			array('id' => 'pr.product_name', 'name' => 'Product Title'),
			array('id' => 'p.hits', 'name' => 'Hits'),
			array('id' => 'RAND()', 'name' => 'Random')
		);
	}

	function getFilters(){
		$db = JFactory::getDbo();

		$model = VmModel::getModel('Manufacturer');
		$mans = $model->getManufacturers(true, true,true);

		$i = 0;
		$manufacturers = array();
		foreach ($mans as $manufacturer) {
			$item = array();
			$item["name"] = $manufacturer->mf_name;
			$item["id"] = $manufacturer->virtuemart_manufacturer_id;
			$manufacturers[] = $item;
			$i++;
		}

		/*Categories list*/
		$query = 'SELECT c.virtuemart_category_id AS id, c.category_name AS name, c.category_name AS title
			FROM #__virtuemart_categories_'.VMLANG.' AS c
			LEFT JOIN #__virtuemart_categories as cd
			ON cd.virtuemart_category_id = c.virtuemart_category_id
			WHERE cd.published = 1';
		$db->setQuery( $query );
		$categories = $db->loadAssocList();

		$filters="";
		$filters.=$this->multiSelect("manufacturers",$manufacturers,"manufacturers");
		$filters.=$this->multiSelect("categories",$categories,"categories");

		return $filters;
	}

	function getOrderBys(){
		return $this->simpleSelect("orderby",$this->_orderbys);
	}

	function getData() {
		$db = JFactory::getDbo();

		$manufacturers = $this->getMultiSelectIntData("manufacturers");
		$categories = $this->getMultiSelectIntData("categories");
		$orderby = isset($this->_data['post_orderby']) ? $this->_data['post_orderby'] : '';
		$ordering = isset($this->_data['post_order']) ? $this->_data['post_order'] : 'ASC';

		$query = 'SELECT ';
		$query .= 'pr.virtuemart_product_id AS id, ';
		$query .= 'pr.product_name AS title, ';
		$query .= 'pr.product_s_desc AS shortdesc, ';
		$query .= 'pr.product_desc AS fulldesc, ';
		$query .= 'prcat.virtuemart_category_id AS catid, ';
		$query .= 'cat.category_name AS cat_title, ';
		$query .= 'p.created_by, ';
		$query .= 'p.created_on AS created, ';
		$query .= 'usr.name AS created_by_alias, ';
		$query .= 'p.modified_on AS modified, ';
		$query .= 'p.hits, ';
		$query .= 'p.product_sku AS sku, ';
		$query .= 'usr.name AS created_by_alias, ';
		$query .= '(SELECT m.file_url AS path FROM #__virtuemart_medias AS m
									LEFT JOIN #__virtuemart_product_medias AS me ON m.virtuemart_media_id = me.virtuemart_media_id
									WHERE me.virtuemart_product_id = pr.virtuemart_product_id ORDER BY me.ordering ASC LIMIT 1 ) AS image ';

		$query .= 'FROM #__virtuemart_products_'.VMLANG.' AS pr ';
		$query .= 'LEFT JOIN #__virtuemart_product_categories AS prcat ON prcat.virtuemart_product_id = pr.virtuemart_product_id ';
		$query .= 'LEFT JOIN #__virtuemart_categories_'.VMLANG.' AS cat ON cat.virtuemart_category_id = prcat.virtuemart_category_id ';
		$query .= 'LEFT JOIN #__virtuemart_products AS p ON pr.virtuemart_product_id = p.virtuemart_product_id ';
		$query .= 'LEFT JOIN #__users AS usr ON usr.id = p.created_by ';
		$query .= 'LEFT JOIN #__virtuemart_product_manufacturers AS prm ON prm.virtuemart_product_id = pr.virtuemart_product_id ';


		if (!in_array(0,$categories)){
			$query .= 'WHERE prcat.virtuemart_category_id IN (' . implode(',', $categories) . ') ';
		}else{
			$query .= 'WHERE 1=1 ';
		}

		if (!in_array(0,$manufacturers)){
			$query .= 'AND prm.virtuemart_manufacturer_id IN (' . implode(',', $manufacturers) . ') ';
		}

		$query .= 'AND p.published = 1 ';

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
			$result[$i]['url'] = JRoute::_("index.php?option=com_virtuemart&view=productdetails&virtuemart_category_id=".$result[$i]['catid']."&virtuemart_product_id=".$result[$i]['id']);
			$result[$i]['categorylisturl'] = JRoute::_("index.php?option=com_virtuemart&view=category&virtuemart_category_id=".$result[$i]['catid']);
			/*get image from content*/
			$result[$i]['content_image'] = "";
			$result[$i]['content_image_url'] = "";
			preg_match_all('/<img.*?src=["\'](.*?((jpg)|(png)|(jpeg)|(gif)))["\'].*?\>/i',$result[$i]['shortdesc'].$result[$i]['fulldesc'], $res);
			if (isset($res[1]) && isset($res[1][0])){
				$result[$i]['content_image'] = '<img src="'.$this->url.$res[1][0].'" alt="">';
				$result[$i]['content_image_url'] = $this->url.$res[1][0];
			}

			$result[$i]['introtext'] = strip_tags($result[$i]['shortdesc']);
			$result[$i]['fulltext'] = strip_tags($result[$i]['fulldesc']);
			$result[$i]['price'] = $this->getPrice($result[$i]['id']);

			/*Need to declare for previews*/
			$result[$i]['thumbnail'] = isset($result[$i]['image']) && strlen($result[$i]['image'])>0 ? $this->url.$result[$i]['image'] : (isset($images['image_fulltext']) && strlen($images['image_fulltext'])>0 ? $this->url.$images['image_fulltext'] : (strlen($result[$i]['content_image_url'])>0 ? $result[$i]['content_image_url'] : LS_ROOT_URL . '/static/admin/img/blank.gif'));
			$result[$i]['image-url'] = $result[$i]['thumbnail'];
			$result[$i]['content'] = ($result[$i]['shortdesc']) ? $result[$i]['shortdesc'] : $result[$i]['fulldesc'];
			$result[$i]['date-published'] = $result[$i]['created'];
			$result[$i]['author'] = $result[$i]['created_by_alias'];
			$result[$i]['title'] = $result[$i]['title']." - ".$result[$i]['price'];

			unset($result[$i]['images']);
		}
		return $result;
	}

	function getPrice($pid) {
		if (!class_exists('CurrencyDisplay')) {
			require_once(JPATH_VM_ADMINISTRATOR . '/helpers/currencydisplay.php');
		}
		$product_model = VmModel::getModel('product');
		$currency = CurrencyDisplay::getInstance();
		$product = $product_model->getProduct($pid,TRUE,TRUE,TRUE,1);
		$p = strip_tags(str_replace("PricesalesPrice", "", $currency->createPriceDiv ('salesPrice', '', $product->prices)));
		return $p;
	}
}