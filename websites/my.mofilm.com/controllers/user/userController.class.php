<?php
/**
 * userController
 *
 * Stored in userController.class.php
 * 
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_my.mofilm.com
 * @subpackage controllers
 * @category userController
 * @version $Rev: 255 $
 */


/**
 * userController
 *
 * userController class
 * 
 * @package websites_my.mofilm.com
 * @subpackage controllers
 * @category userController
 */
class userController extends mvcController {
	
	const ACTION_VIEW = 'view';
	const ACTION_PAGE = 'page';
	const ACTION_SKILLS = "skills";
	const ACTION_CREW = "crew";
	const ACTION_CURRENT = 'current';
	const ACTION_ALLTIME = 'alltime';
	const ACTION_COUNTRY = 'country';
	const ACTION_SEARCH = 'search';
	const ACTION_USERS = 'users';
	const ACTION_THUMBNAIL = 'getThumbnail';

	/**
	 * @see mvcControllerBase::initialise()
	 */
	function initialise() {
		parent::initialise();
		
		$this->setDefaultAction(self::ACTION_VIEW);
		$this->setRequiresAuthentication(false);
		
		$this->getControllerActions()
			->addAction(self::ACTION_VIEW)
			->addAction(self::ACTION_PAGE)
			->addAction(self::ACTION_CURRENT)
			->addAction(self::ACTION_ALLTIME)
			->addAction(self::ACTION_COUNTRY)
			->addAction(self::ACTION_SKILLS)
			->addAction(self::ACTION_CREW)	
			->addAction(self::ACTION_SEARCH)	
			->addAction(self::ACTION_USERS)
			->addAction(self::ACTION_THUMBNAIL)
			->addAction(new mvcControllerAction('profileName', '/[\w\-\_]+/i'));
	}
	
	/**
	 * @see mvcControllerBase::launch()
	 */
	function launch() {
		switch ( $this->getAction() ) {
			case self::ACTION_VIEW:
			case self::ACTION_CURRENT:
				$this->currentAction();
			break;

			case self::ACTION_ALLTIME: $this->alltimeAction(); break;
			case self::ACTION_COUNTRY: $this->countryAction(); break;
			case self::ACTION_SEARCH: $this->searchAction(); break;
			case self::ACTION_SKILLS: $this->skillsAction(); break;
			case self::ACTION_CREW: $this->crewAction(); break;
			case self::ACTION_USERS: $this->getUsersAction(); break;
			case self::ACTION_THUMBNAIL: $this->getThumbnailUri(); break;

			default:
				$this->getModel()->setProfileName($this->getAction());

				$oView = new userView($this);
				$oView->showUserPage();
		}
	}

	/**
	 * Handles displaying the current leaderboard and paging
	 *
	 * @return void
	 */
	function currentAction() {
		$this->getModel()->setPage($this->getPageFromRequest());

		$oView = new userView($this);
		$oView->showCurrentLeaderboard();
	}

	/**
	 * Handles displaying the alltime leaderboard
	 *
	 * @return void
	 */
	function alltimeAction() {
		$this->getModel()->setPage($this->getPageFromRequest());

		$oView = new userView($this);
		$oView->showAlltimeLeaderboard();
	}

	/**
	 * Handles the uses skills section
	 *
	 * @return void
	 */
	function skillsAction() {
		$this->getModel()->setPage($this->getPageFromRequest());
		
		$this->getInputManager()->setLookupGlobals(utilityInputManager::LOOKUPGLOBALS_GET);	
		$data = $this->getInputManager()->doFilter();
		if ( $data["skill"] || $data["location"] ) {
			if (isset($data["location"])) {
				$this->getModel()->setLocation(urlencode($data["location"]));
			}
			if (isset($data["skill"])) {
				$this->getModel()->setSkill(urlencode($data["skill"]));
			}
			
		} else if ( strlen($this->getKeywordFromRequest()) >= 3 ) {
			$this->getModel()->setKeyword(urlencode($this->getKeywordFromRequest()));
		} else {
			$this->getModel()->setKeyword("*:*");
		}
		$oView = new userView($this);
		$oView->showSkills();
	}
	
	/**
	 * 
	 */
	function getThumbnailUri() {
		$oFilter = utilityInputFilter::filterInt();
		$inMovieID = $oFilter->doFilter($this->getActionFromRequest(false, 1));

		$oObject = mofilmMovieManager::getInstanceByID($inMovieID);
		$thumbnail_uri = $oObject->getThumbnailUri();
		$movieid = $oObject->getID();
		
		$oView = new userView($this);
		$oView->sendJsonResult($thumbnail_uri, $movieid);
	}
	
	/**
	 * 
	 */
	function crewAction(){
		$oView = new userView($this);
		$oView->showCrew();		
	}
	
	/**
	 * Handles the search part 
	 * 
	 * @return void
	 */
	function searchAction() {
		$this->getModel()->setPage($this->getPageFromRequest());
		$this->getInputManager()->setLookupGlobals(utilityInputManager::LOOKUPGLOBALS_GET);
		$inData = $this->getInputManager()->doFilter();
		$this->addInputToModel($inData, $this->getModel());
		$oView = new userView($this);
		$oView->showSearchLeaderboard();
	}
	
	
	function getUsersAction() {
		$this->getInputManager()->setLookupGlobals(utilityInputManager::LOOKUPGLOBALS_GET);	
		$data = $this->getInputManager()->doFilter();
		if ( strlen($data["term"]) >= 3 ) {
			$this->getModel()->setKeyword($data["term"]);
			$result = $this->getModel()->getSearchLeaderboard();
			$json_arr = array();
			foreach ( $result as $oProfile ) {
				if( $oProfile->getFirstName() ) {
					$json_arr[] = $oProfile->getFullName();
				}
			}
			$list = json_encode($json_arr);
			echo $list;
			return;
		}
	}
	
	
	/**
	 * Handles displaying the country leaderboards
	 *
	 * @return void
	 */
	function countryAction() {
		$oFilter = utilityInputFilter::filterString();
		$country = $oFilter->doFilter($this->getActionFromRequest(false, 1));

		if ( !$country || strlen($country) != 2 ) {
			$country = null;
		}

		$this->getModel()->setCountry($country);
		$this->getModel()->setPage($this->getPageFromRequest());

		$oView = new userView($this);
		if ( $country ) {
			$oView->showCountryLeaderboard();
		} else {
			$oView->showCountryList();
		}
	}

	/**
	 * Returns the page number from the request
	 *
	 * @return integer
	 */
	function getPageFromRequest() {
		$oFilter = utilityInputFilter::filterInt();
		$page = $oFilter->doFilter($this->getActionFromRequest(false, 2));
		
		if ( !$page || !is_numeric($page) || $page < 1 ) {
			$page = 1;
		}

		return $page;
	}
	

	/**
	 * Returns the page number from the request
	 *
	 * @return integer
	 */
	function getKeywordFromRequest() {
		$oFilter = utilityInputFilter::filterString();
		$key = $oFilter->doFilter($this->getActionFromRequest(false, 3));
		return $key;
	}
	
	
	
	/**
	 * @see mvcControllerBase::addInputFilters()
	 */
	function addInputFilters() {
		$this->getInputManager()->addFilter("userSearch", utilityInputFilter::filterString());
		$this->getInputManager()->addFilter("keyword", utilityInputFilter::filterString());
		$this->getInputManager()->addFilter("term", utilityInputFilter::filterString());
		$this->getInputManager()->addFilter("skill", utilityInputFilter::filterString());
		$this->getInputManager()->addFilter("location", utilityInputFilter::filterString());
	}
	
	/**
	 * @see mvcControllerBase::addInputToModel()
	 */
	function addInputToModel($inData, $inModel) {
		$inModel->setKeyword($inData['userSearch']);
	}
	
	/**
	 * Fetches the model
	 *
	 * @return userModel
	 */
	function getModel() {
		if ( !parent::getModel() ) {
			$this->buildModel();
		}
		return parent::getModel();
	}
	
	/**
	 * Builds the model
	 *
	 * @return void
	 */
	function buildModel() {
		$oModel = new userModel();
		$this->setModel($oModel);
	}
}