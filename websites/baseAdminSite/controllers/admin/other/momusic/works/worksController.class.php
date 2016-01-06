<?php
/**
 * worksController
 *
 * Stored in worksController.class.php
 * 
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category worksController
 * @version $Rev: 624 $
 */


/**
 * worksController
 *
 * worksController class
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category worksController
 */
class worksController extends mvcDaoController {
	
	/**
	 * @see mvcControllerBase::initialise()
	 */
	function initialise() {
		parent::initialise();
		
		$this->setControllerView('worksView');
	}
	
	function launch() {
		if ( $this->getAction() == self::ACTION_DO_NEW ) {
/*                    
			$this->buildModel();
			$this->getInputManager()->setLookupGlobals(utilityInputManager::LOOKUPGLOBALS_POST);
			$data = $this->getInputManager()->doFilter();
			$this->addInputToModel($data, $this->getModel());
			$this->getModel()->musicSave();
 * 
 */
			$this->redirect($this->buildUriPath(self::ACTION_VIEW));
		} elseif ( $this->getAction() == self::ACTION_DO_EDIT ) {
			$this->buildModel();
			$this->getInputManager()->setLookupGlobals(utilityInputManager::LOOKUPGLOBALS_POST);
			$data = $this->getInputManager()->doFilter();
			$this->addInputToModel($data, $this->getModel());
			$this->getModel()->musicEditSave();
			$this->redirect($this->buildUriPath(self::ACTION_VIEW));			
		} else {
			parent::launch();
		}
		
	}
	
	/**
	 * @see mvcControllerBase::addInputFilters()
	 */
	function addInputFilters() {
		$this->getInputManager()->addFilter('PrimaryKey', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('ID', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('ArtistID', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('TrackName', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Path', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Duration', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Source', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Tags', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Description', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Sku', utilityInputFilter::filterInt());
	}
	
	/**
	 * @see mvcControllerBase::addInputToModel()
	 *
	 * @param array $inData
	 * @param worksModel $inModel
	 */
	function addInputToModel($inData, $inModel) {
		/**
		 * @todo set the primary key here
		 */
		//$inModel->setPrimaryKey($inData['PrimaryKey']);
		$inModel->setID($inData['PrimaryKey']);
		$inModel->setArtistName($inData['ArtistID']);
		$inModel->setSongName($inData['TrackName']);
		$inModel->setPath($inData['Path']);
		$inModel->setDuration($inData['Duration']);
		$inModel->setMusicSource($inData['Source']);
		$inModel->setDescription($inData["Description"]);
	}
	
	/**
	 * Builds the model
	 *
	 * @return void
	 */
	function buildModel() {
		$oModel = new worksModel();
		$this->setModel($oModel);
	}
}