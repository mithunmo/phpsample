<?php
/**
 * trackManagerController
 *
 * Stored in trackManagerController.class.php
 * 
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category trackManagerController
 * @version $Rev: 11 $
 */


/**
 * trackManagerController
 *
 * trackManagerController class
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category trackManagerController
 */
class trackManagerController extends mvcDaoController {
	
	/**
	 * @see mvcDaoController::actionView()
	 */
	function actionView() {
		$this->getInputManager()->setLookupGlobals(utilityInputManager::LOOKUPGLOBALS_GET);
		$this->getInputManager()->addFilter('SupplierID', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('SourceID', utilityInputFilter::filterInt());
		$data = $this->getInputManager()->doFilter();
		
		$this->setSearchOptionFromRequestData($data, 'SupplierID');
		$this->setSearchOptionFromRequestData($data, 'SourceID');
		
		parent::actionView();
	}
	
	/**
	 * @see mvcControllerBase::initialise()
	 */
	function initialise() {
		parent::initialise();
		
		$this->setControllerView('trackManagerView');
		
		$this->getMenuItems()->getItem(self::ACTION_VIEW)->addItem(
			new mvcControllerMenuItem(
				$this->buildUriPath(self::ACTION_SEARCH), 'Search', self::IMAGE_ACTION_SEARCH, 'Search', false, mvcControllerMenuItem::PATH_TYPE_URI, true
			)
		);
	}
	
	/**
	 * @see mvcControllerBase::addInputFilters()
	 */
	function addInputFilters() {
		$this->getInputManager()->addFilter('PrimaryKey', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('Description', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Filetype', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Filename', utilityInputFilter::filterString());
		
		$this->getInputManager()->addFilter('SupplierID', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('ExternalReference', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Artist', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Title', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('DigitalISRC', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Sources', utilityInputFilter::filterStringArray());
	}
	
	/**
	 * @see mvcControllerBase::addInputToModel()
	 *
	 * @param array $inData
	 * @param trackManagerModel $inModel
	 */
	function addInputToModel($inData, $inModel) {
		$inModel->setID($inData['PrimaryKey']);
		$inModel->setDescription($inData['Description']);
		$inModel->setFiletype($inData['Filetype']);
		$inModel->setFilename($inData['Filename']);
		$inModel->setSupplierID($inData['SupplierID']);
		$inModel->setExternalReference($inData['ExternalReference']);
		$inModel->setArtist($inData['Artist']);
		$inModel->setTitle($inData['Title']);
		$inModel->setDigitalISRC($inData['DigitalISRC']);
		
		if ( isset($inData['Sources']) ) {
			$inModel->getSourceSet()->reset();
			
			foreach ( $inData['Sources'] as $sourceID ) {
				$inModel->getSourceSet()->setObject(mofilmSource::getInstance($sourceID));
			}
		}
	}
	
	/**
	 * Builds the model
	 *
	 * @return void
	 */
	function buildModel() {
		$oModel = new trackManagerModel();
		$this->setModel($oModel);
	}
}