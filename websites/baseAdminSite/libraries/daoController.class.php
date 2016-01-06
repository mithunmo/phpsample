<?php
/**
 * mvcDaoController.class.php
 *
 * mvcDaoController class
 *
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage websites_baseAdminSite_libraries
 * @category mvcDaoController
 */


/**
 * mvcDaoController
 *
 * Mofilm admin controller for administering DAO objects in a unified manner.
 * This is based on the Scorpio baseAdminSite plugins mvcDaoController system.
 *
 * @package mofilm
 * @subpackage websites_baseAdminSite_libraries
 * @category mvcDaoController
 */
abstract class mvcDaoController extends mvcController {

	const ACTION_SEARCH = 'search';
	const ACTION_VIEW = 'viewObjects';
	const ACTION_NEW = 'newObject';
	const ACTION_EDIT = 'editObject';
	const ACTION_DELETE = 'deleteObject';
	const ACTION_DO_NEW = 'doNewObject';
	const ACTION_DO_EDIT = 'doEditObject';
	const ACTION_DO_DELETE = 'doDeleteObject';
	const ACTION_DENIED = 'denied';
	
	const IMAGE_ACTION_VIEW = 'action-view-objects';
	const IMAGE_ACTION_SEARCH = 'search';
	const IMAGE_ACTION_RESET = 'action-undo';
	const IMAGE_ACTION_CANCEL = 'action-cancel';
	const IMAGE_ACTION_NEXT = 'action-next';
	const IMAGE_ACTION_NEW = 'action-new-object';
	const IMAGE_ACTION_EDIT = 'action-edit-object';
	const IMAGE_ACTION_DELETE = 'action-delete-object';
	const IMAGE_ACTION_DO_NEW = 'action-do-new-object';
	const IMAGE_ACTION_DO_EDIT = 'action-do-edit-object';
	const IMAGE_ACTION_DO_DELETE = 'action-do-delete-object';

	const VIEW_LIST = 'list';
	const VIEW_FORM = 'form';
	const VIEW_ACTIONS = 'actions';

	const PAGING_VAR_OFFSET = 'offset';
	const PAGING_VAR_LIMIT = 'limit';
	const PAGING_DEFAULT_LIMIT = 20;

	/**
	 * Stores $_ControllerView
	 *
	 * @var string
	 * @access protected
	 */
	protected $_ControllerView;

	/**
	 * Stores $_MenuItems
	 *
	 * @var mvcControllerMenuItems
	 * @access protected
	 */
	protected $_MenuItems;

	/**
	 * Stores $_PrimaryKey
	 *
	 * @var string
	 * @access protected
	 */
	protected $_PrimaryKey;

	/**
	 * Stores $_PagingOptions
	 *
	 * @var array
	 * @access protected
	 */
	protected $_PagingOptions;
	
	/**
	 * Stores $_SearchOptions
	 *
	 * @var baseOptionsSet
	 * @access protected
	 */
	protected $_SearchOptions;



	/**
	 * Builds the model for the controller
	 *
	 * @return mvcDaoModel
	 * @abstract
	 */
	abstract function buildModel();

	/**
	 * @see mvcControllerBase::initialise()
	 */
	function initialise() {
		parent::initialise();

		$this->setDefaultAction(self::ACTION_VIEW);
		$this->setControllerView('mvcDaoView');
		$this->setPrimaryKey(null);
		$this->setPagingOptions(
			array(
				self::PAGING_VAR_OFFSET => null,
				self::PAGING_VAR_LIMIT => self::PAGING_DEFAULT_LIMIT
			)
		);

		/*
		 * Register actions
		 */
		$this->getControllerActions()
			->addAction(self::ACTION_VIEW)
			->addAction(self::ACTION_NEW)
			->addAction(self::ACTION_EDIT)
			->addAction(self::ACTION_DELETE)
			->addAction(self::ACTION_DO_NEW)
			->addAction(self::ACTION_DO_DELETE)
			->addAction(self::ACTION_DO_EDIT);

		/*
		 * Register standalone views
		 */
		$this->getControllerViews()
			->addView(self::VIEW_ACTIONS)
			->addView(self::VIEW_FORM)
			->addView(self::VIEW_LIST);

		/*
		 * Register basic menu items
		 *
		 * First item is the list view
		 */
		$oItem = new mvcControllerMenuItem(self::ACTION_VIEW, 'View', self::IMAGE_ACTION_VIEW, 'Default view');
		$oItem->addItem(
			new mvcControllerMenuItem(
				$this->buildUriPath(self::ACTION_VIEW), 'Refresh', self::IMAGE_ACTION_VIEW, 'Refresh list', false, mvcControllerMenuItem::PATH_TYPE_URI
			)
		);
		$oItem->addItem(
			new mvcControllerMenuItem(
				$this->buildUriPath(self::ACTION_NEW), 'New', self::IMAGE_ACTION_NEW, 'Create a new record', false, mvcControllerMenuItem::PATH_TYPE_URI
			)
		);
		$this->getMenuItems()->addItem($oItem);

		/*
		 * Add actions for a "new object" view
		 */
		$oItem = new mvcControllerMenuItem(self::ACTION_NEW, 'New', self::IMAGE_ACTION_NEW, 'Create a new record');
		$oItem->addItem(
			new mvcControllerMenuItem(
				$this->buildUriPath(self::ACTION_DO_NEW), 'Save', self::IMAGE_ACTION_DO_NEW, 'Save record', true, mvcControllerMenuItem::PATH_TYPE_URI, true
			)
		);
		$oItem->addItem(
			new mvcControllerMenuItem(
				$this->buildUriPath(self::ACTION_VIEW), 'Cancel', self::IMAGE_ACTION_CANCEL, 'Cancel new record', false, mvcControllerMenuItem::PATH_TYPE_URI
			)
		);
		$this->getMenuItems()->addItem($oItem);

		/*
		 * Add actions for the edit action view
		 */
		$oItem = new mvcControllerMenuItem(self::ACTION_EDIT, 'Edit', self::IMAGE_ACTION_EDIT, 'Edit the record');
		$oItem->addItem(
			new mvcControllerMenuItem(
				$this->buildUriPath(self::ACTION_DO_EDIT), 'Save', self::IMAGE_ACTION_DO_EDIT, 'Save record', true, mvcControllerMenuItem::PATH_TYPE_URI, true
			)
		);
		$oItem->addItem(
			new mvcControllerMenuItem(
				'reset', 'Reset', self::IMAGE_ACTION_RESET, 'Undo changes', false, mvcControllerMenuItem::PATH_TYPE_URI, true
			)
		);
		$oItem->addItem(
			new mvcControllerMenuItem(
				$this->buildUriPath(self::ACTION_VIEW), 'Cancel', self::IMAGE_ACTION_CANCEL, 'Cancel edit record', false, mvcControllerMenuItem::PATH_TYPE_URI
			)
		);
		$this->getMenuItems()->addItem($oItem);

		/*
		 * Add actions for the delete object view
		 */
		$oItem = new mvcControllerMenuItem(self::ACTION_DELETE, 'Delete', self::IMAGE_ACTION_DELETE, 'Delete the record');
		$oItem->addItem(
			new mvcControllerMenuItem(
				$this->buildUriPath(self::ACTION_DO_DELETE), 'Delete', self::IMAGE_ACTION_DO_DELETE, 'Delete record', true, mvcControllerMenuItem::PATH_TYPE_URI, true
			)
		);
		$oItem->addItem(
			new mvcControllerMenuItem(
				$this->buildUriPath(self::ACTION_VIEW), 'Cancel', self::IMAGE_ACTION_CANCEL, 'Cancel delete record', false, mvcControllerMenuItem::PATH_TYPE_URI
			)
		);
		$this->getMenuItems()->addItem($oItem);
	}



	/**
	 * Builds an activity log object
	 *
	 * @param string $inDescription
	 * @return systemUserActivityLog
	 */
	function buildActivityLog($inDescription) {
		$oLog = new mofilmUserLog();
		$oLog->setUserID($this->getRequest()->getSession()->getUser()->getID());
		$oLog->setDescription($inDescription);
		$oLog->setType(mofilmUserLog::TYPE_OTHER);

		return $oLog;
	}

	/**
	 * @see mvcControllerBase::launch()
	 */
	function launch() {
		switch ( $this->getAction() ) {
			case self::ACTION_NEW:       $this->actionNew();      break;
			case self::ACTION_DO_NEW:    $this->actionDoNew();    break;
			case self::ACTION_EDIT:      $this->actionEdit();     break;
			case self::ACTION_DO_EDIT:   $this->actionDoEdit();   break;
			case self::ACTION_DELETE:    $this->actionDelete();   break;
			case self::ACTION_DO_DELETE: $this->actionDoDelete(); break;

			case self::ACTION_VIEW:
			default:
				$this->actionView();
			break;
		}
	}

	/**
	 * Performs additional validation on the input data, returns true if OK, or an error string
	 *
	 * @param array $inData
	 * @return boolean
	 */
	function validateData($inData) {
		$message = '';
		try {
			if ( $this->getInputManager()->getFilterCount() == 0 ) {
				return $message;
			}

			$curLookup = $this->getInputManager()->getLookupGlobals();
			$this->getInputManager()->setLookupGlobals(utilityInputManager::LOOKUPGLOBALS_DATA);
			$data = $this->getInputManager()->doFilter($inData);
			$this->getInputManager()->setLookupGlobals($curLookup);

			$message = $this->validateInputData($data);
			if ( $message === true && count($data) == 0 && $this->getInputManager()->getFilterCount() > 0 ) {
				$message = 'Please complete at least one field on the form';
			}

			if ( $message === true ) {
				$oObject = $this->getExistingObject();
				$this->addInputToModel($data, $oObject);

				if ( $oObject instanceof systemDaoValidatorInterface ) {
					$oObject->isValid($message);
				}
			}
		} catch ( Exception $e ) {
			systemLog::error('Error while doing data check: '.$e->getMessage());
			$message = $e->getMessage();
		}
		return $message;
	}
	
	/**
	 * Handles listing objects
	 * 
	 * @return void
	 */
	function actionView() {
		$this->getInputManager()->setLookupGlobals(utilityInputManager::LOOKUPGLOBALS_GET);
		$this->getInputManager()->addFilter('Search', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Offset', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('Limit', utilityInputFilter::filterInt());
		$data = $this->getInputManager()->doFilter();
		
		if ( isset($data['Offset']) && $data['Offset'] >= 0 ) {
			$offset = $data['Offset'];
			$limit = (isset($data['Limit']) && $data['Limit'] > 0) ? $data['Limit'] : self::PAGING_DEFAULT_LIMIT;
		} else {
			if ( !array_key_exists('Search', $data) && $this->getRequest()->getSession()->getParam(get_class($this).'.offset') ) {
				$offset = $this->getRequest()->getSession()->getParam(get_class($this).'.offset');
				$limit = $this->getRequest()->getSession()->getParam(get_class($this).'.limit');
			} else {
				$offset = 0;
				$limit = self::PAGING_DEFAULT_LIMIT;
			}
		}
		$this->getRequest()->getSession()->setParam(get_class($this).'.offset', $offset);
		$this->getRequest()->getSession()->setParam(get_class($this).'.limit', $limit);

		$this->setPagingOptions(
			array(
				self::PAGING_VAR_OFFSET => $offset,
				self::PAGING_VAR_LIMIT => $limit
			)
		);

		$oView = new $this->_ControllerView($this);
		$oView->showDaoPage();
	}
	
	/**
	 * Handles a new object
	 * 
	 * @return void
	 */
	function actionNew() {
		$this->buildModel();
		
		$msg = 'User is creating a new '.get_class($this->getModel());
		$this->buildActivityLog($msg)->save();

		$oView = new $this->_ControllerView($this);
		$oView->showDaoPage();
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
			$oView->showDaoPage();
		}
	}
	
	/**
	 * Handles editing an object
	 * 
	 * @return void
	 */
	function actionEdit() {
		$primaryKey = $this->getActionFromRequest(false, 1);
		$this->setPrimaryKey($primaryKey);

		$msg = 'Editing object '.get_class($this->getModel()).' with ID '.$this->getPrimaryKey();
			
		$this->buildActivityLog($msg)->save();
		systemLog::notice($msg);

		try {
			$this->buildModel();
		} catch (Exception $e) {
			systemLog::error(__CLASS__.'::'.__FUNCTION__.' '.$e->getMessage());
			$this->getRequest()->getSession()->setStatusMessage($e->getMessage(), mvcSession::MESSAGE_ERROR);
		}
		$oView = new $this->_ControllerView($this);
		$oView->showDaoPage();
	}
	
	/**
	 * Handles committing changes to an edited object
	 * 
	 * @return void
	 */
	function actionDoEdit() {
		try {
			$primaryKey = $this->getActionFromRequest(false, 1);
			$this->setPrimaryKey($primaryKey);
			$this->getExistingObject();

			$this->getInputManager()->setLookupGlobals(utilityInputManager::LOOKUPGLOBALS_POST);
			$data = $this->getInputManager()->doFilter();

			$this->addInputToModel($data, $this->getModel());
			$this->getModel()->save();

			$msg = get_class($this->getModel()).' with ID '.$this->getModel()->getPrimaryKey().' successfully updated';
			$this->buildActivityLog($msg)->save();
			
			systemLog::notice($msg);
			$this->getRequest()->getSession()->setStatusMessage($msg, mvcSession::MESSAGE_OK);

			$this->redirect($this->buildUriPath(self::ACTION_VIEW));

		} catch (Exception $e) {
			systemLog::error(__CLASS__.':'.__FUNCTION__.' '.$e->getMessage());
			$this->setAction(self::ACTION_EDIT);
			$this->buildActivityLog(
				$this->getRequest()->getSession()->getUser()->getUsername().' tried to update object but it failed with error: '.$e->getMessage()
			)->save();

			$this->getRequest()->getSession()->setStatusMessage($e->getMessage(), mvcSession::MESSAGE_ERROR);

			$oView = new $this->_ControllerView($this);
			$oView->showDaoPage();
		}
	}
	
	/**
	 * Handles delete requests
	 * 
	 * @return void
	 */
	function actionDelete() {
		$primaryKey = $this->getActionFromRequest(false, 1);
		$this->setPrimaryKey($primaryKey);

		try {
			$this->buildModel();
			systemLog::notice('Delete called for object '.get_class($this->getModel()).' with ID '.$this->getPrimaryKey());
			
		} catch (Exception $e) {
			systemLog::error(__CLASS__.'::'.__FUNCTION__.' '.$e->getMessage());
			$this->getRequest()->getSession()->setStatusMessage($e->getMessage(), mvcSession::MESSAGE_ERROR);
		}

		$oView = new $this->_ControllerView($this);
		$oView->showDaoPage();
	}
	
	/**
	 * Actually deletes an object from the system
	 * 
	 * @return void
	 */
	function actionDoDelete() {
		try {
			$primaryKey = $this->getActionFromRequest(false, 1);
			$this->setPrimaryKey($primaryKey);

			$oModel = $this->getExistingObject();
			$oModel->delete();

			$msg = get_class($this->getModel()).' with ID '.$primaryKey.' removed successfully';
			$this->buildActivityLog($msg)->save();
			systemLog::notice($msg);

			$this->getRequest()->getSession()->setStatusMessage($msg, mvcSession::MESSAGE_OK);

		} catch (Exception $e) {
			systemLog::error(__CLASS__.':'.__FUNCTION__.' '.$e->getMessage());
			$msg = $this->getRequest()->getSession()->getUser()->getUsername().' tried to remove an object but it failed with error: '.$e->getMessage();
			$this->buildActivityLog($msg)->save();

			$this->getRequest()->getSession()->setStatusMessage($e->getMessage(), mvcSession::MESSAGE_ERROR);
		}

		$this->redirect($this->buildUriPath(self::ACTION_VIEW));
	}



	/*
	 * Get / set methods
	 */

	/**
	 * @see mvcControllerBase::getModel()
	 *
	 * @return mvcDaoModelInterface
	 */
	function getModel() {
		if ( !parent::getModel() ) {
			$this->buildModel();
		}
		return parent::getModel();
	}

	/**
	 * Returns an instance of an existing object
	 *
	 * @return systemDaoInterface
	 */
	function getExistingObject() {
		return $this->getModel()->getExistingObject($this->getPrimaryKey());
	}

	/**
	 * Returns $_ControllerView
	 *
	 * @return string
	 */
	function getControllerView() {
		return $this->_ControllerView;
	}

	/**
	 * Set $_ControllerView to $inControllerView
	 *
	 * @param string $inControllerView
	 * @return mvcDaoController
	 */
	function setControllerView($inControllerView) {
		if ( $inControllerView !== $this->_ControllerView ) {
			$this->_ControllerView = $inControllerView;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_MenuItems
	 *
	 * @return mvcControllerMenuItems
	 */
	function getMenuItems() {
		if ( !$this->_MenuItems instanceof mvcControllerMenuItems ) {
			$this->_MenuItems = new mvcControllerMenuItems();
		}
		return $this->_MenuItems;
	}

	/**
	 * Set $_MenuItems to $inMenuItems
	 *
	 * @param mvcControllerMenuItems $inMenuItems
	 * @return mvcDaoController
	 */
	function setMenuItems(mvcControllerMenuItems $inMenuItems) {
		if ( $inMenuItems !== $this->_MenuItems ) {
			$this->_MenuItems = $inMenuItems;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_PrimaryKey
	 *
	 * @return string
	 */
	function getPrimaryKey() {
		return $this->_PrimaryKey;
	}

	/**
	 * Set $_PrimaryKey to $inPrimaryKey
	 *
	 * @param string $inPrimaryKey
	 * @return mvcDaoController
	 */
	function setPrimaryKey($inPrimaryKey) {
		if ( $inPrimaryKey !== $this->_PrimaryKey ) {
			$this->_PrimaryKey = $inPrimaryKey;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_PagingOptions, or optionally var named $inVarName, returns false if not found
	 *
	 * @param string $inVarName
	 * @return array(offset, limit, var...)
	 */
	function getPagingOptions($inVarName = null) {
		if ( $inVarName !== null ) {
			if ( array_key_exists($inVarName, $this->_PagingOptions) ) {
				return $this->_PagingOptions[$inVarName];
			} else {
				return false;
			}
		}
		return $this->_PagingOptions;
	}

	/**
	 * Set $_PagingOptions to $inPagingOptions
	 *
	 * @param array $inPagingOptions
	 * @return mvcDaoController
	 */
	function setPagingOptions(array $inPagingOptions = array()) {
		if ( $inPagingOptions !== $this->_PagingOptions ) {
			$this->_PagingOptions = $inPagingOptions;
			$this->setModified();
		}
		return $this;
	}

	
	
	/**
	 * Returns the search options
	 *
	 * @return baseOptionsSet
	 */
	function getSearchOptions() {
		if ( !$this->_SearchOptions instanceof baseOptionsSet ) {
			$this->_SearchOptions = new baseOptionsSet();
		}
		return $this->_SearchOptions;
	}
	
	/**
	 * Adds a search param to the array of search options
	 * 
	 * @param string $inOption
	 * @param mixed $inValue Value, either int, string, bool or array - objects not allowed
	 * @return mvcDaoController
	 */
	function addSearchParameter($inOption, $inValue) {
		$this->getSearchOptions()->setOptions(array($inOption => $inValue));
		return $this;
	}
	
	/**
	 * Returns the search option $inOption, or $inDefault if not found
	 * 
	 * @param string $inOption
	 * @param mixed $inDefault (optional) default 'null'
	 * @return mixed
	 */
	function getSearchParameter($inOption, $inDefault = null) {
		return $this->getSearchOptions()->getOptions($inOption, $inDefault);
	}
	
	/**
	 * Removes the search option $inOption
	 * 
	 * @param string $inOption
	 * @return mvcDaoController
	 */
	function removeSearchParameter($inOption) {
		$this->getSearchOptions()->removeOptions(array($inOption));
		return $this;
	}
	
	/**
	 * Sets an array of search options
	 *
	 * @param array $inSearchOptions
	 * @return mvcDaoController
	 */
	function setSearchOptions(array $inSearchOptions) {
		$this->getSearchOptions()->setOptions($inSearchOptions);
		return $this;
	}
	
	/**
	 * Sets a search option from a processed array of request data
	 * 
	 * $inData comes from the InputManager instance and is filtered before passed
	 * to this method. $inVarName is the string name of the request var to map to
	 * the search options.
	 * 
	 * @param array $inData
	 * @param string $inVarName
	 * @return mvcDaoController
	 */
	function setSearchOptionFromRequestData(array $inData, $inVarName) {
		if ( array_key_exists($inVarName, $inData) && !is_null($inData[$inVarName]) ) {
			$value = $inData[$inVarName];
		} else {
			if ( $this->getRequest()->getSession()->getParam(get_class($this).'.'.$inVarName) ) {
				$value = $this->getRequest()->getSession()->getParam(get_class($this).'.'.$inVarName);
			} else {
				$value = null;
			}
		}
		$this->getRequest()->getSession()->setParam(get_class($this).'.'.$inVarName, $value);
		
		$this->addSearchParameter($inVarName, $value);
		
		return $this;
	}
	
	/**
	 * Converts the search options to URI parameters
	 * 
	 * @return string
	 */
	function getSearchQueryAsUriString() {
		if ( $this->getSearchOptions()->getCount() > 0 ) {
			return http_build_query($this->getSearchOptions()->getOptions());
		}
		return '';
	}
}