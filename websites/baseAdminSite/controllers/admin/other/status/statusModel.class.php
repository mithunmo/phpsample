<?php
/**
 * statusModel.class.php
 * 
 * statusModel class
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category statusModel
 * @version $Rev: 11 $
 */


/**
 * statusModel class
 * 
 * Provides the "status" page
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category statusModel
 */
class statusModel extends mvcModelBase {
	
	/**
	 * Stores $_Daemons
	 *
	 * @var array
	 * @access protected
	 */
	protected $_Daemons;
	
	
	
	/**
	 * @see mvcModelBase::__construct()
	 */
	function __construct() {
		parent::__construct();
		
		$this->_Daemons = array();
	}
	
	/**
	 * Loads daemon information
	 * 
	 * @return void
	 */
	function loadDaemonInfo() {
		$this->setDaemons(cliProcessInformation::getDaemonInformation());
	}

	/**
	 * Returns $_Daemons
	 *
	 * @return array
	 */
	function getDaemons() {
		if ( count($this->_Daemons) == 0 ) {
			$this->loadDaemonInfo();
		}
		return $this->_Daemons;
	}
	
	/**
	 * Set $_Daemons to $inDaemons
	 *
	 * @param array $inDaemons
	 * @return statusModel
	 */
	function setDaemons($inDaemons) {
		if ( $inDaemons !== $this->_Daemons ) {
			$this->_Daemons = $inDaemons;
			$this->setModified();
		}
		return $this;
	}
	
	
	/*
	 * MySQL Stats -- taken from PHPMyAdmin server_status.php
	 */
	
	/**
	 * Returns stats data as an array
	 *
	 * @return array
	 */
	function getStats() {
		$server_status = $this->getGlobalStatus();
		$server_variables = $this->getGlobalVars();
		
		/*
		 * Knicked verbatim from phpMyAdmin server_status.php
		 */
		
		$start_time = $this->getServerStartTime($server_status['Uptime']);
		
		/**
		 * cleanup some deprecated values
		 */
		$deprecated = array(
		    'Com_prepare_sql' => 'Com_stmt_prepare',
		    'Com_execute_sql' => 'Com_stmt_execute',
		    'Com_dealloc_sql' => 'Com_stmt_close',
		);
		
		foreach ($deprecated as $old => $new) {
		    if (isset($server_status[$old])
		      && isset($server_status[$new])) {
		        unset($server_status[$old]);
		    }
		}
		unset($deprecated);
		
		
		/**
		 * calculate some values
		 */
		// Key_buffer_fraction
		if (isset($server_status['Key_blocks_unused'])
		  && isset($server_variables['key_cache_block_size'])
		  && isset($server_variables['key_buffer_size'])) {
		    $server_status['Key_buffer_fraction_%'] =
		        100
		      - $server_status['Key_blocks_unused']
		      * $server_variables['key_cache_block_size']
		      / $server_variables['key_buffer_size']
		      * 100;
		} elseif (
		     isset($server_status['Key_blocks_used'])
		  && isset($server_variables['key_buffer_size'])) {
		    $server_status['Key_buffer_fraction_%'] =
		        $server_status['Key_blocks_used']
		      * 1024
		      / $server_variables['key_buffer_size'];
		  }
		
		// Ratio for key read/write
		if (isset($server_status['Key_writes'])
		    && isset($server_status['Key_write_requests'])
		    && $server_status['Key_write_requests'] > 0)
		        $server_status['Key_write_ratio_%'] = 100 * $server_status['Key_writes'] / $server_status['Key_write_requests'];
		
		if (isset($server_status['Key_reads'])
		    && isset($server_status['Key_read_requests'])
		    && $server_status['Key_read_requests'] > 0)
		        $server_status['Key_read_ratio_%'] = 100 * $server_status['Key_reads'] / $server_status['Key_read_requests'];
		
		// Threads_cache_hitrate
		if (isset($server_status['Threads_created'])
		  && isset($server_status['Connections'])
		  && $server_status['Connections'] > 0) {
		    $server_status['Threads_cache_hitrate_%'] =
		        100
		      - $server_status['Threads_created']
		      / $server_status['Connections']
		      * 100;
		}
		
		
		/**
		 * define some alerts
		 */
		// name => max value before alert
		$alerts = array(
		    // lower is better
		    // variable => max value
		    'Aborted_clients' => 0,
		    'Aborted_connects' => 0,
		
		    'Binlog_cache_disk_use' => 0,
		
		    'Created_tmp_disk_tables' => 0,
		
		    'Handler_read_rnd' => 0,
		    'Handler_read_rnd_next' => 0,
		
		    'Innodb_buffer_pool_pages_dirty' => 0,
		    'Innodb_buffer_pool_reads' => 0,
		    'Innodb_buffer_pool_wait_free' => 0,
		    'Innodb_log_waits' => 0,
		    'Innodb_row_lock_time_avg' => 10, // ms
		    'Innodb_row_lock_time_max' => 50, // ms
		    'Innodb_row_lock_waits' => 0,
		
		    'Slow_queries' => 0,
		    'Delayed_errors' => 0,
		    'Select_full_join' => 0,
		    'Select_range_check' => 0,
		    'Sort_merge_passes' => 0,
		    'Opened_tables' => 0,
		    'Table_locks_waited' => 0,
		    'Qcache_lowmem_prunes' => 0,
		    'Slow_launch_threads' => 0,
		
		    // depends on Key_read_requests
		    // normaly lower then 1:0.01
		    'Key_reads' => (0.01 * $server_status['Key_read_requests']),
		    // depends on Key_write_requests
		    // normaly nearly 1:1
		    'Key_writes' => (0.9 * $server_status['Key_write_requests']),
		
		    'Key_buffer_fraction' => 0.5,
		
		    // alert if more than 95% of thread cache is in use
		    'Threads_cached' => 0.95 * $server_variables['thread_cache_size']
		
		    // higher is better
		    // variable => min value
		    //'Handler read key' => '> ',
		);
		
		
		/**
		 * split variables in sections
		 */
		$allocations = array(
		    // variable name => section
		
		    'Com_'              => 'com',
		    'Innodb_'           => 'innodb',
		    'Ndb_'              => 'ndb',
		    'Ssl_'              => 'ssl',
		    'Handler_'          => 'handler',
		    'Qcache_'           => 'qcache',
		    'Threads_'          => 'threads',
		    'Slow_launch_threads' => 'threads',
		
		    'Binlog_cache_'     => 'binlog_cache',
		    'Created_tmp_'      => 'created_tmp',
		    'Key_'              => 'key',
		
		    'Delayed_'          => 'delayed',
		    'Not_flushed_delayed_rows' => 'delayed',
		
		    'Flush_commands'    => 'query',
		    'Last_query_cost'   => 'query',
		    'Slow_queries'      => 'query',
		
		    'Select_'           => 'select',
		    'Sort_'             => 'sort',
		
		    'Open_tables'       => 'table',
		    'Opened_tables'     => 'table',
		    'Table_locks_'      => 'table',
		
		    'Rpl_status'        => 'repl',
		    'Slave_'            => 'repl',
		
		    'Tc_'               => 'tc',
		);
		
		$sections = array(
		    // section => section name (description)
		    'com'           => array('title' => ''),
		    'query'         => array('title' => 'SQL QUeries'),
		    'innodb'        => array('title' => 'InnoDB'),
		    'ndb'           => array('title' => 'NDB'),
		    'ssl'           => array('title' => 'SSL'),
		    'handler'       => array('title' => 'Handler'),
		    'qcache'        => array('title' => 'Query Cache'),
		    'threads'       => array('title' => 'Threads'),
		    'binlog_cache'  => array('title' => 'Binary Log'),
		    'created_tmp'   => array('title' => 'Temporary Data'),
		    'delayed'       => array('title' => 'Delayed Inserts'),
		    'key'           => array('title' => 'Key Cache'),
		    'select'        => array('title' => 'Joins'),
		    'repl'          => array('title' => 'Replication'),
		    'sort'          => array('title' => 'Sorting'),
		    'table'         => array('title' => 'Tables'),
		    'tc'            => array('title' => 'Transaction Co-ordinator'),
		);
		
		// sort status vars into arrays
		foreach ($server_status as $name => $value) {
		    if (isset($allocations[$name])) {
		        $sections[$allocations[$name]]['vars'][$name] = $value;
		        unset($server_status[$name]);
		    } else {
		        foreach ($allocations as $filter => $section) {
		            if (preg_match('/^' . $filter . '/', $name)
		              && isset($server_status[$name])) {
		                unset($server_status[$name]);
		                $sections[$section]['vars'][$name] = $value;
		            }
		        }
		    }
		}
		unset($name, $value, $filter, $section, $allocations);
		
		// rest
		$sections['all']['vars'] =& $server_status;
		
		$hour_factor    = 3600 / $server_status['Uptime'];
		
		
		return array(
			'sections' => $sections,
			'server_status' => $server_status,
			'server_variables' => $server_variables,
			'start_time' => $start_time,
			'hour_factor' => $hour_factor,
		);
	}
	
	/**
	 * Returns an array of global variables
	 *
	 * @return array(var => value)
	 */
	function getGlobalVars() {
		$oDB = dbManager::getInstance();
		$oRes = $oDB->query('SHOW GLOBAL VARIABLES');
		
		$res = array();
		foreach ( $oRes as $row ) {
			$res[$row['Variable_name']] = $row['Value'];
		}
		return $res;
	}
	
	/**
	 * Returns an array of global status values
	 *
	 * @return array(var => value)
	 */
	function getGlobalStatus() {
		$oDB = dbManager::getInstance();
		$oRes = $oDB->query('SHOW GLOBAL STATUS');
		
		$res = array();
		foreach ( $oRes as $row ) {
			$res[$row['Variable_name']] = $row['Value'];
		}
		return $res;
	}
	
	/**
	 * Returns number of seconds server has been running since Unix Epoch
	 *
	 * @param integer $inUptime
	 * @return integer
	 */
	function getServerStartTime($inUptime) {
		return dbManager::getInstance()->query('SELECT UNIX_TIMESTAMP() - ' . $inUptime.' AS uptime')->fetchColumn();
	}
	
	/**
	 * Returns size in human readable format
	 *
	 * @param float $inNumber
	 * @return string
	 */
	function getNumberFormat($inNumber) {
		return utilityStringFunction::humanReadableSize($inNumber);
	}
	
	/**
	 * Time in seconds returned in human readable format
	 *
	 * @param integer $inTime
	 * @return string
	 */
	function getTimeFormat($inTime) {
		return utilityStringFunction::humanReadableTime($inTime);
	}
}