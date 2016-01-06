<?php
/**
 * mvcDistributor.class.php
 * 
 * mvcDistributor class
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage mvc
 * @category mvcDistributorBase
 * @version $Rev: 760 $
 */


/**
 * mvcDistributor
 * 
 * This is the main implementation of the distributor. It handles routing
 * requests into the MVC system. See {@link mvcDistributorBase} for further
 * details of the dispatch process.
 * 
 * mvcDistributor supports several states that can be set before execution.
 * These states are for handling events such as database offline, or a site
 * being marked in-active. States are set via global constants. The current
 * supported states are:
 * 
 * <ul>
 *   <li>SYSTEM_OFFLINE - system not available</li>
 *   <li>SYSTEM_DATABASE_OFFLINE - database not available</li>
 * </ul>
 * 
 * SYSTEM_OFFLINE can be defined by checking for the presence of an offline
 * file in the web root (for example). Databases can be checked by
 * implementing a DB ping or some other DB property.
 * 
 * Example usage:
 * <code>
 * $oRequest = mvcRequest::getInstance();
 * $oDistributor = new mvcDistributor($oRequest);
 * $oDistributor->initialise();
 * $oDistributor->dispatch();
 * </code>
 * 
 * @package scorpio
 * @subpackage mvc
 * @category mvcDistributor
 */
class mvcDistributor extends mvcDistributorBase {
	
	/**
	 * @see mvcDistributorBase::dispatch()
	 */
	function dispatch() {
		try {
			if ( (defined('SYSTEM_OFFLINE') && SYSTEM_OFFLINE) || !$this->getSiteConfig()->isActive() ) {
				throw new mvcDistributorOfflineException();
			}
			if ( defined('SYSTEM_DATABASE_OFFLINE') && SYSTEM_DATABASE_OFFLINE ) {
				throw new mvcDistributorDatabaseOfflineException();
			}
			
			$this->getPluginSet()->executePreDispatch();
			
			$this->resolveController();
			$oController = $this->loadController();
			
			if ( $oController instanceof mvcControllerBase ) {
				if ( !$oController->isValidAction() ) {
					throw new mvcDistributorInvalidActionException($this->getRequest()->getRequestUri(), $oController->getAction());
				}
				
				if ( $oController->isAuthorised() ) {
					$oController->launch();
				} else {
					$oController->authorise();
				}
			} else {
				throw new mvcDistributorInvalidRequestException($this->getRequest()->getRequestUri());
			}
			
			$this->getPluginSet()->executePostDispatch();
			
		} catch ( Exception $e ) {
			/*
			 * Clear any existing buffers to prevent error injection
			 */
			ob_end_clean();
			
			systemLog::critical(
				'Exception thrown at line '.$e->getLine().' in file '.
				$e->getFile().' with message: '.($e->getMessage() ? $e->getMessage() : ' No message in Exception')
			);
			$controller = $this->getDistributorErrorController();
			
			$oErrorController = new $controller($this->getRequest(), $this->getResponse());
			if ( $oErrorController instanceof mvcErrorInterface && $oErrorController instanceof mvcControllerBase ) {
				$oErrorController->setAction(get_class($e));
				$oErrorController->setException($e);
				$oErrorController->launch();
			} else {
				throw $e;
			}
		}
		
		$this->getPluginSet()->executeOnShutdown();
	}
}