<?php
/**
 * utilityValidateUri.class.php
 * 
 * utilityValidateUri
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage utility
 * @category utilityValidateUri
 * @version $Rev: 706 $
 */


/**
 * utilityValidateUri
 * 
 * Validates that a variable is a URI and optionally requires the URI have
 * a path component.
 * 
 * <code>
 * $oValidator = new utilityValidateUri(
 *     array(
 *         utilityValidateUri:::PATH_REQUIRED => true,
 *     )
 * );
 * if ( !$oValidator->isValid($uri) ) {
 *     print_r($oValidator->getMessages());
 * }
 * </code>
 *
 * @package scorpio
 * @subpackage utility
 * @category utilityValidateUri
 */
class utilityValidateUri extends utilityValidateAbstract {
	
	const PATH_REQUIRED = 'uriPathRequired';
	const MESSAGE_NOT_AN_URI = 'notAnUri';
	const MESSAGE_INVALID_PORT = 'invalidPort';
	
	/**
	 * @see utilityValidateAbstract::initialise()
	 */
	function initialise() {
		$this->addMessageTemplates(self::MESSAGE_NOT_AN_URI, 'Value %value% is not a valid URI');
		$this->addMessageTemplates(self::MESSAGE_INVALID_PORT, 'Value %value% is not a valid port (range: 1-65535)');
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
		$oFilter = utilityInputFilter::filterValidateUrl();
		if ( $this->getOptions(self::PATH_REQUIRED) ) {
			$oFilter->setOptions(array('flags' => FILTER_FLAG_PATH_REQUIRED));
		}
		$filtered = $oFilter->doFilter($inValue);
		unset($oFilter);
		if ( !$filtered ) {
			$valid = false;
			$this->addMessage($this->_formatMessage(self::MESSAGE_NOT_AN_URI, $inValue));
		}
		
		$matches = array();
		if ( $valid && preg_match('/:(\d{1,5})\//', $inValue, $matches) ) {
			$oValidator = new utilityValidateNumber(
				array(
					self::MIN => 1,
					self::MAX => 65535
				)
			);
			if ( !$oValidator->isValid($matches[1]) ) {
				$valid = false;
				$this->addMessage($this->_formatMessage(self::MESSAGE_INVALID_PORT, $matches[1]));
			}
		}
		
		return $valid;
	}
}