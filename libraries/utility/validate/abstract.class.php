<?php
/**
 * utilityValidateAbstract.class.php
 * 
 * utilityValidateAbstract
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage utility
 * @category utilityValidateAbstract
 * @version $Rev: 777 $
 */


/**
 * utilityValidateAbstract
 * 
 * Abstract supertype for validators. Implements the validate interface
 * and provides additional methods and options for templated error
 * messages and translation support.
 * 
 * This class should be extended into the specific validate class.
 * 
 * To use translation, pass a configured instance of {@link translateManager}
 * to the constructor of the validator.
 *
 * @package scorpio
 * @subpackage utility
 * @category utilityValidateAbstract
 */
abstract class utilityValidateAbstract implements utilityValidateInterface {
	
	const MIN = 'min';
	const MAX = 'max';
	const NOT_NULL = 'notNull';
	const NOT_EMPTY = 'notEmpty';
	const BREAK_ON_FAIL = 'breakOnFail';
	const OBSCURE_VALUE = 'obscureValue';
	const STRICT_CHECK = 'strictCheck';
	
	/**
	 * Base options set, for handling validator options
	 *
	 * @var baseOptionsSet
	 * @access protected
	 */
	protected $_Options;
	
	/**
	 * Stores an array of message templates for the error messages
	 *
	 * @var array
	 * @access protected
	 */
	protected $_MessageTemplates;
	
	/**
	 * Stores an array of identifiers and values to be used in error messages
	 *
	 * @var array
	 * @access protected
	 */
	protected $_MessageVariables;
	
	/**
	 * Array of messages
	 *
	 * @var array
	 * @access protected
	 */
	protected $_Messages = array();
	
	/**
	 * Stores the original unmodified value
	 *
	 * @var mixed
	 * @access protected
	 */
	protected $_Value;
	
	/**
	 * Instance of the translation manager
	 *
	 * @var translateManager
	 * @access protected
	 */
	protected $_TranslateManager;
	
	
	
	/**
	 * Creates a new validator instance, validator is initialised after options are set
	 *
	 * @param array $inOptions
	 * @param translateManager $inTranslateManager (optional)
	 */
	function __construct(array $inOptions = array(), $inTranslateManager = null) {
		$this->reset();
		$this->setOptions($inOptions);
		$this->setTranslationManager($inTranslateManager);
		$this->initialise();
	}
	
	/**
	 * Performs validator initialisation including customisation of template messages etc.
	 *
	 * @return void
	 */
	abstract function initialise();
	
	/**
	 * Resets the object
	 *
	 * @return void
	 */
	function reset() {
		$this->_MessageTemplates = array();
		$this->_MessageVariables = array();
		$this->_Messages = array();
		$this->_Options = null;
		$this->_Value = null;
	}
	
	

	/**
	 * Returns the message template corresponding to $inTemplateKey
	 *
	 * @param string $inTemplateKey
	 * @return string Returns null if no template exists
	 */
	function getMessageTemplate($inTemplateKey) {
		if ( isset($this->_MessageTemplates[$inTemplateKey]) ) {
			return $this->_MessageTemplates[$inTemplateKey];
		} else {
			return null;
		}
	}
	
	/**
	 * Adds a new message template for key $inTemplateKey
	 *
	 * @param string $inTemplateKey
	 * @param string $inMessageTemplate
	 * @return utilityValidateAbstract
	 */
	function addMessageTemplates($inTemplateKey, $inMessageTemplate) {
		$this->_MessageTemplates[$inTemplateKey] = $inMessageTemplate;
		return $this;
	}
	
	/**
	 * Formats an error message replacing keys with data
	 *
	 * @param string $inTemplateKey
	 * @param mixed $inValue
	 * @return string
	 */
	protected function _formatMessage($inTemplateKey, $inValue) {
		$message = (string) $this->getMessageTemplate($inTemplateKey);
		
		if ( $this->getTranslationManager() instanceof translateManager ) {
			$message = $this->getTranslationManager()->translate($message);
		}
		
		if ( is_object($inValue) ) {
			if ( !in_array('__toString', get_class_methods($inValue)) ) {
				$inValue = get_class($inValue) . ' object';
			} else {
				$inValue = $inValue->__toString();
			}
		} else {
			$inValue = (string) $inValue;
		}
		
		if ( $this->getOptions(self::OBSCURE_VALUE) ) {
			$inValue = str_repeat('*', strlen($inValue));
		}
		
		$message = str_replace('%value%', (string) $inValue, $message);
		foreach ( $this->_MessageVariables as $identifier => $value ) {
			$message = str_replace("%$identifier%", (string) $value, $message);
		}
        return $message;
	}
	
	/**
	 * Returns $_MessageVariables
	 *
	 * @return array
	 */
	function getMessageVariables() {
		return $this->_MessageVariables;
	}
	
	/**
	 * Adds a message variable, $inValue should be either a string or number
	 *
	 * @param string $inIdentifier
	 * @param mixed $inValue
	 * @return utilityValidateAbstract
	 */
	function addMessageVariable($inIdentifier, $inValue) {
		$this->_MessageVariables[$inIdentifier] = $inValue;
		return $this;
	}
	
	/**
	 * @see utilityValidateInterface::getMessages()
	 *
	 * @return array
	 */
	function getMessages() {
		return $this->_Messages;
	}
	
	/**
	 * Adds the message to the stack
	 *
	 * @param string $inMessage
	 * @return utilityValidateInterface
	 */
	function addMessage($inMessage) {
		$this->_Messages[] = $inMessage;
		return $this;
	}
	
	/**
	 * Returns the options set
	 *
	 * @return baseOptionsSet
	 */
	function getOptionsSet() {
		if ( !$this->_Options instanceof baseOptionsSet ) {
			$this->_Options = new baseOptionsSet();
		}
		return $this->_Options;
	}
	
	/**
	 * @see utilityValidateInterface::getOptions()
	 *
	 * @param string $inOption
	 * @return mixed
	 */
	function getOptions($inOption = null) {
		return $this->getOptionsSet()->getOptions($inOption);
	}

	/**
	 * @see utilityValidateInterface::setOptions()
	 *
	 * @param array $inOptions
	 * @return utilityValidateInterface
	 */
	function setOptions(array $inOptions = array()) {
		$this->getOptionsSet()->setOptions($inOptions);
		return $this;
	}

	/**
	 * Returns $_Value
	 *
	 * @return mixed
	 */
	function getValue() {
		return $this->_Value;
	}
	
	/**
	 * Set $_Value to $inValue
	 *
	 * @param mixed $inValue
	 * @return utilityValidateAbstract
	 */
	function setValue($inValue) {
		if ( $inValue !== $this->_Value ) {
			$this->_Value = $inValue;
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
	 * @return utilityValidateAbstract
	 */
	function setTranslationManager($inManager) {
		if ( $inManager instanceof translateManager ) {
			$this->_TranslateManager = $inManager;
		}
		return $this;
	}
}