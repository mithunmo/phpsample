#!/usr/bin/php
<?php
/**
 * Brightcove Video Upload
 *
 * Stored in videoUpload.php
 *
 * @author Pavan Kumar P G
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage daemons
 * @category originUpload
 * @version $Rev: 296 $
 */
/*
 * Load dependencies
 */
require_once(dirname(dirname(__FILE__)) . '/libraries/system.inc');
// require_once(dirname(dirname(__FILE__)) . '/classes/Http/Http.php');

/*
 * Declare ticks to allow signal handling to be registered
 */
declare(ticks = 1);

/*
 * Set our logging
 */
systemLog::getInstance()->setSource('StartUp');
systemLog::message('--------------------------------------------------');
systemLog::message('Initialising Brightcove Video Daemon');

/**
 *
 * @package mofilm
 * @subpackage daemons
 * @category videoUpload
 * 
 */
class videoUploadDaemon extends cliDaemon {

	/**
	 * @see cliDaemon::__construct()
	 */
	function __construct() {
		parent::__construct('videoUploadDaemon', 'videoUploadDaemon');
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

			$oMofilmUploadQueue = mofilmUploadQueue::getMovieFromQueueDesc();

			if ( $oMofilmUploadQueue ) {

				systemLog::message("Processing the movieID " . $oMofilmUploadQueue->getMovieID());
				$oMofilmUploadQueue->setStatus(mofilmUploadQueue::STATUS_PROCESSING);
				$oMofilmUploadQueue->setQueued(date('Y-m-d H:i:s'));
				$oMofilmUploadQueue->save();

				$oMovie = mofilmMovieManager::getInstanceByID($oMofilmUploadQueue->getMovieID());
				$oMovie->getAssetSet()->setMovieID($oMovie->getID());
				systemLog::message("Sending to Video Cloud");
				$return = $this->uploadToVideoCloud($oMovie->getID(), $oMovie->getShortDesc(), $oMovie->getAssetSet()->getFirst()->getFilename());

				if ( $return ) {
					$oMofilmUploadQueue->setStatus(mofilmUploadQueue::STATUS_SENT);
					$oMofilmUploadQueue->save();
					
					$oMofilmUploadStatus = new mofilmUploadStatus();
					$oMofilmUploadStatus->setMovieID($oMofilmUploadQueue->getMovieID());
					$oMofilmUploadStatus->setVideoCloudID($return);
					$oMofilmUploadStatus->setStatus(mofilmUploadStatus::STATUS_PROCESSING);
					$oMofilmUploadStatus->save();
				} else {
					$oMofilmUploadQueue->setStatus(mofilmUploadQueue::STATUS_FAILED);
					$oMofilmUploadQueue->setQueued(date('Y-m-d H:i:s'));
					$oMofilmUploadQueue->save();
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

	function uploadToVideoCloud($Id, $shortDesc, $filename) {
	    try {
		    systemLog::message('-- uploading to video cloud : Start @ '.date('Y-m-d H:i:s'));
		    $readapi = "ENKQgSflymW_ukhqMrK61tAIfECtvftCJYQFVYp6w2gQ60VYbZ-kAw..";
		    $writeapi = "2GgynviHfffnM9X_2rtj8YRJykktDmaerHmLeuYIgoVyQz7LIfq4-w..";

		    $bc = new BCMAPI($readapi, $writeapi);
	    
		    $metaData = array(
				'name' => $shortDesc,
				'referenceId' => $Id
		    );

		    $options = array(
				'create_multiple_renditions' => TRUE,
				'preserve_source_rendition ' => TRUE
		    );
	    
		    // Upload the video and save the video ID
		    $id = $bc->createMedia('video', $filename, $metaData, $options);
		    systemLog::message('-- uploading to video cloud : End @ '.date('Y-m-d H:i:s'));
		    return $id;
	    } catch (Exception $e) {
		    systemLog::message($e);
		    return false;
	    }
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
cliProcessControls::initialise($oRequest, 'videoUploadDaemon');
cliProcessControls::daemonise();

/*
 * Start up the daemon
 */
$oDaemon = new videoUploadDaemon();
$oDaemon->setPosixId(cliProcessControls::getPosixId());
$oDaemon->setPidFile(cliProcessControls::getPidFile());
$oDaemon->setPosixUser(system::getConfig()->getSystemUserId());
$oDaemon->setPosixGroup(system::getConfig()->getSystemGroupGid());
$oDaemon->trapSignal(SIGINT, SIGHUP, SIGTERM); // exit on these signals
$oDaemon->getListeners()->attachListener(new cliApplicationListenerLog());
$oDaemon->execute();
