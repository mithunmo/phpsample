<?php
/**
 * movieAwardsController
 *
 * Stored in movieAwardsController.class.php
 * 
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category movieAwardsController
 * @version $Rev: 11 $
 */


/**
 * movieAwardsController
 *
 * movieAwardsController class
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category movieAwardsController
 */
class movieAwardsController extends mvcDaoController {

	/**
	 * @see mvcControllerBase::initialise()
	 */
	function initialise() {
		parent::initialise();
		
		$this->setControllerView('movieAwardsView');
		
		$this->getMenuItems()->getItem(self::ACTION_VIEW)->addItem(
			new mvcControllerMenuItem(
				$this->buildUriPath(self::ACTION_SEARCH), 'Search', self::IMAGE_ACTION_SEARCH, 'Search', false, mvcControllerMenuItem::PATH_TYPE_URI, true
			)
		);
	}
	
	/**
	 * Handles listing objects and search options
	 * 
	 * @return void
	 */
	function actionView() {
		$this->getInputManager()->setLookupGlobals(utilityInputManager::LOOKUPGLOBALS_GET);
		$this->getInputManager()->addFilter('EventID', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('Type', utilityInputFilter::filterString());
		$data = $this->getInputManager()->doFilter();
		
		$this->setSearchOptionFromRequestData($data, 'EventID');
		$this->setSearchOptionFromRequestData($data, 'Type');
		
		parent::actionView();
	}
	
	/**
	 * @see mvcControllerBase::addInputFilters()
	 */
	function addInputFilters() {
		$this->getInputManager()->addFilter('PrimaryKey', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('MovieID', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('EventID', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('SourceID', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('Position', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('Type', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Name', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Year', utilityInputFilter::filterInt());
	}
	
	/**
	 * @see mvcControllerBase::addInputToModel()
	 *
	 * @param array $inData
	 * @param movieAwardsModel $inModel
	 */
	function addInputToModel($inData, $inModel) {
		$inModel->setID($inData['PrimaryKey']);
		$inModel->setMovieID($inData['MovieID']);
		$inModel->setEventID($inData['EventID']);
		$inModel->setSourceID($inData['SourceID']);
		$inModel->setPosition($inData['Position']);
		$inModel->setType($inData['Type']);
		$inModel->setName($inData['Name']);
		$inModel->setYear($inData['Year']);
	}
	
	/**
	 * Builds the model
	 *
	 * @return void
	 */
	function buildModel() {
		$oModel = new movieAwardsModel();
		$this->setModel($oModel);
	}
}