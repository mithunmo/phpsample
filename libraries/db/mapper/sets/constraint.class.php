<?php
/**
 * dbMapperConstraintSet.class.php
 * 
 * Holds a set of constraint definitions
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage db
 * @category dbMapperConstraintSet
 * @version $Rev: 837 $
 */


/**
 * dbMapperConstraintSet Class
 * 
 * Holds a set of Constraint definitions. This is used by the dbMapper
 * when building a map of the database properties. It can be used
 * standalone to simulate a tables foreign keys.
 * 
 * <code>
 * $oSet = new dbMapperConstraintSet();
 * $oSet->addConstraintDefinition(new dbMapperConstraintDefinition());
 * $oSet->addConstraintDefinition(new dbMapperConstraintDefinition());
 * </code>
 * 
 * @see dbMapperConstraintDefinition
 * @package scorpio
 * @subpackage db
 * @category dbMapperConstraintSet
 */
class dbMapperConstraintSet extends baseSet {
	
	/**
	 * Returns a single instance of the base set
	 *
	 * @return dbMapperConstraintSet
	 */
	static function getInstance() {
		return new self();
	}
	
	/**
	 * Adds a Field definition to the set
	 *
	 * @param dbMapperConstraintDefinition $inConstraint
	 * @return dbMapperConstraintSet
	 */
	function addConstraintDefinition(dbMapperConstraintDefinition $inConstraint) {
		return parent::_setItem($inConstraint->getConstraintName(), $inConstraint);
	}
	
	/**
	 * Sets an array of constraints
	 *
	 * @param array $inConstraints
	 * @return dbMapperConstraintSet
	 */
	function setConstraints($inConstraints) {
		return parent::_setItem($inConstraints);
	}
	
	/**
	 * Returns a constraint definition
	 *
	 * @param string $inConstraintName
	 * @return dbMapperConstraintDefinition
	 */
	function getConstraintDefinition($inConstraintName) {
		return parent::_getItem($inConstraintName);
	}
	
	/**
	 * Removes a constraint definition
	 *
	 * @param dbMapperConstraintDefinition $inConstraint
	 * @return dbMapperConstraintSet
	 */
	function removeConstraintDefinition(dbMapperConstraintDefinition $inConstraint) {
		return parent::_removeItem($inConstraint->getConstraintName());
	}
	
	/**
	 * Removes all constraints
	 *
	 * @return dbMapperConstraintSet
	 */
	function removeConstraints() {
		return parent::_resetSet();
	}
	
	/**
	 * Returns number of objects in set
	 *
	 * @return integer
	 */
	function countConstraints() {
		return parent::_itemCount();
	}

	/**
	 * Returns true if the specified table index is a foreign key
	 *
	 * @param string $inIndex
	 * @return boolean
	 */
	function isIndexForeignKey($inIndex) {
		if ( $this->getCount() > 0 ) {
			/**
			 * @var dbMapperConstraintDefinition $oObject
			 */
			foreach ( $this as $oObject ) {
				if ( $oObject->getKeyName() == $inIndex ) {
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Returns the constraint matching the index $inIndex
	 *
	 * @param string $inIndex
	 * @return dbMapperConstraintDefinition
	 */
	function getConstraintByIndex($inIndex) {
		if ( $this->getCount() > 0 ) {
			/**
			 * @var dbMapperConstraintDefinition $oObject
			 */
			foreach ( $this as $oObject ) {
				if ( $oObject->getKeyName() == $inIndex ) {
					return $oObject;
				}
			}
		}

		return null;
	}

	/**
	 * Returns true if $inTable and $inField are present in any of the constraints
	 *
	 * @param string $inTable
	 * @param string $inField
	 * @return boolean
	 */
	function isTableFieldForeignKey($inTable, $inField) {
		if ( $this->getCount() > 0 ) {
			/**
			 * @var dbMapperConstraintDefinition $oObject
			 */
			foreach ( $this as $oObject ) {
				if ( $oObject->getReferenceTable() == $inTable && $oObject->getReferenceColumn() == $inField ) {
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Returns definition if $inTable and $inField match the reference table, field
	 *
	 * @param string $inTable
	 * @param string $inField
	 * @return dbMapperConstraintDefinition
	 */
	function getConstraintByTableField($inTable, $inField) {
		if ( $this->getCount() > 0 ) {
			/**
			 * @var dbMapperConstraintDefinition $oObject
			 */
			foreach ( $this as $oObject ) {
				if ( $oObject->getReferenceTable() == $inTable && $oObject->getReferenceColumn() == $inField ) {
					return $oObject;
				}
			}
		}

		return null;
	}
}