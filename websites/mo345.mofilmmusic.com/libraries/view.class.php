<?php
/**
 * mvcView.class.php
 *
 * mvcView class
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm.com
 * @subpackage websites_mofilm.com_libraries
 * @category mvcView
 */


/**
 * mvcView
 *
 * Main site mvcView implementation, holds base directives and defaults for the site
 *
 * @package mofilm.com
 * @subpackage websites_mofilm.com_libraries
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
		
		/*
		 * Set Mofilm site URIs
		 */
		$this->getEngine()->assign('mofilmWwwUri', system::getConfig()->getParam('mofilm', 'wwwMofilmUri', 'http://www.mofilm.com')->getParamValue());
		
		if ( $this->getRequest()->getDistributor()->getSiteConfig()->getI18nDefaultLanguage()->getParamValue() == "zh" ) {
			$this->getEngine()->assign('mofilmMyUri', "http://my.mofilm.cn");
		} else {
			$this->getEngine()->assign('mofilmMyUri', "http://my.mofilm.com");
		}	
	}
}