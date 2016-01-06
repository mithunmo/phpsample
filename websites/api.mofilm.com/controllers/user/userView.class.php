<?php
/**
 * userView.class.php
 * 
 * userView class
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package websites_api.mofilm.com
 * @subpackage controllers
 * @category userView
 * @version $Rev: 634 $
 */


/**
 * userView class
 * 
 * Provides the "userView" page
 * 
 * @package websites_api.mofilm.com
 * @subpackage controllers
 * @category userView
 */
class userView extends mvcView {

	/**
	 * @see mvcViewBase::setupInitialVars()
	 */
	function setupInitialVars() {
		parent::setupInitialVars();
	}
	
	/**
	 * Shows the Authenticated userView page
	 *
	 * @return void
	 */
	function showUserPage() {
		$this->setCacheLevelNone();
		$this->getEngine()->assign('userID', utilityOutputWrapper::wrap($this->getModel()->getUser()->getID()));
		$this->getEngine()->assign('username',  utilityOutputWrapper::wrap($this->getModel()->getUsername()));
		$this->getEngine()->assign('token',  utilityOutputWrapper::wrap($this->getModel()->getRequestToken()));

		$this->render($this->getTpl('user'));
	}

        function showRegisterPage(){
		$this->getEngine()->assign('userID', utilityOutputWrapper::wrap($this->getModel()->getUser()->getID()));
                $this->render($this->getTpl('register'));
        }

        function showInvalidRegister($inMessg){
                $this->getEngine()->assign('message', $inMessg);            
		$this->render($this->getTpl('invalidRegister'));            
        }
        
	/**
	 * Shows the invalid user and the message
	 *
	 * @return void
	 */
	function showInvalidUser() {
		$this->render($this->getTpl('invalidUser'));
	}

	/**
	 * Shows the accountDetail page
	 *
	 * @return void
	 */
	function showUserDetailPage() {
		$this->getEngine()->assign('oUser', utilityOutputWrapper::wrap($this->getModel()->getUser()));

		$this->render($this->getTpl('accountDetail'));
	}
}