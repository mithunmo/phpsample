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

			$objPHPExcel = PHPExcel_IOFactory::load("/var/www/html/mofilms/mofilm/trunk/temp/Getty_20131002.xlsx");

			//foreach ( $objPHPExcel->getWorksheetIterator() as $worksheet ) {
			$worksheet = $objPHPExcel->getSheetByName("Sheet1");
			//foreach ( $worksheet)
			$highestRow = $worksheet->getHighestRow(); // e.g. 10
			
			
echo 'here';exit;
			
			for ( $row = 0; $row <= $highestRow ; ++$row ) {

				$song_name = trim($worksheet->getCellByColumnAndRow(4, $row)->getValue());
				$artist_name = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
                                $file_name = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
				
				$workDetails = momusicWork::getInstanceSongName($song_name, $artist_name);
                                echo '<pre>';
                                print_r($workDetails);exit;


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
