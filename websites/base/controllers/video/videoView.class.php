<?php

/**
 * videoView.class.php
 * 
 * videoView class
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package websites_base
 * @subpackage controllers
 * @category videoView
 * @version $Rev: 11 $
 */

/**
 * videoView class
 * 
 * Provides the "videoView" page
 * 
 * @package websites_base
 * @subpackage controllers
 * @category videoView
 */
class videoView extends mvcView {

    /**
     * @see mvcViewBase::setupInitialVars()
     */
    function setupInitialVars() {
        parent::setupInitialVars();
		$this->addJavascriptResource(
			new mvcViewJavascript('swfObject', mvcViewJavascript::TYPE_FILE, '/libraries/swfobject/swfobject.js')
		);
        
    }

    /**
     * Shows the videoView page
     *
     * @return void
     */
    function showVideoPage() {
        $this->setCacheLevelNone();

        //$cacheId = 'movie_player_'.$this->getModel()->getMovieReference();

        /*
          if ( !$this->isCached($this->getTpl('video', '/video'), $cacheId) ) {
          $this->addJavascriptResource(
          new mvcViewJavascript('swfObject', mvcViewJavascript::TYPE_FILE, '/libraries/swfobject/swfobject.js')
          );
          $this->addJavascriptResource(
          new mvcViewJavascript('swfObjectLoad', mvcViewJavascript::TYPE_INLINE, '
          var flashvars = {};
          flashvars.xmlFile = "/xml/'.$this->getModel()->getMovie()->getID().'.xml";
          flashvars.hotSpots = "y";

          var params = {};
          params.menu = "false";
          params.wmode = "transparent";

          params.allowFullScreen = "true";
          var attributes = {};
          swfobject.embedSWF("/resources/flash/MOFILMplayer.swf", "mofilmVideoPlayer", "604", "338", "9.0.0", "expressInstall.swf", flashvars, params, attributes);'
          )
          );
          }
         */

        $movieID = $this->getModel()->getMovie()->getID();
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

	$this->addCssResource(new mvcViewCss('fancybox', mvcViewCss::TYPE_FILE, '/themes/mofilm/css/jquery.fancybox.css'));
	$this->addJavascriptResource(new mvcViewJavascript('fancybox', mvcViewJavascript::TYPE_FILE, '/libraries/mofilm/jquery.fancybox.js'));
        $this->addJavascriptResource(new mvcViewJavascript('fancyboxloader', mvcViewJavascript::TYPE_INLINE, "$('.fancybox').fancybox();"));
        
        
        $this->getEngine()->assign('oMovie', utilityOutputWrapper::wrap($this->getModel()->getMovie()));
        $this->getEngine()->assign('oModel', utilityOutputWrapper::wrap($this->getModel()));
        $this->getEngine()->assign('oUser', utilityOutputWrapper::wrap(mofilmUserManager::getInstanceByID($this->getModel()->getMovie()->getUserID())));

        if (strstr($_SERVER['HTTP_USER_AGENT'], 'iPhone')) {
            $this->getEngine()->assign('device', 0);
        } else if (strstr($_SERVER['HTTP_USER_AGENT'], 'Android')) {
            $this->getEngine()->assign('device', 0);
        } else if (strstr($_SERVER['HTTP_USER_AGENT'], 'iPad')) {
            $this->getEngine()->assign('device', 0);
        } else {
            $this->getEngine()->assign('device', 1);
        }


        $this->render($this->getTpl('video', '/video'));
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
				flowplayer("mofilmVideoPlayer", "/libraries/flowplayer/flowplayer-3.2.7.swf", {
					clip: {
						autoPlay: true,
						autoBuffering: true,
						bufferLength: 3
					},
					playlist: [
						{ url: "' . $this->getModel()->getMovie()->getThumbnailUri('l') . '" },
						{ url: "' . $this->getModel()->getMovie()->getAssetSet()->getObjectByAssetAndFileType(mofilmMovieAsset::TYPE_FILE, 'FLV')->getFirst()->getCdnURL() . '", autoPlay: false }
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
                new mvcViewJavascript('bcPlayerApi', mvcViewJavascript::TYPE_FILE, 'https://sadmin.brightcove.com/js/APIModules_all.js')
            );
        $this->addJavascriptResource(
                new mvcViewJavascript('bcPlayerLoader', mvcViewJavascript::TYPE_INLINE, 'brightcove.createExperiences();')
        );
        $this->addJavascriptResource(
                new mvcViewJavascript(
                'BrightcoveBumperTimer', mvcViewJavascript::TYPE_INLINE, "
				    
					    // video ID for bumper asset
					    var bumperAssetID = 2236366274001,
					    videoID,
					    currentVideoID,
					    playHead,
					    player,
					    modVP,
					    modExp,
					    menuMod;


					    function onTemplateLoaded(expId) {
					    console.log('templateLoaded fired!');
					    // get reference to the player itself
					    player = bcPlayer.getPlayer(expId);
					    // get reference to video player module
					    modVP = player.getModule(APIModules.VIDEO_PLAYER);
					    // get reference to expereince module
					    modExp = player.getModule(APIModules.EXPERIENCE);
					    // get reference to menu module
					    menuMod = player.getModule(APIModules.MENU);
					    // setup event listner once 'templateReady' is fired
					    modExp.addEventListener(BCExperienceEvent.TEMPLATE_READY, onTemplateReady);

					    }

					    function onTemplateReady(e) {
					    console.log('templateReady fired!');
					    // video ID we'll load after bumper
					    videoID = modVP.getCurrentVideo().id;
					    // get reference to player playhead element
					    playeHead = modExp.getElementByID('playhead');
					    // load bumper
					    modVP.loadVideo(bumperAssetID);
					    // set up event listener for media begin event
					    modVP.addEventListener(BCMediaEvent.BEGIN, onBumperBegin);
					    // set tup event listener for media complete to track
					    // when bumper finishes playing
					    modVP.addEventListener(BCMediaEvent.COMPLETE, onBumperComplete);
					    }


					    function onBumperBegin() {
					    // get reference to currently playing ID
					    currentVideoID = modVP.getCurrentVideo().id;

					    if (currentVideoID == bumperAssetID) { // if loaded video id is our bumper
					    // hide and disable playhead by setting alpha transparency to 0
					    playeHead.setAlpha(0);
					    playeHead.setEnabled(false);

					    } else {
					    // remove event listener
					    modVP.removeEventListener(BCMediaEvent.BEGIN, onBumperBegin);
					    // show playhead element
					    playeHead.setAlpha(1);
					    playeHead.setEnabled(true);
					    }
					    }

					    function onBumperComplete() {
					    if (currentVideoID != bumperAssetID) {
					    // show link menu once video starts playing
					    menuMod.showMenuPage('Link');
					    // remove event listener
					    modVP.removeEventListener(BCMediaEvent.COMPLETE, onBumperComplete);
					    } else {
					    // load video into player after bumper finishes playing
					    modVP.loadVideo(videoID); // loadVideo() method will auto play the video,
					    // if don't wish to auto start use cueVideo()
					    }


					    }"
                )
        );
    }

}
