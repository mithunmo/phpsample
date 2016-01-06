<?php
/**
 * senderEmailView.class.php
 * 
 * senderEmailView class
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category senderEmailView
 * @version $Rev: 624 $
 */


/**
 * senderEmailView class
 * 
 * Provides the "senderEmailView" page
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category senderEmailView
 */
class senderEmailView extends mvcDaoView {

	/**
	 * @see mvcDaoView::assignCustomViewData()
	 */
	function assignCustomViewData() {
		$this->getEngine()->assign('imapList', utilityOutputWrapper::wrap(mofilmCommsImapServerDetail::listOfObjects()));
		$this->getEngine()->assign('parentController', 'admin');
	}
	
	/**
	 * @see mvcDaoView::getObjectListView()
	 */
	function getObjectListView() {
		return $this->getTpl('senderEmailList');
	}
	
	/**
	 * @see mvcDaoView::getObjectFormView()
	 */
	function getObjectFormView() {
		return $this->getTpl('senderEmailForm');
	}
}