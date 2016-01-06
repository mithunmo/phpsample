<?php
/**
 * mvcViewHelperInterface.class.php
 * 
 * mvcViewHelperInterface class
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage mvc
 * @category mvcViewHelperInterface
 * @version $Rev: 668 $
 */


/**
 * mvcViewHelperInterface class
 * 
 * Interface definition for the mvcViewHelper system.
 * 
 * @package scorpio
 * @subpackage mvc
 * @category mvcViewHelperInterface
 */
interface mvcViewHelperInterface {
	
	/**
	 * Sets the view object currently being rendered
	 *
	 * @param mvcViewBase $inView
	 * @return mvcViewHelperInterface
	 */
	function setView(mvcViewBase $inView);
	
	/**
	 * Renders the helper out of context, returning the result
	 *
	 * @return string
	 */
	function render();
}