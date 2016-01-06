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
			
			    systemLog::message('-------- start ----'.  memory_get_usage());
			    $oMofilmUploadStatus = mofilmUploadStatus::getOldEncodingMovies();
			
			    $return = $this->checkUploadStatus($oMofilmUploadStatus->getVideoCloudID(), $oMofilmUploadStatus->getMovieID(), $oMofilmUploadStatus->getID());
			    
			    if ( $return ) {
				$oMofilmUploadStatus->setStatus(mofilmUploadStatus::STATUS_SUCCESS);
				$oMofilmUploadStatus->save();
			    } else {
				$oMofilmUploadStatus->setStatus(mofilmUploadStatus::STATUS_QUEUED);
				$oMofilmUploadStatus->save();
			    }
			    
			    unset ($oMofilmUploadStatus);
			    unset ($return);
			
			if ( $this->signalTrapped() ) {
				$loop = false;
			}
			
			systemLog::message('-------- end ----'.  memory_get_usage());
			
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

	function checkUploadStatus($movieCID = null, $movieID = null, $id = null) {
		try {
			systemLog::message('-- start --'.  memory_get_usage());
			$writeapi = "2GgynviHfffnM9X_2rtj8YRJykktDmaerHmLeuYIgoVyQz7LIfq4-w..";
			$readapi = "Ekg-LmhL4QrFPEdtjwJlyX2Zi4l6mgdiPnWGP0bKIyKKT_94PTKHrw..";

			$bc = new BCMAPI($readapi, $writeapi);
			
			$vStatus = $bc->getStatus('video', $movieCID);
			
			if ( $vStatus == 'COMPLETE' ) {
				try {
					$oVideo = $bc->find('find_video_by_id', array('video_id' => $movieCID, 'media_delivery' => 'http'));
					if ( $oVideo->videoStillURL && $oVideo->thumbnailURL ) {
						
						$thumbnailURL = strstr($oVideo->thumbnailURL, '?', true);
						$videoStillURL = strstr($oVideo->videoStillURL, '?', true);

						$oMofilmMovies = mofilmMovieManager::getInstanceByID($movieID);
						if ( $oMofilmMovies )  {
							$thumbnailAssets = $oMofilmMovies->getAssetSet()->getObjectByAssetType('ThumbNail')->getIterator();

							foreach ( $thumbnailAssets as $oThumbnail ) {
							    $thumbnail = $oThumbnail->toArray();
							    if ( $thumbnail['_Width'] == 150 ) {
								    $asset = mofilmMovieAsset::getInstance($thumbnail['_ID']);
								    $asset->setCdnURL($thumbnailURL);
								    $asset->setModified(date('Y-m-d H:i:s'));
								    $asset->save();
								    unset ($asset);
							    } elseif ( $thumbnail['_Width'] == 300 )  {
								    $asset = mofilmMovieAsset::getInstance($thumbnail['_ID']);
								    $asset->setCdnURL($videoStillURL);
								    $asset->setModified(date('Y-m-d H:i:s'));
								    $asset->save();
								    unset ($asset);
							    } else {
								    $asset = mofilmMovieAsset::getInstance($thumbnail['_ID']);
								    $asset->setWidth(640);
								    $asset->setHeight(340);
								    $asset->setDescription(mofilmMovieAsset::TYPE_THUMBNAIL.'_640x340');
								    $asset->setCdnURL($videoStillURL);
								    $asset->setModified(date('Y-m-d H:i:s'));
								    $asset->save();
								    unset ($asset);
							    }
							    unset ($thumbnail);
							    unset ($oThumbnail);
							}
							unset ($thumbnailAssets);

							if ( $oVideo->FLVURL ) {
								$size = 0;
								$cdnURL = '';
							    	$size = $oVideo->FLVFullLength->size;
								$cdnURL = $oVideo->FLVFullLength->url;
								$videoID = $oVideo->FLVFullLength->id;

								if ( $oVideo->videoFullLength->size > $size ) {
									$size = $oVideo->videoFullLength->size;
									$cdnURL = $oVideo->videoFullLength->url;
									$videoID = $oVideo->videoFullLength->id;
								}

								foreach ($oVideo->renditions as $rendition) {
									if ( $rendition->size > $size ) {
										$size = $rendition->size;
										$cdnURL = $rendition->url;
										$videoID = $rendition->id;
									}
								}
			
							    $oFlvAsset = $oMofilmMovies->getAssetSet()->getObjectByAssetAndFileType('File', 'FLV')->getIterator();

							    foreach ( $oFlvAsset as $oFlvfile ) {
								    $flvfile = $oFlvfile->toArray();
								    $asset = mofilmMovieAsset::getInstance($flvfile['_ID']);
								    $asset->setCdnURL($cdnURL);
								    $asset->setProfileID($videoID);
								    $asset->setModified(date('Y-m-d H:i:s'));
								    $asset->save();
								    unset ($asset);
								    unset ($flvfile);
								    unset ($oFlvfile);
							    }
							}
							unset ($writeapi);
							unset ($readapi);
							unset ($bc);
							unset ($vStatus);
							unset ($oVideo);
							unset ($oMofilmMovies);
							unset ($thumbnailURL);
							unset ($videoStillURL);
							unset ($oFlvAsset);
							unset ($movieCID);
							unset ($movieID);
							unset ($id);
							return true;
						
						} else {
						    
							$oMofilmAsset = new mofilmMovieAsset();
							$oMofilmAsset->setMovieID($movieID);
							$oMofilmAsset->setModified(date('Y-m-d H:i:s'));
							
							$oMofilmAsset->setType(mofilmMovieAsset::TYPE_THUMBNAIL);
							$oMofilmAsset->setExt('JPG');
							
							$oMofilmAsset->setWidth(150);
							$oMofilmAsset->setHeight(84);
							$oMofilmAsset->setDescription(mofilmMovieAsset::TYPE_THUMBNAIL.'_150x84');
							$oMofilmAsset->setCdnURL($thumbnailURL);
							$oMofilmAsset->save();
							
							$oMofilmAsset->setWidth(640);
							$oMofilmAsset->setHeight(340);
							$oMofilmAsset->setDescription(mofilmMovieAsset::TYPE_THUMBNAIL.'_640x340');
							$oMofilmAsset->setCdnURL($videoStillURL);
							$oMofilmAsset->save();
							
							if ( $oVideo->FLVURL ) {
								$size = 0;
								$cdnURL = $oVideo->FLVURL;
								$videoID = $oVideo->id;
								
								if ( $oVideo->FLVFullLength->size > $size ) {
									$size = $oVideo->FLVFullLength->size;
									$cdnURL = $oVideo->FLVFullLength->url;
									$videoID = $oVideo->FLVFullLength->id;
								}

								if ( $oVideo->videoFullLength->size > $size ) {
									$size = $oVideo->videoFullLength->size;
									$cdnURL = $oVideo->videoFullLength->url;
									$videoID = $oVideo->videoFullLength->id;
								}

								foreach ($oVideo->renditions as $rendition) {
									if ( $rendition->size > $size ) {
										$size = $rendition->size;
										$cdnURL = $rendition->url;
										$videoID = $rendition->id;
									}
								}
								
								$oMofilmAsset->setType(mofilmMovieAsset::TYPE_FILE);
								$oMofilmAsset->setExt('FLV');
								$oMofilmAsset->setWidth(640);
								$oMofilmAsset->setHeight(360);
								$oMofilmAsset->setDescription(mofilmMovieAsset::TYPE_FILE.'_640x360');
								$oMofilmAsset->setCdnURL($cdnURL);
								$oMofilmAsset->setProfileID($videoID);
								$oMofilmAsset->save();
							}
							
							
							unset ($oMofilmAsset);
							unset ($writeapi);
							unset ($readapi);
							unset ($bc);
							unset ($vStatus);
							unset ($oVideo);
							unset ($oMofilmMovies);
							unset ($thumbnailURL);
							unset ($videoStillURL);
							unset ($movieCID);
							unset ($movieID);
							unset ($id);
							return true;
						}
					} else {
						unset ($bc);
						unset ($readapi);
						sleep (5);
						return false;
					}
						
				} catch (Exception $e) {
					systemLog::message($e);
					unset ($bc);
					unset ($readapi);
					sleep (5);
					return false;
				}
			} else {
			    unset ($bc);
			    unset ($readapi);
			    sleep (5);
			    return false;
			}
			
		} catch (Exception $e) {
			systemLog::message($e);
			unset ($bc);
			unset ($readapi);
			sleep (5);
			return false;
		}
		systemLog::message('-- end --'.  memory_get_usage());
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
