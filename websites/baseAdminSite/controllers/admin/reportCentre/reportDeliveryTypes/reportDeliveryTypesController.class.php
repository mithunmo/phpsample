<?php
/**
 * reportDeliveryTypesController
 *
 * Stored in reportDeliveryTypesController.class.php
 * 
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category reportDeliveryTypesController
 * @version $Rev: 11 $
 */


/**
 * reportDeliveryTypesController
 *
 * reportDeliveryTypesController class
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category reportDeliveryTypesController
 */
class reportDeliveryTypesController extends mvcDaoController {
	
	/**
	 * @see mvcControllerBase::initialise()
	 */
	function initialise() {
		parent::initialise();
		
		$this->setControllerView('reportDeliveryTypesView');
	}
	
	/**
	 * @see mvcControllerBase::addInputFilters()
	 */
	function addInputFilters() {
		$this->getInputManager()->addFilter('PrimaryKey', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('DeliveryTypeID', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('TypeName', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('SendToInbox', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('SendToUserEmail', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('SendToGroup', utilityInputFilter::filterInt());
	}
	
	/**
	 * @see mvcControllerBase::addInputToModel()
	 *
	 * @param array $inData
	 * @param reportDeliveryTypesModel $inModel
	 */
	function addInputToModel($inData, $inModel) {
		$inModel->setDeliveryTypeID($inData['PrimaryKey']);
		$inModel->setTypeName($inData['TypeName']);
		$inModel->setSendToInbox($inData['SendToInbox']);
		$inModel->setSendToUserEmail($inData['SendToUserEmail']);
		$inModel->setSendToGroup($inData['SendToGroup']);
	}
	
	/**
	 * Builds the model
	 *
	 * @return void
	 */
	function buildModel() {
		$oModel = new reportDeliveryTypesModel();
		$this->setModel($oModel);
	}
}