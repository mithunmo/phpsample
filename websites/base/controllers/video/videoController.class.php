<?php
/**
 * videoController
 *
 * Stored in videoController.class.php
 * 
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_base
 * @subpackage controllers
 * @category videoController
 * @version $Rev: 11 $
 */


/**
 * videoController
 *
 * videoController class
 * 
 * @package websites_base
 * @subpackage controllers
 * @category videoController
 */
class videoController extends mvcController {
	
	const ACTION_VIEW = 'view';
	const ACTION_GET_USERS = 'getUsers';	
	
	/**
	 * @see mvcControllerBase::initialise()
	 */
	function initialise() {
		parent::initialise();
		
		$this->setDefaultAction(self::ACTION_VIEW);
		$this->setRequiresAuthentication(false);
		
		$this->getControllerActions()
			->addAction(self::ACTION_VIEW)
			->addAction(self::ACTION_GET_USERS)	
			->addAction(new mvcControllerAction('movieHash', '/^\w+$/i'))
			->addAction(new mvcControllerAction('movieId', '/^\d+$/'));
	}
	
	/**
	 * @see mvcControllerBase::launch()
	 */
	function launch() {
		switch ( $this->getAction() ) {
			case self::ACTION_VIEW:
				$this->redirect('/');
			break;
			
			case self::ACTION_GET_USERS:		$this->getUsersAction();	break;
			case is_string($this->getAction()) && strlen($this->getAction()) > 0:
				$this->getModel()->setMovieReference($this->getAction());
				
				$oView = new videoView($this);
				$oView->showVideoPage();
			break;
			
			default:
				$this->redirect('/');
			break;
		}
	}
	
	
	
	/**
	 * @see mvcControllerBase::addInputFilters()
	 */
	function addInputFilters() {
		$this->getInputManager()->addFilter('term', utilityInputFilter::filterString());
	}
	
	/**
	 * @see mvcControllerBase::addInputToModel()
	 */
	function addInputToModel($inData, $inModel) {
		
	}
	
	/**
	 * Fetches the model
	 *
	 * @return videoModel
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
		$oModel = new videoModel();
		$this->setModel($oModel);
	}
	
	/**
	 * Gets the list of users based on search
	 * 
	 * @return array 
	 */
	function getUsersAction() {
		
		$this->getInputManager()->setLookupGlobals(utilityInputManager::LOOKUPGLOBALS_GET);	
		$data = $this->getInputManager()->doFilter();
		if ( strlen($data["term"]) >= 2 ) {
			
			if ( preg_match("/@/", $data["term"]) ) {
				$oMofilmUserSearch = new mofilmUserSearch();
				$oMofilmUserSearch->setUser($this->getRequest()->getSession()->getUser());
				$oMofilmUserSearch->setUserEmailAddress($data["term"]);
				$result = $oMofilmUserSearch->search();		
				$json_arr = array();
				foreach ( $result as $oProfile ) {
					if( $oProfile->getFirstName() ) {
						$json_arr[] = array("label" => $oProfile->getFullName(),"value" => $oProfile->getFullName(),"key" => $oProfile->getID());
					}
				}
				
				//$json_arr[] = array("label" => "<b> Search by name,email addrress. <br />Enter the email address if you have not found the user <br /> we shall invite for you ! </b>","value" => "none");
				$list = json_encode($json_arr);
				echo $list;
				return;
				
									
			} else {
				$oMofilmUserSearch = new mofilmUserSearch();
				$oMofilmUserSearch->setUser($this->getRequest()->getSession()->getUser());
				$oMofilmUserSearch->setSearchTextType(mofilmUserSearch::SEARCH_TEXT_EXACT);
				$oMofilmUserSearch->setWhereType(2);
				$oMofilmUserSearch->setKeywords($data["term"]);
                                $oMofilmUserSearch->setOrderBy(mofilmUserSearch::ORDERBY_FULLNAME);
                                $oMofilmUserSearch->setOrderDirection(mofilmUserSearch::ORDER_ASC);
				$result = $oMofilmUserSearch->search();		
				$json_arr = array();
				foreach ( $result as $oProfile ) {
					if( $oProfile->getFirstName() ) {
						$json_arr[] = array("label" => $oProfile->getFullName(),"value" => $oProfile->getFullName(), "key" => $oProfile->getID());
					}
				}

				//$json_arr[] = array("label" => "<b> Search by name,email addrress. <br /> Enter the email addrress if you have not found the user <br /> we shall invite for you ! </b>","value" => "none");
				$list = json_encode($json_arr);
				echo $list;
				return;				
			}
		}
		
		
	}
	
	
}