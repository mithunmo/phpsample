<?php
/**
 * mofilmCommsNewsletterFilterlist
 *
 * Stored in filterlist.class.php
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmCommsNewsletterFilterlist
 * @category mofilmCommsNewsletterFilterlist
 * 
 */
class mofilmCommsNewsletterFilterlist implements mofilmCommsNewsletterFilter {
		
	/**
	 * Stores $_MofilmCommsNewsletterData;
	 * 
	 * @var object mofilmCommsNewsletterdata
	 * @access protected
	 */
	protected $_MofilmCommsNewsletterData;
	
	/**
	 *
	 * @param type $inNewsletterData 
	 */
	function __construct($inNewsletterData) {
		$this->_MofilmCommsNewsletterData = $inNewsletterData;
	}



	/**
	 * Gets the mofilmCommsNewsletterdata object
	 *
	 * @return mofilmCommsNewsletterdata
	 */
	function getNewsletterData() {
		return $this->_MofilmCommsNewsletterData;
	}

	/**
	 * Sets the mofilmCommsNewsletterdata object
	 *
	 * @param mofilmCommsNewsletterdata $inNewsletterData
	 * @return mofilmCommsNewsletterlist
	 */
	function setNewsletterData($inNewsletterData) {
		if ( $inNewsletterData !== $this->_MofilmCommsNewsletterData ) {
			$this->_MofilmCommsNewsletterData = $inNewsletterData;
		}
		return $this;
	}
	
	/**
	 * Applys the desired filter 
	 *
	 * @return array
	 */
	function apply() {
		
	}
}