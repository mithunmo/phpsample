<?php
/**
 * uploadMusicView.class.php
 * 
 * uploadMusicView class
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package websites_mofilmmusic.com
 * @subpackage controllers
 * @category uploadMusicView
 * @version $Rev: 634 $
 */


/**
 * uploadMusicView class
 * 
 * Provides the "uploadMusicView" page
 * 
 * @package websites_mofilmmusic.com
 * @subpackage controllers
 * @category uploadMusicView
 */
class uploadMusicView extends mvcView {

	/**
	 * @see mvcViewBase::setupInitialVars()
	 */
	function setupInitialVars() {
		parent::setupInitialVars();

		/*
		 * Add any further custom setup for the view that is needed on every request
		 */
	}
	
	
	function showDoneMusicPage() {
		$this->setCacheLevelNone();
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

		$this->render($this->getTpl('doneMusic'));
		
	}
	
	
	/**
	 * Shows the uploadMusicView page
	 *
	 * @return void
	 */
	function showUploadMusicPage() {
		$this->setCacheLevelNone();
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
		$this->addCssResource(new mvcViewCss('uploadifycss', mvcViewCss::TYPE_FILE, '/libraries/plupload/js1/jquery.ui.plupload/css/jquery.ui.plupload.css'));
		$this->addJavascriptResource(new mvcViewJavascript('plupload', mvcViewJavascript::TYPE_FILE, '/libraries/plupload/js1/plupload.js'));		
		$this->addJavascriptResource(new mvcViewJavascript('pluploadflash', mvcViewJavascript::TYPE_FILE, '/libraries/plupload/js1/plupload.flash.js'));		
		$this->addJavascriptResource(new mvcViewJavascript('plupload4', mvcViewJavascript::TYPE_FILE, '/libraries/plupload/js1/plupload.html4.js'));				
		$this->addJavascriptResource(new mvcViewJavascript('plupload5', mvcViewJavascript::TYPE_FILE, '/libraries/plupload/js1/plupload.html5.js'));				
		$this->addJavascriptResource(new mvcViewJavascript('pljquery', mvcViewJavascript::TYPE_FILE, '/libraries/plupload/js1/jquery.ui.plupload/jquery.ui.plupload.js'));				
		$this->addJavascriptResource(new mvcViewJavascript('momusicupload', mvcViewJavascript::TYPE_FILE, '/libraries/mofilm/momusicupload.js?'.mofilmConstants::JS_VERSION));

		$this->render($this->getTpl('uploadMusic'));
	}
}