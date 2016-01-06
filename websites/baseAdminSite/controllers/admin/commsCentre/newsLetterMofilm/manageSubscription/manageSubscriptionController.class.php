<?php
/**
 * manageSubscriptionController
 *
 * Stored in manageSubscriptionController.class.php
 * 
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category manageSubscriptionController
 * @version $Rev: 624 $
 */


/**
 * manageSubscriptionController
 *
 * manageSubscriptionController class
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category manageSubscriptionController
 */
class manageSubscriptionController extends mvcDaoController {

	const ACTION_ADD = 'Manage List';
	const ACTION_USER_SEARCH = 'userSearch';
	const ACTION_USER_ADD = 'userAdd';
	const ACTION_USER_DELETE = 'userDelete';
	
	/**
	 * @see mvcControllerBase::initialise()
	 */
	function initialise() {
		parent::initialise();
		
		$this->setControllerView('manageSubscriptionView');
		$this->getControllerActions()->addAction(self::ACTION_ADD);
		$this->getControllerActions()->addAction(self::ACTION_USER_SEARCH);
		$this->getControllerActions()->addAction(self::ACTION_USER_ADD);
		$this->getControllerActions()->addAction(self::ACTION_USER_DELETE);
		$this->getMenuItems()->getItem(self::ACTION_VIEW)->addItem( new mvcControllerMenuItem( $this->buildUriPath(self::ACTION_ADD, ""), 'Manage List', self::ACTION_ADD, 'Manage List', false, mvcControllerMenuItem::PATH_TYPE_URI));
	}

	/**
	 * @see mvcControllerBase::launch()
	 */
	function launch() {
		switch( $this->getAction() ) {
			case self::ACTION_ADD:	       $this->actionAdd();	  break;
			case self::ACTION_USER_SEARCH: $this->actionUserSearch(); break;
			case self::ACTION_USER_ADD:    $this->actionUserAdd();    break;
			case self::ACTION_USER_DELETE: $this->actionUserDelete(); break;
			default :		       parent::launch();
		}
	}

	/**
	 * Handles the search functionality and calls the parent function
	 *
	 * @return void
	 */
	function actionView() {
		$this->getInputManager()->setLookupGlobals(utilityInputManager::LOOKUPGLOBALS_GET);
		$this->getInputManager()->addFilter('subListID', utilityInputFilter::filterString());
		$data = $this->getInputManager()->doFilter();
		$this->setSearchOptionFromRequestData($data, 'subListID');
		parent::actionView();
	}


	/**
	 * @see mvcControllerBase::addInputFilters()
	 */
	function addInputFilters() {
		$this->getInputManager()->addFilter('PrimaryKey', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('EmailID', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('ListID', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('Subscribed', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Hash', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Keyword', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('UserEmail', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('UserID', utilityInputFilter::filterInt());
	}
	
	/**
	 * @see mvcControllerBase::addInputToModel()
	 *
	 * @param array $inData
	 * @param manageSubscriptionModel $inModel
	 */
	function addInputToModel($inData, $inModel) {
		//$inModel->setPrimaryKey($inData['PrimaryKey']);
		$inModel->setEmailID($inData['PrimaryKey']);
		$inModel->setListID($inData['ListID']);
		$inModel->setSubscribed($inData['Subscribed']);
		$inModel->setHash($inData['Hash']);
		$inModel->setUserEamil($inData['UserEmail']);
		$inModel->setUserID($inData['UserID']);
		$inModel->setKeyword($inData['Keyword']);
	}
	
	/**
	 * Builds the model
	 *
	 * @return void
	 */
	function buildModel() {
		$oModel = new manageSubscriptionModel();
		$oModel->setCurrentUser($this->getRequest()->getSession()->getUser());
		$this->setModel($oModel);
	}

	/**
	 * Handles diaply of the Adding/Deleting user from the list functionality
	 *
	 * @return void
	 */
	function actionAdd() {
		$this->getMenuItems()->reset();
		$oView = new manageSubscriptionView($this);
		$oView->getAddUserToList();
	}

	/**
	 * Handles searching of the user from a particular domain
	 *
	 * @return void 
	 */
	function actionUserSearch() {
		$this->getMenuItems()->reset();
		$this->addInputFilters();
		$data = $this->getInputManager()->doFilter();
		$this->addInputToModel($data, $this->getModel());
		$this->getModel()->getUsersResult();
		$oView = new manageSubscriptionView($this);
		$oView->showAddUserToList();
	}

	/**
	 * Handles adding a user to the subscription list
	 *
	 * @return void
	 */
	function actionUserAdd() {
		$this->getInputManager()->setLookupGlobals(utilityInputManager::LOOKUPGLOBALS_GET);
		$data = $this->getInputManager()->doFilter();
		$this->addInputToModel($data, $this->getModel());
		if ( $this->getModel()->addUserToSubsList() ) {
			$oView = new manageSubscriptionView($this);
			$oView->showMessageUserAdded();
		} else {
			$oView = new manageSubscriptionView($this);
			$oView->showMessageUserError();
		}
	}

	/**
	 * Handles deleting a user from the subscription list
	 * 
	 * @return void 
	 */
	function actionUserDelete() {
		$this->getInputManager()->setLookupGlobals(utilityInputManager::LOOKUPGLOBALS_GET);
		$data = $this->getInputManager()->doFilter();
		$this->addInputToModel($data, $this->getModel());
		if ( $this->getModel()->deleteUserToSubsList() ) {
			$oView = new manageSubscriptionView($this);
			$oView->showMessageUserDeleted();
		} else {
			$oView = new manageSubscriptionView($this);
			$oView->showMessageUserDeleteError();
		}
	}
}