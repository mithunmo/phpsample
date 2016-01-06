<?php
/**
 * mofilmCommsNewsletterMessageRegular
 *
 * Stored in regular.class.php
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmCommsNewsletterMessageRegular
 * @category mofilmCommsNewsletterMessageRegular
 * 
 */
class mofilmCommsNewsletterMessageRegular extends mofilmCommsNewsletterMessageBase implements mofilmCommsNewsletterMessageCompilerInterface {
	
	/**
	 * Returns a new instance of mofilmCommsNewsletterMessageCca
	 * 
	 * @param mofilmCommsNewsletterdata $inMofilmCommsNewsletterData
	 */	
	function __construct($inMofilmCommsNewsletterData) {
		parent::__construct($inMofilmCommsNewsletterData);
	}

	/**
	 * Object destructor, used to remove internal object instances
	 * 
	 * @return void
	 */	
	function __destruct() {
		parent::__destruct();
	}
	
	/**
	 * Compiles the mesage .
	 * 
	 * @return string
	 */
	public function compile() {
		parent::compile();
		
		return $this->getMessageHTML();
	}	
}