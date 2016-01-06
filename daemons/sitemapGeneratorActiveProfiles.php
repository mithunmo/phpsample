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
systemLog::message('Initialising SiteMap Generator :: Active Profiles Daemon');

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
				$oActiveProfiles = mofilmUserProfile::listOfObjects();
				
				$dom = new DOMDocument('1.0', 'UTF-8');
				$dom->formatOutput = true;
				
				$domElement = $dom->createElement('urlset');
				$root = $dom->appendChild($domElement);
				
				$domElementNS = $dom->createAttribute('xmlns');
				$domElement->appendChild($domElementNS);
				
				$domElementNSValue = $dom->createTextNode('http://www.sitemaps.org/schemas/sitemap/0.9');
				$domElementNS->appendChild($domElementNSValue);
				
				$domElementNS1 = $dom->createAttribute('xmlns:xsi');
				$domElement->appendChild($domElementNS1);
				
				$domElementNSValue1 = $dom->createTextNode('http://www.w3.org/2001/XMLSchema-instance');
				$domElementNS1->appendChild($domElementNSValue1);
				
				$domElementNS2 = $dom->createAttribute('xsi:schemaLocation');
				$domElement->appendChild($domElementNS2);
				
				$domElementNSValue2 = $dom->createTextNode('http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd');
				$domElementNS2->appendChild($domElementNSValue2);

				$xmlElement = simplexml_import_dom($dom);

				foreach ( $oActiveProfiles as $oActiveProfile ) {
					if ( $oActiveProfile->isActive() ) { $i++;
						$xmlNode = $xmlElement->addChild('url');
						$url = system::getConfig()->getParam('mofilm', 'myMofilmUri')->getParamValue().DIRECTORY_SEPARATOR.'user'.DIRECTORY_SEPARATOR.$oActiveProfile->getProfileName().DIRECTORY_SEPARATOR;
						$xmlNode->addChild('loc', htmlspecialchars($url));
						$xmlNode->addChild('lastmod', date('Y-m-d'));
						$xmlNode->addChild('changefreq', 'weekly');
					}
				}
				$xmlElement->asXML('/var/www/html/mofilmcake/app/webroot/sitemap/active_profile.xml');
				$loop = false;
			    
			} catch (Exception $e) {
				systemLog::message('--- in catch ---');
				systemLog::message($e);
				$loop = true;
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
