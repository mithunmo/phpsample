<?php
/**
 * galleryController
 *
 * Stored in galleryController.class.php
 * 
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_south-bytes.com
 * @subpackage controllers
 * @category galleryController
 * @version $Rev: 11 $
 */


/**
 * galleryController
 *
 * galleryController class
 * 
 * @package websites_south-bytes.com
 * @subpackage controllers
 * @category galleryController
 */
class galleryController extends mvcController {
	
	const ACTION_VIEW = 'view';
	
	/**
	 * @see mvcControllerBase::initialise()
	 */
	function initialise() {
		parent::initialise();
		
		$this->setDefaultAction(self::ACTION_VIEW);
		$this->setRequiresAuthentication(false);
		
		$this->getControllerActions()->addAction(self::ACTION_VIEW);
	}
	
	/**
	 * @see mvcControllerBase::launch()
	 */
	function launch() {
		$oView = new galleryView($this);
		$oView->showGalleryPage();
	}
	
	
	
	/**
	 * @see mvcControllerBase::addInputFilters()
	 */
	function addInputFilters() {
		
	}
	
	/**
	 * @see mvcControllerBase::addInputToModel()
	 */
	function addInputToModel($inData, $inModel) {
		
	}
	
	/**
	 * Fetches the model
	 *
	 * @return galleryModel
	 */
	function getModel() {
		if ( !parent::getModel() ) {
			$this->buildModel();
		}
		return parent::getModel();
	}
	
	/**
	 * Builds the model
	 *
	 * @return void
	 */
	function buildModel() {
		$oModel = new galleryModel();
		$this->setModel($oModel);
	}
}