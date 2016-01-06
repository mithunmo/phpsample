<?php
/**
 * mofilmCommsNewsletterlist
 *
 * Stored in mofilmCommsNewsletterlist.class.php
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmCommsNewsletterlist
 * @category mofilmCommsNewsletterlist
 * @version $Rev: 73 $
 */
class mofilmCommsNewsletterlist {

	/**
	 * Stores $_ClassName
	 *
	 * @var string
	 * @access protected
	 */
	protected $_ClassName;
	
	/**
	 * Stores $_MofilmCommsNewsletterdata
	 * 
	 * @var object mofilmCommsNewsletterdata
	 * @access protected
	 */
	protected $_MofilmCommsNewsletterData;
	
	
	/**
	 * Gets the mofilmCommsNewsletterdata object
	 *
	 * @return object
	 */
	function getNewsletterData() {
		return $this->_MofilmCommsNewsletterData;
	}

	/**
	 * Sets the mofilmCommsNewsletterdata object
	 *
	 * @param object mofilmCommsNewsletterdata
	 * @return mofilmCommsNewsletterdata
	 */
	function setNewsletterData($inNewsletterData) {
		if ( $inNewsletterData !== $this->_MofilmCommsNewsletterData ) {
			$this->_MofilmCommsNewsletterData = $inNewsletterData;
		}
		return $this;
	}
	

	/**
	 * Gets the className
	 *
	 * @return string
	 */
	function getClassName() {
		return $this->_ClassName;
	}

	/**
	 * Sets the class Name
	 *
	 * @param string $inClassName
	 * @return mofilmCommsNewsletterlist
	 */
	function setClassName($inClassName) {
		if ( $inClassName !== $this->_ClassName ) {
			$this->_ClassName = $inClassName;
		}
		return $this;
	}
	
	/**
	 * Gets the filter object class based on the params
	 *
	 * @return Object mofilmCommsNewsletterFilter
	 */
	function getFilter() {
		$oClassName = mofilmCommsNewsletterFilterclass::getInstance($this->getClassName())->getClassname();
		return new $oClassName($this->getNewsletterData());
	}

}