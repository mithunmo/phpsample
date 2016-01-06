<?php
/**
 * termsModel.class.php
 * 
 * termsModel class
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_mofilm.com
 * @subpackage controllers
 * @category termsModel
 * @version $Rev: 11 $
 */


/**
 * termsModel class
 * 
 * Provides the "terms" page
 * 
 * @package websites_mofilm.com
 * @subpackage controllers
 * @category termsModel
 */
class termsModel extends mvcModelBase {

	/**
	 * Stores $_EventID
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_EventID;

	/**
	 * Stores $_SourceID
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_SourceID;

	/**
	 * Stores $_TermsID
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_TermsID;

	/**
	 * Stores $_Event
	 *
	 * @var mofilmEvent
	 * @access protected
	 */
	protected $_Event;

	/**
	 * Stores $_Source
	 *
	 * @var mofilmSource
	 * @access protected
	 */
	protected $_Source;

	/**
	 * Stores $_Terms
	 *
	 * @var mofilmTerms
	 * @access protected
	 */
	protected $_Terms;



	/**
	 * @see mvcModelBase::__construct()
	 */
	function __construct() {
		parent::__construct();

		$this->reset();
	}

	/**
	 * Resets the object
	 *
	 * @return void
	 */
	function reset() {
		$this->_EventID = null;
		$this->_SourceID = null;
		$this->_TermsID = null;
		$this->_Event = null;
		$this->_Source = null;
		$this->_Terms = null;
		$this->setModified(false);
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
	 * @return termsModel
	 */
	function setEventID($inEventID) {
		if ( $inEventID !== $this->_EventID ) {
			$this->_EventID = $inEventID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns the value of $_SourceID
	 *
	 * @return integer
	 */
	function getSourceID() {
		return $this->_SourceID;
	}

	/**
	 * Set $_SourceID to $inSourceID
	 *
	 * @param integer $inSourceID
	 * @return termsModel
	 */
	function setSourceID($inSourceID) {
		if ( $inSourceID !== $this->_SourceID ) {
			$this->_SourceID = $inSourceID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns the value of $_TermsID
	 *
	 * @return integer
	 */
	function getTermsID() {
		return $this->_TermsID;
	}

	/**
	 * Set $_TermsID to $inTermsID
	 *
	 * @param integer $inTermsID
	 * @return termsModel
	 */
	function setTermsID($inTermsID) {
		if ( $inTermsID !== $this->_TermsID ) {
			$this->_TermsID = $inTermsID;
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
	 * @return termsModel
	 */
	function setEvent($inEvent) {
		if ( $inEvent !== $this->_Event ) {
			$this->_Event = $inEvent;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns the value of $_Source
	 *
	 * @return mofilmSource
	 */
	function getSource() {
		if ( !$this->_Source instanceof mofilmSource ) {
			$this->_Source = mofilmSource::getInstance($this->getSourceID());
		}
		return $this->_Source;
	}

	/**
	 * Set $_Source to $inSource
	 *
	 * @param mofilmSource $inSource
	 * @return termsModel
	 */
	function setSource($inSource) {
		if ( $inSource !== $this->_Source ) {
			$this->_Source = $inSource;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns the terms object instance, loading it if not set
	 *
	 * Load order is determined by the class properties. The order is:
	 *
	 * * terms matching _TermsID
	 * * terms from the source at _SourceID
	 * * terms from the event at _EventID
	 * * an empty terms object 
	 *
	 * @return mofilmTerms
	 */
	function getTerms() {
		if ( !$this->_Terms instanceof mofilmTerms ) {
			if ( $this->getTermsID() ) {
				$this->_Terms = mofilmTerms::getInstance($this->getTermsID());
			} else {
				if ( $this->getSourceID() ) {
					$this->_Terms = $this->getSource()->getTerms();
				} elseif ( $this->getEventID() ) {
					$this->_Terms = $this->getEvent()->getTerms();
				} else {
					$this->_Terms = new mofilmTerms();
				}
			}
		}
		return $this->_Terms;
	}

	/**
	 * Set $_Terms to $inTerms
	 *
	 * @param mofilmTerms $inTerms
	 * @return termsModel
	 */
	function setTerms($inTerms) {
		if ( $inTerms !== $this->_Terms ) {
			$this->_Terms = $inTerms;
			$this->setModified();
		}
		return $this;
	}
}