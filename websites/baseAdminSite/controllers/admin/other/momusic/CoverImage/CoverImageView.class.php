<?php
/**
 * CoverImageView.class.php
 * 
 * CoverImageView class
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category CoverImageView
 * @version $Rev: 624 $
 */


/**
 * CoverImageView class
 * 
 * Provides the "CoverImageView" page
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category CoverImageView
 */
class CoverImageView extends mvcDaoView {

	/**
	 * @see mvcDaoView::assignCustomViewData()
	 */
	function assignCustomViewData() {
		/**
		 * @todo set these parameters
		 */
		$this->getEngine()->assign('parentController', 'admin');
	}
	
	/**
	 * @see mvcDaoView::getObjectListView()
	 */
	function getObjectListView() {
		return $this->getTpl('CoverImageList');
	}
	
	/**
	 * @see mvcDaoView::getObjectFormView()
	 */
	function getObjectFormView() {
		return $this->getTpl('CoverImageForm');
	}
        
	function showUploadPage() {
		$this->setCacheLevelNone();
		
		$this->render($this->getTpl('upload'));
	}
        
}