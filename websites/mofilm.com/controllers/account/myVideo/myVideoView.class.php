<?php
/**
 * myVideoView.class.php
 * 
 * myVideoView class
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package websites_mofilm.com
 * @subpackage controllers
 * @category myVideoView
 * @version $Rev: 634 $
 */


/**
 * myVideoView class
 * 
 * Provides the "myVideoView" page
 * 
 * @package websites_mofilm.com
 * @subpackage controllers
 * @category myVideoView
 */
class myVideoView extends mvcView {

	/**
	 * @see mvcViewBase::setupInitialVars()
	 */
	function setupInitialVars() {
		parent::setupInitialVars();
	}
	
	/**
	 * Shows the myVideoView page
	 *
	 * @return void
	 */
	function showMyVideoPage() {
		$this->setCacheLevelNone();
		$this->getEngine()->assign('wwwMofilmUri', system::getConfig()->getParam('mofilm', 'wwwMofilmUri', 'http://www.mofilm.com')->getParamValue());
		$this->addCssResource(new mvcViewCss('admincss', mvcViewCss::TYPE_FILE, '/themes/mofilm/css/admin.css'));
		$this->getEngine()->assign('oModel', utilityOutputWrapper::wrap($this->getModel()));
		$this->getEngine()->assign('oResults',  utilityOutputWrapper::wrap($this->getModel()->doSearch()));
		$this->render($this->getTpl('myVideo'));
	}
	
	/**
	 * Shows the myVideoView page
	 *
	 * @return void
	 */
	function showEditVideoPage() {
		
		$this->setCacheLevelNone();
		
		$movieID = $this->getModel()->getMovieID();
		$this->getEngine()->assign('oModel', utilityOutputWrapper::wrap($this->getModel()));
		
		$tags = array_merge(mofilmTag::getTagsByMovieID($movieID, mofilmTag::TYPE_TAG), mofilmTag::getTagsByMovieID($movieID, mofilmTag::TYPE_GENRE), mofilmTag::getTagsByMovieID($movieID, mofilmTag::TYPE_CATEGORY));		
		$this->getEngine()->assign('tags', utilityOutputWrapper::wrap($tags));
	
		if ( $this->getModel()->getMovie()->getUploadStatusSet()->getVideoCloudID() == 0) {
			$this->addCssResource(new mvcViewCss('fancybox', mvcViewCss::TYPE_FILE, '/themes/mofilm/css/jquery.fancybox.css'));
			$this->addJavascriptResource(new mvcViewJavascript('fancybox', mvcViewJavascript::TYPE_FILE, '/libraries/mofilm/jquery.fancybox.js'));
			$this->addJavascriptResource(new mvcViewJavascript('fancyboxloader', mvcViewJavascript::TYPE_INLINE, "$('.fancybox').fancybox();"));
		} else {
			//$this->addBCMofilmPlayer();		    
                        if ($movieID > 5000) {
                             $this->addBCMofilmPlayer();
                         } else {
                             //$this->addMofilmPlayer();
                             $this->addCssResource(new mvcViewCss('video-js111', mvcViewCss::TYPE_FILE, '/themes/mofilm/video-js/video-js.css'));
                             $this->addJavascriptResource(new mvcViewJavascript('video-js-css', mvcViewJavascript::TYPE_FILE, '/themes/mofilm/video-js/video.js?' . mofilmConstants::JS_VERSION));
                             $this->addJavascriptResource(
                                     new mvcViewJavascript(
                                     'video-js-swf', mvcViewJavascript::TYPE_INLINE, "

                                                         videojs.options.flash.swf = '/themes/mofilm/video-js/video-js.swf';
                 "
                                     )
                             );
                         }
                    
                    
                    
		}
		
		$list = mofilmRole::listOfObjects();
		$tmp = array();
		foreach ( $list as $oObject ) {
			$tmp[] = array("label" => $oObject->getDescription(),"value" => $oObject->getDescription(),"key" => $oObject->getID());
		}
		$this->getEngine()->assign('availableRoles', json_encode($tmp));
		$this->getEngine()->assign('index', 0);
		$this->addJavascriptResource(new mvcViewJavascript('creditAutomcomplete', mvcViewJavascript::TYPE_FILE, '/libraries/mofilm/creditAutocomplete.js?'.mofilmConstants::JS_VERSION));
		$this->addJavascriptResource(new mvcViewJavascript('jqueryautocompletehtml', mvcViewJavascript::TYPE_FILE, '/libraries/jqueryautocomplete/jquery.ui.autocomplete.html.js'));
		
		$this->getEngine()->assign('doMovieSave', $this->getController()->buildUriPath(myVideoController::ACTION_USERDOEDIT));
		$this->getEngine()->assign('oMovie', utilityOutputWrapper::wrap($this->getModel()->getMovie()));
		$this->getEngine()->assign('newGenres', utilityOutputWrapper::wrap(mofilmTag::listOfObjects(null, null, mofilmTag::TYPE_GENRE)));
		$this->render($this->getTpl('editMyVideo'));
	}
	
	
	
	/**
	 * Gets the result for encoded vides for the facebook style notification
	 * 
	 */
	function showTotalEncodedVideoPage() {
		$arr = array();
		$arr["total"] = $this->getModel()->doEncodeSearch()->getTotalResults();
		echo json_encode($arr);
	}
	
	
	/**
	 * Adds the SWFObject code to laod the current movie from the model
	 * 
	 * @return void
	 */
	function addMofilmPlayer() {
		$this->addJavascriptResource(
			new mvcViewJavascript('flowPlayerLib', mvcViewJavascript::TYPE_FILE, '/libraries/flowplayer/example/flowplayer-3.2.6.min.js')
		);
		$this->addJavascriptResource(
			new mvcViewJavascript('flowPlayerLoader', mvcViewJavascript::TYPE_INLINE, '
				flowplayer("mofilmMoviePlayer", "/libraries/flowplayer/flowplayer-3.2.7.swf", {
					clip: {
						autoPlay: true,
						autoBuffering: true,
						bufferLength: 3
					},
					playlist: [
						{ url: "'.$this->getModel()->getMovie()->getThumbnailUri('l').'" },
						{ url: "'.$this->getModel()->getMovie()->getAssetSet()->getObjectByAssetAndFileType(mofilmMovieAsset::TYPE_FILE, 'FLV')->getFirst()->getCdnURL().'", autoPlay: false }
   					]
				});'
			)
		);
	}

	/**
	 * Adds the Brightcove object code to load the current movie from the model
	 * 
	 * @return void
	 */
	function addBCMofilmPlayer() {
                      $this->addJavascriptResource(
                          new mvcViewJavascript('bcPlayerLib', mvcViewJavascript::TYPE_FILE, 'https://sadmin.brightcove.com/js/BrightcoveExperiences.js')
                      );
		$this->addJavascriptResource(
			new mvcViewJavascript('bcPlayerLoader', mvcViewJavascript::TYPE_INLINE, 'brightcove.createExperiences();')
		);
	}
	
	
}