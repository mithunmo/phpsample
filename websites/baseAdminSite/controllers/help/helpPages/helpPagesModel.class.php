<?php
/**
 * helpPagesModel.class.php
 *
 * helpPagesModel class
 *
 * @author Pavan Kumar
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category helpPagesModel
 * @version $Rev: 624 $
 */


/**
 * helpPagesModel class
 *
 * Provides the "helpPages" page
 *
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category helpPagesModel
 */
class helpPagesModel extends mofilmSystemHelpPages implements mvcDaoModelInterface {

	/**
	 * Stores $_SiteConfig
	 *
	 * @var mvcSiteConfig
	 * @access protected
	 */
	protected $_SiteConfig;
	
	/**
	 * Stores $_SearchResult
	 *
	 * @var mofilmSystemHelpPageSearchResult
	 * @access protected
	 */
	protected $_SearchResult;
	
	/**
	 * Stores $_TagID
	 * 
	 * @var Integer
	 * @access protected
	 */
	protected $_TagID;
	
	/**
	 * Stores $_TagName
	 * 
	 * @var String
	 * @access protected
	 */
	protected $_TagName;

	

	/**
	 * Returns a list of objects, optionally from $inOffset for $inLimit
	 *
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @param string $inDomainName
	 * @param string $inSearchValue
	 * @return array
	 */
	function getObjectList($inOffset = 0, $inLimit = 30, $inDomainName = null, $inSearchValue = null) {
		if ( strtolower($inSearchValue) == 'value to search for' || $inSearchValue == '' ) {
			$inSearchValue = null;
		}

		$oMofilmHelpSearch = new mofilmSystemHelpPageSearch();
		$oMofilmHelpSearch->setLimit($inLimit);
		$oMofilmHelpSearch->setOffset($inOffset);
		$oMofilmHelpSearch->setDomainName($inDomainName);
		if ( $inSearchValue !== null && strlen($inSearchValue) > 3 ) {
			$oMofilmHelpSearch->setKeywords($inSearchValue);
		}

		$this->setSearchResult($oMofilmHelpSearch->search());

		return $this->getSearchResult();
	}
	
	/**
	 * Attempts to load the help page based on domain and reference
	 * 
	 * This method will iterate over the siteconfig using the site hierarchy to
	 * locate the help page. If none is found, an empty help page is created.
	 * 
	 * @return mofilmSystemHelpPages
	 */
	function getHelpPage() {
		$oManager = mofilmSystemHelpPageManager::getInstance();
		$oManager->setLoadObjectDetails(true);

		try {
			$oSiteConfig = $this->getSiteConfig();

			do {
				$serverName = $oSiteConfig->getServerName();
				if ( !$serverName ) {
					$serverName = str_replace(
						system::getConfig()->getPathWebsites().DIRECTORY_SEPARATOR, '', $oSiteConfig->getSitePath()->getParamValue()
					);
				}

				systemLog::info('Searching for '.$this->getReference().' in site '.$serverName);
				$oObject = $oManager->getObjectByDomainAndReference(
					$serverName, $this->getReference()
				);

				if ( $oObject instanceof mofilmSystemHelpPages && $oObject->getID() > 0 ) {
					return $oObject;
				} else {
					$oSiteConfig = $oSiteConfig->getParentConfig();
				}
			} while ( !$oObject instanceof mofilmSystemHelpPages && $oSiteConfig instanceof mvcSiteConfig );

		} catch ( Exception $e ) {
			systemLog::error($e->getMessage());
		}

		return
			mofilmSystemHelpPageManager::newObject()
				->setDomainName($this->getSiteConfig()->getServerName())
				->setReference($this->getReference())
				->setTitle('Not Found')
				->setContent('<p>Sorry, we could not locate a help page at this time.</p>');
	}
	
	/**
	 * Returns a list of object based on selected tag
	 * 
	 * @return array
	 */
	function getHelpTagSelection() {
	    	$oMofilmHelpSearch = new mofilmSystemHelpPageSearch();
		$oMofilmHelpSearch->setTagIDFlag(true);
		$oMofilmHelpSearch->setTagID($this->getTagID());

		$this->setSearchResult($oMofilmHelpSearch->search());

		return $this->getSearchResult();
	}

	/**
	 * Returns the object primary key value
	 *
	 * @return string
	 */
	function getPrimaryKey() {
		return parent::getPrimaryKey();
	}

	/**
	 * Returns total object count for this table
	 *
	 * @return integer
	 */
	function getTotalObjects() {
		return $this->getSearchResult()->getTotalResults();
	}

	/**
	 * Returns the limit needed to get to the last page of results
	 *
	 * @param integer $inLimit
	 * @return integer
	 */
	function getLastPageOffset($inLimit) {
		$total = $this->getTotalObjects();

		if ( $inLimit > 0 ) {
			return $inLimit * floor($total / $inLimit);
		} else {
			return 0;
		}
	}

	/**
	 * Returns a new blank object
	 *
	 * @return systemDaoInterface
	 */
	function getNewObject() {
		$oObject = new mofilmSystemHelpPages();
		$oObject->setDomainName($this->getSiteConfig()->getServerName());
		if ( $this->getReference() ) {
			$oObject->setReference($this->getReference());
		}
		if ( $this->getTitle() ) {
			$oObject->setTitle($this->getTitle());
		}

		return $oObject;
	}

	/**
	 * Returns the corresponding route to the reference from the controllerMapper
	 *
	 * @param string $inReference
	 * @return mvcControllerMap
	 */
	function getMapPath($inReference) {
		return $this->getSiteConfig()->getControllerMapper()->getController($inReference);
	}

	/**
	 * Loads an existing object with $inPrimaryKey
	 *
	 * @param string $inPrimaryKey
	 * @return systemDaoInterface
	 */
	function getExistingObject($inPrimaryKey) {
		$this->setID($inPrimaryKey);
		$this->load();
		return $this;
	}

	/**
	 * Returns $_SearchResult
	 *
	 * @return mofilmSystemHelpPageSearchResult
	 */
	function getSearchResult() {
		return $this->_SearchResult;
	}

	/**
	 * Set $_SearchResult to $inSearchResult
	 *
	 * @param mofilmSystemHelpPageSearchResult $inSearchResult
	 * @return helpPageSearchResult
	 */
	function setSearchResult($inSearchResult) {
		if ( $inSearchResult !== $this->_SearchResult ) {
			$this->_SearchResult = $inSearchResult;
		}
		return $this;
	}

	/**
	 * Returns the value of $_SiteConfig
	 *
	 * @return mvcSiteConfig
	 */
	function getSiteConfig() {
		return $this->_SiteConfig;
	}

	/**
	 * Set $_SiteConfig to $inSiteConfig
	 *
	 * @param mvcSiteConfig $inSiteConfig
	 * @return helpPagesModel
	 */
	function setSiteConfig($inSiteConfig) {
		if ( $inSiteConfig !== $this->_SiteConfig ) {
			$this->_SiteConfig = $inSiteConfig;
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
	 * Return the value of $_TagName
	 * 
	 * @return 
	 */
	function getTagName() {
		return $this->_TagName;
	}
	
	/**
	 * Set $_TagName to $inTagName
	 * 
	 * @param string $inTagName
	 * @return string
	 */
	function setTagName($inTagName) {
		if ( $inTagName !== $this->_TagName ) {
		    $this->_TagName = $inTagName;
		}
		return $this;
	}
}