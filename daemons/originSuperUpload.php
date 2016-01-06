#!/usr/bin/php
<?php
/**
 * newsletterd
 *
 * Stored in originSuperUpload.php
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage daemons
 * @category originSuperUpload
 * @version $Rev: 296 $
 */
/*
 * Load dependencies
 */
require_once(dirname(dirname(__FILE__)) . '/libraries/system.inc');
require_once(dirname(dirname(__FILE__)) . '/classes/Http/Http.php');

/*
 * Declare ticks to allow signal handling to be registered
 */
declare(ticks = 1);

/*
 * Set our logging
 */
systemLog::getInstance()->setSource('StartUp');
systemLog::message('--------------------------------------------------');
systemLog::message('Initialising originSuperUpload Daemon');

/**
 * originSuperUpload
 *
 * originSuperUpload Daemon. 
 *
 *
 * @package mofilm
 * @subpackage daemons
 * @category originSuperUpload
 */
class originSuperUploadDaemon extends cliDaemon {

	/**
	 * @see cliDaemon::__construct()
	 */
	function __construct() {
		parent::__construct('originSuperUploadDaemon', 'originSuperUpload Daemon');
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
			$oMofilmOriginQueue = mofilmOriginQueue::getLatestMovieFromQueue();
			if ( $oMofilmOriginQueue ) {

				systemLog::message("Processing the movieID " . $oMofilmOriginQueue->getMovieID());
				$oMofilmOriginQueue->setStatus(mofilmOriginQueue::STATUS_PROCESSING);
				$oMofilmOriginQueue->save();

				$oMovie = mofilmMovieManager::getInstanceByID($oMofilmOriginQueue->getMovieID());
				$oMovie->getAssetSet()->setMovieID($oMovie->getID());
				systemLog::message("Sending to origin");
				$return = $this->sendToOriginByCurl($oMovie->getID(), $oMovie->getShortDesc(), $oMovie->getAssetSet()->getFirst()->getFilename());

				if ( $return ) {
					$oMofilmOriginQueue->setStatus(mofilmOriginQueue::STATUS_SENT);
					$oMofilmOriginQueue->save();
				} else {
					systemLog::message("Not uploaded some error . We need to investigate");
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

	function sendToOriginByCurl($ID, $shortDesc, $filename) {
	
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_VERBOSE, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible;)");
		curl_setopt($ch, CURLOPT_URL, "http://www.odaptor.com/alp/tools/PostFile.aspx");
		curl_setopt($ch, CURLOPT_POST, true);
		$filename = "@" . $filename;
		$post = array(
			"CustomerID" => 1139,
			"Profile" => 2146,
			"YourAssetID" => $ID,
			"AssetName" => $shortDesc,
			"FileName" => $filename,
		);
		systemLog::message("Uploading the file to origin");
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		$response = curl_exec($ch);
		systemLog::message($response);
		return "success";
		
	
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
cliProcessControls::initialise($oRequest, 'originSuperUploadDaemon');
cliProcessControls::daemonise();

/*
 * Start up the daemon
 */
$oDaemon = new originSuperUploadDaemon();
$oDaemon->setPosixId(cliProcessControls::getPosixId());
$oDaemon->setPidFile(cliProcessControls::getPidFile());
$oDaemon->setPosixUser(system::getConfig()->getSystemUserId());
$oDaemon->setPosixGroup(system::getConfig()->getSystemGroupGid());
$oDaemon->trapSignal(SIGINT, SIGHUP, SIGTERM); // exit on these signals
$oDaemon->getListeners()->attachListener(new cliApplicationListenerLog());
$oDaemon->execute();
