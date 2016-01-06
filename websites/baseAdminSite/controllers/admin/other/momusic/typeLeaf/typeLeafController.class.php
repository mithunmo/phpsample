<?php
/**
 * typeLeafController
 *
 * Stored in typeLeafController.class.php
 * 
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category typeLeafController
 * @version $Rev: 624 $
 */


/**
 * typeLeafController
 *
 * typeLeafController class
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category typeLeafController
 */
class typeLeafController extends mvcDaoController {
	
	
	const ACTION_GET_ROOT = 'root';
	const ACTION_GET_TYPE = 'type';

	/**
	 * @see mvcControllerBase::initialise()
	 */
	function initialise() {
		parent::initialise();
		
		$this->getControllerActions()->addAction(self::ACTION_GET_ROOT);
		$this->setControllerView('typeLeafView');
		

	}
	
	
	function launch() {
		if ( $this->getAction() == self::ACTION_GET_ROOT ) {
			$this->getRootList();
		} else if ( $this->getAction() == self::ACTION_GET_TYPE ) {
			$this->getTypeList();
		} else {
			parent::launch();
		}
	}
	
	
	function getRootList() {
		$this->getInputManager()->setLookupGlobals(utilityInputManager::LOOKUPGLOBALS_GET);
		$data = $this->getInputManager()->doFilter();			
		$this->addInputToModel($data, $this->getModel());
				
		$oView = new typeLeafView($this);
		$oView->showgetRootList($data["RootID"]);
	}
	
	
	function getTypeList() {
		$this->getInputManager()->setLookupGlobals(utilityInputManager::LOOKUPGLOBALS_GET);
		$data = $this->getInputManager()->doFilter();			
		$this->addInputToModel($data, $this->getModel());		
		$oView = new typeLeafView($this);
		$oView->showgetTypeList($data["Type"]);		
	}
	
	
	/**
	 * @see mvcControllerBase::addInputFilters()
	 */
	function addInputFilters() {
		$this->getInputManager()->addFilter('PrimaryKey', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('ID', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Name', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('RootID', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('ParentID', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Type', utilityInputFilter::filterString());
	}
	
	/**
	 * @see mvcControllerBase::addInputToModel()
	 *
	 * @param array $inData
	 * @param typeLeafModel $inModel
	 */
	function addInputToModel($inData, $inModel) {
		/**
		 * @todo set the primary key here
		 */
		//$inModel->setPrimaryKey($inData['PrimaryKey']);
				
		$inModel->setID($inData['PrimaryKey']);
		$inModel->setName($inData['Name']);
		$inModel->setRootID($inData['RootID']);
		$inModel->setParentID($inData['ParentID']);
		$inModel->setType($inData['Type']);
	}
	
	/*
	function buildModel() {
		$oModel = new typeLeafModel();
		$this->setModel($oModel);
	}
	*/
	
	/**
	 * Fetches the model
	 *
	 * @return uploadModel
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
		$oModel = new typeLeafModel();
		//$oModel->setRequest($this->getRequest());
		$this->setModel($oModel);
	}
	
	
}