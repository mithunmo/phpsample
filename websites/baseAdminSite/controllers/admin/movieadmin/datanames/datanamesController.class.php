<?php
/**
 * datanamesController
 *
 * Stored in datanamesController.class.php
 * 
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category datanamesController
 * @version $Rev: 11 $
 */


/**
 * datanamesController
 *
 * datanamesController class
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category datanamesController
 */
class datanamesController extends mvcDaoController {
	
	/**
	 * @see mvcControllerBase::initialise()
	 */
	function initialise() {
		parent::initialise();
		
		$this->setControllerView('datanamesView');
	}
	
	/**
	 * @see mvcControllerBase::addInputFilters()
	 */
	function addInputFilters() {
		$this->getInputManager()->addFilter('PrimaryKey', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('Name', utilityInputFilter::filterString());
	}
	
	/**
	 * @see mvcControllerBase::addInputToModel()
	 *
	 * @param array $inData
	 * @param datanamesModel $inModel
	 */
	function addInputToModel($inData, $inModel) {
		$inModel->setID($inData['PrimaryKey']);
		$inModel->setName($inData['Name']);
	}
	
	/**
	 * Builds the model
	 *
	 * @return void
	 */
	function buildModel() {
		$oModel = new datanamesModel();
		$this->setModel($oModel);
	}
}