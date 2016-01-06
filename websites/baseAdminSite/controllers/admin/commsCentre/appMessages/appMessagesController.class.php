<?php
/**
 * appMessagesController
 *
 * Stored in appMessagesController.class.php
 * 
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category appMessagesController
 * @version $Rev: 112 $
 */


/**
 * appMessagesController
 *
 * appMessagesController class
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category appMessagesController
 */
class appMessagesController extends mvcDaoController {

	const ACTION_TRANSLATE = 'translate';
	const ACTION_DPROPERT = 'dProperties';
	
	/**
	 * @see mvcControllerBase::initialise()
	 */
	function initialise() {
		parent::initialise();
		$this->setControllerView('appMessagesView');
		$this->getControllerActions()->addAction(self::ACTION_TRANSLATE);
		$this->getControllerActions()->addAction(self::ACTION_DPROPERT);
	}

	/**
	 * @see mvcControllerBase::launch()
	 */
	function  launch() {
		if ($this->getAction() == self::ACTION_TRANSLATE ) {
			$primaryKey=$this->getActionFromRequest(false,1);
			$language = $this->getActionFromRequest(false,2);
			$oObject = $this->getModel()->getExistingObject($primaryKey);
			$oCommsApplicationMessage = clone $oObject;
			$oCommsApplicationMessage->setMessageID(0);
			$oCommsApplicationMessage->setLanguage($language);
			$oCommsApplicationMessage->save();
			$messageID = $oCommsApplicationMessage->getMessageID();
			if ( $messageID != 0 ) {
				$this->redirect($this->buildUriPath(self::ACTION_EDIT."/".$messageID));
			} else {
				$this->getRequest()->getSession()->setStatusMessage("Choose another languge",mvcSession::MESSAGE_ERROR);
				$this->redirect($this->buildUriPath(self::ACTION_VIEW));
			}
		}
		if ( $this->getAction() == self::ACTION_DPROPERT ) {
			$this->getInputManager()->setLookupGlobals(utilityInputManager::LOOKUPGLOBALS_GET);
			$this->getInputManager()->addFilter('MessageGroupID', utilityInputFilter::filterInt());
			$data = $this->getInputManager()->doFilter();
			$resposneArr = $this->getModel()->getDynamicProperties($data['MessageGroupID']);

			$oView = new appMessagesView($this);
			$oView->sendDynamicProperties($resposneArr);
			return;
		    
		}

		parent::launch();
	}

	/**
	 *  Adds a new menu item called translate and calls parent method in daoController
	 *  @return void
	 */
	function actionEdit() {
		$primarykey = $this->getActionFromRequest(false,1);

		$this->getMenuItems()->getItem(self::ACTION_EDIT)->addItem(
			new mvcControllerMenuItem(
				$this->buildUriPath(
					self::ACTION_TRANSLATE, $primarykey
				), 'Translate', self::ACTION_TRANSLATE, 'Translate', false, mvcControllerMenuItem::PATH_TYPE_URI
			)
		);

		parent::actionEdit();
	}
		
	/**
	 * @see mvcControllerBase::addInputFilters()
	 */
	function addInputFilters() {
		$this->getInputManager()->addFilter('PrimaryKey', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('ApplicationID', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('OutboundTypeID', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('MessageGroupID', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('CurrencyID', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('Charge', utilityInputFilter::filterFloat());
		$this->getInputManager()->addFilter('Language', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('MessageHeader', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('MessageBody', utilityInputFilter::filterUnsafeRaw());
		$this->getInputManager()->addFilter('IsHtml', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('Delay', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('MessageOrder', utilityInputFilter::filterInt());
	}
	
	/**
	 * @see mvcControllerBase::addInputToModel()
	 *
	 * @param array $inData
	 * @param appMessagesModel $inModel
	 */
	function addInputToModel($inData, $inModel) {
		$inModel->setMessageID($inData['PrimaryKey']);
		$inModel->setApplicationID($inData['ApplicationID']);
		$inModel->setOutboundTypeID($inData['OutboundTypeID']);
		$inModel->setMessageGroupID($inData['MessageGroupID']);
		$inModel->setCurrencyID($inData['CurrencyID']);
		$inModel->setCharge((float)$inData['Charge']);
		$inModel->setLanguage($inData['Language']);
		$inModel->setMessageHeader($inData['MessageHeader']);
		
		if ( $inData['OutboundTypeID'] == commsOutboundType::T_EMAIL && $inData['IsHtml'] == 1 ) {
			$inModel->setMessageBody($inData['MessageBody']);
		} else {
			$inModel->setMessageBody(strip_tags($inData['MessageBody']));
			$inData['IsHtml'] = 0;
		}
		
		$inModel->setIsHtml($inData['IsHtml']);
		$inModel->setDelay($inData['Delay']);
		$inModel->setMessageOrder($inData['MessageOrder']);
	}
	
	/**
	 * Builds the model
	 *
	 * @return void
	 */
	function buildModel() {
		$oModel = new appMessagesModel();
		$this->setModel($oModel);
	}
}