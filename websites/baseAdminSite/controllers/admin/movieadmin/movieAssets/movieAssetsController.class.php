<?php
/**
 * movieAssetsController
 *
 * Stored in movieAssetsController.class.php
 * 
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category movieAssetsController
 * @version $Rev: 11 $
 */


/**
 * movieAssetsController
 *
 * movieAssetsController class
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category movieAssetsController
 */
class movieAssetsController extends mvcDaoController {

	/**
	 * @see mvcControllerBase::initialise()
	 */
	function initialise() {
		parent::initialise();
		
		$this->setControllerView('movieAssetsView');
		
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
		$this->getInputManager()->addFilter('MovieID', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('Type', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Filename', utilityInputFilter::filterString());
		$data = $this->getInputManager()->doFilter();
		
		$this->setSearchOptionFromRequestData($data, 'MovieID');
		$this->setSearchOptionFromRequestData($data, 'Type');
		$this->setSearchOptionFromRequestData($data, 'Filename');
		
		parent::actionView();
	}
	
	/**
	 * @see mvcControllerBase::addInputFilters()
	 */
	function addInputFilters() {
		$this->getInputManager()->addFilter('PrimaryKey', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('MovieID', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Type', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Ext', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('ProfileID', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Description', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Width', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Height', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Filename', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('CdnURL', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Notes', utilityInputFilter::filterString());
	}
	
	/**
	 * @see mvcControllerBase::addInputToModel()
	 *
	 * @param array $inData
	 * @param movieAssetsModel $inModel
	 */
	function addInputToModel($inData, $inModel) {
		$inModel->setID($inData['PrimaryKey']);
		$inModel->setMovieID($inData['MovieID']);
		$inModel->setType($inData['Type']);
		$inModel->setExt($inData['Ext']);
		$inModel->setProfileID($inData['ProfileID']);
		$inModel->setDescription($inData['Description']);
		$inModel->setWidth($inData['Width']);
		$inModel->setHeight($inData['Height']);
		$inModel->setFilename($inData['Filename']);
		$inModel->setCdnURL($inData['CdnURL']);
		$inModel->setNotes($inData['Notes']);
	}
	
	/**
	 * Builds the model
	 *
	 * @return void
	 */
	function buildModel() {
		$oModel = new movieAssetsModel();
		$this->setModel($oModel);
	}
}