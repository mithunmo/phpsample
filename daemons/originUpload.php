#!/usr/bin/php
<?php
/**
 * newsletterd
 *
 * Stored in originUpload.php
 *
 * @author Mithun Mohan
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
require_once(dirname(dirname(__FILE__)) . '/classes/Http/Http.php');

//require_once(dirname(dirname(__FILE__)) . '/libraries/cli/application/event.class.php');
//require_once(dirname(dirname(__FILE__)) . '/libraries/cli/application/listeners.class.php');
/*
 * Declare ticks to allow signal handling to be registered
 */
declare(ticks = 1);

/*
 * Set our logging
 */
systemLog::getInstance()->setSource('StartUp');
systemLog::message('--------------------------------------------------');
systemLog::message('Initialising originUpload Daemon');

/**
 * originUpload
 *
 * originUpload Daemon. 
 *
 *
 * @package mofilm
 * @subpackage daemons
 * @category originUpload
 */
class originUploadDaemon extends cliDaemon {

	/**
	 * @see cliDaemon::__construct()
	 */
	function __construct() {
		parent::__construct('originUploadDaemon', 'originUpload Daemon');
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
			$oMofilmOriginQueue = mofilmOriginQueue::getMovieFromQueue();
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

	function sendToOrigin($ID, $shortDesc, $filename) {
		$success = true;
		systemLog::message("Coming to oririgin");
		$http = new http_class();

		$http->timeout = 1;
		$http->data_timeout = 1;
		$http->debug = 0;
		$http->html_debug = 1;
		$http->force_multipart_form_post = 1;

		if ( preg_match("/[^0-9a-z\_\-\.]/", basename($filename)) ) {
			$newName = preg_replace("/[^0-9a-z\_\-\.]/", '_', basename($filename));
			$newName = '/tmp/' . $newName;
			systemLog::message("\n Copying $filename to $newName \n");
			copy($filename, $newName);
			$filename = $newName;
		}

		if ( !file_exists($filename) || filesize($filename) == 0 ) {
			systemLog::message("\n Filename : $filename \n");
			if ( strstr($filename, '/Library/WebServer/Documents/trunk/websites/base/resources/video/') ) {
				$filename = '/opt/content/' . substr($filename, 16);
				$this->Log_Write("New file $filename");
				$movie = mofilmMovieManager::getInstanceByID($ID);
				$movie->getAssetSet()->setFileName($filename);
				$movie->Save();
			} else {
				//throw new InvalidDataException('Source File is missing or zero size');
				systemLog::message("There is no file present");
			}
		}

		systemLog::message("Sending $filename");
		$url = "http://www.odaptor.com/alp/tools/PostFile.aspx";

		$error = $http->GetRequestArguments($url, $arguments);
		$arguments["RequestMethod"] = "POST";
		$arguments["PostValues"] = array(
			'CustomerID' => 1139,
			'Profile' => urlencode(sprintf('%s', $this->profile)),
			'YourAssetID' => $ID,
			'AssetName' => urlencode($shortDesc)
		);
		$arguments["PostFiles"] = array(
			"File1" => array(
				"FileName" => $filename,
				"Content-Type" => "automatic/name",
			)
		);
		$arguments["Referer"] = "http://www.mofilm.com/";
		systemLog::message("\n Opening connection to: " . htmlentities($arguments["HostName"]));

		$error = $http->Open($arguments);

		if ( $error == "" ) {
			systemLog::message("\n Sending data... \n");
			$error = $http->SendRequest($arguments);
			if ( $error == "" ) {
				systemLog::message("\n Request:\n" . HtmlEntities($http->request));
				systemLog::message("\n Request headers:");

				for ( reset($http->request_headers), $header = 0; $header < count($http->request_headers); next($http->request_headers), $header++ ) {
					$header_name = key($http->request_headers);
					if ( gettype($http->request_headers[$header_name]) == "array" ) {
						for ( $header_value = 0; $header_value < count($http->request_headers[$header_name]); $header_value++ )
							systemLog::message($header_name . ": " . $http->request_headers[$header_name][$header_value]);
					}
					else
						systemLog::message($header_name . ": " . $http->request_headers[$header_name]);
				}
				$headers = array();
				$error = $http->ReadReplyHeaders($headers);
				if ( $error == "" ) {
					systemLog::message("\n Response headers: \n");
					for ( Reset($headers), $header = 0; $header < count($headers); Next($headers), $header++ ) {
						$header_name = Key($headers);
						if ( GetType($headers[$header_name]) == "array" ) {
							for ( $header_value = 0; $header_value < count($headers[$header_name]); $header_value++ )
								systemLog::message($header_name . ": " . $headers[$header_name][$header_value]);
						}
						else
							systemLog::message($header_name . ": " . $headers[$header_name]);
					}

					systemLog::message("\n Response body: \n");
					for (;; ) {
						$error = $http->ReadReplyBody($body, 1000);
						if ( $error != ""
							|| strlen($body) == 0 )
							break;
						systemLog::message("$body");
					}
				}
			}

			$http->Close();
		} else if ( strlen($error) ) {
			systemLog::message("====Error: $error ==");
			return false;
			//throw new HttpConnectionException($error);
		}

		systemLog::message("\n Finished Delivery - no errors \n");
		systemLog::message("\n Finished Delivery without Error \n");
		return $success;
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
cliProcessControls::initialise($oRequest, 'originUploadDaemon');
cliProcessControls::daemonise();

/*
 * Start up the daemon
 */
$oDaemon = new originUploadDaemon();
$oDaemon->setPosixId(cliProcessControls::getPosixId());
$oDaemon->setPidFile(cliProcessControls::getPidFile());
$oDaemon->setPosixUser(system::getConfig()->getSystemUserId());
$oDaemon->setPosixGroup(system::getConfig()->getSystemGroupGid());
$oDaemon->trapSignal(SIGINT, SIGHUP, SIGTERM); // exit on these signals
$oDaemon->getListeners()->attachListener(new cliApplicationListenerLog());
$oDaemon->execute();
