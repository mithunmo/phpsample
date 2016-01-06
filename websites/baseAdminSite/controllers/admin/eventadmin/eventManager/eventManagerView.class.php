<?php
/**
 * eventManagerView.class.php
 * 
 * eventManagerView class
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category eventManagerView
 * @version $Rev: 20 $
 */


/**
 * eventManagerView class
 * 
 * Provides the "eventManagerView" page
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category eventManagerView
 */
class eventManagerView extends mvcDaoView {

	/**
	 * @see mvcViewBase::setupInitialVars()
	 */
	function setupInitialVars() {
		parent::setupInitialVars();

		$rootPath = system::getConfig()->getPathWebsites().system::getDirSeparator().'base';
                $this->getEngine()->assign('products', utilityOutputWrapper::wrap(mofilmProduct::listOfObjects()));
		$this->getEngine()->assign('adminEventFolder', str_replace($rootPath, '', mofilmConstants::getAdminEventsFolder()));
		$this->getEngine()->assign('adminSourceFolder', str_replace($rootPath, '', mofilmConstants::getAdminSourceFolder()));
		$this->getEngine()->assign('clientEventFolder', str_replace($rootPath, '', mofilmConstants::getClientEventsFolder()));
		$this->getEngine()->assign('clientSourceFolder', str_replace($rootPath, '', mofilmConstants::getClientSourceFolder()));
                }
	
	/**
	 * @see mvcDaoView::assignCustomViewData()
	 */
	function assignCustomViewData() {
            $request = parse_url($_SERVER['REQUEST_URI']);
        $params = explode('/', $request['path']);
        $eventID = trim($params[count($params) - 1], ' '); 
        $UserPermissionsID = new mofilmUserEventPermissions();
        $IDList = $UserPermissionsID->getUserListID($eventID);
        foreach($IDList as $oMofilmUserList)
        {
            $FMArray[] =  $oMofilmUserList->getUserID();
        }
        if(isset($FMArray))
        {
        for($i=0;$i<count($FMArray);$i++)
        {
            $id = $FMArray[$i];
            $FM[] = $id ;
            $FMNAME[] = mofilmUserManager::getInstanceByID($id)->getFullname();
        }
        }
        if(isset($FM))
        {
            array_unshift($FM,""); 
            unset($FM[0]); 
        }  
        
        if(isset($FMNAME)){
                    array_unshift($FMNAME,"");
                    unset($FMNAME[0]); 
                }
        /*if((count($FMArray) == 0))
           $allPer = 0; */
        if(isset($FMArray))
           $allPer = 0;
        else
           $allPer = 1; 
        $this->getEngine()->assign('ALLPer', $allPer);
        if(!$allPer)
        {
           $this->getEngine()->assign('FM', $FM);
           $this->getEngine()->assign('FMNAME', $FMNAME);
        }
		$this->getEngine()->assign('parentController', 'admin');
		$this->getEngine()->assign('formEncType', 'multipart/form-data');
		
		$rootPath = system::getConfig()->getPathWebsites().system::getDirSeparator().'base';
		$this->getEngine()->assign('adminEventFolder', str_replace($rootPath, '', mofilmConstants::getAdminEventsFolder()));
		$this->getEngine()->assign('adminSourceFolder', str_replace($rootPath, '', mofilmConstants::getAdminSourceFolder()));
	}
	
	/**
	 * @see mvcDaoView::getObjectListView()
	 */
	function getObjectListView() {
		return $this->getTpl('eventManagerList');
	}
	
	/**
	 * @see mvcDaoView::getObjectFormView()
	 */
	function getObjectFormView() {
		$this->addJavascriptResource(new mvcViewJavascript('tinymce', mvcViewJavascript::TYPE_FILE, '/libraries/jquery-plugins/tinymce/jscripts/tiny_mce/jquery.tinymce.js'));
		$this->addJavascriptResource(new mvcViewJavascript('tinymce_popup', mvcViewJavascript::TYPE_FILE, '/libraries/jquery-plugins/tinymce/jscripts/tiny_mce/plugins/browser/fileBrowser.js'));
		
		if ( $this->getController()->getAction() == eventManagerController::ACTION_EDIT ) {
			$this->addJavascriptResource(new mvcViewJavascript('swfobject', mvcViewJavascript::TYPE_FILE, '/libraries/swfobject/swfobject.js'));
			$this->addJavascriptResource(new mvcViewJavascript('uploadify', mvcViewJavascript::TYPE_FILE, '/libraries/jquery-uploadify/jquery.uploadify.min.js'));

			$this->addJavascriptResource(new mvcViewJavascript('uploadifyInitLogo', mvcViewJavascript::TYPE_INLINE, '$(\'#EventLogo\').uploadify({
					    \'uploader\'  : \'/libraries/jquery-uploadify/uploadify.swf\',
					    \'script\'    : \''.$this->buildUriPath(eventManagerController::ACTION_UPLOAD_EVENT_LOGO, $this->getController()->getPrimaryKey()).'\',
					    \'cancelImg\' : \'/libraries/jquery-uploadify/cancel.png\',
					    \'auto\'      : true,
					    \'fileDataName\': \'FilesLogo\',
					    \'scriptData\': { \''.$this->getRequest()->getSession()->getSessionName().'\': \''.$this->getRequest()->getSession()->getSessionID().'\' }
				    });
			'));

			$this->addJavascriptResource(new mvcViewJavascript('uploadifyInitBanner', mvcViewJavascript::TYPE_INLINE, '$(\'#EventBanner\').uploadify({
					    \'uploader\'  : \'/libraries/jquery-uploadify/uploadify.swf\',
					    \'script\'    : \''.$this->buildUriPath(eventManagerController::ACTION_UPLOAD_EVENT_BANNER, $this->getController()->getPrimaryKey()).'\',
					    \'cancelImg\' : \'/libraries/jquery-uploadify/cancel.png\',
					    \'auto\'      : true,
					    \'fileDataName\': \'FilesBanner\',
					    \'scriptData\': { \''.$this->getRequest()->getSession()->getSessionName().'\': \''.$this->getRequest()->getSession()->getSessionID().'\' }
				    });
			'));

			$this->addJavascriptResource(new mvcViewJavascript('uploadifyInitFiller', mvcViewJavascript::TYPE_INLINE, '$(\'#EventFiller\').uploadify({
					    \'uploader\'  : \'/libraries/jquery-uploadify/uploadify.swf\',
					    \'script\'    : \''.$this->buildUriPath(eventManagerController::ACTION_UPLOAD_EVENT_FILLER, $this->getController()->getPrimaryKey()).'\',
					    \'cancelImg\' : \'/libraries/jquery-uploadify/cancel.png\',
					    \'auto\'      : true,
					    \'fileDataName\': \'FilesFiller\',
					    \'scriptData\': { \''.$this->getRequest()->getSession()->getSessionName().'\': \''.$this->getRequest()->getSession()->getSessionID().'\' }
				    });
			'));
		}
		
		return $this->getTpl('eventManagerForm');
	}
	
	/**
	 * Returns an event list view
	 */
	function getEventListView($params = array()) {
		$this->setCacheLevelNone();
		
		/*
		 * events can be restricted to just those available to the current user:
		 * 
		 * $this->getRequest()->getSession()->getUser()->getSourceSet()->getEventIDs()
		 *
		 * DR 2011-04-07:
		 * Added excluding favourites from normal list because we'll loop these separately.
		 */
		$events = mofilmEvent::listOfObjects(
			null, null, false, mofilmEvent::ORDERBY_ENDDATE, array(), $this->getRequest()->getSession()->getUser()->getEventFavourites()->getEventIDs()
		);

		$this->getEngine()->assign('oFavourites', utilityOutputWrapper::wrap($this->getRequest()->getSession()->getUser()->getEventFavourites()));
		$this->getEngine()->assign('events', utilityOutputWrapper::wrap($events));
		
		if ( array_key_exists('collapse', $params) ) {
			if ( array_key_exists('maxAge', $params) ) {
				$maxAge = $params['maxAge'];
			} else {
				$maxAge = 6;
			}
			$maxAge = date('Y-m-d H:i:s', strtotime("-$maxAge months"));
			
			$this->getEngine()->assign('collapseOldEvents', true);
			$this->getEngine()->assign('eventMaxAge', $maxAge);
		}
		
		$rootPath = system::getConfig()->getPathWebsites().system::getDirSeparator().'base';
		$this->getEngine()->assign('adminEventFolder', str_replace($rootPath, '', mofilmConstants::getAdminEventsFolder()));
		$this->getEngine()->assign('adminSourceFolder', str_replace($rootPath, '', mofilmConstants::getAdminSourceFolder()));
		$this->getEngine()->assign('clientEventFolder', str_replace($rootPath, '', mofilmConstants::getClientEventsFolder()));
		$this->getEngine()->assign('clientSourceFolder', str_replace($rootPath, '', mofilmConstants::getClientSourceFolder()));
		
		return $this->compile($this->getTpl('eventListView', '/admin/eventadmin/eventManager'));
	}
	
	/**
	 * Renders the source stats as a page fragment
	 * 
	 * @return void
	 */
	function showSourceStats() {
		$this->setCacheLevelNone();
		
		$this->getController()->getExistingObject();
		$this->getEngine()->assign('sourceStats', utilityOutputWrapper::wrap($this->getModel()->getSourceStats()));
		
		$this->render($this->getTpl('eventSourceStats'));
	}
	
	/**
	 * Renders the source stats as a page fragment
	 * 
	 * @return void
	 */
	function showGrantStats() {
		$this->setCacheLevelNone();
		
		$this->getController()->getExistingObject();
		$this->getEngine()->assign('grantStats', utilityOutputWrapper::wrap($this->getModel()->getGrantStats()));
		
		$this->render($this->getTpl('eventGrantStats'));
	}
}