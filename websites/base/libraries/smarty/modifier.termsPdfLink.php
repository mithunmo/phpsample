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
 * Returns the PDF link (url) of the termsID
 *
 * Type:     modifier<br>
 * Name:     termsPdfLink<br>
 * Date:     Feb 11, 2010
 * Purpose:
 * Input:    terms ID for the record
 * Example:  {$termsID|termsPdfLink}
 * @author   Chris Noden
 * @version 1.0
 * @param integer
 * @return string
 */
function smarty_modifier_termsPdfLink($termsID) {
	$value = false;
	$oTerms = mofilmTerms::getInstance($termsID);
	if ( $oTerms->getID() > 0 ) {
		$value = $oTerms->getPdfLink();
	}
	$oTerms = null;
	unset($oTerms);
	
	return $value;
}