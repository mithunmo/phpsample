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
		$this->getEngine()->assign('oModel', utilityOutputWrapper::wrap($this->getModel()));
		$this->addCssResource(new mvcViewCss('mm', mvcViewCss::TYPE_FILE, '/themes/mofilm/css/mm.css?'.mofilmConstants::CSS_VERSION));
		$this->getEngine()->assign('oLogged', utilityOutputWrapper::wrap($this->getRequest()->getSession()->isLoggedIn()));

		if ( $this->getRequest()->getSession()->isLoggedIn() ) {
			$this->getEngine()->assign('oName', utilityOutputWrapper::wrap($this->getRequest()->getSession()->getUser()->getFirstname()." !"));
		}
		
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
	 * Shows the musicView page
	 *
	 * @return void
	 */
	function showMusicPage() {
		//$this->setCacheLevelNone();
		//Sound Manager
		
		//$this->addCssResource(new mvcViewCss('dew-css', mvcViewCss::TYPE_FILE, '/libraries/dewplayer/styles.css'));
		
		$this->addCssResource(new mvcViewCss('jp-css', mvcViewCss::TYPE_FILE, '/libraries/jplayer/blue.monday/jplayer.blue.monday.css'));
		$this->addJavascriptResource(new mvcViewJavascript('jp-js', mvcViewJavascript::TYPE_FILE, '/libraries/jplayer/js/jquery.jplayer.min.js'));
		
		
		
		//$this->addCssResource(new mvcViewCss('sm-css', mvcViewCss::TYPE_FILE, '/libraries/soundmanager/mp3-player-button.css'));
		//$this->addJavascriptResource(new mvcViewJavascript('sm', mvcViewJavascript::TYPE_FILE, '/libraries/soundmanager/soundmanager2.js'));
		//$this->addJavascriptResource(new mvcViewJavascript('sm-mp3', mvcViewJavascript::TYPE_FILE, '/libraries/soundmanager/mp3-player-button.js'));
		
		//36o-ui
		//$this->addCssResource(new mvcViewCss('sm-360-css', mvcViewCss::TYPE_FILE, '/libraries/soundmanager/360/360player.css'));
		//$this->addJavascriptResource(new mvcViewJavascript('sm-360-animation', mvcViewJavascript::TYPE_FILE, '/libraries/soundmanager/360/berniecode-animator.js'));
		//$this->addJavascriptResource(new mvcViewJavascript('sm', mvcViewJavascript::TYPE_FILE, '/libraries/soundmanager/soundmanager2.js'));
		//$this->addJavascriptResource(new mvcViewJavascript('sm-360', mvcViewJavascript::TYPE_FILE, '/libraries/soundmanager/360/360player.js'));
		/*
		$this->addJavascriptResource(new mvcViewJavascript('sm-swf', mvcViewJavascript::TYPE_INLINE, '
			soundManager.setup({
			  // required: path to directory containing SM2 SWF files
			  url: "/libraries/soundmanager/swf/"
			});
	
		'));
		 * 
		 */
		//
		$this->addJavascriptResource(new mvcViewJavascript('swfobject', mvcViewJavascript::TYPE_FILE, '/libraries/moviemasher/media/js/swfobject/swfobject.js'));
		//$this->addCssResource(new mvcViewCss('mm', mvcViewCss::TYPE_FILE, '/themes/mofilm/css/mm.css'));
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

		swfobject.embedSWF("/libraries/moviemasher/com/moviemasher/core/MovieMasher/stable.swf", "moviemasher_container", "1030", "550", "10.0.0", "/libraries/moviemasher/media/js/swfobject/expressInstall.swf", flashvarsObj,parObj, parObj);

		function moviemasher()
		{	
			return (navigator.appName.indexOf("Microsoft") == -1) ? document[parObj.id] : window[parObj.id];
		}
		
		function evaluateExpression(form)
		{
			var expression = form.expression.value;

			if (expression.length)
			{
				form.result.value = moviemasher().evaluate(expression);
			}
			return false;
		}

			
		'));
		$this->addJavascriptResource(new mvcViewJavascript('uploadifv3', mvcViewJavascript::TYPE_INLINE, "$('#myfile').uploadify({
		height        : 30,
		swf           : '/libraries/uploadify-v3.1/uploadify.swf',
		uploader      : '/music/upload',
		width         : 120,
		'fileSizeLimit' : '200MB',	
		fileTypeExts  : '*.flv;*.mp4;*.mov;*.avi;*.mpg;*.wmv',
		'onUploadSuccess' : function(file, data, response) {
			moviemasher().evaluate('browser.parameters.group=video');
        },					
		'buttonText' : 'Upload Video',	
		formData : { '{$this->getRequest()->getSession()->getSessionName()}' : '{$this->getRequest()->getSession()->getSessionID()}' }		
		});
		"));
		
		$this->getEngine()->assign('oLogged', utilityOutputWrapper::wrap($this->getRequest()->getSession()->isLoggedIn()));
		if ( $this->getRequest()->getSession()->isLoggedIn() ) {
			$this->getEngine()->assign('oName', utilityOutputWrapper::wrap($this->getRequest()->getSession()->getUser()->getFirstname()." !"));
		}
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
		$oObject = momusicWork::getInstance($inID);
		//$this->addCssResource(new mvcViewCss('mm', mvcViewCss::TYPE_FILE, '/themes/mofilm/css/mm.css'));
		$this->getEngine()->assign('oObject', utilityOutputWrapper::wrap($oObject));
		$this->render($this->getTpl('musicLicense'));
	}
	
	function showMusicMash($inHash) {
		$oObject = momusicMash::getInstanceByHash($inHash);
		$this->getEngine()->assign('oObject', utilityOutputWrapper::wrap($oObject));

		$this->getEngine()->assign('oModel', utilityOutputWrapper::wrap($this->getModel()));
		$this->getEngine()->assign('oController', utilityOutputWrapper::wrap($this->getController()));
		$this->getEngine()->assign('oResults', $this->getModel()->solrSearch());	
		$this->addCssResource(new mvcViewCss('jp-css', mvcViewCss::TYPE_FILE, '/libraries/jplayer/blue.monday/jplayer.blue.monday.css'));
		$this->addJavascriptResource(new mvcViewJavascript('jp-js', mvcViewJavascript::TYPE_FILE, '/libraries/jplayer/js/jquery.jplayer.min.js'));		
		
		$this->addJavascriptResource(new mvcViewJavascript('swfobject', mvcViewJavascript::TYPE_FILE, '/libraries/moviemasher/media/js/swfobject/swfobject.js'));
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

		swfobject.embedSWF("/libraries/moviemasher/com/moviemasher/core/MovieMasher/stable.swf", "moviemasher_container", "1030", "510", "10.0.0", "/libraries/moviemasher/media/js/swfobject/expressInstall.swf", flashvarsObj,parObj, parObj);

		function moviemasher()
		{	
			return (navigator.appName.indexOf("Microsoft") == -1) ? document[parObj.id] : window[parObj.id];
		}
		
		function evaluateExpression(form)
		{
			var expression = form.expression.value;

			if (expression.length)
			{
				form.result.value = moviemasher().evaluate(expression);
			}
			return false;
		}

			
		'));
		$this->addJavascriptResource(new mvcViewJavascript('uploadifv3', mvcViewJavascript::TYPE_INLINE, "$('#myfile').uploadify({
		height        : 35,
		width		  :150,	
		swf           : '/libraries/uploadify-v3.1/uploadify.swf',
		uploader      : '/music/upload',
		width         : 120,
		'fileSizeLimit' : '200MB',	
		fileTypeExts  : '*.flv;*.mp4;*.mov;*.avi;*.mpg;*.wmv',
		'onUploadSuccess' : function(file, data, response) {
			moviemasher().evaluate('browser.parameters.group=video');
        },					
		'buttonText' : 'Upload Video',	
		formData : { '{$this->getRequest()->getSession()->getSessionName()}' : '{$this->getRequest()->getSession()->getSessionID()}' }		
		});
		"));
		
		$this->render($this->getTpl('mash'));
	}
	
	/**
	 * Shows the music help page
	 * 
	 */
	function showMusicHelp() {
		
		$this->render($this->getTpl('help'));
	}
	
	/**
	 * Displays all the works by the user
	 * 
	 * 
	 */
	function showMusicWorks() {
		$this->getEngine()->assign('oModel', utilityOutputWrapper::wrap($this->getModel()));
		$this->getEngine()->assign('momusicuri' , system::getConfig()->getParam('mofilm', 'momusic', 'http://mofilmmusic.com') );		
		if ( $this->getRequest()->getSession()->isLoggedIn() ) {
			$this->getEngine()->assign('userID',$this->getRequest()->getSession()->getUser()->getID());
			$this->getEngine()->assign('oResult', momusicMash::listOfObjectsByUserID($this->getRequest()->getSession()->getUser()->getID(),$this->getModel()->getOffset(),$this->getModel()->getLimit()));
		} else {
			$this->getController()->redirect("/account/login?redirect=/music/myWork");
		}
		
		$this->render($this->getTpl('myWork'));
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
	
	
	function showMusic1SearchResult() {
				
		//systemLog::message($this->getModel()->doSearch());
		$this->getEngine()->assign('oModel', $this->getModel());
		$this->render($this->getTpl('musicList'));
	}
	
	function showMusicHome() {
                
		$this->getEngine()->assign('brandMusic', momusicBrandmusic::listOfObjects());
                $this->getEngine()->assign('handpickedMusic', momusicHandpickedmusic::listOfObjectsByRank());                
                $this->getEngine()->assign('artistList', momusicFeaturedArtist::listOfObjectsHome(0,2));                
		$this->addCssResource(new mvcViewCss('jp-css', mvcViewCss::TYPE_FILE, '/libraries/jplayer/blue.monday/jplayer.blue.monday.css'));
		$this->addJavascriptResource(new mvcViewJavascript('jp-js', mvcViewJavascript::TYPE_FILE, '/libraries/jplayer/js/jquery.jplayer.min.js'));		
		$this->render($this->getTpl('home'));
	}


	function showMusicBrief() {
		$this->getEngine()->assign('search', utilityOutputWrapper::wrap($this->getModel()->getSearchWord()));
		$this->getEngine()->assign('oModel', utilityOutputWrapper::wrap($this->getModel()));
		$this->getEngine()->assign('oController', utilityOutputWrapper::wrap($this->getController()));
                $this->getEngine()->assign('brandMusicID', $this->getModel()->getBrandMusicID());
		//$this->getEngine()->assign('oResults', $this->getModel()->solrSearch());
                $this->getEngine()->assign('oResults', $this->getModel()->briefMusicSearch());
		$this->addCssResource(new mvcViewCss('jp-css', mvcViewCss::TYPE_FILE, '/libraries/jplayer/blue.monday/jplayer.blue.monday.css'));
		$this->addJavascriptResource(new mvcViewJavascript('jp-js', mvcViewJavascript::TYPE_FILE, '/libraries/jplayer/js/jquery.jplayer.min.js'));		
		$this->render($this->getTpl('brief'));
	}
        
        
	function showMusicResult() {
		systemLog::message($_SERVER["REQUEST_URI"]);
		$cnt = substr_count($_SERVER["REQUEST_URI"], "filterq");
		if ( $cnt == 1 ) {
			$this->getEngine()->assign('currentUrl', $_SERVER["REQUEST_URI"]);
			$str = strstr($_SERVER["REQUEST_URI"], '&', true); // As of PHP 5.3.0
			systemLog::message("url".$str);
			$this->getEngine()->assign('currentUrl', $str);
		} else {
			if (strpos($_SERVER["REQUEST_URI"],'Offset') !== false) {

				$query = strstr($_SERVER["REQUEST_URI"], '&Offset', true); 

			} else {
				$query = $_SERVER["REQUEST_URI"];			
			}			
			$this->getEngine()->assign('currentUrl', $query);
		}
		$this->getEngine()->assign('req', $this->getRequest()->getRequestUri());
		$this->getEngine()->assign('search', utilityOutputWrapper::wrap($this->getModel()->getSearchWord()));
		$this->getEngine()->assign('oModel', utilityOutputWrapper::wrap($this->getModel()));
		$this->getEngine()->assign('oController', utilityOutputWrapper::wrap($this->getController()));
		$this->getEngine()->assign('oResults', $this->getModel()->solrSearch());
		$this->getEngine()->assign('oFacet', $this->getModel()->getFacet());		
		$this->addCssResource(new mvcViewCss('jp-css', mvcViewCss::TYPE_FILE, '/libraries/jplayer/blue.monday/jplayer.blue.monday.css'));
		$this->addJavascriptResource(new mvcViewJavascript('jp-js', mvcViewJavascript::TYPE_FILE, '/libraries/jplayer/js/jquery.jplayer.min.js'));		
		$this->render($this->getTpl('result'));
	}
	
	/**
	 * Shows momusic terms and conditions page
	 * 
	 */
	function showMusicTerms() {
		
		$this->render($this->getTpl('terms'));
	}
	
	/**
	 * Shows the momusic contact us page 
	 * 
	 */
	function showMusicContact() {
		
		$this->render($this->getTpl('contact'));
	}
	
	/**
	 * Shows the submit new music page 
	 * 
	 */
	function showMusicSubmit() {
		
		$this->render($this->getTpl('submit'));
	}
	
	/**
	 * 
	 * Shows the music tips page
	 * 
	 */
	function showMusicTips() {
		
		$this->render($this->getTpl('tips'));
	}
	
	/**
	 * 
	 * Shows the music tips page
	 * 
	 */
	function showReel() {
		$this->render($this->getTpl('reel'));
	}
	
	
	function showMusicSync() {
		
		$this->getEngine()->assign('oModel', utilityOutputWrapper::wrap($this->getModel()));
		$this->getEngine()->assign('oController', utilityOutputWrapper::wrap($this->getController()));
		$this->getEngine()->assign('oResults', $this->getModel()->solrSearch());	
		$this->addCssResource(new mvcViewCss('jp-css', mvcViewCss::TYPE_FILE, '/libraries/jplayer/blue.monday/jplayer.blue.monday.css'));
		$this->addJavascriptResource(new mvcViewJavascript('jp-js', mvcViewJavascript::TYPE_FILE, '/libraries/jplayer/js/jquery.jplayer.min.js'));		
		
		$this->addJavascriptResource(new mvcViewJavascript('swfobject', mvcViewJavascript::TYPE_FILE, '/libraries/moviemasher/media/js/swfobject/swfobject.js'));
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

		swfobject.embedSWF("/libraries/moviemasher/com/moviemasher/core/MovieMasher/stable.swf", "moviemasher_container", "1030", "510", "10.0.0", "/libraries/moviemasher/media/js/swfobject/expressInstall.swf", flashvarsObj,parObj, parObj);

		function moviemasher()
		{	
			return (navigator.appName.indexOf("Microsoft") == -1) ? document[parObj.id] : window[parObj.id];
		}
		
		function evaluateExpression(form)
		{
			var expression = form.expression.value;

			if (expression.length)
			{
				form.result.value = moviemasher().evaluate(expression);
			}
			return false;
		}

			
		'));
		$this->addJavascriptResource(new mvcViewJavascript('uploadifv3', mvcViewJavascript::TYPE_INLINE, "$('#myfile').uploadify({
		height        : 35,
		width		  :150,	
		swf           : '/libraries/uploadify-v3.1/uploadify.swf',
		uploader      : '/music/upload',
		width         : 120,
		'fileSizeLimit' : '200MB',	
		fileTypeExts  : '*.flv;*.mp4;*.mov;*.avi;*.mpg;*.wmv',
		'onUploadSuccess' : function(file, data, response) {
			moviemasher().evaluate('browser.parameters.group=video');
        },					
		'buttonText' : 'Upload Video',	
		formData : { '{$this->getRequest()->getSession()->getSessionName()}' : '{$this->getRequest()->getSession()->getSessionID()}' }		
		});
		"));
		
		
		
		$this->render($this->getTpl('sync'));
	}
	
	
	/**
	 * Shows the musicView page
	 *
	 * @return void
	 */
	function showMusic1Page() {
		$this->addCssResource(new mvcViewCss('jp-css', mvcViewCss::TYPE_FILE, '/libraries/jplayer/blue.monday/jplayer.blue.monday.css'));
		$this->addJavascriptResource(new mvcViewJavascript('jp-js', mvcViewJavascript::TYPE_FILE, '/libraries/jplayer/js/jquery.jplayer.min.js'));
		$this->addJavascriptResource(new mvcViewJavascript('swfobject', mvcViewJavascript::TYPE_FILE, '/libraries/moviemasher/media/js/swfobject/swfobject.js'));
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

		swfobject.embedSWF("/libraries/moviemasher/com/moviemasher/core/MovieMasher/stable.swf", "moviemasher_container", "1000", "550", "10.0.0", "/libraries/moviemasher/media/js/swfobject/expressInstall.swf", flashvarsObj,parObj, parObj);

		function moviemasher()
		{	
			return (navigator.appName.indexOf("Microsoft") == -1) ? document[parObj.id] : window[parObj.id];
		}
		
		function evaluateExpression(form)
		{
			var expression = form.expression.value;

			if (expression.length)
			{
				form.result.value = moviemasher().evaluate(expression);
			}
			return false;
		}

			
		'));
		$this->addJavascriptResource(new mvcViewJavascript('uploadifv3', mvcViewJavascript::TYPE_INLINE, "$('#myfile').uploadify({
		height        : 30,
		swf           : '/libraries/uploadify-v3.1/uploadify.swf',
		uploader      : '/music/upload',
		width         : 120,
		'fileSizeLimit' : '200MB',	
		fileTypeExts  : '*.flv;*.mp4;*.mov;*.avi;*.mpg;*.wmv',
		'onUploadSuccess' : function(file, data, response) {
			moviemasher().evaluate('browser.parameters.group=video');
        },					
		'buttonText' : 'Upload Video',	
		formData : { '{$this->getRequest()->getSession()->getSessionName()}' : '{$this->getRequest()->getSession()->getSessionID()}' }		
		});
		"));
		
		$this->getEngine()->assign('oLogged', utilityOutputWrapper::wrap($this->getRequest()->getSession()->isLoggedIn()));
		if ( $this->getRequest()->getSession()->isLoggedIn() ) {
			$this->getEngine()->assign('oName', utilityOutputWrapper::wrap($this->getRequest()->getSession()->getUser()->getFirstname()." !"));
		}
		
		//$this->getEngine()->assign('oMusicResult', utilityOutputWrapper::wrap(momusicWorks::listOfObjects()));
		
		$this->getEngine()->assign('oModel', utilityOutputWrapper::wrap($this->getModel()));
		$this->getEngine()->assign('oController', utilityOutputWrapper::wrap($this->getController()));
		//$this->getEngine()->assign('pagingOffset',  musicController::PAGING_VAR_OFFSET);
		//$this->getEngine()->assign('pagingLimit', musicController::PAGING_VAR_LIMIT);		
		
		//$result = $this->getModel()->doSearch();
		
		//$this->getEngine()->assign('oResults', utilityOutputWrapper::wrap($this->getModel()->doSearch()));
		$this->getEngine()->assign('oResults', $this->getModel()->solrSearch());
		
		//systemLog::message($result);
		
		$this->render($this->getTpl('musicView'));
	}
	
	
	
}