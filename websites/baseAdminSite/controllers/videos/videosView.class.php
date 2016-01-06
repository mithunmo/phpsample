<?php
/**
 * videosView.class.php
 *
 * videosView class
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category videosView
 * @version $Rev: 324 $
 */


/**
 * videosView class
 *
 * Provides the "videosView" page
 *
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category videosView
 */
class videosView extends mvcView {

	/**
	 * @see mvcViewBase::setupInitialVars()
	 */
	function setupInitialVars() {
		parent::setupInitialVars();

		/*
		 * Add any further custom setup for the view that is needed on every request
		 */
		if ( system::getConfig()->isProduction() && $this->getRequest()->getSession()->getUser()->getClientID() == 1 ) {			
			$this->getEngine()->assign('daoUriView', $this->buildUriPath(videosController::ACTION_SOLR_SEARCH));
		} else {
			$this->getEngine()->assign('daoUriView', $this->buildUriPath(videosController::ACTION_SEARCH));
		}
		$this->getEngine()->assign('doEditURI', $this->buildUriPath(videosController::ACTION_DO_EDIT));
		$this->getEngine()->assign('editURI', $this->buildUriPath(videosController::ACTION_EDIT));
		$this->getEngine()->assign('reviewURI', $this->buildUriPath(videosController::ACTION_REVIEW));
		$this->getEngine()->assign('viewURI', $this->buildUriPath(videosController::ACTION_VIEW));
		$this->getEngine()->assign('watchURI', $this->buildUriPath(videosController::ACTION_WATCH));
		$this->getEngine()->assign('rateURI', $this->buildUriPath(videosController::ACTION_RATE));
		$this->getEngine()->assign('statusURI', $this->buildUriPath(videosController::ACTION_STATUS));
		$this->getEngine()->assign('changeUserURI', $this->buildUriPath(videosController::ACTION_CHANGE_USER));
		$this->getEngine()->assign('doChangeUserURI', $this->buildUriPath(videosController::ACTION_DO_CHANGE_USER));
		$this->getEngine()->assign('doModerationCommentUri', $this->buildUriPath(videosController::ACTION_DO_MOD_COMMENT));
		
		$rootPath = system::getConfig()->getPathWebsites().system::getDirSeparator().'base';
		$this->getEngine()->assign('adminEventFolder', str_replace($rootPath, '', mofilmConstants::getAdminEventsFolder()));
		$this->getEngine()->assign('adminSourceFolder', str_replace($rootPath, '', mofilmConstants::getAdminSourceFolder()));
		$this->getEngine()->assign('clientEventFolder', str_replace($rootPath, '', mofilmConstants::getClientEventsFolder()));
		$this->getEngine()->assign('clientSourceFolder', str_replace($rootPath, '', mofilmConstants::getClientSourceFolder()));

		$this->getEngine()->assign('myMofilmUri', system::getConfig()->getParam('mofilm', 'myMofilmUri', 'http://mofilm.com')->getParamValue());
		$this->getEngine()->assign('wwwMofilmUri', system::getConfig()->getParam('mofilm', 'wwwMofilmUri', 'http://www.mofilm.com')->getParamValue());

		$this->addJavascriptResource(
			new mvcViewJavascript('swfObject', mvcViewJavascript::TYPE_FILE, '/libraries/swfobject/swfobject.js')
		);
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

	/**
	 * Adds the movie rating scripts and styles
	 * 
	 * @param string $inFormElement
	 * @return void
	 */
	function addMovieRating($inFormElement = '#movieRatingForm') {
		$this->addCssResource(new mvcViewCss('uiStarsCss', mvcViewCss::TYPE_FILE, '/libraries/jquery-ui-stars/jquery-ui.stars.min.css'));
		$this->addJavascriptResource(new mvcViewJavascript('uiStarsJs', mvcViewJavascript::TYPE_FILE, '/libraries/jquery-ui-stars/jquery-ui.stars.min.js'));
		$this->addJavascriptResource(
			new mvcViewJavascript('uiStarsInit', mvcViewJavascript::TYPE_INLINE, '
			if ( $("#mofilmMovieAverageRating").length > 0 ) { 
				$("#mofilmMovieAverageRating").stars({
					disabled: true,
					cancelShow: false
				});
				$("#mofilmMovieRating").stars({
					cancelShow: false,
					callback: function(ui, type, value) {
						opt = {
							MovieID: $("#MasterMovieID").val(),
							Rating: value
						}
						$.post("'.$this->buildUriPath(videosController::ACTION_RATE).'/as.xml", opt, function(xml) {
							avgRating = parseInt($(xml).find(\'rating\').text());
							count = $(xml).find(\'ratingCount\').text();
							$("#mofilmMovieAverageRating").stars("selectID", avgRating-1);
							$("#mofilmMovieAverageRatingCount").text(count);
						}, \'xml\');
					}
				});
			}')
		);
	}
	
	/**
	 * Adds the movie status AJAX controls
	 * 
	 * @return void
	 */
	function addMovieStatus() {
		/*
		 * Shared ajax function for the actual submission, replaces elements and text too
		 */
		$ajax = '
			$.get(link.attr(\'href\')+"/as.json", null, function(data, textStatus, XMLHttpRequest) {
				$(\'#body div.container\').append(\'<div class="messageBox \'+data.status+\'"><p>\'+data.message+\'</p></div>\');
				$(\'#body div.container div.messageBox\').delay(2000).slideUp(200);
				if ( $(\'#movieStatus\').length > 0 ) {
					status = link.attr(\'href\').replace(/\/videos\/status\/\d+\//, \'\');
					$(\'#movieStatus\').val(status);
					$(\'.statusUpdate.approve img\').remove();
					$(\'.statusUpdate.reject img\').remove();
					
					if ( status == \'Approved\' ) {
						$(\'.statusUpdate.approve\').html(\'&nbsp;Approved\')
						$(\'.statusUpdate.reject\').html(\'&nbsp;Reject\')
						$(\'.statusUpdate.approve\').prepend(\'<img src="/themes/shared/icons/tick.png" alt="approved" class="smallIcon" />\');
					} else {
						$(\'.statusUpdate.approve\').html(\'&nbsp;Approve\')
						$(\'.statusUpdate.reject\').html(\'&nbsp;Rejected\')
						$(\'.statusUpdate.reject\').prepend(\'<img src="/themes/shared/icons/cross.png" alt="rejected" class="smallIcon" />\');
					}
				}
				return false;
			}, \'json\');';

		if ( $this->getModel()->getMovie()->getModeratorID() && $this->getModel()->getMovie()->getStatus() != mofilmMovie::STATUS_PENDING ) {
			$this->addJavascriptResource(
				new mvcViewJavascript('ajaxStatusUpdate', mvcViewJavascript::TYPE_INLINE, '
				$("a.statusUpdate").click(function(){
					var link = $(this);
					if ( confirm(\'This movie has already been moderated by '.$this->getModel()->getMovie()->getModerator()->getFullname().'. Are you sure you wish to change the status?\') ) {
						'.$ajax.'
					}
					return false;
				});')
			);
		} else {
			$this->addJavascriptResource(
				new mvcViewJavascript('ajaxStatusUpdate', mvcViewJavascript::TYPE_INLINE, '
				$("a.statusUpdate").click(function(){
					var link = $(this);
					'.$ajax.'
					return false;
				});')
			);
		}
	}
	
	/**
	 * Adds the award libs and stuff
	 * 
	 * @return void
	 */
	function addMovieAward() {
		$this->addCssResource(new mvcViewCss('uiCustomInputCss', mvcViewCss::TYPE_FILE, '/libraries/jquery-plugins/input-ui/input-ui.css'));
		$this->addJavascriptResource(new mvcViewJavascript('uiCustomInputJs', mvcViewJavascript::TYPE_FILE, '/libraries/jquery-plugins/jquery.input-ui.js'));
	}

	/**
	 * Shows the videosView page
	 *
	 * @return void
	 */
	function showVideosPage() {
		$this->setCacheLevelNone();
		
		$query = $this->getController()->getSearchQuery();
		if ( array_key_exists('Display', $query) && in_array($query['Display'], array('list', 'grid')) ) {
			$display = $query['Display'];
		} else {
			$display = 'list';
		}
		$corporateID = $this->getController()->getCorporateQuery();
                $brandID     = $this->getController()->getBrandQuery();
		$this->getEngine()->assign('oModel', utilityOutputWrapper::wrap($this->getModel()));
		$this->getEngine()->assign('oResults', utilityOutputWrapper::wrap($this->getModel()->doSearch($corporateID,$brandID)));
		$this->getEngine()->assign('rawDaoSearchQuery', $this->getController()->getSearchQueryAsString());
		
		$query = $this->getController()->getSearchQuery();
		$query['OrderBy'] = $this->getModel()->getSearchResult()->getSearchInterface()->getOrderBy();
		$query['OrderDir'] = $this->getModel()->getSearchResult()->getSearchInterface()->getOrderDirection();
		$this->getController()->setSearchQuery($query);
		
		$this->getEngine()->assign('daoSearchQuery', $this->getController()->getSearchQueryAsString());
		$this->getEngine()->assign('searchDisplay', $display);
		$this->getEngine()->assign('searchOrderBy', $this->getModel()->getSearchResult()->getSearchInterface()->getOrderBy());
		$this->getEngine()->assign('searchOrderDir', $this->getModel()->getSearchResult()->getSearchInterface()->getOrderDirection());
		$this->getEngine()->assign('newGenres', utilityOutputWrapper::wrap(mofilmTag::listOfObjects(null, null, mofilmTag::TYPE_GENRE)));

		if ( $this->getController()->getAction() == usersController::ACTION_SEARCH ) {
			$this->getEngine()->assign('searchStatus', $this->getModel()->getSearchResult()->getSearchInterface()->getStatus());
			$this->getEngine()->assign('searchKeywords', $this->getModel()->getSearchResult()->getSearchInterface()->getKeywords());
			$this->getEngine()->assign('searchEventID', isset($query['EventID']) ? $query['EventID'] : null);
			$this->getEngine()->assign('searchSourceID', isset($query['SourceID']) ? $query['SourceID'] : 0);
			$this->getEngine()->assign('searchUserID', $this->getModel()->getSearchResult()->getSearchInterface()->getUserID());
			$this->getEngine()->assign('searchFavourites', $this->getModel()->getSearchResult()->getSearchInterface()->getOnlyFavourites());
			$this->getEngine()->assign('searchTags', $this->getModel()->getSearchResult()->getSearchInterface()->getOnlyTags());
			$this->getEngine()->assign('searchAward', $this->getModel()->getSearchResult()->getSearchInterface()->getAwardType());
		}
		
		$this->render($this->getTpl('videos'));
	}

	/**
	 * Shows the videos page using Solr
	 * 
	 */
	function showSearchVideosPage() {
		$query = $this->getController()->getSearchQuery();
		
		if ( array_key_exists('Display', $query) && in_array($query['Display'], array('list', 'grid')) ) {
			$display = $query['Display'];
		} else {
			$display = 'list';
		}
		
		
		$this->setCacheLevelNone();
		$this->getEngine()->assign('daoUriView', $this->buildUriPath(videosController::ACTION_SOLR_SEARCH));		
		$this->getEngine()->assign('oModel', utilityOutputWrapper::wrap($this->getModel()));
		$this->getEngine()->assign('oController', utilityOutputWrapper::wrap($this->getController()));
		
		$this->getEngine()->assign('oResults', $this->getModel()->doSolrVideoSearch());
                systemLog::message("raw".$this->getController()->getSearchQueryAsString());
		$this->getEngine()->assign('rawDaoSearchQuery', $this->getController()->getSearchQueryAsString());
		
		
		$query['OrderBy'] = $this->getModel()->getSolrVideoSearch()->getOrderBy();
		$query['OrderDir'] = $this->getModel()->getSolrVideoSearch()->getOrderDirection();
		$this->getController()->setSearchQuery($query);

		$this->getEngine()->assign('newGenres', utilityOutputWrapper::wrap(mofilmTag::listOfObjects(null, null, mofilmTag::TYPE_GENRE)));
		
		$this->getEngine()->assign('daoSearchQuery', $this->getController()->getSearchQueryAsString());
		$this->getEngine()->assign('searchDisplay', $display);
		
		$this->getEngine()->assign('searchOrderBy', $this->getModel()->getSolrVideoSearch()->getOrderBy());
		$this->getEngine()->assign('searchOrderDir', $this->getModel()->getSolrVideoSearch()->getOrderDirection());
		
		if ( $this->getModel()->getSolrVideoSearch()->getKeyword() != "*:*") {
                    systemLog::message("set keyword".urldecode($this->getModel()->getSolrVideoSearch()->getKeyword()));
			$this->getEngine()->assign('searchKeywords', urldecode($this->getModel()->getSolrVideoSearch()->getKeyword()));		
		}


		$this->getEngine()->assign('searchFavourites', $this->getModel()->getSolrVideoSearch()->getFavourites());
		$this->getEngine()->assign('searchTags', $this->getModel()->getSolrVideoSearch()->getTags());
		$this->getEngine()->assign('searchOnlyTitles', $this->getModel()->getSolrVideoSearch()->getTitles());

		$this->getEngine()->assign('searchStatus', $this->getModel()->getSolrVideoSearch()->getStatus());
		$this->getEngine()->assign('searchEventID', isset($query['EventID']) ? $query['EventID'] : null);
		$this->getEngine()->assign('searchSourceID', isset($query['SourceID']) ? $query['SourceID'] : 0);
		$this->getEngine()->assign('searchUserID', $this->getModel()->getSolrVideoSearch()->getUserID());
		$this->getEngine()->assign('searchAward', $this->getModel()->getSolrVideoSearch()->getType());
		$this->render($this->getTpl('videoSearch'));
	}
	
	
	/**
	 * Shows the videosView page
	 *
	 * @return void
	 */
	function showReviewPage() {
		$this->setCacheLevelNone();
		
		$this->getEngine()->assign('daoUriView', $this->buildUriPath(videosController::ACTION_REVIEW));
		$this->getEngine()->assign('oModel', utilityOutputWrapper::wrap($this->getModel()));
		$this->getEngine()->assign('oResults', utilityOutputWrapper::wrap($this->getModel()->doSearch()));
		$this->getEngine()->assign('daoSearchArray', $this->getController()->getSearchQuery());
		$this->getEngine()->assign('daoSearchQuery', $this->getController()->getSearchQueryAsString());
		$this->getEngine()->assign('oMovie', utilityOutputWrapper::wrap($this->getModel()->getMovie()));
		
		if ( $this->getModel()->getMovie() ) {
			$this->addMofilmPlayer();
			$this->addMovieRating();
			$this->addMovieStatus();
			
			$this->addJavascriptResource(
				new mvcViewJavascript('scrollableMovieLib', mvcViewJavascript::TYPE_FILE, '/libraries/jquery-tools/jquery.tools.min.js')
			);
			$this->addJavascriptResource(
				new mvcViewJavascript(
					'scrollableMovies', mvcViewJavascript::TYPE_INLINE, "
						$(function() {
							$('.scrollable').scrollable({ vertical: true });
						});
					"
				)
			);
		}
		
		$this->render($this->getTpl('review'));
	}
	
	/**
	 * Shows the video player page
	 * 
	 * @return void
	 */
	function showWatchPage() {
		$this->setCacheLevelNone();
		
		$this->getEngine()->assign('daoUriView', $this->buildUriPath(videosController::ACTION_REVIEW));
		$this->getEngine()->assign('oModel', utilityOutputWrapper::wrap($this->getModel()));
		$this->getEngine()->assign('oMovie', utilityOutputWrapper::wrap($this->getModel()->getMovie()));
		$movieID = $this->getModel()->getMovieID();
		
		if ( $this->getModel()->getMovie()->getUploadStatusSet()->getVideoCloudID() == 0) {
			$this->addCssResource(new mvcViewCss('fancybox', mvcViewCss::TYPE_FILE, '/themes/mofilm/css/jquery.fancybox.css'));
			$this->addJavascriptResource(new mvcViewJavascript('fancybox', mvcViewJavascript::TYPE_FILE, '/libraries/mofilm/jquery.fancybox.js'));
			$this->addJavascriptResource(new mvcViewJavascript('fancyboxloader', mvcViewJavascript::TYPE_INLINE, "$('.fancybox').fancybox();"));
		} else {
			$this->addBCMofilmPlayer();
		}

		$this->addMovieRating();
		
		$this->render($this->getTpl('watch'));
	}
	
	/**
	 * Shows the video editing page
	 * 
	 * @return void
	 */
	function showEditPage() {
		$this->setCacheLevelNone();  
		
		/* Add data */
		$this->getEngine()->assign('daoUriView', $this->buildUriPath(videosController::ACTION_REVIEW));
		$this->getEngine()->assign('oModel', utilityOutputWrapper::wrap($this->getModel()));
		$this->getEngine()->assign('oMovie', utilityOutputWrapper::wrap($this->getModel()->getMovie()));
		$this->getEngine()->assign('tags', utilityOutputWrapper::wrap(mofilmTag::getTagsByMovieID($this->getModel()->getMovieID(), mofilmTag::TYPE_TAG)));
		$this->getEngine()->assign('genres', utilityOutputWrapper::wrap(mofilmTag::getTagsByMovieID($this->getModel()->getMovieID(), mofilmTag::TYPE_GENRE)));
		$this->getEngine()->assign('categories', utilityOutputWrapper::wrap(mofilmTag::getTagsByMovieID($this->getModel()->getMovieID(), mofilmTag::TYPE_CATEGORY)));
		$this->getEngine()->assign('newGenres', utilityOutputWrapper::wrap(mofilmTag::listOfObjects(null, null, mofilmTag::TYPE_GENRE)));
                
		$this->getEngine()->assign('channel', utilityOutputWrapper::wrap(mofilmChannel::listOfObjects()));
                $this->getEngine()->assign('dist', utilityOutputWrapper::wrap(mofilmDistribution::listOfObjects()));
                $this->getEngine()->assign("movieChannel", mofilmMovieChannel::getInstanceByMovieID($this->getModel()->getMovie()->getID()));
               
                
                 
                if ( $this->getModel()->getMovie()->getID() > 5000 ) {
                    $this->getEngine()->assign('result', $this->getModel()->BCMovieAssets());
                }
                
                if ( $this->getModel()->getMovieID()) {
                    $data['MovieID'] = $this->getModel()->getMovieID();
                    $this->getEngine()->assign('brandDetails', $this->getModel()->getBrand($data['MovieID']));
                    $this->getEngine()->assign('eventType', $this->getModel()->getEventType($data));
                    $this->getEngine()->assign('movieAwards', $this->getModel()->getMovieAwards($data));
                   
                    $this->getEngine()->assign('awardBestOfClient', $this->getModel()->checkBestOfClientExist($data));
                }
		// $this->getEngine()->assign('tags', utilityOutputWrapper::wrap(mofilmTag::listOfObjects(null, null, mofilmTag::TYPE_TAG)));
		// $this->getEngine()->assign('categories', utilityOutputWrapper::wrap(mofilmTag::listOfObjects(null, null, mofilmTag::TYPE_CATEGORY)));
		// $this->getEngine()->assign('categories', utilityOutputWrapper::wrap(mofilmCategory::listOfObjects(null, null)));

		$noEditStatus = array(mofilmMovieBase::STATUS_ENCODING, mofilmMovieBase::STATUS_FAILED_ENCODING);
		
		$this->addJavascriptResource(
			new mvcViewJavascript(
				'tagsTab', mvcViewJavascript::TYPE_INLINE, "
					$(function() {
						$( '#tabs').tabs();
					});"
			)
		);
				
		if ( !in_array($this->getModel()->getMovie()->getStatus(), $noEditStatus) ) {
			/* Add controls */
		    
			$movieID = $this->getModel()->getMovieID();
			
			if ($this->getModel()->getMovie()->getUploadStatusSet()->getVideoCloudID() == 0) {
				$this->addCssResource(new mvcViewCss('fancybox', mvcViewCss::TYPE_FILE, '/themes/mofilm/css/jquery.fancybox.css'));
				$this->addJavascriptResource(new mvcViewJavascript('fancybox', mvcViewJavascript::TYPE_FILE, '/libraries/mofilm/jquery.fancybox.js'));
				$this->addJavascriptResource(new mvcViewJavascript('fancyboxloader', mvcViewJavascript::TYPE_INLINE, "$('.fancybox').fancybox();"));
			} else {
				$this->addBCMofilmPlayer();
			}
			
			$this->addMovieRating('#movieDetailsForm');
			$this->addMovieStatus();
			$this->addMovieAward();
			
			/* Add contributor roles */
			$list = mofilmRole::listOfObjects();
                        $tmp = array();
			foreach ( $list as $oObject ) {
				//$tmp[] = $oObject->getDescription();
				$tmp[] = array("label" => $oObject->getDescription(),"value" => $oObject->getDescription(),"key" => $oObject->getID());

			}
                        
                        if (strstr($_SERVER['HTTP_USER_AGENT'],'iPhone') ) {
                            $this->getEngine()->assign('device', 0);
                        } else if (strstr($_SERVER['HTTP_USER_AGENT'],'Android')) {
                            $this->getEngine()->assign('device', 0);
                        } else if (strstr($_SERVER['HTTP_USER_AGENT'],'iPad')) {
                            $this->getEngine()->assign('device', 0);
                        } else {
                            $this->getEngine()->assign('device', 1);
                        }

                        $countrylist = mofilmTerritory::listOfObjects();
                        $tmpCountry = array();
			foreach ( $countrylist as $oObject ) {
				//$tmp[] = $oObject->getDescription();
				$tmpCountry[] = array($oObject->getID() => $oObject->getCountry());

			}
                        
                        $this->getEngine()->assign('countryList', json_encode($tmpCountry));
			$this->getEngine()->assign('bIndex', 0);
                        $this->addJavascriptResource(new mvcViewJavascript('loadCountries', mvcViewJavascript::TYPE_FILE, '/libraries/mofilm/loadCountries.js?'.mofilmConstants::JS_VERSION));
                        
			$this->getEngine()->assign('availableRoles', json_encode($tmp));
			$this->getEngine()->assign('index', 0);
			$this->addJavascriptResource(new mvcViewJavascript('creditAutomcomplete', mvcViewJavascript::TYPE_FILE, '/libraries/mofilm/creditAutocomplete.js?'.mofilmConstants::JS_VERSION));

                        $this->addCssResource(new mvcViewCss('video-js', mvcViewCss::TYPE_FILE, '/themes/mofilm/video-js/video-js.css'));
                        $this->addJavascriptResource(new mvcViewJavascript('video-js-css', mvcViewJavascript::TYPE_FILE, '/themes/mofilm/video-js/video.js?'.mofilmConstants::JS_VERSION));                
                        $this->addJavascriptResource(
                            new mvcViewJavascript(
                                    'video-js-swf', mvcViewJavascript::TYPE_INLINE, "
                              
                                        videojs.options.flash.swf = '/themes/mofilm/video-js/video-js.swf';
"
                            )
                        );
                        
                        

                        
                        //$this->addJavascriptResource(new mvcViewJavascript('flowp', mvcViewJavascript::TYPE_FILE, '/themes/mofilm/flowplayer/flowplayer.min.js?'.mofilmConstants::JS_VERSION));                
                        //$this->addCssResource(new mvcViewCss('flowcss', mvcViewCss::TYPE_FILE, '/themes/mofilm/flowplayer/skin/functional.css'));
                        
			$this->addJavascriptResource(new mvcViewJavascript('jqueryautocompletehtml', mvcViewJavascript::TYPE_FILE, '/libraries/jqueryautocomplete/jquery.ui.autocomplete.html.js'));
			$this->render($this->getTpl('edit'));
		} else {
			$this->render($this->getTpl('editNotAvailable'));
		}
	}

	/**
	 * Displays the change user pages
	 *
	 * @return void
	 */
	function showChangeUserPage() {
		$this->setCacheLevelNone();

		$this->getEngine()->assign('daoUriView', $this->buildUriPath(videosController::ACTION_CHANGE_USER, $this->getModel()->getMovieID()));
		$this->getEngine()->assign('oModel', utilityOutputWrapper::wrap($this->getModel()));
		$this->getEngine()->assign('oMovie', utilityOutputWrapper::wrap($this->getModel()->getMovie()));
		$this->getEngine()->assign('oResults', utilityOutputWrapper::wrap($this->getModel()->doUserSearch()));
		$this->getEngine()->assign('daoSearchQuery', $this->getController()->getSearchQueryAsString());

		$this->render($this->getTpl('changeUser'));
	}

	function showChangeUserConfirmationPage() {
		$this->setCacheLevelNone();

		$this->getEngine()->assign('daoUriView', $this->buildUriPath(videosController::ACTION_CHANGE_USER, $this->getModel()->getMovieID()));
		$this->getEngine()->assign('oModel', utilityOutputWrapper::wrap($this->getModel()));
		$this->getEngine()->assign('oMovie', utilityOutputWrapper::wrap($this->getModel()->getMovie()));
		$this->getEngine()->assign('oSwitchUser', utilityOutputWrapper::wrap($this->getModel()->getSwitchUser()));

		$this->render($this->getTpl('changeUserConfirmation'));
	}
	
	/**
	 * Sends the rating result for AJAX requests
	 * 
	 * @return void
	 */
	function sendRatingResult() {
		$this->setCacheLevelNone();
		
		$this->getEngine()->assign('oModel', utilityOutputWrapper::wrap($this->getModel()));
		$this->getEngine()->assign('oMovie', utilityOutputWrapper::wrap($this->getModel()->getMovie()));
		
		$this->render($this->getTpl('ratingResult'));
	}
	
	/**
	 * Sends the status update result for AJAX requests
	 * 
	 * @return void
	 */
	function sendStatusResult() {
		$this->setCacheLevelNone();
		
		$this->getEngine()->assign('oModel', utilityOutputWrapper::wrap($this->getModel()));
		$this->getEngine()->assign('oMovie', utilityOutputWrapper::wrap($this->getModel()->getMovie()));
		
		$this->render($this->getTpl('statusResult'));
	}
	
	/**
	 * Sends a JSON response for AJAX calls
	 * 
	 * @param string $inMessage Message to display
	 * @param mixed $inStatus Status of result, 0 = info, true = success, false = error, 
	 * @return void
	 */
	function sendJsonResult($inMessage, $inStatus) {
		$this->setCacheLevelNone();
		
		$response = json_encode(
			array(
				'status' => $inStatus === mvcSession::MESSAGE_INFO ? 'info' : ($inStatus === mvcSession::MESSAGE_OK ? 'success' : 'error'),
				'message' => $inMessage,
			)
		);
		echo $response;
	}
	
	
	
	/**
	 * Fetches the movie stats for the current user
	 * 
	 * @return string
	 */
	function getMovieStatsView() {
		$this->setCacheLevelNone();
		
		$this->getEngine()->assign('oStats', utilityOutputWrapper::wrap($this->getModel()->getMovieStats()));
		
		return $this->compile($this->getTpl('movieStats', '/videos'));
	}
	
	/**
	 * Returns the number of movies the current user has to review
	 * 
	 * @return string
	 */
	function getMovieReviewCountView() {
		$this->setCacheLevelNone();
		
		$oResults = $this->getModel()->getVideoSearch()->search();
		
		return $oResults->getTotalResults();
	}
	
	/**
	 * 
	 */
	function playHDVideo() {
		$this->setCacheLevelNone();
		$this->render($this->getTpl('HDVideo'));
	}

	/**
	 * Creates a fragment award list view
	 *
	 * @return void
	 */
	function getAwardList() {
		$this->setCacheLevelNone();

		$this->getEngine()->assign('isFragment', true);
		$this->getEngine()->assign('oMovie', utilityOutputWrapper::wrap($this->getModel()->getMovie()));

		$this->render($this->getTpl('editMovieAwardsHistoryList'));
	}

	/**
	 * Creates a fragment award list view
	 *
	 * @return void
	 */
	function getCommentList() {
		$this->setCacheLevelNone();

		$this->getEngine()->assign('isFragment', true);
		$this->getEngine()->assign('oMovie', utilityOutputWrapper::wrap($this->getModel()->getMovie()));

		$this->render($this->getTpl('editMovieCommentsList'));
	}
		
}