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
		parent::__construct('videoStatusDaemon', 'videoStatusDaemon');
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
			
			$oMofilmUploadStatusArray = mofilmUploadStatus::getSuccessMovies();
			foreach ( $oMofilmUploadStatusArray as $oMofilmUploadStatus) {
				if ( $oMofilmUploadStatus ) {
			    		systemLog::message('---');
					$return = $this->checkUploadStatus($oMofilmUploadStatus);
					systemLog::message($return);
				}
				sleep(5);
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

	function checkUploadStatus($oMofilmUploadStatus) {
		try {
			systemLog::message('-- check upload status of video : '.$oMofilmUploadStatus->getVideoCloudID());
			systemLog::message('-- check upload status of video : '.$oMofilmUploadStatus->getMovieID());
			
			$writeapi = "2GgynviHfffnM9X_2rtj8YRJykktDmaerHmLeuYIgoVyQz7LIfq4-w..";
			$readapi = "Ekg-LmhL4QrFPEdtjwJlyX2Zi4l6mgdiPnWGP0bKIyKKT_94PTKHrw..";

			$bc = new BCMAPI($readapi, $writeapi);
			
			$movieCID = $oMofilmUploadStatus->getVideoCloudID();
			$movieID = $oMofilmUploadStatus->getMovieID();
			
			$vStatus = $bc->getStatus('video', $movieCID);
			systemLog::message('-- -- -- -- -- -- -- -- : '.$vStatus);

			$vStatus = 'COMPLETE';
			if ( $vStatus == 'COMPLETE' ) {
				try {
					$oVideo = $bc->find('find_video_by_id', array('video_id' => $movieCID, 'media_delivery' => 'http'));
						if ( $oVideo->FLVURL ) {
							$this->saveMovieAsset($movieID, $oVideo->FLVURL, 640, 360, 'File');
							return true;
						} else {
							unset ($bc);
							unset ($readapi);
							sleep (10);
							return false;
						}
						
				} catch (Exception $e) {
					systemLog::message($e);
					unset ($bc);
					unset ($readapi);
					sleep (15);
					return false;
				}
			} else {
			    unset ($bc);
			    unset ($readapi);
			    sleep (10);
			    return false;
			}
			
		} catch (Exception $e) {
			systemLog::message($e);
			unset ($bc);
			unset ($readapi);
			sleep (15);
			return false;
		}
	}
	
	function saveMovieAsset($movieID = NULL, $cdnURL = NULL, $height = NULL, $width = NULL, $type = NULL) {
		systemLog::message('-- in save movie asset : start --');
		$asset = new mofilmMovieAsset();
		$asset->setMovieID($movieID);
		$asset->setType($type);
		$asset->setExt('FLV');
		$asset->setDescription('FlashVideo_'.$height.'x'.$width);	
		$asset->setWidth($height);
		$asset->setHeight($width);
		$asset->setCdnURL($cdnURL);
		$asset->setModified(date('Y-m-d H:i:s'));
		
		$oMovie = mofilmMovieManager::getInstanceByID($movieID);
		$oMovie->getAssetSet()->setObject($asset);
		$oMovie->save();
		systemLog::message('-- in save movie asset : end --');
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
