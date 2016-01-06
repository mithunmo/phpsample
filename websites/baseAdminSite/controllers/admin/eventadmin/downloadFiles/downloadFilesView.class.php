<?php
/**
 * downloadFilesView.class.php
 * 
 * downloadFilesView class
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category downloadFilesView
 * @version $Rev: 11 $
 */


/**
 * downloadFilesView class
 * 
 * Provides the "downloadFilesView" page
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category downloadFilesView
 */
class downloadFilesView extends mvcDaoView {

	/**
	 * @see mvcDaoView::assignCustomViewData()
	 */
	function assignCustomViewData() {
		$this->getEngine()->assign('parentController', 'admin');
		$this->getEngine()->assign('fileTypes', utilityOutputWrapper::wrap(mofilmDownloadFile::getFileTypes()));
		$this->getEngine()->assign('formEncType', 'multipart/form-data');
		
		$this->addJavascriptResource(new mvcViewJavascript('swfobject', mvcViewJavascript::TYPE_FILE, '/libraries/swfobject/swfobject.js'));
		$this->addJavascriptResource(new mvcViewJavascript('uploadify', mvcViewJavascript::TYPE_FILE, '/libraries/jquery-uploadify/jquery.uploadify.min.js'));
		$this->addJavascriptResource(new mvcViewJavascript('uploadifyInit', mvcViewJavascript::TYPE_INLINE, '$(\'#FileUpload\').uploadify({
				\'uploader\'  : \'/libraries/jquery-uploadify/uploadify.swf\',
				\'script\'    : \''.$this->buildUriPath(downloadFilesController::ACTION_DO_EDIT, $this->getController()->getPrimaryKey()).'\',
				\'cancelImg\' : \'/libraries/jquery-uploadify/cancel.png\',
				\'auto\'      : true,
				\'fileDataName\': \'Files\',
				\'scriptData\': { \''.$this->getRequest()->getSession()->getSessionName().'\': \''.$this->getRequest()->getSession()->getSessionID().'\' }
			});
		'));
	}
	
	/**
	 * @see mvcDaoView::getObjectListView()
	 */
	function getObjectListView() {
		return $this->getTpl('downloadFilesList');
	}
	
	/**
	 * @see mvcDaoView::getObjectFormView()
	 */
	function getObjectFormView() {
		return $this->getTpl('downloadFilesForm');
	}
}