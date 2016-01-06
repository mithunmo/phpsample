<?php
/**
 * reviewController
 *
 * Stored in reviewController.class.php
 * 
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package websites_mofilm.com
 * @subpackage controllers
 * @category reviewController
 * @version $Rev: 736 $
 */


/**
 * reviewController
 *
 * reviewController class
 * 
 * @package websites_mofilm.com
 * @subpackage controllers
 * @category reviewController
 */
class reviewController extends mvcController {
	
	const ACTION_VIEW = 'view';
	const ACTION_COMMIT = "userCommit";
	const ACTION_CHECK_MOVIE_STATUS = "readyToCommit";
	const ACTION_REJECT = "userReject";
	
	/**
	 * @see mvcControllerBase::initialise()
	 */
	function initialise() {
		parent::initialise();
		
		$this->setDefaultAction(self::ACTION_VIEW);
		$this->setRequiresAuthentication(true);
		
		$this->getControllerActions()->addAction(self::ACTION_VIEW);
		$this->getControllerActions()->addAction(self::ACTION_COMMIT);
		$this->getControllerActions()->addAction(self::ACTION_CHECK_MOVIE_STATUS);
		$this->getControllerActions()->addAction(self::ACTION_REJECT);
	}
	
	/**
	 * @see mvcControllerBase::launch()
	 */
	function launch() {
		if ( $this->getAction() == self::ACTION_VIEW ) {
			$movieID = $this->getActionFromRequest(false, 1);
			$this->getModel()->setMovieID($movieID);
			$this->getModel()->setUserID($this->getRequest()->getSession()->getUser()->getID());
						
			if ( $this->getModel()->doSearch()->getInstance($movieID) && mofilmMovieManager::getInstance()->setLoadOnlyActive(false)->getMovieByID($movieID)->getActive() == "N" ) {
				$oView = new reviewView($this);
				$oView->showReviewPage();
			} else {
				throw new mvcDistributorInvalidActionException($this->getAction(), $eventID);
			}
			
			
		} elseif ( $this->getAction() == self::ACTION_COMMIT ) {
			$this->addInputFilters();
			$data = $this->getInputManager()->doFilter();
			$this->addInputToModel($data, $this->getModel());
			$this->getModel()->commitUserMovie();
			$oView = new reviewView($this);
			$oView->showUserCommitPage();		
		} elseif ( $this->getAction() == self::ACTION_CHECK_MOVIE_STATUS ) {
			$this->addInputFilters();
			$data = $this->getInputManager()->doFilter();
			$this->addInputToModel($data, $this->getModel());
			$oView = new reviewView($this);
			$oView->showIsReadyCommitPage();					
		} elseif ( $this->getAction() == self::ACTION_REJECT ) {
			$this->addInputFilters();
			$data = $this->getInputManager()->doFilter();
			$this->addInputToModel($data, $this->getModel());
			$this->getModel()->rejectUserMovie();
			$oView = new reviewView($this);
			$oView->showUserRejectPage();					
		}
	}
	
	
	
	/**
	 * @see mvcControllerBase::addInputFilters()
	 */
	function addInputFilters() {
		$this->getInputManager()->addFilter("movieID", utilityInputFilter::filterInt());
	}
	
	/**
	 * @see mvcControllerBase::addInputToModel()
	 */
	function addInputToModel($inData, $inModel) {
		$this->getModel()->setMovieID($inData["movieID"]);
	}
	
	/**
	 * Fetches the model
	 *
	 * @return reviewModel
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
		$oModel = new reviewModel();
		$this->setModel($oModel);
	}
}