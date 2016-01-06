<?php
/**
 * staticModel.class.php
 *
 * staticModel class
 *
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2009
 * @package scorpio
 * @subpackage websites_base_controllers
 * @category staticModel
 */


/**
 * staticModel class
 *
 * Provides the "static" page
 *
 * @package scorpio
 * @subpackage websites_base_controllers
 * @category staticModel
 */
class staticModel extends mvcModelBase {

	/**
	 * Stores $_PageName
	 *
	 * @var string
	 * @access protected
	 */
	protected $_PageName;
	
	/**
	 * Stores $_Request
	 *
	 * @var mvcRequest
	 * @access protected
	 */
	protected $_Request;
	
	
	
	/**
	 * @see mvcModelBase::__construct()
	 */
	function __construct() {
		parent::__construct();

		$this->_PageName = null;
		$this->_Request = null;
	}



	/**
	 * Returns $_PageName
	 *
	 * @return string
	 */
	function getPageName() {
		return $this->_PageName;
	}

	/**
	 * Set $_PageName to $inPageName
	 *
	 * @param string $inPageName
	 * @return staticModel
	 */
	function setPageName($inPageName) {
		if ( $inPageName !== $this->_PageName ) {
			$this->_PageName = $inPageName;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_Request
	 *
	 * @return mvcRequest
	 */
	function getRequest() {
		return $this->_Request;
	}
	
	/**
	 * Set $_Request to $inRequest
	 *
	 * @param mvcRequest $inRequest
	 * @return staticModel
	 */
	function setRequest(mvcRequest $inRequest) {
		if ( $inRequest !== $this->_Request ) {
			$this->_Request = $inRequest;
			$this->setModified();
		}
		return $this;
	}

	

	/**
	 * Returns a list of all static pages for the current domain
	 *
	 * @return array
	 */
	function getStaticPages() {
		/*
		 * First check file system, then go to the DB
		 */
		$pages = array();
		$path = 
			$this->getRequest()->getDistributor()->getSiteConfig()->getSitePath().
			system::getDirSeparator().
			$this->getRequest()->getDistributor()->getDistributorViewsFolder().
			system::getDirSeparator().'static'.system::getDirSeparator();
		
		$outputType = $this->getRequest()->getOutputType();
		$search = $path.'*.'.$outputType.'.tpl';
		$files = glob($search);
		if ( count($files) > 0 ) {
			foreach ( $files as $filename ) {
				$pageName = basename($filename, ".$outputType.tpl");
				$pages[$pageName] = utilityStringFunction::convertCapitalizedString(ucwords($pageName));
			}
		}
		return $pages;
	}
}