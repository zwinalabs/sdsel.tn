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

class OfflajnGenerator_JoomlaContent extends OfflajnGenerator {

	/*Check if component is installed*/
	static $path= "/components/";

	static $name= "Joomla Content";

	function __construct($data) {
		parent::__construct($data);
		$this->_variables = array(
			'id' => 'ID of the article',
			'title' => 'Title of the article',
			'url' => 'Url of the article',
			'alias' => 'Alias of the article',
			'introtext' => 'Intro of the article',
			'fulltext' => 'Text of the article',
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
			'intro_image' => '',
			'fulltext_image' => '',
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

		// Languages list
		$db->setQuery('SELECT lang_code AS id, title AS name FROM #__languages WHERE published = 1');
		$languages = $db->loadAssocList();
		// array_unshift($languages, array( 'id' => '*', 'name' => JText::_('JALL_LANGUAGE') ));

		// Categories list
		$query = 'SELECT c.id AS id, c.title AS name, c.title AS title, c.parent_id AS parent, c.parent_id AS parent_id
			FROM #__categories AS c
			WHERE c.published = 1 AND c.extension="com_content"';
		$db->setQuery( $query );
		$categories = $db->loadAssocList();

		// Tags list
		if(version_compare(JVERSION,'3.1','ge')) {
			$query = "SELECT DISTINCT t.id AS id, t.alias AS alias, t.title AS name FROM #__tags AS t
				WHERE id > 1 AND published = 1";
			$db->setQuery( $query );
			$tags = $db->loadAssocList();
		} else $tags = array();

		// Author's list who wrote articles
		$query = "SELECT DISTINCT u.id AS id, u.name AS name
			FROM #__users AS u
			LEFT JOIN #__content AS c
			ON c.created_by = u.id
			WHERE c.state = '1'";
		$db->setQuery( $query );
		$authors = $db->loadAssocList();

		$filters = '';
		$filters .= $this->multiSelect('languages', $languages, 'languages');
		$filters .= $this->multiSelect('categories', $categories, 'categories');
		$filters .= $this->multiSelect('authors', $authors, 'authors');
		$filters .= $this->multiSelect('tags', $tags, 'tags');

		return $filters;
	}

	function getOrderBys(){
		return $this->simpleSelect('orderby', $this->_orderbys);
	}

	function getData() {
		$db = JFactory::getDbo();

		$languages = $this->getMultiSelectStringData('languages');
		$categories = $this->getMultiSelectIntData('categories');
		$authors = $this->getMultiSelectIntData('authors');
		$tags = $this->getMultiSelectIntData('tags');
		$orderby = isset($this->_data['post_orderby']) ? $this->_data['post_orderby'] : '';
		$ordering = isset($this->_data['post_order']) ? $this->_data['post_order'] : 'DESC';

		$query = 'SELECT ';
		$query .= 'con.id, ';
		$query .= 'con.title, ';
		$query .= 'con.alias, ';
		$query .= 'con.introtext, ';
		$query .= 'con.fulltext, ';
		$query .= 'con.catid, ';
		$query .= 'cat.title AS cat_title, ';
		$query .= 'con.created_by, ';
		$query .= 'con.created, ';
		$query .= 'con.modified, ';
		$query .= 'con.hits, ';
		$query .= 'con.publish_up, ';
		$query .= 'usr.name AS created_by_alias, ';
		$query .= 'con.images ';
		$query .= 'FROM #__content AS con ';
		$query .= 'LEFT JOIN #__users AS usr ON usr.id = con.created_by ';
		$query .= 'LEFT JOIN #__categories AS cat ON cat.id = con.catid ';

		if (!in_array('0', $languages)) {
			$query .= 'WHERE con.language IN ("' . implode('", "', $languages) . '") ';
		} else {
			$query .= 'WHERE 1=1 ';
		}

		if (!in_array(0, $categories)) {
			$query .= 'AND con.catid IN (' . implode(',', $categories) . ') ';
		}

		if (!in_array(0, $authors)) {
			$query .= 'AND con.created_by IN (' . implode(',', $authors) . ') ';
		}

		$query .= 'AND con.state = 1 ';

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
			$result[$i]['url'] = JRoute::_($this->url.'index.php?option=com_content&view=article&id=' . $result[$i]['id']);
			$result[$i]['categorylisturl'] = JRoute::_($this->url.'index.php?option=com_content&view=category&id=' . $result[$i]['catid']);
			$result[$i]['categoryblogurl'] = JRoute::_($this->url.'index.php?option=com_content&view=category&layout=blog&id=' . $result[$i]['catid']);
			$images = (array)json_decode($result[$i]['images'], true);
			$result[$i]['intro_image'] = isset($images['image_intro']) ? '<img src="'.$this->url.$images['image_intro'].'" alt="">' : '';
			$result[$i]['fulltext_image'] = isset($images['image_fulltext']) ? '<img src="'.$this->url.$images['image_fulltext'].'" alt="">' : '';
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
			$result[$i]['thumbnail'] = isset($images['image_intro']) && strlen($images['image_intro'])>0 ? $this->url.$images['image_intro'] : (isset($images['image_fulltext']) && strlen($images['image_fulltext'])>0 ? $this->url.$images['image_fulltext'] : (strlen($result[$i]['content_image_url'])>0 ? $result[$i]['content_image_url'] : LS_ROOT_URL . '/static/admin/img/blank.gif'));
			$result[$i]['image-url'] = $result[$i]['thumbnail'];
			$result[$i]['content'] = $result[$i]['introtext'];
			$result[$i]['date-published'] = $result[$i]['publish_up'];
			$result[$i]['author'] = $result[$i]['created_by_alias'];

			unset($result[$i]['images']);
		}
		return $result;
	}

}