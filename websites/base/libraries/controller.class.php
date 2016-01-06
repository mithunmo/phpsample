<?php
/**
 * mvcController.class.php
 * 
 * mvcController class
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2009
 * @package scorpio
 * @subpackage websites_base_libraries
 * @category mvcController
 */


/**
 * mvcController
 * 
 * Main site mvcController implementation, holds base directives and defaults for the site
 *
 * @package scorpio
 * @subpackage websites_base_libraries
 * @category mvcController
 */
abstract class mvcController extends mvcControllerBase {
	
	/**
	 * @see mvcControllerBase::authorise()
	 */
	function authorise() {
	
	}
	
	/**
	 * @see mvcControllerBase::initialise()
	 */
	function initialise() {
		$this->setRequiresAuthentication(false);
	}
	
	/**
	 * @see mvcControllerBase::isValidAction()
	 */
	function isValidAction() {
		return true;
	}
	
	/**
	 * @see mvcControllerBase::isValidView()
	 */
	function isValidView($inView) {
		return true;
	}
	
	/**
	 * @see mvcControllerBase::isAuthorised()
	 */
	function isAuthorised() {
		return true;
	}
	
	/**
	 * @see mvcControllerBase::hasAuthority()
	 */
	function hasAuthority($inActivity) {
		return true;
	}
}