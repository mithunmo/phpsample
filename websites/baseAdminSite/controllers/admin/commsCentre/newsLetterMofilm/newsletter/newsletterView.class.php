<?php
/**
 * newsletterView.class.php
 *
 * newsletterView class
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category newsletterView
 * @version $Rev: 624 $
 */


/**
 * newsletterView class
 *
 * Provides the "newsletterView" page
 *
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category newsletterView
 */
class newsletterView extends mvcDaoView {

	/**
	 * @see mvcDaoView::assignCustomViewData()
	 */
	function assignCustomViewData() {
		$this->getEngine()->assign('parentController', 'admin');
		$this->getEngine()->assign('types', utilityOutputWrapper::wrap(commsOutboundType::listOfObjects()));
		$this->getEngine()->assign('groups', utilityOutputWrapper::wrap(commsApplicationMessageGroup::listOfObjects()));
		$this->getEngine()->assign('nlTemplate', utilityOutputWrapper::wrap(mofilmCommsNewslettertemplate::listOfObjects()));
		$this->getEngine()->assign('formEncType','multipart/form-data');
		$this->addJavascriptResource(new mvcViewJavascript('tinymce', mvcViewJavascript::TYPE_FILE, '/libraries/jquery-plugins/tinymce/jscripts/tiny_mce/jquery.tinymce.js'));
		$this->addJavascriptResource(new mvcViewJavascript('poppup', mvcViewJavascript::TYPE_FILE, '/libraries/jquery-plugins/tinymce/jscripts/tiny_mce/plugins/browser/fileBrowser.js'));
	}

	/**
	 * @see mvcDaoView::getObjectListView()
	 */
	function getObjectListView() {
		return $this->getTpl('newsletterList');
	}

	/**
	 * @see mvcDaoView::getObjectFormView()
	 */
	function getObjectFormView() {
		return $this->getTpl('newsletterForm');
	}
}