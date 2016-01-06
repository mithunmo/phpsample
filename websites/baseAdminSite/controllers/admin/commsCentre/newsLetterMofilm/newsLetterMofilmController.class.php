<?php
/**
 * newsLetterMofilmController
 *
 * Stored in newsLetterMofilmController.class.php
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category newsLetterMofilmController
 * @version $Rev: 623 $
 */


/**
 * newsLetterMofilmController
 *
 * newsLetterMofilmController class
 *
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category newsLetterMofilmController
 */
class newsLetterMofilmController extends mvcController {

	const ACTION_VIEW = 'view';

	/**
	 * @see mvcControllerBase::initialise()
	 */
	function initialise() {
		parent::initialise();

		$this->setDefaultAction(self::ACTION_VIEW);
		$this->getControllerActions()->addAction(self::ACTION_VIEW);
	}

	/**
	 * @see mvcControllerBase::launch()
	 */
	function launch() {
		$oView = new newsLetterMofilmView($this);
		$oView->showNewsLetterMofilmPage();
	}


	/**
	 * @see mvcControllerBase::addInputFilters()
	 */
	function addInputFilters() {

	}

	/**
	 * @see mvcControllerBase::addInputToModel()
	 */
	function addInputToModel($inData, $inModel) {

	}

	/**
	 * Fetches the model
	 *
	 * @return newsLetterMofilmModel
	 */
	function getModel() {
		if ( !parent::getModel() ) {
			$this->buildModel();
		}
		return parent::getModel();
	}

	/**
	 * Builds the model
	 *
	 * @return void
	 */
	function buildModel() {
		$oModel = new newsLetterMofilmModel();
		$this->setModel($oModel);
	}
}