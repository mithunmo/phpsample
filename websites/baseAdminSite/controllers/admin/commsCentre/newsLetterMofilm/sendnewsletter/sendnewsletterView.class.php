<?php
/**
 * sendnewsletterView.class.php
 *
 * sendnewsletterView class
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category sendnewsletterView
 * @version $Rev: 624 $
 */


/**
 * sendnewsletterView class
 *
 * Provides the "sendnewsletterView" page
 *
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category sendnewsletterView
 */
class sendnewsletterView extends mvcDaoView {

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
		$this->getEngine()->assign('eventsall', utilityOutputWrapper::wrap(mofilmEvent::listOfObjects()));
		$this->getEngine()->assign('newsletter', utilityOutputWrapper::wrap(mofilmCommsNewsletter::listOfObjectsByType(null, null, 1)));
		$this->getEngine()->assign('emailName',  utilityOutputWrapper::wrap(mofilmCommsSenderemail::listOfObjects()));
		$this->getEngine()->assign('lists', utilityOutputWrapper::wrap(mofilmCommsListType::listOfObjects()));
		$this->getEngine()->assign('sendl', $this->getController()->buildUriPath(sendnewsletterController::ACTION_SEND));
		$this->getEngine()->assign('today',date(system::getConfig()->getDatabaseDatetimeFormat()));
		$this->getEngine()->assign('filterObj', utilityOutputWrapper::wrap(mofilmCommsNewsletterFilterclass::listOfObjects()));
		$this->getEngine()->assign('status', mofilmCommsNewsletterdata::NEWSLETTER_NOT_SENT);
		
		
	}

	/**
	 * @see mvcDaoView::getObjectListView()
	 */
	function getObjectListView() {
		return $this->getTpl('sendnewsletterList');
	}

	/**
	 * @see mvcDaoView::getObjectFormView()
	 */
	function getObjectFormView() {
		$this->addJavascriptResource(new mvcViewJavascript('timepickerjs', mvcViewJavascript::TYPE_FILE, '/libraries/jquery-plugins/timepicker/timePicker.js'));
		$this->addCssResource(new mvcViewCss('timepickercss', mvcViewCss::TYPE_FILE, '/libraries/jquery-plugins/timepicker/timePicker.css'));
		$this->addJavascriptResource(new mvcViewJavascript('multipagejs', mvcViewJavascript::TYPE_FILE, '/libraries/jquery-plugins/multipage/jquery.form.wizard-min.js'));
		return $this->getTpl('sendnewsletterForm');
	}

	/**
	 * Displays a response for ajax requests
	 *
	 * @return void
	 */
	function sendJsonView() {
		$arr = array();
		$arr['name'] = "Message Sent";
		$response = json_encode($arr);
		echo $response;
	}
}