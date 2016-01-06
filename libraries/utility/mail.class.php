<?php
/**
 * utilityMail Class
 * 
 * Stored in mail.class.php
 *
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage utility
 * @category utilityMail
 * @version $Rev: 706 $
 */


/**
 * utilityMail Class
 * 
 * This class is used for sending email and wraps PHPMailer. It automatically
 * sets the From and FromName to the system config properties: system.fromAddress
 * and system.hostname.
 *
 * @package scorpio
 * @subpackage utility
 * @category utilityMail
 */
class utilityMail extends PHPMailer {
	
	/**
	 * Returns new mailer
	 *
	 * @return utilityMail
	 */
	function __construct() {
		$this->IsMail();
		$this->From     = system::getConfig()->getSystemFromAddress();
		$this->FromName = system::getConfig()->getSystemHostname();
	}
}