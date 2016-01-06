<?php
/**
 * sendCCAView.class.php
 * 
 * sendCCAView class
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category sendCCAView
 * @version $Rev: 624 $
 */


/**
 * sendCCAView class
 * 
 * Provides the "sendCCAView" page
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category sendCCAView
 */
class sendCCAView extends mvcDaoView {
	
	/**
	 * @see mvcDaoViewBase::__construct()
	 */
	function __construct($inController) {
		parent::__construct($inController);

	}
	
	
	/**
	 * @see mvcDaoView::assignCustomViewData()
	 */
	function assignCustomViewData() {
		$this->getEngine()->assign('parentController', 'admin');
		$this->getEngine()->assign('formEncType', 'multipart/form-data');
		$this->getEngine()->assign('displayAttachmentURI', $this->getController()->buildUriPath(sendCCAController::ACTION_DIS));
		$this->getEngine()->assign('emailName',  utilityOutputWrapper::wrap(mofilmCommsSenderemail::listOfObjects()));
		$this->getEngine()->assign('shortlisted', utilityOutputWrapper::wrap(mofilmCommsNewsletter::listOfObjectsByType(null,null,2)));
		$this->getEngine()->assign('nonshortlisted', utilityOutputWrapper::wrap(mofilmCommsNewsletter::listOfObjectsByType(null,null,3)));
		$this->getEngine()->assign('nonwinners', utilityOutputWrapper::wrap(mofilmCommsNewsletter::listOfObjectsByType(null,null,4)));
		
	}
	
	/**
	 * @see mvcDaoView::getObjectListView()
	 */
	function getObjectListView() {
		return $this->getTpl('sendCCAList');
		
	}
	
	/**
	 * @see mvcDaoView::getObjectFormView()
	 */
	function getObjectFormView() {
		$this->addJavascriptResource(new mvcViewJavascript('timepickerjs', mvcViewJavascript::TYPE_FILE, '/libraries/jquery-plugins/timepicker/timePicker.js'));
		$this->addCssResource(new mvcViewCss('timepickercss', mvcViewCss::TYPE_FILE, '/libraries/jquery-plugins/timepicker/timePicker.css'));
		return $this->getTpl('sendCCAForm');
	}
}