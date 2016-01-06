<?php
/**
 * dbDriver.class.php
 * 
 * Contains management system for database connections
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage db
 * @category dbDriver
 * @version $Rev: 650 $
 */


/**
 * dbDriver Class
 * 
 * Provides an abstract base for all database specific implementations. This class inherits from
 * PDO and extends the main query functions (exec, query, prepare) to include query logging when
 * systemLog is set to systemLogLevel::DEBUG {@link systemLogLevel}.
 * 
 * Additional properties are increased support for transactions including nested transactions and
 * a database model object ({@link dbUtilities}). This provides a unified set of methods to extract meta-
 * data information from the server including: databases, tables, table structure etc.
 * 
 * dbUtilities is required for the {@link dbMapper} object.
 * 
 * The dbDriver is instantiated via the {@link dbManager} object.
 * 
 * @package scorpio
 * @subpackage db
 * @category dbDriver
 * @abstract 
 */
abstract class dbDriver extends PDO {
	
	/**
	 * Stores $_DbOptions
	 *
	 * @var dbOptions
	 * @access protected
	 */
	protected $_DbOptions				= null;
	/**
	 * Driver identifier
	 *
	 * @var string
	 * @abstract 
	 */
	protected $_DriverName				= 'dbDriver';
	/**
	 * Stores the transaction nesting level.
	 *
	 * @var integer
	 */
	protected $_TransactionLevel		= 0;
	/**
	 * True if transaction error occurs
	 *
	 * @var boolean
	 */
	protected $_TransactionError		= false;
	/**
	 * Characters used to markup SQL identifiers, should be overridden in child classes
	 * where appropriate (e.g. MySQL)
	 * 
	 * @var string
	 * @abstract 
	 */
	protected $_IdentifierQuotes = array(
		"start" => '"',
		"end"   => '"'
	);
	/**
	 * Holds an instance of dbUtilities
	 *
	 * @var dbUtilities
	 * @access protected
	 */
	protected $_DbUtilities = null;
	/**
	 * Stores $_LastQuery
	 *
	 * @var string
	 * @access protected
	 */
	protected $_LastQuery = null;
	/**
	 * Holds an array of data about the number of queries that have been executed
	 *
	 * @var array
	 * @access protected
	 */
	protected $_QueryStats = array(
		'SELECT' => array('count' => 0, 'queries' => array()),
		'INSERT' => array('count' => 0, 'queries' => array()),
		'UPDATE' => array('count' => 0, 'queries' => array()),
		'DELETE' => array('count' => 0, 'queries' => array()),
	);
	
	
	
	/**
	 * Creates a new dbDriver object, requires a dbOptions object
	 *
	 * @param dbOptions $oOptions
	 */
	function __construct(dbOptions $oOptions) {
		if ( !$oOptions instanceof dbOptions ) {
			throw new dbDriverException('Options not instance of dbOptions', 'n/a');
		}
		if ( !$oOptions->getDbDsn() ) {
			$oOptions->setError('PDO DSN string');
		}
		if ( !$oOptions->isValid() ) {
			throw new dbDriverException('dbOptions could not be validated', $oOptions->getError());
		}
		
		$this->setDbOptions($oOptions);
		
		parent::__construct($oOptions->getDbDsn(), $oOptions->getUser(), $oOptions->getPassword(), $oOptions->getOptionalParams());
		$this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$this->_LastQuery = null;
	}
	
	
	
	/**
	 * Returns the current driver name
	 *
	 * @return string
	 */
	public function getDriverName() {
		return $this->_DriverName;
	}
	
	/**
	 * Returns $_DbOptions
	 *
	 * @return dbOptions
	 */
	function getDbOptions() {
		return $this->_DbOptions;
	}
	 
	/**
	 * Sets $_DbOptions to $inDbOptions
	 *
	 * @param dbOptions $inDbOptions
	 * @return dbDriver
	 */
	function setDbOptions($inDbOptions) {
		if ( $this->_DbOptions !== $inDbOptions ) {
			$this->_DbOptions = $inDbOptions;
			$this->_Modified = true;
		}
		return $this;
	}
	
	
	
	/**
	 * Begins a transaction.
	 *
	 * This method executes a begin transaction query unless a
	 * transaction has already been started (transaction nesting level > 0 )
	 *
	 * Each call to begin() must have a corresponding commit() or rollback() call.
	 *
	 * @see dbDriver::commit()
	 * @see dbDriver::rollback()
	 * @return boolean
	 */
	public function beginTransaction() {
		$retval = true;
		if ( $this->_TransactionLevel == 0 ) {
			$retval = parent::beginTransaction();
		}
	
		$this->_TransactionLevel++;
		return $retval;
	}
	
	/**
	 * Commits a transaction.
	 *
	 * If this this call to commit corresponds to the outermost call to
	 * begin() and all queries within this transaction were successful,
	 * a commit query is executed. If one of the queries
	 * returned with an error, a rollback query is executed instead.
	 *
	 * This method returns true if the transaction was successful. If the
	 * transaction failed and rollback was called, false is returned.
	 *
	 * @see dbDriver::beginTransaction()
	 * @see dbDriver::rollback()
	 * @return boolean
	 */
	public function commit() {
		if ( $this->_TransactionLevel <= 0 ) {
			$this->_TransactionLevel = 0;
			throw new dbDriverTransactionException("commit() called before beginTransaction().");
		}
	
		$retval = true;
		if ( $this->_TransactionLevel == 1 ) {
			if ( $this->_TransactionError ) {
				parent::rollback();
				$this->_TransactionError = false; // reset error flag
				$retval = false;
			} else {
				parent::commit();
			}
		}
	
		$this->_TransactionLevel--;
		return $retval;
	}
	
	/**
	 * Rollback a transaction.
	 *
	 * If this this call to rollback corresponds to the outermost call to
	 * begin(), a rollback query is executed. If this is an inner transaction
	 * (nesting level > 1) the error flag is set, leaving the rollback to the
	 * outermost transaction.
	 *
	 * This method always returns true.
	 *
	 * @see dbDriver::beginTransaction()
	 * @see dbDriver::commit()
	 * @return boolean
	 */
	public function rollback() {
		if ( $this->_TransactionLevel <= 0 ) {
			$this->_TransactionLevel = 0;
			throw new dbDriverTransactionException("rollback() called without previous beginTransaction().");
		}
	
		if ( $this->_TransactionLevel == 1 ) {
			parent::rollback();
			$this->_TransactionError = false; // reset error flag
		} else {
			// set the error flag, so that if there is outermost commit
			// then ROLLBACK will be done instead of COMMIT
			$this->_TransactionError = true;
		}
	
		$this->_TransactionLevel--;
		return true;
	}
	
	/**
	 * Prepare and execute $inSql; returns the statement object for iteration
	 *
	 * @param string $inSql
	 * @param integer $inFetchMode
	 * @return PDOStatement
	 */
	function query($inSql, $inFetchMode = PDO::FETCH_ASSOC) {
		$this->logQuery($inSql);
		return parent::query($inSql, $inFetchMode);
	}
	
	/**
	 * Execute a query that does not return a row set, returning the number of rows affected
	 *
	 * @param string $inSql
	 * @return long
	 */
	function exec($inSql) {
		$this->logQuery($inSql);
		return parent::exec($inSql);
	}
	
	/**
	 * Prepares a statement for execution and returns a statement object
	 *
	 * @param string $inStatement
	 * @param array $inOptions
	 * @return PDOStatement
	 */
	function prepare($inStatement, $inOptions = array()) {
		$this->logQuery($inStatement);
		return parent::prepare($inStatement, $inOptions);
	}
	
	/**
	 * Returns $_LastQuery
	 *
	 * @return string
	 */
	function getLastQuery() {
		return $this->_LastQuery;
	}
	
	/**
	 * Returns $_LastQuery
	 *
	 * @param string $inLastQuery
	 * @return dbDriver
	 */
	protected function setLastQuery($inLastQuery) {
		if ( $inLastQuery !== $this->_LastQuery ) {
			$this->_LastQuery = $inLastQuery;
		}
		return $this;
	}
	
	/**
	 * Logs the query out at debug level and sets the last query run
	 *
	 * @param string $inQuery
	 * @return void
	 */
	protected function logQuery($inQuery) {
		$this->setLastQuery($inQuery);
		
		if ( $this->getDbOptions()->areStatsEnabled() ) {
			$inQuery = trim($inQuery);
			systemLog::debug($inQuery);
			
			switch (true) {
				case stripos($inQuery, 'SELECT') === 0:
					$this->_QueryStats['SELECT']['count']++;
					$this->_QueryStats['SELECT']['queries'][] = $inQuery;
					break;
					
				case stripos($inQuery, 'INSERT') === 0:
					$this->_QueryStats['INSERT']['count']++;
					$this->_QueryStats['INSERT']['queries'][] = $inQuery;
					break;
					
				case stripos($inQuery, 'UPDATE') === 0:
					$this->_QueryStats['UPDATE']['count']++;
					$this->_QueryStats['UPDATE']['queries'][] = $inQuery;
					break;
					
				case stripos($inQuery, 'DELETE') === 0:
					$this->_QueryStats['DELETE']['count']++;
					$this->_QueryStats['DELETE']['queries'][] = $inQuery;
					break;
			}
		}
	}
	
	/**
	 * Returns the array of query stats
	 *
	 * @return array
	 */
	public function getQueryStats() {
		return $this->_QueryStats;
	}
	
	/**
	 * Quotes the identifier with the DB specific markup allowing string to be safely used
	 * 
	 * @param string $inIdentifier
	 * @return string
	 */
	public function quoteIdentifier($inIdentifier) {
		if ( count($this->_IdentifierQuotes) === 2 ) {
			$inIdentifier = $this->_IdentifierQuotes["start"].str_replace($this->_IdentifierQuotes["end"], $this->_IdentifierQuotes["end"].$this->_IdentifierQuotes["end"], $inIdentifier).$this->_IdentifierQuotes["end"];
		}
		return $inIdentifier;
	}
	
	/**
	 * Returns a new dbUtility object that contains methods for interrogating the database
	 *
	 * @return dbUtilities
	 * @abstract 
	 */
	public abstract function getDbUtilities();
}