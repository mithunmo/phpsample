<?php
/**
 * appMessageGroupsController
 *
 * Stored in appMessageGroupsController.class.php
 * 
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category appMessageGroupsController
 * @version $Rev: 11 $
 */


/**
 * appMessageGroupsController
 *
 * appMessageGroupsController class
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category appMessageGroupsController
 */
class appMessageGroupsController extends mvcDaoController {
	
	/**
	 * @see mvcControllerBase::initialise()
	 */
	function initialise() {
		parent::initialise();
		
		$this->setControllerView('appMessageGroupsView');
	}
	
	/**
	 * @see mvcControllerBase::addInputFilters()
	 */
	function addInputFilters() {
		$this->getInputManager()->addFilter('PrimaryKey', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('MessageType', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Description', utilityInputFilter::filterString());
	}
	
	/**
	 * @see mvcControllerBase::addInputToModel()
	 *
	 * @param array $inData
	 * @param appMessageGroupsModel $inModel
	 */
	function addInputToModel($inData, $inModel) {
		$inModel->setMessageGroupID($inData['PrimaryKey']);
		$inModel->setMessageType($inData['MessageType']);
		$inModel->setDescription($inData['Description']);
	}
	
	/**
	 * Builds the model
	 *
	 * @return void
	 */
	function buildModel() {
		$oModel = new appMessageGroupsModel();
		$this->setModel($oModel);
	}
}