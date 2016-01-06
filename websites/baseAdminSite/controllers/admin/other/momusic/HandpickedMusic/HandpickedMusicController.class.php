<?php
/**
 * HandpickedMusicController
 *
 * Stored in HandpickedMusicController.class.php
 * 
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category HandpickedMusicController
 * @version $Rev: 624 $
 */


/**
 * HandpickedMusicController
 *
 * HandpickedMusicController class
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category HandpickedMusicController
 */
class HandpickedMusicController extends mvcDaoController {
	
	/**
	 * @see mvcControllerBase::initialise()
	 */
	function initialise() {
		parent::initialise();
		
		$this->setControllerView('HandpickedMusicView');
	}
	
	/**
	 * @see mvcControllerBase::addInputFilters()
	 */
	function addInputFilters() {
		$this->getInputManager()->addFilter('PrimaryKey', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('ID', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('CoverImageID', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('TrackID', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Status', utilityInputFilter::filterString());
                $this->getInputManager()->addFilter('Rank', utilityInputFilter::filterString());
	}
	
	/**
	 * @see mvcControllerBase::addInputToModel()
	 *
	 * @param array $inData
	 * @param HandpickedMusicModel $inModel
	 */
	function addInputToModel($inData, $inModel) {
		/**
		 * @todo set the primary key here
		 */
		//$inModel->setPrimaryKey($inData['PrimaryKey']);
		//$inModel->setID($inData['ID']);
		$inModel->setCoverImageID($inData['CoverImageID']);
		$inModel->setTrackID($inData['TrackID']);
		$inModel->setStatus($inData['Status']);
                $inModel->setRank($inData['Rank']);
	}
	
	/**
	 * Builds the model
	 *
	 * @return void
	 */
	function buildModel() {
		$oModel = new HandpickedMusicModel();
		$this->setModel($oModel);
	}
}