<?php
/**
 * paymentTypesController
 *
 * Stored in paymentTypesController.class.php
 * 
 * @author Pavan Kumar
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category paymentTypesController
 * @version $Rev: 624 $
 */


/**
 * paymentTypesController
 *
 * paymentTypesController class
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category paymentTypesController
 */
class paymentTypesController extends mvcDaoController {
	
	/**
	 * @see mvcControllerBase::initialise()
	 */
	function initialise() {
		parent::initialise();
		
		$this->setControllerView('paymentTypesView');
	}
	
	/**
	 * @see mvcControllerBase::addInputFilters()
	 */
	function addInputFilters() {
		$this->getInputManager()->addFilter('PrimaryKey', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('ID', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Name', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Description', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Moderation', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('ModerationLimit', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('LastModified', utilityInputFilter::filterString());
	}
	
	/**
	 * @see mvcControllerBase::addInputToModel()
	 *
	 * @param array $inData
	 * @param paymentTypesModel $inModel
	 */
	function addInputToModel($inData, $inModel) {
		/**
		 * @todo set the primary key here
		 */
		//$inModel->setPrimaryKey($inData['PrimaryKey']);
		$inModel->setID($inData['ID']);
		$inModel->setName($inData['Name']);
		$inModel->setDescription($inData['Description']);
		$inModel->setModeration($inData['Moderation']);
		$inModel->setModerationLimit($inData['ModerationLimit']);
		$inModel->setLastModified($inData['LastModified']);
	}
	
	/**
	 * Builds the model
	 *
	 * @return void
	 */
	function buildModel() {
		$oModel = new paymentTypesModel();
		$this->setModel($oModel);
	}
}