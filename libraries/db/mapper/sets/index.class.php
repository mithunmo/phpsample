<?php
/**
 * dbMapperIndexSet.class.php
 * 
 * Holds a set of Index definitions
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage db
 * @category dbMapperIndexSet
 * @version $Rev: 835 $
 */


/**
 * dbMapperIndexSet Class
 * 
 * Holds a set of Index definitions. This is used by the dbMapper
 * when building a map of the database properties. It can be used
 * standalone to simulate a tables indexes.
 * 
 * <code>
 * $oIndexSet = new dbMapperIndexSet();
 * $oIndexSet->addIndexDefinition(new dbMapperIndexDefinition());
 * $oIndexSet->addIndexDefinition(new dbMapperIndexDefinition());
 * </code>
 * 
 * @see dbMapperIndexDefinition
 * @package scorpio
 * @subpackage db
 * @category dbMapperIndexSet
 */
class dbMapperIndexSet extends baseSet {
	
	/**
	 * Returns a single instance of the base set
	 *
	 * @return dbMapperIndexSet
	 */
	static function getInstance() {
		return new self();
	}
	
	/**
	 * Adds a Index definition to the set
	 *
	 * @param dbMapperIndexDefinition $oIndex
	 * @return dbMapperIndexSet
	 */
	function addIndexDefinition(dbMapperIndexDefinition $oIndex) {
		return parent::_setValue($oIndex);
	}
	
	/**
	 * Sets an array of indexes
	 *
	 * @param array $inIndexes
	 * @return dbMapperIndexSet
	 */
	function setIndexes($inIndexes) {
		return parent::_setItem($inIndexes);
	}
	
	/**
	 * Returns an Index definition from $inIndexName specifically for $inColumnName or $inSequenceNum
	 *
	 * @param string $inIndexName
	 * @return dbMapperIndexDefinition
	 */
	function getIndexDefinition($inIndexName, $inColumnName = null, $inSequenceNum = 1) {
		if ( $this->getCount() > 0 ) {
			if ( false ) $oIndex = new dbMapperIndexDefinition();
			foreach ( $this as $oIndex ) {
				if ( $oIndex->getKeyName() == $inIndexName ) {
					if ( $inColumnName !== null && $oIndex->getColumnName() == $inColumnName ) {
						return $oIndex;
					} elseif ( $inColumnName === null && $oIndex->getSequenceInIndex() == $inSequenceNum ) {
						return $oIndex;
					}
				}
			}
		}
		return false;
	}
	
	/**
	 * Removes a Index definition
	 *
	 * @param dbMapperIndexDefinition $oIndex
	 * @return dbMapperIndexSet
	 */
	function removeIndexDefinition(dbMapperIndexDefinition $oIndex) {
		return parent::_removeItemWithValue($oIndex);
	}
	
	/**
	 * Removes all Indexs
	 *
	 * @return dbMapperIndexSet
	 */
	function removeIndexes() {
		return parent::_resetSet();
	}
	
	/**
	 * Returns number of Indexs in set
	 *
	 * @return integer
	 */
	function countIndexes() {
		return parent::_itemCount();
	}
	
	
	
	/**
	 * Returns true if $inFieldName is contained in any index
	 *
	 * @param string $inFieldName
	 * @return boolean
	 */
	function isFieldInIndex($inFieldName) {
		if ( $inFieldName instanceof dbMapperFieldDefinition ) {
			$inFieldName = $inFieldName->getColumnName();
		}
		foreach ( $this as $index ) {
			if ( $index->getColumnName() == $inFieldName ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Returns an array of all indexes that use $inFieldName
	 * 
	 * @param string $inFieldName
	 * @return array
	 */
	function getIndexesContainingField($inFieldName) {
		$res = array();

		if ( $this->getCount() > 0 ) {
			/**
			 * @var dbMapperIndexDefinition $oObject
			 */
			foreach ( $this as $oObject ) {
				if ( $oObject->getColumnName() == $inFieldName ) {
					$res[] = $oObject;
				}
			}
		}

		return $res;
	}
	
	/**
	 * Returns the names of the fields that make the primary key as an array(SequenceInKey => dbMapperIndexDefinition)
	 *
	 * @param string $inKeyName
	 * @return array
	 */
	function getFieldsInKey($inKeyName = 'PRIMARY') {
		$res = array();
		foreach ( $this as $index ) {
			if ( $index->getKeyName() == $inKeyName ) {
				$res[$index->getSequenceInIndex()] = $index;
			}
		}
		return $res;
	}
	
	/**
	 * Returns an array indexed by name of all dbIndexDefinition in the unique index(es)
	 * array[KEY_NAME][SEQ_ID] = dbMapperIndexDefinition
	 *
	 * @return array
	 */
	function getFieldsInUniqueKey() {
		$res = array();
		foreach ( $this as $index ) {
			if ( $index->getUnique() ) {
				$res[$index->getKeyName()][$index->getSequenceInIndex()] = $index;
			}
		}
		return $res;
	}
	
	/**
	 * Returns an array of distinct fields that make up unique indexes
	 *
	 * @return array
	 */
	function getDistinctUniqueFields() {
		$res = array();
		foreach ( $this as $index ) {
			if ( $index->getUnique() ) {
				$res[$index->getColumnName()] = $index;
			}
		}
		return $res;
	}
}