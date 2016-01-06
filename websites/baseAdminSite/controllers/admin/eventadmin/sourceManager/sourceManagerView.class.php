<?php
/**
 * sourceManagerView.class.php
 * 
 * sourceManagerView class
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category sourceManagerView
 * @version $Rev: 11 $
 */


/**
 * sourceManagerView class
 * 
 * Provides the "sourceManagerView" page
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category sourceManagerView
 */
class sourceManagerView extends mvcDaoView {

	/**
	 * @see mvcViewBase::setupInitialVars()
	 */
	function setupInitialVars() {
		parent::setupInitialVars();

		$rootPath = system::getConfig()->getPathWebsites().system::getDirSeparator().'base';
		$this->getEngine()->assign('adminEventFolder', str_replace($rootPath, '', mofilmConstants::getAdminEventsFolder()));
		$this->getEngine()->assign('adminSourceFolder', str_replace($rootPath, '', mofilmConstants::getAdminSourceFolder()));
		$this->getEngine()->assign('clientEventFolder', str_replace($rootPath, '', mofilmConstants::getClientEventsFolder()));
		$this->getEngine()->assign('clientSourceFolder', str_replace($rootPath, '', mofilmConstants::getClientSourceFolder()));
	}
	
	/**
	 * @see mvcDaoView::assignCustomViewData()
	 */
	function assignCustomViewData() {
                $oSearch = new mofilmUserSearch();
		$oSearch->setUser(mofilmUserManager::getInstanceByID(30247));
		//$oSearch->setOnlyAdminUsers(true);
                $oSearch->setClientID(1);
		$oSearch->setOffset(0);
		$oSearch->setLimit(1000);
		$oSearch->setOrderBy(mofilmUserSearch::ORDERBY_FULLNAME);
		$oSearch->setOrderDirection(mofilmUserSearch::ORDER_ASC);
		$searchResult = $oSearch->search();
                $arr = $searchResult->getResults();
                $this->getEngine()->assign('userList', $arr);

		$this->getEngine()->assign('parentController', 'admin');
                
                $request = parse_url($_SERVER['REQUEST_URI']);
                $params = explode('/', $request['path']);
                $sourceID = trim($params[count($params) - 1], ' '); 
                
                if(strstr($_SERVER['REQUEST_URI'],"editObject"))
                {   
                    $BudgetDataList = new mofilmSourceBudget();
                    $BudgetData = $BudgetDataList->getBudget($sourceID);
                    
                    $eventID = mofilmSource::getInstance($sourceID)->getEventID();
                    $productID = mofilmEvent::getInstance($eventID)->getProductID();
                    
                    $totalApprovedAmount = $this->getModel()->getApprovedAmount($sourceID,$eventID);
                    if($productID == "5")
                    {
			unset($GrantID);
                        $GrantDataList = new mofilmGrants();
                        $GrantData = $GrantDataList->getGrantID($sourceID);
                        if(isset($GrantData))
                        $GrantID = $GrantData->getID();
                        $GrantDataParam = new grantdata();
                        if(isset($GrantID))
                        $ParamData = $GrantDataParam->getValue($GrantID,"Question");
                        if(!isset($ParamData))
                            $this->getEngine()->assign('NoData', "1");
                        $this->getEngine()->assign('ParamData', $ParamData);
                        /*unset($GrantID);
                        $GrantDataList = new mofilmGrants();
                        $GrantData = $GrantDataList->getGrantID($sourceID);
                        $GrantID = $GrantData->getID();
                        $GrantDataParam = new grantdata();
                        $ParamData = $GrantDataParam->getValue($GrantID,"Question");
                        if(!isset($ParamData))
                            $this->getEngine()->assign('NoData', "1");
                        $this->getEngine()->assign('ParamData', $ParamData);*/
                    }
                    if($productID == "3")
                    {
			$GrantDataList = new mofilmGrants();
                        $GrantData = $GrantDataList->getGrantID($sourceID);
                        if(isset($GrantData))
                        $GrantID = $GrantData->getID();
                        $GrantDataParam = new grantdata();
                        if(isset($GrantID)){
                        $ParamData = $GrantDataParam->getValue($GrantID,"IdeaSubmission");
                        $this->getEngine()->assign('ParamData', $ParamData);
                        }
                        else
                            $this->getEngine()->assign('NoData', "1");
                        /*$GrantDataList = new mofilmGrants();
                        $GrantData = $GrantDataList->getGrantID($sourceID);
                        $GrantID = $GrantData->getID();
                        $GrantDataParam = new grantdata();
                        if(isset($GrantID)){
                        $ParamData = $GrantDataParam->getValue($GrantID,"IdeaSubmission");
                        $this->getEngine()->assign('ParamData', $ParamData);
                        }
                        else
                            $this->getEngine()->assign('NoData', "1");*/
                    }
                }
                $this->getEngine()->assign('BudgetData', $BudgetData);
                $UserPermissionsID = new mofilmUserSourcePermissions();
                $IDList = $UserPermissionsID->getUserListID($sourceID);
                $AllPermissions = $UserPermissionsID->getAllFlag($sourceID);
                foreach($AllPermissions as $oMofilmPermissions)
                    $allPer = $oMofilmPermissions->getHasEvent();
                foreach($IDList as $oMofilmUserList)
                {
                    $FMArray[] =  $oMofilmUserList->getUserID();
                }
                if(isset($FMArray))
                {
                for($i=0;$i<count($FMArray);$i++)
                {
                    $id = $FMArray[$i];
                    $FM[] = $id;
                    $FMNAME[] =  $this->getModel()->getUser($id);
                }
                }
                if(isset($FM))
                {
                    array_unshift($FM,""); 
                    unset($FM[0]); 
                }  
        
                if(isset($FMNAME))
                {
                    array_unshift($FMNAME,"");
                    unset($FMNAME[0]); 
                }
               
                if(count($FMArray) == 0)
                $allPer = 0; 
                $this->getEngine()->assign('ALLPer', $allPer);
                if(!$allPer)
                {
                   $this->getEngine()->assign('FM', $FM);
                   $this->getEngine()->assign('FMNAME', $FMNAME);
                }
                
		$list = mofilmSupplier::listOfObjects();
		$tmp = array();
		foreach ( $list as $oObject ) {
			$tmp[] = $oObject->getDescription();
		}
                $length = count($FM);
                $this->getEngine()->assign('length',count($FM));
                $this->getEngine()->assign('approvedAmt',$totalApprovedAmount);
		$this->getEngine()->assign('availableSuppliers', json_encode($tmp));
		$this->getEngine()->assign('formEncType', 'multipart/form-data');
		
		$rootPath = system::getConfig()->getPathWebsites().system::getDirSeparator().'base';
		$this->getEngine()->assign('adminEventFolder', str_replace($rootPath, '', mofilmConstants::getAdminEventsFolder()));
		$this->getEngine()->assign('adminSourceFolder', str_replace($rootPath, '', mofilmConstants::getAdminSourceFolder()));
	}
	
	/**
	 * @see mvcDaoView::getObjectListView()
	 */
	function getObjectListView() {
		return $this->getTpl('sourceManagerList');
	}
	
        function showSourceEdit(){
            $eventID = $this->getModel()->getEventID();
            $productID = mofilmEvent::getInstance($eventID)->getProductID();
            
		$this->addJavascriptResource(new mvcViewJavascript('tinymce', mvcViewJavascript::TYPE_FILE, '/libraries/jquery-plugins/tinymce/jscripts/tiny_mce/jquery.tinymce.js'));
		$this->addJavascriptResource(new mvcViewJavascript('tinymce_popup', mvcViewJavascript::TYPE_FILE, '/libraries/jquery-plugins/tinymce/jscripts/tiny_mce/plugins/browser/fileBrowser.js'));
		
		    
                      if ( $this->getController()->getAction() == sourceManagerController::ACTION_EDIT ) {
                    
                        $this->addJavascriptResource(new mvcViewJavascript('swfobject', mvcViewJavascript::TYPE_FILE, '/libraries/swfobject/swfobject.js'));
			$this->addJavascriptResource(new mvcViewJavascript('uploadify', mvcViewJavascript::TYPE_FILE, '/libraries/jquery-uploadify/jquery.uploadify.min.js'));
			
			$this->addJavascriptResource(new mvcViewJavascript('uploadifyInitLogo', mvcViewJavascript::TYPE_INLINE, '$(\'#SourceLogo\').uploadify({
					    \'uploader\'  : \'/libraries/jquery-uploadify/uploadify.swf\',
					    \'script\'    : \''.$this->buildUriPath(sourceManagerController::ACTION_UPLOAD_SOURCE_LOGO, $this->getController()->getPrimaryKey()).'\',
					    \'cancelImg\' : \'/libraries/jquery-uploadify/cancel.png\',
					    \'auto\'      : true,
					    \'fileDataName\': \'FilesLogo\',
					    \'scriptData\': { \''.$this->getRequest()->getSession()->getSessionName().'\': \''.$this->getRequest()->getSession()->getSessionID().'\' }
				    });
			'));

			$this->addJavascriptResource(new mvcViewJavascript('uploadifyInitBanner', mvcViewJavascript::TYPE_INLINE, '$(\'#SourceBanner\').uploadify({
					    \'uploader\'  : \'/libraries/jquery-uploadify/uploadify.swf\',
					    \'script\'    : \''.$this->buildUriPath(sourceManagerController::ACTION_UPLOAD_SOURCE_BANNER, $this->getController()->getPrimaryKey()).'\',
					    \'cancelImg\' : \'/libraries/jquery-uploadify/cancel.png\',
					    \'auto\'      : true,
					    \'fileDataName\': \'FilesBanner\',
					    \'scriptData\': { \''.$this->getRequest()->getSession()->getSessionName().'\': \''.$this->getRequest()->getSession()->getSessionID().'\' }
				    });
			'));

			$this->addJavascriptResource(new mvcViewJavascript('uploadifyInitFiller', mvcViewJavascript::TYPE_INLINE, '$(\'#SourceFiller\').uploadify({
					    \'uploader\'  : \'/libraries/jquery-uploadify/uploadify.swf\',
					    \'script\'    : \''.$this->buildUriPath(sourceManagerController::ACTION_UPLOAD_SOURCE_FILLER, $this->getController()->getPrimaryKey()).'\',
					    \'cancelImg\' : \'/libraries/jquery-uploadify/cancel.png\',
					    \'auto\'      : true,
					    \'fileDataName\': \'FilesFiller\',
					    \'scriptData\': { \''.$this->getRequest()->getSession()->getSessionName().'\': \''.$this->getRequest()->getSession()->getSessionID().'\' }
				    });
			'));
			
			foreach ($this->getModel()->getSourceDownloadFiles() as $oDownloadFile) {
				$dID = $oDownloadFile->getID();
				$this->addJavascriptResource(new mvcViewJavascript('uploadifyInit'.$dID, mvcViewJavascript::TYPE_INLINE, '$(\'#FileUpload'.$dID.'\').uploadify({
					    \'uploader\'  : \'/libraries/jquery-uploadify/uploadify.swf\',
					    \'script\'    : \'/admin/eventadmin/downloadFiles/doEditObject/'.$dID.'\',
					    \'cancelImg\' : \'/libraries/jquery-uploadify/cancel.png\',
					    \'auto\'      : true,
					    \'fileDataName\': \'Files\',
					    \'scriptData\': { \''.$this->getRequest()->getSession()->getSessionName().'\': \''.$this->getRequest()->getSession()->getSessionID().'\' }
				    });
				'));
			}
		}
		
                systemLog::message("productiD".$productID);
                
                if ( $productID != 0 ){
                    if ($productID == 5 ){
                        $this->render($this->getTpl('sourceManagerProForm'));
                        
                    } else if ($productID == 12 ){ 
                        $this->render($this->getTpl('sourceManagerMosaicForm'));
                        
                    } else if ($productID == 3 ){ 
                        $this->render($this->getTpl('sourceManagerMomindsForm'));
                        
                    }                     
                    else {
                        $this->render($this->getTpl('sourceManagerMarqueeForm'));
                    }
                } else {
                    $this->render($this->getTpl('sourceManagerForm'));
                    
                }
        }
        
	/**
	 * @see mvcDaoView::getObjectFormView()
	 */
	function getObjectFormView() {
           
                $eventID = $this->getModel()->getEventID();
                systemLog::message("eventID".$eventID);
                $productID = mofilmEvent::getInstance($eventID)->getProductID();
            
		$this->addJavascriptResource(new mvcViewJavascript('tinymce', mvcViewJavascript::TYPE_FILE, '/libraries/jquery-plugins/tinymce/jscripts/tiny_mce/jquery.tinymce.js'));
		$this->addJavascriptResource(new mvcViewJavascript('tinymce_popup', mvcViewJavascript::TYPE_FILE, '/libraries/jquery-plugins/tinymce/jscripts/tiny_mce/plugins/browser/fileBrowser.js'));
		
		if ( $this->getController()->getAction() == sourceManagerController::ACTION_EDIT ) {
                    
                        $this->addJavascriptResource(new mvcViewJavascript('swfobject', mvcViewJavascript::TYPE_FILE, '/libraries/swfobject/swfobject.js'));
			$this->addJavascriptResource(new mvcViewJavascript('uploadify', mvcViewJavascript::TYPE_FILE, '/libraries/jquery-uploadify/jquery.uploadify.min.js'));
			
			$this->addJavascriptResource(new mvcViewJavascript('uploadifyInitLogo', mvcViewJavascript::TYPE_INLINE, '$(\'#SourceLogo\').uploadify({
					    \'uploader\'  : \'/libraries/jquery-uploadify/uploadify.swf\',
					    \'script\'    : \''.$this->buildUriPath(sourceManagerController::ACTION_UPLOAD_SOURCE_LOGO, $this->getController()->getPrimaryKey()).'\',
					    \'cancelImg\' : \'/libraries/jquery-uploadify/cancel.png\',
					    \'auto\'      : true,
					    \'fileDataName\': \'FilesLogo\',
					    \'scriptData\': { \''.$this->getRequest()->getSession()->getSessionName().'\': \''.$this->getRequest()->getSession()->getSessionID().'\' }
				    });
			'));

			$this->addJavascriptResource(new mvcViewJavascript('uploadifyInitBanner', mvcViewJavascript::TYPE_INLINE, '$(\'#SourceBanner\').uploadify({
					    \'uploader\'  : \'/libraries/jquery-uploadify/uploadify.swf\',
					    \'script\'    : \''.$this->buildUriPath(sourceManagerController::ACTION_UPLOAD_SOURCE_BANNER, $this->getController()->getPrimaryKey()).'\',
					    \'cancelImg\' : \'/libraries/jquery-uploadify/cancel.png\',
					    \'auto\'      : true,
					    \'fileDataName\': \'FilesBanner\',
					    \'scriptData\': { \''.$this->getRequest()->getSession()->getSessionName().'\': \''.$this->getRequest()->getSession()->getSessionID().'\' }
				    });
			'));

			$this->addJavascriptResource(new mvcViewJavascript('uploadifyInitFiller', mvcViewJavascript::TYPE_INLINE, '$(\'#SourceFiller\').uploadify({
					    \'uploader\'  : \'/libraries/jquery-uploadify/uploadify.swf\',
					    \'script\'    : \''.$this->buildUriPath(sourceManagerController::ACTION_UPLOAD_SOURCE_FILLER, $this->getController()->getPrimaryKey()).'\',
					    \'cancelImg\' : \'/libraries/jquery-uploadify/cancel.png\',
					    \'auto\'      : true,
					    \'fileDataName\': \'FilesFiller\',
					    \'scriptData\': { \''.$this->getRequest()->getSession()->getSessionName().'\': \''.$this->getRequest()->getSession()->getSessionID().'\' }
				    });
			'));
			
			foreach ($this->getModel()->getSourceDownloadFiles() as $oDownloadFile) {
				$dID = $oDownloadFile->getID();
				$this->addJavascriptResource(new mvcViewJavascript('uploadifyInit'.$dID, mvcViewJavascript::TYPE_INLINE, '$(\'#FileUpload'.$dID.'\').uploadify({
					    \'uploader\'  : \'/libraries/jquery-uploadify/uploadify.swf\',
					    \'script\'    : \'/admin/eventadmin/downloadFiles/doEditObject/'.$dID.'\',
					    \'cancelImg\' : \'/libraries/jquery-uploadify/cancel.png\',
					    \'auto\'      : true,
					    \'fileDataName\': \'Files\',
					    \'scriptData\': { \''.$this->getRequest()->getSession()->getSessionName().'\': \''.$this->getRequest()->getSession()->getSessionID().'\' }
				    });
				'));
			}
		}
                systemLog::message("productiD".$productID);
                if ( $productID != 0 ){
                    if ($productID == 5 ){
                        return $this->getTpl('sourceManagerProForm');
                    } else if ($productID == 12 ){ 
                        return $this->getTpl('sourceManagerMosaicForm');
                    }else if ($productID == 3 ){
                        return $this->getTpl('sourceManagerMomindsForm');

                    }	 else {
                        return $this->getTpl('sourceManagerMarqueeForm');
                    }
                } else {
                    return $this->getTpl('sourceManagerForm');
                }
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
}
