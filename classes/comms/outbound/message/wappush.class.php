<?php
/**
 * commsOutboundMessageWapPush
 *
 * Stored in commsOutboundMessageWapPush.class.php
 *
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package comms
 * @subpackage outbound
 * @category commsOutboundMessageWapPush
 * @version $Rev: 10 $
 */


/**
 * commsOutboundMessageWapPush Class
 *
 * Custom class for WapPush type messages.
 *
 * @package comms
 * @subpackage outbound
 * @category commsOutboundMessageWapPush
 */
class commsOutboundMessageWapPush extends commsOutboundMessageSms {
	
	/**
	 * Stores $_Title
	 *
	 * @var string
	 * @access protected
	 */
	protected $_Title = '';
	
	/**
	 * Stores $_WapLink
	 *
	 * @var string
	 * @access protected
	 */
	protected $_WapLink = '';
	
	/**
	 * Stores $_Lifetime
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_Lifetime = 72;
	
	
	
	/**
	 * Return the message body, compiles up the body if not set already
	 *
	 * @return string
	 */
	function getMessageBody() {
		if ( !parent::getMessageBody() ) {
			$this->setMessageBody($this->getTitle().'|'.$this->getTarget().'|'.$this->getLifetime());
		}
		return parent::getMessageBody();
	}

	/**
	 * Override setting the message body so WAP Push components can be stripped out if present
	 *
	 * @see commsOutboundMessage::setMessageBody()
	 * @param string $inMessageBody
	 * @return commsOutboundMessageWapPush
	 */
	function setMessageBody($inMessageBody) {
		if ( stripos($inMessageBody, '|') !== false ) {
			list($title, $target, $lifetime) = explode('|', $inMessageBody);
			$this->setTitle($title);
			$this->setWapLink($target);
			$this->setLifetime($lifetime);
		}
		return parent::setMessageBody($inMessageBody);
	}
	
	/**
	 * Override charge, WAP push messages cannot be billed
	 * 
	 * @param float $inCharge
	 * @return commsOutboundMessageWapPush
	 */
	function setCharge($inCharge) {
		return $this;
	}
	
	/**
	 * Returns $_Title
	 *
	 * @return string
	 */
	function getTitle() {
		return $this->_Title;
	}
	
	/**
	 * Set $_Title to $inTitle
	 *
	 * @param string $inTitle
	 * @return commsOutboundMessageWapPush
	 */
	function setTitle($inTitle) {
		if ( $inTitle !== $this->_Title ) {
			$this->_Title = $inTitle;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Returns $_WapLink
	 *
	 * @return string
	 */
	function getWapLink() {
		return $this->_WapLink;
	}
	
	/**
	 * Set $_WapLink to $inWapLink
	 *
	 * @param string $inWapLink
	 * @return commsOutboundMessageWapPush
	 */
	function setWapLink($inWapLink) {
		if ( $inWapLink !== $this->_WapLink ) {
			$this->_WapLink = $inWapLink;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_Lifetime
	 *
	 * @return integer
	 */
	function getLifetime() {
		return $this->_Lifetime;
	}
	
	/**
	 * Set $_Lifetime to $inLifetime
	 *
	 * @param integer $inLifetime
	 * @return commsOutboundMessageWapPush
	 */
	function setLifetime($inLifetime) {
		if ( $inLifetime !== $this->_Lifetime ) {
			$this->_Lifetime = $inLifetime;
			$this->setModified();
		}
		return $this;
	}
}