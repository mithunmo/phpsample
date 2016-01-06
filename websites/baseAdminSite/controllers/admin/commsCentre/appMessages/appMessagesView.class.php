<?php
/**
 * appMessagesView.class.php
 * 
 * appMessagesView class
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category appMessagesView
 * @version $Rev: 218 $
 */


/**
 * appMessagesView class
 * 
 * Provides the "appMessagesView" page
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category appMessagesView
 */
class appMessagesView extends mvcDaoView {

	/**
	 *
	 * @see mvcDaoViewBase::__construct()
	 */
	function  __construct($inController) {
		parent::__construct($inController);
	}
	/**
	 * @see mvcDaoView::assignCustomViewData()
	 */
	function assignCustomViewData() {
		$this->getEngine()->assign('parentController', 'admin');
		$this->getEngine()->assign('types', utilityOutputWrapper::wrap(commsOutboundType::listOfObjects()));
		$this->getEngine()->assign('groups', utilityOutputWrapper::wrap(commsApplicationMessageGroup::listOfObjects()));
		$this->getEngine()->assign('oUser',$this->getRequest()->getSession()->getUser());
		$this->addJavascriptResource(new mvcViewJavascript('tinymce', mvcViewJavascript::TYPE_FILE, '/libraries/jquery-plugins/tinymce/jscripts/tiny_mce/jquery.tinymce.js'));
		$this->addJavascriptResource(new mvcViewJavascript('tinymce_popup', mvcViewJavascript::TYPE_FILE, '/libraries/jquery-plugins/tinymce/jscripts/tiny_mce/plugins/browser/fileBrowser.js'));
	}
	
	/**
	 * @see mvcDaoView::getObjectListView()
	 */
	function getObjectListView() {
		if ( $this->getRequest()->getSession()->getUser()->isAuthorised('root') ) {
			return $this->getTpl('appMessagesList');
		} else {
			return $this->getTpl('appMessagesSimpleList');
		}
	}
	
	/**
	 * @see mvcDaoView::getObjectFormView()
	 */
	function getObjectFormView() {
		if ( $this->getRequest()->getSession()->getUser()->isAuthorised('root') ) {
			return $this->getTpl('appMessagesForm');
		} else {
			return $this->getTpl('appMessagesFormSimple');
		}
	}

	/**
	 * Encodes the Array in json 
	 * @param array $inResponseArr
	 */
	function sendDynamicProperties($inResponseArr) {
		$response = json_encode($inResponseArr);
		echo $response;
	}
}