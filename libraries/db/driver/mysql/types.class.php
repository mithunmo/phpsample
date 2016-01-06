<?php
/**
 * dbDriverMySqlTypes.class.php
 * 
 * Contains database to PHP type definitions for use in generator and dbMapper
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage db
 * @category dbDriverMySqlTypes
 * @version $Rev: 707 $
 */


/**
 * dbDriverMySqlTypes Class
 * 
 * dbDriverMySqlTypes definition
 * 
 * @package scorpio
 * @subpackage db
 * @category dbDriverMySqlTypes
 */
class dbDriverMySqlTypes extends dbMapperPhpTypes {
	
	/**
	 * Map DB type to a PHP type
	 *
	 * @var array
	 * @access private
	 * @static 
	 */
	private static $_TypeMap = array(
		'tinyint' => dbMapperPhpTypes::PHP_INTEGER,
		'smallint' => dbMapperPhpTypes::PHP_INTEGER,
		'mediumint' => dbMapperPhpTypes::PHP_INTEGER,
		'int' => dbMapperPhpTypes::PHP_INTEGER,
		'integer' => dbMapperPhpTypes::PHP_INTEGER,
		'bigint' => dbMapperPhpTypes::PHP_INTEGER,
		'int24' => dbMapperPhpTypes::PHP_INTEGER,
		'real' => dbMapperPhpTypes::PHP_STRING,
		'float' => dbMapperPhpTypes::PHP_FLOAT,
		'decimal' => dbMapperPhpTypes::PHP_FLOAT,
		'numeric' => dbMapperPhpTypes::PHP_INTEGER,
		'double' => dbMapperPhpTypes::PHP_FLOAT,
		'char' => dbMapperPhpTypes::PHP_STRING,
		'varchar' => dbMapperPhpTypes::PHP_STRING,
		'date' => dbMapperPhpTypes::PHP_STRING,
		'time' => dbMapperPhpTypes::PHP_STRING,
		'year' => dbMapperPhpTypes::PHP_INTEGER,
		'datetime' => dbMapperPhpTypes::PHP_DATETIME,
		'timestamp' => dbMapperPhpTypes::PHP_TIMESTAMP,
		'tinyblob' => dbMapperPhpTypes::PHP_STRING,
		'blob' => dbMapperPhpTypes::PHP_STRING,
		'mediumblob' => dbMapperPhpTypes::PHP_STRING,
		'longblob' => dbMapperPhpTypes::PHP_STRING,
		'longtext' => dbMapperPhpTypes::PHP_STRING,
		'tinytext' => dbMapperPhpTypes::PHP_STRING,
		'mediumtext' => dbMapperPhpTypes::PHP_STRING,
		'text' => dbMapperPhpTypes::PHP_STRING,
		'enum' => dbMapperPhpTypes::PHP_STRING,
		'set' => dbMapperPhpTypes::PHP_STRING,
	);
	
	/**
	 * Maps a default value to the various DB types
	 *
	 * @var array
	 * @access private
	 * @static 
	 */
	private static $_DefaultValue = array(
		'tinyint' => 0,
		'smallint' => 0,
		'mediumint' => '0',
		'int' => 0,
		'integer' => 0,
		'bigint' => '0',
		'int24' => '0',
		'real' => '0',
		'float' => 0.0,
		'decimal' => 0.0,
		'numeric' => 0,
		'double' => 0.0,
		'char' => '',
		'varchar' => '',
		'date' => 'date(system::getConfig()->getDatabaseDateFormat()->getParamValue())',
		'time' => 'date(system::getConfig()->getDatabaseTimeFormat()->getParamValue())',
		'year' => 'date("Y")',
		'datetime' => 'date(system::getConfig()->getDatabaseDatetimeFormat()->getParamValue())',
		'timestamp' => 'date(system::getConfig()->getDatabaseDatetimeFormat()->getParamValue())',
		'tinyblob' => '',
		'blob' => '',
		'mediumblob' => '',
		'longblob' => '',
		'longtext' => '',
		'tinytext' => '',
		'mediumtext' => '',
		'text' => '',
		'enum' => '',
		'set' => '',
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