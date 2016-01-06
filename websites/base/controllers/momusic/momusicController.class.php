<?php
/**
 * momusicController
 *
 * Stored in momusicController.class.php
 * 
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package websites_base
 * @subpackage controllers
 * @category momusicController
 * @version $Rev: 736 $
 */


/**
 * momusicController
 *
 * momusicController class
 * 
 * @package websites_base
 * @subpackage controllers
 * @category momusicController
 */
class momusicController extends mvcController {
	
	const ACTION_VIEW = 'view';
	const ACTION_REGISTER = 'create';
	const ACTION_MOMUSIC = "momusic";
	
	/**
	 * @see mvcControllerBase::initialise()
	 */
	function initialise() {
		parent::initialise();
		
		$this->setDefaultAction(self::ACTION_VIEW);
		$this->setRequiresAuthentication(true);
		
		$this->getControllerActions()->addAction(self::ACTION_VIEW);
		$this->getControllerActions()->addAction(self::ACTION_REGISTER);
		$this->getControllerActions()->addAction(self::ACTION_MOMUSIC);
	}
	
	/**
	 * @see mvcControllerBase::launch()
	 */
	function launch() {
		if ( $this->getAction() == self::ACTION_VIEW ) {
			/*
			 * No downloads route page
			 */
			$this->momusicAction();
		} elseif ( $this->getAction()== self::ACTION_MOMUSIC ) {
			
			$this->momusicAuthAction();
		} else {
			$this->redirect("/account/register");
		}		
	}
	
	
	
	/**
	 * @see mvcControllerBase::addInputFilters()
	 */
	function addInputFilters() {
		
	}
	
	/**
	 * @see mvcControllerBase::addInputToModel()
	 */
	function addInputToModel($inData, $inModel) {
		
	}
	
	/**
	 * This function gets a signinurl if apiID is stored or else creates a new user for the audiosocket API
	 * 
	 * 
	 * @return void
	 */
	function momusicAction() {		
		$oUser = $this->getRequest()->getSession()->getUser();
		$email = $oUser->getEmail();
		$apiKey = system::getConfig()->getParam("momusic", "apiKey")->getParamValue();
		$server = system::getConfig()->getParam("momusic", "server");

		if ( $oUser->getParamSet()->getParam('audiosocketID') ) {
			
			$url = $server."/".$oUser->getParamSet()->getParam('audiosocketID')."/sign-in-url";
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-Audiosocket-Token:'.system::getConfig()->getParam('momusic', 'apiKey')->getParamValue()));
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible;)");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
			curl_setopt($ch, CURLOPT_VERBOSE, TRUE);
			curl_setopt($ch, CURLOPT_POST, 1);
			$jsonResponse = curl_exec($ch);
			$result = json_decode($jsonResponse);
			$this->redirect($result->url);		
		} else {
			$url = $server;
			$ch = curl_init();
			$arr = array("email" => $email);	
			$json_string = json_encode($arr);			
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json','X-Audiosocket-Token:'.system::getConfig()->getParam('momusic', 'apiKey')->getParamValue()));
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible;)");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
			curl_setopt($ch, CURLOPT_VERBOSE, TRUE);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $json_string);
			curl_setopt($ch, CURLOPT_POST, 1);
			$jsonResponse = curl_exec($ch);
			systemLog::message($jsonResponse);
			$result = json_decode($jsonResponse);
			$oUser->getParamSet()->setParam("audiosocketID", $result->id);
			$oUser->save();
			$this->redirect($result->url);		
		}	
	}
	
	
	function momusicAuthAction() {
		
		$userID = $this->getRequest()->getSession()->getUser()->getID();
		$userID = base64_encode($userID);
		$uri = system::getConfig()->getParam('mofilm', 'momusic','http://music.mofilmmusi.com');
		$this->redirect($uri."/music/auth?userID=".$userID);
	}
	
	/**
	 * Fetches the model
	 *
	 * @return momusicModel
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
		$oModel = new momusicModel();
		$this->setModel($oModel);
	}
}