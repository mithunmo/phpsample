#!/usr/bin/php
<?php
/**
 * reportd
 *
 * Stored in reportd.php
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage daemons
 * @category reportd
 * @version $Rev: 5 $
 */


/*
 * Load dependencies
 */
ini_set('memory_limit', '2048M');
require_once(dirname(dirname(__FILE__)).'/libraries/system.inc');

/*
 * Declare ticks to allow signal handling to be registered
 */
declare(ticks=1);

/*
 * Set our logging
 */
systemLog::getInstance()->setSource('StartUp');
systemLog::message('--------------------------------------------------');
systemLog::message('Initialising Reporting Daemon');

/**
 * reportd
 *
 * Reporting Daemon. Processes reports on a scheduled basis ensuring only one
 * is run at any one time.
 *
 * @package mofilm
 * @subpackage daemons
 * @category reportd
 */
class reportingDaemon extends cliDaemon {
	
	/**
	 * Stores $_LastCacheClear
	 *
	 * @var datetime
	 * @access protected
	 */
	protected $_LastCacheClear;	
	
	
	
	/**
	 * @see cliDaemon::__construct()
	 */
	function __construct() {
		parent::__construct('reportd', 'Reporting Daemon');
	}
	
	/**
	 * @see cliDaemon::execute()
	 */
	function execute() {
		$this->notify(
			new cliApplicationEvent(
				cliApplicationEvent::EVENT_INFORMATIONAL,
				'Entering main process loop',
				null,
				array(
					'log.source' => 'Process'
				)
			)
		);
		$this->setStatusParam('Status', 'Running');
		$this->setLastCacheClear(time());
		$this->getQueueStats();
		$this->updateStatus();
		
		/**
		 * Main daemon loop
		 */
		$loop = true;
		do {
			if ( time() - $this->getLastStatusUpdate() > 60 ) {
				$this->getQueueStats();
				$this->updateStatus();
			}
			
			if ( $this->signalTrapped() ) {
				$loop = false;
			}
			
			$oReport = reportCentreReportQueue::getNextQueuedReport();
			if ( $oReport instanceof reportCentreReportQueue ) {
				$this->notify(
					new cliApplicationEvent(
						cliApplicationEvent::EVENT_INFORMATIONAL,
						'Processing report: '.$oReport->getReportID(),
						null,
						array(
							'log.source' => array(
								'RepID' => $oReport->getReportID(),
								'Sch' => $oReport->getScheduled(),
							)
						)
					)
				);
				
				$com = system::getConfig()->getPathApps().'/ReportGenerator/start.php';
				if ( is_executable($com) ) {
					$com .= ' report '.$oReport->getReportID();
					$this->notify(
						new cliApplicationEvent(
							cliApplicationEvent::EVENT_INFORMATIONAL,
							'Attempting to run: '.$com,
							null
						)
					);
					$results = shell_exec($com);
					if ( strpos($results, '0:') !== 0 ) {
						$this->notify(
							new cliApplicationEvent(
								cliApplicationEvent::EVENT_ERROR,
								'Report failed to run: '.$results
							)
						);
					}
				} else {
					throw new cliApplicationException('ReportGenerator script is not executable');
				}
				
				$oReport->delete();
				
				$this->notify(
					new cliApplicationEvent(
						cliApplicationEvent::EVENT_INFORMATIONAL,
						'Report processing complete',
						null,
						array(
							'log.source' => array()
						)
					)
				);
			}
			$oReport = null;
			unset($oReport);
			
			if ( (time() - $this->getLastStatusUpdate()) >= 60 ) {
				$this->getQueueStats();
				$this->updateStatus();
			} else {
				sleep(1);
			}
			
			if ( time()-$this->getLastCacheClear() > 3600 ) {
				$this->notify(
					new cliApplicationEvent(
						cliApplicationEvent::EVENT_INFORMATIONAL,
						'reportd running cache clear',
						null,
						array(
							'log.source' => 'CacheClear'
						)
					)
				);
				$this->clearCache();
			}
			
			if ( $this->signalTrapped() ) {
				$loop = false;
			}
		} while ( $loop === true );
	}
	
	/**
	 * Gets the number of log messages in the queue, and sets to daemon params
	 *
	 * @return void
	 */
	private function getQueueStats() {
		$oStmt = dbManager::getInstance()->prepare('SELECT COUNT(*) AS Count FROM '.system::getConfig()->getDatabase('reports').'.reportQueue');
		$oStmt->execute();
		$count = $oStmt->fetchColumn();
		$oStmt->closeCursor();
		$oStmt = null;
		
		$this->setStatusParam('Report Queue', $count);
	}
	
	/**
	 * Clears cache files that are old
	 * 
	 * @return void
	 */
	function clearCache() {
		$cachePath = system::getConfig()->getPathTemp().'/reports';
		$files = fileObject::parseDir($cachePath);
		$ttl = (int) system::getConfig()->getParam('reports', 'cacheTTL', 3600*12)->getParamValue();
		if ( count($files) > 0 ) {
			foreach ( $files as $oFile ) {
				if ( time()-$oFile->lastModified() > $ttl ) {
					$this->notify(
						new cliApplicationEvent(
							cliApplicationEvent::EVENT_INFORMATIONAL,
							'Removing file: '.$oFile->getOriginalFilename(),
							null
						)
					);
					@unlink($oFile->getOriginalFilename());
				}
			}
			
			foreach (new DirectoryIterator($cachePath) as $file ) {
				if ( !$file->isDot() && $file->isDir() ) {
					if ( $this->countFilesInDir($cachePath.DIRECTORY_SEPARATOR.$file, 'file') == 0 ) {
						$this->notify(
							new cliApplicationEvent(
								cliApplicationEvent::EVENT_INFORMATIONAL,
								'Removing empty directory: '.$cachePath.DIRECTORY_SEPARATOR.$file,
								null
							)
						);
						rmdir($cachePath.DIRECTORY_SEPARATOR.$file);
					}
				}
			}
		}
		$files = null;
		unset($files);
		
		$this->setLastCacheClear(time());
	}
	
	/**
	 * Returns the file or directory count of $inDir
	 * 
	 * $inType is either "file" or "dir" to fetch only files or directory counts.
	 * This method does not include the dot folders (. & ..) from Unix filesystems.
	 * 
	 * @param string $inDir
	 * @param string $inType Either file or dir
	 * @return integer
	 */
	function countFilesInDir($inDir, $inType = 'file') {
		$file_count = 0;
		$dir_count = 0;
		
		foreach (new DirectoryIterator($inDir) as $file ) {
			if ( !$file->isDot() ) {
				if ( $file->isDir() ) {
					$dir_count++;
				} else {
					$file_count++;
				}
			}
		}
		
		return $inType == 'file' ? $file_count : $dir_count;
	}
	
	/**
	 * @see cliDaemon::terminate()
	 */
	function terminate() {
		$this->notify(
			new cliApplicationEvent(
				cliApplicationEvent::EVENT_INFORMATIONAL,
				'Shutting down reporting daemon...'
			)
		);
		$this->getQueueStats();
		$this->setStatusParam('Status', 'Stopped');
		$this->updateStatus();
		return true;
	}
	
	

	/**
	 * Returns $_LastCacheClear
	 *
	 * @return datetime
	 */
	function getLastCacheClear() {
		return $this->_LastCacheClear;
	}
	
	/**
	 * Set $_LastCacheClear to $inLastCacheClear
	 *
	 * @param datetime $inLastCacheClear
	 * @return reportingDaemon
	 */
	function setLastCacheClear($inLastCacheClear) {
		if ( $inLastCacheClear !== $this->_LastCacheClear ) {
			$this->_LastCacheClear = $inLastCacheClear;
			$this->setModified();
		}
		return $this;
	}
}

/**
 * @var cliRequest $oRequest
 */
$oRequest = cliRequest::getInstance();

/*
 * Allow info and debug logging
 */
$oLog = new cliCommandLog($oRequest);
$oLog->execute();

/*
 * Allow logging output to be dumped to the screen
 */
$oLogToConsole = new cliCommandLogToConsole($oRequest);
$oLogToConsole->execute();



/**
 * Initialise process controls
 */
cliProcessControls::initialise($oRequest, 'reportingDaemon');

/**
 * Attempt to daemonise process
 */
cliProcessControls::daemonise();

/**
 * @var reportingDaemon $oDaemon
 */
$oDaemon = new reportingDaemon();
$oDaemon->setPosixId(cliProcessControls::getPosixId());
$oDaemon->setPidFile(cliProcessControls::getPidFile());
$oDaemon->setPosixUser(system::getConfig()->getSystemUserId());
$oDaemon->setPosixGroup(system::getConfig()->getSystemGroupGid());
$oDaemon->trapSignal(SIGINT, SIGHUP, SIGTERM);
$oDaemon->getListeners()->attachListener(new cliApplicationListenerLog());
$oDaemon->execute();