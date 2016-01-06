<?php
/**
 * gatewaysController
 *
 * Stored in gatewaysController.class.php
 * 
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category gatewaysController
 * @version $Rev: 11 $
 */


/**
 * gatewaysController
 *
 * gatewaysController class
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category gatewaysController
 */
class gatewaysController extends mvcDaoController {
	
	/**
	 * @see mvcControllerBase::initialise()
	 */
	function initialise() {
		parent::initialise();
		
		$this->setControllerView('gatewaysView');
	}
	
	/**
	 * @see mvcControllerBase::addInputFilters()
	 */
	function addInputFilters() {
		$this->getInputManager()->addFilter('PrimaryKey', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('Active', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('Description', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('ClassName', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('TransportClass', utilityInputFilter::filterString());
	}
	
	/**
	 * @see mvcControllerBase::addInputToModel()
	 *
	 * @param array $inData
	 * @param gatewaysModel $inModel
	 */
	function addInputToModel($inData, $inModel) {
		$inModel->setGatewayID($inData['PrimaryKey']);
		$inModel->setActive($inData['Active']);
		$inModel->setDescription($inData['Description']);
		$inModel->setClassName($inData['ClassName']);
		$inModel->setTransportClass($inData['TransportClass']);
		$inModel->setMarkForDeletion($inData['MarkForDeletion']);
	}
	
	/**
	 * Builds the model
	 *
	 * @return void
	 */
	function buildModel() {
		$oModel = new gatewaysModel();
		$this->setModel($oModel);
	}
}