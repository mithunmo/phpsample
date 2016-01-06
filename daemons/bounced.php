#!/usr/bin/php
<?php
 /**
 * bounced
 *
 * Stored in bounced.class.php
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage daemons
 * @category bounced
 *
 */


/*
 * Load dependencies
 */
require_once(dirname(dirname(__FILE__)) . '/libraries/system.inc');

/*
 * Declare ticks to allow signal handling to be registered
 */
declare(ticks = 1) ;

/*
 * Set our logging
 */
systemLog::getInstance()->setSource('StartUp');
systemLog::message('--------------------------------------------------');
systemLog::message('Initialising Bounce Daemon');

/**
 * bounced
 *
 * bounce Daemon. adds bounced emails to bounceEmail table
 *
 * @package mofilm
 * @subpackage daemons
 * @category bounced
 */
class bounceDaemon extends cliDaemon {

	/**
	 * @see cliDaemon::__construct()
	 */
	function __construct() {
		parent::__construct('bounceDaemon', 'Bounce Daemon');
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
			$oSESList = mofilmCommsSenderemail::listOfObjects();
			$oCount = count($oSESList);

			for ( $i = 0; $i < $oCount; $i++ ) {
				/* @var mofilmCOmmsSenderemail $oSes */
				$oSes = $oSESList[$i];

				$this->notify(
					new cliApplicationEvent(
						cliApplicationEvent::EVENT_INFORMATIONAL,
						'Processing bounce responses',
						null,
						array(
							'log.source' => array(
								'ServerID' => $oSes->getImapServerID(),
								'Email' => $oSes->getSenderEmail(),
							)
						)
					)
				);


				$imapServerID = $oSes->getImapServerID();
				$sesEmail = $oSes->getSenderEmail();
				$sesPassword = $oSes->decrypt($oSes->getSenderPassword());
				$oImapDetail = mofilmCommsImapServerDetail::getInstance($imapServerID);
				$imapServerName = $oImapDetail->getImapServer();
				$imapServerFolder = $oImapDetail->getImapFolder();
				$imapMailerDaemonEmail = $oImapDetail->getDaemonEmail();

				$hostname = "{" . $imapServerName . "}" . $imapServerFolder;
				$username = $sesEmail;
				$password = $sesPassword;
				$mbox = imap_open($hostname, $username, $password);
				$count = imap_num_msg($mbox);
				for ( $i = 0; $i < $count; $i++ ) {
					$header = imap_header($mbox, $i);
					$fromAddress = $header->fromaddress;
					if ( $fromAddress == $imapMailerDaemonEmail ) {
						$msgBody = imap_fetchbody($mbox, $i, 1);
						$patternLink = '/The following message to <(.*)>/iU';
						preg_match($patternLink, $msgBody, $matches);
						$email = $matches[1];
						$oBouncedEmail = new mofilmCommsBouncedEmail();
						$oBouncedEmail->setEmailAddress($email);
						$oBouncedEmail->setErrorDescription($msgBody);
						$oBouncedEmail->setSenderAddress($sesEmail);
						$oBouncedEmail->save();
						imap_delete($mbox, $i);
					}
				}

				imap_expunge($mbox);
				imap_close($mbox);

				$this->notify(
					new cliApplicationEvent(
						cliApplicationEvent::EVENT_INFORMATIONAL,
						'Processed '.$count.' messages',
						null
					)
				);

				if ( $this->signalTrapped() ) {
					$loop = false;
					break;
				}
			}

			if ( $this->signalTrapped() ) {
				$loop = false;
			} else {
				sleep(900);
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
cliProcessControls::initialise($oRequest, 'bounceDaemon');
cliProcessControls::daemonise();

/*
 * Start up the daemon
 */
$oDaemon = new bounceDaemon();
$oDaemon->setPosixId(cliProcessControls::getPosixId());
$oDaemon->setPidFile(cliProcessControls::getPidFile());
$oDaemon->setPosixUser(system::getConfig()->getSystemUserId());
$oDaemon->setPosixGroup(system::getConfig()->getSystemGroupGid());
$oDaemon->trapSignal(SIGINT, SIGHUP, SIGTERM); // exit on these signals
$oDaemon->getListeners()->attachListener(new cliApplicationListenerLog());
$oDaemon->execute();