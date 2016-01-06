{*
Template contains the following keys:

'appAuthor' -> the application author
'appCopyright' -> application copyright notice
'classBaseString' -> the prefix for the class (database name canonised)
'database' -> the database this table is in
'exceptionClass' -> name of the exception to class to throw on error
'oConfig' -> full base config
'className' -> our intended class name
'primaryKey' -> array of fields that make up the primary key
'uniqueKeys' -> array of unique indexes containing the fields that make the key
'uniqueKeyFields' -> array of unqiue fields from all unique indexes
'nonUniqueFields' -> array of fields NOT in any unique or primary keys
'oTableDefinition' -> the full table definition object
'textUtil' -> text transform utility class
'hasUpdateField' -> set if the table has a field named updateDate

Generator uses { & } as a Smarty var
*}
{'<?php'}
/**
 * {$className}
 *
 * Stored in {$className}.class.php
 *
 * @author {$appAuthor}
 * @copyright {$appCopyright}
 * @package {$classBaseString}
 * @subpackage {$className}
 * @category {$className}
 * @version $Rev: 801 $
 */


/**
 * {$className} Class
 *
 * This is a composite class that includes various sub-objects that make up
 * this object.
 *
 * @package {$classBaseString}
 * @subpackage {$className}
 * @category {$className}
 */
class {$className} extends {$className}Base {

	/**
	 * Object destructor, used to remove internal object instances
	 *
	 * @return void
 	 */
	function __destruct() {
		parent::__destruct();
	}

	/**
	 * Get an instance of {$className} by primary key
	 *
{if count($primaryKey) > 0}
{foreach $primaryKey as $seqID => $field}
	 * @param {assign var=oField value=$oTableDefinition->getField($field->getColumnName())}{$oField->getPhpType()} $in{$textUtil->CamelText($oField->getField())}
{/foreach}
{/if}
	 * @return {$className}
	 * @static
	 */
	public static function getInstance({include file='primaryKeyMethodParameters.tpl'}) {
		$key = {foreach $primaryKey as $seqID => $field}$in{$textUtil->CamelText($field->getColumnName())}{if !$field@last}.'.'.{/if}{/foreach};

		/**
		 * Check for an existing instance
		 */
{if count($primaryKey) > 0}
		if ( isset(self::$_Instances[$key]) ) {
			return self::$_Instances[$key];
		}

		/**
		 * No instance, create one
		 */
		$oObject = new {$className}();
{foreach $primaryKey as $seqID => $field}
		$oObject->set{$textUtil->CamelText($field->getColumnName())}($in{$textUtil->CamelText($field->getColumnName())});
{/foreach}
		if ( $oObject->load() ) {
			self::$_Instances[$key] = $oObject;
		}
{else}
		$oObject = new {$className}();
{/if}

		return $oObject;
	}
{if count($uniqueKeys) > 0}
{foreach $uniqueKeys as $indexName => $fields}
{if $indexName != 'PRIMARY'}

	/**
	 * Get instance of {$className} by unique key ({$indexName})
	 *
{foreach $fields as $field}
	 * @param {assign var=oField value=$oTableDefinition->getField($field->getColumnName())}{$oField->getPhpType()} $in{$textUtil->CamelText($oField->getField())}
{/foreach}
	 * @return {$className}
	 * @static
	 */
	public static function getInstanceBy{$textUtil->CamelText($indexName)}({foreach $fields as $field}$in{$textUtil->CamelText($field->getColumnName())}{if !$field@last}, {/if}{/foreach}) {
		$key = {foreach $fields as $field}$in{$textUtil->CamelText($field->getColumnName())}{if !$field@last}.'.'.{/if}{/foreach};

		/**
		 * Check for an existing instance
		 */
		if ( isset(self::$_Instances[$key]) ) {
			return self::$_Instances[$key];
		}

		/**
		 * No instance, create one
		 */
		$oObject = new {$className}();
{foreach $fields as $seqID => $field}
		$oObject->set{$textUtil->CamelText($field->getColumnName())}($in{$textUtil->CamelText($field->getColumnName())});
{/foreach}
		if ( $oObject->load() ) {
			self::$_Instances[$key] = $oObject;
		}

		return $oObject;
	}
{/if}
{/foreach}
{/if}

	/**
	 * Returns an array of objects of {$className}
	 *
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @return array
	 * @static
	 */
	public static function listOfObjects($inOffset = null, $inLimit = 30) {
		/*
		 * Holds values to be assigned during query execution. Values do not need
		 * to be escaped because they are injected into named place-holders in the
		 * prepared query. Add items using $values[':PlaceHolder'] = $value;
  		 */
		$values = array();

		$query = '
			SELECT {foreach $oTableDefinition->getFields() as $oField}{$oField->getField()}{if !$oField@last}, {/if}{/foreach}

			  FROM '.system::getConfig()->getDatabase('{$database}').'.{$oTableDefinition->getTableName()}
			 WHERE 1';

		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}

		$list = array();

		$oStmt = dbManager::getInstance()->prepare($query);
		if ( $oStmt->execute($values) ) {
			foreach ( $oStmt as $row ) {
				$oObject = new {$className}();
				$oObject->loadFromArray($row);
				$list[] = $oObject;
			}
		}
		$oStmt->closeCursor();

		return $list;
	}



	/**
	 * Saves object to the table
	 *
	 * @return boolean
	 */
	function save() {
		$return = false;

		if ( $this->isModified() ) {
			$return = parent::save();
		}

		return $return;
	}

	/**
	 * Deletes the object from the table
	 *
	 * @return boolean
	 */
	function delete() {
		$return = false;

		if ( $this->getPrimaryKey() ) {
			$return = parent::delete();
		}

		return $return;
	}

	/**
	 * Resets object properties to defaults
	 *
	 * @return void
	 */
	function reset() {
		parent::reset();
	}

	/**
	 * Returns true if object has been modified
	 *
	 * @return boolean
	 */
	function isModified() {
		$modified = parent::isModified();

		return $modified;
	}
}