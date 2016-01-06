<?php
/**
 * cliApplicationListeners Class
 * 
 * Stored in listeners.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage cli
 * @category cliApplicationListeners
 * @version $Rev: 707 $
 */


/**
 * cliApplicationListeners class
 * 
 * Holds a collection of listeners for the current application, allowing
 * event information to be passed to the listeners, basically an 
 * implementation of the observer pattern.
 * 
 * <code>
 * // create listeners set
 * $oListeners = new cliApplicationListeners();
 * $oListeners->attachListener(new cliApplicationListenerLog());
 * 
 * // send a notice
 * $oListeners->notify(new cliApplicationEvent());
 * </code>
 * 
 * @see cliApplicationListener
 * @see cliApplicationEvent
 * @package scorpio
 * @subpackage cli
 * @category cliApplicationListeners
 */
class cliApplicationListeners extends baseSet {
	
	/**
	 * Creates a new cliApplicationListeners instance
	 *
	 * @return cliApplicationListeners
	 */
	function __construct() {
		$this->reset();
	}
	
	/**
	 * Resets object
	 *
	 * @return void
	 */
	function reset() {
		parent::_resetSet();
	}
	
	/**
	 * Notify listeners that something occured and pass down the cliRequest
	 *
	 * @param cliApplicationEvent $inEvent
	 * @return void
	 */
	function notify(cliApplicationEvent $inEvent) {
		if ( $this->getCount() > 0 ) {
			foreach ( $this as $oListener ) {
				$oListener->notify($inEvent);
			}
		}
	}
	
	/**
	 * Attach a listener that should be notified when an event is dispatched
	 *
	 * @param cliApplicationListener $inListener
	 * @return cliApplicationListeners
	 */
	function attachListener(cliApplicationListener $inListener) {
		return $this->_setValue($inListener);
	}
	
	/**
	 * Detaches the listener
	 *
	 * @param cliApplicationListener $inListener
	 * @return cliApplicationListeners
	 */
	function detachListener(cliApplicationListener $inListener) {
		return $this->_removeItemWithValue($inListener);
	}
	
	/**
	 * Returns the number of listeners registered
	 *
	 * @return integer
	 */
	function getCount() {
		return $this->_itemCount();
	}
}