<?php
/**
 * categoriesController
 *
 * Stored in categoriesController.class.php
 * 
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category categoriesController
 * @version $Rev: 11 $
 */


/**
 * categoriesController
 *
 * categoriesController class
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category categoriesController
 */
class categoriesController extends mvcDaoController {
	
	/**
	 * @see mvcControllerBase::initialise()
	 */
	function initialise() {
		parent::initialise();
		
		$this->setControllerView('categoriesView');
	}
	
	/**
	 * @see mvcControllerBase::addInputFilters()
	 */
	function addInputFilters() {
		$this->getInputManager()->addFilter('PrimaryKey', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('SourceID', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('Description', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Exclusive', utilityInputFilter::filterString());
	}
	
	/**
	 * @see mvcControllerBase::addInputToModel()
	 *
	 * @param array $inData
	 * @param categoriesModel $inModel
	 */
	function addInputToModel($inData, $inModel) {
		$inModel->setID($inData['PrimaryKey']);
		$inModel->setSourceID($inData['SourceID']);
		$inModel->setDescription($inData['Description']);
		$inModel->setExclusive($inData['Exclusive']);
	}
	
	/**
	 * Builds the model
	 *
	 * @return void
	 */
	function buildModel() {
		$oModel = new categoriesModel();
		$this->setModel($oModel);
	}
}