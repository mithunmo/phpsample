<?php
/**
 * xmlController
 *
 * Stored in xmlController.class.php
 * 
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_base
 * @subpackage controllers
 * @category xmlController
 * @version $Rev: 11 $
 */


/**
 * xmlController
 *
 * xmlController class
 * 
 * @package websites_base
 * @subpackage controllers
 * @category xmlController
 */
class xmlController extends mvcController {
	
	const ACTION_VIEW = 'view';
	
	/**
	 * @see mvcControllerBase::initialise()
	 */
	function initialise() {
		parent::initialise();
		
		$this->setDefaultAction(self::ACTION_VIEW);
		$this->setRequiresAuthentication(false);
		$this
			->getControllerActions()
				->addAction(self::ACTION_VIEW)
				->addAction(new mvcControllerAction('MovieIDXml', '/^\d+\.xml$/'))
				->addAction(new mvcControllerAction('MovieID', '/^\d+$/'));
	}
	
	/**
	 * @see mvcControllerBase::launch()
	 */
	function launch() {
		$this->getModel()->setMovieID((int) $this->getActionFromRequest(false, 0));
		
		$oView = new xmlView($this);
		$oView->showXmlPage();
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
	 * @return xmlModel
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
		$oModel = new xmlModel();
		$this->setModel($oModel);
	}
}