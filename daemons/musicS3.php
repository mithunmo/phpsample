#!/usr/bin/php
<?php
/**
 * Momusic upload as on August 26
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

ini_set('memory_limit', '2048M');
#require_once('audio/includes/classAudioFile.php');
require_once(dirname(dirname(__FILE__)) . '/libraries/system.inc');
ini_set("auto_detect_line_endings", "1");

//require_once 's3.php';

/*
 * Declare ticks to allow signal handling to be registered
 */
declare(ticks = 1);

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
class newsletterDaemon extends cliDaemon {

	/**
	 * @see cliDaemon::__construct()
	 */
	function __construct() {
		parent::__construct('newsletterDaemon', 'Newsletter Daemon');
	}

	/**1
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
			//$sku = 888880;
			//sleep(100);

			$objPHPExcel = PHPExcel_IOFactory::load("/var/www/html/mofilms/mofilm/trunk/temp/Getty_Metadata_Add.xlsx");

			//foreach ( $objPHPExcel->getWorksheetIterator() as $worksheet ) {
			$worksheet = $objPHPExcel->getSheetByName("Sheet1");
			//foreach ( $worksheet)
			$highestRow = $worksheet->getHighestRow(); // e.g. 10
			
			

			
			for ( $row = 1000; $row <= 40 ; ++$row ) {
                                $mp3folder = "/mnt/GettyFiles/3/";

				$song_name = trim($worksheet->getCellByColumnAndRow(0, $row)->getValue());
				$artist_name = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
				$album = $worksheet->getCellByColumnAndRow(28, $row)->getValue();
				
				$filename = "";
				$filename = trim($worksheet->getCellByColumnAndRow(1, $row)->getValue());
				$filenameArray = explode('_',$filename);
                                $fileLength = strlen($filenameArray[0]);
                                $vendorID = $filenameArray[0];
                                $dyFolderName = '';
                                $firstDigit      = substr($filenameArray[0], 0, 1);
                                if($fileLength == 5){
                                    $dyFolderName   .= $firstDigit.'0000';
                                    $mp3folder .= 	$dyFolderName .'/';
                                }else{
                                    $firstFourDigit     = substr($filenameArray[0], 0, 4);
                                    if($firstDigit == 7 && $firstFourDigit > 7012){
                                    // not to do add foldername
                                    }else{
                                        $firstTwoDigit      = substr($filenameArray[0], 0, 2);
                                        $dyFolderName  = $firstTwoDigit.'0000';
                                        $mp3folder .= 	$dyFolderName .'/';	
                                    }
                                }
		
				echo "== $filename  $row ==";
				if ( $song_name == "" ) {
					continue;
				}

				echo "$song_name";

				$oWork = new momusicWork();

				$oWork->setSongName(strval($song_name));
				$oWork->setArtistName($artist_name);

				$context = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
				if ( isset($context) ) {
					$oWork->setContext($context);
				}

				if (isset($album)) {
					$oWork->setAlbum($album);
				} else {
					$album = "album-".$artist_name;
					$oWork->setAlbum("album");					
				}



				$s3 = new AmazonS3("AKIAI4HCNO3U37FJLVNA", "ug562esrHehmnzGlB1DUv/vpDpBR0rS5AhGrZQHd");
				$bucket = "chakra-test";
				$s3->putBucket($bucket, AmazonS3::ACL_PUBLIC_READ);

				$inSong = mofilmUtilities::removeSpecialChars($song_name);
				$inArtist = mofilmUtilities::removeSpecialChars($artist_name);
				$inAlbum = mofilmUtilities::removeSpecialChars($album);
				$path_parts = pathinfo($filename);
				$extension = $path_parts["extension"];

				systemLog::message($extension);

				$dllink = $inArtist . "/" . $inAlbum . "/" . $inSong . "." . $extension;
				$fullpath = $mp3folder. $filename;

				systemLog::message("Uploadig to s3" . $fullpath);
				if ( $s3->putObjectFile($fullpath, $bucket, $dllink, AmazonS3::ACL_PUBLIC_READ) ) {

					$momusicURL = "http://chakra-test.s3.amazonaws.com/" . $inArtist . "/" . $inAlbum . "/" . $inSong . "." . $extension;

					$oWork->setPath($momusicURL);
				}


				$genre1 = $worksheet->getCellByColumnAndRow(5, $row)->getValue();
				if ( isset($genre1) ) {
					$oWork->setGenre1($genre1);
				}
				$genre2 = $worksheet->getCellByColumnAndRow(6, $row)->getValue();
				if ( isset($genre2) ) {
					$oWork->setGenre2($genre2);
				}

				$genre3 = $worksheet->getCellByColumnAndRow(7, $row)->getValue();
				if ( isset($genre3) ) {
					$oWork->setGenre3($genre3);
				}

				$mood1 = $worksheet->getCellByColumnAndRow(8, $row)->getValue();
				if ( isset($mood1) ) {
					$oWork->setMood1($mood1);
				}
				$mood2 = $worksheet->getCellByColumnAndRow(9, $row)->getValue();
				if ( isset($mood2) ) {
					$oWork->setMood2($mood2);
				}
				$mood3 = $worksheet->getCellByColumnAndRow(10, $row)->getValue();
				if ( isset($mood3) ) {
					$oWork->setMood3($mood3);
				}


				$style1 = $worksheet->getCellByColumnAndRow(11, $row)->getValue();
				if ( isset($style1) ) {
					$oWork->setStyle1($style1);
				}
				$style2 = $worksheet->getCellByColumnAndRow(12, $row)->getValue();
				if ( isset($style2) ) {
					$oWork->setStyle2($style2);
				}
				$style3 = $worksheet->getCellByColumnAndRow(13, $row)->getValue();
				if ( isset($style3) ) {
					$oWork->setStyle3($style3);
				}

				$keyword = $worksheet->getCellByColumnAndRow(14, $row)->getValue();
				if ( isset($keyword) ) {
					$oWork->setKeywords($keyword);
				}


				$inst1 = $worksheet->getCellByColumnAndRow(15, $row)->getValue();
				if ( isset($inst1) && is_string($inst1) ) {
					$oWork->setInstrument1($inst1);
				}
				$inst2 = $worksheet->getCellByColumnAndRow(16, $row)->getValue();
				if ( isset($inst2) && is_string($inst2) ) {
					$oWork->setInstrument2($inst2);
				}
				$inst3 = $worksheet->getCellByColumnAndRow(17, $row)->getValue();
				if ( isset($inst3) && is_string($inst3) ) {
					$oWork->setInstrument3($inst3);
				}
				$inst4 = $worksheet->getCellByColumnAndRow(18, $row)->getValue();
				if ( isset($inst4) && is_string($inst4) ) {
					$oWork->setInstrument4($inst4);
				}


				$sounds_like1 = $worksheet->getCellByColumnAndRow(21, $row)->getValue();
				if ( isset($sounds_like1) ) {
					$oWork->setSoundsLike1($sounds_like1);
				}
				$res_song1 = $worksheet->getCellByColumnAndRow(22, $row)->getValue();
				if ( isset($res_song1) ) {
					$oWork->setResemblesSong1($res_song1);
				}

				$sounds_like2 = $worksheet->getCellByColumnAndRow(23, $row)->getValue();
				if ( isset($sounds_like2) ) {
					$oWork->setSoundsLike2(strval($sounds_like2));
				}
				$res_song2 = $worksheet->getCellByColumnAndRow(24, $row)->getValue();
				if ( isset($res_song2) ) {
					$oWork->setResemblesSong2(strval($res_song2));
				}

				$sounds_like3 = $worksheet->getCellByColumnAndRow(25, $row)->getValue();
				if ( isset($sounds_like3) ) {
					$oWork->setSoundsLike3($sounds_like3);
				}
				$res_song3 = $worksheet->getCellByColumnAndRow(26, $row)->getValue();
				if ( isset($res_song3) ) {
					//$oWork->setResemblesSong3($res_song3);
					$oWork->setResemblesSong3(strval($res_song3));
				}
				$composer = $worksheet->getCellByColumnAndRow(27, $row)->getValue();
				if ( isset($composer) ) {
					$oWork->setComposer($composer);
				}
				$publisher = $worksheet->getCellByColumnAndRow(31, $row)->getValue();
				$oWork->setPublisher(strval($publisher));


				$desc = $worksheet->getCellByColumnAndRow(34, $row)->getValue();
                                if ( isset($desc) ) {
					//$oWork->setResemblesSong3($res_song3);
					$oWork->setDescription($desc);
                                }else{
                                    $oWork->setDescription('dummy');
                                }
				//$oWork->setDescription($desc);
				if ( isset($vendorID) ) {
					$oWork->setVendorID($vendorID);
				}
                                
                                

				$path = $momusicURL;
				$time = exec("ffmpeg -i " . escapeshellarg($path) . " 2>&1 | grep 'Duration' | cut -d ' ' -f 4 | sed s/,//");
				list($hms, $milli) = explode('.', $time);
				list($hours, $minutes, $seconds) = explode(':', $hms);
				$total_seconds = ($hours * 3600) + ($minutes * 60) + $seconds;
				$oWork->setDuration($total_seconds);
				
				$oWork->setMusicSource("VIP");
				$oWork->setPriority(777);
				$oWork->save();

/*


				$AF = new AudioFile;
				//$filename = $oWorkObject->getPath();
				$pathinfo = pathinfo($dllink);
				//shell_exec("curl -O ".$filename);
				$fullpath = "/Library/WebServer/Documents/mm/trunk/temp/diners/" . $filename;
				systemLog::message("duration" . $fullpath);
				$AF->loadFile($fullpath);
				$durationArr = $AF->durationInfo();

				systemLog::message($durationArr);

				if ( floor($durationArr["duration_seconds"]) > 0 ) {
					systemLog::message($dllink);
					systemLog::message($durationArr);
					systemLog::message("================");
					$oWork->setDuration(floor($durationArr["duration_seconds"]));
					//$oWorkObject->setDuration(floor($durationArr["duration_seconds"]));
					//$oWorkObject->save();
				} else {
					systemLog::message("Error duratioin");
					systemLog::message($dllink);
				}

*/

				unset($oWork);
			}
			//}
			unset($objPHPExcel);


			print "\n ====== \n";


			sleep(300);
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
cliProcessControls::initialise($oRequest, 'newsletterDaemon');
cliProcessControls::daemonise();

/*
 * Start up the daemon
 */
$oDaemon = new newsletterDaemon();
$oDaemon->setPosixId(cliProcessControls::getPosixId());
$oDaemon->setPidFile(cliProcessControls::getPidFile());
$oDaemon->setPosixUser(system::getConfig()->getSystemUserId());
$oDaemon->setPosixGroup(system::getConfig()->getSystemGroupGid());
$oDaemon->trapSignal(SIGINT, SIGHUP, SIGTERM); // exit on these signals
$oDaemon->getListeners()->attachListener(new cliApplicationListenerLog());
$oDaemon->execute();
