<?php
/**
 * videoPlatformController
 *
 * Stored in videoPlatformController.class.php
 * 
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package websites_my.mofilm.in
 * @subpackage controllers
 * @category videoPlatformController
 * @version $Rev: 736 $
 */


/**
 * videoPlatformController
 *
 * videoPlatformController class
 * 
 * @package websites_my.mofilm.in
 * @subpackage controllers
 * @category videoPlatformController
 */
class videoPlatformController extends mvcController {
	
	const ACTION_POSTABCK = "postback";
	const ACTION_DOWNLOAD = "download";
	
	/**
	 * @see mvcControllerBase::initialise()
	 */
	function initialise() {
		parent::initialise();
		
		$this->setRequiresAuthentication(true);
		$this->getControllerActions()->addAction(self::ACTION_POSTABCK);	
		$this->getControllerActions()->addAction(self::ACTION_DOWNLOAD);	
	}
	
	/**
	 * @see mvcControllerBase::launch()
	 */
	function launch() {
		if ( $this->getAction() == self::ACTION_POSTABCK ) {
			systemLog::message("Action postback");
			$xml = file_get_contents('php://input');
			if ( $_GET['def'] ) {
				$xml = file_get_contents('/tmp/received.xml');
			}						
			if ( strlen($xml) > 10 ) {
				systemLog::message("Received XML from origin");
				$oOriginXML = new mofilmOriginXML();
				$oOriginXML->setXml($xml);
				$oOriginXML->save();
			}
		} elseif ( $this->getAction() == self::ACTION_DOWNLOAD ) {
			$oUser = $this->getRequest()->getSession()->getUser();
			
			if ( $oUser->getClientID() == 1 ) {
				$this->getInputManager()->setLookupGlobals(utilityInputManager::LOOKUPGLOBALS_GET);
				$inData = $this->getInputManager()->doFilter();
				$this->getModel()->setMovieID($inData["movieID"]);
				$oView = new videoPlatformView($this);
				$oView->showVideoPlatformPage();
			} else {
				throw new mvcDistributorInvalidRequestException(sprintf('Unhandled action specified by requestor'));
			}

		}
		
	}
	
	
	
	/**
	 * @see mvcControllerBase::addInputFilters()
	 */
	function addInputFilters() {
		$this->getInputManager()->addFilter("movieID", utilityInputFilter::filterString());
		
	}
	
	/**
	 * @see mvcControllerBase::addInputToModel()
	 */
	function addInputToModel($inData, $inModel) {
		
	}
	
	/**
	 * Fetches the model
	 *
	 * @return videoPlatformModel
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
		$oModel = new videoPlatformModel();
		$this->setModel($oModel);
	}
}
