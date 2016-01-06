<?php
/**
 * imapServerController
 *
 * Stored in imapServerController.class.php
 * 
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category imapServerController
 * @version $Rev: 624 $
 */


/**
 * imapServerController
 *
 * imapServerController class
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category imapServerController
 */
class imapServerController extends mvcDaoController {
	
	/**
	 * @see mvcControllerBase::initialise()
	 */
	function initialise() {
		parent::initialise();
		
		$this->setControllerView('imapServerView');
	}
	
	/**
	 * @see mvcControllerBase::addInputFilters()
	 */
	function addInputFilters() {
		$this->getInputManager()->addFilter('PrimaryKey', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('ID', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('ImapServer', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('ImapPort', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('ImapFolder', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('DaemonEmail', utilityInputFilter::filterString());
	}
	
	/**
	 * @see mvcControllerBase::addInputToModel()
	 *
	 * @param array $inData
	 * @param imapServerModel $inModel
	 */
	function addInputToModel($inData, $inModel) {
		$inModel->setID($inData['PrimaryKey']);
		$inModel->setImapServer($inData['ImapServer']);
		$inModel->setImapPort($inData['ImapPort']);
		$inModel->setImapFolder($inData['ImapFolder']);
		$inModel->setDaemonEmail($inData['DaemonEmail']);
	}
	
	/**
	 * Builds the model
	 *
	 * @return void
	 */
	function buildModel() {
		$oModel = new imapServerModel();
		$this->setModel($oModel);
	}
}