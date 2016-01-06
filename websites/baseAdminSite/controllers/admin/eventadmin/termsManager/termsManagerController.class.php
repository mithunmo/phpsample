<?php
/**
 * termsManagerController
 *
 * Stored in termsManagerController.class.php
 * 
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category termsManagerController
 * @version $Rev: 11 $
 */


/**
 * termsManagerController
 *
 * termsManagerController class
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category termsManagerController
 */
class termsManagerController extends mvcDaoController {
	
	/**
	 * @see mvcControllerBase::initialise()
	 */
	function initialise() {
		parent::initialise();
		
		$this->setControllerView('termsManagerView');
	}
	
	/**
	 * @see mvcControllerBase::addInputFilters()
	 */
	function addInputFilters() {
		$this->getInputManager()->addFilter('PrimaryKey', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('ReplacesTerms', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('Description', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('HtmlLink', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('PdfLink', utilityInputFilter::filterString());
	}
	
	/**
	 * @see mvcControllerBase::addInputToModel()
	 *
	 * @param array $inData
	 * @param termsManagerModel $inModel
	 */
	function addInputToModel($inData, $inModel) {
		$inModel->setID($inData['PrimaryKey']);
		$inModel->setReplacesTerms($inData['ReplacesTerms']);
		$inModel->setDescription($inData['Description']);
		$inModel->setHtmlLink($inData['HtmlLink']);
		$inModel->setPdfLink($inData['PdfLink']);
	}
	
	/**
	 * Builds the model
	 *
	 * @return void
	 */
	function buildModel() {
		$oModel = new termsManagerModel();
		$this->setModel($oModel);
	}
}