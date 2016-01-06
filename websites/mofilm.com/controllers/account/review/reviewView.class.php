<?php
/**
 * reviewView.class.php
 * 
 * reviewView class
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package websites_mofilm.com
 * @subpackage controllers
 * @category reviewView
 * @version $Rev: 634 $
 */


/**
 * reviewView class
 * 
 * Provides the "reviewView" page
 * 
 * @package websites_mofilm.com
 * @subpackage controllers
 * @category reviewView
 */
class reviewView extends mvcView {

	/**
	 * @see mvcViewBase::setupInitialVars()
	 */
	function setupInitialVars() {
		parent::setupInitialVars();
	}
	
	
	/**
	 * Adds the SWFObject code to load the current movie from the model
	 * 
	 * @return void
	 */
	function addMofilmPlayer() {
		$this->addCssResource(new mvcViewCss('admincss', mvcViewCss::TYPE_FILE, '/themes/mofilm/css/admin.css'));
		$this->addJavascriptResource(
			new mvcViewJavascript('swfObject', mvcViewJavascript::TYPE_FILE, '/libraries/swfobject/swfobject.js')
		);
		
		$this->addJavascriptResource(
			new mvcViewJavascript('flowPlayerLib', mvcViewJavascript::TYPE_FILE, 'http://cdn.mofilm.com/web/common/flowplayer-3.2.6.min.js')
		);
		
		$thumbnailurl = "";
		$cdnurl = "";
		if ( $this->getModel()->getMovie()->getStatus() == mofilmMovieBase::STATUS_ENCODING ) {
			$thumbnailurl = "/resources/video/thumb_300x169.png";
		$this->addJavascriptResource(
			new mvcViewJavascript('flowPlayerLoader', mvcViewJavascript::TYPE_INLINE, '
				flowplayer("mofilmMoviePlayer", "http://cdn.mofilm.com/web/common/flowplayer.commercial-3.2.7-0.swf", {
					clip: {
						autoPlay: true,
						autoBuffering: true,
						bufferLength: 3
					},
					playlist: [
						{ url: "'.$thumbnailurl.'" }
   					]
				});'
			)
		);
			
		} elseif ( $this->getModel()->getMovie()->getStatus() == mofilmMovieBase::STATUS_PENDING ) {
			$thumbnailurl = $this->getModel()->getMovie()->getThumbnailUri('l');
			$cdnurl = $this->getModel()->getMovie()->getAssetSet()->getObjectByAssetAndFileType(mofilmMovieAsset::TYPE_FILE, 'FLV')->getFirst()->getCdnURL();
		$this->addJavascriptResource(
			new mvcViewJavascript('flowPlayerLoader', mvcViewJavascript::TYPE_INLINE, '
				flowplayer("mofilmMoviePlayer", "http://cdn.mofilm.com/web/common/flowplayer.commercial-3.2.7-0.swf", {
					clip: {
						autoPlay: true,
						autoBuffering: true,
						bufferLength: 3
					},
					playlist: [
						{ url: "'.$thumbnailurl.'" },
						{ url: "'.$cdnurl.'", autoPlay: false }
   					]
				});'
			)
		);
			
		} elseif ($this->getModel()->getMovie()->getStatus() == mofilmMovieBase::STATUS_FAILED_ENCODING  ) {
			$this->getController()->redirect("/account");
		}
		
		
		
	}
	
	
	/**
	 * Shows the reviewView page
	 *
	 * @return void
	 */
	function showReviewPage() {
		$this->setCacheLevelNone();
		$this->addMofilmPlayer();
		$this->getEngine()->assign('oMovie', utilityOutputWrapper::wrap($this->getModel()->getMovie()));
		$this->render($this->getTpl('review'));
	}
	
	/**
	 * Handles the userCommitPage
	 * 
	 * @return string
	 */
	function showUserCommitPage() {
		$arr = array();
		$arr["name"] = "done";
		echo json_encode($arr);
	}

	/**
	 * Handles the userRejectPage
	 * 
	 * @return string
	 */
	function showUserRejectPage() {
		$arr = array();
		$arr["name"] = "done";
		echo json_encode($arr);
	}
	
	/**
	 * Indicates to the user if the movie is ready to be committed
	 * 
	 * @return string
	 */
	function showIsReadyCommitPage() {
		$arr = array();
		if ( $this->getModel()->isCommitReady() ) {
			$arr["name"] = "done";
		} else {
			$arr["name"] = "Videos still needs to be encoded";
		}
		echo json_encode($arr);		
	}
}