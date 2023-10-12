<?php
/*
 #########################################################################
 #                       xt:Commerce Shopsoftware
 # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 #
 # Copyright 2021 xt:Commerce GmbH All Rights Reserved.
 # This file may not be redistributed in whole or significant part.
 # Content of this file is Protected By International Copyright Laws.
 #
 # ~~~~~~ xt:Commerce Shopsoftware IS NOT FREE SOFTWARE ~~~~~~~
 #
 # https://www.xt-commerce.com
 #
 # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 #
 # @copyright xt:Commerce GmbH, www.xt-commerce.com
 #
 # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 #
 # xt:Commerce GmbH, Maximilianstrasse 9, 6020 Innsbruck
 #
 # office@xt-commerce.com
 #
 #########################################################################
 */

defined('_VALID_CALL') or die('Direct Access is not allowed.');

class nested_set_update extends getCategorySQL_query {

	/**
	 * Position before
	 * @var string
	 */
	const POSITION_BEFORE = 'above';

	/**
	 * Position first child
	 * @var string
	 */
	const POSITION_FIRST_CHILD = 'first_child';

	/**
	 * Position last child
	 * @var string
	 */
	const POSITION_LAST_CHILD = 'last_child';

	/**
	 * Position after
	 * @var string
	 */
	const POSITION_AFTER = 'below';

	const MAX_LEVELS = 7;

	/**
	 * Database adapter
	 * @var ADOConnection
	 */
	protected static $_db = null;

	/**
	 * Table name
	 * @var string
	 */
	protected $_table = TABLE_CATEGORIES;

	/**
	 * Table description name
	 * @var string
	 */
	protected $_table_description = TABLE_CATEGORIES_DESCRIPTION;

	/**
	 * Primary table column
	 * @var string
	 */
	protected $_primary_column = 'categories_id';

	/**
	 * Categories left column name
	 * @var string
	 */
	protected $_column_left = 'categories_left';

	/**
	 * Categories right column name
	 * @var string
	 */
	protected $_column_right = 'categories_right';

	/**
	 * Categories right column name
	 * @var string
	 */
	protected $_column_level = 'categories_level';

	/**
	 * Categories parent_id
	 * @var string
	 */
	protected $_column_parent = 'parent_id';

	/**
	 * @access private
	 */
	protected $_nodeLevelName = 'level';

	/**
	 * @access private
	 */
	protected $_subNodesCountName = 'SubNodesCount';

	/**
	 * @var array
	 */
	protected $_joinConditions = array();

	/**
	 * @var array
	 */
	protected $_whereConditions = array();

	/**
	 * @var array
	 */
	protected $_nestedLevel = array();
	
	/**
	 * @var boolean
	 */
	public $loadMoreImages = false;

	/**
	 * @var array
	 */
	private $completeTree;
	

	/**
	 * Set database adapter
	 * @param object $db
	 */
	public static function setDbAdapter($db)
	{
		self::$_db = $db;
	}

	/**
	 * Get database adapter
	 * @return object
	 */
	public static function getDbAdapter()
	{
		return self::$_db;
	}

	public function setNestedLevel($level)
	{
		$this->_nestedLevel = $level;
	}

	/**
	 * Class constructor. If there is no default database set it to initialized global
	 * $db object.
	 */
	public function __construct()
	{
		global $db;

		if (null == self::$_db) {
			self::$_db = $db;
		}

		parent::getCategorySQL_query();
	}

	/**
	 * Sets table name
	 * @param string $tableName
	 * @return nested_set
	 */
	public function setTable($tableName)
	{
		$this->_table = (string)$tableName;
		return $this;
	}

	/**
	 * Get table name
	 * @return string
	 */
	public function getTable()
	{
		return $this->_table;
	}

	/**
	 * Sets table description name
	 * @param string $tableDescriptionName
	 * @return nested_set
	 */
	public function setTableDescription($tableDescriptionName)
	{
		$this->_table_description = (string)$tableDescriptionName;
		return $this;
	}

	/**
	 * Get table description name
	 * @return string
	 */
	public function getTableDescription()
	{
		return $this->_table_description;
	}

	/**
	 * Get new categories_left and categories_right for given parent at given position
	 * @param int $categories_id
	 * @param string $position
	 * @return int[]
	 */
	public function getCategoryLeftRight($categories_id, $position = self::POSITION_LAST_CHILD)
	{
		$lftRgt = array();
		$lft = null;
		$rgt = null;
		$left = $this->_column_left;
		$right = $this->_column_right;

		if (!empty($categories_id)) {
			list($lft, $rgt) = $this->getCategoryBounds($categories_id);
		}

		// Existing node id
		if ((null !== $lft) && (null !== $rgt)) {
			$sql1 = '';
			$sql2 = '';
			switch ($position) {
				case self::POSITION_FIRST_CHILD :
					$sql1 = "UPDATE {$this->_table} SET $right = $right + 2 WHERE $right > $lft";
					$sql2 = "UPDATE {$this->_table} SET $left = $left + 2 WHERE $left > $lft";

					// Left
					$lftRgt[] = $lft + 1;
					// Right
					$lftRgt[] = $lft + 2;

					break;
				case self::POSITION_LAST_CHILD :
					$sql1 = "UPDATE {$this->_table} SET $right = $right + 2 WHERE $right >= $rgt";
					$sql2 = "UPDATE {$this->_table} SET $left = $left + 2 WHERE $left > $rgt";

					// Left
					$lftRgt[] = $rgt;
					// Right
					$lftRgt[] = $rgt + 1;

					break;
				case self::POSITION_AFTER :
					$sql1 = "UPDATE {$this->_table} SET $right = $right + 2 WHERE $right > $rgt";
					$sql2 = "UPDATE {$this->_table} SET $left = $left + 2 WHERE $left > $rgt";

					// Left
					$lftRgt[] = $rgt + 1;
					// Right
					$lftRgt[] = $rgt + 2;

					break;
				case self::POSITION_BEFORE :
					$sql1 = "UPDATE {$this->_table} SET $right = $right + 2 WHERE $right > $lft";
					$sql2 = "UPDATE {$this->_table} SET $left = $left + 2 WHERE $left >= $lft";

					// Left
					$lftRgt[] = $lft;
					// Right
					$lftRgt[] = $lft + 1;

					break;
			}

			self::$_db->Execute($sql1);
			self::$_db->Execute($sql2);
		} else {
			// Add it to the end of set at first level
			$query = "SELECT MAX({$this->_column_right}) AS categories_right FROM {$this->_table}";
			/** @var ADORecordSet $rs */
			$rs = self::$_db->Execute($query);

			if ($rs->RecordCount() == 0) {
				$right = 0;
			} else {
				$right = $rs->fields['categories_right'];
			}
			$lftRgt = array($right + 1, $right + 2);
		}

		// Left, Right
		return $lftRgt;
	}

	/**
	 * Builds the nested set from the adjacent model and updates the DB
	 *
	 * @param string $primaryKey the primary key
	 * @param string $parentKey the parent key used in the adjacent model
	 * @param string $leftKey the left key for the nested set
	 * @param string $rightKey the right key for the nested set
	 * @return array returns 2 arrays with the left and right values
	 */
	public function buildNestedSet()
	{
		$rootCategoryIds = $this->getCategoryChildren(0);

		$heirarchy = array();
		$positionLeft = 0;

		foreach ($rootCategoryIds as $categoryId) {
			$heirarchy = array_merge($heirarchy, $this->computePositions($categoryId, $positionLeft));
		}

		self::$_db->StartTrans();

		$preparedUpdate = self::$_db->Prepare('
				UPDATE '.$this->_table.'
				SET '.$this->_column_left.'= ?, '.$this->_column_right.' = ?, '.$this->_column_level.' = ?
				WHERE '.$this->_primary_column.' = ?');

		// Update the nested set
		foreach ($heirarchy as $row) {
			self::$_db->Execute($preparedUpdate, array($row[$this->_column_left], $row[$this->_column_right], $row[$this->_column_level], $row[$this->_primary_column]));
		}
		self::$_db->CompleteTrans();
	}

	protected function computePositions($category_id, &$positionLeft, $level = 1)
	{
		$positions = array();
		$positionLeft += 1;
		$currentCategoryPositions = array(
			$this->_primary_column => $category_id,
			$this->_column_left => $positionLeft,
			$this->_column_level => $level
		);

		$children = $this->getCategoryChildren($category_id);
		foreach ($children as $childId) {
			$positions = array_merge($positions, $this->computePositions($childId, $positionLeft, $level+1));
		}

		$positionLeft += 1;
		$currentCategoryPositions[$this->_column_right] = $positionLeft;
		$positions[] = $currentCategoryPositions;

		return $positions;
	}

	private function getTreeBasedOnParentIDs() {
		if($this->completeTree === null) {
			$query = '
				SELECT t1.'.$this->_primary_column.' AS id1, t1.'.$this->_column_left.' AS left1, t1.'.$this->_column_right.' AS right1, t1.categories_level';

			for($i=2;$i<=self::MAX_LEVELS;++$i) {
				$query .= ',t'.$i.'.'.$this->_primary_column.' AS id'.$i.', t'.$i.'.'.$this->_column_left.' AS left'.$i.', t'.$i.'.'.$this->_column_right.' AS right'.$i.',t'.$i.'.categories_level AS level'.$i;
			}

			$query .= '
				FROM '.$this->_table.' AS t1';

			for($i=2;$i<=self::MAX_LEVELS;++$i) {
				$query .= '
				LEFT JOIN '.$this->_table.' AS t'.$i.' ON t'.$i.'.'.$this->_column_parent.' = t'.($i-1).'.'.$this->_primary_column;
			}

			$query .= '
				WHERE t1.'.$this->_column_parent.' = 0
				ORDER BY t1.'.$this->_column_left;

			for($i=2;$i<=self::MAX_LEVELS;++$i) {
				$query .= ', t'.$i.'.'.$this->_column_left;
			}

			/* @var ADORecordSet $result */
			$result = self::$_db->Execute($query);

			while(!$result->EOF) {
				for($level = 1;$level <= self::MAX_LEVELS; ++$level) {
					if($result->fields['id'.$level] === null) {
						break;
					}

					if(!isset($this->completeTree[$result->fields['id'.$level]])) {
						$this->completeTree[$result->fields['id'.$level]] = array(
							'children' => array()
						);
					}

					$parentId = 0;
					if($level > 1) {
						$parentId = $result->fields['id'.($level - 1)];
					}

					// performance optimisation instead of always checking for in_array
					$this->completeTree[$parentId]['children'][$result->fields['id'.$level]] = true;
				}

				$result->MoveNext();
			}
		}

		return $this->completeTree;
	}
	
	public function getCategoryParentLevel($parent_id) 
	{
		$parent_id=(int)$parent_id;
		if ($parent_id==0) return 0;
		
		$query = "SELECT {$this->_column_level} FROM {$this->_table} WHERE {$this->_primary_column} = '{$parent_id}'";
		
		$rs = self::$_db->Execute($query);
		
		if ($rs->RecordCount() == 0) {
			return 0;
		}
		
		return $rs->fields[$this->_column_level];
	}

	protected function getCategoryChildren($parent_id)
	{
		$tree = $this->getTreeBasedOnParentIDs();

		if(!isset($tree[$parent_id]['children'])) {
			return array();
		}

		return array_keys($tree[$parent_id]['children']);
	}

	/**
	 * Returns the left and right values for the nested set
	 *
	 * @param array $records records
	 * @param string $primaryKey the primary key
	 * @return array returns 2 arrays with the left and right values
	 */
	protected function getNestedSet($records, $primaryKey)
	{
		$left = array();
		$right = array();

		$level = 1;
		$current = 0;
		foreach ($records as $row) {
			if ($row['level'] == $level) {
				$current++;
			} elseif ($row['level'] < $level) {
				$current += $level - $row['level'] + 1;
			}
			$level = $row['level'];

			$left[$row[$primaryKey]] = $current;
			$current++;
			$right[$row[$primaryKey]] = $current + $row[$this->_subNodesCountName] * 2;
		}

		return array($left, $right);
	}

	/**
	 * Get category right value
	 * @param integer $categories_id
	 * @return number
	 */
	public function getCategoryRight($categories_id)
	{
		list($left, $right) = $this->getCategoryBounds($categories_id);
		return $right;
	}

	/**
	 * Get category left value
	 * @param int $categories_id
	 * @return number
	 */
	public function getCategoryLeft($categories_id)
	{
		list($left, $right) = $this->getCategoryBounds($categories_id);
		return $left;
	}

	/**
	 * Get category left/right bounds
	 * @param int $categories_id
	 * @return array
	 */
	public function getCategoryBounds($categories_id)
	{
		if (!empty($categories_id)) {
			$query = "SELECT {$this->_column_right}, {$this->_column_left} FROM {$this->_table} WHERE {$this->_primary_column} = '{$categories_id}'";
		} else {
			$query = "SELECT MAX({$this->_column_right}) AS {$this->_column_right}, MIN({$this->_column_left}) AS {$this->_column_left} FROM {$this->_table}";
		}

		/** @var ADORecordSet $rs */
		$rs = self::$_db->Execute($query);

		if ($rs->RecordCount() == 0) {
			return array(1,2);
		}

		return array(
			$rs->fields[$this->_column_left],
			$rs->fields[$this->_column_right]
		);
	}

	function getSQL_query($additionalColumns = '', $filter_type ='string') {
		global $xtPlugin;

		if (USER_POSITION =='store') {
			$this->setFilter('GroupCheck');
			$this->setFilter('StoreCheck');
		}
		if (!empty($this->_nestedLevel)) {
			$this->setSQL_WHERE(" AND c.{$this->_column_parent} IN (" . join(',', $this->_nestedLevel) . ")");
		}

		($plugin_code = $xtPlugin->PluginCode('class.category_sql_query.php:getSQL_query_filter')) ? eval($plugin_code) : false;

		$this->getFilter();
		$this->getHooks();
		$this->a_sql_cols = join(',', $this->_selectTables) . $this->a_sql_cols.',c.'.$this->_column_level.' AS level'.$additionalColumns;

		$sql = "
			SELECT {$this->a_sql_cols} FROM {$this->a_sql_table}";
		if (is_data($this->a_sql_where))
			$sql.=' WHERE '.$this->a_sql_where;
		$sql .= " GROUP BY c.{$this->_primary_column}";
		if (is_data($this->a_sql_sort))
			$sql.=' ORDER BY '.$this->a_sql_sort;
		if (is_data($this->a_sql_limit))
			$sql.=' LIMIT '.$this->a_sql_limit;

		if (USER_POSITION =='admin') {
			$sql = str_replace(" c.categories_status = '1' and ","",$sql);
		}

		return $sql;
	}

	function F_Sorting($sort) {
		switch ($sort) {
			case 'name' :
				$this->setSQL_SORT(' cd.categories_name');
				break;

			case 'name-desc' :
				$this->setSQL_SORT(' cd.categories_name DESC');
				break;

			case 'sort_order' :
				$this->setSQL_SORT(' c.categories_left');
				break;

			case 'sort_order-desc' :
				$this->setSQL_SORT(' c.categories_left DESC');
				break;

			default:
				return false;
		}
	}

	/**
	 * Get category three
	 *
	 * @param boolean $cached
	 * @return array
	 */
	public function getTree($cached = true)
	{
		global $xtPlugin;

		($plugin_code = $xtPlugin->PluginCode('class.nested_set.php:getTree')) ? eval($plugin_code) : false;
		if(isset($plugin_return_value))
			return $plugin_return_value;

		$query = $this->getSQL_query();

		/** @var ADORecordSet $rs */
		$rs = self::$_db->Execute($query);
		$tree = array();
		if ($rs->RecordCount() > 0) {
			while(!$rs->EOF){
				if($rs->fields[$this->_column_level] > 0) {
					$rs->fields[$this->_subNodesCountName] = max(
						0,
						($rs->fields[$this->_column_right] - $rs->fields[$this->_column_left] - 1) / 2
					);
					$tree[] = $rs->fields;
				}
				$rs->MoveNext();
			}
			$rs->Close();
		}

		return $tree;
	}

	/**
	 * Build a heirarchy from tree
	 * @param array $tree
	 * @param number $parent_id
	 * @param string $nested
	 * @param array $current_path
	 * @param string $class The class name of current class
	 * @return array
	 */
	public function buildHierarchy($tree, $parent_id = 0, $nested = true, $current_path = array(),$direct_children_only=0, $class = "category")
	{
		global $mediaImages, $xtLink;
		$return = array();
		$categories = $this->getTopTree($tree, $parent_id);

		foreach ($categories as &$category) {
			$category['active'] = '0';

			if ($this->loadMoreImages) {
				$media_images = $mediaImages->get_media_images($category[$this->_primary_column], $class);
				$category['more_images'] = $media_images['images'];
			}

			// Check only last element. Last element is the current category in the current path.
			if ($category[$this->_primary_column] == $current_path[count($current_path)-1])
				$category['active'] = '1';
			// mark the category as active parent if in current path
			else if (in_array($category[$this->_primary_column], $current_path))
				$category['active_parent'] = '1';

			if ($category['category_custom_link']==1) // custom_link not a category
			{
				$url = $this->buildCustomLinkURL($category);
				$category['categories_link'] = $url;
			}
			else {
				$link_array = array('page'=> 'categorie',
									'type'=> 'category',
									'name'=>$category['categories_name'],
									'text'=>$category['categories_name'],
									'id'=>$category['categories_id'],
									'seo_url'=>$category['url_text'],
				);

				$category['categories_link'] = $xtLink->_link($link_array);
			}

			$tpl = new Template();
			$category['listing_template'] = $tpl->getDefaultTemplate($category['listing_template'], 'product_listing/');
			$category['categories_template'] = $tpl->getDefaultTemplate($category['categories_template'], 'categorie_listing/');

			if ($nested) {
				$category['sub'] = $this->buildHierarchy($tree, $category[$this->_primary_column], $nested, $current_path,$direct_children_only);
				$return[] = $category;
			} else {
				$return[] = $category;
				if ($direct_children_only!=1){
					$children = $this->buildHierarchy($tree, $category[$this->_primary_column], $nested, $current_path,$direct_children_only);
					$return = array_merge($return, $children);
				}
			}
		}

		return $return;
	}

	/*Building custom link in category tree
     * @param int $category
     * @return string
     * */
	public function buildCustomLinkURL($category){
		/** @var stdClass $language */
		global $xtLink,$store_handler, $language;

		$url = '';
		switch($category['category_custom_link_type']){
			case 'product':
			case 'category':
			case 'content':
				// create an instance of the respective custom link class (product,category or content)
				$info =  new $category['category_custom_link_type']($category['category_custom_link_id']);
				$link_arr = array('page'=> $category['category_custom_link_type'],
								  'type'=>$category['category_custom_link_type'],
				                  'name'=>$category['categories_name'],
								  'id'=>$category['category_custom_link_id'],
								  'seo_url'=>$info->data['url_text']);
				$url = $xtLink->_link($link_arr);
				break;

			case 'plugin':
				/** @var ADORecordSet $rs */
				$rs = self::$_db->Execute("SELECT url_text, code FROM ".TABLE_SEO_URL." s
                                  INNER JOIN ".TABLE_PLUGIN_PRODUCTS." p ON p.plugin_id = s.link_id
                                  WHERE s.link_type=1000 and s.link_id = ?
                                  AND s.store_id = ? and s.language_code = ?",
					array($category['category_custom_link_id'], $store_handler->shop_id, $language->code));
				if ($rs->RecordCount()>0)
				{
					$link_arr = array('page'=> $rs->fields['code'],
									  'type'=>$rs->fields['code'],
									  'id'=>$category['category_custom_link_id'],
									  'seo_url'=>$rs->fields['url_text']);
					$url = $xtLink->_link($link_arr);
				}
				break;
			case 'custom':

				$url = $category['link_url'];
				break;
		}

		return $url;
	}

	/**
	 * Get all child categories of category no matter the depth
	 * @param int $categories_id
	 * @return array
	 */
	public function getChildCategoryIds($categories_id) {

		$category_ids = array();
		$categories_id = (int)$categories_id;
		list($left, $right) = $this->getCategoryBounds($categories_id);

		/** @var ADORecordSet $rs */
		$rs = self::$_db->Execute("SELECT ".$this->_primary_column." FROM {$this->_table} WHERE categories_left BETWEEN {$left} AND {$right}");

		if ($rs->RecordCount() > 0) {
			while (!$rs->EOF) {
				$category_ids[] = $rs->fields[$this->_primary_column];
				$rs->MoveNext();
			}
			$rs->Close();
		}

		return $category_ids;
	}

	/**
	 * Get category parent path
	 * @param int $categories_id
	 * @return int[] ancestor category IDs order by level descending
	 */
	public function getCategoryPath($categories_id) {
		$path = array();

		$query = "
			SELECT {$this->_primary_column}
			FROM {$this->_table}
			WHERE
				{$this->_column_left} <= (SELECT {$this->_column_left} FROM {$this->_table} WHERE {$this->_primary_column} = '{$categories_id}') AND
				{$this->_column_right} >= (SELECT {$this->_column_right} FROM {$this->_table} WHERE {$this->_primary_column} = '{$categories_id}')
			ORDER BY {$this->_column_left} DESC
		";
		/** @var ADORecordSet $rs */
		$rs = self::$_db->Execute($query);

		if ($rs->RecordCount() > 0) {
			while (!$rs->EOF) {
				$path[] = $rs->fields[$this->_primary_column];
				$rs->MoveNext();
			}
			$rs->Close();
		}

		return $path;
	}

	/**
	 * Retrieves subtree starting with $id
	 *
	 * @access public
	 */
	function getSubTree(&$rows, $id, $idName, $returnTopNode = true)
	{
		if (empty($id)) {
			return $rows;
		}
		$skipLevel = -1;
		$resultRows = array();
		foreach ($rows as $key => $row) {
			if (($skipLevel != -1) and ($row[$this->_nodeLevelName] > $skipLevel)) {
				$resultRows[] = $row;
				continue;
			}

			if ($row[$idName] == $id) {
				$skipLevel = $row[$this->_nodeLevelName];
				if ($returnTopNode)
					$resultRows[] = $row;
			} else
				$skipLevel = -1;
		}
		return $resultRows;
	}

	function getTopTree(&$rows, $id)
	{
		$resultRows = array();
		foreach ($rows as $row) {
			if ($row[$this->_column_parent] == $id) {
				$resultRows[] = $row;
			}
		}
		return $resultRows;
	}

	/**
	 * Returns only the nodes that are expanded, preserving the deep tree traversal order
	 *
	 * @param array $rows        the nodes in deep tree traversal order
	 * @param array $expandedIds an array of ids that are expanded
	 *
	 * @access public
	 * @return array
	 */
	function getExpandedNodesOnly(&$rows, $expandedIds)
	{
		$skipLevel = -1;
		$expandedIds = array_map('intval', $expandedIds);
		$resultRows = array();
		foreach ($rows as $key => $row) {
			if (($skipLevel != -1) and ($row[$this->_nodeLevelName] > $skipLevel))	// Skip hidden sublevels
				continue;
			if (($row[$this->_column_parent] == 0) or in_array((int)$row[$this->_column_parent], $expandedIds)) {
				$resultRows[] = $row;
				$skipLevel = -1;
			} else
				$skipLevel = $row[$this->_nodeLevelName];
		}
		return $resultRows;
	}

	/**
	 * Returns the rows with all subnodes of $id removed
	 */
	function removeSubTree(&$rows, $id, $idName, $removeTopNode = false)
	{
		$skipLevel = -1;
		$resultRows = array();
		foreach ($rows as $row) {
			if (($skipLevel != -1) and ($row[$this->_nodeLevelName] > $skipLevel))	// Skip hidden sublevels
				continue;

			if ($row[$idName] == $id) {
				$skipLevel = $row[$this->_nodeLevelName];
				if ($removeTopNode) continue;
			} else
				$skipLevel = -1;
			$resultRows[] = $row;
		}
		return $resultRows;
	}
}

