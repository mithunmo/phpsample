<?php
/**
 * staticController
 *
 * Stored in staticController.class.php
 *
 * @author Dave Redfern
 * @copyright Mofilm Ltd. (c) 2009-2010
 * @package mofilm
 * @subpackage websites_base_controllers
 * @category staticController
 * @version 0.1
 */


/**
 * staticController
 *
 * staticController class
 *
 * @package mofilm
 * @subpackage websites_baseAdminSite_controllers
 * @category staticController
 */
class staticController extends mvcController {

	const ACTION_VIEW = 'view';
	
	/**
	 * @see mvcControllerBase::initialise()
	 */
	function initialise() {
		parent::initialise();
		
		$this->setDefaultAction(self::ACTION_VIEW);
	}

	/**
	 * @see mvcControllerBase::isValidAction()
	 */
	function isValidAction() {
		return true;
	}

	/**
	 * @see mvcControllerBase::launch()
	 */
	function launch() {
		$oView = new staticView($this);
		$oView->showPage();
	}



	/**
	 * Fetches the model
	 *
	 * @return staticModel
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
		$oModel = new staticModel();
		$oModel->setPageName($this->getAction());
		$oModel->setRequest($this->getRequest());
		$this->setModel($oModel);
	}
}