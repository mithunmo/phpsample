<?php
/**
 * clientsView.class.php
 * 
 * clientsView class
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category clientsView
 * @version $Rev: 11 $
 */


/**
 * clientsView class
 * 
 * Provides the "clientsView" page
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category clientsView
 */
class clientsView extends mvcDaoView {

	/**
	 * @see mvcDaoView::assignCustomViewData()
	 */
	function assignCustomViewData() {
		$this->getEngine()->assign('parentController', 'admin');
		$this->getEngine()->assign('formEncType', 'multipart/form-data');
		
		$this->getEngine()->assign('events', utilityOutputWrapper::wrap(mofilmEvent::listOfObjects(null, null, null, mofilmEvent::ORDERBY_NAME)));
	}
	
	/**
	 * @see mvcDaoView::getObjectListView()
	 */
	function getObjectListView() {
		return $this->getTpl('clientsList');
	}
	
	/**
	 * @see mvcDaoView::getObjectFormView()
	 */
	function getObjectFormView() {
		return $this->getTpl('clientsForm');
	}
}