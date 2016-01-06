<?php
/**
 * mvcControllerAction.class.php
 * 
 * mvcControllerAction class
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage mvc
 * @category mvcControllerAction
 * @version $Rev: 707 $
 */


/**
 * mvcControllerAction
 * 
 * Stores information about a particular action including any validation rules. This is used
 * within the mvcControllerActions object. Requires an action name and a valid regular
 * expression that can be used with {@link http://www.php.net/preg_match preg_match}. Actions are held
 * in the {@link mvcControllerActions} set.
 * 
 * <code>
 * // most basic example
 * $oAction = new mvcControllerAction('action');
 * 
 * // allow an integer as an action e.g. product ID
 * $oAction = new mvcControllerAction('productID', '/^\d+$/');
 * 
 * // use the factory method
 * $oAction = mvcControllerAction::factory('productID', '/^\d+$/');
 * </code>
 * 
 * @package scorpio
 * @subpackage mvc
 * @category mvcControllerAction
 */
class mvcControllerAction {
	
	/**
	 * The action name
	 *
	 * @var string
	 * @access protected
	 */
	protected $_Action;
	
	/**
	 * A regular expression to validate the action
	 *
	 * @var string
	 * @access protected
	 */
	protected $_ValidationRegExp;
	
	/**
	 * Stores $_Modified
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_Modified;
	
	
	
	/**
	 * Factory class for mvcControllerAction
	 *
	 * @param string $inAction
	 * @param string $inValidationRegExp
	 * @return mvcControllerAction
	 * @static
	 */
	static function factory($inAction, $inValidationRegExp = null) {
		return new mvcControllerAction($inAction, $inValidationRegExp);
	}
	
	
	
	/**
	 * Returns a new mvcControllerAction
	 *
	 * @param string $inAction
	 * @param string $inValidationRegExp
	 * @return mvcControllerAction
	 */
	function __construct($inAction, $inValidationRegExp = null) {
		$this->reset();
		if ( is_null($inValidationRegExp) ) {
			$inValidationRegExp = "/^$inAction$/";
		}
		$this->setAction($inAction);
		$this->setValidationRegExp($inValidationRegExp);
	}
	
	
	/**
	 * Resets object properties
	 *
	 * @return void
	 */
	function reset() {
		$this->_Action = '';
		$this->_ValidationRegExp = null;
		$this->_Modified = false;
	}
	
	/**
	 * Returns true if the request action is valid against this actions validation rules
	 *
	 * @param string $inRequest
	 * @return boolean
	 * @throws mvcControllerException
	 */
	function isValidAction($inRequest = '') {
		if ( !$this->getValidationRegExp() ) {
			throw new mvcControllerException("No validation instructions for ({$this->getAction()}), please set a validation expression");
		}
		return preg_match($this->getValidationRegExp(), $inRequest) > 0;
	}
	
	
	
	/**
	 * Returns true if object is modified
	 *
	 * @return boolean
	 */
	function isModified() {
		return $this->_Modified;
	}
	
	/**
	 * Set modified status
	 *
	 * @param boolean $inStatus
	 * @return mvcControllerAction
	 */
	function setModified($inStatus = true) {
		$this->_Modified = $inStatus;
		return $this;
	}
	
	/**
	 * Returns the action
	 *
	 * @return string
	 */
	function getAction() {
		return $this->_Action;
	}
	
	/**
	 * Set the action
	 *
	 * @param string $inAction
	 * @return mvcControllerAction
	 */
	function setAction($inAction) {
		if ( $this->_Action !== $inAction ) {
			$this->_Action = $inAction;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Return the validation regular expression
	 *
	 * @return string
	 */
	function getValidationRegExp() {
		return $this->_ValidationRegExp;
	}
	
	/**
	 * Set the validation regular expression
	 *
	 * @param string $inValidationRegExp
	 * @return mvcControllerAction
	 */
	function setValidationRegExp($inValidationRegExp) {
		if ( $this->_ValidationRegExp !== $inValidationRegExp ) {
			$this->_ValidationRegExp = $inValidationRegExp;
			$this->setModified();
		}
		return $this;
	}
}