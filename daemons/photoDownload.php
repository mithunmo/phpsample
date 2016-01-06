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
            
            $oSearch = new mofilmMovieSearch();
            $oSearch->setLoadMovieData(true);
            $oSearch->setUser(mofilmUserManager::getInstanceByID(30247));
            $oSearch->setLimit(5000);
            $oSearch->setEvents(array("125"));
            $result = $oSearch->search(33,33);
            
          //  systemLog::message("====== Movie Result 1111 =======");
            $movieResult = $result->getResults();
            
            //systemLog::message($movieResult);
            foreach ($movieResult as $key => $oMovie) {
                
                systemLog::message($oMovie->getID()."==========");
                $imageList = $oMovie->getAssetSet()->getObjectByAssetType('Source')->getIterator();

		$path = "/home/ec2-user/mophoto/".mofilmUserManager::getInstanceByID($oMovie->getUserID())->getFullname();  
		if (!file_exists($path)) {
			mkdir($path);             
		}
                foreach ( $imageList as $images ) {
                    systemLog::message($images->getFilename());
	 	   $name = basename($images->getFileName()); 
                   copy("/var/www/html/mofilms/mofilm/trunk/websites/base".$images->getFilename(),$path."/".$name);
                    
                    
                }
                
            }
            
            exit();
            
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
