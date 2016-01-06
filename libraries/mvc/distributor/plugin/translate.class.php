<?php
/**
 * session.class.php
 * 
 * mvcDistributorPluginTranslate class
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage mvc
 * @category mvcDistributorPluginTranslate
 * @version $Rev: 650 $
 */


/**
 * mvcDistributorPluginTranslate class
 * 
 * Registers the sites I18n support and creates the translationManager instance.
 *
 * @package scorpio
 * @subpackage mvc
 * @category mvcDistributorPluginTranslate
 */
class mvcDistributorPluginTranslate extends mvcDistributorPlugin {
	
	/**
	 * Registers a translation adaptor pre-dispatch
	 *
	 * @return void
	 */
	function executeOnDispatcherInitialise() {
		$langIdentifier = $this->getRequest()->getDistributor()->getSiteConfig()->getI18nIndentifier()->getParamValue();
		$langAdaptor = $this->getRequest()->getDistributor()->getSiteConfig()->getI18nAdaptor()->getParamValue();
		$langLanguage = $this->getRequest()->getDistributor()->getSiteConfig()->getI18nDefaultLanguage()->getParamValue();
		$langOptions = $this->getRequest()->getDistributor()->getSiteConfig()->getI18nAdaptorOptions();

		if ( $langOptions instanceof utilityOutputWrapper ) {
			$langOptions = $langOptions->getSeed();
		}
		$langData = $this->getRequest()->getDistributor()->getSiteConfig()->getSitePath()->getParamValue().'/libraries/lang/';

		translateManager::getInstance($langAdaptor, $langData, $langLanguage, $langOptions);
	}
}