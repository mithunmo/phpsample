<?php
/**
 * sendnewsletterController
 *
 * Stored in sendnewsletterController.class.php
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category sendnewsletterController
 * @version $Rev: 624 $
 */


/**
 * sendnewsletterController
 *
 * sendnewsletterController class
 *
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category sendnewsletterController
 */
class sendnewsletterController extends mvcDaoController {


	const ACTION_SEND = 'send';

	/**
	 * @see mvcControllerBase::initialise()
	 *
	 *
	 */
	function initialise() {
		parent::initialise();

		$this->setControllerView('sendnewsletterView');
		$this->getControllerActions()->addAction(self::ACTION_SEND);
	}

	/**
	 * Handles all the controller Actions
	 * @return void
	 *
	 */
	function launch() {
		
		if ( $this->getAction() == self::ACTION_SEND ) {

			if ( $this->getRequest()->isAjaxRequest() ) {
				$primaryKey = $this->getActionFromRequest(false, 1);
				$status = $this->getActionFromRequest(false, 2);
				$data["PrimaryKey"] = $primaryKey;
				$data["Status"] = $status;
				$this->addInputToModel($data, $this->getModel());
				$this->getModel()->updateNewsLetterStatus($primaryKey, $status);
				$oView = new sendnewsletterView($this);
				$oView->sendJsonView();
				return;

			}

		}
		parent::launch();

	}
	
	/**
	 * @see mvcControllerBase::addInputFilters()
	 */
	function addInputFilters() {
		$this->getInputManager()->addFilter('PrimaryKey', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('Id', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('Nlid', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('Status', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('Classname', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('EventParams', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('EventID', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Params_list', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('EmailName', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('MessageType', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('ScheduledDate', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('videoRating', utilityInputFilter::filterInt());

	}

	/**
	 * @see mvcControllerBase::addInputToModel()
	 *
	 * @param array $inData
	 * @param sendnewsletterModel $inModel
	 */
	function addInputToModel($inData, $inModel) {
		$inModel->setId($inData['PrimaryKey']);
		$inModel->setNewsletterID($inData['Nlid']);
		$inModel->setStatus($inData['Status']);
		$inModel->setScheduledDate($inData['ScheduledDate']);
		$inModel->setEmailName($inData['EmailName']);
		$inModel->setMessageType(($inData['MessageType']));
		
		if ( $inData["EventID"] != "" ) {
			$inModel->getParamSet()->setParam(mofilmCommsNewsletterdata::PARAM_NL_EVENTID, $inData["EventID"]);
				
		}
		
		if ( $inData["videoRating"] !=  "" ) {
			$inModel->getParamSet()->setParam(mofilmCommsNewsletterdata::PARAM_NL_VIDEO_RATING, $inData["videoRating"]);
		}
		
		if ( isset($inData["EventParams"]) ) {
			$this->validateParams($inData);
			$inModel->setClassname("events");
			$inModel->setParams($inData["EventParams"]);
		} else {
			$inModel->setClassname("mofilmCommsSubscription");
			$inModel->setParams($inData['Params_list']);
		}
	
	}

	/**
	 * Builds the model
	 *
	 * @return void
	 */
	function buildModel() {
		$oModel = new sendnewsletterModel();
		$this->setModel($oModel);
	}

	/**
	 * Validates the Event params
	 *
	 * @param array $inData
	 * @return void
	 */
	function validateParams($inData) {
		$oMofilmCommsNewsletterFilterClass = mofilmCommsNewsletterFilterclass::getInstance($inData['EventParams']);
		$paramArray = preg_split("/\//",$oMofilmCommsNewsletterFilterClass->getDefaultParams());
		foreach ( $paramArray as $value ) {
			if ( $inData[$value] == "" ) {
				throw new mofilmException("Invalid Param for ".$value);
			}
		}
	}
}