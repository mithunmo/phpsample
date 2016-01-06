<?php
/**
 * termsController
 *
 * Stored in termsController.class.php
 * 
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_mofilm.com
 * @subpackage controllers
 * @category termsController
 * @version $Rev: 11 $
 */


/**
 * termsController
 *
 * termsController class
 * 
 * @package websites_mofilm.com
 * @subpackage controllers
 * @category termsController
 */
class termsController extends mvcController {
	
	const ACTION_VIEW = 'view';
	const ACTION_EVENT = 'event';
	const ACTION_SOURCE = 'source';
	
	/**
	 * @see mvcControllerBase::initialise()
	 */
	function initialise() {
		parent::initialise();
		
		$this->setDefaultAction(self::ACTION_VIEW);
		$this->setRequiresAuthentication(false);
		
		$this->getControllerActions()
			->addAction(self::ACTION_VIEW)
			->addAction(self::ACTION_EVENT)
			->addAction(self::ACTION_SOURCE)
			->addAction(new mvcControllerAction('termsID', '/^\d+$/'));
	}
	
	/**
	 * @see mvcControllerBase::launch()
	 */
	function launch() {
		switch ( $this->getAction() ) {
			case self::ACTION_SOURCE:
				$this->sourceAction();
			break;

			case self::ACTION_EVENT:
				$this->eventAction();
			break;

			case is_numeric($this->getAction()):
				$this->termsAction();
			break;

			case self::ACTION_VIEW:
			default:
				$this->redirect('/');
		}
	}

	/**
	 * Handles event term requests
	 *
	 * @return void
	 */
	protected function eventAction() {
		$eventID = $this->getActionFromRequest(false, 1);
		if ( is_numeric($eventID) && $eventID > 0 ) {
			$this->getModel()->setEventID($eventID);

			$oView = new termsView($this);
			$oView->showEventTermsPage();

		} else {
			throw new mvcDistributorInvalidActionException($this->getAction(), $eventID);
		}
	}

	/**
	 * Handles source term requests
	 *
	 * @return void
	 */
	protected function sourceAction() {
		$sourceID = $this->getActionFromRequest(false, 1);
		if ( is_numeric($sourceID) && $sourceID > 0 ) {
			$this->getModel()->setSourceID($sourceID);

			$oView = new termsView($this);
			$oView->showSourceTermsPage();

		} else {
			throw new mvcDistributorInvalidActionException($this->getAction(), $sourceID);
		}
	}

	/**
	 * Handles straight terms requests
	 *
	 * @return void
	 */
	protected function termsAction() {
		$this->getModel()->setTermsID($this->getAction());

		$oView = new termsView($this);
		$oView->showTermsPage();
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
	 * @return termsModel
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
		$oModel = new termsModel();
		$this->setModel($oModel);
	}
}