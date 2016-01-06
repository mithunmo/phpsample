<?php
/**
 * mvcErrorView.class.php
 * 
 * mvcErrorView class
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage mvc
 * @category mvcErrorView
 * @version $Rev: 650 $
 */


/**
 * mvcErrorView class
 * 
 * Handles routing views for error conditions. Each site should contain a
 * view folder called 'error' that then contains each of the following templates:
 * 
 * <ul>
 *   <li>invalidRequest</li>
 *   <li>invalidAction</li>
 *   <li>offline</li>
 *   <li>404</li>
 *   <li>500</li>
 *   <li>503</li>
 * </ul>
 * 
 * A template will be required for each output type e.g. html, xml, tpl etc.
 * The exception that caused the error condition is automatically added to the
 * view.
 * 
 * @package scorpio
 * @subpackage mvc
 * @category mvcErrorView
 */
class mvcErrorView extends mvcViewBase {
	
	/**
	 * @see mvcViewBase::__construct()
	 * @param mvcErrorController $inController
	 */
	function __construct(mvcErrorInterface $inController) {
		parent::__construct($inController);
		
		$this->getEngine()->assign('oException', utilityOutputWrapper::wrap($this->getController()->getException()));
		$this->getEngine()->assign('oController', utilityOutputWrapper::wrap($this->getController()));
		$this->getEngine()->assign('basePath', system::getConfig()->getBasePath()->getParamValue());
	}
	
	/**
	 * Displays invalid request page
	 * 
	 * @return void
	 */
	function invalidRequest() {
		$this->setCacheLevelNone();
		$this->addHeader(self::HEADER_STATUS, self::HEADER_STATUS_404);
		$this->render($this->getTpl('invalidRequest', '/error'));
	}

	/**
	 * Displays invalid action page
	 * 
	 * @return void
	 */
	function invalidAction() {
		$this->setCacheLevelNone();
		$this->addHeader(self::HEADER_STATUS, self::HEADER_STATUS_400);
		$this->render($this->getTpl('invalidAction', '/error'));
	}
	
	/**
	 * Displays a site offline notice
	 * 
	 * @return void
	 */
	function siteOffline() {
		$this->setCacheLevelNone();
		$this->addHeader(self::HEADER_STATUS, self::HEADER_STATUS_503);
		$this->render($this->getTpl('offline', '/error'));
	}
	
	/**
	 * Routes request to a 404 not found error page
	 * 
	 * @return void
	 */
	function requestNotFound() {
		$this->setCacheLevelNone();
		$this->addHeader(self::HEADER_STATUS, self::HEADER_STATUS_404);
		$this->render($this->getTpl('404', '/error'));
	}
	
	/**
	 * Routes request to a 500 internal server error page
	 * 
	 * @return void
	 */
	function internalServerError() {
		$this->setCacheLevelNone();
		$this->addHeader(self::HEADER_STATUS, self::HEADER_STATUS_500);
		$this->render($this->getTpl('500', '/error'));
	}
	
	/**
	 * Routes request to a 503 service not available error page
	 * 
	 * @return void
	 */
	function serverNotAvailable() {
		$this->setCacheLevelNone();
		$this->addHeader(self::HEADER_STATUS, self::HEADER_STATUS_503);
		$this->render($this->getTpl('503', '/error'));
	}
}