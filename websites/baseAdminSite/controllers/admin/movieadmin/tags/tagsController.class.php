<?php
/**
 * tagsController
 *
 * Stored in tagsController.class.php
 * 
 * @author Pavan Kumar P G
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category tagsController
 * @version $Rev: 11 $
 */


/**
 * tagsController
 *
 * tagsController class
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category tagsController
 */
class tagsController extends mvcDaoController {
	
	const ACTION_AUTOCOMPLETE_SEARCH = 'searchTag';

	/**
	 * @see mvcControllerBase::initialise()
	 */
	function initialise() {
		parent::initialise();
		
		$this->setControllerView('tagsView');
		$this->getControllerActions()->addAction(self::ACTION_AUTOCOMPLETE_SEARCH);
	}
	
	/**
	 * @see mvcControllerBase::launch()
	 */
	function launch() {
		switch( $this->getAction() ) {
			case self::ACTION_AUTOCOMPLETE_SEARCH: $this->actionAutoCompleteSearch(); break;
			default :	parent::launch();
		}
	}
	
	/**
	 * @see mvcControllerBase::addInputFilters()
	 */
	function addInputFilters() {
		$this->getInputManager()->addFilter('PrimaryKey', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('Name', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Type', utilityInputFilter::filterString());
	}
	
	/**
	 * @see mvcControllerBase::addInputToModel()
	 *
	 * @param array $inData
	 * @param tagsModel $inModel
	 */
	function addInputToModel($inData, $inModel) {
		$inModel->setID($inData['PrimaryKey']);
		$inModel->setName($inData['Name']);
		$inModel->setType($inData['Type']);
	}
	
	/**
	 * Builds the model
	 *
	 * @return void
	 */
	function buildModel() {
		$oModel = new tagsModel();
		$this->setModel($oModel);
	}
	
	/**
	 * Returns the tag which matches the characters typed
	 */
	function actionAutoCompleteSearch() {
		$this->getInputManager()->setLookupGlobals(utilityInputManager::LOOKUPGLOBALS_GET);
		$data = $this->getInputManager()->doFilter();
		return true;
	}
}