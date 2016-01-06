<?php
/**
 * mvcErrorInterface
 * 
 * Stored in interface.iface.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage mvc
 * @category mvcErrorInterface
 * @version $Rev: 668 $
 */


/**
 * mvcErrorInterface inteface
 * 
 * mvcErrorController interface. Ensures that custome error handlers
 * provide the necessary interface to the distributor for handling
 * errors.
 * 
 * @package scorpio
 * @subpackage mvc
 * @category mvcErrorInterface
 */
interface mvcErrorInterface {
	
	/**
	 * Returns the exception object
	 * 
	 * @return Exception
	 */
	function getException();

	/**
	 * The exception that was raised
	 * 
	 * @param Exception $inException
	 */
	function setException(Exception $inException);
}