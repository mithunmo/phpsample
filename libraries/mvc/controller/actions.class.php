<?php
/**
 * mvcControllerActions.class.php
 * 
 * mvcControllerActions class
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage mvc
 * @category mvcControllerActions
 * @version $Rev: 650 $
 */


/**
 * mvcControllerActions
 * 
 * Stores information on the controllers "actions" allowing an easier way of keeping them updated.
 * Each action is an instance of {@link mvcControllerAction}. An action can be added by either
 * specifying the full mvcControllerAction object or passing the string name of the action. This
 * is then converted to the appropriate object with a rule matching the action name.
 *
 * @package scorpio
 * @subpackage mvc
 * @category mvcControllerActions
 */
class mvcControllerActions extends baseSet {
	
	/**
	 * Stores $_DefaultAction
	 *
	 * @var string
	 * @access protected
	 */
	protected $_DefaultAction;
	
	
	
	/**
	 * Returns new instance of mvcControllerActions, optionally specify a default action
	 *
	 * @param string $inDefault
	 * @return mvcControllerActions
	 */
	function __construct($inDefault = null) {
		$this->reset();
		if ( $inDefault !== null ) {
			$this->setDefaultAction($inDefault);
			$this->addAction($inDefault);
		}
	}
	
	
	
	/**
	 * Resets object to defaults
	 *
	 * @return void
	 */
	function reset() {
		$this->_DefaultAction = null;
		$this->_resetSet();
	}
	
	/**
	 * Returns the action matching $inAction, false if not found
	 *
	 * @param string $inAction The action name or instance of mvcControllerAction
	 * @return mvcControllerAction
	 */
	function getAction($inAction) {
		$action = $inAction;
		if ( $inAction instanceof mvcControllerAction ) {
			$action = $inAction->getAction();
		}
		return $this->_getItem($action);
	}
	
	/**
	 * Adds the action to the allowed actions list, accepts either a string or mvcControllerAction
	 *
	 * @param string $inAction
	 * @return mvcControllerActions
	 */
	function addAction($inAction) {
		$oAction = $inAction;
		if ( !$inAction instanceof mvcControllerAction ) {
			$oAction = mvcControllerAction::factory($inAction);
		}
		return $this->_setItem($oAction->getAction(), $oAction);
	}
	
	/**
	 * Removes the action from allowed actions list
	 *
	 * @param string $inAction
	 * @return mvcControllerActions
	 */
	function removeAction($inAction) {
		$action = $inAction;
		if ( $inAction instanceof mvcControllerAction ) {
			$action = $inAction->getAction();
		}
		return $this->_removeItem($action);
	}
	
	/**
	 * Returns $_DefaultAction
	 *
	 * @return string
	 */
	function getDefaultAction() {
		return $this->_DefaultAction;
	}
	
	/**
	 * Set $_DefaultAction to $inDefaultAction
	 *
	 * @param string $inDefaultAction
	 * @return mvcControllerActions
	 */
	function setDefaultAction($inDefaultAction) {
		if ( $inDefaultAction !== $this->_DefaultAction ) {
			$this->_DefaultAction = $inDefaultAction;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Returns true if $inAction is a valid action
	 *
	 * @param string $inAction
	 * @return boolean
	 */
	function isValidAction($inAction) {
		$valid = false;
		if ( $this->getActionCount() > 0 ) {
			if ( false ) $oAction = new mvcControllerAction();
			foreach ( $this as $action => $oAction ) {
				$valid = $oAction->isValidAction($inAction) || $valid;
			}
		}
		return $valid;
	}
	
	/**
	 * Returns the number of actions that have been assigned
	 *
	 * @return integer
	 */
	function getActionCount() {
		return $this->_itemCount();
	}
	
	/**
	 * Returns all allowed actions
	 *
	 * @return array
	 */
	function getAllowedActions() {
		return $this->_getItem();
	}
}