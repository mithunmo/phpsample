<?php
/**
 * momusicWorkSearch
 *
 * Stored in search.class.php
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage momusicWorkSearch
 * @category momusicWorkSearch
 * @version $Rev: 209 $
 */


/**
 * momusicWorkSearch Class
 *
 * The main user search system.
 *
 * @package mofilm
 * @subpackage momusicWorkSearch
 * @category momusicWorkSearch
 */
class momusicWorkSearch extends baseSearch {

	const ORDERBY_ID = 'ID';
	const ORDERBY_DATE = 'registered';
	const ORDERBY_EMAIL = 'email';
	const ORDERBY_FULLNAME = 'fullname';
	
	/**
	 * Stores $_User
	 *
	 * @var mofilmUser
	 * @access protected
	 */
	protected $_User;
	
	/**
	 * Stores $_UserID
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_UserID;
	
	
	
	/**
	 * @see baseSearch::reset()
	 */
	function reset() {
		parent::reset();
		$this->_UserID = null;

	}

	/**
	 * @see baseSearch::initialise()
	 */
	function initialise() {
		parent::initialise();
	}

	/**
	 * Runs the search using the supplied data
	 *
	 * @return momusicWorkSearchResult
	 */
	function search() {
		if ( $this->canSearchRun() ) {
			
			$query = '';
			$this->buildSelect($query);
			$this->buildWhere($query);
			$this->buildOrderBy($query);
			$this->buildLimit($query);
			
			$count = 0;
			$list = array();
			
			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $oStmt->execute() ) {
				$tmp = array();
				foreach ( $oStmt as $row ) {
					$tmp[] = $row['worksID'];
					//$list[] = momusicWorks::getInstance($row["worksID"]);
				}
				$count = dbManager::getInstance()->query('SELECT FOUND_ROWS() AS Results')->fetchColumn();
				if ( count($tmp) > 0 ) {
					
					foreach ( $tmp as $oID ) {
						$list[] = momusicWorks::getInstance($oID);
					}
				}
			}
			$oStmt->closeCursor();
			systemLog::message("count".$count);
			return new momusicWorkSearchResult($list, $count, $this);
		}
		/*
		 * Always return empty result set
		 */
		return new momusicWorkSearchResult(array(), 0, $this);
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
		$inQuery = 'SELECT SQL_CALC_FOUND_ROWS DISTINCT(musicTags.worksID) ';
		$inQuery .= ' FROM '.system::getConfig()->getDatabase('momusic_content').'.tags ';
		
		
		$inQuery .= ' INNER JOIN '.system::getConfig()->getDatabase('momusic_content').'.musicTags ON (tags.ID = musicTags.tagID) ';
		$inQuery .= ' INNER JOIN '.system::getConfig()->getDatabase('momusic_content').'.musicWorks ON (musicWorks.ID = musicTags.worksID) ';
		
	}

	/**
	 * @see baseSearchInterface::buildWhere()
	 */
	function buildWhere(&$inQuery) {
		$where = array();
		
		if ( $this->getKeywords()  ) {
			switch ( $this->getSearchTextType() ) {
				case self::SEARCH_TEXT_LIKE:
				case self::SEARCH_TEXT_MATCH:
					if ( strlen($this->getKeywords()) <=3 ) {
						$where[] = '  tags.name LIKE '.dbManager::getInstance()->quote('% '.str_replace(' ', '%', $this->getKeywords()).' %');
						$where[] = ' musicWorks.status = 1';	
					} else {
						systemLog::message("coming inse status");
						$where[] = ' MATCH (tags.name) AGAINST ('.dbManager::getInstance()->quote('%'.str_replace(' ', '%', $this->getKeywords()).'%').')';	
						$where[] = ' musicWorks.status = 1';	
					}
				break;

				case self::SEARCH_TEXT_MATCH_BOOLEAN:
					$where[] = ' MATCH (tags.name) AGAINST ('.dbManager::getInstance()->quote($this->getKeywords()).' IN BOOLEAN MODE)';
				break;
				case self::SEARCH_TEXT_EXACT:
					$where[] = '  MATCH (tags.name) AGAINST ('.dbManager::getInstance()->quote($this->getKeywords()).')';
					$where[] = '  tags.name LIKE '.dbManager::getInstance()->quote('%'.str_replace(' ', '%', $this->getKeywords()).'%');
				break;	
			}
		} else {
			$where[] = ' musicWorks.status = 1';	
		}

		if ( count($where) > 0 ) {
			$join = $this->getWhereType() == self::WHERE_USING_OR ? ' OR ' : ' AND ';
			$inQuery .= ' WHERE '.implode($join, $where);
		}
		/*
		if ( $this->getHasUploadedMovie() !== null || $this->getOnlyFinalists() ) {
			$inQuery .= ' GROUP BY tags.ID ';
		}
		* 
		*/
	}

	/**
	 * Adds the order by clause to the query
	 *
	 * @param string &$inQuery
	 */
	function buildOrderBy(&$inQuery) {
		$dir = $this->getOrderDirection() == self::ORDER_ASC ? 'ASC' : 'DESC';
		if ( !$this->getKeywords() && $this->getOrderBy() ) {
			if ( $this->getOrderBy() == self::ORDERBY_FULLNAME ) {
				$inQuery .= ' ORDER BY tags.name '.$dir.', tags.name '.$dir;
			} else {
				$inQuery .= ' ORDER BY tags.'.$this->getOrderBy().' '.$dir;
			}
		}
	}

	

	/**
	 * Return value of $_User
	 *
	 * @return mofilmUser
	 * @access public
	 */
	function getUser() {
		return $this->_User;
	}

	/**
	 * Set $_User to $inUser
	 *
	 * @param mofilmUser $inUser
	 * @return momusicWorkSearch
	 * @access public
	 */
	function setUser(mofilmUser $inUser) {
		if ( $inUser !== $this->_User ) {
			$this->_User = $inUser;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Return value of $_UserID
	 *
	 * @return integer
	 * @access public
	 */
	function getUserID() {
		return $this->_UserID;
	}

	/**
	 * Set $_UserID to $inUserID
	 *
	 * @param integer $inUserID
	 * @return momusicWorkSearch
	 * @access public
	 */
	function setUserID($inUserID) {
		if ( $inUserID !== $this->_UserID ) {
			$this->_UserID = $inUserID;
			$this->setModified();
		}
		return $this;
	}

	function setKeywords($inKeywords) {
		if ( $inKeywords !== $this->_Keywords ) {
			if ( strlen($inKeywords) < 2 || !$inKeywords ) {
				throw new systemException("Keywords must be longer than 3 characters and cannot be empty");
			}
			$this->_Keywords = $inKeywords;
			$this->setModified();
		}
		return $this;
	}
	
}