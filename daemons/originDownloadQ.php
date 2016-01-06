#!/usr/bin/php
<?php
 /**
 * newsletterd
 *
 * Stored in newsletterd.class.php
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage daemons
 * @category newsletterd
 * @version $Rev: 296 $
 */


/*
 * Load dependencies
 */
require_once(dirname(dirname(__FILE__)) . '/libraries/system.inc');

/*
 * Declare ticks to allow signal handling to be registered
 */
declare(ticks = 1) ;

/*
 * Set our logging
 */
systemLog::getInstance()->setSource('StartUp');
systemLog::message('--------------------------------------------------');
systemLog::message('Initialising Newsletter Daemon');

/**
 * newsletterd
 *
 * newsletter Daemon. adds newsletter to outbound messages
 *
 *
 * @package mofilm
 * @subpackage daemons
 * @category newsletterd
 */
class originDownloadDaemon extends cliDaemon {

	/**
	 * @see cliDaemon::__construct()
	 */
	function __construct() {
		parent::__construct('originDownloadDaemon', 'originDownload Daemon');
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
		$this->updateStatus();

		$loop = true;
		do {
			if ( $this->signalTrapped() ) {
				$loop = false;
			}
			
			$tempDIr = "/opt/content/resources/movies";
			$oMofilmAssetDownload = mofilmAssetDownloadQ::getAssetToDownload();
			if ( $oMofilmAssetDownload ) {
				$oMofilmMovieAsset = mofilmMovieAsset::getInstance($oMofilmAssetDownload->getAssetID());
				echo "Processing ". $oMofilmMovieAsset->getID();
				echo $oMofilmMovieAsset->getCdnURL();
				print "beginning wget of file from: " ;
				print "saving file to $tempDir" ;
				$startTime = time();
				
				try {
					$return = utilityCurl::downloadFile($oMofilmMovieAsset->getCdnURL(), $tempDIr."/".$oMofilmMovieAsset->getMovieID()."/".basename($oMofilmMovieAsset->getCdnURL()));
					if ( $return ) {
						$oMofilmAssetDownload->delete();
					}
				} 
				catch (Exception $e) {
					echo $e->getMessage();
				}
				
			}
			
			
			if ( $this->signalTrapped() ) {
				$loop = false;
			} else {
				sleep(5);
			}

		} while ( $loop === true );
	}

	/**
	 * @see cliDaemon::terminate()
	 */
	function terminate() {
		$this->setStatusParam('Status', 'Stopped');
		$this->updateStatus();
		return true;
		
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

/*
 * Initialise cli and daemonise
 */
cliProcessControls::initialise($oRequest, 'originDownloadDaemon');
cliProcessControls::daemonise();

/*
 * Start up the daemon
 */
$oDaemon = new originDownloadDaemon();
$oDaemon->setPosixId(cliProcessControls::getPosixId());
$oDaemon->setPidFile(cliProcessControls::getPidFile());
$oDaemon->setPosixUser(system::getConfig()->getSystemUserId());
$oDaemon->setPosixGroup(system::getConfig()->getSystemGroupGid());
$oDaemon->trapSignal(SIGINT, SIGHUP, SIGTERM); // exit on these signals
$oDaemon->getListeners()->attachListener(new cliApplicationListenerLog());
$oDaemon->execute();