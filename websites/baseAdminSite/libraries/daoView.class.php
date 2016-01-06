<?php
/**
 * mvcDaoView.class.php
 * 
 * mvcDaoView class
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage websites_baseAdminSite_libraries
 * @category mvcDaoView
 */


/**
 * mvcDaoView
 * 
 * Main site mvcDaoView implementation, holds base directives and defaults for the site
 *
 * @package scorpio
 * @subpackage websites_baseAdminSite_libraries
 * @category mvcDaoView
 */
class mvcDaoView extends mvcView {
	
	/**
	 * @see mvcDaoViewBase::__construct()
	 */
	function __construct($inController) {
		parent::__construct($inController);

		$this->getEngine()->assign('daoUriView', $this->buildUriPath(mvcDaoController::ACTION_VIEW));
		$this->getEngine()->assign('daoUriDelete', $this->buildUriPath(mvcDaoController::ACTION_DELETE));
		$this->getEngine()->assign('daoUriDoDelete', $this->buildUriPath(mvcDaoController::ACTION_DO_DELETE, $this->getController()->getPrimaryKey()));
		$this->getEngine()->assign('daoUriDoEdit', $this->buildUriPath(mvcDaoController::ACTION_DO_EDIT, $this->getController()->getPrimaryKey()));
		$this->getEngine()->assign('daoUriDoNew', $this->buildUriPath(mvcDaoController::ACTION_DO_NEW));
		$this->getEngine()->assign('daoUriEdit', $this->buildUriPath(mvcDaoController::ACTION_EDIT));
		$this->getEngine()->assign('daoUriNew', $this->buildUriPath(mvcDaoController::ACTION_NEW));
		$this->getEngine()->assign('daoUriAction', $this->getController()->getAction());
		$this->getEngine()->assign('daoSearchQuery', $this->getController()->getSearchQueryAsUriString());
		if ( !in_array($this->getController()->getAction(), array(mvcDaoController::ACTION_VIEW, mvcDaoController::ACTION_SEARCH)) ) {
			$this->getEngine()->assign('formMonitor', true);
		}
		
		$this->assignCustomViewData();
		$this->setCacheLevelNone();
		$this->buildActionsMenu();
		$this->buildForm();
		$this->buildList();
	}
	
	/**
	 * Assigns custom view data to the daoView layer
	 *
	 * @return void
	 * @abstract 
	 */
	function assignCustomViewData() {}
	
	/**
	 * Returns the current controller for this view
	 *
	 * @return mvcDaoController
	 */
	function getController() {
		return parent::getController();
	}
	
	
	
	/**
	 * Returns the location of the list view
	 *
	 * @return string
	 * @abstract 
	 */
	function getObjectListView() {
		return $this->getTpl('daoObjectList', '/shared');
	}
	
	/**
	 * Returns the location of the form view
	 *
	 * @return string
	 * @abstract 
	 */
	function getObjectFormView() {
		return $this->getTpl('daoObjectForm', '/shared');
	}
	
	/**
	 * Returns the location of the actions menu view
	 *
	 * @return string
	 */
	function getActionsMenuView() {
		return $this->getTpl('daoActionsMenu', '/shared');
	}
	
	/**
	 * Returns the location of the main page view
	 *
	 * @return string
	 * @abstract 
	 */
	function getObjectPageView() {
		return $this->getTpl('daoObjectPage', '/shared');
	}
	
	
	
	/**
	 * Prepares the data for the list view
	 *
	 * @return void
	 */
	function buildList() {
		$this->getEngine()->assign('oModel', utilityOutputWrapper::wrap($this->getModel()));
		$this->getEngine()->assign('pagingOptions', utilityOutputWrapper::wrap($this->getController()->getPagingOptions()));
		$this->getEngine()->assign('pagingOffset', mvcDaoController::PAGING_VAR_OFFSET);
		$this->getEngine()->assign('pagingLimit', mvcDaoController::PAGING_VAR_LIMIT);
	}
	
	/**
	 * Builds the form for editing the object
	 *
	 * @return void
	 */
	function buildForm() {
		$formMethod = 'post';
		switch ( $this->getController()->getAction() ) {
			case mvcDaoController::ACTION_DELETE:
				$formAction = $this->getEngine()->getTemplateVar('daoUriDoDelete');
			break;

			case mvcDaoController::ACTION_EDIT:
				$formAction = $this->getEngine()->getTemplateVar('daoUriDoEdit');
			break;

			case mvcDaoController::ACTION_NEW:
                
				$formAction = $this->getEngine()->getTemplateVar('daoUriDoNew');
			break;

			default:
				$formAction = $this->getEngine()->getTemplateVar('daoUriView');
				$formMethod = 'get';
			break;
		}
		$this->getEngine()->assign('formAction', $formAction);
		$this->getEngine()->assign('formMethod', $formMethod);

		if ( $this->getController()->getAction() == mvcDaoController::ACTION_NEW ) {
			$this->getEngine()->assign('oObject', utilityOutputWrapper::wrap($this->getModel()->getNewObject()));
		} else {
			$this->getEngine()->assign('oObject', utilityOutputWrapper::wrap($this->getController()->getExistingObject()));
		}
	}
	
	/**
	 * Builds the action menu for the object
	 *
	 * @return void
	 */
	function buildActionsMenu() {
		if ( $this->getController()->getMenuItems()->getItem($this->getController()->getAction()) ) {
			$actions = $this->getController()->getMenuItems()->getItem($this->getController()->getAction());
		} else {
			$actions = $this->getController()->getMenuItems();
		}
		$this->getEngine()->assign('actions', utilityOutputWrapper::wrap($actions));
	}
	
	/**
	 * Displays the main editing page
	 *
	 * @return void
	 */
	function showDaoPage() {
		if ( !$this->isCached($this->getObjectPageView()) ) {
			
		}
		$this->render($this->getObjectPageView());
	}
	
	/**
	 * Displays the DAO denied page
	 *
	 * @return void
	 */
	function showDaoDeniedPage() {
		if ( !$this->isCached($this->getTpl('daoDenied', '/shared')) ) {
			
		}
		$this->render($this->getTpl('daoDenied', '/shared'));
	}
}