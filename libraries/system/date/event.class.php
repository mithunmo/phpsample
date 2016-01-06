<?php
/**
 * systemDateEvent
 *
 * Stored in event.class.php
 *
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage system
 * @category systemDateEvent
 * @version $Rev: 722 $
 */


/**
 * systemDateEvent
 *
 * Holds information about an event on a particular day. This is a
 * wrapper for that information allowing it to be used with the
 * {@link systemDateCalendar calendar}. An event has a title, summary
 * and URI. The EventID should be unique and can be any valid string.
 * If it is not set, one is generated.
 *
 * Additional event information can be stored in the _EventData property.
 *
 * @package scorpio
 * @subpackage system
 * @category systemDateEvent
 */
class systemDateEvent {

	/**
	 * Stores $_Modified
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_Modified;

	/**
	 * Stores $_EventID
	 *
	 * @var string
	 * @access protected
	 */
	protected $_EventID;

	/**
	* Stores $_Title
	*
	* @var string
	* @access protected
	*/
	protected $_Title;

	/**
	 * Stores $_Summary
	 *
	 * @var string
	 * @access protected
	 */
	protected $_Summary;

	/**
	 * Stores $_EventURI
	 *
	 * @var string
	 * @access protected
	 */
	protected $_EventURI;

	/**
	 * Stores $_EventData an associative array of additional data
	 *
	 * @var array
	 * @access protected
	 */
	protected $_EventData;
	


	/**
	 * Creates a new event
	 *
	 * @param string $inEventID Unique reference for this event
	 * @param string $inTitle Event title
	 * @param string $inSummary Short summary of this event
	 * @param string $inEventURI Link to further event details
	 */
	function __construct($inEventID = null, $inTitle = null, $inSummary = null, $inEventURI = null) {
		$this->reset();
		if ( $inEventID !== null ) {
			$this->setEventID($inEventID);
		}
		if ( $inTitle !== null ) {
			$this->setTitle($inTitle);
		}
		if ( $inSummary !== null ) {
			$this->setSummary($inSummary);
		}
		if ( $inEventURI !== null ) {
			$this->setEventURI($inEventURI);
		}
	}

	/**
	 * Resets the object
	 *
	 * @return void
	 */
	function reset() {
		$this->_EventID = '';
		$this->_EventURI = '';
		$this->_Title = '';
		$this->_Summary = '';
		$this->_EventData = array();
		$this->_Modified = false;
	}



	/**
	 * Returns true if object modified
	 *
	 * @return boolean
	 */
	function isModified() {
		return $this->_Modified;
	}

	/**
	 * Sets $_Modified to $inStatus
	 *
	 * @param boolean $inStatus
	 * @return systemDateCalendar
	 */
	function setModified($inStatus = true) {
		$this->_Modified = $inStatus;
		return $this;
	}

	/**
	 * Returns the value of $_EventID
	 *
	 * @return string
	 */
	function getEventID() {
		if ( !$this->_EventID ) {
			$this->_EventID = md5(time());
		}
		return $this->_EventID;
	}

	/**
	 * Set value of $_EventID
	 *
	 * @param string $inEventID
	 * @return systemDateEvent
	 */
	function setEventID($inEventID) {
		if ( $inEventID !== $this->_EventID ) {
			$this->_EventID = $inEventID;
			$this->setModified();
		}
		return $this;
	}

	/**
	* Returns the value of $_Title
	*
	* @return string
	*/
	function getTitle() {
		return $this->_Title;
	}

	/**
	* Set value of $_Title
	*
	* @param string $inTitle
	* @return systemDateEvent
	*/
	function setTitle($inTitle) {
		if ( $inTitle !== $this->_Title ) {
			$this->_Title = $inTitle;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns the value of $_Summary
	 *
	 * @return string
	 */
	function getSummary() {
		return $this->_Summary;
	}

	/**
	 * Set value of $_Summary
	 *
	 * @param string $inSummary
	 * @return systemDateEvent
	 */
	function setSummary($inSummary) {
		if ( $inSummary !== $this->_Summary ) {
			$this->_Summary = $inSummary;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns the value of $_EventURI
	 *
	 * @return string
	 */
	function getEventURI() {
		return $this->_EventURI;
	}

	/**
	 * Set value of $_EventURI
	 *
	 * @param string $inEventURI
	 * @return systemDateEvent
	 */
	function setEventURI($inEventURI) {
		if ( $inEventURI !== $this->_EventURI ) {
			$this->_EventURI = $inEventURI;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns the array of additional event data
	 *
	 * @return array
	 */
	function getEventData() {
		return $this->_EventData;
}

	/**
	 * Sets an array of additional event data
	 *
	 * @param array $inData
	 * @return systemDateEvent
	 */
	function setEventData(array $inData = array()) {
		if ( $inData !== $this->_EventData ) {
			$this->_EventData = $inData;
			$this->setModified();
		}
		return $this;
	}
}