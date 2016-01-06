<?php
/**
 * competitionsController
 *
 * Stored in competitionsController.class.php
 * 
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_mofilm.com
 * @subpackage controllers
 * @category competitionsController
 * @version $Rev: 11 $
 */


/**
 * competitionsController
 *
 * competitionsController class
 * 
 * @package websites_mofilm.com
 * @subpackage controllers
 * @category competitionsController
 */
class competitionsController extends mvcController {
	
	const ACTION_VIEW = 'view';
	
	/**
	 * @see mvcControllerBase::initialise()
	 */
	function initialise() {
		parent::initialise();
		
		$this->setDefaultAction(self::ACTION_VIEW);
		$this->setRequiresAuthentication(false);
		
		$this->getControllerActions()
			->addAction(self::ACTION_VIEW)
			->addAction(new mvcControllerAction('compID', '/\w+/i'));
	}
	
	/**
	 * @see mvcControllerBase::launch()
	 */
	function launch() {
		switch ( $this->getAction() ) {
			case self::ACTION_VIEW:
				$oView = new competitionsView($this);
				$oView->showAllCompetitionsPage();
			break;
			
			default:
				$oView = new competitionsView($this);
				$oView->showCompetitionPage();
		}
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
	 * @return competitionsModel
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
		$oModel = new competitionsModel();
		$this->setModel($oModel);
	}
}