<?php
/**
 * utilityValidateIpAddress.class.php
 * 
 * utilityValidateIpAddress
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage utility
 * @category utilityValidateIpAddress
 * @version $Rev: 706 $
 */


/**
 * utilityValidateIpAddress
 * 
 * Validates that the variable is a valid IP address. Supports both IPv4
 * and IPv6 validation. This validator uses the PHP filter_var::VALIDATE_IP
 * to perform validation.
 * 
 * <code>
 * // only validate an IPv4 address
 * $oValidator = new utilityValidateIpAddress(
 *     array(
 *         utilityValidateIpAddress::ONLY_IPV4 => true
 *     )
 * );
 * if ( !$oValidator->isValid($ip) ) {
 *     print_r($oValidator->getMessages());
 * }
 * 
 * // validate any IPv4/6 IP but exclude private and reserved IPs
 * $oValidator = new utilityValidateIpAddress(
 *     array(
 *         utilityValidateIpAddress::NO_PRIVATE_IPS => true
 *         utilityValidateIpAddress::NO_RESERVED_IPS => true 
 *     )
 * );
 * if ( !$oValidator->isValid($time) ) {
 *     print_r($oValidator->getMessages());
 * }
 * </code>
 *
 * @package scorpio
 * @subpackage utility
 * @category utilityValidateIpAddress
 */
class utilityValidateIpAddress extends utilityValidateAbstract {
	
	const ONLY_IPV4 = 'onlyIpV4';
	const ONLY_IPV6 = 'onlyIpV6';
	const NO_PRIVATE_IPS = 'noPrivateIps';
	const NO_RESERVED_IPS = 'noReservedIps';
	const MESSAGE_NOT_AN_IPADDRESS = 'notAnIpAddress';
	
	/**
	 * @see utilityValidateAbstract::initialise()
	 */
	function initialise() {
		$this->addMessageTemplates(self::MESSAGE_NOT_AN_IPADDRESS, 'Value %value% is not a valid IP address');
	}

	/**
	 * @see utilityValidateInterface::isValid()
	 *
	 * @param mixed $inValue
	 * @return boolean
	 */
	function isValid($inValue) {
		$this->setValue($inValue);
		
		$valid = true;
		$options = array('flags' => 0);
		
		$oFilter = utilityInputFilter::filterValidateIp();
		if ( $this->getOptions(self::ONLY_IPV4) && !$this->getOptions(self::ONLY_IPV6) ) {
			$options['flags'] = FILTER_FLAG_IPV4;
		}
		if ( !$this->getOptions(self::ONLY_IPV4) && $this->getOptions(self::ONLY_IPV6) ) {
			$options['flags'] = FILTER_FLAG_IPV6;
		}
		if ( $this->getOptions(self::NO_PRIVATE_IPS) ) {
			$options['flags'] |= FILTER_FLAG_NO_PRIV_RANGE;
		}
		if ( $this->getOptions(self::NO_RESERVED_IPS) ) {
			$options['flags'] |= FILTER_FLAG_NO_RES_RANGE;
		}
		$oFilter->setOptions($options);
		$filtered = $oFilter->doFilter($inValue);
		if ( !$filtered ) {
			$valid = false;
			$this->addMessage($this->_formatMessage(self::MESSAGE_NOT_AN_IPADDRESS, $inValue));
		}
		unset($oFilter);
		
		return $valid;
	}
}