<?php
/**
 * downloadsController
 *
 * Stored in downloadsController.class.php
 * 
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category downloadsController
 * @version $Rev: 624 $
 */


/**
 * downloadsController
 *
 * downloadsController class
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category downloadsController
 */
class downloadsController extends mvcDaoController {
	
	/**
	 * @see mvcControllerBase::initialise()
	 */
	function initialise() {
		parent::initialise();
		
		$this->setControllerView('downloadsView');
	}
	
	/**
	 * @see mvcControllerBase::addInputFilters()
	 */
	function addInputFilters() {
		$this->getInputManager()->addFilter('PrimaryKey', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('ID', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('TrackID', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('LicenseID', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('UserID', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('TrackName', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Status', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('MusicSource', utilityInputFilter::filterString());
	}
	
	/**
	 * @see mvcControllerBase::addInputToModel()
	 *
	 * @param array $inData
	 * @param downloadsModel $inModel
	 */
	function addInputToModel($inData, $inModel) {
		/**
		 * @todo set the primary key here
		 */
		//$inModel->setPrimaryKey($inData['PrimaryKey']);
		$inModel->setID($inData['ID']);
		$inModel->setTrackID($inData['TrackID']);
		$inModel->setLicenseID($inData['LicenseID']);
		$inModel->setUserID($inData['UserID']);
		$inModel->setTrackName($inData['TrackName']);
		$inModel->setStatus($inData['Status']);
		$inModel->setMusicSource($inData['MusicSource']);
	}
	
	/**
	 * Builds the model
	 *
	 * @return void
	 */
	function buildModel() {
		$oModel = new downloadsModel();
		$this->setModel($oModel);
	}
}