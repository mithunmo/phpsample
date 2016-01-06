<?php
/**
 * utilityValidateObject.class.php
 * 
 * utilityValidateObject
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage utility
 * @category utilityValidateObject
 * @version $Rev: 706 $
 */


/**
 * utilityValidateObject
 * 
 * Validates that a variable is an object, and optionally is an instance of
 * a specific type, or implements a specific interface. Both a class and
 * interface can be specified if you require both a class and a specific
 * interface.
 *
 * <code>
 * // validates that the variable is an instanceof "someObject"
 * $oValidator = new utilityValidateObject(
 *     array(
 *         utilityValidateObject::OBJECT_TYPE => 'someObject'
 *     )
 * );
 * if ( !$oValidator->isValid($object) ) {
 *     print_r($oValidator->getMessages());
 * }
 * 
 * // validates that the variable implements the interface "someInterface"
 * $oValidator = new utilityValidateObject(
 *     array(
 *         utilityValidateObject::OBJECT_IMPLEMENTS => 'someInterface'
 *     )
 * );
 * if ( !$oValidator->isValid($object) ) {
 *     print_r($oValidator->getMessages());
 * }
 * </code>
 *
 * @package scorpio
 * @subpackage utility
 * @category utilityValidateObject
 */
class utilityValidateObject extends utilityValidateAbstract {
	
	const OBJECT_TYPE = 'type';
	const OBJECT_IMPLEMENTS = 'interface';
	
	const MESSAGE_NOT_AN_OBJECT = 'notAnObject';
	const MESSAGE_NOT_OF_TYPE = 'notOfType';
	const MESSAGE_NOT_INTERFACE = 'notInterface';
	
	/**
	 * @see utilityValidateAbstract::initialise()
	 */
	function initialise() {
		$this->addMessageTemplates(self::MESSAGE_NOT_AN_OBJECT, 'Value type "%value%" is not an object');
		$this->addMessageTemplates(self::MESSAGE_NOT_OF_TYPE, 'Object is not an instance of "%type%"');
		$this->addMessageTemplates(self::MESSAGE_NOT_INTERFACE, 'Object does not implement "%interface%"');
		
		$this->addMessageVariable(self::MESSAGE_NOT_OF_TYPE, $this->getOptions(self::OBJECT_TYPE));
		$this->addMessageVariable(self::MESSAGE_NOT_INTERFACE, $this->getOptions(self::OBJECT_IMPLEMENTS));
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
		if ( !is_object($inValue) ) {
			$valid = false;
			$this->addMessage($this->_formatMessage(self::MESSAGE_NOT_AN_OBJECT, $inValue));
		}
		if ( $valid && $this->getOptions(self::OBJECT_TYPE) ) {
			$class = $this->getOptions(self::OBJECT_TYPE);
			if ( !$inValue instanceof $class ) {
				$valid = false;
				$this->addMessage($this->_formatMessage(self::MESSAGE_NOT_OF_TYPE, $inValue));
			}
		}
		if ( $valid && $this->getOptions(self::OBJECT_IMPLEMENTS) ) {
			$interface = $this->getOptions(self::OBJECT_IMPLEMENTS);
			$oReflectionObj = new ReflectionObject($inValue);
			if ( !$oReflectionObj->implementsInterface($interface) ) {
				$valid = false;
				$this->addMessage($this->_formatMessage(self::MESSAGE_NOT_INTERFACE, $inValue));
			}
			unset($oReflectionObj);
		}
		return $valid;
	}
}