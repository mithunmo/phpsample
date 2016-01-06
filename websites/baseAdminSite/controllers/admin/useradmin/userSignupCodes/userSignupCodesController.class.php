<?php
/**
 * userSignupCodesController
 *
 * Stored in userSignupCodesController.class.php
 * 
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category userSignupCodesController
 * @version $Rev: 11 $
 */


/**
 * userSignupCodesController
 *
 * userSignupCodesController class
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category userSignupCodesController
 */
class userSignupCodesController extends mvcDaoController {
	
	/**
	 * @see mvcControllerBase::initialise()
	 */
	function initialise() {
		parent::initialise();
		
		$this->setControllerView('userSignupCodesView');
	}
	
	/**
	 * @see mvcControllerBase::addInputFilters()
	 */
	function addInputFilters() {
		$this->getInputManager()->addFilter('PrimaryKey', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('ID', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('Code', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Description', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Location', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('TerritoryID', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('StartDate', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('EndDate', utilityInputFilter::filterString());
		
		$this->getInputManager()->addFilter('StartdateTime', utilityInputFilter::filterStringArray());
		$this->getInputManager()->addFilter('EnddateTime', utilityInputFilter::filterStringArray());
	}
	
	/**
	 * @see mvcControllerBase::addInputToModel()
	 *
	 * @param array $inData
	 * @param userSignupCodesModel $inModel
	 */
	function addInputToModel($inData, $inModel) {
		$inModel->setID($inData['PrimaryKey']);
		$inModel->setCode($inData['Code']);
		$inModel->setDescription($inData['Description']);
		$inModel->setLocation($inData['Location']);
		$inModel->setTerritoryID($inData['TerritoryID']);
		
		if ( isset($inData['StartDate']) && strlen($inData['StartDate']) == 10 ) {
			$inModel->setStartDate(mofilmUtilities::buildDate($inData, 'StartDate', 'StartdateTime'));
		}
		if ( isset($inData['EndDate']) && strlen($inData['EndDate']) == 10 ) {
			$inModel->setEndDate(mofilmUtilities::buildDate($inData, 'EndDate', 'EnddateTime'));
		}
	}
	
	/**
	 * Builds the model
	 *
	 * @return void
	 */
	function buildModel() {
		$oModel = new userSignupCodesModel();
		$this->setModel($oModel);
	}
}