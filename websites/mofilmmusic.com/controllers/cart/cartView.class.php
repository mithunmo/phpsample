<?php
/**
 * cartView.class.php
 * 
 * cartView class
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package websites_mofilmmusic.com
 * @subpackage controllers
 * @category cartView
 * @version $Rev: 634 $
 */


/**
 * cartView class
 * 
 * Provides the "cartView" page
 * 
 * @package websites_mofilmmusic.com
 * @subpackage controllers
 * @category cartView
 */
class cartView extends mvcView {

	/**
	 * @see mvcViewBase::setupInitialVars()
	 */
	function setupInitialVars() {
		parent::setupInitialVars();
		$this->getEngine()->assign('oModel', utilityOutputWrapper::wrap($this->getModel()));

		$this->getEngine()->assign('oLogged', utilityOutputWrapper::wrap($this->getRequest()->getSession()->isLoggedIn()));

		if ( $this->getRequest()->getSession()->isLoggedIn() ) {
			$this->getEngine()->assign('oName', utilityOutputWrapper::wrap($this->getRequest()->getSession()->getUser()->getFirstname()." !"));
		}
		
		$this->addCssResource(new mvcViewCss('mm', mvcViewCss::TYPE_FILE, '/themes/mofilm/css/mm.css?' . mofilmConstants::CSS_VERSION));
		
		$this->addCssResource(new mvcViewCss('sm-css', mvcViewCss::TYPE_FILE, '/libraries/soundmanager/mp3-player-button.css'));
		$this->addJavascriptResource(new mvcViewJavascript('sm', mvcViewJavascript::TYPE_FILE, '/libraries/soundmanager/soundmanager2.js'));
		$this->addJavascriptResource(new mvcViewJavascript('sm-mp3', mvcViewJavascript::TYPE_FILE, '/libraries/soundmanager/mp3-player-button.js'));
		
		$this->addJavascriptResource(new mvcViewJavascript('sm-swf', mvcViewJavascript::TYPE_INLINE, '
			soundManager.setup({
			  // required: path to directory containing SM2 SWF files
			  url: "/libraries/soundmanager/swf/"
			});
	
		'));
		
		

		/*
		 * Add any further custom setup for the view that is needed on every request
		 */
	}
	
	/**
	 * Shows the cartView page
	 *
	 * @return void
	 */
	function showCartPage() {
		$this->setCacheLevelNone();
		
		$this->render($this->getTpl('cart'));
	}
	
	
	
	function showMusicCart() {	
		if ( $this->getRequest()->getSession()->getTracks() ) {
			$this->getEngine()->assign('cnt', sizeof($this->getRequest()->getSession()->getTracks()));		
			$this->getEngine()->assign('login', "true");		
			$this->getEngine()->assign('oModel', $this->getModel());		
			$this->render($this->getTpl('cart'));
		} else {
			$this->getEngine()->assign('cnt', "");		
			$this->getEngine()->assign('login', "true");		
			$this->getEngine()->assign('oModel', $this->getModel());		
			$this->render($this->getTpl('cart'));
			
		}
	}

	
	function showGetCart() {
		if ( $this->getRequest()->getSession()->getTracks() ) {
			$this->getEngine()->assign('cnt', count($this->getRequest()->getSession()->getTracks()));		
			$this->getEngine()->assign('oObjects', $this->getRequest()->getSession()->getTracks());		
			$this->getEngine()->assign('oModel', $this->getModel());		
			$this->render($this->getTpl('showCart'));
		} else {
			$this->getEngine()->assign('cnt', "");		
			$this->getEngine()->assign('oModel', $this->getModel());		
			$this->render($this->getTpl('showCart'));			
		}	
	}
	
	

	function showErrorLogin() {	
		$this->getEngine()->assign('login', "false");
		$this->render($this->getTpl('cart'));
	}
	
	
	
}