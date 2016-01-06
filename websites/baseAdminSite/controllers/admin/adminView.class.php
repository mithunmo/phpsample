<?php
/**
 * adminView.class.php
 *
 * adminView class
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category adminView
 * @version $Rev: 11 $
 */


/**
 * adminView class
 *
 * Provides the "adminView" page
 *
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category adminView
 */
class adminView extends mvcView {

	/**
	 * @see mvcViewBase::setupInitialVars()
	 */
	function setupInitialVars() {
		parent::setupInitialVars();

		$this->getEngine()->assign('parentController', 'home');
	}

	/**
	 * Shows the adminView page
	 *
	 * @return void
	 */
	function showAdminPage() {
		$this->setCacheLevelNone();

		$this->render($this->getTpl('admin'));
	}
}