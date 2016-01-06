<?php
/**
 * musicView.class.php
 * 
 * musicView class
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package websites_my.mofilm.com
 * @subpackage controllers
 * @category musicView
 * @version $Rev: 634 $
 */


/**
 * musicView class
 * 
 * Provides the "musicView" page
 * 
 * @package websites_my.mofilm.com
 * @subpackage controllers
 * @category musicView
 */
class musicView extends mvcView {

	/**
	 * @see mvcViewBase::setupInitialVars()
	 */
	function setupInitialVars() {
		parent::setupInitialVars();
		
		/*
		 * Add any further custom setup for the view that is needed on every request
		 */
	}
	
	/**
	 * Shows the musicView page
	 *
	 * @return void
	 */
	function showMusicPage() {
		//$this->setCacheLevelNone();
		//Sound Manager
		$this->addCssResource(new mvcViewCss('sm-css', mvcViewCss::TYPE_FILE, '/libraries/soundmanager/mp3-player-button.css'));
		$this->addJavascriptResource(new mvcViewJavascript('sm', mvcViewJavascript::TYPE_FILE, '/libraries/soundmanager/soundmanager2.js'));
		$this->addJavascriptResource(new mvcViewJavascript('sm-mp3', mvcViewJavascript::TYPE_FILE, '/libraries/soundmanager/mp3-player-button.js'));
		
		//36o-ui
		//$this->addCssResource(new mvcViewCss('sm-360-css', mvcViewCss::TYPE_FILE, '/libraries/soundmanager/360/360player.css'));
		//$this->addJavascriptResource(new mvcViewJavascript('sm-360-animation', mvcViewJavascript::TYPE_FILE, '/libraries/soundmanager/360/berniecode-animator.js'));
		//$this->addJavascriptResource(new mvcViewJavascript('sm', mvcViewJavascript::TYPE_FILE, '/libraries/soundmanager/soundmanager2.js'));
		//$this->addJavascriptResource(new mvcViewJavascript('sm-360', mvcViewJavascript::TYPE_FILE, '/libraries/soundmanager/360/360player.js'));

		$this->addJavascriptResource(new mvcViewJavascript('sm-swf', mvcViewJavascript::TYPE_INLINE, '
			soundManager.setup({
			  // required: path to directory containing SM2 SWF files
			  url: "/libraries/soundmanager/swf/"
			});
	
		'));
		//
		$this->addJavascriptResource(new mvcViewJavascript('swfobject', mvcViewJavascript::TYPE_FILE, '/libraries/moviemasher/media/js/swfobject/swfobject.js'));
		$this->addCssResource(new mvcViewCss('mm', mvcViewCss::TYPE_FILE, '/themes/mofilm/css/mm.css'));
		$this->addCssResource(new mvcViewCss('uploadifycss', mvcViewCss::TYPE_FILE, '/libraries/uploadify-v3.1/uploadify.css'));
		$this->addJavascriptResource(new mvcViewJavascript('uploadify', mvcViewJavascript::TYPE_FILE, '/libraries/uploadify-v3.1/jquery.uploadify-3.1.min.js'));
		$this->addJavascriptResource(new mvcViewJavascript('uploadifyInit', mvcViewJavascript::TYPE_INLINE, '
	
		var base = window.location.href.substr(0, window.location.href.lastIndexOf("m/"));
		base = base + "m";
		var flashvarsObj = new Object();
		flashvarsObj.base = base;
		//flashvarsObj.debug = 1;
		flashvarsObj.evaluate = 1;
		flashvarsObj.sid = "ssss";
		flashvarsObj.config = "/libraries/moviemasher/xml/config.xml";
		flashvarsObj.preloader = "/libraries/moviemasher/com/moviemasher/display/Preloader/stable.swf";

		var parObj = new Object();
		parObj.allowFullScreen = "true";
		parObj.id = "moviemasher_applet";

		swfobject.embedSWF("/libraries/moviemasher/com/moviemasher/core/MovieMasher/stable.swf", "moviemasher_container", "900", "400", "10.0.0", "/libraries/moviemasher/media/js/swfobject/expressInstall.swf", flashvarsObj,parObj, parObj);

		function moviemasher()
		{	
			return (navigator.appName.indexOf("Microsoft") == -1) ? document[parObj.id] : window[parObj.id];
		}

			
		'));
		$this->addJavascriptResource(new mvcViewJavascript('uploadifv3', mvcViewJavascript::TYPE_INLINE, "$('#myfile').uploadify({
		height        : 30,
		swf           : '/libraries/uploadify-v3.1/uploadify.swf',
		uploader      : '/music/upload',
		width         : 120,
		'fileSizeLimit' : '200MB',	
		fileTypeExts  : '*.flv;*.mp4;*.mov;*.avi;*.mpg',
		'onUploadSuccess' : function(file, data, response) {
			moviemasher().evaluate('browser.parameters.group=video');
        },					
		'buttonText' : 'Upload Video',	
		formData : { '{$this->getRequest()->getSession()->getSessionName()}' : '{$this->getRequest()->getSession()->getSessionID()}' }		
		});
		"));
		
		$this->getEngine()->assign('oLogged', utilityOutputWrapper::wrap($this->getRequest()->getSession()->isLoggedIn()));
		$this->getEngine()->assign('oMusicResult', utilityOutputWrapper::wrap(momusicWorks::listOfObjects()));
		$this->getEngine()->assign('oModel', utilityOutputWrapper::wrap($this->getModel()));
		$this->getEngine()->assign('oController', utilityOutputWrapper::wrap($this->getController()));
		$this->getEngine()->assign('pagingOffset',  musicController::PAGING_VAR_OFFSET);
		$this->getEngine()->assign('pagingLimit', musicController::PAGING_VAR_LIMIT);		
		
		//$result = $this->getModel()->doSearch();
		
		$this->getEngine()->assign('oResults', utilityOutputWrapper::wrap($this->getModel()->doSearch()));

		
		//systemLog::message($result);
		
		$this->render($this->getTpl('music'));
	}
	
	
	function showMusicLicensePage($inID) {
		$oObject = momusicWorks::getInstance($inID);
		$this->getEngine()->assign('oObject', utilityOutputWrapper::wrap($oObject));
		$this->render($this->getTpl('musicLicense'));
	}
	
	function showMusicSearchResult() {
		
		try {
			$this->getModel()->getUserSearch()->setKeywords($this->getModel()->getKeywords());
		} catch ( Exception $e)	{
			
		}
		
		
		
		//systemLog::message($this->getModel()->doSearch());
		$this->getEngine()->assign('oModel', $this->getModel());
		$this->render($this->getTpl('sourceList'));
	}
	
	
}