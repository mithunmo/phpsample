<?php
/**
 * mofilmErrorController.class.php
 *
 * mofilmErrorController class
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package baseAdminSite
 * @subpackage websites_baseAdminSite_libraries
 * @category mofilmErrorController
 * @version $Rev: 11 $
 */


/**
 * mofilmErrorController
 *
 * A custom error controller for handling errors in the admin system.
 *
 * @package baseAdminSite
 * @subpackage websites_baseAdminSite_libraries
 * @category mofilmErrorController
 */
class mofilmErrorController extends mvcController implements mvcErrorInterface {
	
	/**
	 * Stores $_Exception
	 *
	 * @var Exception
	 * @access protected
	 */
	protected $_Exception = null;



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
		return false;
	}

	/**
	 * @see mvcControllerBase::launch()
	 */
	function launch() {
		$oView = new mvcErrorView($this);
		$oView->getEngine()->assign('oUser', utilityOutputWrapper::wrap($this->getRequest()->getSession()->getUser()));
		$oView->getEngine()->assign('mofilmWwwUri', system::getConfig()->getParam('mofilm', 'wwwMofilmUri', 'http://www.mofilm.com')->getParamValue());
		$oView->getEngine()->assign('mofilmMyUri', system::getConfig()->getParam('mofilm', 'myMofilmUri', 'http://mofilm.com')->getParamValue());
		
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