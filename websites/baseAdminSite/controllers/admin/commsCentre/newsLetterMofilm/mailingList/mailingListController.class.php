<?php
/**
 * mailingListController
 *
 * Stored in mailingListController.class.php
 * 
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category mailingListController
 * @version $Rev: 624 $
 */


/**
 * mailingListController
 *
 * mailingListController class
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category mailingListController
 */
class mailingListController extends mvcDaoController {
	
	/**
	 * @see mvcControllerBase::initialise()
	 */
	function initialise() {
		parent::initialise();
		
		$this->setControllerView('mailingListView');
	}
	
	/**
	 * @see mvcControllerBase::addInputFilters()
	 */
	function addInputFilters() {
		$this->getInputManager()->addFilter('PrimaryKey', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('ID', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('Name', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Description', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Folder', utilityInputFilter::filterString());
	}
	
	/**
	 * @see mvcControllerBase::addInputToModel()
	 *
	 * @param array $inData
	 * @param mailingListModel $inModel
	 */
	function addInputToModel($inData, $inModel) {
		//$inModel->setPrimaryKey($inData['PrimaryKey']);
		$inModel->setID($inData['PrimaryKey']);
		$inModel->setName($inData['Name']);
		$inModel->setDescription($inData['Description']);
		$inModel->setFolder($inData['Folder']);
	}
	
	/**
	 * Builds the model
	 *
	 * @return void
	 */
	function buildModel() {
		$oModel = new mailingListModel();
		$this->setModel($oModel);
	}
}