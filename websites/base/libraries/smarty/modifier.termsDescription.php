<?php
/**
 * Smarty plugin
 *
 * @package mofilm
 * @subpackage websites_base
 * @category smarty_plugin
 * @version $Rev: 11 $
 */


/**
 * Returns the description of the termsID
 *
 * Type:     modifier<br>
 * Name:     termsDescription<br>
 * Date:     Feb 11, 2010
 * Purpose:  Returns the description for the termsID
 * Input:    terms ID for the record
 * Example:  {$termsID|termsDescription}
 * @author   Chris Noden
 * @version 1.0
 * @param integer
 * @return string
 */
function smarty_modifier_termsDescription($termsID) {
	$value = false;
	$oTerms = mofilmTerms::getInstance($termsID);
	if ( $oTerms->getID() > 0 ) {
		$value = $oTerms->getDescription();
	}
	$oTerms = null;
	unset($oTerms);

	return $value;
}