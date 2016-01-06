<?php
/**
 * appMessageGroupsView.class.php
 * 
 * appMessageGroupsView class
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category appMessageGroupsView
 * @version $Rev: 11 $
 */


/**
 * appMessageGroupsView class
 * 
 * Provides the "appMessageGroupsView" page
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category appMessageGroupsView
 */
class appMessageGroupsView extends mvcDaoView {

	/**
	 * @see mvcDaoView::assignCustomViewData()
	 */
	function assignCustomViewData() {
		$this->getEngine()->assign('parentController', 'admin');
	}
	
	/**
	 * @see mvcDaoView::getObjectListView()
	 */
	function getObjectListView() {
		return $this->getTpl('appMessageGroupsList');
	}
	
	/**
	 * @see mvcDaoView::getObjectFormView()
	 */
	function getObjectFormView() {
		return $this->getTpl('appMessageGroupsForm');
	}
}