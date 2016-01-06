<?php
/**
 * mofilmCommsNewsletterMessageBase
 *
 * Stored in base.class.php
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmCommsNewsletterMessageBase
 * @category mofilmCommsNewsletterMessageBase
 * 
 */
abstract class mofilmCommsNewsletterMessageBase implements mofilmCommsNewsletterMessageCompilerInterface {
	
	/**
	 * Stores $_MofilmCommsNewsletterData
	 * 
	 * @var mofilmCommsNewsletterdata 
	 * @access protected
	 */
	protected $_MofilmCommsNewsletterData;
	
	/**
	 * Stores $_MofilmCommsNewsletter
	 * 
	 * @var mofilmCommsNewsletter
	 * @access protected
	 */
	protected $_MofilmCommsNewsletter;
	
	/**
	 * Stores $_UserID
	 * 
	 * @var integer
	 * @access protected 
	 */
	protected $_UserID;
	
	/**
	 * Stores $_TransactionID
	 *  
	 * @var integer 
	 * @access protected
	 */
	protected $_TransactionID;
	
	/**
	 * Stores $_MessageParam
	 * 
	 * @var array
	 * @access protected 
	 */
    protected $_MessageParam;
	
	/**
	 * Stores $_MessageHTML
	 * 
	 * @var string 
	 * @access protected
	 */
	protected $_MessageHTML;
	
	
	
	/**
	 * Returns a new instance
	 * 
	 * @param mofilmCommsNewsletterdata $inMofilmCommsNewsletterData
	 */
	function __construct($inMofilmCommsNewsletterData) {
		$this->setNewsletterData($inMofilmCommsNewsletterData);
		$this->setNewsletter(mofilmCommsNewsletter::getInstance($this->getNewsletterData()->getNewsletterID()));
	}
	
	/**
	 * Object destructor, used to remove internal object instances
	 * 
	 * @return void
	 */
	function __destruct() {
		$this->_MessageParam = null;
		$this->_UserID = null;
		$this->_MofilmCommsNewsletterData = null;
		$this->_MofilmCommsNewsletter = null;
		$this->_TransactionID = null;
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
	 * @return mofilmCommsNewsletterMessageBase
	 */
	function setNewsletterData($inNewsletterData) {
		if ( $inNewsletterData !== $this->_MofilmCommsNewsletterData ) {
			$this->_MofilmCommsNewsletterData = $inNewsletterData;
		}
		return $this;
	}

	/**
	 * Gets the compiled HTML message
	 *
	 * @return string
	 */
	function getMessageHTML() {
		return $this->_MessageHTML;
	}
	
	/**
	 * Sets the compiled HTML message
	 *  
	 * @param string $inMessageHTML
	 * @return mofilmCommsNewsletterMessageBase 
	 */
	function setMessageHTML($inMessageHTML) {
		if ( $inMessageHTML !== $this->_MessageHTML ) {
			$this->_MessageHTML = $inMessageHTML;
		}
		return $this;
	}	
	
	/**
	 * Gets the MessageParam if any
	 * 
	 * @return array 
	 */
	function getMessageParam() {
		return $this->_MessageParam;
	}
	
	/**
	 * Sets the messageParam 
	 * 
	 * @param array $inMessageParam
	 * @return mofilmCommsNewsletterMessageBase 
	 */
	function setMessageParam($inMessageParam) {
		if ( $inMessageParam !== $this->_MessageParam ) {
			$this->_MessageParam = $inMessageParam;
		}
		return $this;
	}

	/**
	 * Creates the newsletterHistory object and returns the ID
	 * 
	 * @return integer 
	 */
	function createTransactionID() {
		$oNewsletterHistory = new mofilmCommsNewsletterhistory();
		$oNewsletterHistory->setNewsletterID($this->getNewsletterData()->getNewsletterID());
		$oNewsletterHistory->setStatus(0);
		$oNewsletterHistory->setUserID($this->getUserID());
		$oNewsletterHistory->setTransactionID($this->getTransactionID());
		$oNewsletterHistory->save();
		return $oNewsletterHistory->getID();
	}
	
	/**
	 * Gets the mofilmCommsNewsletter object
	 *
	 * @return mofilmCommsNewsletter
	 */
	function getNewsletter() {
		return $this->_MofilmCommsNewsletter;
	}

	/**
	 * Sets the mofilmCommsNewsletter object
	 *
	 * @param mofilmCommsNewsletter $inNewsletter
	 * @return mofilmCommsNewsletterMessageBase
	 */
	function setNewsletter($inNewsletter) {
		if ( $inNewsletter !== $this->_MofilmCommsNewsletter ) {
			$this->_MofilmCommsNewsletter = $inNewsletter;
		}
		return $this;
	}
	
	/**
	 * Sets the userID for the current message
	 * 
	 * @param integer $inUserID 
	 */
	function setUserID($inUserID) {
		$this->_UserID = $inUserID;
	}
	
	/**
	 * Gets the userID for the current message
	 * 
	 * @return integer 
	 */
	function getUserID() {
		return $this->_UserID;
	}
	
	/**
	 * Sets the transactionID for the current message
	 * 
	 * @param integer $inTransactionID 
	 */
	function setTransactionID($inTransactionID) {
		$this->_TransactionID = $inTransactionID;
	}
	
	/**
	 * Gets the transactionID for the current message
	 * 
	 * @return integer 
	 */
	function getTransactionID() {
		return $this->_TransactionID;
	}

	
	/**
	 * Compiles the message 
	 * 
	 * @return void
	 */
	function compile() {
		$arrLinks = $this->getLinks();
		$trackUrl = $this->getTrackingLink();
		$preUrl = $this->preTrackLink($trackUrl);
		$postUrl = $this->postTrackLink($trackUrl);
		$historyID = $this->createTransactionID();

		$pattern = "/src=\".*track.png\"/";
		$newurl = "src=" . "\"" . $preUrl . "$historyID" . $postUrl . "\"";
		$linkHtml = preg_replace($pattern, $newurl, $this->getNewsletter()->getMessageBody());

		$trackImageUrl = system::getConfig()->getParam("mofilm", "myMofilmUri");

		foreach ( $arrLinks as $value ) {
			$patternLinkItem = "/".addcslashes($value, '/')."\"/";
			$newurlLink =
				"$trackImageUrl/track/nlLink?nlLink=".urlencode($value).
				"&nlId=".$this->getNewsletterData()->getNewsletterID().
				"&userId=".$this->getUserID().
				"&nlHistoryId=".$historyID.
				"\"";

			$linkHtml = preg_replace($patternLinkItem, $newurlLink, $linkHtml);
		}
		$this->setMessageHTML($linkHtml);

		unset($historyID,$arrLinks,$trackUrl,$preUrl,$postUrl,$newurl,$newhtml,$linkHtml);
	}
		
	/**
	 * Gets all the links present in the body
	 * 
	 * @return array 
	 */
	function getLinks() {
		$patternLink = '/< *a[^>]*href="(http.*)"/iU';
		preg_match_all($patternLink, $this->getNewsletter()->getMessageBody(), $matches);

		return array_unique($matches[1]);
	}
	
	/**
	 * Gets the tracking link in the Body
	 * 
	 * @return string 
	 */
	function getTrackingLink() {
		$pattern = '/< *img[^>]*src="(.*track.png)/i';
		preg_match($pattern, $this->getNewsletter()->getMessageBody(), $matches);

		return $matches[1];
	}
	
	/**
	 * Splits the track URL for inserting unique identifiers
	 * 
	 * @param string $inUrl
	 * @return string 
	 */
	function preTrackLink($inUrl) {
		return substr($inUrl, 0, (strlen($inUrl)-4));
	}
	
	/**
	 * Splits the track URL for inserting unique identifiers
	 * 
	 * @param string $inUrl
	 * @return string 
	 */
	function postTrackLink($inUrl) {
		return substr($inUrl, -4, 4);
	}
}