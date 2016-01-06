<?php
/**
 * musicLicenseController
 *
 * Stored in musicLicenseController.class.php
 * 
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category musicLicenseController
 * @version $Rev: 623 $
 */


/**
 * musicLicenseController
 *
 * musicLicenseController class
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category musicLicenseController
 */
class musicLicenseController extends mvcController {
	
	const ACTION_VIEW = 'view';
	const ACTION_VALIDATE = "validate";
	const ACTION_MOVIEDETAILS = "details";
	
	
	
	/**
	 * @see mvcControllerBase::initialise()
	 */
	function initialise() {
		parent::initialise();
		
		$this->setDefaultAction(self::ACTION_VIEW);
		$this->getControllerActions()->addAction(self::ACTION_VIEW);
		$this->getControllerActions()->addAction(self::ACTION_VALIDATE);
		$this->getControllerActions()->addAction(self::ACTION_MOVIEDETAILS);
	}
	
	/**
	 * @see mvcControllerBase::launch()
	 */
	function launch() {
		if ( $this->getAction() == self::ACTION_VALIDATE ) {
			try {
				$data = $this->getInputManager()->doFilter();
				$this->addInputToModel($data, $this->getModel());
				$oView = new musicLicenseView($this);
				$oView->showMusicLicenseValidatePage();
				return;
			} catch (Exception $e) {
				$oView = new musicLicenseView($this);
				$oView->showError($e->getMessage());
			}
		} elseif ( $this->getAction() == self::ACTION_MOVIEDETAILS ) {
			try {
				$data = $this->getInputManager()->doFilter();
				$this->addInputToModel($data, $this->getModel());
				$oView = new musicLicenseView($this);
				$oView->showMovieDetails();
				return;
			}
			catch(Exception $e) {
				$oView = new musicLicenseView($this);
				$oView->showError($e->getMessage());				
			}
		} else {
			$oView = new musicLicenseView($this);
			$oView->showMusicLicensePage();
			
		}	
	}
	
	
	
	/**
	 * @see mvcControllerBase::addInputFilters()
	 */
	function addInputFilters() {
		$this->getInputManager()->addFilter("LicenseID", utilityInputFilter::filterStringArray());
		$this->getInputManager()->addFilter("MovieID", utilityInputFilter::filterInt());
	}
	
	/**
	 * @see mvcControllerBase::addInputToModel()
	 *
	 * @param array $inData
	 * @param musicLicenseModel $inModel
	 */
	function addInputToModel($inData, $inModel) {
		if ( is_array($inData["LicenseID"]) ) {
			$inModel->setLicenseID($inData["LicenseID"]);
		} else {
			$licenseArray = array();
			$oMovie = mofilmMovieManager::getInstanceByID($inData["MovieID"]);
			if ( !$oMovie instanceof mofilmMovie ) {
				throw new mofilmException("MovieID not found");
			}
			
			foreach ( $oMovie->getLicenseSet() as $oMofilmUserMusicLicense ) {
				$licenseArray[] = $oMofilmUserMusicLicense->getLicenseID();
			}
			$inModel->setLicenseID($licenseArray);
		}
		$inModel->setMovieID($inData["MovieID"]);
	}
	
	/**
	 * Fetches the model
	 *
	 * @return musicLicenseModel
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
		$oModel = new musicLicenseModel();
		$this->setModel($oModel);
	}
}