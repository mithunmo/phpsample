<?php
/**
 * languagesController
 *
 * Stored in languagesController.class.php
 * 
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category languagesController
 * @version $Rev: 11 $
 */


/**
 * languagesController
 *
 * languagesController class
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category languagesController
 */
class languagesController extends mvcDaoController {
	
	/**
	 * @see mvcControllerBase::initialise()
	 */
	function initialise() {
		parent::initialise();
		
		$this->setControllerView('languagesView');
	}
	
	/**
	 * @see mvcControllerBase::addInputFilters()
	 */
	function addInputFilters() {
		$this->getInputManager()->addFilter('PrimaryKey', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('Name', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Iso', utilityInputFilter::filterString());
	}
	
	/**
	 * @see mvcControllerBase::addInputToModel()
	 *
	 * @param array $inData
	 * @param languagesModel $inModel
	 */
	function addInputToModel($inData, $inModel) {
		$inModel->setID($inData['PrimaryKey']);
		$inModel->setName($inData['Name']);
		$inModel->setIso($inData['Iso']);
	}
	
	/**
	 * Builds the model
	 *
	 * @return void
	 */
	function buildModel() {
		$oModel = new languagesModel();
		$this->setModel($oModel);
	}
}