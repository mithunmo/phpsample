<?php
/**
 * corporateController
 *
 * Stored in corporateController.class.php
 * 
 * @author Poulami Chakraborty
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category corporateController
 * @version $Rev: 624 $
 */


/**
 * corporateController
 *
 * corporateController class
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category corporateController
 */
class corporateController extends mvcDaoController {
	
	/**
	 * @see mvcControllerBase::initialise()
	 */
	function initialise() {
		parent::initialise();
		
		$this->setControllerView('corporateView');
	}
	  function launch() {
		 parent::launch();
	}
	/**
	 * @see mvcControllerBase::addInputFilters()
	 */
	function addInputFilters() {
		$this->getInputManager()->addFilter('PrimaryKey', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('ID', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Name', utilityInputFilter::filterString());
                
	}
	
	/**
	 * @see mvcControllerBase::addInputToModel()
	 *
	 * @param array $inData
	 * @param corporateModel $inModel
	 */
	function addInputToModel($inData, $inModel) {
		/**
		 * @todo set the primary key here
		 */
		//$inModel->setPrimaryKey($inData['PrimaryKey']);
		$inModel->setID($inData['ID']);
		$inModel->setName($inData['Name']);
	}
	
	/**
	 * Builds the model
	 *
	 * @return void
	 */
	function buildModel() {
		$oModel = new corporateModel();
		$this->setModel($oModel);
	}
        
        function actionView() {
		$this->getInputManager()->setLookupGlobals(utilityInputManager::LOOKUPGLOBALS_GET);
		$this->getInputManager()->addFilter('EventID', utilityInputFilter::filterInt());
		$data = $this->getInputManager()->doFilter();
		
		$this->setSearchOptionFromRequestData($data, 'EventID');
		
		parent::actionView();
	}
}