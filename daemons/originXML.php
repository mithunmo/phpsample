#!/usr/bin/php
<?php
/**
 * newsletterd
 *
 * Stored in originXML.php
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage daemons
 * @category originXML
 * @version $Rev: 296 $
 */
/*
 * Load dependencies
 */
require_once(dirname(dirname(__FILE__)) . '/libraries/system.inc');

/*
 * Declare ticks to allow signal handling to be registered
 */
declare(ticks = 1);

/*
 * Set our logging
 */
systemLog::getInstance()->setSource('StartUp');
systemLog::message('--------------------------------------------------');
systemLog::message('Initialising originXML Daemon');

/**
 * originXML
 *
 * originXML Daemon. 
 *
 *
 * @package mofilm
 * @subpackage daemons
 * @category originXML
 */
class originXMLDaemon extends cliDaemon {

	/**
	 * @see cliDaemon::__construct()
	 */
	function __construct() {
		parent::__construct('originXMLDaemon', 'originXML Daemon');
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
			systemLog::message("Start waiting");
			$oMofilmOriginXML = mofilmOriginXML::getXmlFromQueue();
			if ( $oMofilmOriginXML ) {
				systemLog::message("Processing " . $oMofilmOriginXML->getID());

				try {
					$inMovieID = $this->parseXml($oMofilmOriginXML->getXml());
					$oMofilmOriginXML->setStatus("Success");
					$oMofilmOriginXML->setMovieID($inMovieID);
					$this->sendEmail(mofilmMovieManager::getInstanceByID($inMovieID)->getUserID(), $inMovieID);
					//$this->sendSuccessEmail();
				} catch ( Exception $ex ) {
					$oMofilmOriginXML->setStatus("Failed");
					if ( $inMovieID ) {
						mofilmMovieManager::getInstanceByID($inMovieID)->setStatus(mofilmMovie::STATUS_FAILED_ENCODING);
					}
				}
				$oMofilmOriginXML->save();
			}
			systemLog::message("End Waiting");
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

	function parseXML($inXML) {
		// we handle any XML errors ourselves (don't want them appearing on the console)
		libxml_use_internal_errors(true);
		$xml = simplexml_load_string($inXML);

		if ( count(libxml_get_errors()) > 0 ) {
			foreach ( libxml_get_errors() AS $error ) {
				//$this->Errors[] = $this->format_xml_error($error);
			}
			throw new UnparsableXmlException("XML does not parse");
		}

		$ID = intval($xml->Asset['YourAssetID']);
		$oMovie = mofilmMovieManager::getInstanceByID($ID);


		//$this->profileID = intval($xml['ProfileID']);
		$profileID = intval($xml['ProfileID']);

		// Get the status of the job
		$jobStatus = $xml['Status'];
		$jobStatus = ucwords(trim($jobStatus));
		if ( $jobStatus != 'Complete' ) {
			$notes = $xml['Notes'];
			// The job was unsuccessful
			systemLog::message("Movie $ID failed");
			systemLog::message("Movie $ID failed - notes = $notes");
			throw new Exception($notes);
		} else {
			// The movie was successfully encoded
			systemLog::message("Movie $ID - PROCESSING RESULT");

			// We need to dig down to get the assets (the encoded movies)
			foreach ( $xml->Asset AS $asset ) {
				foreach ( $asset->Files AS $files ) {
					foreach ( $files->File AS $movieFile ) {
						$assetFileID = (int) $movieFile['AssetFileID'];
						$url = (string) $movieFile['AssetURL'];
						$duration = (int) $movieFile['Duration'];

						// been seeing a random thumb coming from Origin - this will ignore it
						if ( strtolower(basename($url)) == '.jpg' ) {
							continue;
						}

						try {
							$this->extractAssetData($movieFile, $ID, $profileID);
							if ( $duration > 1 ) {
								$oMovie->setRuntime($duration);
							}
						} catch ( InvalidValueException $ex ) {
							systemLog::message("Problem with AssetFile $assetFileID : " . $ex->getMessage());
						} catch ( Exception $ex ) {
							systemLog::message("Unexpected problem with AssetFile $assetFileID : " . $ex->getMessage());
						}
					}
				}
			}

			// now we save the movie object to the database
			//$this->movie->dump();
			$oMovie->setStatus(mofilmMovie::STATUS_PENDING);

			$oMovie->save();
			systemLog::message("Movie ID " . $this->movieID . " saved to db");
			return $oMovie->getID();
		}
	}

	private function sendEmail($inUserID, $inMovieID) {
		$oQueue = commsOutboundManager::newQueueFromApplicationMessageGroup(
				0, mofilmMessages::MSG_GRP_USR_VIDEO_ENCODED, 'en');

		commsOutboundManager::setCustomerInMessageStack($oQueue, $inUserID);
		commsOutboundManager::setRecipientInMessageStack($oQueue, mofilmUserManager::getInstanceByID($inUserID)->getUsername());
		commsOutboundManager::replaceDataInMessageStack($oQueue, array('%MOVIE_ID%'), array($inMovieID));
		return $oQueue->send();
	}

	private function extractAssetData($movieFile, $movieID, $profileID) {
		if ( !is_int($movieID) ) {
			//throw new InvalidValueException('movieID must be set as an integer before extracting the asset data');
		}

		// remove the "simpleXMLobject" style info
		// and make a simple array of the attributes
		$metadata = array();
		foreach ( $movieFile->attributes() AS $key => $val ) {
			$metadata[(string) $key] = (string) $val;
		}

		$width = (string) $metadata['Width'];
		$width = intval($width);
		$height = (string) $metadata['Height'];
		$height = intval($height);
		$description = (string) $metadata['AssetTypeID'];
		if ( $width > 0 && $height > 0 ) {
			$description = sprintf('%s_%sx%s', $description, $width, $height);
		}
		$cdnURL = (string) $metadata['AssetURL'];
		$tmp = (string) $metadata['AssetTypeID'];
		$tmp = strtolower($tmp);
		switch ( $tmp ) {
			case 'jpg':
			case 'jpeg':
			case 'gif':
			case 'bmp':
			case 'png':
				$description = sprintf('ThumbNail_%sx%s', $width, $height);
				$type = 'ThumbNail';
				break;

			default:
				$type = 'File';
		}

		try {
			systemLog::message("Found asset $description for movie ($cdnURL)");

			//$asset = new Mofilm_Movie_Asset();
			$asset = new mofilmMovieAsset();
			$filename = basename($cdnURL);
			//if ( $asset->isDuplicate($filename) ) {
			//	return;
			//}
			// get the file extension
			$arr = explode('.', basename($cdnURL));
			$ext = strtoupper($arr[count($arr) - 1]);
			// check we don't already have this asset
			//if ( !$asset->FindAsset($movieID, $ext, $width, $height) ) {
			$asset->setMovieID($movieID);
			$asset->setProfileID($profileID);
			$asset->setType($type);
			$asset->setWidth($width);
			$asset->setHeight($height);
			$asset->setExt($ext);
			//}

			$asset->setCdnURL($cdnURL);
			$asset->setDescription($description);
			$asset->setMetaData($metadata);
			//$asset->Save();

			$oMovie = mofilmMovieManager::getInstanceByID($movieID);
			$oMovie->getAssetSet()->setObject($asset);
			$oMovie->save();

			$oMofilmAssetDownload = new mofilmAssetDownloadQ();
			$oMofilmAssetDownload->setAssetID($asset->getID());
			$oMofilmAssetDownload->setScheduled(date('Y-m-d H:i:s', strtotime("+5 seconds")));
			$oMofilmAssetDownload->save();
			//$asset->markForDownload();
			//$asset->set
		} catch ( Exception $ex ) {
			echo "Error" . $ex->getMessage();
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
cliProcessControls::initialise($oRequest, 'originXMLDaemon');
cliProcessControls::daemonise();

/*
 * Start up the daemon
 */
$oDaemon = new originXMLDaemon();
$oDaemon->setPosixId(cliProcessControls::getPosixId());
$oDaemon->setPidFile(cliProcessControls::getPidFile());
$oDaemon->setPosixUser(system::getConfig()->getSystemUserId());
$oDaemon->setPosixGroup(system::getConfig()->getSystemGroupGid());
$oDaemon->trapSignal(SIGINT, SIGHUP, SIGTERM); // exit on these signals
$oDaemon->getListeners()->attachListener(new cliApplicationListenerLog());
$oDaemon->execute();
