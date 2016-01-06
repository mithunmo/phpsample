<?php
/**
 * movieadminView.class.php
 *
 * movieadminView class
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category movieadminView
 * @version $Rev: 11 $
 */


/**
 * movieadminView class
 *
 * Provides the "movieadminView" page
 *
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category movieadminView
 */
class movieadminView extends mvcView {

	/**
	 * @see mvcViewBase::setupInitialVars()
	 */
	function setupInitialVars() {
		parent::setupInitialVars();

		$this->getEngine()->assign('parentController', 'admin');
	}

	/**
	 * Shows the movieadminView page
	 *
	 * @return void
	 */
	function showMovieadminPage() {
		$this->setCacheLevelNone();

		$this->render($this->getTpl('movieadmin'));
	}
}