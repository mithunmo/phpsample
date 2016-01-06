<?php
/**
 * bouncedEmailController
 *
 * Stored in bouncedEmailController.class.php
 * 
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category bouncedEmailController
 * @version $Rev: 624 $
 */


/**
 * bouncedEmailController
 *
 * bouncedEmailController class
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category bouncedEmailController
 */
class bouncedEmailController extends mvcDaoController {
	
	/**
	 * @see mvcControllerBase::initialise()
	 */
	function initialise() {
		parent::initialise();
		
		$this->setControllerView('bouncedEmailView');
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
		$this->getInputManager()->addFilter('EmailAddress', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('ErrorDescription', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('SenderAddress', utilityInputFilter::filterString());
	}
	
	/**
	 * @see mvcControllerBase::addInputToModel()
	 *
	 * @param array $inData
	 * @param bouncedEmailModel $inModel
	 */
	function addInputToModel($inData, $inModel) {
		$inModel->setID($inData['PrimaryKey']);
		$inModel->setEmailAddress($inData['EmailAddress']);
		$inModel->setErrorDescription($inData['ErrorDescription']);
		$inModel->setSenderAddress($inData['SenderAddress']);
	}
	
	/**
	 * Builds the model
	 *
	 * @return void
	 */
	function buildModel() {
		$oModel = new bouncedEmailModel();
		$this->setModel($oModel);
	}
}