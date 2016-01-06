<?php
/**
 * newslettertemplateView.class.php
 * 
 * newslettertemplateView class
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category newslettertemplateView
 * @version $Rev: 624 $
 */


/**
 * newslettertemplateView class
 * 
 * Provides the "newslettertemplateView" page
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category newslettertemplateView
 */
class newslettertemplateView extends mvcDaoView {

	/**
	 * @see mvcDaoView::assignCustomViewData()
	 */
	function assignCustomViewData() {
		$this->getEngine()->assign('parentController', 'admin');
		$this->addJavascriptResource(new mvcViewJavascript('tinymce', mvcViewJavascript::TYPE_FILE, '/libraries/jquery-plugins/tinymce/jscripts/tiny_mce/jquery.tinymce.js'));
	}
	
	/**
	 * @see mvcDaoView::getObjectListView()
	 */
	function getObjectListView() {
		return $this->getTpl('newslettertemplateList');
	}
	
	/**
	 * @see mvcDaoView::getObjectFormView()
	 */
	function getObjectFormView() {
		return $this->getTpl('newslettertemplateForm');
	}

	/**
	 * Gets the HTML content for a particular ID
	 * @param integer $inHtml
	 */
	function getHtml($inHtml) {
		$arr = array();
		$arr["html"] = $inHtml;
		$response = json_encode($arr);
		echo $response;
	}
}