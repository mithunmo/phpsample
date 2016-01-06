<?php
/**
 * linkController
 *
 * Stored in linkController.class.php
 * 
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_base
 * @subpackage controllers
 * @category linkController
 * @version $Rev: 11 $
 */


/**
 * linkController
 *
 * linkController class
 * 
 * @package websites_base
 * @subpackage controllers
 * @category linkController
 */
class linkController extends mvcController {
	
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
			->addAction(new mvcControllerAction('linkID', '/^\d+$/'));
	}
	
	/**
	 * @see mvcControllerBase::launch()
	 */
	function launch() {
		$oView = new linkView($this);
		$oView->showLinkPage();
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
	 * @return linkModel
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
		$oModel = new linkModel();
		$this->setModel($oModel);
	}
}