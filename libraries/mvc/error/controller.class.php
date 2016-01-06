<?php
/**
 * mvcErrorController.class.php
 * 
 * mvcErrorController class
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage mvc
 * @category mvcErrorController
 * @version $Rev: 707 $
 */


/**
 * mvcErrorController class
 * 
 * The base error handler controller, used internally by {@link mvcDistributor}
 * to handle and route display of error pages e.g. request / action not valid
 * and other unhandled error conditions.
 * 
 * Please note: this class does not use the mvcController defined in the site,
 * but extends the mvcControllerBase. Therefore any authentication or view
 * assignments will not be used.
 * 
 * @package scorpio
 * @subpackage mvc
 * @category mvcErrorController
 */
class mvcErrorController extends mvcControllerBase implements mvcErrorInterface {
	
	/**
	 * Stores $_Exception
	 *
	 * @var Exception
	 * @access protected
	 */
	protected $_Exception = null;
	
	
	
	/**
	 * @see mvcControllerBase::authorise() 
	 */
	function authorise() {
		
	}

	/**
	 * @see mvcControllerBase::initialise()
	 */
	function initialise() {
		
	}

	/**
	 * @see mvcControllerBase::isAuthorised()
	 */
	function isAuthorised() {
		return true;
	}
	
	/**
	 * Default to always deny as this is an error condition
	 * 
	 * @see mvcControllerBase::hasAuthority()
	 */
	function hasAuthority($inActivity) {
		return false;
	}

	/**
	 * @see mvcControllerBase::isValidAction()
	 */
	function isValidAction() {
		return true;
	}

	/**
	 * @see mvcControllerBase::isValidView()
	 * @param $inView
	 */
	function isValidView($inView) {
		return true;
	}

	/**
	 * @see mvcControllerBase::launch()
	 */
	function launch() {
		$oView = new mvcErrorView($this);
		switch ( $this->getAction() ) {
			case 'mvcMapNoPathComponentsToSearch':
			case 'mvcDistributorInvalidRequestException':
				$oView->invalidRequest();
			break;
			
			case 'mvcDistributorInvalidActionException':
				$oView->invalidAction();
			break;
			
			case 'mvcDistributorOfflineException':
			case 'mvcDistributorDatabaseOfflineException':	
				$oView->siteOffline();
			break;
			
			default:
				$oView->internalServerError();
			break;
		}
	}
	
	

	/**
	 * Returns $_Exception
	 *
	 * @return Exception
	 * @access public
	 */
	function getException() {
		return $this->_Exception;
	}
	
	/**
	 * Set $_Exception to $inException
	 *
	 * @param Exception $inException
	 * @return mvcErrorController
	 * @access public
	 */
	function setException(Exception $inException) {
		if ( $this->_Exception !== $inException ) {
			$this->_Exception = $inException;
			$this->setModified();
		}
		return $this;
	}
}