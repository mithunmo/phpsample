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
systemLog::message('Initialising SiteMap Generator :: Finalist Videos Daemon');

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
		parent::__construct('videoUploadDaemon', 'videoUploadDaemon');
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
				$oFinalistVideos = mofilmMovieAward::listOfObjects(NULL, NULL, NULL, NULL, mofilmMovieAward::TYPE_FINALIST, NULL);

				$xml = '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
					xmlns:video="http://www.google.com/schemas/sitemap-video/1.1">';
				$i=0;
				foreach ( $oFinalistVideos as $oFinalistVideo ) {$i++;
				    
					$xml .= '<url>
							<loc>'.htmlspecialchars($oFinalistVideo->getMovie()->getShortUri(0,TRUE)).'</loc>
							<video:video>';
					
					if ( $oFinalistVideo->getMovie()->getThumbnailUri() ) {
						$xml .= '<video:thumbnail_loc>'.htmlspecialchars($oFinalistVideo->getMovie()->getThumbnailUri()).'</video:thumbnail_loc>';
					}
					
					if ( $oFinalistVideo->getMovie()->getTitle() ) {
						$xml .= '<video:title>'.htmlspecialchars($oFinalistVideo->getMovie()->getTitle()).'</video:title>';
					}
					
					if ( $oFinalistVideo->getMovie()->getDescription() ) {
						$xml .= '<video:description>'.htmlspecialchars($oFinalistVideo->getMovie()->getDescription()).'</video:description>';
					}
							    
					if ( $oFinalistVideo->getMovie()->getDuration() ) {
						$xml .= '<video:duration>'.htmlspecialchars($oFinalistVideo->getMovie()->getDuration()).'</video:duration>';
					}		    
							    
					$xml .= '	</video:video>
						</url>';
				}
				$xml .= '</urlset>';
				systemLog::message($i);
				$xmlElement = new SimpleXMLElement($xml);
				$xmlElement->asXML('/var/www/html/mofilmcake/app/webroot/sitemap/award_winning_videos.xml');
				$loop = false;
			} catch (Exception $e) {
				systemLog::message('--- in catch ---');
				systemLog::message($e);
				$loop = false;
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
