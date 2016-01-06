<?php
/**
 * eventadminView.class.php
 *
 * eventadminView class
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category eventadminView
 * @version $Rev: 11 $
 */


/**
 * eventadminView class
 *
 * Provides the "eventadminView" page
 *
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category eventadminView
 */
class eventadminView extends mvcView {

	/**
	 * @see mvcViewBase::setupInitialVars()
	 */
	function setupInitialVars() {
		parent::setupInitialVars();
		
		$this->getEngine()->assign('parentController', 'admin');
	}

	/**
	 * Shows the eventadminView page
	 *
	 * @return void
	 */
	function showEventadminPage() {
		$this->setCacheLevelNone();

		$this->render($this->getTpl('eventadmin'));
	}
}