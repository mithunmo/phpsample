<?php
/**
 * dbDriverPgSqlTypes.class.php
 * 
 * Contains database to PHP type definitions for use in generator and dbMapper
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage db
 * @category dbDriverPgSqlTypes
 * @version $Rev: 650 $
 */


/**
 * dbDriverPgSqlTypes Class
 * 
 * dbDriverPgSqlTypes definition
 * 
 * @package scorpio
 * @subpackage db
 * @category dbDriverPgSqlTypes
 */
class dbDriverPgSqlTypes extends dbMapperPhpTypes {
	
	/**
	 * Map DB type to a PHP type
	 *
	 * @var array
	 * @access private
	 * @static 
	 */
	private static $_TypeMap = array(
		'int2' => dbMapperPhpTypes::PHP_INTEGER,
		'int4' => dbMapperPhpTypes::PHP_INTEGER, 
		'oid' => dbMapperPhpTypes::PHP_INTEGER,
		'int8' => dbMapperPhpTypes::PHP_INTEGER,
		'cash'  => dbMapperPhpTypes::PHP_FLOAT,
		'money'  => dbMapperPhpTypes::PHP_FLOAT,
		'numeric' => dbMapperPhpTypes::PHP_INTEGER,
		'float4' => dbMapperPhpTypes::PHP_FLOAT,
		'float8' => dbMapperPhpTypes::PHP_FLOAT,
		'bpchar' => dbMapperPhpTypes::PHP_STRING, 
		'char' => dbMapperPhpTypes::PHP_STRING, 
		'char2' => dbMapperPhpTypes::PHP_STRING, 
		'char4' => dbMapperPhpTypes::PHP_STRING, 
		'char8' => dbMapperPhpTypes::PHP_STRING, 
		'char16' => dbMapperPhpTypes::PHP_STRING,
		'varchar' => dbMapperPhpTypes::PHP_STRING, 
		'text' => dbMapperPhpTypes::PHP_STRING, 
		'name' => dbMapperPhpTypes::PHP_STRING, 
		'filename' => dbMapperPhpTypes::PHP_STRING,
		'bytea' => dbMapperPhpTypes::PHP_STRING,
		'bool' => dbMapperPhpTypes::PHP_BOOLEAN,
		'date' => dbMapperPhpTypes::PHP_STRING,
		'time' => dbMapperPhpTypes::PHP_STRING,
		'abstime' => dbMapperPhpTypes::PHP_TIMESTAMP, 
		'timestamp' => dbMapperPhpTypes::PHP_TIMESTAMP, 
		'timestamptz' => dbMapperPhpTypes::PHP_TIMESTAMP,
		'_bool' => dbMapperPhpTypes::PHP_ARRAY,     
		'_char' => dbMapperPhpTypes::PHP_ARRAY, 
		'_int2' => dbMapperPhpTypes::PHP_ARRAY, 
		'_int4' => dbMapperPhpTypes::PHP_ARRAY, 
		'_text' => dbMapperPhpTypes::PHP_ARRAY,
		'_oid' => dbMapperPhpTypes::PHP_ARRAY, 
		'_varchar' => dbMapperPhpTypes::PHP_ARRAY, 
		'_int8' => dbMapperPhpTypes::PHP_ARRAY, 
		'_float4' => dbMapperPhpTypes::PHP_ARRAY, 
		'_float8' => dbMapperPhpTypes::PHP_ARRAY,
		'_abstime' => dbMapperPhpTypes::PHP_ARRAY, 
		'_date' => dbMapperPhpTypes::PHP_ARRAY, 
		'_time' => dbMapperPhpTypes::PHP_ARRAY, 
		'_timestamp' => dbMapperPhpTypes::PHP_ARRAY, 
		'_numeric' => dbMapperPhpTypes::PHP_ARRAY,
		'_bytea' => dbMapperPhpTypes::PHP_ARRAY,
	);
	
	/**
	 * Maps a default value to the various DB types
	 *
	 * @var array
	 * @access private
	 * @static 
	 */
	private static $_DefaultValue = array(
		'int2' => 0,
		'int4' => 0, 
		'oid' => 0,
		'int8' => 0,
		'cash'  => 0.0,
		'money'  => 0.0,
		'numeric' => 0,
		'float4' => 0.0,
		'float8' => 0.0,
		'bpchar' => '', 
		'char' => '', 
		'char2' => '', 
		'char4' => '', 
		'char8' => '', 
		'char16' => '',
		'varchar' => '', 
		'text' => '', 
		'name' => '', 
		'filename' => '',
		'bytea' => '',
		'bool' => false,
		'date' => 'date(system::getConfig()->getDatabaseDateFormat()->getParamValue())',
		'time' => 'date(system::getConfig()->getDatabaseDateFormat()->getParamValue())',
		'abstime' => 'time()', 
		'timestamp' => 'time()', 
		'timestamptz' => 'time()',
		'_bool' => array(),     
		'_char' => array(), 
		'_int2' => array(), 
		'_int4' => array(), 
		'_text' => array(),
		'_oid' => array(), 
		'_varchar' => array(), 
		'_int8' => array(), 
		'_float4' => array(), 
		'_float8' => array(),
		'_abstime' => array(), 
		'_date' => array(), 
		'_time' => array(), 
		'_timestamp' => array(), 
		'_numeric' => array(),
		'_bytea' => array(),
	);
	
	
	/**
	 * Returns the PHP type for the specified database field type
	 *
	 * @param string $inDbType
	 * @return integer
	 * @static 
	 */
	public static function getPhpType($inDbType) {
		$inDbType = strtolower($inDbType);
		if ( array_key_exists($inDbType, self::$_TypeMap) ) {
			return self::$_TypeMap[$inDbType];
		} else {
			return dbMapperPhpTypes::PHP_STRING;
		}
	}
	
	/**
	 * Returns a default PHP value for the specified type
	 *
	 * @param string $inDbType
	 * @return mixed
	 * @static 
	 */
	public static function getDefaultPhpValue($inDbType) {
		if ( isset(self::$_DefaultValue[$inDbType]) ) {
			return self::$_DefaultValue[$inDbType];
		} else {
			return false;
		}
	}
}