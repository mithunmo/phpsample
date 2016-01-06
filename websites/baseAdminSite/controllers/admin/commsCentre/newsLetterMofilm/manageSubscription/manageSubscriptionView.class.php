<?php
/**
 * manageSubscriptionView.class.php
 * 
 * manageSubscriptionView class
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category manageSubscriptionView
 * @version $Rev: 624 $
 */


/**
 * manageSubscriptionView class
 * 
 * Provides the "manageSubscriptionView" page
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category manageSubscriptionView
 */
class manageSubscriptionView extends mvcDaoView {

	/**
	 * @see mvcDaoView::assignCustomViewData()
	 */
	function assignCustomViewData() {
		$this->getEngine()->assign('parentController', 'admin');
		$this->getEngine()->assign('oList', utilityOutputWrapper::wrap(mofilmCommsListType::listOfObjects()));
	}
	
	/**
	 * @see mvcDaoView::getObjectListView()
	 */
	function getObjectListView() {
		return $this->getTpl('manageSubscriptionList');
	}
	
	/**
	 * @see mvcDaoView::getObjectFormView()
	 */
	function getObjectFormView() {
		return $this->getTpl('manageSubscriptionForm');
	}

	/**
	 * gets the Add User list page and accepts the keyword
	 */
	function getAddUserToList() {
		$this->getEngine()->assign('oModel',$this->getModel());
		$this->getEngine()->assign('action',"get");
		$this->getEngine()->assign('formAction',"/admin/commsCentre/newsLetterMofilm/manageSubscription/userSearch/");
		$this->getEngine()->assign('oResult',  utilityOutputWrapper::wrap($this->getModel()->getSearchResult()));
		$this->getEngine()->assign('oList', utilityOutputWrapper::wrap(mofilmCommsListType::listOfObjects()));
		$this->render($this->getTpl('addUserToList'));
	}

	/**
	 * shows the list of users based on the keyword
	 *
	 * @param array $inSearchResult
	 */
	function showAddUserToList() {
		$this->getEngine()->assign('oModel',$this->getModel());
		$this->getEngine()->assign('action',"show");
		$this->getEngine()->assign('oResult',  utilityOutputWrapper::wrap($this->getModel()->getSearchResult()));
		$this->getEngine()->assign('oList', utilityOutputWrapper::wrap(mofilmCommsListType::listOfObjects()));
		$this->render($this->getTpl('addUserToList'));
	}

	/**
	 * Sends back the user added message to the json request 
	 */
	function showMessageUserAdded() {
		$arr = array();
		$arr['name'] = "User Added";
		$response = json_encode($arr);
		echo $response;
	}

	/**
	 * Sends back the user already added message to json request
	 */
	function showMessageUserError() {
		$arr = array();
		$arr['name'] = "User Already Added";
		$response = json_encode($arr);
		echo $response;
	}

	/**
	 * Sends back the user deleted message to json request
	 */
	function showMessageUserDeleted() {
		$arr = array();
		$arr['name'] = "User Deleted";
		$response = json_encode($arr);
		echo $response;
	}

	/**
	 * Sends back the user not found message to the json request
	 */
	function showMessageUserDeleteError() {
		$arr = array();
		$arr['name'] = "User not found";
		$response = json_encode($arr);
		echo $response;
	}



}