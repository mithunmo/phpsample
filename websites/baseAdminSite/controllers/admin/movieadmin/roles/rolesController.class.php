<?php
/**
 * rolesController
 *
 * Stored in rolesController.class.php
 * 
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category rolesController
 * @version $Rev: 11 $
 */


/**
 * rolesController
 *
 * rolesController class
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category rolesController
 */
class rolesController extends mvcDaoController {
	
	/**
	 * @see mvcControllerBase::initialise()
	 */
	function initialise() {
		parent::initialise();
		
		$this->setControllerView('rolesView');
	}
	
	/**
	 * @see mvcControllerBase::addInputFilters()
	 */
	function addInputFilters() {
		$this->getInputManager()->addFilter('PrimaryKey', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('Description', utilityInputFilter::filterString());
	}
	
	/**
	 * @see mvcControllerBase::addInputToModel()
	 *
	 * @param array $inData
	 * @param rolesModel $inModel
	 */
	function addInputToModel($inData, $inModel) {
		$inModel->setID($inData['PrimaryKey']);
		$inModel->setDescription($inData['Description']);
	}
	
	/**
	 * Builds the model
	 *
	 * @return void
	 */
	function buildModel() {
		$oModel = new rolesModel();
		$this->setModel($oModel);
	}
}