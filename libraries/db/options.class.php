<?php
/**
 * dbOptions.class.php
 * 
 * Contains management system for database connections
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage db
 * @category dbOptions
 * @version $Rev: 650 $
 */


/**
 * dbOptions Class
 * 
 * Holds DSN parsing methods, connection properties etc. Many of these can be set
 * automatically by providing a DSN string e.g. phptype://username:pword@hostspec/database_name
 * 
 * Additionally custom properties for the database can be assigned into the options via
 * {@link dbOptions::setParam()}.
 * 
 * @package scorpio
 * @subpackage db
 * @category dbOptions
 * @final  
 */
final class dbOptions extends baseSet {
	
	const PARAM_DATABASE = 'db.database';
	const PARAM_DSN = 'original.dsn';
	const PARAM_DB_DSN = 'db.dsn';
	const PARAM_DB_HOST = 'db.host';
	const PARAM_DB_PASSWORD = 'db.password';
	const PARAM_DB_PORT = 'db.port';
	const PARAM_DB_PROTOCOL = 'db.protocol';
	const PARAM_DB_SOCKET = 'db.socket';
	const PARAM_DB_SYNTAX = 'db.syntax';
	const PARAM_DB_TYPE = 'db.type';
	const PARAM_DB_USER = 'db.user';
	const PARAM_DB_SQLITE_VERSION = 'db.sqlite.version';
	const PARAM_DB_STATS = 'db.stats.enabled';
	
	/**
	 * Holds the error message from validating options
	 *
	 * @var string
	 */
	protected $_Error = '';
	/**
	 * Flag toggling if this options object is valid
	 *
	 * @var boolean
	 */
	protected $_Valid = false;
	
	
	
	/**
	 * Returns a new dbOptions object
	 *
	 * @param string $inDsn
	 * @return dbOptions
	 */
	function __construct($inDsn = null) {
		if ( $inDsn !== null ) {
			$this->setDsn($inDsn);
			$this->parseDsn();
		}
	}
	
	
	
	/**
	 * Returns a new instance of dbOptions from the supplied DSN, throws an exception if DSN can not be parsed
	 *
	 * @param string $inDsn
	 * @return dbOptions
	 * @throws dbOptionsException
	 */
	public static function getInstance($inDsn = null) {
		try {
			$oOptions = new dbOptions($inDsn);
			if ( $oOptions->isValid() ) {
				return $oOptions;
			} else {
				throw new dbOptionsException('Error with dbOptions::getInstance. '.$oOptions->getError());
			}
		} catch ( dbException $e ) {
			throw $e;
		}
		return new dbOptions();
	}
	
	
	
	/**
	 * Main Methods
	 */
	
	/**
	 * Parses a DSN string into parameters
	 * 
	 * The format of the supplied DSN is in its fullest form:
     * <code>
     *  phptype(dbsyntax)://username:password@protocol+hostspec/database?option=8&another=true
     * </code>
     *
     * Most variations are allowed:
     * <code>
     *  phptype://username:password@protocol+hostspec:110//usr/db_file.db?mode=0644
     *  phptype://username:password@hostspec/database_name
     *  phptype://username:password@hostspec
     *  phptype://username@hostspec
     *  phptype://hostspec/database
     *  phptype://hostspec
     *  phptype(dbsyntax)
     *  phptype
     * </code>
	 *
	 * This function is 'borrowed' from PEAR /DB.php and ezComponents database/factory class.
     *
     * @param string $dsn Data Source Name to be parsed
	 * @return string
	 */
	function parseDsn() {
		if ( $this->getDsn() ) {
			$dsn = $this->getDsn();
			// Find phptype and dbsyntax
			if ( ($pos = strpos($dsn, '://')) !== false ) {
				$str = substr( $dsn, 0, $pos );
				$dsn = substr( $dsn, $pos + 3 );
			} else {
				$str = $dsn;
				$dsn = null;
			}
			
			// Get phptype and dbsyntax
			// $str => phptype(dbsyntax)
			$arr = array();
			if ( preg_match('|^(.+?)\((.*?)\)$|', $str, $arr) ) {
				$this->setDbType($arr[1]);
				$this->setDbSyntax(!$arr[2] ? $arr[1] : $arr[2]);
			} else {
				$this->setDbType($str);
				$this->setDbSyntax($str);
			}
			
			// Get (if found): username and password
			// $dsn => username:password@protocol+hostspec/database
			if ( ($at = strrpos((string) $dsn, '@')) !== false ) {
				$str = substr( $dsn, 0, $at );
				$dsn = substr( $dsn, $at + 1 );
				if ( ( $pos = strpos( $str, ':' ) ) !== false ) {
					$this->setUser(rawurldecode(substr($str, 0, $pos)));
					$this->setPassword(rawurldecode(substr($str, $pos + 1 )));
				} else {
					$this->setUser(rawurldecode($str));
				}
			}
			
			// Find protocol and hostspec
			$match = array();
			if ( preg_match('|^([^(]+)\((.*?)\)/?(.*?)$|', $dsn, $match) ) {
				// $dsn => proto(proto_opts)/database
				$proto       = $match[1];
				$proto_opts  = $match[2] ? $match[2] : false;
				$dsn         = $match[3];
			} else {
				// $dsn => protocol+hostspec/database (old format)
				if ( strpos($dsn, '+') !== false ) {
					list($proto, $dsn) = explode('+', $dsn, 2);
				}
				if ( strpos( $dsn, '/' ) !== false ) {
					list($proto_opts, $dsn) = explode('/', $dsn, 2);
				} else {
					$proto_opts = $dsn;
					$dsn = null;
				}
			}
			
			// process the different protocol options
			$this->setProtocol((!empty($proto)) ? $proto : 'tcp');
			$proto_opts = rawurldecode($proto_opts);
			switch ( $this->getProtocol() ) {
				case 'tcp':
					if ( strpos($proto_opts, ':') !== false ) {
						list($host, $port) = explode(':', $proto_opts);
						$this->setHost($host);
						$this->setPort($port);
					} else {
						$this->setHost($proto_opts);
					}
				break;
					
				case 'unix':
					$this->setSocket($proto_opts);
				break;
			}
			
			// Get database if any
			// $dsn => database
			if ( $dsn ) {
				if ( ($pos = strpos($dsn, '?')) === false ) {
					// /database
					$this->setDatabase(rawurldecode($dsn));
				} else {
					// /database?param1=value1&param2=value2
					$this->setDatabase(rawurldecode(substr($dsn, 0, $pos)));
					$dsn = substr($dsn, $pos + 1);
					if ( strpos($dsn, '&') !== false ) {
						$opts = explode( '&', $dsn );
					} else { // database?param1=value1
						$opts = array($dsn);
					}
					foreach ( $opts as $opt ) {
						list($key, $value) = explode('=', $opt);
						if ( !$this->getParam($key) ) {
							// don't allow params overwrite
							if ( $this->getDbType() == 'sqlite' ) {
								if ( $key == 'version' ) {
									 $this->setParam(self::PARAM_DB_SQLITE_VERSION, $value);
									 continue;
								}
							}
							$this->setParam($key, rawurldecode($value));
						}
					}
				}
			}
			$this->validateOptions();
		} else {
			throw new dbOptionsException('No DSN supplied to parse');
		}
	}
	
	/**
	 * Validates the object to make sure we can connect
	 *
	 * @return void
	 */
	function validateOptions() {
		$this->setValid(true);
		if ( !$this->getDsn() ) {
			$this->setError('DSN');
		}
		if ( !$this->getDbType() ) {
			$this->setError('dbType');
		}
		if ( !$this->getHost() && !$this->getSocket() ) {
			$this->setError('one of host or socket');
		}
		if ( !$this->getDatabase() ) {
			$this->setError('database');
		}
	}
	
	/**
	 * Resets the dbOptions object
	 *
	 * @return dbOptions
	 */
	function reset() {
		$this->_Valid = false;
		$this->_Error = '';
		return parent::_resetSet();
	}
	
	
	
	/**
	 * Get the param
	 *
	 * @param string $inParam
	 * @return mixed
	 */
	function getParam($inParam) {
		return $this->_getItem($inParam);
	}
	
	/**
	 * Returns all params that are not part of the main params
	 *
	 * @return array
	 */
	function getOptionalParams() {
		$ignore = array(
			self::PARAM_DATABASE,
			self::PARAM_DSN,
			self::PARAM_DB_DSN,
			self::PARAM_DB_HOST,
			self::PARAM_DB_PASSWORD,
			self::PARAM_DB_PORT,
			self::PARAM_DB_PROTOCOL,
			self::PARAM_DB_SOCKET,
			self::PARAM_DB_SYNTAX,
			self::PARAM_DB_TYPE,
			self::PARAM_DB_USER,
			self::PARAM_DB_SQLITE_VERSION,
			self::PARAM_DB_STATS,
		);
		
		$params = $this->_getItem();
		$return = array();
		foreach ( $params as $name => $value ) {
			if ( !in_array($name, $ignore) ) {
				$return[$name] = $value;
			}
		}
		return $return;
	}
	
	/**
	 * Set db param value
	 *
	 * @param string $inParam
	 * @param mixed $inValue
	 */
	function setParam($inParam, $inValue) {
		return $this->_setItem($inParam, $inValue);
	}
	
	/**
	 * Returns the error message from validation
	 *
	 * @return string
	 */
	function getError() {
		return $this->_Error;
	}
	
	/**
	 * Set the error message, triggers the dbOptions to be invalid
	 *
	 * @param string $string
	 */
	function setError($inString) {
		if ( !$this->_Error ) {
			$this->_Error = 'Error in dbOptions, missing the following properties:';
		}
		$this->_Error .= ' '.$inString.',';
		$this->setValid(false);
	}
	
	/**
	 * Returns true if dbOptions are valid
	 *
	 * @return boolean
	 */
	function isValid() {
		return $this->_Valid;
	}
	
	/**
	 * Set the valid status
	 *
	 * @param boolean $inStat
	 * @return dbOptions
	 * @access protected
	 */
	protected function setValid($inStat = false) {
		if ( $inStat !== $this->_Valid ) {
			$this->_Valid = $inStat;
		}
		return $this;
	}
	
	/**
	 * Returns the current DSN
	 *
	 * @return string
	 */
	function getDsn() {
		return $this->getParam(self::PARAM_DSN);
	}
	
	/**
	 * Set the DSN string
	 *
	 * @param string $inDsn
	 * @return dbOptions
	 */
	function setDsn($inDsn) {
		return $this->setParam(self::PARAM_DSN, $inDsn);
	}
	
	/**
	 * Returns the database specific PDO DSN string
	 *
	 * @return string
	 */
	function getDbDsn() {
		return $this->getParam(self::PARAM_DB_DSN);
	}
	
	/**
	 * Set the PDO db DSN string
	 *
	 * @param string $dsn
	 * @return dbOptions
	 */
	function setDbDsn($inDsn) {
		return $this->setParam(self::PARAM_DB_DSN, $inDsn);
	}
	
	/**
	 * Returns the current dbType
	 *
	 * @return string
	 */
	function getDbType() {
		return $this->getParam(self::PARAM_DB_TYPE);
	}
	
	/**
	 * Set the db type
	 *
	 * @param string $inType
	 * @return dbOptions
	 */
	function setDbType($inType) {
		return $this->setParam(self::PARAM_DB_TYPE, $inType);
	}
	
	/**
	 * Returns the current dbSyntax
	 *
	 * @return string
	 */
	function getDbSyntax() {
		return $this->getParam(self::PARAM_DB_SYNTAX);
	}
	
	/**
	 * Set the db syntax
	 *
	 * @param string $inSyntax
	 * @return dbOptions
	 */
	function setDbSyntax($inSyntax) {
		return $this->setParam(self::PARAM_DB_SYNTAX, $inSyntax);
	}
	
	/**
	 * Return the current connection protocol
	 *
	 * @return string
	 */
	function getProtocol() {
		return $this->getParam(self::PARAM_DB_PROTOCOL);
	}
	
	/**
	 * Set the protocol
	 *
	 * @param string $inProtocol
	 * @return dbOptions
	 */
	function setProtocol($inProtocol) {
		return $this->setParam(self::PARAM_DB_PROTOCOL, $inProtocol);
	}

	/**
	 * Return the db host
	 *
	 * @return string
	 */
	function getHost() {
		return $this->getParam(self::PARAM_DB_HOST);
	}
	
	/**
	 * Set the database host
	 *
	 * @param string $inHost
	 * @return dbOptions
	 */
	function setHost($inHost) {
		return $this->setParam(self::PARAM_DB_HOST, $inHost);
	}
	
	/**
	 * Return the db port
	 *
	 * @return integer
	 */
	function getPort() {
		return $this->getParam(self::PARAM_DB_PORT);
	}
	
	/**
	 * Set the database port
	 *
	 * @param integer $inPort
	 * @return dbOptions
	 */
	function setPort($inPort) {
		return $this->setParam(self::PARAM_DB_PORT, $inPort);
	}
	
	/**
	 * Returns the socket location
	 *
	 * @return string
	 */
	function getSocket() {
		return $this->getParam(self::PARAM_DB_SOCKET);
	}
	
	/**
	 * Set the socket location
	 *
	 * @param string $inSocket
	 * @return dbOptions
	 */
	function setSocket($inSocket) {
		return $this->setParam(self::PARAM_DB_SOCKET, $inSocket);
	}
	
	/**
	 * Return the connection user
	 *
	 * @return string
	 */
	function getUser() {
		return $this->getParam(self::PARAM_DB_USER);
	}
	
	/**
	 * Set the database connection user
	 *
	 * @param string $inUser
	 * @return dbOptions
	 */
	function setUser($inUser) {
		return $this->setParam(self::PARAM_DB_USER, $inUser);
	}
	
	/**
	 * Return the connection users password
	 *
	 * @return string
	 */
	function getPassword() {
		return $this->getParam(self::PARAM_DB_PASSWORD);
	}
	
	/**
	 * Set the database connection password
	 *
	 * @param string $inPassword
	 * @return dbOptions
	 */
	function setPassword($inPassword) {
		return $this->setParam(self::PARAM_DB_PASSWORD, $inPassword);
	}
	
	/**
	 * Return default database
	 *
	 * @return string
	 */
	function getDatabase() {
		return $this->getParam(self::PARAM_DATABASE);
	}
	
	/**
	 * Set the default database
	 *
	 * @param string $inDatabase
	 * @return dbOptions
	 */
	function setDatabase($inDatabase) {
		return $this->setParam(self::PARAM_DATABASE, $inDatabase);
	}

	/**
	 * Return if debug mode is enabled
	 *
	 * @return boolean
	 */
	function areStatsEnabled() {
		return $this->getParam(self::PARAM_DB_STATS);
	}
	
	/**
	 * Enable or disable query stats / logging
	 *
	 * @param boolean $inStatus
	 * @return dbOptions
	 */
	function setStats($inStatus = false) {
		return $this->setParam(self::PARAM_DB_STATS, $inStatus);
	}
}