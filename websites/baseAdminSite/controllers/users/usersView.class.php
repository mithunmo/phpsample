<?php
/**
 * usersView.class.php
 *
 * usersView class
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category usersView
 * @version $Rev: 115 $
 */


/**
 * usersView class
 *
 * Provides the "usersView" page
 *
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category usersView
 */
class usersView extends mvcView {

	/**
	 * @see mvcViewBase::setupInitialVars()
	 */
	function setupInitialVars() {
		parent::setupInitialVars();

		/*
		 * Add any further custom setup for the view that is needed on every request
		 */
		$this->getEngine()->assign('viewUri', $this->buildUriPath(usersController::ACTION_VIEW));
		$this->getEngine()->assign('daoUriView', $this->buildUriPath(usersController::ACTION_SEARCH));
		$this->getEngine()->assign('doSearchUri', $this->buildUriPath(usersController::ACTION_SEARCH));
		$this->getEngine()->assign('doEditUri', $this->buildUriPath(usersController::ACTION_DO_EDIT));
		$this->getEngine()->assign('editUri', $this->buildUriPath(usersController::ACTION_EDIT));
		$this->getEngine()->assign('messageUri', $this->buildUriPath(usersController::ACTION_MESSAGE));
		$this->getEngine()->assign('doMessageUri', $this->buildUriPath(usersController::ACTION_DO_MESSAGE));
		$this->getEngine()->assign('uploadUri', $this->buildUriPath(usersController::ACTION_UPLOAD));
	}

	/**
	 * Shows the usersView page
	 *
	 * @return void
	 */
	function showUsersPage() {
		$this->setCacheLevelNone();
		
		$this->getEngine()->assign('oModel', utilityOutputWrapper::wrap($this->getModel()));
		$this->getEngine()->assign('oResults', utilityOutputWrapper::wrap($this->getModel()->doSearch()));
		$this->getEngine()->assign('searchStatus', $this->getModel()->getSearchResult()->getSearchInterface()->getEnabled());
		
		if ( $this->getController()->getAction() == usersController::ACTION_SEARCH ) {
			$this->getEngine()->assign('searchKeywords', $this->getModel()->getSearchResult()->getSearchInterface()->getKeywords());
			$this->getEngine()->assign('searchClientID', $this->getModel()->getSearchResult()->getSearchInterface()->getClientID());
			$this->getEngine()->assign('searchCountryID', $this->getModel()->getSearchResult()->getSearchInterface()->getTerritoryID());
			$this->getEngine()->assign('searchFinalists', $this->getModel()->getSearchResult()->getSearchInterface()->getOnlyFinalists());
			$this->getEngine()->assign('searchOnlyEmail', ($this->getModel()->getSearchResult()->getSearchInterface()->getUserEmailAddress()));
			$this->getEngine()->assign('searchUploadedMovie', $this->getModel()->getSearchResult()->getSearchInterface()->getHasUploadedMovie());
			$this->getEngine()->assign('daoSearchQuery', $this->getController()->getSearchQueryAsString());
		}
		
		$this->render($this->getTpl('users'));
	}
	
	/**
	 * Shows the user editing page
	 * 
	 * @return void
	 */
	function showUserEditPage() {
		$this->setCacheLevelNone();
		
		$this->getEngine()->assign('oModel', utilityOutputWrapper::wrap($this->getModel()));
		$this->getEngine()->assign('oObject', utilityOutputWrapper::wrap($this->getModel()->getUser()));
		$this->getEngine()->assign('myMofilmUri', system::getConfig()->getParam('mofilm', 'myMofilmUri', 'http://mofilm.com')->getParamValue());
		$this->getEngine()->assign('wwwMofilmUri', system::getConfig()->getParam('mofilm', 'wwwMofilmUri', 'http://www.mofilm.com')->getParamValue());

		$this->render($this->getTpl('editPage'));
	}
	
	/**
	 * Shows the user editing page
	 * 
	 * @return void
	 */
	function showUserMessagePage() {
		$this->setCacheLevelNone();
		
		$this->getEngine()->assign('oObject', utilityOutputWrapper::wrap($this->getModel()->getUser()));
		$this->getEngine()->assign('messageParams', utilityOutputWrapper::wrap($this->getModel()->getMessageParams()));
		$this->getEngine()->assign('oModel', utilityOutputWrapper::wrap($this->getModel()));
		$this->getEngine()->assign('appMessages', utilityOutputWrapper::wrap(commsApplicationMessage::listOfObjects(null, null, null, null, 'en', commsOutboundType::T_EMAIL, true)));
		
		$this->render($this->getTpl('messagePage'));
	}
	
	/**
	 * Renders the user stats view
	 * 
	 * @param array $inParams
	 * @return string
	 */
	function getUserStatsView($inParams = array()) {
		$this->setCacheLevelNone();
		
		$this->getEngine()->assign('oStats', utilityOutputWrapper::wrap($this->getModel()->getStats()));
		
		return $this->compile($this->getTpl('userStats', '/users'));
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