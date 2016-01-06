<?php
/**
 * commsCentreView.class.php
 *
 * commsCentreView class
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category commsCentreView
 * @version $Rev: 11 $
 */


/**
 * commsCentreView class
 *
 * Provides the "commsCentreView" page
 *
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category commsCentreView
 */
class commsCentreView extends mvcView {

	/**
	 * @see mvcViewBase::setupInitialVars()
	 */
	function setupInitialVars() {
		parent::setupInitialVars();

		$this->getEngine()->assign('parentController', 'admin');
	}

	/**
	 * Shows the commsCentreView page
	 *
	 * @return void
	 */
	function showCommsCentrePage() {
		$this->setCacheLevelNone();

		$this->render($this->getTpl('commsCentre'));
	}
}