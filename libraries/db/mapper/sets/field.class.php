<?php
/**
 * dbMapperFieldSet.class.php
 * 
 * Holds a set of Field definitions
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage db
 * @category dbMapperFieldSet
 * @version $Rev: 835 $
 */


/**
 * dbMapperFieldSet Class
 * 
 * Holds a set of Field definitions. This is used by the dbMapper
 * when building a map of the database properties. It can be used
 * standalone to simulate a tables fields.
 * 
 * <code>
 * $oFieldSet = new dbMapperFieldSet();
 * $oFieldSet->addFieldDefinition(new dbMapperFieldDefinition());
 * $oFieldSet->addFieldDefinition(new dbMapperFieldDefinition());
 * </code>
 * 
 * @see dbMapperFieldDefinition
 * @package scorpio
 * @subpackage db
 * @category dbMapperFieldSet
 */
class dbMapperFieldSet extends baseSet {
	
	/**
	 * Returns a single instance of the base set
	 *
	 * @return dbMapperFieldSet
	 */
	static function getInstance() {
		return new self();
	}
	
	/**
	 * Adds a Field definition to the set
	 *
	 * @param dbMapperFieldDefinition $oField
	 * @return dbMapperFieldSet
	 */
	function addFieldDefinition(dbMapperFieldDefinition $oField) {
		return parent::_setItem($oField->getField(), $oField);
	}
	
	/**
	 * Sets an array of fields
	 *
	 * @param array $inFields
	 * @return dbMapperFieldSet
	 */
	function setFields($inFields) {
		return parent::_setItem($inFields);
	}
	
	/**
	 * Returns a Field definition
	 *
	 * @param string $inFieldName
	 * @return dbMapperFieldDefinition
	 */
	function getFieldDefinition($inFieldName) {
		return parent::_getItem($inFieldName);
	}
	
	/**
	 * Removes a Field definition
	 *
	 * @param dbMapperFieldDefinition $oField
	 * @return dbMapperFieldSet
	 */
	function removeFieldDefinition(dbMapperFieldDefinition $oField) {
		return parent::_removeItem($oField->getField());
	}
	
	/**
	 * Removes all Fields
	 *
	 * @return dbMapperFieldSet
	 */
	function removeFields() {
		return parent::_resetSet();
	}
	
	/**
	 * Returns number of Fields in set
	 *
	 * @return integer
	 */
	function countFields() {
		return parent::_itemCount();
	}
}