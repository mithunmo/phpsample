<?php
/**
 * utilityValidateInterface.class.php
 * 
 * utilityValidateInterface
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage utility
 * @category utilityValidateInterface
 * @version $Rev: 650 $
 */


/**
 * utilityValidateInterface
 * 
 * Defines the validator interface for each validator component.
 *
 * @package scorpio
 * @subpackage utility
 * @category utilityValidateInterface
 */
interface utilityValidateInterface {
	
	/**
	 * Returns true if $inValue is valid, false if not
	 *
	 * @param mixed $inValue
	 * @return boolean
	 */
	function isValid($inValue);
	
	/**
	 * Returns the validator options, or specific option
	 *
	 * @param string $inOption (optional)
	 * @return mixed
	 */
	function getOptions($inOption = null);
	
	/**
	 * Sets the validator options
	 *
	 * @param array $inOptions
	 * @return utilityValidateInterface
	 */
	function setOptions(array $inOptions = array());
	
	/**
	 * Returns the array of messages from failed validation
	 *
	 * @return array
	 */
	function getMessages();
	
	/**
	 * Returns the translate manager instance, null if not set
	 *
	 * @return translateManager
	 */
	function getTranslationManager();
	
	/**
	 * Sets the translation manager instance
	 *
	 * @param translateManager $inManager
	 * @return utilityValidateInterface
	 */
	function setTranslationManager($inManager);
}