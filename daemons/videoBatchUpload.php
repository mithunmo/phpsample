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

/*
 * Declare ticks to allow signal handling to be registered
 */
declare(ticks = 1);

/*
 * Set our logging
 */
systemLog::getInstance()->setSource('StartUp');
systemLog::message('--------------------------------------------------');
systemLog::message('Initialising Video Batch Upload Daemon');

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
		parent::__construct('videoBatchUploadDaemon', 'videoBatchUploadDaemon');
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

			$dir = "/tmp/batch_upload";
			systemLog::message(" -- Dir --".$dir);
			$dh = opendir($dir);
			while (($file = readdir($dh)) !== false) {
			    if($file !== '.' && $file !== '..' && $file !== '.DS_Store' && $file !== '.svn') {
				    systemLog::message(" -- File --".$file);
				    $inUserID = 34921;
				    $inData['Description'] = strstr($file, '.', true);
				    $inData['Title'] = strstr($file, '.', true);
				    $inData['EventID'] = 22;
				    $inData['sourceID'] = 158;
				    $inData['fileName'] = $file;
				    $inData['customLicense'] = 'batch upload ';
				    systemLog::message($inData);
				    $this->saveMovie($inData, $inUserID);
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

	function saveMovie($inData, $inUserID) {

		$oUserTerms = new mofilmUserTerms();
                $oUserTerms->setUserID($inUserID);
                $oUserTerms->setTermsID(0);
                $oUserTerms->save();

		$oMovie = new mofilmMovie();
		$oMovie->setUserID($inUserID);
		$oMovie->setLongDesc($inData["Description"]);
		$oMovie->setShortDesc($inData["Title"]);
		
		$oMovie->setCredits("No credit");
		$oMovie->setActive(mofilmMovie::ACTIVE_Y);

		$oMovie->save();
		
		$oMovie->getSourceSet()->setObject(mofilmSource::getInstance($inData["sourceID"]));

		$oMofilmMovieAsset = new mofilmMovieAsset();
		$oMofilmMovieAsset->setMovieID($oMovie->getID());
		$originalPath = "/tmp/batch_upload/".$inData["fileName"];
		$finalPath = "/share/content/_platform"."/".$oMovie->getID()."/".$inData["fileName"];
		mkdir("/share/content/_platform"."/".$oMovie->getID(), 0755, true);
		copy($originalPath,$finalPath);
		unlink($originalPath);
		$oMofilmMovieAsset->setFilename($finalPath);
		$oMofilmMovieAsset->setType(mofilmMovieAsset::TYPE_SOURCE);
		$oMofilmMovieAsset->setHeight(0);
		$oMofilmMovieAsset->setWidth(0);
		$path_parts = pathinfo($inData["fileName"]);
		$oMofilmMovieAsset->setExt("mov");
		$oMofilmMovieAsset->setDescription("source");
		$oMovie->getAssetSet()->setObject($oMofilmMovieAsset);


		$oMovie->getDataSet()->setMovieID($oMovie->getID());
		$oMovie->getDataSet()->setProperty(mofilmDataname::DATA_USER_IP, $_SERVER["REMOTE_ADDR"]);
		$oMovie->getDataSet()->setProperty(mofilmDataname::DATA_USER_COUNTRY_CODE, $oMovie->getDataSet()->getUserCountryCode());
                $oMovie->getDataSet()->setProperty(mofilmDataname::DATA_MOVIE_LICENSEID, $inData["customLicense"]);

					
		$oMovie->getLicenseSet()->setMovieID($oMovie->getID());
		$oLicenseSet = new mofilmMovieMusicLicenseSet();
		$licenseArray = $inData["LicenseID"];
		for ( $i = 0 ; $i<count($licenseArray); $i++ ) {
			$oMovie->getLicenseSet()->setObject(mofilmUserMusicLicense::getInstance($licenseArray[$i]));
		}

		$oMovie->save();
		
		$oUploadQueue = new mofilmUploadQueue();
		$oUploadQueue->setMovieID($oMovie->getID());
		$oUploadQueue->setUserID($oMovie->getUserID());
		$oUploadQueue->setStatus(mofilmUploadQueue::STATUS_QUEUED);
		$oUploadQueue->save();

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
