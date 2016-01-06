<?php
/**
 * helpTagsController
 *
 * Stored in helpTagsController.class.php
 * 
 * @author Pavan Kumar
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category helpTagsController
 * @version $Rev: 624 $
 */


/**
 * helpTagsController
 *
 * helpTagsController class
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category helpTagsController
 */
class helpTagsController extends mvcDaoController {
	
	/**
	 * @see mvcControllerBase::initialise()
	 */
	function initialise() {
		parent::initialise();
		
		$this->setControllerView('helpTagsView');
	}
	
	/**
	 * @see mvcControllerBase::addInputFilters()
	 */
	function addInputFilters() {
		$this->getInputManager()->addFilter('PrimaryKey', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('ID', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('Tag', utilityInputFilter::filterString());
	}
	
	/**
	 * @see mvcControllerBase::addInputToModel()
	 *
	 * @param array $inData
	 * @param helpTagsModel $inModel
	 */
	function addInputToModel($inData, $inModel) {
		$inModel->setID($inData['PrimaryKey']);
		$inModel->setTag($inData['Tag']);
	}
	
	/**
	 * Builds the model
	 *
	 * @return void
	 */
	function buildModel() {
		$oModel = new helpTagsModel();
		$this->setModel($oModel);
	}
}