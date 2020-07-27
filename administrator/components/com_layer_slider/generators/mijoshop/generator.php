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

class OfflajnGenerator_MijoShop extends OfflajnGenerator {

	/*Check if component is installed*/
	static $path= "/components/com_mijoshop/";

	static $name= "MijoShop Products";

	function __construct($data) {
		parent::__construct($data);
		if(is_dir(JPATH_ROOT.'/components/com_mijoshop/mijoshop')){
			require_once(JPATH_ROOT.'/components/com_mijoshop/mijoshop/mijoshop.php');
		}
		$this->_variables = array(
			'id' => 'ID of the product',
			'title' => 'Name of the product',
			'url' => 'Url of the product',
			'introtext' => 'Description of the product',
			'catid' => 'Id of the product\'s category',
			'cat_title' => 'Title of the product\'s category',
			//'categoryurl' => 'Url to the product\'s category ',
			'created_by_alias' => 'Name of the product\'s creator',
			'created' => 'Creation date of the product',
			'publish_up' => 'Publication date of the product',
			'modified' => 'Last modification date of the product',
			'hits' => 'Total hits the product',
			'price' => 'Price of the product',
			'sku' => 'SKU of the product',
			'model' => 'model of the product',
			'product_image' => '',
			'content_image' => 'It contains the first image from the article\'s content'
		);
		$this->_orderbys = array(
			array('id' => 'p.date_added', 'name' => 'Date Created'),
			array('id' => 'p.date_modified', 'name' => 'Last Modified'),
			array('id' => 'p.product_id', 'name' => 'Product ID'),
			array('id' => 'pd.name', 'name' => 'Product Title'),
			array('id' => 'p.viewed', 'name' => 'Hits'),
			array('id' => 'RAND()', 'name' => 'Random')
		);
	}

	function getFilters(){
		$db = JFactory::getDbo();

		$this->config = MijoShop::get('opencart')->get('config');
		if (is_object($config)) {
			$lang = ' AND cd.language_id = '.$config->get('config_language_id');
		}

		/*Manufacturers List*/
		$query = "SELECT man.manufacturer_id AS id, name AS name FROM #__mijoshop_manufacturer AS man";
		$db->setQuery( $query );
		$manufacturers = $db->loadAssocList();

		/*Categories list*/
		$query = 'SELECT m.category_id AS id, cd.name AS name, cd.name AS title, m.parent_id AS parent, m.parent_id as parent_id
			FROM #__mijoshop_category m
			LEFT JOIN #__mijoshop_category_description AS cd
			ON cd.category_id = m.category_id
			WHERE m.status = 1 '.$lang.'
			ORDER BY m.sort_order';
		$db->setQuery( $query );
		$categories = $db->loadAssocList();

		/*Tags list*/
		// $query = $db->getQuery(true);
		// $query = "SELECT DISTINCT t.id AS id, t.alias AS alias, t.title AS name  FROM #__tags AS t";
		// $db->setQuery( $query );
		// $tags = $db->loadAssocList();

		/*Author's list who wrote articles*/
		// $query = $db->getQuery(true);
		// $query = "SELECT DISTINCT user.id AS id, user.name AS name
		// FROM #__users AS user
		// LEFT JOIN #__content AS content ON content.created_by = user.id
		// WHERE content.state = '1'";
		// $db->setQuery( $query );
		// $authors = $db->loadAssocList();

		$filters="";
		$filters.=$this->multiSelect("manufacturers",$manufacturers,"manufacturers");
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

		$store_id = JRequest::getInt('mijoshop_store_id', null);
		if (is_null($store_id)) {
		 $store_id = (int) MijoShop::get('opencart')->get('config')->get('config_store_id');
		}

		$language_id = (int) MijoShop::get('opencart')->get('config')->get('config_language_id');

		$query = 'SELECT ';
		$query .= 'p.product_id AS id, ';
		$query .= 'pd.name AS title, ';
		$query .= 'pd.description AS introtext, ';
		$query .= 'p.image AS product_image, ';
		$query .= 'pc.category_id AS catid, ';
		$query .= 'pcd.name AS cat_title, ';
		$query .= 'p.date_added AS created, ';
		$query .= 'p.date_modified AS modified, ';
		$query .= 'p.viewed AS hits, ';
		$query .= 'p.sku AS sku, ';
		$query .= 'p.model AS model ';

		$query .= "FROM #__mijoshop_product AS p ";
		$query .= "INNER JOIN #__mijoshop_product_description AS pd ON p.product_id = pd.product_id ";
		$query .= "LEFT JOIN #__mijoshop_product_to_store AS ps ON p.product_id = ps.product_id ";
		$query .= "LEFT JOIN #__mijoshop_product_to_category AS pc ON p.product_id = pc.product_id ";
		$query .= "LEFT JOIN #__mijoshop_category_description AS pcd ON pc.category_id = pcd.category_id ";
		$query .= "LEFT JOIN #__mijoshop_category_description AS cd ON (pc.category_id = cd.category_id AND cd.language_id = {$language_id}) ";
		$query .= "LEFT JOIN #__mijoshop_category_to_store AS cs ON (pc.category_id = cs.category_id AND cs.store_id = {$store_id}) ";
		//$query .= "LEFT JOIN #__mijoshop_product_tag AS pt ON p.product_id = pt.product_id ";

		if (!in_array(0,$categories)){
			$query .= 'WHERE cd.category_id IN (' . implode(',', $categories) . ') ';
		}else{
			$query .= 'WHERE 1=1 ';
		}

		if (!in_array(0,$manufacturers) && isset($manufacturers)){
			$query .= 'AND p.manufacturer_id IN (' . implode(',', $manufacturers) . ') ';
		}

		$query .= " AND p.status = '1' "
			."AND p.date_available <= NOW() "
			."AND ps.store_id = {$store_id} "
			."AND pd.language_id = '" . $language_id . "' ";

		$orderbys = array();
		foreach ($this->_orderbys as &$item) {
			$orderbys[ $item['id'] ] = $item['name'];
		}
		if ($orderby && isset($orderbys[$orderby]))
			$query .= 'ORDER BY ' . $orderby . ' ' . $ordering . ' ';

		$query .= 'LIMIT 0, 30';

		$db->setQuery($query);
		$result = $db->loadAssocList();

		//print_r($result); exit;
		for ($i = 0; $i < count($result); $i++) {
			$result[$i]['url'] = JRoute::_("index.php?option=com_mijoshop&view=product&product_id=".$result[$i]['id']);
			//$result[$i]['categoryurl'] = JRoute::_("index.php?option=com_virtuemart&view=category&virtuemart_category_id=".$result[$i]['catid']);
			$result[$i]['product_image'] = "/components/com_mijoshop/opencart/image/".$result[$i]['product_image'];
			//echo $result[$i]['product_image']."<br />";
			/*get image from content*/
			$result[$i]['content_image'] = "";
			$result[$i]['content_image_url'] = "";
			preg_match_all('/<img.*?src=["\'](.*?((jpg)|(png)|(jpeg)|(gif)))["\'].*?\>/i',$result[$i]['shortdesc'].$result[$i]['fulldesc'], $res);
			if (isset($res[1]) && isset($res[1][0])){
				$result[$i]['content_image'] = '<img src="'.$this->url.$res[1][0].'" alt="">';
				$result[$i]['content_image_url'] = $this->url.$res[1][0];
			}

			$result[$i]['introtext'] = $result[$i]['fulltext'] = strip_tags($result[$i]['introtext']);
			//$result[$i]['price'] = $this->getPrice($result[$i]['id']);

			/*Need to declare for previews*/
			$result[$i]['thumbnail'] = isset($result[$i]['product_image']) && strlen($result[$i]['product_image'])>0 ? $this->url.$result[$i]['product_image'] : (isset($images['image_fulltext']) && strlen($images['image_fulltext'])>0 ? $this->url.$images['image_fulltext'] : (strlen($result[$i]['content_image_url'])>0 ? $result[$i]['content_image_url'] : LS_ROOT_URL . '/static/admin/img/blank.gif'));
			$result[$i]['image-url'] = $result[$i]['thumbnail'];
			$result[$i]['content'] = ($result[$i]['shortdesc']) ? $result[$i]['shortdesc'] : $result[$i]['fulldesc'];
			$result[$i]['date-published'] = $result[$i]['created'];
			$result[$i]['author'] = "";
			$result[$i]['title'] = $result[$i]['title'];

			unset($result[$i]['images']);
		}
		return $result;
	}

	function getPrice($pid) {
		$row = MijoShop::get('db')->getRecord($pid);
		$p = "";
		if (($this->config->get('config_customer_price') && $this->oc_customer->isLogged()) || !$this->config->get('config_customer_price')) {
			$p = $this->oc_currency->format($this->oc_tax->calculate($row['price'], $row['tax_class_id'], $this->config->get('config_tax')));
		}
		return $p;
	}
}