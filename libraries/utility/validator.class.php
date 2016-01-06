<?php
/**
 * utilityValidator.class.php
 * 
 * utilityValidator
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage utility
 * @category utilityValidator
 * @version $Rev: 706 $
 */


/**
 * utilityValidator
 * 
 * Aggregates various validators and allows an array of data
 * to be validated in one go. This is ideal for handling form
 * data or data that is in an associative array of key => value
 * pairs.
 * 
 * The data to be validated needs to have a string key that identifies
 * it, and then a set of rules can be added based on that key. For
 * example: username might have validators for string, length, regex
 * etc.
 *
 * If validation fails, an array of messages is stored internally
 * detailing what went wrong during validation. These can be used to
 * correct the data.
 * 
 * utilityValidator supports multiple namespaces that will be scanned
 * for validators. The default is utilityValidate. The leading letter
 * is automatically capitalised.
 * 
 * <code>
 * $data = array(
 * 	'number1' => 123,
 * 	'number2' => 34738,
 * 	'number3' => 123.1121,
 * 	'number4' => 'a23',
 * );
 * 
 * $rules = array(
 * 	'number1' => array(
 * 		'number' => array()
 * 	),
 * 	'number2' => array(
 * 		'number' => array('min' => 100, 'max' => 1000)
 *	),
 * 	'number3' => array(
 * 		'number' => array('min' => 100, 'max' => 125.0000)
 * 	),
 * 	'number4' => array(
 * 		'number' => array()
 * 	),
 * );
 * 
 * $oValidator = new utilityValidator($data, $rules);
 * $oValidator->isValid();
 * print_r($oValidator);
 * </code>
 * 
 * All validators support translation of the error messages. To enable
 * translations, configure an instance of {@link translateManager} and
 * pass this in during instantiation. Now all messages can be translated
 * automatically - so long as the language resources contains strings.
 * 
 * <code>
 * // using translateManager
 * $translateManager = translateManager::getInstance(
 * 	translateManager::ADAPTOR_CSV, '/path/to/lang.csv', 'en_GB', array()
 * );
 * 
 * $oValidator = new utilityValidator($data, $rules, $translateManager);
 * if ( !$oValidator->isValid() ) {
 *     print_r($oValidator->getMessages());
 * }
 * </code>
 * 
 * @package scorpio
 * @subpackage utility
 * @category utilityValidator
 */
class utilityValidator {
	
	/**
	 * Stores $_Modified
	 * 
	 * @var boolean
	 * @access protected
	 */
	protected $_Modified = false;
	
	/**
	 * Stores $_Data
	 *
	 * @var array
	 * @access protected
	 */
	protected $_Data;
	
	/**
	 * Array of rules to apply to the data
	 *
	 * @var array
	 * @access protected
	 */
	protected $_Rules;
	
	/**
	 * Stores $_Namespaces
	 *
	 * @var array
	 * @access protected
	 */
	protected $_Namespaces;
	
	/**
	 * Stores $_Messages
	 *
	 * @var array
	 * @access protected
	 */
	protected $_Messages;
	
	/**
	 * Stores an instance of the translation manager
	 *
	 * @var translateManager
	 * @access protected
	 */
	protected $_TranslateManager;
	
	
	
	/**
	 * Creates a new validator object
	 *
	 * @param array $inData
	 * @param array $inRules Array of utilityValidatorInterface objects
	 * @param translateManager $inTranslateManager (optional) translate manager instance
	 * @return utilityValidator
	 */
	function __construct(array $inData = array(), array $inRules = array(), $inTranslateManager = null) {
		$this->reset();
		$this->setTranslationManager($inTranslateManager);
		$this->setData($inData);
		$this->setRules($inRules);
	}
	
	/**
	 * Returns true only if ALL data passes validation, otherwise returns false
	 *
	 * @param array $inData Optional array of data to validate if not already set
	 * @return boolean
	 */
	function isValid($inData = null) {
		if ( $inData !== null && is_array($inData) ) {
			$this->setData($inData);
		}
		
		foreach ( $this->_Data as $var => $value ) {
			$validators = $this->getRules($var);
			foreach ( $validators as $oValidator ) {
				if ( $this->getTranslationManager() instanceof translateManager && !$oValidator->getTranslationManager() ) {
					$oValidator->setTranslationManager($this->getTranslationManager());
				}
				
				if ( $oValidator->isValid($value) ) {
					continue;
				} else {
					$this->addMessage($var, $oValidator->getMessages());
					if ( $oValidator->getOptions(utilityValidateAbstract::BREAK_ON_FAIL) ) {
						break;
					}
				}
			}
		}
		
		return (count($this->getMessages()) == 0);
	}
	
	/**
	 * Reset the object
	 * 
	 * @return void
	 */
	function reset() {
		$this->_Data = array();
		$this->_Rules = array();
		$this->_Namespaces = array('utilityValidate');
		$this->_Messages = array();
		$this->_TranslateManager = null;
		$this->_Modified = false;
	}
	
	
	
	/**
	 * Returns true if object has been modified
	 * 
	 * @return boolean
	 */
	function isModified() {
		return $this->_Modified;
	}
	
	/**
	 * Set the status of the object if it has been changed
	 * 
	 * @param boolean $status
	 * @return utilityValidator
	 */
	function setModified($status = true) {
		$this->_Modified = $status;
		return $this;
	}
	
	/**
	 * Returns the array of data being validated
	 *
	 * @return array
	 */
	function getData() {
		return $this->_Data;
	}
	
	/**
	 * Set the data to be validated
	 * 
	 * Data must be an associative array of varName => value with
	 * matching rules using varName.
	 *
	 * @param array $inData
	 * @return utilityValidator
	 */
	function setData(array $inData = array()) {
		if ( $inData !== $this->_Data ) {
			$this->_Data = $inData;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns the array of class prefixes to use when searching for
	 * validation classes.
	 *
	 * @return array
	 */
	function getNamespaces() {
		return $this->_Namespaces;
	}
	
	/**
	 * Adds a new namespace prefix for validators
	 *
	 * @param string $inNamespace
	 * @return utilityValidator
	 */
	function addNamespace($inNamespace) {
		if ( !in_array($inNamespace, $this->_Namespaces) ) {
			$this->_Namespaces[] = $inNamespace;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Set the array of class prefixes to use when searching for validate classes.
	 *
	 * @param array $inNamespaces
	 * @return utilityValidator
	 */
	function setNamespaces($inNamespaces) {
		if ( $inNamespaces !== $this->_Namespaces ) {
			$this->_Namespaces = $inNamespaces;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns the array of messages from validation
	 *
	 * @return array
	 */
	function getMessages() {
		return $this->_Messages;
	}
	
	/**
	 * Adds a message to the internal stack for the set $inVarName
	 *
	 * @param string $inVarName
	 * @param string $inMessage
	 * @return utilityValidator
	 */
	function addMessage($inVarName, $inMessage) {
		if ( !$inVarName ) {
			$inVarName = '0';
		}
		if ( is_array($inMessage) ) {
			foreach ( $inMessage as $message ) {
				$this->_Messages[$inVarName][] = $message;
			}
		} else {
			$this->_Messages[$inVarName][] = $inMessage;
		}
		return $this;
	}
	
	/**
	 * Sets an array of messages, overwriting any existing values
	 *
	 * @param array $inMessages
	 * @return utilityValidator
	 */
	function setMessages($inMessages) {
		if ( $inMessages !== $this->_Messages ) {
			$this->_Messages = $inMessages;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Returns the rules for $inVarName or if null, all rules
	 *
	 * @param string $inVarName (optional)
	 * @return array
	 */
	function getRules($inVarName = null) {
		if ( $inVarName === null ) {
			return $this->_Rules;
		}
		
		if ( isset($this->_Rules[$inVarName]) ) {
			return $this->_Rules[$inVarName];
		} else {
			return array();
		}
	}
	
	/**
	 * Sets the validation rules for the data
	 * 
	 * The rules should be an associative array with the following format:
	 * varname => validator => array(options for validator) e.g.:
	 * 
	 * <code>
	 * $rules = array();
	 * $rules['MyVar']['Number'] = array('min' => 0, 'max' => 3);
	 * </code>
	 * 
	 * Alternatively, for single validation or to use the default options
	 * you can specify rules using the following formats:
	 * 
	 * <code>
	 * $rules = array();
	 * $rules['MyVar'] = array('string','notEmpty','date');
	 * $rules['MyVar2'] = 'string';
	 * </code>
	 * 
	 * @param array $inRules
	 * @return utilityValidator
	 */
	function setRules(array $inRules = array()) {
		if ( count($inRules) > 0 ) {
			foreach ( $inRules as $varName => $validatorData ) {
				if ( is_string($validatorData) ) {
					$this->addRule($varName, $validatorData);
				} elseif ( is_array($validatorData) && count($validatorData) > 0 ) {
					foreach ( $validatorData as $validator => $options ) {
						if ( is_numeric($validator) ) {
							$this->addRule($varName, $options);
						} else {
							$this->addRule($varName, $validator, (array) $options);
						}
					}
				}
			}
		}
		return $this;
	}
	
	/**
	 * Adds a rule that will validate $inVarName against $inValidator
	 * 
	 * $inValidator is the name of the validator minus any prefix. For example
	 * to validate a number use 'number'. The leading letter does not need to
	 * be capitalised.
	 *
	 * @param string $inVarName
	 * @param string $inValidator
	 * @param array $inOptions Array of options for the validator
	 * @return utilityValidator
	 */
	function addRule($inVarName, $inValidator, array $inOptions = array()) {
		try {
			$loaded = false;
			foreach ( $this->getNamespaces() as $namespace ) {
				$class = $namespace.ucfirst($inValidator);
				if ( class_exists($class) ) {
					$this->_Rules[$inVarName][$inValidator] = new $class($inOptions, $this->getTranslationManager());
					$loaded = true;
					break;
				}
			}
			if ( !$loaded ) {
				throw new utilityValidateException("Failed to locate validator of type ($inValidator) for var ($inVarName)");
			}
		} catch ( Exception $e ) {
			$this->addMessage($inVarName, $e->getMessage());
		}
		return $this;
	}
	
	/**
	 * Removes the validator for $inVarName
	 *
	 * @param string $inVarName
	 * @param string $inValidator
	 * @return utilityValidator
	 */
	function removeRule($inVarName, $inValidator) {
		if ( isset($this->_Rules[$inVarName]) && isset($this->_Rules[$inVarName][$inValidator]) ) {
			$this->_Rules[$inVarName][$inValidator] = null;
			unset($this->_Rules[$inVarName][$inValidator]);
		}
		return $this;
	}
	
	/**
	 * Returns the translation manager, or null if not set
	 *
	 * @return translateManager
	 */
	function getTranslationManager() {
		if ( $this->_TranslateManager instanceof translateManager ) {
			return $this->_TranslateManager;
		}
		return null;
	}
	
	/**
	 * Sets the translation manager for use with validators
	 *
	 * @param translateManager $inManager
	 * @return utilityValidator
	 */
	function setTranslationManager($inManager) {
		if ( $inManager instanceof translateManager ) {
			$this->_TranslateManager = $inManager;
			$this->setModified();
		}
		return $this;
	}
}