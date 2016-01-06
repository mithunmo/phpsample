<?php
/**
 * grantsView.class.php
 * 
 * grantsView class
 *
 * @author Pavan Kumar P G
 * @copyright Mofilm (c) 2009-2010
 * @package websites_mofilm.com
 * @subpackage controllers
 * @category grantsView
 * @version $Rev: 623 $
 */


/**
 * grantsView class
 * 
 * Provides the "grantsView" page
 * 
 * @package websites_mofilm.com
 * @subpackage controllers
 * @category grantsView
 */
class grantsView extends mvcView {
	
	/**
	 * Assigns some default values to template engine that are always needed
	 *
	 * @return void
	 */
	function setupInitialVars() {
		parent::setupInitialVars();
		
		$this->getEngine()->assign('daoUriView', $this->buildUriPath(grantsController::ACTION_SEARCH));
	}

	/**
	 * @see mvcDaoView::getObjectListView()
	 */
	function getObjectListView() {
            
		$this->getEngine()->assign('oModel', utilityOutputWrapper::wrap($this->getModel()));
		$this->getEngine()->assign('oResults', utilityOutputWrapper::wrap($this->getModel()->doSearch()));
		$this->getEngine()->assign('pagingOffset', utilityOutputWrapper::wrap($this->getModel()->getGrantsSearch()->getOffset()));
		
                $this->getEngine()->assign('rawDaoSearchQuery', $this->getController()->getSearchQueryAsString());
                
                $query = $this->getController()->getSearchQuery();
		$query['OrderBy'] = $this->getModel()->getSearchResult()->getSearchInterface()->getOrderBy();
		$query['OrderDir'] = $this->getModel()->getSearchResult()->getSearchInterface()->getOrderDirection();
		
                $this->getController()->setSearchQuery($query);
                                
                $this->getEngine()->assign('daoSearchQuery', $this->getController()->getSearchQueryAsString());
                
            	$this->getEngine()->assign('searchOrderBy',  $this->getModel()->getSearchResult()->getSearchInterface()->getOrderBy());
		$this->getEngine()->assign('searchOrderDir', $this->getModel()->getSearchResult()->getSearchInterface()->getOrderDirection());
		
		if ( $this->getController()->getAction() == grantsController::ACTION_SEARCH ) {
			$this->getEngine()->assign('searchStatus', $this->getModel()->getGrantsSearch()->getStatus());
			
			if ( $this->getModel()->getGrantsSearch()->getEventCount() == 1 ) {
				$tmp = $this->getModel()->getGrantsSearch()->getEvents();
				$inEventID = $tmp[0];
			} else {
				$inEventID = 0;
			}
			$this->getEngine()->assign('searchEventID', $inEventID);
			unset ($tmp);
			
			if ( $this->getModel()->getGrantsSearch()->getSourceCount() > 1 ) {
				$tmp = $this->getModel()->getGrantsSearch()->getSources();
				$inSourceID = mofilmSource::getInstance($tmp[0])->getName();
			} elseif ( $this->getModel()->getGrantsSearch()->getSourceCount() == 1 ) {
				$tmp = $this->getModel()->getGrantsSearch()->getSources(); 
				$inSourceID = $tmp[0];
			} else {
				$inSourceID = 0;
			}
			$this->getEngine()->assign('searchSourceID', $inSourceID);
			unset ($tmp);
		}
		
		$this->render($this->getTpl('grantsList'));
	}
	
	/**
	 * @see mvcDaoView::getObjectFormView()
	 */
	function getObjectFormView() {
		return $this->getTpl('grantsForm');
	}
	
	/**
	 * 
	 */
	function showApplyForGrants($inSourceID = null) {
		$this->getEngine()->assign('oGrants', utilityOutputWrapper::wrap(mofilmGrants::getInstanceBySourceID($inSourceID)));
		$this->getEngine()->assign('oSource', utilityOutputWrapper::wrap(mofilmSource::getInstance($inSourceID)));
		$this->render($this->getTpl('apply'));
	}
	
	/*
	 * 
	 */
	function grantView($grantProductID, $inGrantID = null) {
                $grantInstance = utilityOutputWrapper::wrap(mofilmUserMovieGrants::getInstance($inGrantID));
                $user_id = $grantInstance->getUserID();
                $userInstance = utilityOutputWrapper::wrap(mofilmUserProfile::getUserActiveProfile($user_id));
                $this->getEngine()->assign('oGrant',$grantInstance );
                $this->getEngine()->assign('oTest',$userInstance );
                if($grantProductID == "12") {
                    $this->render($this->getTpl('mosaicview'));
                } elseif($grantProductID == "5"){
                    $ograntInstance = mofilmUserMovieGrants::getInstance($inGrantID);
                    $GID = $ograntInstance->getGrantID();
                    $oGrantData = new grantdata();
                    $grantUpdate = $oGrantData->getValue($GID,"Question");
                    if(isset($grantUpdate))
                    {
                        $QuestionVal = $grantUpdate->getParamValue();
                        $this->getEngine()->assign('QuestionVal',$QuestionVal );
                    }
                    $this->render($this->getTpl('proview'));
                } elseif($grantProductID == "3"){
                    $ograntInstance = mofilmUserMovieGrants::getInstance($inGrantID);
                    $SID = $ograntInstance->getGrants()->getSourceID();
                    if($SID == "941")
                        $this->render($this->getTpl('captainmorganmomindsview'));
                    else if(($SID == "959") || ($SID == "963"))
                        $this->render($this->getTpl('dosview'));
                    else    
                        $this->render($this->getTpl('momindsview'));
                } else {
                    $this->addGrantRating();
                    $avgRating = mofilmUserGrantsRating::averageGrantRating($inGrantID);
                    $this->getEngine()->assign('avgRating', ceil($avgRating[0]));
                    $this->getEngine()->assign('RatingCount', $avgRating[1]);
                    $this->getEngine()->assign('ratingList', mofilmUserGrantsRating::listOfGrantRatings($inGrantID));                    
                    $this->render($this->getTpl('view'));
                }
	}
	
	/*
	 * 
	 */
	function sendEmail($inUserMovieGrantID = null) {
		$this->getEngine()->assign('oUserMovieGrant', utilityOutputWrapper::wrap(mofilmUserMovieGrants::getInstance($inUserMovieGrantID)));
		$this->render($this->getTpl('sendEmail'));
	}
	
	/*
	 * 
	 */
	function grantEdit($grantProductID,$inGrantID = null) {
		$oGrants = mofilmUserMovieGrants::getInstance($inGrantID);
		$this->getEngine()->assign('oGrant', utilityOutputWrapper::wrap($oGrants));
		
		$this->getEngine()->assign('oResults', utilityOutputWrapper::wrap($this->getModel()->doSearch()));
		
		$inGrantsDisbursed = mofilmUserMovieGrants::totalGrantsDisbursed($oGrants->getGrants()->getSourceID());
		$this->getEngine()->assign('inGrantsDisbursed', $inGrantsDisbursed);
                $userGrantRating = mofilmUserGrantsRating::getInstanceByGrantID($inGrantID, $this->getRequest()->getSession()->getUser()->getID());                
                $this->getEngine()->assign('userRating', $userGrantRating->getRating());
                $this->addGrantRating(); 
                
                $sourceID = $oGrants->getGrants()->getSourceID();
                $oMofilmSourceBudget = new mofilmSourceBudget();
                $checkBudget = $oMofilmSourceBudget->checkIfBudgetExists($sourceID);
                if(isset($checkBudget))
                {
                    $sourceBudget = mofilmSourceBudget::getInstance($checkBudget);
                    $bufferGrant = $sourceBudget->getGrantBuffer();
                    $this->getEngine()->assign('bufferGrant', $bufferGrant);
                }
                
                $avgRating = mofilmUserGrantsRating::averageGrantRating($inGrantID);
                $this->getEngine()->assign('avgRating', ceil($avgRating[0]));
                $this->getEngine()->assign('RatingCount', $avgRating[1]);
                $this->getEngine()->assign('ratingList', mofilmUserGrantsRating::listOfGrantRatings($inGrantID));
                if($grantProductID == "12")
                    $this->render($this->getTpl('mosaicedit'));
                elseif($grantProductID == "5"){
                    $ograntInstance = mofilmUserMovieGrants::getInstance($inGrantID);
                    $GID = $ograntInstance->getGrantID();
                    $oGrantData = new grantdata();
                    $grantUpdate = $oGrantData->getValue($GID,"Question");
                    if(isset($grantUpdate))
                    {
                        $QuestionVal = $grantUpdate->getParamValue();
                        $this->getEngine()->assign('QuestionVal',$QuestionVal );
                    }
                    $this->render($this->getTpl('proedit'));
                }
                elseif($grantProductID == "3")
                {
                    if($sourceID == "941")
                        $this->render($this->getTpl('captainmorganmomindsedit'));
                    else if(($sourceID == "959")||($sourceID == "963"))
                        $this->render($this->getTpl('dosedit'));
                    else
                        $this->render($this->getTpl('momindsedit'));
                }
                else
                    $this->render($this->getTpl('edit'));
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
        
	function addGrantRating($inFormElement = '#GrantRatingForm') {
		$this->addCssResource(new mvcViewCss('uiStarsCss', mvcViewCss::TYPE_FILE, '/libraries/jquery-ui-stars/jquery-ui.stars.min.css'));
		$this->addJavascriptResource(new mvcViewJavascript('uiStarsJs', mvcViewJavascript::TYPE_FILE, '/libraries/jquery-ui-stars/jquery-ui.stars.min.js'));
		$this->addJavascriptResource(
			new mvcViewJavascript('uiStarsInit', mvcViewJavascript::TYPE_INLINE, '
			if ( $("#mofilmGrantAverageRating").length > 0 ) { 
				$("#mofilmGrantAverageRating").stars({
					disabled: true,
					cancelShow: false
				});
				$("#mofilmGrantRating").stars({
					cancelShow: false,
					callback: function(ui, type, value) {
						opt = {
							GrantID: $("#GrantID").val(),
							Rating: value
						}
						$.post("'.$this->buildUriPath(videosController::ACTION_RATE).'/as.xml", opt, function(xml) {
							avgRating = parseInt($(xml).find(\'rating\').text());
							count = $(xml).find(\'ratingCount\').text();
							$("#mofilmGrantAverageRating").stars("selectID", avgRating-1);
							$("#mofilmGrantAverageRatingCount").text(count);                                                        
						}, \'xml\');
                                                setTimeout(function(){ window.location.href = $("#GrantID").val(); }, 1000);
                                                
                                                
					}
                                        
				});
			}')
		);
	}
        
	function sendRatingResult() {
		$this->setCacheLevelNone();
		
		$this->getEngine()->assign('oModel', utilityOutputWrapper::wrap($this->getModel()));
		$this->getEngine()->assign('oMovie', utilityOutputWrapper::wrap($this->getModel()->getMovie()));		
		$this->render($this->getTpl('ratingResult'));
	}
        
        
}
