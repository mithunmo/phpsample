<?php
/**
 * reportCentreView.class.php
 * 
 * reportCentreView class
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category reportCentreView
 * @version $Rev: 11 $
 */


/**
 * reportCentreView class
 * 
 * Provides the "reportCentreView" page
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category reportCentreView
 */
class reportCentreView extends mvcView {

	/**
	 * @see mvcViewBase::setupInitialVars()
	 */
	function setupInitialVars() {
		parent::setupInitialVars();

		/*
		 * Add any further custom setup for the view that is needed on every request
		 */
		$this->getEngine()->assign('parentController', 'admin');
	}
	
	/**
	 * Shows the reportCentreView page
	 *
	 * @return void
	 */
	function showReportCentrePage() {
		$this->setCacheLevelNone();
		
		$this->render($this->getTpl('reportCentre'));
	}
}