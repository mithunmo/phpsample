<?php
/**
 * currenciesController
 *
 * Stored in currenciesController.class.php
 * 
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category currenciesController
 * @version $Rev: 11 $
 */


/**
 * currenciesController
 *
 * currenciesController class
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category currenciesController
 */
class currenciesController extends mvcDaoController {
	
	/**
	 * @see mvcControllerBase::initialise()
	 */
	function initialise() {
		parent::initialise();
		
		$this->setControllerView('currenciesView');
	}
	
	/**
	 * @see mvcControllerBase::addInputFilters()
	 */
	function addInputFilters() {
		$this->getInputManager()->addFilter('PrimaryKey', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('Description', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('IsoCodeString', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('IsoCodeNumeric', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('Symbol', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Position', utilityInputFilter::filterString());
	}
	
	/**
	 * @see mvcControllerBase::addInputToModel()
	 *
	 * @param array $inData
	 * @param currenciesModel $inModel
	 */
	function addInputToModel($inData, $inModel) {
		$inModel->setID($inData['PrimaryKey']);
		$inModel->setDescription($inData['Description']);
		$inModel->setIsoCodeString($inData['IsoCodeString']);
		$inModel->setIsoCodeNumeric($inData['IsoCodeNumeric']);
		$inModel->setSymbol($inData['Symbol']);
		$inModel->setPosition($inData['Position']);
	}
	
	/**
	 * Builds the model
	 *
	 * @return void
	 */
	function buildModel() {
		$oModel = new currenciesModel();
		$this->setModel($oModel);
	}
}