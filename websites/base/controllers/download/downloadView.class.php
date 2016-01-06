<?php
/**
 * downloadView.class.php
 * 
 * downloadView class
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_base
 * @subpackage controllers
 * @category downloadView
 * @version $Rev: 11 $
 */


/**
 * downloadView class
 * 
 * Provides the "downloadView" page
 * 
 * @package websites_base
 * @subpackage controllers
 * @category downloadView
 */
class downloadView extends mvcView {

	/**
	 * @see mvcViewBase::setupInitialVars()
	 */
	function setupInitialVars() {
		parent::setupInitialVars();
		
		$this->setCacheLevelNone();
	}
	
	/**
	 * Shows the downloadView page
	 *
	 * @return void
	 */
	function showDownloadPage() {
		$this->getEngine()->assign('oObject', utilityOutputWrapper::wrap($this->getModel()->getFile()));
		
		$this->render($this->getTpl('download', '/download'));
	}
	
	/**
	 * Shows the 404 file not found page
	 * 
	 * @return void
	 */
	function show404Page() {
		header("HTTP/1.0 404 Not Found");
		
		$this->render($this->getTpl('404', '/download'));
	}
	
	/**
	 * Shows found but not available page
	 * 
	 * @return void
	 */
	function showNotAvailablePage() {
		$this->render($this->getTpl('notAvailable', '/download'));
	}
	
	/**
	 * Shows the download expired page
	 * 
	 * @return void
	 */
	function showDownloadExpiredPage() {
		$this->render($this->getTpl('expired', '/download'));
	}
}