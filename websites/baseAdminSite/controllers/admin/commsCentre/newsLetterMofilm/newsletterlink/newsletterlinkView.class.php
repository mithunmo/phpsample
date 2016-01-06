<?php
/**
 * newsletterlinkView.class.php
 * 
 * newsletterlinkView class
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category newsletterlinkView
 * @version $Rev: 624 $
 */


/**
 * newsletterlinkView class
 * 
 * Provides the "newsletterlinkView" page
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category newsletterlinkView
 */
class newsletterlinkView extends mvcDaoView {

	/**
	 * @see mvcDaoView::assignCustomViewData()
	 */
	function assignCustomViewData() {
		$this->getEngine()->assign('parentController', 'admin');
		$this->getEngine()->assign('newslettersent', utilityOutputWrapper::wrap(mofilmCommsNewsletter::listOfObjects()));
		$this->getEngine()->assign('newsl', utilityOutputWrapper::wrap($inID));
		
	}
	
	/**
	 * @see mvcDaoView::getObjectListView()
	 */
	function getObjectListView() {
		return $this->getTpl('newsletterlinkList');
	}
	
	/**
	 * @see mvcDaoView::getObjectFormView()
	 */
	function getObjectFormView() {
		return $this->getTpl('newsletterlinkForm');
	}
}