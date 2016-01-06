<?php
/**
 * mofilmSystemHelpPageSearch
 *
 * Stored in mofilmSystemHelpPageSearch.class.php
 *
 * @author Pavan Kumar P G
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmSystemHelpPageSearch
 * @category mofilmSystemHelpPageSearch
 * @version $Rev: 806 $
 */


/**
 * mofilmSystemHelpPageSearch Class
 *
 * The main help page search system.
 *
 * @package mofilm
 * @subpackage mofilmSystemHelpPageSearch
 * @category mofilmSystemHelpPageSearch
 */
class mofilmSystemHelpPageSearch extends baseSearch {

	/**
	 * Stores $_ID
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_ID;

	/**
	 * Stores $_DomainName
	 *
	 * @var string
	 * @access protected
	 */
	protected $_DomainName;

	/**
	 * Stores $_Reference
	 *
	 * @var string
	 * @access protected
	 */
	protected $_Reference;

	/**
	 * Stores $_Title
	 *
	 * @var string
	 * @access protected
	 */
	protected $_Title;

	/**
	 * Stores $_Content
	 *
	 * @var string
	 * @access protected
	 */
	protected $_Content;

	/**
	 * Stores $_Language
	 *
	 * @var string
	 * @access protected
	 */
	protected $_Language;

	/**
	 * Stores $_LoadObjectDetails
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_LoadObjectDetails;
	
	/**
	 * Stores $_TagID
	 * 
	 * @var Integer
	 * @access protected
	 */
	protected $_TagID;
	
	/**
	 * 
	 */
	protected $_TagIDFlag;

	
	/**
	 * @see baseSearch::reset()
	 */
	function reset() {
		parent::reset();
		$this->_ID = null;
		$this->_DomainName = null;
		$this->_Reference = null;
		$this->_Title = null;
		$this->_Content = null;
		$this->_Language = null;
		$this->_LoadObjectDetails = false;
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
	 * @return mofilmSystemHelpPageSearchResult
	 */
	function search() {
		if ( $this->canSearchRun() ) {
			if ( $this->getID() ) {
				return new mofilmSystemHelpPageSearchResult(
					array(mofilmSystemHelpPageManager::getInstanceByID($this->getID())), 1, $this
				);
			}

			$query = '';
			$this->buildSelect($query);
			$this->buildWhere($query);
			$this->buildOrderBy($query);
			$this->buildGroupBy($query);
			$this->buildLimit($query);

			$count = 0;
			$list = array();

			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $oStmt->execute() ) {
				$tmp = array();
				foreach ( $oStmt as $row ) {
					$tmp[] = $row['ID'];
				}

				$count = dbManager::getInstance()->query('SELECT FOUND_ROWS() AS Results')->fetchColumn();
				if ( count($tmp) > 0 ) {
					$oObjectMan = mofilmSystemHelpPageManager::getInstance();
					$oObjectMan->setLoadObjectDetails($this->getLoadObjectDetails());
					$list = $oObjectMan->loadObjectsByArray($tmp);
				}
			}
			$oStmt->closeCursor();

			return new mofilmSystemHelpPageSearchResult($list, $count, $this);
		}

		/*
		 * Always return empty result set
		 */
		return new mofilmSystemHelpPageSearchResult(array(), 0, $this);
	}
	
	/*
	 * @see baseSearchInterface::buildSelect()
	 */
	function buildSelect(&$inQuery) {
		$inQuery = '
			SELECT SQL_CALC_FOUND_ROWS DISTINCT(helpPages.ID)
			  FROM '.system::getConfig()->getDatabase('system').'.helpPages ';
		
		if ( $this->getTagIDFlag() ) {
			$inQuery .= '
			    LEFT JOIN '.system::getConfig()->getDatabase('system').'.helpPageTags on ( helpPages.ID = helpPageTags.helpID )
			    ';
		} else {
			$inQuery .= '
			    INNER JOIN '.system::getConfig()->getDatabase('system').'.helpPageTags on ( helpPages.ID = helpPageTags.helpID )
			    INNER JOIN '.system::getConfig()->getDatabase('system').'.helpTags on ( helpPageTags.tagID = helpTags.ID )
			    ';
		}
	}

	/*
	 * @see baseSearchInterface::buildWhere()
	 */
	function buildWhere(&$inQuery) {
		$where = array();

		if ( null !== $this->getDomainName() && strlen($this->getDomainName()) > 1 ) {
			$where[] = 'domainName = ' . dbManager::getInstance()->quote($this->getDomainName());
		}

		if ( null !== $this->getLanguage() && strlen($this->getLanguage()) > 0 ) {
			$where[] = 'language = '.dbManager::getInstance()->quote($this->getLanguage());
		}

		if ( null !== $this->getTitle() && strlen($this->getTitle()) > 0 && !$this->getKeywords() ) {
			$where[] = 'title = '.dbManager::getInstance()->quote($this->getTitle());
		}
		
		if ( null !== $this->getTagID() && strlen($this->getTagID()) > 0 ) {
			$where[] = 'helpPageTags.tagID = '.$this->getTagID();
		}
		
		if ( $this->getKeywords() && strlen($this->getKeywords()) > 3 ) {
			switch ( $this->getSearchTextType() ) {
				case self::SEARCH_TEXT_EXACT:
					$where[] = 'title = '.dbManager::getInstance()->quote($this->getKeywords());
				break;

				case self::SEARCH_TEXT_LIKE:
					$where[] = 'content LIKE '.dbManager::getInstance()->quote(str_replace(' ', '%', $this->getKeywords()));
				break;

				case self::SEARCH_TEXT_MATCH:
					$where[] = '( MATCH (title, content) AGAINST ('.dbManager::getInstance()->quote($this->getKeywords()).') || 
					    tag = '.dbManager::getInstance()->quote($this->getKeywords()).')';
				break;

				case self::SEARCH_TEXT_MATCH_BOOLEAN:
					$where[] = 'MATCH (title, content) AGAINST ('.dbManager::getInstance()->quote($this->getKeywords()).' IN BOOLEAN MODE)';
				break;
			}
		}

		if ( null !== $this->getReference() && strlen($this->getReference()) > 1 ) {
			$where[] = 'reference = ' . dbManager::getInstance()->quote($this->getReference());
		}

		if ( count($where) > 0 ) {
			$inQuery .= ' WHERE ' . implode(' AND ', $where);
		}
	}
	
	/**
	 * Adds the order by clause to the query
	 *
	 * @param string &$inQuery
	 */
	function buildOrderBy(&$inQuery) {
		$dir = $this->getOrderDirection() == self::ORDER_ASC ? 'ASC' : 'DESC';
		if ( !$this->getKeywords() && $this->getOrderBy() ) {
			$inQuery .= ' ORDER BY helpPages.'.$this->getOrderBy().' '.$dir;
		}
	}
	
	/**
	 * Adds the build by clause to the query
	 * 
	 * @param string &$inQuery
	 */
	function buildGroupBy(&$inQuery) {
		if ( !$this->getTagIDFlag() ) {
			$inQuery .= ' GROUP BY helpPages.ID';
		}
	}

	/**
	 * Return the current value of the property $_ID
	 *
	 * @return integer
	 */
	function getID() {
		return $this->_ID;
	}

	/**
	 * Set the object property _ID to $inID
	 *
	 * @param integer $inID
	 * @return mofilmSystemHelpPages
	 */
	function setID($inID) {
		if ( $inID !== $this->_ID ) {
			$this->_ID = $inID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_DomainName
	 *
	 * @return string
	 */
	function getDomainName() {
		return $this->_DomainName;
	}

	/**
	 * Set the object property _DomainName to $inDomainName
	 *
	 * @param string $inDomainName
	 * @return mofilmSystemHelpPages
	 */
	function setDomainName($inDomainName) {
		if ( $inDomainName !== $this->_DomainName ) {
			$this->_DomainName = $inDomainName;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_Reference
	 *
	 * @return string
	 */
	function getReference() {
		return $this->_Reference;
	}

	/**
	 * Set the object property _Reference to $inReference
	 *
	 * @param string $inReference
	 * @return mofilmSystemHelpPages
	 */
	function setReference($inReference) {
		if ( $inReference !== $this->_Reference ) {
			$this->_Reference = $inReference;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_Title
	 *
	 * @return string
	 */
	function getTitle() {
		return $this->_Title;
	}

	/**
	 * Set the object property _Title to $inTitle
	 *
	 * @param string $inTitle
	 * @return mofilmSystemHelpPages
	 */
	function setTitle($inTitle) {
		if ( $inTitle !== $this->_Title ) {
			$this->_Title = $inTitle;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_Content
	 *
	 * @return string
	 */
	function getContent() {
		return $this->_Content;
	}

	/**
	 * Set the object property _Content to $inContent
	 *
	 * @param string $inContent
	 * @return mofilmSystemHelpPages
	 */
	function setContent($inContent) {
		if ( $inContent !== $this->_Content ) {
			$this->_Content = $inContent;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_Language
	 *
	 * @return string
	 */
	function getLanguage() {
		return $this->_Language;
	}

	/**
	 * Set the object property _Language to $inLanguage
	 *
	 * @param string $inLanguage
	 * @return mofilmSystemHelpPages
	 */
	function setLanguage($inLanguage) {
		if ( $inLanguage !== $this->_Language ) {
			$this->_Language = $inLanguage;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns the value of $_LoadObjectDetails
	 *
	 * @return boolean
	 */
	function getLoadObjectDetails() {
		return $this->_LoadObjectDetails;
	}

	/**
	 * Set $_LoadObjectDetails to $inLoadObjectDetails
	 *
	 * @param boolean $inLoadObjectDetails
	 * @return mofilmSystemHelpPageSearch
	 */
	function setLoadObjectDetails($inLoadObjectDetails) {
		if ( $inLoadObjectDetails !== $this->_LoadObjectDetails ) {
			$this->_LoadObjectDetails = $inLoadObjectDetails;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Return the value of $_TagID
	 * 
	 * @return 
	 */
	function getTagID() {
		return $this->_TagID;
	}
	
	/**
	 * Set $_TagID to $inTagID
	 * 
	 * @param integer $inTagID
	 * @return integer 
	 */
	function setTagID($inTagID) {
		if ( $inTagID !== $this->_TagID ) {
			$this->_TagID = $inTagID;
		}
		return $this;
	}
	
	/**
	 * Return the value of $_TagIDFlag
	 * 
	 * @return
	 */
	function getTagIDFlag() {
		return $this->_TagIDFlag;
	}
	
	/**
	 * Set $_TagIDFlag to $inFlag
	 * 
	 * @param boolean $inFlag
	 * @return boolean
	 */
	function setTagIDFlag($inFlag) {
		if ( $this->_TagIDFlag !== $inFlag ) {
			$this->_TagIDFlag = $inFlag;
		}
		return $this;
	}
}