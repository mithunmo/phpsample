<?php
/**
 * newsletterlinkController
 *
 * Stored in newsletterlinkController.class.php
 * 
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category newsletterlinkController
 * @version $Rev: 624 $
 */


/**
 * newsletterlinkController
 *
 * newsletterlinkController class
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category newsletterlinkController
 */
class newsletterlinkController extends mvcDaoController {
	
	/**
	 * @see mvcControllerBase::initialise()
	 */
	function initialise() {
		parent::initialise();
		
		$this->setControllerView('newsletterlinkView');
	}


	/**
	 * @see mvcControllerBase::launch()
	 */
	function  launch() {
	    $this->getMenuItems()->reset();
	    parent::launch();
	}

	/**
	 * Handles listing objects and search options
	 *
	 * @return void
	 */
	function actionView() {
		$this->getInputManager()->setLookupGlobals(utilityInputManager::LOOKUPGLOBALS_GET);
		$this->getInputManager()->addFilter('newslettertri', utilityInputFilter::filterInt());
		$data = $this->getInputManager()->doFilter();
		$oUser = $this->getRequest()->getSession()->getUser();
		$this->setSearchOptionFromRequestData($data, 'newslettertri');
		parent::actionView();
	}


	/**
	 * @see mvcControllerBase::addInputFilters()
	 */
	function addInputFilters() {
		$this->getInputManager()->addFilter('PrimaryKey', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('ID', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('NewsletterID', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('LinkName', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('UserID', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Status', utilityInputFilter::filterString());
	}
	
	/**
	 * @see mvcControllerBase::addInputToModel()
	 *
	 * @param array $inData
	 * @param newsletterlinkModel $inModel
	 */
	function addInputToModel($inData, $inModel) {
		//$inModel->setPrimaryKey($inData['PrimaryKey']);
		$inModel->setID($inData['PrimaryKey']);
		$inModel->setNewsletterID($inData['NewsletterID']);
		$inModel->setLinkName($inData['LinkName']);
		$inModel->setUserID($inData['UserID']);
		$inModel->setStatus($inData['Status']);
	}
	
	/**
	 * Builds the model
	 *
	 * @return void
	 */
	function buildModel() {
		$oModel = new newsletterlinkModel();
		$this->setModel($oModel);
	}
}