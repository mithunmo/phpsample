<?php
/**
 * newsletterController
 *
 * Stored in newsletterController.class.php
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category newsletterController
 * @version $Rev: 624 $
 */


/**
 * newsletterController
 *
 * newsletterController class
 *
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category newsletterController
 */
class newsletterController extends mvcDaoController {

	/**
	 * @see mvcControllerBase::initialise()
	 */
	function initialise() {
		parent::initialise();
		$this->setControllerView('newsletterView');
	}

	/**
	 * @see mvcControllerBase::addInputFilters()
	 */
	function addInputFilters() {
		$this->getInputManager()->addFilter('PrimaryKey', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('Nlid', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('OutboundTypeID', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('Language', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Messageheader', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('MessageText', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Ishtml', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('NewsletterType', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('Name', utilityInputFilter::filterString());

	}

	/**
	 * @see mvcControllerBase::addInputToModel()
	 *
	 * @param array $inData
	 * @param newsletterModel $inModel
	 */
	function addInputToModel($inData, $inModel) {
		//$inModel->setPrimaryKey($inData['PrimaryKey']);
		$inModel->setNlid($inData['PrimaryKey']);
		$inModel->setOutboundtypeid($inData['OutboundTypeID']);
		$inModel->setLanguage($inData['Language']);
		$inModel->setMessageSubject($inData['Messageheader']);
		$inModel->setMessageText($inData['MessageText']);
		$messageHTML = preg_replace("/<br \/>/", "<br />\n", stripslashes(trim($_POST['MessageBody'])));
		$inModel->setMessageBody($messageHTML);
		$inModel->setIshtml($inData['Ishtml']);
		$inModel->setNewsletterType($inData['NewsletterType']);
		$inModel->setMarkForDeletion($inData['MarkForDeletion']);
		$inModel->setName($inData['Name']);
	}

	/**
	 * Builds the model
	 *
	 * @return void
	 */
	function buildModel() {
		$oModel = new newsletterModel();
		$this->setModel($oModel);
	}
}