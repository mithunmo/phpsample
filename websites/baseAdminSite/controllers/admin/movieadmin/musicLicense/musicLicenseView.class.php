<?php
/**
 * musicLicenseView.class.php
 * 
 * musicLicenseView class
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category musicLicenseView
 * @version $Rev: 634 $
 */


/**
 * musicLicenseView class
 * 
 * Provides the "musicLicenseView" page
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category musicLicenseView
 */
class musicLicenseView extends mvcView {

	/**
	 * @see mvcViewBase::setupInitialVars()
	 */
	function setupInitialVars() {
		parent::setupInitialVars();
	}
	
	/**
	 * Shows the musicLicenseView page
	 *
	 * @return void
	 */
	function showMusicLicensePage() {
		$this->setCacheLevelNone();
		$this->render($this->getTpl('musicLicense'));
	}

	/**
	 * Shows the musicLicenseValidateView page
	 *
	 * @return void
	 */
	function showMusicLicenseValidatePage() {
		$this->getEngine()->assign('oModel', utilityOutputWrapper::wrap($this->getModel()));		
		$this->render($this->getTpl('movieLicense'));

	}
	
	/**
	 * Shows the license associated with movie details
	 * 
	 * @return void
	 */
	function showMovieDetails() {
		$this->getEngine()->assign('oModel', utilityOutputWrapper::wrap($this->getModel()));		
		$this->render($this->getTpl('movieValidLicense'));
	}
	
	/**
	 * Handles the error handling 
	 * 
	 * @param string $inError 
	 * @return void
	 */
	function showError($inError) {
		$this->getEngine()->assign('error', $inError);		
		$this->render($this->getTpl('errorMessage'));
	}
}