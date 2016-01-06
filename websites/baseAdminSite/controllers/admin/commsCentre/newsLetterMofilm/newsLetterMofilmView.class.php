<?php
/**
 * newsLetterMofilmView.class.php
 *
 * newsLetterMofilmView class
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category newsLetterMofilmView
 * @version $Rev: 634 $
 */


/**
 * newsLetterMofilmView class
 *
 * Provides the "newsLetterMofilmView" page
 *
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category newsLetterMofilmView
 */
class newsLetterMofilmView extends mvcView {

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
	 * Shows the newsLetterMofilmView page
	 *
	 * @return void
	 */
	function showNewsLetterMofilmPage() {
		$this->setCacheLevelNone();

		$this->render($this->getTpl('newsLetterMofilm'));
	}
}