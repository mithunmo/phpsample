<?php
/**
 * groupController
 *
 * Stored in groupController.class.php
 * 
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_my.mofilm.com
 * @subpackage controllers
 * @category groupController
 * @version $Rev: 11 $
 */


/**
 * groupController
 *
 * groupController class
 * 
 * @package websites_my.mofilm.com
 * @subpackage controllers
 * @category groupController
 */
class groupController extends mvcController {
	
	const ACTION_VIEW = 'view';
	
	/**
	 * @see mvcControllerBase::initialise()
	 */
	function initialise() {
		parent::initialise();
		
		$this->setDefaultAction(self::ACTION_VIEW);
		$this->setRequiresAuthentication(false);
		
		$this->getControllerActions()
			->addAction(self::ACTION_VIEW)
			->addAction(new mvcControllerAction('groupName', '/[\w\-\_]+/i'));
	}
	
	/**
	 * @see mvcControllerBase::launch()
	 */
	function launch() {
		if ( $this->getAction() == self::ACTION_VIEW ) {
			$this->redirect('/');
		} else {
			$this->getModel()->setGroupName($this->getAction());
			
			$oView = new groupView($this);
			$oView->showGroupPage();
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
	 * @return groupModel
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
		$oModel = new groupModel();
		$this->setModel($oModel);
	}
}