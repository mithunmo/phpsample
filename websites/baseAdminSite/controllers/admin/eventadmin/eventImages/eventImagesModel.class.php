<?php
/**
 * eventImagesModel.class.php
 * 
 * eventImagesModel class
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category eventImagesModel
 * @version $Rev: 623 $
 */


/**
 * eventImagesModel class
 * 
 * Provides the "eventImages" page
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category eventImagesModel
 */
class eventImagesModel extends mvcModelBase {

	/**
	 * Stores $_EventID
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_EventID;

	/**
	 * Stores $_Event
	 *
	 * @var mofilmEvent
	 * @access protected
	 */
	protected $_Event;



	/**
	 * @see mvcModelBase::__construct()
	 */
	function __construct() {
		parent::__construct();

		$this->_EventID = null;
		$this->_Event = null;
	}

	/**
	 * Returns the value of $_EventID
	 *
	 * @return integer
	 */
	function getEventID() {
		return $this->_EventID;
	}

	/**
	 * Set $_EventID to $inEventID
	 *
	 * @param integer $inEventID
	 * @return eventImagesModel
	 */
	function setEventID($inEventID) {
		if ( $inEventID !== $this->_EventID ) {
			$this->_EventID = $inEventID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns the value of $_Event
	 *
	 * @return mofilmEvent
	 */
	function getEvent() {
		if ( !$this->_Event instanceof mofilmEvent ) {
			$this->_Event = mofilmEvent::getInstance($this->getEventID());
		}
		return $this->_Event;
	}

	/**
	 * Set $_Event to $inEvent
	 *
	 * @param mofilmEvent $inEvent
	 * @return eventImagesModel
	 */
	function setEvent($inEvent) {
		if ( $inEvent !== $this->_Event ) {
			$this->_Event = $inEvent;
			$this->setModified();
		}
		return $this;
	}
}