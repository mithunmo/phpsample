<?php
/**
 * otherView.class.php
 *
 * otherView class
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category otherView
 * @version $Rev: 11 $
 */


/**
 * otherView class
 *
 * Provides the "otherView" page
 *
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category otherView
 */
class otherView extends mvcView {

	/**
	 * @see mvcViewBase::setupInitialVars()
	 */
	function setupInitialVars() {
		parent::setupInitialVars();

		$this->getEngine()->assign('parentController', 'admin');
	}

	/**
	 * Shows the otherView page
	 *
	 * @return void
	 */
	function showOtherPage() {
		$this->setCacheLevelNone();

		$this->render($this->getTpl('other'));
	}
}