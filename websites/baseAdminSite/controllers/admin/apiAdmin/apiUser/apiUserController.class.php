<?php
/**
 * apiUserController
 *
 * Stored in apiUserController.class.php
 * 
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category apiUserController
 * @version $Rev: 624 $
 */


/**
 * apiUserController
 *
 * apiUserController class
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category apiUserController
 */
class apiUserController extends mvcDaoController {
	
	/**
	 * @see mvcControllerBase::initialise()
	 */
	function initialise() {
		parent::initialise();
		
		$this->setControllerView('apiUserView');
	}
	
	/**
	 * Deletes the mofilmAPIKey record associated with mofilmAPIUser and then
	 * calls mvcDaoController actionDoDelete Method
	 * 
	 */
	function  actionDoDelete() {
		$primaryKey = $this->getActionFromRequest(false, 1);
		$this->setPrimaryKey($primaryKey);
		$oModel = $this->getExistingObject();
		mofilmSystemAPIKey::getInstance($oModel->getMofilmAPIKeyID())->delete();
		parent::actionDoDelete();
	}

	/**
	 * @see mvcControllerBase::addInputFilters()
	 */
	function addInputFilters() {
		$this->getInputManager()->addFilter('PrimaryKey', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('ID', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('MofilmAPIKeyID', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('CompanyName', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('EmailContact', utilityInputFilter::filterString());
	}
	
	/**
	 * @see mvcControllerBase::addInputToModel()
	 *
	 * @param array $inData
	 * @param apiUserModel $inModel
	 */
	function addInputToModel($inData, $inModel) {
		$inModel->setID($inData['PrimaryKey']);
		$inModel->setMofilmAPIKeyID($this->getModel()->getMofilmKey());
		$inModel->setCompanyName($inData['CompanyName']);
		$inModel->setEmailContact($inData['EmailContact']);
	}
	
	/**
	 * Builds the model
	 *
	 * @return void
	 */
	function buildModel() {
		$oModel = new apiUserModel();
		$this->setModel($oModel);
	}
}