<?php
/**
 * Smarty plugin
 * 
 * @package scorpio
 * @subpackage mvc
 * @category smarty_plugin
 * @version $Rev: 761 $
 */


/**
 * smarty_prefilter_translate
 * 
 * This is a prefilter that provides access to the translation system for the current site.
 * When I18N support is activated, the first request is processed via this prefilter. This
 * generates various compiled forms of the templates. CompileIDs are handled automatically
 * via the mvcViewBase system.
 * 
 * The processing of templates via Smarty becomes the following path:
 * 
 * 1. template is requested for render or compile
 * 2. template is passed to translate prefilter
 * 3. if I18n is active for the site, the template content is processed
 * 4. I18n config is pulled from the site configuration
 * 5. the language location is set to the CURRENT site
 * 6. text to be replaced is parsed out from the template content
 * 7. each string chunk is passed to the {@link translateAdaptor->translate()} method
 * 8. the returned translated string is replaced into the template content
 * 9. the language markup tags are removed
 * 10. the template content is returned for further processing
 * 
 * This can and will have a performance impact especially if always compile and always
 * recompile are enabled in Smarty. However, once the initial passes have been made for
 * each language, compiling will not need to be performed again thus skipping this step
 * entirely.
 * 
 * This prefilter is based on {@link http://code.google.com/p/intsmarty/ smarty_lang_prefilter}
 * by {@link http://www.coggeshall.org/ John Coggeshall}.
 *
 * @param string $content
 * @param Smarty $smarty
 * @return string
 */
function smarty_prefilter_translate($content, $smarty) {
	if ( false ) $oRequest = new mvcRequest();
	$oRequest = $smarty->getTemplateVars('oRequest');
	
	if ( is_object($oRequest) && $oRequest->getDistributor()->getSiteConfig()->isI18nActive() ) {
		$langLocale = $oRequest->getLocale();
		$langIdentifier = $oRequest->getDistributor()->getSiteConfig()->getI18nIndentifier()->getParamValue();
		$langAdaptor = $oRequest->getDistributor()->getSiteConfig()->getI18nAdaptor()->getParamValue();
		$langOptions = $oRequest->getDistributor()->getSiteConfig()->getI18nAdaptorOptions();
		if ( $langOptions instanceof utilityOutputWrapper ) {
			$langOptions = $langOptions->getSeed();
		}
		$langData = $oRequest->getDistributor()->getSiteConfig()->getSitePath()->getParamValue().'/libraries/lang/';
		
		$ldq = preg_quote($smarty->left_delimiter, '!');
		$rdq = preg_quote($smarty->right_delimiter, '!');
		
		$oTransAdap = translateManager::getInstance($langAdaptor, $langData, $langLocale, $langOptions);
		
		/* Grab all of the tagged strings */
		$matches = array();
		preg_match_all("!{$ldq}{$langIdentifier}{$rdq}(.*?){$ldq}/{$langIdentifier}{$rdq}!s", $content, $matches);
		
		foreach( $matches[1] as $str) {
			$p_str = preg_quote($str);
			$content = preg_replace("!{$ldq}{$langIdentifier}{$rdq}$p_str{$ldq}/{$langIdentifier}{$rdq}!s", $oTransAdap->translate($str), $content);
		}
		
		/* Strip off the tags now that the strings have been replaced */
		$content = preg_replace("!{$ldq}{$langIdentifier}{$rdq}(.*?){$ldq}/{$langIdentifier}{$rdq}!s", "\${1}", $content);
	}
	return $content;
}