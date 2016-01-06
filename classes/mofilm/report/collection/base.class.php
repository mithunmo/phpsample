<?php
/**
 * mofilmReportCollectionBase
 * 
 * Stored in mofilmReportCollectionBase.class.php
 * 
 * @author Dave Redfern
 * @copyright MOFILM Ltd (c) 2009-2010
 * @package mofilm
 * @subpackage report
 * @category mofilmReportCollectionBase
 * @version $Rev: 10 $
 */


/**
 * mofilmReportCollectionBase
 * 
 * A Mofilm specific report collection that can be extended to provide
 * shared logic to summary and grouped reports.
 *
 * @package mofilm
 * @subpackage report
 * @category mofilmReportCollectionBase
 */
abstract class mofilmReportCollectionBase extends reportCollectionBase {
	
	const OPTION_EVENT_ID = 'report.event.id';
	
	/**
	 * Holds an instance of mofilmEvent
	 * 
	 * @var mofilmEvent
	 * @access protected
	 */
	protected $_Event;
	
	

	/**
	 * Returns the eventID
	 *
	 * @return integer
	 */
	function getEventID() {
		return $this->getOption(self::OPTION_EVENT_ID, 0);
	}
	
	/**
	 * Returns the mofilmEvent, loading it if not already loaded
	 * 
	 * @return mofilmEvent
	 */
	function getEvent() {
		if ( !$this->_Event instanceof mofilmEvent ) {
			$this->_Event = mofilmEvent::getInstance($this->getEventID());
		}
		return $this->_Event;
	}
}