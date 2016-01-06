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
ini_set ( 'max_execution_time', 0); 
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
$query = 'SELECT * from momusic_content.workGetty ';


			$list = array();

			$oStmt = dbManager::getInstance()->prepare($query);

if ( $oStmt->execute($values) ) {
				foreach ( $oStmt as $row ) {


        $mp3folder = "/mnt/GettyFiles/3/";

        $song_name = trim($row['song_name']);
        $artist_name = $row['artist_name'];
        $album = $row['album'];

        $filename = "";
        $filename = trim($row['file_name']);
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


        $context = $row['context'];
        if ( isset($context) ) {
                $oWork->setContext($context);
        }

        if (isset($album) && $album != '' && strlen($album) <40) {
                $oWork->setAlbum($album);
        } else {
                $album = "album-".$artist_name;
                $oWork->setAlbum("album");					
        }


        $s3 = new AmazonS3("AKIAI4HCNO3U37FJLVNA", "ug562esrHehmnzGlB1DUv/vpDpBR0rS5AhGrZQHd");
        $bucket = "chakra-test";
        $s3->putBucket($bucket, AmazonS3::ACL_PUBLIC_READ);

        $inSong = mofilmUtilities::removeSpecialChars($song_name);
        $inSong= preg_replace('/[^(\x20-\x7F)]*/','', $inSong);

        $inArtist = mofilmUtilities::removeSpecialChars($artist_name);
        $inAlbum = mofilmUtilities::removeSpecialChars($album);
        $inAlbum= preg_replace('/[^(\x20-\x7F)]*/','', $inAlbum);

        $path_parts = pathinfo($filename);
        $extension = $path_parts["extension"];

        systemLog::message($extension);

        $dllink = "20032015/".$inArtist . "/" . $inAlbum .   "/" . $inSong . "." . $extension;
        $fullpath = $mp3folder. $filename;

        systemLog::message("Uploadig to s3" . $fullpath);
        if(file_exists($fullpath)){
             if ( $s3->putObjectFile($fullpath, $bucket, $dllink, AmazonS3::ACL_PUBLIC_READ) ) {

                $momusicURL = "http://chakra-test.s3.amazonaws.com/20032015/" . $inArtist . "/" . $inAlbum . "/" . $inSong . "." . $extension;
                $saveURL = "http://momusic.s3.amazonaws.com/20032015/" . $inArtist . "/" . $inAlbum . "/" . $inSong . "." . $extension;
                $oWork->setPath($saveURL);
             }
        }else{
$query = '
			UPDATE '.system::getConfig()->getDatabase('momusic_content').'.workGetty
			   SET notUploaded= 1
			 WHERE ID ='.$row['ID'];

		$oStmt = dbManager::getInstance()->prepare($query);
	        $oStmt->execute();
		$oStmt->closeCursor();
	continue;
	}
       $oWork->setFileName($dllink);


        $genre1 = $row['genre1'];
        if ( isset($genre1) ) {
                $oWork->setGenre1($genre1);
        }
        $genre2 = $row['genre2'];
        if ( isset($genre2) ) {
                $oWork->setGenre2($genre2);
        }

        $genre3 = $row['genre3'];
        if ( isset($genre3) ) {
                $oWork->setGenre3($genre3);
        }

        $mood1 = $row['mood1'];
        if ( isset($mood1) ) {
                $oWork->setMood1($mood1);
        }
        $mood2 = $row['mood2'];
        if ( isset($mood2) ) {
                $oWork->setMood2($mood2);
        }
        $mood3 = $row['mood3'];
        if ( isset($mood3) ) {
                $oWork->setMood3($mood3);
        }


        $style1 = $row['style1'];
        if ( isset($style1) ) {
                $oWork->setStyle1($style1);
        }
        $style2 = $row['style2'];
        if ( isset($style2) ) {
                $oWork->setStyle2($style2);
        }
        $style3 = $row['style3'];
        if ( isset($style3) ) {
                $oWork->setStyle3($style3);
        }

        $keyword = $row['keywords'];
        if ( isset($keyword) ) {
                $oWork->setKeywords($keyword);
        }


        $inst1 = $row['instrument1'];
        if ( isset($inst1) && is_string($inst1) ) {
                $oWork->setInstrument1($inst1);
        }
        $inst2 = $row['instrument2'];
        if ( isset($inst2) && is_string($inst2) ) {
                $oWork->setInstrument2($inst2);
        }
        $inst3 = $row['instrument3'];
        if ( isset($inst3) && is_string($inst3) ) {
                $oWork->setInstrument3($inst3);
        }
        $inst4 = $row['instrument4'];
        if ( isset($inst4) && is_string($inst4) ) {
                $oWork->setInstrument4($inst4);
        }


        $sounds_like1 = $row['sounds_like1'];
        if ( isset($sounds_like1) ) {
                $oWork->setSoundsLike1($sounds_like1);
        }
        $sounds_like2 = $row['sounds_like2'];
        if ( isset($sounds_like2) ) {
                $oWork->setSoundsLike2($sounds_like2);
        }

        $sounds_like3 = $row['sounds_like3'];
        if ( isset($sounds_like3) ) {
                $oWork->setSoundsLike3(strval($sounds_like3));
        }
        
        
        $resembles_song1 = $row['resembles_song1'];
        if ( isset($resembles_song1) ) {
                $oWork->setResemblesSong1(strval($resembles_song1));
        }

        $resembles_song2 = $row['resembles_song2'];
        if ( isset($resembles_song2) ) {
                $oWork->setResemblesSong2(strval($resembles_song2));
        }
        
        $resembles_song3 = $row['resembles_song3'];
        if ( isset($resembles_song3) ) {
                $oWork->setResemblesSong3(strval($resembles_song3));
        }
        
        $composer = $row['composer'];
        if ( isset($composer) ) {
                $oWork->setComposer($composer);
        }
        
        
        $publisher = $row['publisher'];
        $oWork->setPublisher(strval($publisher));

        $desc = $row['description'];
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
	echo $path;
        $time = exec("ffmpeg -i " . escapeshellarg($path) . " 2>&1 | grep 'Duration' | cut -d ' ' -f 4 | sed s/,//");
        list($hms, $milli) = explode('.', $time);
        list($hours, $minutes, $seconds) = explode(':', $hms);
        $total_seconds = ($hours * 3600) + ($minutes * 60) + $seconds;
        $oWork->setDuration($total_seconds);

        $oWork->setMusicSource("Getty");
        $oWork->setPriority(777);
        $oWork->save();

        unset($oWork);
                               }
}
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