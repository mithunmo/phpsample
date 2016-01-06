#!/usr/bin/php
<?php
/**
 * City Update Daemon
 *
 * Stored in cityUpdate.php
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage daemons
 * @category cityUpdate
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
systemLog::message('Initialising Brightcove Video Daemon');

/**
 *
 * @package mofilm
 * @subpackage daemons
 * @category videoUpload
 * 
 */
class cityUpdate extends cliDaemon {

	/**
	 * @see cliDaemon::__construct()
	 */
	function __construct() {
		parent::__construct('cityUpdate', 'cityUpdate');
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
			
			$values = array();

			$query = '
				SELECT userID,paramValue FROM mofilm_content.userData WHERE paramName = "City" and paramValue = "" and userID in ( 
				SELECT userID
				FROM mofilm_content.userData
				LEFT JOIN mofilm_content.users on mofilm_content.userData.userID = mofilm_content.users.ID
				WHERE paramName = "Skills"
				AND paramValue IS NOT NULL 
				AND paramValue != "" AND mofilm_content.users.enabled ="Y" ) ';


			$list = array();

			$oStmt = dbManager::getInstance()->prepare($query);
			if ( $oStmt->execute($values) ) {
				foreach ( $oStmt as $row ) {
					$list[] = $row["userID"];
				}
			}
			$oStmt->closeCursor();

			systemLog::message($list);
			
			foreach ( $list as $usr ) {
				$oUser =  mofilmUserManager::getInstanceByID($usr);
				
				if ( $oUser->getRegIP() != null  || $oUser->getRegIP() != "" ) {
					$tags = get_meta_tags('http://www.geobytes.com/IpLocator.htm?GetLocation&template=php3.txt&IpAddress='.$oUser->getRegIP());
					systemLog::message($tags);
					if ($tags["city"] != "Limit Exceeded") {
						$oUser->getParamSet()->setParam(mofilmUser::PARAM_CITY, $tags["city"]);
						$oUser->save();
					} else {
						systemLog::message("Not updated");
					}
					sleep(200);

					
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
cliProcessControls::initialise($oRequest, 'cityUpdate');
cliProcessControls::daemonise();

/*
 * Start up the daemon
 */
$oDaemon = new cityUpdate();
$oDaemon->setPosixId(cliProcessControls::getPosixId());
$oDaemon->setPidFile(cliProcessControls::getPidFile());
$oDaemon->setPosixUser(system::getConfig()->getSystemUserId());
$oDaemon->setPosixGroup(system::getConfig()->getSystemGroupGid());
$oDaemon->trapSignal(SIGINT, SIGHUP, SIGTERM); // exit on these signals
$oDaemon->getListeners()->attachListener(new cliApplicationListenerLog());
$oDaemon->execute();
