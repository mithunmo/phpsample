<?php
/**
 * searchController
 *
 * Stored in searchController.class.php
 * 
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_mofilm.com
 * @subpackage controllers
 * @category searchController
 * @version $Rev: 11 $
 */


/**
 * searchController
 *
 * searchController class
 * 
 * @package websites_mofilm.com
 * @subpackage controllers
 * @category searchController
 */
class searchController extends mvcController {
	
	const ACTION_VIEW = 'view';
	const ACTION_SEARCH = 'doSearch';
	
	/**
	 * @see mvcControllerBase::initialise()
	 */
	function initialise() {
		parent::initialise();
		
		$this->setDefaultAction(self::ACTION_VIEW);
		$this->setRequiresAuthentication(false);
		$this->getControllerActions()
			->addAction(self::ACTION_VIEW)
			->addAction(self::ACTION_SEARCH);
			
		$this->addInputFilters();
	}
	
	/**
	 * @see mvcControllerBase::launch()
	 */
	function launch() {
		$oView = new searchView($this);
		switch ( $this->getAction() ) {
			case self::ACTION_SEARCH:
				$this->getInputManager()->setLookupGlobals(utilityInputManager::LOOKUPGLOBALS_GET);
				$data = $this->getInputManager()->doFilter();
				
				$this->addInputToModel($data, $this->getModel());
				
				$oView->showSearchResults();
			break;
			
			default:
				$oView->showSearchPage();
		}
	}
	
	
	
	/**
	 * @see mvcControllerBase::addInputFilters()
	 */
	function addInputFilters() {
		$this->getInputManager()->addFilter('callback', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Keywords', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Latest', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('Rating', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('EventID', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('SourceID', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('Offset', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('Limit', utilityInputFilter::filterInt());
	}
	
	/**
	 * @see mvcControllerBase::addInputToModel()
	 * 
	 * @param array $inData
	 * @param searchModel $inModel
	 */
	function addInputToModel($inData, $inModel) {
		if ( !isset($inData['Offset']) || $inData['Offset'] < 0 ) {
			$inData['Offset'] = 0;
		}
		if ( !isset($inData['Limit']) || $inData['Limit'] < 1 ) {
			$inData['Limit'] = 10;
		}
		if ( !isset($inData['Latest']) || $inData['Latest'] !== 1 ) {
			$inData['Latest'] = 0;
		}
		if ( !isset($inData['Rating']) || $inData['Rating'] !== 1 ) {
			$inData['Rating'] = 0;
		}
		
		$inModel->addEvent($inData['EventID']);
		$inModel->addSource($inData['SourceID']);
		if ( isset($inData['Keywords']) && strlen($inData['Keywords']) > 3 ) {
			$inModel->setKeywords($inData['Keywords']);
		}
		$inModel->setLimit($inData['Limit']);
		$inModel->setOffset($inData['Offset']);
		
		if ( $inData['Latest'] ) {
			$inModel->setOrderBy(searchModel::ORDERBY_DATE);
			$inModel->setOrderDirection(searchModel::ORDER_DESC);
		}
		if ( $inData['Rating'] ) {
			$inModel->setOrderBy(searchModel::ORDERBY_RATING);
			$inModel->setOrderDirection(searchModel::ORDER_DESC);
		}
		if ( $inData['callback'] && strlen($inData['callback']) > 0 ) {
			$inModel->setJsonCallback($inData['callback']);
		}
	}
	
	/**
	 * Fetches the model
	 *
	 * @return searchModel
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
		$oModel = new searchModel();
		$oModel->setUser(new mofilmUser());
		$oModel->setStatus(mofilmMovieBase::STATUS_APPROVED);
		$this->setModel($oModel);
	}
}