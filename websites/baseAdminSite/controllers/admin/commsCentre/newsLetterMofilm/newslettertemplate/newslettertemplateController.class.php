<?php
/**
 * newslettertemplateController
 *
 * Stored in newslettertemplateController.class.php
 * 
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category newslettertemplateController
 * @version $Rev: 624 $
 */


/**
 * newslettertemplateController
 *
 * newslettertemplateController class
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category newslettertemplateController
 */
class newslettertemplateController extends mvcDaoController {

	const ACTION_GET_HTML = 'getHtml';
	/**
	 * @see mvcControllerBase::initialise()
	 */
	function initialise() {
		parent::initialise();
		
		$this->setControllerView('newslettertemplateView');
		$this->getControllerActions()->addAction(self::ACTION_GET_HTML);
	}
	/**
	 *
	 * @see mvcControllerBase::launch()
	 */
	function launch(){
		if($this->getAction() == self::ACTION_GET_HTML) {
		    if($this->getRequest()->isAjaxRequest()) {
			    $this->getInputManager()->setLookupGlobals(utilityInputManager::LOOKUPGLOBALS_GET);
			    $data = $this->getInputManager()->doFilter();
			    $inHtml = $this->getModel()->getHtmlTemplateById($data['ID']);
			    $oView = new newslettertemplateView($this);
			    $oView->getHtml($inHtml);
			    return;
		    }
		}
		parent::launch();
	}
	
	/**
	 * @see mvcControllerBase::addInputFilters()
	 */
	function addInputFilters() {
		$this->getInputManager()->addFilter('PrimaryKey', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('ID', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Name', utilityInputFilter::filterString());
		//$this->getInputManager()->addFilter('HtmlTemplate', utilityInputFilter::filterString());
	}
	
	/**
	 * @see mvcControllerBase::addInputToModel()
	 *
	 * @param array $inData
	 * @param newslettertemplateModel $inModel
	 */
	function addInputToModel($inData, $inModel) {
		//$inModel->setPrimaryKey($inData['PrimaryKey']);
		$inModel->setID($inData['PrimaryKey']);
		$inModel->setName($inData['Name']);
		$inModel->setHtmlTemplate(stripslashes(trim($_POST['HtmlTemplate'])));
	}
	
	/**
	 * Builds the model
	 *
	 * @return void
	 */
	function buildModel() {
		$oModel = new newslettertemplateModel();
		$this->setModel($oModel);
	}
}