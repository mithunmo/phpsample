<?php
/**
 * mofilmCommsNewsletterFilter
 *
 * Stored in filter.class.php
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmCommsNewsletterFilter
 * @category mofilmCommsNewsletterFilter
 * 
 */
interface mofilmCommsNewsletterFilter {
	
	/**
	 * Points to index 0 in the params list
	 */
	const FILTER_CLASSNAME = 0;

	/**
	 * Points to index 1 in the param list
	 *
	 */
	const FILTER_EVENTID = 1;



	/**
	 * Applys the desired filter
	 *
	 */
	function apply();
}