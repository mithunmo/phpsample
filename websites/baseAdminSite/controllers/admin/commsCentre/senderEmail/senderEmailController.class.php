<?php
/**
 * senderEmailController
 *
 * Stored in senderEmailController.class.php
 * 
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category senderEmailController
 * @version $Rev: 624 $
 */


/**
 * senderEmailController
 *
 * senderEmailController class
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category senderEmailController
 */
class senderEmailController extends mvcDaoController {
	
	/**
	 * @see mvcControllerBase::initialise()
	 */
	function initialise() {
		parent::initialise();
		
		$this->setControllerView('senderEmailView');
	}
	
	/**
	 * @see mvcControllerBase::addInputFilters()
	 */
	function addInputFilters() {
		$this->getInputManager()->addFilter('PrimaryKey', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('ID', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('Name', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('SenderEmail', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('SenderPassword', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('ImapServer', utilityInputFilter::filterInt());
	}
	
	/**
	 * @see mvcControllerBase::addInputToModel()
	 *
	 * @param array $inData
	 * @param senderEmailModel $inModel
	 */
	function addInputToModel($inData, $inModel) {
		$inModel->setID($inData['PrimaryKey']);
		$inModel->setName($inData['Name']);
		$inModel->setSenderEmail(stripslashes(trim($_POST['SenderEmail'])));			
		$inModel->setSenderPassword($inModel->encrypt($inData['SenderPassword']));
		$inModel->setImapServerID($inData['ImapServer']);
	}
	
	/**
	 * Builds the model
	 *
	 * @return void
	 */
	function buildModel() {
		$oModel = new senderEmailModel();
		$this->setModel($oModel);
	}
}