<?php
/**
 * userNotificationController
 *
 * Stored in userNotificationController.class.php
 * 
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category userNotificationController
 * @version $Rev: 624 $
 */


/**
 * userNotificationController
 *
 * userNotificationController class
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category userNotificationController
 */
class userNotificationController extends mvcDaoController {
	
	/**
	 * @see mvcControllerBase::initialise()
	 */
	function initialise() {
		parent::initialise();
		
		$this->setControllerView('userNotificationView');
	}
	
	/**
	 * @see mvcControllerBase::addInputFilters()
	 */
	function addInputFilters() {
		$this->getInputManager()->addFilter('PrimaryKey', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Id', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('SourceID', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Title', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Status', utilityInputFilter::filterString());
	}
	
	/**
	 * @see mvcControllerBase::addInputToModel()
	 *
	 * @param array $inData
	 * @param userNotificationModel $inModel
	 */
	function addInputToModel($inData, $inModel) {
		/**
		 * @todo set the primary key here
		 */
		//$inModel->setPrimaryKey($inData['PrimaryKey']);
		//$inModel->setId($inData['Id']); 
		$inModel->setSourceID($inData['SourceID']);
		$inModel->setTitle($inData['Title']);
		$inModel->setStatus($inData['Status']);
	}
	
	/**
	 * Builds the model
	 *
	 * @return void
	 */
	function buildModel() {
		$oModel = new userNotificationModel();
		$this->setModel($oModel);
	}
}