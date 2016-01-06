<?php
/**
 * distributionController
 *
 * Stored in distributionController.class.php
 * 
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category distributionController
 * @version $Rev: 624 $
 */


/**
 * distributionController
 *
 * distributionController class
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category distributionController
 */
class distributionController extends mvcDaoController {
	
	/**
	 * @see mvcControllerBase::initialise()
	 */
	function initialise() {
		parent::initialise();
		
		$this->setControllerView('distributionView');
	}
	
	/**
	 * @see mvcControllerBase::addInputFilters()
	 */
	function addInputFilters() {
		$this->getInputManager()->addFilter('PrimaryKey', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('ID', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Name', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Class', utilityInputFilter::filterString());
	}
	
	/**
	 * @see mvcControllerBase::addInputToModel()
	 *
	 * @param array $inData
	 * @param distributionModel $inModel
	 */
	function addInputToModel($inData, $inModel) {
		/**
		 * @todo set the primary key here
		 */
		//$inModel->setPrimaryKey($inData['PrimaryKey']);
		//$inModel->setID($inData['ID']);
		$inModel->setName($inData['Name']);
		$inModel->setClass($inData['Class']);
	}
	
	/**
	 * Builds the model
	 *
	 * @return void
	 */
	function buildModel() {
		$oModel = new distributionModel();
		$this->setModel($oModel);
	}
}