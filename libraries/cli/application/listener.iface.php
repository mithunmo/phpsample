<?php
/**
 * cliApplicationListener Interface
 * 
 * Stored in listener.iface.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage cli
 * @category cliApplicationListener
 * @version $Rev: 650 $
 */


/**
 * cliApplicationListener interface
 *
 * Defines the listener interface for application observers.
 * 
 * @package scorpio
 * @subpackage cli
 * @category cliApplicationListener
 */
interface cliApplicationListener {
	
	/**
	 * Receives the event (cliApplicationEvent $inEvent) from the application observer
	 *
	 * @param cliApplicationEvent $inEvent
	 * @return void
	 */
	function notify(cliApplicationEvent $inEvent);
}