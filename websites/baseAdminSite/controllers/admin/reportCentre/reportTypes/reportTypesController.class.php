<?php
/**
 * reportTypesController
 *
 * Stored in reportTypesController.class.php
 * 
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category reportTypesController
 * @version $Rev: 11 $
 */


/**
 * reportTypesController
 *
 * reportTypesController class
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category reportTypesController
 */
class reportTypesController extends mvcDaoController {
	
	/**
	 * @see mvcControllerBase::initialise()
	 */
	function initialise() {
		parent::initialise();
		
		$this->setControllerView('reportTypesView');
	}
	
	/**
	 * @see mvcControllerBase::addInputFilters()
	 */
	function addInputFilters() {
		$this->getInputManager()->addFilter('PrimaryKey', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('ReportTypeID', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('TypeName', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Description', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Visible', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('ClassName', utilityInputFilter::filterString());
	}
	
	/**
	 * @see mvcControllerBase::addInputToModel()
	 *
	 * @param array $inData
	 * @param reportTypesModel $inModel
	 */
	function addInputToModel($inData, $inModel) {
		$inModel->setReportTypeID($inData['PrimaryKey']);
		$inModel->setTypeName($inData['TypeName']);
		$inModel->setDescription($inData['Description']);
		$inModel->setVisible($inData['Visible']);
		$inModel->setClassName($inData['ClassName']);
	}
	
	/**
	 * Builds the model
	 *
	 * @return void
	 */
	function buildModel() {
		$oModel = new reportTypesModel();
		$this->setModel($oModel);
	}
}