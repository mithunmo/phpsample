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
        const ACTION_LEGACY_HOME = 'legacy';
	
	
	
	/**
	 * @see mvcControllerBase::initialise()
	 */
	function initialise() {
		parent::initialise();
                $this->getControllerActions()
				->addAction(self::ACTION_LEGACY_HOME)
                                ->addAction(self::ACTION_HOME);
	}
	
	/**
	 * @see mvcControllerBase::launch()
	 */
	function launch() {
            
                switch($this->getAction()){
                    case self::ACTION_HOME:
                                    $this->redirect(system::getConfig()->getParam('mofilm', 'platformUri')->getParamValue().'dashboard/?token='.$this->getRequest()->getSession()->getToken());
                    case self::ACTION_LEGACY_HOME:
                                   $oView = new homeView($this);
                                    $oView->showHomePage();
                                    break;
                    default:
                                   $this->redirect(system::getConfig()->getParam('mofilm', 'platformUri')->getParamValue().'dashboard/?token='.$this->getRequest()->getSession()->getToken());			                              
                }
        }
}