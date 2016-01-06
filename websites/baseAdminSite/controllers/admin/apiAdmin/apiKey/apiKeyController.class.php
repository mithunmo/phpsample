<?php
/**
 * apiKeyController
 *
 * Stored in apiKeyController.class.php
 * 
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category apiKeyController
 * @version $Rev: 624 $
 */


/**
 * apiKeyController
 *
 * apiKeyController class
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category apiKeyController
 */
class apiKeyController extends mvcDaoController {
	
	/**
	 * @see mvcControllerBase::initialise()
	 */
	function initialise() {
		parent::initialise();
		
		$this->setControllerView('apiKeyView');
	}
	
	/**
	 * @see mvcControllerBase::addInputFilters()
	 */
	function addInputFilters() {
		$this->getInputManager()->addFilter('PrimaryKey', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('ID', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('PublicKey', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('PrivateKey', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Active', utilityInputFilter::filterInt());
	}
	
	/**
	 * @see mvcControllerBase::addInputToModel()
	 *
	 * @param array $inData
	 * @param apiKeyModel $inModel
	 */
	function addInputToModel($inData, $inModel) {
		//$inModel->setPrimaryKey($inData['PrimaryKey']);
		$inModel->setID($inData['PrimaryKey']);
		$inModel->setPublicKey($inData['PublicKey']);
		$inModel->setPrivateKey($inData['PrivateKey']);
		$inModel->setActive($inData['Active']);
	}
	
	/**
	 * Builds the model
	 *
	 * @return void
	 */
	function buildModel() {
		$oModel = new apiKeyModel();
		$this->setModel($oModel);
	}
}