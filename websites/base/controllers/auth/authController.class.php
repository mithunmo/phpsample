<?php
/**
 * authController
 *
 * Stored in authController.class.php
 * 
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package websites_base
 * @subpackage controllers
 * @category authController
 * @version $Rev: 736 $
 */


/**
 * authController
 *
 * authController class
 * 
 * @package websites_base
 * @subpackage controllers
 * @category authController
 */
class authController extends mvcController {
	
	const ACTION_VIEW = 'view';
	const ACTION_REDIRECT = "r";
	
	/**
	 * @see mvcControllerBase::initialise()
	 */
	function initialise() {
		parent::initialise();
		
		$this->setDefaultAction(self::ACTION_VIEW);
		$this->setRequiresAuthentication(true);
		
		$this->getControllerActions()->addAction(self::ACTION_VIEW);
		$this->getControllerActions()->addAction(self::ACTION_REDIRECT);
	}
	
	/**
	 * @see mvcControllerBase::launch()
	 */
	function launch() {
		
		if ( $this->getAction() == self::ACTION_REDIRECT ) {
			
			$this->getInputManager()->setLookupGlobals(utilityInputManager::LOOKUPGLOBALS_GET);
			$inData = $this->getInputManager()->doFilter();
			
			$this->redirect(urldecode($inData["url"]));
			
		} else {
			$oView = new authView($this);
			$oView->showAuthPage();			
		}
	}
	
	
	
	/**
	 * @see mvcControllerBase::addInputFilters()
	 */
	function addInputFilters() {
		$this->getInputManager()->addFilter("url", utilityInputFilter::filterString());
		
	}
	
	/**
	 * @see mvcControllerBase::addInputToModel()
	 */
	function addInputToModel($inData, $inModel) {
		
	}
	
	/**
	 * Fetches the model
	 *
	 * @return authModel
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
		$oModel = new authModel();
		$this->setModel($oModel);
	}
}