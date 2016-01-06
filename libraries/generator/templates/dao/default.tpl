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
 * @version $Rev: 840 $
 */


/**
 * {$className} Class
 *
 * Provides access to records in {$database}.{$oTableDefinition->getTableName()}
 *
 * Creating a new record:
 * <code>
 * $o{$textUtil->classify($className)} = new {$className}();
{foreach $oTableDefinition->getFields() as $oField}
 * $o{$textUtil->classify($className)}->set{$textUtil->CamelText($oField->getField())}($in{$textUtil->CamelText($oField->getField())});
{/foreach}
 * $o{$textUtil->classify($className)}->save();
 * </code>
 *
 * Accessing a record by primary key on constructor:
 * <code>
 * $o{$textUtil->classify($className)} = new {$className}({if count($primaryKey) > 0}{foreach name=construct key=seqID item=field from=$primaryKey}$in{$textUtil->CamelText($field->getColumnName())}{if !$smarty.foreach.construct.last}, {/if}{/foreach}{/if});
 * </code>
 *
{if count($primaryKey) > 0}
 * Access by manually calling load:
 * <code>
 * $o{$textUtil->classify($className)} = new {$className}();
{foreach $primaryKey as $oField}
 * $o{$textUtil->classify($className)}->set{$textUtil->CamelText($oField->getColumnName())}($in{$textUtil->CamelText($oField->getColumnName())});
{/foreach}
 * $o{$textUtil->classify($className)}->load();
 * </code>
{/if}
 *
 * Accessing a record by instance:
 * <code>
 * $o{$textUtil->classify($className)} = {$className}::getInstance({include file='primaryKeyMethodParameters.tpl'});
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 *
 * @package {$classBaseString}
 * @subpackage {$className}
 * @category {$className}
 */
class {$className} implements systemDaoInterface, systemDaoValidatorInterface {

	/**
	 * Character used to separate values in a compound primary key
	 *
	 * @var string
 	 */
	const PRIMARY_KEY_SEPARATOR = '.';

	/**
	 * Container for static instances of {$className}
	 *
	 * @var array
	 * @access protected
	 * @static
	 */
	protected static $_Instances = array();

	/**
	 * Stores $_Modified
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_Modified = false;

	/**
	 * Stores the validator for this object
	 *
	 * @var utilityValidator
	 * @access protected
	 */
	protected $_Validator;
{foreach $oTableDefinition->getFields() as $oField}

	/**
	 * Stores $_{$textUtil->CamelText($oField->getField())}
	 *
	 * @var {if $oField->getPhpType() == 'datetime' || $oField->getPhpType() == 'timestamp'}systemDateTime{else}{$oField->getPhpType()}{/if} {if count($oField->getValues()) > 0}({foreach $oField->getValues() as $value}{$oField->getField()|upper}_{$value|upper|regex_replace:'/\W/':'_'},{/foreach}){/if}

	 * @access protected
	 */
	protected $_{$textUtil->CamelText($oField->getField())};
{if count($oField->getValues()) > 0}
{foreach $oField->getValues() as $value}
	const {$oField->getField()|upper}_{$value|upper|regex_replace:'/\W/':'_'} = '{$value}';
{/foreach}
{/if}
{/foreach}

	/**
	 * If true, the object is scheduled to be deleted
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_MarkForDeletion = false;



	/**
	 * Returns a new instance of {$className}
	 *
{if count($primaryKey) > 0}
{foreach $primaryKey as $seqID => $field}
	 * @param {assign var=oField value=$oTableDefinition->getField($field->getColumnName())}{$oField->getPhpType()} $in{$textUtil->CamelText($oField->getField())}
{/foreach}
{/if}
	 * @return {$className}
	 */
	function __construct({include file='primaryKeyMethodParameters.tpl' setNull=true}) {
		$this->reset();
{if count($primaryKey) > 0}
		if ( {foreach $primaryKey as $seqID => $field}$in{$textUtil->CamelText($field->getColumnName())} !== null{if !$field@last} && {/if}{/foreach} ) {
{foreach $primaryKey as $seqID => $field}
			$this->set{$textUtil->CamelText($field->getColumnName())}($in{$textUtil->CamelText($field->getColumnName())});
{/foreach}
			$this->load();
		}
{/if}
	}

	/**
	 * Object destructor, used to remove internal object instances
	 *
	 * @return void
 	 */
	function __destruct() {
		if ( $this->_Validator instanceof utilityValidator ) {
			$this->_Validator = null;
		}
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
	 * Loads a record from the database based on the primary key or first unique index
	 *
	 * @return boolean
	 */
	function load() {
		$return = false;
		$values = array();

		$query = '
			SELECT {foreach $oTableDefinition->getFields() as $oField}{$oField->getField()}{if !$oField@last}, {/if}{/foreach}

			  FROM '.system::getConfig()->getDatabase('{$database}').'.{$oTableDefinition->getTableName()}';

		$where = array();
{if count($uniqueKeyFields) > 0}
{foreach $uniqueKeyFields as $keyIndex}
		if ( $this->_{$textUtil->CamelText($keyIndex->getColumnName())} !== {assign var=oField value=$oTableDefinition->getField($keyIndex->getColumnName())}{if $oField->getIsNull()}null{elseif is_array($oField->getDefault())}array(){elseif is_float($oField->getDefault())}0.{string_repeat char=0 repeat=$oField->getPrecision()|default:1}{elseif is_numeric($oField->getDefault())}0{elseif is_string($oField->getDefault())}{if count($oField->getValues()) > 0}'{$oField->getDefault()}'{else}''{/if}{else}false{/if} ) {
			$where[] = ' {$keyIndex->getColumnName()} = :{$textUtil->CamelText($keyIndex->getColumnName())} ';
			$values[':{$textUtil->CamelText($keyIndex->getColumnName())}'] = $this->get{$textUtil->CamelText($keyIndex->getColumnName())}();
		}
{/foreach}
{else}
{foreach $oTableDefinition->getFields() as $oField}
		if ( $this->_{$textUtil->CamelText($oField->getField())} !== {include file='defaultFieldValue.tpl' oField=$oField} ) {
			$where[] = ' {$oField->getField()} = :{$textUtil->CamelText($oField->getField())} ';
			$values[':{$textUtil->CamelText($oField->getField())}'] = $this->get{$textUtil->CamelText($oField->getField())}();
		}
{/foreach}
{/if}

		if ( count($where) == 0 ) {
			return false;
		}

		$query .= ' WHERE '.implode(' AND ', $where);

		$oStmt = dbManager::getInstance()->prepare($query);

		$this->reset();
		if ( $oStmt->execute($values) ) {
			$row = $oStmt->fetch();
			if ( $row !== false && is_array($row) ) {
				$this->loadFromArray($row);
				$oStmt->closeCursor();
				$return = true;
			}
		}

		return $return;
	}

	/**
	 * Loads a record by array
	 *
	 * @param array $inArray
	 * @return void
 	 */
	function loadFromArray(array $inArray) {
{foreach $oTableDefinition->getFields() as $oField}
		$this->set{$textUtil->CamelText($oField->getField())}({if $oField->getPhpType() == 'integer'}(int){/if}$inArray['{$oField->getField()}']);
{/foreach}
		$this->setModified(false);
	}

	/**
	 * Saves object to the table
	 *
	 * @return boolean
	 */
	function save() {
		$return = false;
		if ( $this->isModified() ) {
			$message = '';
			if ( !$this->isValid($message) ) {
				throw new {$exceptionClass}($message);
			}

{if $hasUpdateField}
			$this->getUpdateDate()->setTimestamp(time());
{/if}
			if ( $this->_Modified ) {
				$query = '
				INSERT INTO '.system::getConfig()->getDatabase('{$database}').'.{$oTableDefinition->getTableName()}
					( {foreach $oTableDefinition->getFields() as $oField}{$oField->getField()}{if !$oField@last}, {/if}{/foreach} )
				VALUES
					( {foreach $oTableDefinition->getFields() as $oField}:{$textUtil->CamelText($oField->getField())}{if !$oField@last}, {/if}{/foreach} )
{if count($nonUniqueFields) > 0}
				ON DUPLICATE KEY UPDATE
{foreach $nonUniqueFields as $field}
					{$field->getField()}=VALUES({$field->getField()}){if !$field@last},
{/if}
{/foreach}
{/if}				';

				$oDB = dbManager::getInstance();
				$oStmt = $oDB->prepare($query);
{foreach $oTableDefinition->getFields() as $field}
				$oStmt->bindValue(':{$textUtil->CamelText($field->getField())}', $this->get{$textUtil->CamelText($field->getField())}());
{/foreach}

				if ( $oStmt->execute() ) {
{if count($primaryKey) == 1}
{foreach $primaryKey as $seqID => $index}
					if ( !$this->get{$textUtil->CamelText($index->getColumnName())}() ) {
						$this->set{$textUtil->CamelText($index->getColumnName())}($oDB->lastInsertId());
					}
{/foreach}
{/if}
					$this->setModified(false);
					$return = true;
				}
			}
		}

		return $return;
	}

	/**
	 * Deletes the object from the table
	 *
	 * @return boolean
	 */
	function delete() {
		$query = '
			DELETE FROM '.system::getConfig()->getDatabase('{$database}').'.{$oTableDefinition->getTableName()}
			WHERE
{foreach $primaryKey as $seqID => $index}
				{$index->getColumnName()} = :{$textUtil->CamelText($index->getColumnName())}{if !$index@last} AND
{/if}
{/foreach}

			LIMIT 1';

		$oStmt = dbManager::getInstance()->prepare($query);
{foreach $primaryKey as $seqID => $index}
		$oStmt->bindValue(':{$textUtil->CamelText($index->getColumnName())}', $this->get{$textUtil->CamelText($index->getColumnName())}());
{/foreach}

		if ( $oStmt->execute() ) {
			$oStmt->closeCursor();
			$this->reset();
			return true;
		}

		return false;
	}

	/**
	 * Resets object properties to defaults
	 *
	 * @return {$className}
	 */
	function reset() {
{foreach $oTableDefinition->getFields() as $oField}
		$this->_{$textUtil->CamelText($oField->getField())} = {include file='defaultFieldValue.tpl'};
{/foreach}
		$this->_Validator = null;
		$this->setModified(false);
		$this->setMarkForDeletion(false);
		return $this;
	}

	/**
	 * Returns properties of object as an array
	 *
	 * @return array
	 */
	function toArray() {
		return get_object_vars($this);
	}

	/**
	 * Returns the validator, creating one if not set
	 *
	 * @return utilityValidator
	 */
	function getValidator() {
		if ( !$this->_Validator instanceof utilityValidator ) {
			$this->_Validator = new utilityValidator();
		}
		return $this->_Validator;
	}

	/**
	 * Set a pre-built validator instance
	 *
	 * @param utilityValidator $inValidator
	 * @return {$className}
	 */
	function setValidator(utilityValidator $inValidator) {
		$this->_Validator = $inValidator;
		return $this;
	}

	/**
	 * Returns true if object is valid, any errors are added to $inMessage
	 *
	 * @param string $inMessage
	 * @return boolean
	 */
	function isValid(&$inMessage = '') {
		$valid = true;

		$oValidator = $this->getValidator();
		$oValidator->reset();
		$oValidator->setData($this->toArray())->setRules($this->getValidationRules());
		if ( !$oValidator->isValid() ) {
			foreach ( $oValidator->getMessages() as $key => $messages ) {
				$inMessage .= "Error with $key: ".implode(', ', $messages)."\n";
			}
			$valid = false;
		}

		return $valid;
	}

	/**
	 * Returns the array of rules used to validate this object
	 *
	 * @return array
 	 */
	function getValidationRules() {
		return array(
{foreach $oTableDefinition->getFields() as $oField}
			'_{$textUtil->CamelText($oField->getField())}' => array(
				'{include file='validatorType.tpl'}' => {include file='validatorRules.tpl'},
			),
{/foreach}
		);
	}



	/**
	 * Returns true if object has been modified
	 *
	 * @return boolean
	 */
	function isModified() {
		$modified = $this->_Modified;
{if $hasUpdateField}
		if ( !$modified && $this->getUpdateDate() ) {
			$this->_Modified = $this->getUpdateDate()->isModified() || $modified;
		}
{/if}

		return $modified;
	}

	/**
	 * Set the status of the object if it has been changed
	 *
	 * @param boolean $status
	 * @return {$className}
	 */
	function setModified($status = true) {
		$this->_Modified = $status;
		return $this;
	}

	/**
	 * Returns the primaryKey
	 *
	 * @return string
	 */
	function getPrimaryKey() {
		return {foreach $primaryKey as $seqID => $index}$this->_{$textUtil->CamelText($index->getColumnName())}{if !$index@last}.self::PRIMARY_KEY_SEPARATOR.{/if}{/foreach};
	}

	/**
	 * Sets the primaryKey for the object
	 *
	 * The primary key should be a string separated by the class defined
	 * separator string e.g. X.Y.Z where . is the character from:
	 * {$className}::PRIMARY_KEY_SEPARATOR.
 	 *
	 * @param string $inKey
	 * @return {$className}
  	 */
	function setPrimaryKey($inKey) {
		list({foreach $primaryKey as $seqID => $index}${$index->getColumnName()}{if !$index@last}, {/if}{/foreach}) = explode(self::PRIMARY_KEY_SEPARATOR, $inKey);
{foreach $primaryKey as $seqID => $index}
		$this->set{$textUtil->CamelText($index->getColumnName())}(${$index->getColumnName()});
{/foreach}
	}
{foreach $oTableDefinition->getFields() as $oField}

	/**
	 * Return the current value of the property $_{$textUtil->CamelText($oField->getField())}
	 *
	 * @return {if $oField->getPhpType() == 'datetime' || $oField->getPhpType() == 'timestamp'}systemDateTime{else}{$oField->getPhpType()}{/if}

 	 */
	function get{$textUtil->CamelText($oField->getField())}() {
		return $this->_{$textUtil->CamelText($oField->getField())};
	}

	/**
	 * Set the object property _{$textUtil->CamelText($oField->getField())} to $in{$textUtil->CamelText($oField->getField())}
	 *
	 * @param {if $oField->getPhpType() == 'datetime' || $oField->getPhpType() == 'timestamp'}systemDateTime{else}{$oField->getPhpType()}{/if} $in{$textUtil->CamelText($oField->getField())}
	 * @return {$className}
	 */
	function set{$textUtil->CamelText($oField->getField())}($in{$textUtil->CamelText($oField->getField())}) {
		if ( $in{$textUtil->CamelText($oField->getField())} !== $this->_{$textUtil->CamelText($oField->getField())} ) {
{if $oField->getPhpType() == 'datetime' || $oField->getPhpType() == 'timestamp'}
			if ( !$in{$textUtil->CamelText($oField->getField())} instanceof DateTime ) {
				$in{$textUtil->CamelText($oField->getField())} = new systemDateTime($in{$textUtil->CamelText($oField->getField())}, system::getConfig()->getSystemTimeZone()->getParamValue());
			}
{/if}
			$this->_{$textUtil->CamelText($oField->getField())} = $in{$textUtil->CamelText($oField->getField())};
			$this->setModified();
		}
		return $this;
	}
{/foreach}

	/**
	 * Returns $_MarkForDeletion
	 *
	 * @return boolean
	 */
	function getMarkForDeletion() {
		return $this->_MarkForDeletion;
	}

	/**
	 * Set $_MarkForDeletion to $inMarkForDeletion
	 *
	 * @param boolean $inMarkForDeletion
	 * @return {$className}
	 */
	function setMarkForDeletion($inMarkForDeletion) {
		if ( $inMarkForDeletion !== $this->_MarkForDeletion ) {
			$this->_MarkForDeletion = $inMarkForDeletion;
		}
		return $this;
	}
}