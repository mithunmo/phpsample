<?php
/**
 * dbMapperPhpTypes.class.php
 * 
 * Stored in dbMapperPhpTypes.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage db
 * @category dbMapperPhpTypes
 * @version $Rev: 707 $
 */


/**
 * dbMapperPhpTypes Class
 * 
 * Maps DB properties to PHP properties, requires a database specific
 * implementation to map the database properties to the PHP properties.
 * 
 * @package scorpio
 * @subpackage db
 * @category dbMapperPhpTypes
 */
abstract class dbMapperPhpTypes {
	
	const PHP_BOOLEAN		= 1;
	const PHP_INTEGER		= 2;
	const PHP_FLOAT			= 3;
	const PHP_STRING		= 4;
	const PHP_ARRAY			= 10;
	const PHP_OBJECT		= 11;
	const PHP_RESOURCE		= 12;
	const PHP_NULL			= 13;
	
	// although not real types they need specific handling for error checking
	const PHP_TIMESTAMP		= 20;
	const PHP_DATETIME		= 21;
	
	
	/**
	 * Array of php types to a textual description
	 *
	 * @var array
	 * @static 
	 * @access protected
	 */
	protected static $_PhpTypeMap = array(
		self::PHP_ARRAY => 'array',
		self::PHP_BOOLEAN => 'boolean',
		self::PHP_FLOAT => 'float',
		self::PHP_INTEGER => 'integer',
		self::PHP_NULL => 'null',
		self::PHP_OBJECT => 'object',
		self::PHP_RESOURCE => 'resource',
		self::PHP_STRING => 'string',
		
		self::PHP_TIMESTAMP => 'timestamp',
		self::PHP_DATETIME => 'datetime',
	);
	
	
	
	/**
	 * Returns the PHP type for the DB type
	 * This function must be extended in the DB Handler
	 *
	 * @param string $inDbType
	 * @return integer
	 * @throws dbMapperException
	 * @abstract 
	 * @static 
	 */
	public static function getPhpType($inDbType) {
		throw new dbMapperException('dbMapperPhpTypes::getType must be extended to get type ('.$inDbType.')');
	}
	
	/**
	 * Returns the PHP types textual description
	 *
	 * @param integer $inPhpType
	 * @return string
	 * @throws dbMapperException
	 * @static 
	 */
	public static function getPhpName($inPhpType) {
		if ( isset(self::$_PhpTypeMap[$inPhpType]) ) {
			return self::$_PhpTypeMap[$inPhpType];
		} else {
			throw new dbMapperException("Unknown PHP Type passed ($inPhpType). This can not be mapped.");
		}
	}
	
	/**
	 * Returns a default PHP value for the specified database type
	 *
	 * @param string $inDbType
	 * @return mixed
	 * @throws dbMapperException
	 * @abstract 
	 * @static 
	 */
	public static function getDefaultPhpValue($inDbType) {
		throw new dbMapperException('dbMapperPhpTypes::getDefaultPhpValue must be extended to get type ('.$inDbType.')');
	}
}