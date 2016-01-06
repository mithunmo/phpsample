<?php
/**
 * mvcView.class.php
 *
 * mvcView class
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package baseAdminSite
 * @subpackage websites_baseAdminSite_libraries
 * @category mvcView
 * @version $Rev: 11 $
 */


/**
 * mvcView
 *
 * Main site mvcView implementation, holds base directives and defaults for the site
 *
 * @package baseAdminSite
 * @subpackage websites_baseAdminSite_libraries
 * @category mvcView
 */
class mvcView extends mvcViewBase {
	
	/**
	 * Assigns some default values to template engine that are always needed
	 *
	 * @return void
	 */
	function setupInitialVars() {
		parent::setupInitialVars();
		$oConfig = $this->getRequest()->getDistributor()->getSiteConfig();
		$this->getEngine()->getEngine()->addPluginsDir(
			$oConfig->getSitePath().'/libraries/smarty'
		);
		
		$loop = true;
		while ( $loop === true ) {
			$oParent = $oConfig->getParentConfig();
			if ( $oParent instanceof mvcSiteConfig ) {
				$this->getEngine()->getEngine()->addPluginsDir(
					$oParent->getSitePath().'/libraries/smarty'
				);
				$oConfig = $oParent;
			} else {
				$loop = false;
			}
		}

		/*
		 * Always assign the status message for every view
		 */
		$this->getEngine()->assign(
			'statusMessage', $this->getRequest()->getSession()->getStatusMessage()
		);

		/*
		 * Always assign the current user
		 */
		$this->getEngine()->assign(
			'oUser', utilityOutputWrapper::wrap($this->getRequest()->getSession()->getUser())
		);

		/*
		 * Assign current language resource
		 */
		$this->getEngine()->assign(
			'currentLanguage', $this->getRequest()->getDistributor()->getSiteConfig()->getI18nDefaultLanguage()->getParamValue()
		);

		/*
		 * Set current controller
		 */
		$this->getEngine()->assign('oController', utilityOutputWrapper::wrap($this->getController()));
                
                $this->getEngine()->assign(
			'accessToken', $this->getRequest()->getSession()->getToken());
                
	}
}