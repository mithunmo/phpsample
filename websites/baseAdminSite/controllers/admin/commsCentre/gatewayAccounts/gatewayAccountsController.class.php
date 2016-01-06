<?php
/**
 * gatewayAccountsController
 *
 * Stored in gatewayAccountsController.class.php
 * 
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category gatewayAccountsController
 * @version $Rev: 11 $
 */


/**
 * gatewayAccountsController
 *
 * gatewayAccountsController class
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category gatewayAccountsController
 */
class gatewayAccountsController extends mvcDaoController {
	
	/**
	 * @see mvcControllerBase::initialise()
	 */
	function initialise() {
		parent::initialise();
		
		$this->setControllerView('gatewayAccountsView');
	}
	
	/**
	 * @see mvcControllerBase::addInputFilters()
	 */
	function addInputFilters() {
		$this->getInputManager()->addFilter('PrimaryKey', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('GatewayID', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('Description', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Prs', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Active', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('NetworkID', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('Tariff', utilityInputFilter::filterFloat());
		$this->getInputManager()->addFilter('CountryID', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('CurrencyID', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('RequireAcknowledgement', utilityInputFilter::filterInt());
	}
	
	/**
	 * @see mvcControllerBase::addInputToModel()
	 *
	 * @param array $inData
	 * @param gatewayAccountsModel $inModel
	 */
	function addInputToModel($inData, $inModel) {
		$inModel->setGatewayAccountID($inData['PrimaryKey']);
		$inModel->setGatewayID($inData['GatewayID']);
		$inModel->setDescription($inData['Description']);
		$inModel->setPrs($inData['Prs']);
		$inModel->setActive($inData['Active']);
		$inModel->setNetworkID($inData['NetworkID']);
		$inModel->setTariff((float)$inData['Tariff']);
		$inModel->setCountryID($inData['CountryID']);
		$inModel->setCurrencyID($inData['CurrencyID']);
		$inModel->setRequireAcknowledgement($inData['RequireAcknowledgement']);
	}
	
	/**
	 * Builds the model
	 *
	 * @return void
	 */
	function buildModel() {
		$oModel = new gatewayAccountsModel();
		$this->setModel($oModel);
	}
}