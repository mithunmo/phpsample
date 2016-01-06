<?php
/**
 * homeController.class.php
 * 
 * homeController class
 *
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2009
 * @package scorpio
 * @subpackage websites_base_controllers
 * @category homeController
 */


/**
 * homeController class
 * 
 * Provides the "home" page defaults
 * 
 * @package scorpio
 * @subpackage websites_base_controllers
 * @category homeController
 */
class homeController extends mvcController {
	
	const ACTION_HOME = 'home';
	
	
	
	/**
	 * @see mvcControllerBase::initialise()
	 */
	function initialise() {
		parent::initialise();
		$this->setDefaultAction(self::ACTION_HOME);
	}
	
	/**
	 * @see mvcControllerBase::launch()
	 */
	function launch() {
		$oView = new homeView($this);
		$oView->showHomePage();
	}
}