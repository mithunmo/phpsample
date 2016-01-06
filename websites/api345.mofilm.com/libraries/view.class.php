<?php
/**
 * mvcView.class.php
 * 
 * mvcView class
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2009
 * @package scorpio
 * @subpackage websites_base_libraries
 * @category mvcView
 */


/**
 * mvcView
 * 
 * Main site mvcView implementation, holds base directives and defaults for the site
 *
 * @package scorpio
 * @subpackage websites_base_libraries
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
		 * Set current controller
		 */
		$this->getEngine()->assign('oController', utilityOutputWrapper::wrap($this->getController()));

		/*
		 * Set Mofilm site URIs
		 */
		$this->getEngine()->assign('mofilmWwwUri', system::getConfig()->getParam('mofilm', 'wwwMofilmUri', 'http://www.mofilm.com')->getParamValue());
		$this->getEngine()->assign('mofilmMyUri', system::getConfig()->getParam('mofilm', 'myMofilmUri', 'http://my.mofilm.com')->getParamValue());
	}

	/**
	 * Displays a shared "token" timeout template
	 *
	 * @return void
	 */
	function showApiTokenTimeout() {
		$this->setCacheLevelNone();

		$this->render($this->getTpl('tokenTimeout', '/error'));
	}
}