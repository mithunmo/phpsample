#!/usr/bin/php
<?php
/**
 * newsletterd
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

    /**
     * @see cliDaemon::execute()
     */
    function execute() {
        $this->notify(
                new cliApplicationEvent(
                cliApplicationEvent::EVENT_INFORMATIONAL, 'Entering main process loop', null, array(
            'log.source' => 'Process'
                )
                )
        );
        $this->setStatusParam('Status', 'Running');
        $this->updateStatus();

        $loop = true;
        do {
            if ($this->signalTrapped()) {
                $loop = false;
            }

            for ($i = 1; $i <= 2; $i++) {

                $url = "http://api.brightcove.com/services/library?command=find_video_by_reference_id&media_delivery=http&reference_id=" . $i . "&video_fields=name,renditions&token=Ekg-LmhL4QrFPEdtjwJlyX2Zi4l6mgdiPnWGP0bKIyKKT_94PTKHrw..";
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $jsonResponse = curl_exec($ch);
                curl_close($ch);
                $result = json_decode($jsonResponse);

                $result = $result->renditions;

                $arr = array();

                foreach ($result as $value) {

                    $arr[$value->encodingRate] = $value->url;
                    //$encodingrate = $value->
                    //systemLog::message($value->url);  
                }

                $max = max(array_keys($arr));
                systemLog::message($arr[$max] . " " . $max);
                
                $s3 = new AmazonS3("AKIAJ2BVKYYHHMXARDDQ", "RqrpvkP22U//T4m6ND8fARVhxRyPvoUqQnZTWa7b");
                $bucket = "mofilm-video";
                $s3->putBucket($bucket, AmazonS3::ACL_PUBLIC_READ);

                $inSong = mofilmUtilities::removeSpecialChars($song_name);
                $inArtist = mofilmUtilities::removeSpecialChars($artist_name);
                $inAlbum = mofilmUtilities::removeSpecialChars($album);
                $path_parts = pathinfo($filename);
                $extension = $path_parts["extension"];

                systemLog::message($extension);

                //$dllink = $inArtist . "/" . $inAlbum . "/" . $inSong . "." . $extension;
                
                $dllink = $i . $i.".mp4";
                $fullpath = $arr[$max];
                $src= $arr[$max];
                $dst = $i . $i.".mp4";
                $f = fopen($src, 'rb');
                $o = fopen($dst, 'wb');
                while (!feof($f)) {
                     if (fwrite($o, fread($f, 2048)) === FALSE) {
                        return 1;
                     }
                }
                fclose($f);
                fclose($o);
                
                
                systemLog::message("Uploadig to s3" . $fullpath);
                if ($s3->putObjectFile($fullpath, $bucket, $dllink, AmazonS3::ACL_PUBLIC_READ)) {

                    //$momusicURL = "http://momusic.s3.amazonaws.com/" . $inArtist . "/" . $inAlbum . "/" . $inSong . "." . $extension;

                    //$oWork->setPath($momusicURL);
                    systemLog::message("done");
                }



                //systemLog::message($result->renditions);
            }

            if ($this->signalTrapped()) {
                $loop = false;
            } else {
                exit();
            }
        } while ($loop === true);
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
