<?php
/**
 * showController
 *
 * Stored in showController.class.php
 * 
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package websites_bcsff.mofilm.cn
 * @subpackage controllers
 * @category showController
 * @version $Rev: 736 $
 */


/**
 * showController
 *
 * showController class
 * 
 * @package websites_bcsff.mofilm.cn
 * @subpackage controllers
 * @category showController
 */
class showController extends mvcController {
	
	const ACTION_VIEW = 'view';
	const ACTION_PROCESS = "process";
	const ACTION_SUCCESS = "success";
	const ACTION_COMP = "comp";
	const ACTION_EVENT = "event";
	const ACTION_REEBOK = "reebok";
	const ACTION_INTRO = "intro";
	
	/**
	 * @see mvcControllerBase::initialise()
	 */
	function initialise() {
		parent::initialise();
		
		$this->setDefaultAction(self::ACTION_VIEW);
		$this->setRequiresAuthentication(false);
		
		$this->getControllerActions()->addAction(self::ACTION_VIEW);
		$this->getControllerActions()->addAction(self::ACTION_COMP);
		$this->getControllerActions()->addAction(self::ACTION_SUCCESS);
		$this->getControllerActions()->addAction(self::ACTION_PROCESS);
		$this->getControllerActions()->addAction(self::ACTION_EVENT);
		$this->getControllerActions()->addAction(self::ACTION_REEBOK);
		$this->getControllerActions()->addAction(self::ACTION_INTRO);
	}
	
	/**
	 * @see mvcControllerBase::launch()
	 */
	function launch() {
		
		if ( $this->getAction() == self::ACTION_COMP ) {
			$oView = new showView($this);
			$oView->showCompPage();
			
		} else if ( $this->getAction() == self::ACTION_PROCESS ) {
			$oView = new showView($this);
			$oView->showProcessPage();
			
		} else if ( $this->getAction() == self::ACTION_SUCCESS ) {
			$oView = new showView($this);
			$oView->showSuccessPage();
			
		} else if ( $this->getAction() == self::ACTION_EVENT ) {
			$oView = new showView($this);
			$oView->showEventPage();
			
		} else if ( $this->getAction() == self::ACTION_REEBOK ) {
			$oView = new showView($this);
			$oView->showReebokPage();
			
		} else if ( $this->getAction() == self::ACTION_INTRO ) {
			$oView = new showView($this);
			$oView->showIntroPage();
			
		} else {
			$oView = new showView($this);
			$oView->showShowPage();
		}
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
	 * @return showModel
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
		$oModel = new showModel();
		$this->setModel($oModel);
	}
}