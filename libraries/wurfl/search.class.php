<?php
/**
 * wurflSearch class
 * 
 * Provides an interface to the wurfl object system
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage wurfl
 * @category wurflSearch
 * @version $Rev: 650 $
 */


/**
 * wurflSearch
 * 
 * Searches for handsets based on supplied criteria returning a {@link wurflResultSet}
 * object. Handsets can be searched via user agent or wurflID and either specific
 * matches or only root level devices returned.
 * 
 * Example:
 * <code>
 * $oSearch = new wurflSearch();
 * $oSearch->setRootDevices(true);
 * $oSearch->setSearchField(wurflSearch::SEARCH_FIELD_USER_AGENT);
 * $oSearch->setKeywords('w880i');
 * $oResults = $oSearch->search();
 * foreach ( $oResults as $oDevice ) {
 *     // do something with results...
 * }
 * </code>
 * 
 * @package scorpio
 * @subpackage wurfl
 * @category wurflSearch
 */
class wurflSearch extends baseSearch {
	
	const SEARCH_FIELD_USER_AGENT = 'userAgent';
	const SEARCH_FIELD_WURFLID = 'wurflID';
	
	const SEARCH_ROOT_DEVICES_TRUE = true;
	const SEARCH_ROOT_DEVICES_FALSE = false;
	
	const SEARCH_TEXT_UA_LOOKUP = 5;
	
	const ORDERBY_USER_AGENT = 'userAgent';
	const ORDERBY_WURFLID = 'wurflID';
	
	/**
	 * Stores $_RootDevices
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_RootDevices;
	
	/**
	 * Stores $_SearchField
	 *
	 * @var string
	 * @access protected
	 */
	protected $_SearchField;
	
	
	
	/**
	 * @see baseSearch::reset()
	 */
	function reset() {
		parent::reset();
		$this->_RootDevices = null;
		$this->_SearchField = null;
		$this->_OrderBy = self::ORDERBY_USER_AGENT;
		$this->_AllowedOrderBy = array(self::ORDERBY_USER_AGENT, self::ORDERBY_WURFLID);
	}
	
	/**
	 * @see baseSearch::initialise()
	 */
	function initialise() {
		parent::initialise();
		
		$this->addAllowedOrderBy(self::ORDERBY_USER_AGENT)
			->addAllowedOrderBy(self::ORDERBY_WURFLID);
	}
	
	/**
	 * Runs the search using the supplied data
	 *
	 * @return wurflResultSet
	 */
	function search() {
		if ( $this->canSearchRun() ) {
			if ( $this->getSearchTextType() == self::SEARCH_TEXT_UA_LOOKUP ) {
				return new wurflResultSet(array(wurflManager::getInstanceByUserAgent($this->getKeywords())), 1, $this);
			}
			
			$query = '';
			$this->buildSelect($query);
			$this->buildWhere($query);
			$this->buildOrderBy($query);
			$this->buildLimit($query);
			
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $oStmt->execute() ) {
				$list = array();
				foreach ( $oStmt as $row ) {
					$oDevice = new wurflDevice();
					$oDevice->loadFromArray($row);
					$list[] = $oDevice;
				}
				
				$oRes = dbManager::getInstance()->query('SELECT FOUND_ROWS() AS Results');
				$results = $oRes->fetch();
				
				return new wurflResultSet($list, $results['Results'], $this);
			}
		}
		/*
		 * Always return empty result set
		 */
		return new wurflResultSet(array(), 0, $this);
	}
	
	/**
	 * @see baseSearchInterface::canSearchRun()
	 */
	function canSearchRun() {
		$return = true;
		
		return $return;
	}
	
	/**
	 * @see baseSearchInterface::buildSelect()
	 */
	function buildSelect(&$inQuery) {
		$inQuery = 'SELECT SQL_CALC_FOUND_ROWS devices.* ';
		if ( $this->getSearchTextType() && strlen($this->getKeywords()) >= 3 ) {
			switch ( $this->getSearchTextType() ) {
				case self::SEARCH_TEXT_MATCH:
				case self::SEARCH_TEXT_MATCH_BOOLEAN:
					$inQuery .= ', MATCH (devices.'.$this->getSearchField().') AGAINST ('.dbManager::getInstance()->quote($this->getKeywords()).') AS Score ';
				break;
			}
		}
		$inQuery .= ' FROM '.system::getConfig()->getDatabase('wurfl').'.devices ';
	}
	
	/**
	 * @see baseSearchInterface::buildWhere()
	 */
	function buildWhere(&$inQuery) {
		$where = array();
		if ( $this->getRootDevices() ) {
			$where[] = ' devices.rootDevice = 1';
		}
		if ( $this->getKeywords() ) {
			switch ( $this->getSearchTextType() ) {
				case self::SEARCH_TEXT_EXACT:
					$where[] = ' devices.'.$this->getSearchField().' = '.dbManager::getInstance()->quote($this->getKeywords());
				break;
				
				case self::SEARCH_TEXT_LIKE:
					$where[] = ' devices.'.$this->getSearchField().' LIKE '.dbManager::getInstance()->quote('%'.str_replace(' ','%', $this->getKeywords()).'%');
				break;
				
				case self::SEARCH_TEXT_MATCH:
					$where[] = ' MATCH (devices.'.$this->getSearchField().') AGAINST ('.dbManager::getInstance()->quote($this->getKeywords()).')';
				break;
				
				case self::SEARCH_TEXT_MATCH_BOOLEAN:
					$where[] = ' MATCH (devices.'.$this->getSearchField().') AGAINST ('.dbManager::getInstance()->quote($this->getKeywords()).' IN BOOLEAN MODE)';
				break;
			}
		}
		if ( count($where) > 0 ) {
			$join = $this->getWhereType() == self::WHERE_USING_OR ? ' OR ' : ' AND ';
			$inQuery .= ' WHERE '.implode($join, $where);
		}
	}
	
	
	
	/**
	 * @see baseSearch::setSearchTextType()
	 */
	function setSearchTextType($inSearchTextType) {
		if ( $inSearchTextType !== $this->_SearchTextType ) {
			if ( !in_array($inSearchTextType, array(self::SEARCH_TEXT_EXACT, self::SEARCH_TEXT_LIKE, self::SEARCH_TEXT_MATCH, self::SEARCH_TEXT_MATCH_BOOLEAN, self::SEARCH_TEXT_UA_LOOKUP)) ) {
				throw new systemException("The search text modifier must be one of: 1 - Like, 2 - Match, 3 - Exact, 4 - Match Boolean or 5 - UA Lookup");
			}
			$this->_SearchTextType = $inSearchTextType;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Returns $_RootDevices
	 *
	 * @return boolean
	 * @access public
	 */
	function getRootDevices() {
		return $this->_RootDevices;
	}
	
	/**
	 * Set $_RootDevices to $inRootDevices
	 *
	 * @param boolean $inRootDevices
	 * @return wurflSearch
	 * @access public
	 */
	function setRootDevices($inRootDevices) {
		if ( $this->_RootDevices !== $inRootDevices ) {
			$this->_RootDevices = $inRootDevices;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Returns $_SearchField
	 *
	 * @return string
	 * @access public
	 */
	function getSearchField() {
		return $this->_SearchField;
	}
	
	/**
	 * Set $_SearchField to $inSearchField
	 *
	 * @param string $inSearchField
	 * @return wurflSearch
	 * @access public
	 */
	function setSearchField($inSearchField) {
		if ( $this->_SearchField !== $inSearchField ) {
			$valid = array(self::SEARCH_FIELD_USER_AGENT, self::SEARCH_FIELD_WURFLID);
			if ( !in_array($inSearchField, $valid) ) {
				throw new wurflException("($inSearchField) is not a valid field. Must be one of (".implode(', ', $valid).")");
			}
			$this->_SearchField = $inSearchField;
			$this->setModified();
		}
		return $this;
	}
}