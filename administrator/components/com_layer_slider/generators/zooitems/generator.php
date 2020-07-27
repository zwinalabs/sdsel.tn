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

class OfflajnGenerator_ZooItems extends OfflajnGenerator {

  /*Check if component is installed*/
  static $path= "/components/com_zoo";

  static $name= "Zoo Items";

  function __construct($data) {
    parent::__construct($data);
    $this->_variables = array(
      'id' => 'ID of the item',
      'title' => 'Title of the item',
      'url' => 'Url of the item',
      'text' => 'Text of the item',
      'catid' => 'Id of the article\'s category',
      'cat_title' => 'Title of the article\'s category',
      'categorylisturl' => 'Url to the article\'s category with list layout',
      'categoryblogurl' => 'Url to the article\'s category with blog layout',
      'created_by' => 'Id of the article\'s creator',
      'created_by_alias' => 'Name of the article\'s creator',
      'created' => 'Creation date of the article',
      'publish_up' => 'Publication date of the article',
      'modified' => 'Last modification date of the article',
      'hits' => 'Total hits the article',
      'image' => 'Image',
      'content_image' => 'It contains the first image from the article\'s content'
    );
    $this->_orderbys = array(
      array('id' => 'a.created', 'name' => 'Date Created'),
      array('id' => 'a.modified', 'name' => 'Last Modified'),
      array('id' => 'a.id', 'name' => 'ID'),
      array('id' => 'a.name', 'name' => 'Title'),
      array('id' => 'a.hits', 'name' => 'Hits'),
      array('id' => 'RAND()', 'name' => 'Random')
    );
  }

  function getFilters(){
    $db = JFactory::getDbo();

    require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_zoo'.DIRECTORY_SEPARATOR.'config.php');

    /*Apps list*/
    $zoo = App::getInstance('zoo');
		$table = $zoo->table->application;
    $this->apps = $table->all(array('order' => 'name'));
    $apps = array();
    $i = 0;
    foreach($this->apps as $app) {
      @$apps[$i]["title"] = $app->name;
      @$apps[$i]["name"] = $app->name;
      @$apps[$i]["id"] = $app->id;
      $i++;
    }

    /*Categories list*/
    $query = 'SELECT c.id AS id, c.name AS title, c.alias AS name
      FROM #__zoo_category AS c
      WHERE c.published = 1';
    $db->setQuery( $query );
    $categories = $db->loadAssocList();

    $filters="";
    $filters.=$this->multiSelect("apps",$apps,"Apps");
    $filters.=$this->multiSelect("categories",$categories,"categories");

    return $filters;
  }

  function getOrderBys(){
    return $this->simpleSelect("orderby",$this->_orderbys);
  }

  function getData() {
    $db = JFactory::getDbo();

    $apps = $this->getMultiSelectIntData("apps");
    $categories = $this->getMultiSelectIntData("categories");
    $orderby = isset($this->_data['post_orderby']) ? $this->_data['post_orderby'] : '';
    $ordering = isset($this->_data['post_order']) ? $this->_data['post_order'] : 'ASC';

		require_once(JPATH_ADMINISTRATOR.'/components/com_zoo/config.php');

		$this->app = App::getInstance('zoo');
    if (!in_array(0,$categories)){
      $where = ' ci.category_id IN (' . implode(',', $categories) . ') ';
    }else{
      $where = "1=1";
    }
    if (!in_array(0,$apps)){
      $where .= ' AND a.application_id IN (' . implode(',', $apps) . ') ';
    }

  	$now  = $db->Quote($this->app->date->create()->toSQL());
	  $null = $db->Quote($db->getNullDate());

    $limit = "100";
  		$select     = "DISTINCT a.*";
        $from       = ZOO_TABLE_ITEM." AS a"
			         ." LEFT JOIN ".ZOO_TABLE_SEARCH." AS b ON a.id = b.item_id"
		             ." LEFT JOIN ".ZOO_TABLE_TAG." AS c ON a.id = c.item_id"
                 ." LEFT JOIN ".ZOO_TABLE_CATEGORY_ITEM." AS ci ON a.id = ci.item_id";
		$conditions = array("(".$where.")"
                     ." AND a.searchable = 1"
                     ." AND " . $this->app->user->getDBAccessString()
                     ." AND (a.state = 1"
		             ." AND (a.publish_up = ".$null." OR a.publish_up <= ".$now.")"
		             ." AND (a.publish_down = ".$null." OR a.publish_down >= ".$now."))");

    $orderbys = array();
    foreach ($this->_orderbys as &$item) {
      $orderbys[ $item['id'] ] = $item['name'];
    }
    $order = $orderby && isset($orderbys[$orderby]) ? $orderby.' '.$ordering.' ' : 'a.name ASC';

		// execute query
		$items = $this->app->table->item->all(compact('select', 'from', 'conditions', 'order', 'limit'));
    // set renderer
    $renderer = $this->app->renderer->create('item')->addPath(array($this->app->path->path('component.site:'), dirname(__FILE__)));

    $result = array();
    foreach ($items as $k=>$item) {
			$row = array();
			$row["title"] = $item->name;
			$row["text"] = $renderer->render('item.default', array('item' => $item));
			$row["url"] = $this->app->route->item($item);
			$row["created"] = $item->created;
      $row["created_by"] = $item->created_by;
      $row["created_by_alias"] = $item->created_by_alias;
			$b = json_decode($item->elements);
			$key = "";
		  foreach($b as $k=>$i) {
        if(@$b->$k->file) {
          $key = $k;
          //break;
        }
      }
			//$k = key($b);
      if(isset($b) && isset($key) && isset($b->$key)) {
        $row["image"] = isset($key) ? $b->$key->file : "";
      }
      $result[] = $row;
    }

    $ordering = $this->_data['post_order'];

    for ($i = 0; $i < count($result); $i++) {
      $result[$i]['image'] = JURI::root().$result[$i]['image'];
      /*get image from content*/
      $result[$i]['content_image'] = "";
      $result[$i]['content_image_url'] = "";
      preg_match_all('/<img.*?src=["\'](.*?((jpg)|(png)|(jpeg)|(gif)))["\'].*?\>/i',$result[$i]['text'], $res);
      if (isset($res[1]) && isset($res[1][0])){
        $result[$i]['content_image'] = '<img src="'.$this->url.$res[1][0].'" alt="">';
        $result[$i]['content_image_url'] = $this->url.$res[1][0];
      }

      $result[$i]['text'] = strip_tags($result[$i]['text']);

      /*Need to declare for previews*/
      $result[$i]['thumbnail'] = isset($result[$i]['image']) && strlen($result[$i]['image'])>0 ? $result[$i]['image'] : (strlen($result[$i]['content_image_url'])>0 ? $result[$i]['content_image_url'] : LS_ROOT_URL . '/static/admin/img/blank.gif');
      $result[$i]['image-url'] = $result[$i]['thumbnail'];
      $result[$i]['content'] = $result[$i]['text'];
      $result[$i]['date-published'] = $result[$i]['created'];
      $result[$i]['author'] = $result[$i]['created_by_alias'];

      unset($result[$i]['images']);
    }
    return $result;
  }
}