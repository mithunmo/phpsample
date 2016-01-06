#!/usr/bin/php
<?php
/**
 * Brightcove Video Status
 *
 * Stored in videoStatusUploadBrokenThumbnail.php
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
systemLog::message('Initialising Broken Thumbnail Daemon');

/**
 *
 * @package mofilm
 * @subpackage daemons
 * @category videoUpload
 * 
 */
class videoStatusUpdateBrokenThumbnailsDaemon extends cliDaemon {
    
    
	/**
	 * @see cliDaemon::__construct()
	 */
	function __construct() {
		parent::__construct('videoStatusUpdateBrokenThumbnailsDaemon', 'videoStatusUpdateBrokenThumbnailsDaemon');
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
					$return = $this->BCMovieAssets($oMofilmUploadStatus->getMovieID(), $oMofilmUploadStatus->getVideoCloudID());
					systemLog::message($return);
					unset ($return);
				}
			}
			
			$loop = false;
			
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

	function BCMovieAssets($movieID=NULL, $videoCloudID=NULL) {
	    systemLog::message($movieID);
	    systemLog::message($videoCloudID);
		if ( $movieID && $videoCloudID ) {
			$readAPI = 'Ekg-LmhL4QrFPEdtjwJlyX2Zi4l6mgdiPnWGP0bKIyKKT_94PTKHrw..';
			$bc = new BCMAPI($readAPI);
			try {
				$oVideoRenditions = $bc->find('find_video_by_id', array('video_id' => $videoCloudID, 'video_fields' => 'videoStillURL,thumbnailURL,renditions', 'media_delivery' => 'http'));

				$oMovie = new mofilmMovie($movieID);
				$oVideoAssets = $oMovie->getAssetSet()->getObjectByAssetType(mofilmMovieAsset::TYPE_THUMBNAIL)->getIterator();
				foreach ( $oVideoAssets as $oVideoAsset ) {
					$assetParams = $oVideoAsset->toArray();

					if ( $assetParams['_Description'] == 'ThumbNail_640x340' && $assetParams['_CdnURL'] !== strstr($oVideoRenditions->videoStillURL, '?', true) ) {
						systemLog::message('VideoStill Modified -- '.$assetParams['_ID']);
						$assets = new mofilmMovieAsset($assetParams['_ID']);
						$assets->setCdnURL(strstr($oVideoRenditions->videoStillURL, '?', true));
						$assets->save();
						unset ($assets);
					}

					if ( $assetParams['_Description'] == 'ThumbNail_150x84' && $assetParams['_CdnURL'] !== strstr($oVideoRenditions->thumbnailURL, '?', true) ) {
						systemLog::message('Thumbnail Modified -- '.$assetParams['_ID']);
						$assets = new mofilmMovieAsset($assetParams['_ID']);
						$assets->setCdnURL(strstr($oVideoRenditions->thumbnailURL, '?', true));
						$assets->save();
						unset ($assets);
					}
					
					unset ($assetParams);	
				}
				unset ($bc);
				unset ($oMovie);
				unset ($oVideoAsset);
				unset ($oVideoAssets);
				unset ($oVideoRenditions);
				return TRUE;
			} catch (Exception $error) {
				unset ($bc);
				return false;
			}
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
cliProcessControls::initialise($oRequest, 'videoStatusUpdateBrokenThumbnailsDaemon');
cliProcessControls::daemonise();

/*
 * Start up the daemon
 */
$oDaemon = new videoStatusUpdateBrokenThumbnailsDaemon();
$oDaemon->setPosixId(cliProcessControls::getPosixId());
$oDaemon->setPidFile(cliProcessControls::getPidFile());
$oDaemon->setPosixUser(system::getConfig()->getSystemUserId());
$oDaemon->setPosixGroup(system::getConfig()->getSystemGroupGid());
$oDaemon->trapSignal(SIGINT, SIGHUP, SIGTERM); // exit on these signals
$oDaemon->getListeners()->attachListener(new cliApplicationListenerLog());
$oDaemon->execute();
