<?php

/**
 * cartController
 *
 * Stored in cartController.class.php
 * 
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package websites_mofilmmusic.com
 * @subpackage controllers
 * @category cartController
 * @version $Rev: 736 $
 */

/**
 * cartController
 *
 * cartController class
 * 
 * @package websites_mofilmmusic.com
 * @subpackage controllers
 * @category cartController
 */
class cartController extends mvcController {
	const ACTION_VIEW = 'view';
	const ACTION_ADD = "add";
	const ACTION_DELETE = "delete";
	const ACTION_GET = "get";

	/**
	 * @see mvcControllerBase::initialise()
	 */
	function initialise() {
		parent::initialise();

		$this->setDefaultAction(self::ACTION_VIEW);
		$this->setRequiresAuthentication(false);

		$this->getControllerActions()->addAction(self::ACTION_VIEW);
		$this->getControllerActions()->addAction(self::ACTION_ADD);
		$this->getControllerActions()->addAction(self::ACTION_DELETE);
		$this->getControllerActions()->addAction(self::ACTION_GET);
	}

	/**
	 * @see mvcControllerBase::launch()
	 */
	function launch() {

		if ( $this->getAction() == self::ACTION_ADD ) {
			
			if ( $this->getRequest()->getSession()->isLoggedIn() ) {
				if ( $this->getRequest()->isAjaxRequest() ) {
					
					$ID = $_GET["id"];
					if ( $ID ) {
						systemLog::message("id here");
						$this->getRequest()->getSession()->setTracks($ID);
						$oView = new cartView($this);
						$oView->showMusicCart();
					} else {
						$oView = new cartView($this);
						$oView->showMusicCart();						
					}
				}
			} else {
				$oView = new cartView($this);
				$oView->showErrorLogin();
			}
			
			
		} else if ( $this->getAction() == self::ACTION_GET ) {
			systemLog::message($this->getRequest()->getSession()->getTracks());
			$oView = new cartView($this);
			$oView->showGetCart();									
		} else if ( $this->getAction() == self::ACTION_DELETE ) {
			
			$ID = $this->getActionFromRequest(false, 1);
			$arr = $this->getRequest()->getSession()->getTracks();
			unset($arr[$ID]);
			$this->getRequest()->getSession()->setParam(mvcSession::MOMUSIC_TRACKS, $arr);			
			$this->redirect("/cart/get");
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
	 * Fetches the model
	 *
	 * @return cartModel
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
		$oModel = new cartModel();
		$this->setModel($oModel);
	}

}