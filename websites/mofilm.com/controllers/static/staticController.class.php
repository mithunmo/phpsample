<?php
/**
 * staticController
 *
 * Stored in staticController.class.php
 *
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2009
 * @package scorpio
 * @subpackage websites_base_controllers
 * @category staticController
 * @version 0.1
 */


/**
 * staticController
 *
 * staticController class
 *
 * @package scorpio
 * @subpackage websites_base_controllers
 * @category staticController
 */
class staticController extends mvcController {

	const ACTION_VIEW = 'view';

	/**
	 * @see mvcControllerBase::initialise()
	 */
	function initialise() {
		parent::initialise();
		$this->setRequiresAuthentication(false);
		$this->setDefaultAction(self::ACTION_VIEW);
	}

	/**
	 * @see mvcControllerBase::isValidAction()
	 *
	 * @return boolean
	 */
	function isValidAction() {
		return true;
	}

	/**
	 * @see mvcControllerBase::launch()
	 *
	 * Overridden so that we can prevent the default listing action of
	 * the static pages and redirect to the main business / about pages.
	 */
	function launch() {
		if ( $this->getAction() == self::ACTION_VIEW ) {
			if ( stripos($this->getRequest()->getRequestUri(), 'business') !== false ) {
				$this->setAction('business');
			} else {
				$this->setAction('about');
			}
		}

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