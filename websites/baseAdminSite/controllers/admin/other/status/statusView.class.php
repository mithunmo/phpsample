<?php
/**
 * statusView.class.php
 * 
 * statusView class
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category statusView
 * @version $Rev: 11 $
 */


/**
 * statusView class
 * 
 * Provides the "statusView" page
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category statusView
 */
class statusView extends mvcView {

	/**
	 * @see mvcViewBase::setupInitialVars()
	 */
	function setupInitialVars() {
		parent::setupInitialVars();

		/*
		 * Add any further custom setup for the view that is needed on every request
		 */
	}
	
	/**
	 * Shows the statusView page
	 *
	 * @return void
	 */
	function showStatusPage() {
		$this->setCacheLevelNone();
		
		$this->getEngine()->assign('oModel', utilityOutputWrapper::wrap($this->getModel()));
		
		$this->getEngine()->assign($this->getModel()->getStats());
		
		$sections = $this->getEngine()->getTemplateVar('sections');
		$server_status = $this->getEngine()->getTemplateVar('server_status');
		
		$this->getEngine()->assign('tables', 1);
		$this->getEngine()->assign('rows_per_table', ceil(count($sections['com']['vars'])/$this->getEngine()->getTemplateVar('tables')));
		$this->getEngine()->assign('perc_factor', 100 / ($server_status['Questions'] - $server_status['Connections']));
		
		$this->render($this->getTpl('status'));
	}
}