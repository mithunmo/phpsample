<?php
/**
 * brandMusicController
 *
 * Stored in brandMusicController.class.php
 * 
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category brandMusicController
 * @version $Rev: 624 $
 */


/**
 * brandMusicController
 *
 * brandMusicController class
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category brandMusicController
 */
class brandMusicController extends mvcDaoController {
	
	/**
	 * @see mvcControllerBase::initialise()
	 */
	function initialise() {
		parent::initialise();
		
		$this->setControllerView('brandMusicView');
	}
	
	/**
	 * @see mvcControllerBase::addInputFilters()
	 */
	function addInputFilters() {
		$this->getInputManager()->addFilter('PrimaryKey', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('ID', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('BrandID', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('TrackList', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Status', utilityInputFilter::filterString());
	}
	
	/**
	 * @see mvcControllerBase::addInputToModel()
	 *
	 * @param array $inData
	 * @param brandMusicModel $inModel
	 */
	function addInputToModel($inData, $inModel) {
		/**
		 * @todo set the primary key here
		 */
		//$inModel->setPrimaryKey($inData['PrimaryKey']);
		//$inModel->setID($inData['ID']);
		$inModel->setBrandID($inData['BrandID']);
		$inModel->setTrackList($inData['TrackList']);
		$inModel->setStatus($inData['Status']);
	}
	
	/**
	 * Builds the model
	 *
	 * @return void
	 */
	function buildModel() {
		$oModel = new brandMusicModel();
		$this->setModel($oModel);
	}
}