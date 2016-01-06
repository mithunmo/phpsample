<?php
/**
 * eventImagesView.class.php
 * 
 * eventImagesView class
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category eventImagesView
 * @version $Rev: 634 $
 */


/**
 * eventImagesView class
 * 
 * Provides the "eventImagesView" page
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category eventImagesView
 */
class eventImagesView extends mvcView {

	/**
	 * @see mvcViewBase::setupInitialVars()
	 */
	function setupInitialVars() {
		parent::setupInitialVars();

		$rootPath = system::getConfig()->getPathWebsites().system::getDirSeparator().'base';
		$this->getEngine()->assign('adminEventFolder', str_replace($rootPath, '', mofilmConstants::getAdminEventsFolder()));
		$this->getEngine()->assign('adminSourceFolder', str_replace($rootPath, '', mofilmConstants::getAdminSourceFolder()));
		$this->getEngine()->assign('clientEventFolder', str_replace($rootPath, '', mofilmConstants::getClientEventsFolder()));
		$this->getEngine()->assign('clientSourceFolder', str_replace($rootPath, '', mofilmConstants::getClientSourceFolder()));

		$this->getEngine()->assign('viewURI', $this->getController()->buildUriPath(eventImagesController::ACTION_VIEW));
		$this->getEngine()->assign('editURI', $this->getController()->buildUriPath(eventImagesController::ACTION_EDIT));
		$this->getEngine()->assign('doEditURI', $this->getController()->buildUriPath(eventImagesController::ACTION_DO_EDIT));
	}
	
	/**
	 * Shows the eventImagesView page
	 *
	 * @return void
	 */
	function showEventImagesPage() {
		$this->setCacheLevelNone();

		$this->getEngine()->assign('events', utilityOutputWrapper::wrap(mofilmEvent::listOfObjects(null, null, false, mofilmEvent::ORDERBY_ENDDATE)));

		$this->render($this->getTpl('eventImages'));
	}

	/**
	 * Shows the edit page
	 *
	 * @return void
	 */
	function showEditPage() {
		$this->setCacheLevelNone();

		$this->getEngine()->assign('oEvent', utilityOutputWrapper::wrap($this->getModel()->getEvent()));

		$this->render($this->getTpl('edit'));
	}
}