<?php
/**
 * territoriesController
 *
 * Stored in territoriesController.class.php
 * 
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category territoriesController
 * @version $Rev: 11 $
 */


/**
 * territoriesController
 *
 * territoriesController class
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category territoriesController
 */
class territoriesController extends mvcDaoController {
	
	/**
	 * @see mvcControllerBase::initialise()
	 */
	function initialise() {
		parent::initialise();
		
		$this->setControllerView('territoriesView');
	}
	
	/**
	 * @see mvcControllerBase::addInputFilters()
	 */
	function addInputFilters() {
		$this->getInputManager()->addFilter('PrimaryKey', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('Country', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('ShortName', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Languages', utilityInputFilter::filterStringArray());
		$this->getInputManager()->addFilter('States', utilityInputFilter::filterStringArray());
	}
	
	/**
	 * @see mvcControllerBase::addInputToModel()
	 *
	 * @param array $inData
	 * @param territoriesModel $inModel
	 */
	function addInputToModel($inData, $inModel) {
		$inModel->setID($inData['PrimaryKey']);
		$inModel->setCountry($inData['Country']);
		$inModel->setShortName($inData['ShortName']);
		
		if ( isset($inData['Languages']) && count($inData['Languages']) >= 0 ) {
			$inModel->getLanguageSet()->reset();
			foreach ( $inData['Languages'] as $languageID ) {
				$inModel->getLanguageSet()->setObject(mofilmLanguage::getInstance($languageID));
			}
		}
		if ( isset($inData['States']) && count($inData['States']) >= 0 ) {
			if ( $inModel->getStateSet()->getCount() > 0 ) {
				foreach ( $inModel->getStateSet() as $oState ) {
					$oState->setMarkForDeletion(true);
				}
			}
			
			foreach ( $inData['States'] as $stateData ) {
				if ( isset($stateData['ID']) && is_numeric($stateData['ID']) && $stateData['ID'] > 0 ) {
					$oState = $inModel->getStateSet()->getObjectByID($stateData['ID']);
					$oState->setMarkForDeletion(false);
				} elseif ( strlen($stateData['Name']) > 0 && strlen($stateData['Abbr']) > 0 ) {
					$oState = new mofilmTerritoryState();
					$inModel->getStateSet()->setObject($oState);
				}
				
				if ( $oState instanceof mofilmTerritoryState ) {
					$oState->setAbbreviation($stateData['Abbr']);
					$oState->setDescription($stateData['Name']);
					if ( isset($stateData['Remove']) ) {
						$oState->setMarkForDeletion(true);
					}
				}
			}
		}
	}
	
	/**
	 * Builds the model
	 *
	 * @return void
	 */
	function buildModel() {
		$oModel = new territoriesModel();
		$this->setModel($oModel);
	}
}