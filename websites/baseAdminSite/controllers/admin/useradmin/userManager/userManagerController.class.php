<?php
/**
 * userManagerController
 *
 * Stored in userManagerController.class.php
 * 
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category userManagerController
 * @version $Rev: 178 $
 */


/**
 * userManagerController
 *
 * userManagerController.class
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category userManagerController
 */
class userManagerController extends mvcDaoController {

	/**
	 * Override the DAO launch to add in client lock down
	 * 
	 * @todo: DR possibly refactor?
	 * @return void
	 */
	function launch() {
		$primaryKey = $this->getActionFromRequest(false, 1);
		if ( is_numeric($primaryKey) ) {
			$oUser = $this->getRequest()->getSession()->getUser();
			$oObject = mofilmUserManager::getInstanceByID($primaryKey);
			
			if (
				$oUser->getPermissions()->isRoot() ||
				$oUser->getClientID() == mofilmClient::MOFILM ||
				($oObject instanceof mofilmUser && $oUser->getClientID() == $oObject->getClientID())
			) {
				parent::launch();
			} else {
				$msg = 'You are not permitted to continue with the action - this has been logged';
				$this->getRequest()->getSession()->setStatusMessage($msg, mvcSession::MESSAGE_ERROR);
				
				$msg = 'User tried to edit record '.$primaryKey.', but is not authorised';
				$this->buildActivityLog($msg)->save();
				systemLog::error($msg);
				
				$oView = new $this->_ControllerView($this);
				$oView->showDaoDeniedPage();
			}
			
			$oObject = null;
			unset($oObject);
		} else {
			parent::launch();
		}
	}
	
	/**
	 * Handles creating a new user as a step wise process
	 * 
	 * @return void
	 */
	function actionNew() {
		$oView = new $this->_ControllerView($this);
		$data = $this->getInputManager()->doFilter();
		if ( !array_key_exists('Email', $data) || strlen($data['Email']) < 2 ) {
			
			$oMenuItem = $this->getMenuItems()->getItem(self::ACTION_NEW);
			$oMenuItem->reset();
			$oMenuItem->addItem(
				new mvcControllerMenuItem(
					$this->buildUriPath(self::ACTION_NEW), 'Next', self::IMAGE_ACTION_NEXT, 'Next', true, mvcControllerMenuItem::PATH_TYPE_URI, true
				)
			);
			$oMenuItem->addItem(
				new mvcControllerMenuItem(
					$this->buildUriPath(self::ACTION_VIEW), 'Cancel', self::IMAGE_ACTION_CANCEL, 'Cancel new record', false, mvcControllerMenuItem::PATH_TYPE_URI
				)
			);
			
			$oView->showNewUserForm();
		} else {
			try {
				$oUser = mofilmUserManager::getInstance()->setLoadOnlyActive(false)->getUserByUsername($data['Email']);
				if ( $oUser instanceof mofilmUser && $oUser->getID() > 0 ) {
					if ( $oUser->getPermissionGroup()->getID() > 0 || $oUser->getPermissions()->isRoot() ) {
						$this->getModel()->setID($oUser->getID());
						throw new mvcControllerException('The user '.$data['Email'].' is already enabled for admin access.');
					} else {
						$this->getModel()->getExistingObject($oUser->getID());
					}
				}
				$this->getModel()->setEmail($data['Email']);
				
				$msg = 'User is creating a new '.get_class($this->getModel());
				$this->buildActivityLog($msg)->save();
				
				$oView->showNewUserSettingsForm();
				
			} catch ( mvcControllerException $e ) {
				systemLog::error($e->getMessage());
				$oMenuItem = $this->getMenuItems()->getItem(self::ACTION_NEW);
				$oMenuItem->reset();
				$oMenuItem->addItem(
					new mvcControllerMenuItem(
						$this->buildUriPath(self::ACTION_VIEW), 'Cancel', self::IMAGE_ACTION_CANCEL, 'Cancel new record', false, mvcControllerMenuItem::PATH_TYPE_URI
					)
				);
				$oView->showNewUserError();
				
			} catch ( Exception $e ) {
				systemLog::error($e->getMessage());
				$this->getRequest()->getSession()->setStatusMessage($e->getMessage(), mvcSession::MESSAGE_ERROR);
				$this->redirect($this->buildUriPath(self::ACTION_VIEW));
			}
		}
	}

	/**
	 * Handles creating a new object and storing it
	 * 
	 * @return void
	 */
	function actionDoNew() {
		try {
			$this->buildModel();

			$this->getInputManager()->setLookupGlobals(utilityInputManager::LOOKUPGLOBALS_POST);
			$data = $this->getInputManager()->doFilter();

			$this->addInputToModel($data, $this->getModel());
			$this->getModel()->save();

			$msg = 'New '.get_class($this->getModel()).' with ID '.$this->getModel()->getPrimaryKey().' created successfully';
			$this->buildActivityLog($msg)->save();

			systemLog::notice($msg);
			$this->getRequest()->getSession()->setStatusMessage($msg, mvcSession::MESSAGE_OK);

			$this->redirect($this->buildUriPath(self::ACTION_VIEW));

		} catch (Exception $e) {
			systemLog::error(__CLASS__.'::'.__FUNCTION__.' '.$e->getMessage());
			$this->buildActivityLog(
				$this->getRequest()->getSession()->getUser()->getUsername().' tried store a new object but it failed with error: '.$e->getMessage()
			)->save();

			$this->setAction(self::ACTION_NEW);
			$this->getRequest()->getSession()->setStatusMessage($e->getMessage(), mvcSession::MESSAGE_ERROR);

			$oView = new $this->_ControllerView($this);
			$oView->showNewUserSettingsForm();
		}
	}
	
	/**
	 * Handles listing objects and search options
	 * 
	 * @return void
	 */
	function actionView() {
		$this->getInputManager()->setLookupGlobals(utilityInputManager::LOOKUPGLOBALS_GET);
		$this->getInputManager()->addFilter('ClientID', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('Email', utilityInputFilter::filterString());
		$data = $this->getInputManager()->doFilter();
		
		$oUser = $this->getRequest()->getSession()->getUser();
		if ( $oUser->getPermissions()->isRoot() || $oUser->getClientID() == mofilmClient::MOFILM ) {
			$this->setSearchOptionFromRequestData($data, 'ClientID');
			$this->setSearchOptionFromRequestData($data, 'Email');
			
			$this->getMenuItems()->getItem(self::ACTION_VIEW)->addItem(
				new mvcControllerMenuItem(
					$this->buildUriPath(self::ACTION_SEARCH), 'Search', self::IMAGE_ACTION_SEARCH, 'Search', false, mvcControllerMenuItem::PATH_TYPE_URI, true
				)
			);
		} else {
			$this->addSearchParameter('ClientID', $oUser->getClientID());
		}
		
		parent::actionView();
	}
	
	/**
	 * @see mvcControllerBase::initialise()
	 */
	function initialise() {
		parent::initialise();
		
		$this->setControllerView('userManagerView');
	}
	
	/**
	 * @see mvcControllerBase::addInputFilters()
	 */
	function addInputFilters() {
		$this->getInputManager()->addFilter('PrimaryKey', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('ClientID', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('Email', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Password', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Enabled', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('TerritoryID', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('Firstname', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Surname', utilityInputFilter::filterString());
		
		$this->getInputManager()->addFilter('Properties', utilityInputFilter::filterStringArray());
		$this->getInputManager()->addFilter('Permissions', utilityInputFilter::filterStringArray());
		$this->getInputManager()->addFilter('PermissionGroupID', utilityInputFilter::filterInt());
	}
	
	/**
	 * @see mvcControllerBase::addInputToModel()
	 * 
	 * @param array $inData
	 * @param userManagerModel $inModel
	 */
	function addInputToModel($inData, $inModel) {
		if ( $this->getAction() == self::ACTION_DO_NEW ) {
			$oUser = mofilmUserManager::getInstance()->setLoadOnlyActive(false)->getUserByUsername($inData['Email']);
			if ( $oUser instanceof mofilmUser && $oUser->getID() > 0 ) {
				$inModel->getExistingObject($oUser->getID());
				if ( $oUser->getPermissionGroup()->getID() == -1 ) {
					$inModel->setNewAdminUser(true);
				}
			}
		} else {
			$inModel->setID($inData['PrimaryKey']);
		}
		
		$inModel->setEmail($inData['Email']);
		$inModel->setTerritoryID($inData['TerritoryID']);
		$inModel->setFirstname(trim(strip_tags($_POST['Firstname'])));
		$inModel->setSurname(trim(strip_tags($_POST['Surname'])));
		
		if ( $this->hasAuthority('admin.userManagerController.canChangeClient') ) {
			$inModel->setClientID($inData['ClientID']);
		}
		
		if (
			$this->hasAuthority('admin.userManagerController.canChangeStatus') &&
			array_key_exists('Enabled', $inData) && in_array($inData['Enabled'], array('Y', 'N'))
		) {
			$inModel->setEnabled($inData['Enabled']);
		}
		
		if ( $this->getAction() == self::ACTION_DO_NEW && $this->hasAuthority('admin.userManagerController.canChangePassword') ) {
                        if(isset($inData['Password'])){
                            
                            if ( isset($inData['Password']) && strlen($inData['Password']) >= 5 ) {
                                    $inModel->setPassword($inData['Password']);
                            } else {
                                    throw new mvcControllerException('The password should be 5 characters or more');                            
                            }
                        }
		}
                
		if (  $this->hasAuthority('admin.userManagerController.canChangePassword') && self::ACTION_DO_EDIT ) {
                        if(strlen($inData['Password']) > 0){
                            
                            if ( isset($inData['Password']) && strlen($inData['Password']) >= 5 ) {
                                    $inModel->setPassword($inData['Password']);
                            } else {
                                    throw new mvcControllerException('The password should be 5 characters or more');                            
                            }
                        }
		}
                
		
		if ( $this->hasAuthority('admin.userManagerController.canEditUserProperties') ) {
			if ( isset($_POST['Properties']) && is_array($_POST['Properties']) && count($_POST['Properties']) > 0 ) {
				foreach ( $_POST['Properties'] as $propName => $value ) {
					$inModel->getParamSet()->setParam($propName, trim(strip_tags($value)));
				}
			}
		}
		
		if ( $this->hasAuthority('admin.userManagerController.canEditUserPermissions')) {
			/*
			 * Always delete user permissions - ensures nothing gets left over
			 */
			$inModel->getPermissions()->delete();
			$inModel->getPermissions()->reset();
			
			/*
			 * Fixes a bug where permissions might not be saved if they have not changed
			 */
			$inModel->getPermissions()->setModified(true);
			
			if ( isset($inData['Permissions']) && is_array($inData['Permissions']) && count($inData['Permissions']) > 0 ) {
				foreach ( $inData['Permissions'] as $permission ) {
					$inModel->getPermissions()->addPermission($permission, mofilmUserPermissions::PERMISSION_SOURCE_USER);
				}
			}
		}
		
		/*
		 * Set the users permission group - root, only allowed to be set by current root users
		 * 
		 * If not set, ensure current group is reset (mofilmUserPermissions will delete all permissions),
		 * but only do that if it is not a new user record.
		 */
		if ( $this->hasAuthority('admin.userManagerController.canChangeGroup') && array_key_exists('PermissionGroupID', $inData) ) {
			if ( $inData['PermissionGroupID'] == 0 && $this->getRequest()->getSession()->getUser()->getPermissions()->isRoot() ) {
				$inModel->getPermissions()->addPermission('root', mofilmUserPermissions::PERMISSION_SOURCE_USER);
			} elseif ( $inData['PermissionGroupID'] > 0 ) {
				$inModel->getPermissions()->removePermission('root');
				$inModel->getPermissions()->addPermission('G'.$inData['PermissionGroupID'], mofilmUserPermissions::PERMISSION_SOURCE_GROUP);
			}
		} else {
			if ( $this->getAction() != self::ACTION_DO_NEW ) {
				if ( $inModel->getPermissionGroup()->getID() > 0 ) {
					$inModel->getPermissions()->addPermission('G'.$inModel->getPermissionGroup()->getID(), mofilmUserPermissions::PERMISSION_SOURCE_GROUP);
				}
			}
		}
		
		/*
		 * For new records, ensure clientID is inherited from creator on non-Mofilm records
		 */
		if ( $this->getAction() == self::ACTION_DO_NEW ) {
			if ( !$this->hasAuthority('admin.userManagerController.canChangeClient') ) {
				$inModel->setClientID($this->getRequest()->getSession()->getUser()->getClientID());
			}
		}
	}
	
	/**
	 * Builds the model
	 *
	 * @return void
	 */
	function buildModel() {
		$oModel = new userManagerModel();
		$oModel->setCurrentUser($this->getRequest()->getSession()->getUser());
		$oModel->setLanguage($this->getRequest()->getLocale());
		$this->setModel($oModel);
	}
}