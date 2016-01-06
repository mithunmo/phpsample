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
			
			$oMofilmUploadStatusArray = mofilmUploadStatus::getEncodingMovies();
			foreach ( $oMofilmUploadStatusArray as $oMofilmUploadStatus) {
				if ( $oMofilmUploadStatus ) {
			    		systemLog::message('---');
					$return = $this->checkUploadStatus($oMofilmUploadStatus);
					systemLog::message($return);
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

	function checkUploadStatus($oMofilmUploadStatus) {
		try {
			systemLog::message('-- check upload status of video : '.$oMofilmUploadStatus->getVideoCloudID());
			
			$readapi = system::getConfig()->getParam('mofilmvideoupload', 'readapi2')->getParamValue();
			$writeapi = system::getConfig()->getParam('mofilmvideoupload', 'writeapi2')->getParamValue();

			$bc = new BCMAPI($readapi, $writeapi);
			
			$movieCID = $oMofilmUploadStatus->getVideoCloudID();
			$movieID = $oMofilmUploadStatus->getMovieID();
			
			$vStatus = $bc->getStatus('video', $movieCID);
			systemLog::message('-- -- -- -- -- -- -- -- : '.$vStatus);

			if ( $vStatus == 'COMPLETE' ) {
				try {
					$oVideo = $bc->find('find_video_by_id', array('video_id' => $movieCID, 'media_delivery' => 'http'));
					
					if ( $oVideo->videoStillURL && $oVideo->thumbnailURL ) {
					    
						$thumbnailURL = strstr($oVideo->thumbnailURL, '?', true);
						$videoStillURL = strstr($oVideo->videoStillURL, '?', true);
						
						$this->saveMovieAsset($movieID, $videoStillURL, 640, 340, 'ThumbNail');
						//$this->saveMovieAsset($movieID, $videoStillURL, 300, 169, 'ThumbNail');
						$this->saveMovieAsset($movieID, $thumbnailURL, 150, 84, 'ThumbNail');
						
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

							if ( $cdnURL ) {
								$this->saveMovieAsset($movieID, $cdnURL, 640, 360, 'File', $videoID);
							}
						}
						
						$movieID = $oMofilmUploadStatus->getMovieID();
					
						$oMofilmUploadStatus->setStatus(mofilmUploadStatus::STATUS_SUCCESS);
						$oMofilmUploadStatus->save();

						$oMofilmMovies = mofilmMovieManager::getInstanceByID($movieID);
						$oMofilmMovies->setStatus(mofilmMovie::STATUS_PENDING);
						$oMofilmMovies->setRuntime($oVideo->length/1000);
						$oMofilmMovies->save();
				
						//$this->sendEmail(mofilmMovieManager::getInstanceByID($movieID)->getUserID(), $movieID, $oMofilmMovies->getSource()->getEvent()->getName(), $oMofilmMovies->getSource()->getName(), $oMofilmMovies->getSource()->getID());
						 $params = array('http' => array(
                                                                                    'method' => 'POST',
                                                                                    'header'  => 'Content-type: application/x-www-form-urlencoded',
                                                                                    'content' => 'userID='.mofilmMovieManager::getInstanceByID($movieID)->getUserID().'&movieID='. $movieID
                                                                            )
                                                                );

                                                $context = stream_context_create($params);
                                                $result = file_get_contents('https://www.mofilm.com/Video/encode/', false, $context);

                                                
                                                
                                                //$url = 'https://dev.mofilm.com/Video/encode/?userID='.mofilmMovieManager::getInstanceByID($movieID)->getUserID().'&movieID='. $movieID;
                                                //$userData = json_decode(file_get_contents($url) , true);
                                                systemLog::message('-- returned from email --');
					
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
			    
				$timestamp = strtotime($oMofilmUploadStatus->getUpdateDate());
				if ( $timestamp ) {
					$current_timestamp = strtotime("now");
					$diff = $current_timestamp - $timestamp;
						
					if ( $diff > 20000 ) {
						$oMofilmUploadStatus->setStatus(mofilmUploadStatus::STATUS_FAILED);
						$oMofilmUploadStatus->save();
							
						$oMofilmMovies = mofilmMovieManager::getInstanceByID($movieID);
						$oMofilmMovies->setStatus(mofilmMovie::STATUS_FAILED_ENCODING);
						$oMofilmMovies->save();
					}
				}
				
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
	
	function saveMovieAsset($movieID = NULL, $cdnURL = NULL, $height = NULL, $width = NULL, $type = NULL, $videoID = NULL) {
		systemLog::message('-- in save movie asset : start --');
		$asset = new mofilmMovieAsset();
		$asset->setMovieID($movieID);
		$asset->setType($type);
		
		if ( $type == 'ThumbNail') {
			$asset->setExt('JPG');
			$asset->setDescription('ThumbNail_'.$height.'x'.$width);
		} elseif ( $type == 'File' ) {
			$asset->setExt('FLV');
			$asset->setDescription('FlashVideo_'.$height.'x'.$width);
			$asset->setProfileID($videoID);
		}
		
		$asset->setWidth($height);
		$asset->setHeight($width);
		$asset->setCdnURL($cdnURL);
		$asset->setModified(date('Y-m-d H:i:s'));
		
		$oMovie = mofilmMovieManager::getInstanceByID($movieID);
		$oMovie->getAssetSet()->setObject($asset);
		$oMovie->save();
		systemLog::message('-- in save movie asset : end --');
	}
	
	private function sendEmail($inUserID, $inMovieID, $inEvent, $inBrand, $inSourceID) {
		systemLog::message('-- in send email --');
		$oQueue = commsOutboundManager::newQueueFromApplicationMessageGroup(0, mofilmMessages::MSG_GRP_USR_VIDEO_ENCODED, 'en');

		$oUser = mofilmUserManager::getInstanceByID($inUserID);
		
		$oDownloadFiles = mofilmDownloadFile::listOfObjects(NULL, NULL, $inSourceID, mofilmDownloadFile::FILETYPE_BRIEF);
		foreach ($oDownloadFiles as $oDownloadFile) {
		    $oSourceSets = $oDownloadFile->getSourceSet()->getIterator()->getArrayCopy();
		    foreach ($oSourceSets as $oSourceSet) {
			    $inDownloadHash = $oSourceSet->getDownloadHash();
		    }
		}
		
		if ( $inLanguage == "zh" ) {
			$downloadLink = "http://my.mofilm.cn/dl/".$inDownloadHash;
		} else {
			$downloadLink = "http://my.mofilm.com/dl/".$inDownloadHash;
		}
		
		commsOutboundManager::setCustomerInMessageStack($oQueue, $inUserID);
		commsOutboundManager::setRecipientInMessageStack($oQueue, $oUser->getUsername());
		commsOutboundManager::replaceDataInMessageStack($oQueue, array('%MOVIE_ID%','%EVENT_NAME%','%BRAND_NAME%','%mofilm.username%','%mofilm.downloadlink%'), array($inMovieID,$inEvent,$inBrand,$oUser->getFullname(),$downloadLink));
		return $oQueue->send();
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
