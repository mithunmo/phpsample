<?php
/**
 * cliCommandAliases Class
 * 
 * Stored in aliases.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage cli
 * @category cliCommandAliases
 * @version $Rev: 707 $
 */


/**
 * cliCommandAliases class
 *
 * Holds a set of aliases for a command.
 * 
 * <code>
 * // create and assign aliases to existing command
 * $oAliases = new cliCommandAliases(array('m', 'M'));
 * $oCommand->setCommandAliases($oAliases);
 * 
 * // declare aliases in a custom command 
 * class myCommand extends cliCommand {
 *     
 *     function __construct(cliRequest $inRequest) {
 *         parent::__construct($inRequest, 'myCommand', null, array('m', 'M')); 
 *     }
 * }
 * </code>
 * 
 * @package scorpio
 * @subpackage cli
 * @category cliCommandAliases
 */
class cliCommandAliases extends baseSet {
	
	/**
	 * Stores $_Default
	 *
	 * @var string
	 * @access protected
	 */
	protected $_Default;
	
	
	
	/**
	 * Creates a new alias set, you can pass an array of aliases, the first will be set
	 * as the default (for help)
	 *
	 * @param array $inAliases
	 * @return cliCommandAliases
	 */
	function __construct(array $inAliases = array()) {
		$this->reset();
		if ( count($inAliases) > 0 ) {
			foreach ( $inAliases as $alias ) {
				$this->addAlias($alias);
			}
		}
	}
	
	/**
	 * Resets the object
	 *
	 * @return void
	 */
	function reset() {
		$this->_Default = null;
		parent::_resetSet();
	}
	
	
	
	/**
	 * Returns true if command has $inAlias
	 *
	 * @param string $inAlias
	 * @return boolean
	 */
	function hasAlias($inAlias) {
		return $this->_itemValueInSet($inAlias);
	}
	
	/**
	 * Adds an alias to the set
	 *
	 * @param string $inCommand
	 * @return cliCommandAliases
	 */
	function addAlias($inAlias) {
		if ( !$this->getDefault() ) {
			$this->setDefault($inAlias);
		}
		return $this->_setValue($inAlias);
	}
	
	/**
	 * Removes $inAlias from the set
	 *
	 * @param string $inCommand
	 * @return cliCommandAliases
	 */
	function removeAlias($inAlias) {
		return $this->_removeItemWithValue($inAlias);
	}
	
	/**
	 * Returns the alias count
	 *
	 * @return integer
	 */
	function getCount() {
		return parent::_itemCount();
	}
	
	

	/**
	 * Returns $_Default
	 *
	 * @return string
	 * @access public
	 */
	function getDefault() {
		return $this->_Default;
	}
	
	/**
	 * Set $_Default to $inDefault
	 *
	 * @param string $inDefault
	 * @return cliCommandAlises
	 * @access public
	 */
	function setDefault($inDefault) {
		if ( $this->_Default !== $inDefault ) {
			$this->_Default = $inDefault;
			$this->setModified();
		}
		return $this;
	}
}