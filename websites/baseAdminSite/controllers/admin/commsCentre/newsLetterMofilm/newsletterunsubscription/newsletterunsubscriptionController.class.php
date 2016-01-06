<?php
/**
 * newsletterunsubscriptionController
 *
 * Stored in newsletterunsubscriptionController.class.php
 * 
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category newsletterunsubscriptionController
 * @version $Rev: 624 $
 */


/**
 * newsletterunsubscriptionController
 *
 * newsletterunsubscriptionController class
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category newsletterunsubscriptionController
 */
class newsletterunsubscriptionController extends mvcDaoController {
	
	/**
	 * @see mvcControllerBase::initialise()
	 */
	function initialise() {
		parent::initialise();
		
		$this->setControllerView('newsletterunsubscriptionView');
	}

	/**
	 * @see mvcControllerBase::lanuch()
	 */
	function launch() {
		$this->getMenuItems()->reset();
		parent::launch();
	}

	
	/**
	 * @see mvcControllerBase::addInputFilters()
	 */
	function addInputFilters() {
		$this->getInputManager()->addFilter('PrimaryKey', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('ID', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('UserID', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('EmailID', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('NewsletterID', utilityInputFilter::filterString());
	}
	
	/**
	 * @see mvcControllerBase::addInputToModel()
	 *
	 * @param array $inData
	 * @param newsletterunsubscriptionModel $inModel
	 */
	function addInputToModel($inData, $inModel) {
		//$inModel->setPrimaryKey($inData['PrimaryKey']);
		$inModel->setID($inData['PrimaryKey']);
		$inModel->setUserID($inData['UserID']);
		$inModel->setEmailID($inData['EmailID']);
		$inModel->setNewsletterID($inData['NewsletterID']);
	}
	
	/**
	 * Builds the model
	 *
	 * @return void
	 */
	function buildModel() {
		$oModel = new newsletterunsubscriptionModel();
		$this->setModel($oModel);
	}
}