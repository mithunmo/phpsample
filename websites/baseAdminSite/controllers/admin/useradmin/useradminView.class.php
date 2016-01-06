<?php
/**
 * useradminView.class.php
 *
 * useradminView.class
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category useradminView
 * @version $Rev: 11 $
 */


/**
 * useradminView.class
 *
 * Provides the "useradminView" page
 *
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category useradminView
 */
class useradminView extends mvcView {

	/**
	 * @see mvcViewBase::setupInitialVars()
	 */
	function setupInitialVars() {
		parent::setupInitialVars();

		$this->getEngine()->assign('parentController', 'admin');
	}

	/**
	 * Shows the useradminView page
	 *
	 * @return void
	 */
	function showUserAdminPage() {
		$this->setCacheLevelNone();

		$this->render($this->getTpl('useradmin'));
	}
}