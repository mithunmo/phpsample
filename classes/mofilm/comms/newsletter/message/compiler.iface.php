<?php
/**
 * mofilmCommsNewsletterMessageCompiler.iface.php
 * 
 * System mofilmCommsNewsletterMessageCompiler Interface
 * 
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2007-2010
 * @package scorpio
 * @subpackage system
 * @category mofilmCommsNewsletterMessageCompilerInterface
 * @version $Rev: 650 $
 */


/**
 * mofilmCommsNewsletterMessageCompiler
 * 
 * mofilmCommsNewsletterMessageCompiler Interface
 * 
 * @package scorpio
 * @subpackage system
 * @category mofilmCommsNewsletterMessageCompilerInterface
 */
interface mofilmCommsNewsletterMessageCompilerInterface {
 	
	/**
	 * Compiles the message based on the param
	 *
	 * @return string
	 */
 	function compile();
}