<?php
/**
 * motdView.class.php
 * 
 * motdView class
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category motdView
 * @version $Rev: 11 $
 */


/**
 * motdView class
 * 
 * Provides the "motdView" page
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category motdView
 */
class motdView extends mvcDaoView {

	/**
	 * @see mvcDaoView::assignCustomViewData()
	 */
	function assignCustomViewData() {
		$this->getEngine()->assign('parentController', 'admin');
		
		$this->addJavascriptResource(new mvcViewJavascript('ckeditor', mvcViewJavascript::TYPE_FILE, '/libraries/ckeditor/ckeditor.js'));
		$this->addJavascriptResource(new mvcViewJavascript('ckeditorAdaptor', mvcViewJavascript::TYPE_FILE, '/libraries/ckeditor/adapters/jquery.js'));
		$this->addJavascriptResource(new mvcViewJavascript('ckeditorInit', mvcViewJavascript::TYPE_INLINE,  "if ( $('textarea.ckeditor').length > 0 ) { $('textarea.ckeditor').ckeditor(); }"));
	}
	
	/**
	 * @see mvcDaoView::getObjectListView()
	 */
	function getObjectListView() {
		return $this->getTpl('motdList');
	}
	
	/**
	 * @see mvcDaoView::getObjectFormView()
	 */
	function getObjectFormView() {
		return $this->getTpl('motdForm');
	}
	
	/**
	 * Renders the current motd as a snippet
	 * 
	 * @return string
	 */
	function getCurrentMotd() {
		$this->setCacheLevelNone();
		
		$this->getEngine()->assign('oMotd', utilityOutputWrapper::wrap(mofilmMotd::getCurrentMotd()));
		
		return $this->compile($this->getTpl('motd', '/admin/other/motd'));
	}
}