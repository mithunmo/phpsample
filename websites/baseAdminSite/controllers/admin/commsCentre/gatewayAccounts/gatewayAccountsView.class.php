<?php
/**
 * gatewayAccountsView.class.php
 * 
 * gatewayAccountsView class
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category gatewayAccountsView
 * @version $Rev: 11 $
 */


/**
 * gatewayAccountsView class
 * 
 * Provides the "gatewayAccountsView" page
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category gatewayAccountsView
 */
class gatewayAccountsView extends mvcDaoView {

	/**
	 * @see mvcDaoView::assignCustomViewData()
	 */
	function assignCustomViewData() {
		$this->getEngine()->assign('parentController', 'admin');
		$this->getEngine()->assign('gateways', utilityOutputWrapper::wrap(commsGateway::listOfObjects()));
	}
	
	/**
	 * @see mvcDaoView::getObjectListView()
	 */
	function getObjectListView() {
		return $this->getTpl('gatewayAccountsList');
	}
	
	/**
	 * @see mvcDaoView::getObjectFormView()
	 */
	function getObjectFormView() {
		return $this->getTpl('gatewayAccountsForm');
	}
}