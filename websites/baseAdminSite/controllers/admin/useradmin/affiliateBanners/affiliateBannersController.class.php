<?php
/**
 * affiliateBannersController
 *
 * Stored in affiliateBannersController.class.php
 * 
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category affiliateBannersController
 * @version $Rev: 91 $
 */


/**
 * affiliateBannersController
 *
 * affiliateBannersController class
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category affiliateBannersController
 */
class affiliateBannersController extends mvcDaoController {
	
	/**
	 * @see mvcControllerBase::initialise()
	 */
	function initialise() {
		parent::initialise();
		
		$this->setControllerView('affiliateBannersView');
	}
	
	/**
	 * @see mvcControllerBase::addInputFilters()
	 */
	function addInputFilters() {
		$this->getInputManager()->addFilter('PrimaryKey', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('Description', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Url', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Affiliate', utilityInputFilter::filterString());
	}
	
	/**
	 * @see mvcControllerBase::addInputToModel()
	 *
	 * @param array $inData
	 * @param affiliateBannersModel $inModel
	 */
	function addInputToModel($inData, $inModel) {
		$inModel->setID($inData['PrimaryKey']);
		$inModel->setDescription($inData['Description']);
		$inModel->setUrl($inData['Url']);
		$inModel->setAffiliate($inData['Affiliate']);
	}
	
	/**
	 * Builds the model
	 *
	 * @return void
	 */
	function buildModel() {
		$oModel = new affiliateBannersModel();
		$this->setModel($oModel);
	}
}