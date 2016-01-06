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
	 * @see mvcViewBase::__construct()
	 */
	function __construct($inController) {
		parent::__construct($inController);	
	}
}