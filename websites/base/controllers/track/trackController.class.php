<?php
/**
 * trackController
 *
 * Stored in trackController.class.php
 * 
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package websites_base
 * @subpackage controllers
 * @category trackController
 * @version $Rev: 736 $
 */


/**
 * trackController
 *
 * trackController class
 * 
 * @package websites_base
 * @subpackage controllers
 * @category trackController
 */
class trackController extends mvcController {
	
	const ACTION_VIEW = 'view';
	const ACTION_TRACK = 'track';
	const ACTION_LINK = "nlLink";
	const ACTION_UNSUBS = 'unsubs';


	
	/**
	 * @see mvcControllerBase::initialise()
	 */
	function initialise() {
		parent::initialise();
		
		$this->setDefaultAction(self::ACTION_VIEW);
		$this->setRequiresAuthentication(false);
		
		$this->getControllerActions()->addAction(self::ACTION_VIEW);
		$this->getControllerActions()->addAction(self::ACTION_TRACK);
		$this->getControllerActions()->addAction(self::ACTION_LINK);
		$this->getControllerActions()->addAction(self::ACTION_UNSUBS);
	}
	
	/**
	 * @see mvcControllerBase::launch()
	 */
	function launch() {
		switch ( $this->getAction() ) {
			case self::ACTION_TRACK:
				$this->getInputManager()->setLookupGlobals(utilityInputManager::LOOKUPGLOBALS_GET);
				$data = $this->getInputManager()->doFilter();
				$this->getModel()->updateReadStatus($data['track']);
			break;

			case self::ACTION_LINK:
				$this->getInputManager()->setLookupGlobals(utilityInputManager::LOOKUPGLOBALS_GET);
				$data = $this->getInputManager()->doFilter();
				$this->addInputToModel($data,$this->getModel());

				$this->getModel()->createNewsletterLink($data["nlId"], $data["nlLink"],$data["userId"],$data["nlHistoryId"]);
				$this->redirect(urldecode($data["nlLink"]));
			break;

			case self::ACTION_UNSUBS:
				$this->getInputManager()->setLookupGlobals(utilityInputManager::LOOKUPGLOBALS_GET);
				$data = $this->getInputManager()->doFilter();
				$this->addInputToModel($data, $this->getModel());
				$this->getModel()->unsubscribeEmail($data['emailId'], $data['userId'], $data['nlId']);

				$oView = new trackView($this);
				$oView->showUnsubscriptionPage();
			break;
		}
	}
	
	/**
	 * @see mvcControllerBase::addInputFilters()
	 */
	function addInputFilters() {
		$this->getInputManager()->addFilter("track",  utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter("nlLink", utilityInputFilter::filterValidateUrl());
		$this->getInputManager()->addFilter("nlId", utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter("userId", utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter("nlHistoryId", utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter("userId", utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter("emailId", utilityInputFilter::filterInt());
		
	}
	
	/**
	 * @see mvcControllerBase::addInputToModel()
	 */
	function addInputToModel($inData, $inModel) {
		
	}
	
	/**
	 * Fetches the model
	 *
	 * @return trackModel
	 */
	function getModel() {
		if ( !parent::getModel() ) {
			$this->buildModel();
		}
		return parent::getModel();
	}
	
	/**
	 * Builds the model
	 *
	 * @return void
	 */
	function buildModel() {
		$oModel = new trackModel();
		$this->setModel($oModel);
	}
}