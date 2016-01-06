<?php
/**
 * userActivityLogController
 *
 * Stored in userActivityLogController.class.php
 * 
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category userActivityLogController
 * @version $Rev: 11 $
 */


/**
 * userActivityLogController
 *
 * userActivityLogController class
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category userActivityLogController
 */
class userActivityLogController extends mvcDaoController {
	
	/**
	 * @see mvcControllerBase::initialise()
	 */
	function initialise() {
		parent::initialise();
		
		$this->setControllerView('userActivityLogView');
		
		/*
		 * Clear all menu items, only allow refresh and search
		 */
		$this->getMenuItems()->reset();
		$oItem = new mvcControllerMenuItem(self::ACTION_VIEW, 'View', self::IMAGE_ACTION_VIEW, 'Default view');
		$oItem->addItem(
			new mvcControllerMenuItem(
				$this->buildUriPath(self::ACTION_VIEW), 'Refresh', self::IMAGE_ACTION_VIEW, 'Refresh list', false, mvcControllerMenuItem::PATH_TYPE_URI
			)
		);
		$oItem->addItem(
			new mvcControllerMenuItem(
				$this->buildUriPath(self::ACTION_SEARCH), 'Search', self::IMAGE_ACTION_SEARCH, 'Search', false, mvcControllerMenuItem::PATH_TYPE_URI, true
			)
		);
		$this->getMenuItems()->addItem($oItem);
	}

	/**
	 * Handles listing objects and search options
	 * 
	 * @return void
	 */
	function actionView() {
		$this->getInputManager()->setLookupGlobals(utilityInputManager::LOOKUPGLOBALS_GET);
		$this->getInputManager()->addFilter('UserID', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('Type', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Description', utilityInputFilter::filterString());
		$data = $this->getInputManager()->doFilter();
		
		$this->setSearchOptionFromRequestData($data, 'UserID');
		$this->setSearchOptionFromRequestData($data, 'Type');
		$this->setSearchOptionFromRequestData($data, 'Description');
		
		parent::actionView();
	}
	
	/**
	 * @see mvcControllerBase::addInputFilters()
	 */
	function addInputFilters() {
		$this->getInputManager()->addFilter('UserID', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('Type', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Description', utilityInputFilter::filterString());
	}
	
	/**
	 * @see mvcControllerBase::addInputToModel()
	 *
	 * @param array $inData
	 * @param userActivityLogModel $inModel
	 */
	function addInputToModel($inData, $inModel) {
		$inModel->setUserID($inData['UserID']);
		$inModel->setTimestamp($inData['Timestamp']);
		$inModel->setType($inData['Type']);
		$inModel->setDescription($inData['Description']);
	}
	
	/**
	 * Builds the model
	 *
	 * @return void
	 */
	function buildModel() {
		$oModel = new userActivityLogModel();
		$this->setModel($oModel);
	}
}