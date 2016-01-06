<?php
/**
 * mofilmCommsNewsletterMessageCca
 *
 * Stored in cca.class.php
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmCommsNewsletterMessageCca
 * @category mofilmCommsNewsletterMessageCca
 * 
 */
class mofilmCommsNewsletterMessageCca extends mofilmCommsNewsletterMessageBase implements mofilmCommsNewsletterMessageCompilerInterface {
	
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
	 * Replace the string in the body
	 *
	 * @param integer $inKey
	 * @param integer $inValue
	 * @param integer $inLinkHtml
	 * @return string 
	 */
	function replaceString($inKey, $inValue, $inLinkHtml) {
		return preg_replace("/".$inKey."/", $inValue, $inLinkHtml);
	}
	
	/**
	 * Compiles the message
	 * 
	 * @return string
	 */
	public function compile() {
		parent::compile();

		$msgParams = $this->getMessageParam();
		$search = array(
			"%movieID%" => $msgParams[1],
			"%movieTitle%" => $msgParams[2],
			"%emailAddr%" => $msgParams[3],
			"%brandName%" => $msgParams[4],
			"%eventName%" => $msgParams[5],
		);

		$this->setMessageHTML(
			str_replace(array_keys($search), array_values($search), $this->getMessageHTML())
		);

		return $this->getMessageHTML();
	}	
}