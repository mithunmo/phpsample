#!/usr/bin/php
<?php
/**
 * Grants past data updation
 *
 * Stored in grantsAppliedData.php
 *
 * @author Pavan Kumar P G
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage daemons
 * @category grants
 * @version $Rev: 1 $
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
systemLog::message('Initialising Grants Crawl Daemon');

/**
 *
 * @package mofilm
 * @subpackage daemons
 * @category grants
 * 
 */
class grantsAppliedDataDaemon extends cliDaemon {

	/**
	 * @see cliDaemon::__construct()
	 */
	function __construct() {
		parent::__construct('grantsAppliedDataDaemon', 'grantsAppliedDataDaemon');
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
			
			try {
				$fp = fopen('grant21.csv', 'r');

				while ( ($data = fgetcsv($fp)) !== FALSE ) {
					$inSourceID = $data[0];
					
					$oGrant = mofilmGrants::getInstanceBySourceID($inSourceID);
					if ( $oGrant->getID() > 0 ) {
						$inGrantID = $oGrant->getID();
					} else {
						$this->createGrants($inSourceID);
						$oGrant = mofilmGrants::getInstanceBySourceID($inSourceID);
						if ( $oGrant instanceof mofilmGrants ) {
							$inGrantID = $oGrant->getID();
						}
					}
					
					$inMovieID = $data[1];
					$inFilmTitle = ($data[2])?(htmlspecialchars($data[2])):'No Film Title';
					$inUserID = $data[3];
					$inRequestedAmount = (int)($data[4]);
					$inGrantedAmount = (int)($data[5]);
					$inFilmConcept = 'No Film Concept';
					$inUsageOfGrants = 'No Usage of Grants';
					$inScript = 'No Script';
					
					$inStatus = mofilmUserMovieGrants::STATUS_APPROVED;
		
					if ( $inGrantID && $inUserID ) {
						$oUserMovieGrants = new mofilmUserMovieGrants();
						$oUserMovieGrants->setGrantID($inGrantID);
						$oUserMovieGrants->setUserID($inUserID);
						$oUserMovieGrants->setMovieID($inMovieID);
						$oUserMovieGrants->setFilmConcept($inFilmConcept);
						$oUserMovieGrants->setUsageOfGrants($inUsageOfGrants);
						$oUserMovieGrants->setScript($inScript);
						$oUserMovieGrants->setFilmTitle($inFilmTitle);
						$oUserMovieGrants->setRequestedAmount($inRequestedAmount);
						$oUserMovieGrants->setGrantedAmount($inGrantedAmount);
						$oUserMovieGrants->setStatus($inStatus);
						$oUserMovieGrants->setCreated('2012-09-24 10:55:11');
						$oUserMovieGrants->save();
					}
				}
			}
			catch (Exception $error) {
			    systemLog::message('--- userMovieGrant --');
			    systemLog::message($error);
			}
			
			//fclose($fp);
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
	
	function createGrants($i) {
	    try{
	    	$oEnddate = mofilmSource::getInstance($i)->getEvent()->getEndDate();
				
		$oGrants = new mofilmGrants();
		$oGrants->setSourceID($i);
		$oGrants->setCurrencySymbol('$');
		$oGrants->setEndDate($oEnddate);
		$oGrants->setDescription('Description Not available');
		$oGrants->setTotalGrants(100000.00);
		$oGrants->save();
		unset ($oGrants);
	    }
	    catch (Exception $error){
		systemLog::message('--- Grant --');
		systemLog::message($error);
	    }
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
cliProcessControls::initialise($oRequest, 'grantsAppliedDataDaemon');
cliProcessControls::daemonise();

/*
 * Start up the daemon
 */
$oDaemon = new grantsAppliedDataDaemon();
$oDaemon->setPosixId(cliProcessControls::getPosixId());
$oDaemon->setPidFile(cliProcessControls::getPidFile());
$oDaemon->setPosixUser(system::getConfig()->getSystemUserId());
$oDaemon->setPosixGroup(system::getConfig()->getSystemGroupGid());
$oDaemon->trapSignal(SIGINT, SIGHUP, SIGTERM); // exit on these signals
$oDaemon->getListeners()->attachListener(new cliApplicationListenerLog());
$oDaemon->execute();
