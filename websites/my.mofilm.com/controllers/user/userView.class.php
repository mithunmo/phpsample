<?php
/**
 * userView.class.php
 * 
 * userView class
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_my.mofilm.com
 * @subpackage controllers
 * @category userView
 * @version $Rev: 255 $
 */


/**
 * userView class
 * 
 * Provides the "userView" page
 * 
 * @package websites_my.mofilm.com
 * @subpackage controllers
 * @category userView
 */
class userView extends mvcView {

	/**
	 * @see mvcViewBase::setupInitialVars()
	 */
	function setupInitialVars() {
		parent::setupInitialVars();
	}

	/**
	 * Displays the shared landing page
	 *
	 * @return void
	 */
	function showCurrentLeaderboard() {
		if ( system::getConfig()->isProduction() ) {
			$this->setCacheLevelMedium();
		} else {
			$this->setCacheLevelNone();
		}

		$template = 'current';
		
		$cacheId = md5('user.current.'.$this->getModel()->getPage());
		if ( !$this->isCached($this->getTpl($template, '/user'), $cacheId) ) {
			$this->getEngine()->assign('oResult', utilityOutputWrapper::wrap($this->getModel()->getLeaderboard()));
			$this->getEngine()->assign('page', $this->getModel()->getPage());
			$this->getEngine()->assign('lastPage', $this->getModel()->getLastPage());
			$this->getEngine()->assign('offset', $this->getModel()->getOffset());
		}
		
		$this->render($this->getTpl($template, '/user'), $cacheId);
	}

	/**
	 * Displays the shared landing page
	 *
	 * @return void
	 */
	function showAlltimeLeaderboard() {
		if ( system::getConfig()->isProduction() ) {
			$this->setCacheLevelMedium();
		} else {
			$this->setCacheLevelNone();
		}

		$template = 'alltime';

		$cacheId = md5('user.alltime.'.$this->getModel()->getPage());
		if ( !$this->isCached($this->getTpl($template, '/user'), $cacheId) ) {
			$this->getEngine()->assign('oResult', utilityOutputWrapper::wrap($this->getModel()->getAllTimeLeaderboard()));
			$this->getEngine()->assign('page', $this->getModel()->getPage());
			$this->getEngine()->assign('lastPage', $this->getModel()->getLastPage());
			$this->getEngine()->assign('offset', $this->getModel()->getOffset());
		}

		$this->render($this->getTpl($template, '/user'), $cacheId);
	}

	
	/**
	 * Displays the shared landing page
	 *
	 * @return void
	 */
	function showSkills() {
		
		$this->getEngine()->assign('oModel', $this->getModel());
		$this->getEngine()->assign('oResult', $this->getModel()->doSolrCrewSearch());
		$this->getEngine()->assign('page', $this->getModel()->getPage());
		
		
		if ( $_SERVER["QUERY_STRING"] ) {
			$this->getEngine()->assign('uri', $_SERVER["QUERY_STRING"]);
		}
		
		if ( $this->getModel()->getKeyword() != "*:*") {
			$this->getEngine()->assign('key', $this->getModel()->getKeyword());
		}
		
		$this->getEngine()->assign('total', $this->getModel()->getSolrTotal());
		$this->getEngine()->assign('lastPage', $this->getModel()->getSolrLastPageOffset(30));
		$this->getEngine()->assign('offset', $this->getModel()->getSolrOffset());
		
		$this->render($this->getTpl("skills"));
	}
	

	/**
	 * Displays the shared landing page
	 *
	 * @return void
	 */
	function showSearchLeaderboard() {
		$template = 'search';
		$this->getEngine()->assign('oResult', utilityOutputWrapper::wrap($this->getModel()->getSearchLeaderboard()));
		$this->getEngine()->assign('page', $this->getModel()->getPage());
		$this->getEngine()->assign('lastPage', $this->getModel()->getLastPage());
		$this->getEngine()->assign('offset', $this->getModel()->getOffset());
		$this->getEngine()->assign('searchKey', $this->getModel()->getKeyword());
		$this->render($this->getTpl($template, '/user'));
	}
	
	function showCrew(){
		$this->getEngine()->assign('roles', mofilmRole::listOfObjects());
		$this->render($this->getTpl("crew"));
	}
	
	/**
	 * Displays the shared landing page
	 *
	 * @return void
	 */
	function showCountryLeaderboard() {
		if ( system::getConfig()->isProduction() ) {
			$this->setCacheLevelMedium();
		} else {
			$this->setCacheLevelNone();
		}

		$template = 'country';

		$cacheId = md5('user.country.'.$this->getModel()->getCountry().'.'.$this->getModel()->getPage());
		if ( !$this->isCached($this->getTpl($template, '/user'), $cacheId) ) {
			$this->getEngine()->assign('oResult', utilityOutputWrapper::wrap($this->getModel()->getCountryLeaderboard()));
			$this->getEngine()->assign('oTerritory', utilityOutputWrapper::wrap($this->getModel()->getTerritory()));

			$this->getEngine()->assign('page', $this->getModel()->getPage());
			$this->getEngine()->assign('lastPage', $this->getModel()->getLastPage());
			$this->getEngine()->assign('offset', $this->getModel()->getOffset());
		}

		$this->render($this->getTpl($template, '/user'), $cacheId);
	}

	/**
	 * Displays the shared landing page
	 *
	 * @return void
	 */
	function showCountryList() {
		if ( system::getConfig()->isProduction() ) {
			$this->setCacheLevelMedium();
		} else {
			$this->setCacheLevelNone();
		}

		$template = 'country';

		$cacheId = md5('user.country.list');
		if ( !$this->isCached($this->getTpl($template, '/user'), $cacheId) ) {
			$this->getEngine()->assign('countries', utilityOutputWrapper::wrap($this->getModel()->getCountryList()));
		}

		$this->render($this->getTpl($template, '/user'), $cacheId);
	}

	/**
	 * Shows the userView page
	 *
	 * @return void
	 */
	function showUserPage() {
		if ( system::getConfig()->isProduction() ) {
			$this->setCacheLevelMedium();
		} else {
			$this->setCacheLevelNone();
		}
		
		$cacheId = md5($this->getModel()->getProfileName());
		if ( !$this->isCached($this->getTpl('user', '/user'), $cacheId) ) {
			$this->getEngine()->assign('oProfile', utilityOutputWrapper::wrap($this->getModel()->getUser()));
		}
		$this->render($this->getTpl('user', '/user'), $cacheId);
	}
	
	/**
	 * Sends a JSON response for AJAX calls
	 * 
	 * @param string $inMessage Message to display
	 * @param mixed $inStatus Status of result, 0 = info, true = success, false = error, 
	 * @return void
	 */
	function sendJsonResult($inUrl, $movieid) {
		$this->setCacheLevelNone();
		echo 'getdata('.json_encode(array('id' => $movieid, 'url' => $inUrl)).')';
	}
}