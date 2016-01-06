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
		$this->getEngine()->assign('lang', $this->getRequest()->getDistributor()->getSiteConfig()->getI18nDefaultLanguage()->getParamValue());
	}

	/**
	 * @see mvcDaoView::getObjectListView()
	 */
	function getObjectListView() {
		$this->getEngine()->assign('oModel', utilityOutputWrapper::wrap($this->getModel()));
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
	function showApplyForGrants($inSourceID = null, $inUserID = null) {
		
		$this->getEngine()->assign('message', 'Active');
		$oUserMovieGrants = mofilmUserMovieGrants::userMovieGrantsObject($inUserID, $inSourceID);
		$oSource = mofilmSource::getInstance($inSourceID);
		
		if ( $oSource->isClosed() ) {
			$this->getEngine()->assign('message', 'Expired');
		}
		
		if ( $oUserMovieGrants instanceof mofilmUserMovieGrants ) {
			$this->getEngine()->assign('message', 'Applied');
			$this->getEngine()->assign('inAppliedGrantID', $oUserMovieGrants->getID());
		}
			
		$this->getEngine()->assign('oGrants', utilityOutputWrapper::wrap(mofilmGrants::getInstanceBySourceID($inSourceID)));
		$this->getEngine()->assign('oSource', utilityOutputWrapper::wrap(mofilmSource::getInstance($inSourceID)));
		$this->getEngine()->assign('oUser', utilityOutputWrapper::wrap(mofilmUserManager::getInstanceByID($inUserID)));
		
		$this->render($this->getTpl('apply'));
	}
	
	/*
	 * 
	 */
	function grantView($inGrantID = null, $inUserID = null) {
		$this->getEngine()->assign('oGrant', utilityOutputWrapper::wrap(mofilmUserMovieGrants::getInstance($inGrantID)));
		$this->getEngine()->assign('oUser', utilityOutputWrapper::wrap(mofilmUserManager::getInstanceByID($inUserID)));
		$this->addJavascriptResource(new mvcViewJavascript('blockui', mvcViewJavascript::TYPE_FILE, '/libraries/mofilm/jquery.blockUI.js'));
		$this->addJavascriptResource(new mvcViewJavascript('uiblock', mvcViewJavascript::TYPE_INLINE, '
		    function documentsUploadUI() {
			$.blockUI();
		    }
		'));
		$this->render($this->getTpl('view'));
	}
	
	/*
	 * 
	 */
	function grantEdit($inUserMovieGrantID = null, $inUserID = null) {
		$this->getEngine()->assign('oUser', utilityOutputWrapper::wrap(mofilmUserManager::getInstanceByID($inUserID)));
		$this->getEngine()->assign('oGrant', utilityOutputWrapper::wrap(mofilmUserMovieGrants::getInstance($inUserMovieGrantID)));
		$this->render($this->getTpl('edit'));
	}
}