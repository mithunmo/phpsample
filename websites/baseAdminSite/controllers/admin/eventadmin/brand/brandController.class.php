<?php
/**
 * brandController
 *
 * Stored in brandController.class.php
 * 
 * @author Poulami Chakraborty
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category brandController
 * @version $Rev: 624 $
 */


/**
 * brandController
 *
 * brandController class
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category brandController
 */
class brandController extends mvcDaoController {
	

	/**name
	 * @see mvcControllerBase::initialise()case self::ACTION_NEW_BRAND:
                         $oView = new brandView($this);                   
                      
                        $oView->getObjectFormView();
                        break;
	 */
	function initialise() {
		parent::initialise();
                $this->setControllerView('brandView');
             
		
                
	}
        
        function launch() {
         
              
                             parent::launch();
                         
                
        }
	
	/**
	 * @see mvcControllerBase::addInputFilters()
	 */
	function addInputFilters() {
		$this->getInputManager()->addFilter('PrimaryKey', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('ID', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Name', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Company', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('CorporateID', utilityInputFilter::filterString());
                $this->getInputManager()->addFilter('IndustryID', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Firstname', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Lastname', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Address', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('City', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Country', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Email', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Phone', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Status', utilityInputFilter::filterString());
	}
	
	/**
	 * @see mvcControllerBase::addInputToModel()
	 *
	 * @param array $inData
	 * @param brandModel $inModel
	 */
	function addInputToModel($inData, $inModel) {
		/**
		 * @todo set the primary key here
		 */
		//$inModel->setPrimaryKey($inData['PrimaryKey']);
		$inModel->setID($inData['ID']);
		$inModel->setName($inData['Name']);
		$inModel->setCompany($inData['Company']);
		$inModel->setCorporateID($inData['CorporateID']);
                $inModel->setIndustryID($inData['IndustryID']);
		$inModel->setFirstname($inData['Firstname']);
		$inModel->setLastname($inData['Lastname']);
		$inModel->setAddress($inData['Address']);
		$inModel->setCity($inData['City']);
		$inModel->setCountry($inData['Country']);
		$inModel->setEmail($inData['Email']);
		$inModel->setPhone($inData['Phone']);
		$inModel->setStatus($inData['Status']);
	}
	
	/**
	 * Builds the model
	 *function actionView() {
		$this->getInputManager()->setLookupGlobals(utilityInputManager::LOOKUPGLOBALS_GET);
		$this->getInputManager()->addFilter('CorporateID', utilityInputFilter::filterInt());
		$data = $this->getInputManager()->doFilter();
		
		$this->setSearchOptionFromRequestData($data, 'CorporateID');
		
		parent::actionView();
	}
	 * @return void
	 */
	function buildModel() {
		$oModel = new brandModel();
		$this->setModel($oModel);
	}
        
        function actionView() {
                
		$this->getInputManager()->setLookupGlobals(utilityInputManager::LOOKUPGLOBALS_GET);
		$this->getInputManager()->addFilter('CorporateID', utilityInputFilter::filterInt());
                $this->getInputManager()->addFilter('IndustryID', utilityInputFilter::filterInt());
                $this->getInputManager()->addFilter('EventID', utilityInputFilter::filterInt());
		$data = $this->getInputManager()->doFilter();
	
                    $this->setSearchOptionFromRequestData($data, 'CorporateID');
               
           
                    $this->setSearchOptionFromRequestData($data, 'EventID');
             
		
		parent::actionView();
	}
        
}	