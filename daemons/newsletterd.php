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
declare(ticks = 1) ;

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
			gc_enable();
			$resultarr = mofilmCommsNewsletterdata::getNlById();
			if ( count($resultarr) > 0 ) {
				/* @var mofilmCommsNewsletterdata $oNewsletterdata */
				$oNewsletterdata = $resultarr[0];

				$this->notify(
					new cliApplicationEvent(
						cliApplicationEvent::EVENT_INFORMATIONAL,
						'Processing newsletter data: '.$oNewsletterdata->getID(),
						null,
						array(
							'log.source' => array(
								'NlDataID' => $oNewsletterdata->getID(),
								'NlID' => $oNewsletterdata->getNewsletterID(),
								'Class' => $oNewsletterdata->getClassname(),
							)
						)
					)
				);

				$start_time = time();
				$diff = (strtotime($oNewsletterdata->getScheduledDate()) - time());

				if ( $oNewsletterdata->getStatus() == 0 && $diff < 0 ) {
					$oNewsletter = mofilmCommsNewsletter::getInstance($oNewsletterdata->getNewsletterID());
					$oSenderEmail = mofilmCommsSenderemail::getInstance($oNewsletterdata->getEmailName());
					
					$listArr = array();
					$userListArr = array();
					if ( $oNewsletterdata->getClassname() == "mofilmCommsSubscription" ) {
						$oSubscription = new mofilmCommsSubscription();
						$listArr = $oSubscription->getEmailByListId($oNewsletterdata->getParams());
						$count = count($listArr);
						unset($oSubscription);
						gc_collect_cycles();
					} else {
						$oMofilmCommsNewsletterList = new mofilmCommsNewsletterlist();
						$oMofilmCommsNewsletterList->setNewsletterData($oNewsletterdata);
						$oMofilmCommsNewsletterList->setClassName($oNewsletterdata->getParams());
						$userListArr = $oMofilmCommsNewsletterList->getFilter()->apply();
						$count = count($userListArr);
						unset($oMofilmCommsNewsletterList);
					}

					mofilmCommsNewsletterdata::updateStatus($oNewsletterdata->getId(), mofilmCommsNewsletterdata::NEWSLETTER_SENT);
					
					$trackImageUrl = system::getConfig()->getParam("mofilm", "myMofilmUri");
					$imgcode = ' <img src="' . $trackImageUrl . '/themes/mofilm/images/track.png" /> ';

					$nlMessage = $oNewsletter->getMessageBody()." ".$imgcode;
					$oNewsletter->setMessageBody($nlMessage);
					
					$this->notify(
						new cliApplicationEvent(
							cliApplicationEvent::EVENT_INFORMATIONAL,
							'Found '.$count.' recipients for newsletter',
							null
						)
					);

					for ( $i = 0; $i < $count; $i++ ) {
						if ( $oNewsletterdata->getClassname() == "mofilmCommsSubscription" ) {
							$oInstanceID = array();
							$emailObj = mofilmCommsEmail::getInstance($listArr[$i]);
							$inId = $emailObj->getID();
							$email = $emailObj->getEmail();
							$userid = $emailObj->getUserID();
							unset($emailObj);
							mofilmCommsEmail::destroy($inId);
						} else {
							$oInstanceID = $userListArr[$i];
							$oMofilmUser = mofilmUserManager::getInstanceByID($oInstanceID["ID"]);
							if ( $oMofilmUser instanceof mofilmUser ) {
								$inId = '';
								$email = $oMofilmUser->getEmail();
								$userid = $oMofilmUser->getID();
								unset($oMofilmUser);
							} else {
								continue;
							}
						}
						$email = trim($email);
						if ( $userid == NULL || !is_numeric($userid)) {
							continue;
						}
						
						$oTransaction = mofilmCommsTransactions::create();
						$messageHTML = $oNewsletterdata->getCompiler($oTransaction->getID(), $userid, $oInstanceID)->compile();
						

						if ( $oNewsletterdata->getMessageType() == mofilmCommsNewsletterdata::NEWSLETTER_MKT_MESSAGE ) {
							$UnsubscriptionUrl = $trackImageUrl . "/track/unsubs";
							$UnsubscriptionUrl = $UnsubscriptionUrl . "?userId=" . $userid."&emailId=".$inId."&nlId=".$oNewsletter->getNlid();
							$messageHTML = preg_replace("/\/UNSUB/", $UnsubscriptionUrl, $messageHTML);
						} else {
							$messageHTML = preg_replace("/unsubscribe from this list/", " " , $messageHTML);
							$messageHTML = $messageHTML . " <h5>This is an important message</h5> ";
						}
						
						$this->notify(
							new cliApplicationEvent(
								cliApplicationEvent::EVENT_INFORMATIONAL,
								'Sending to '.$email.' ('.$userid.')',
								null
							)
						);
												
						$oQueue = commsOutboundManager::newQueueForNormalMessage(
							$oNewsletter->getMessageSubject(), $messageHTML, "en",
							$oSenderEmail->getSenderEmail(), $oSenderEmail->getName(),
							$oNewsletter->getMessageText(), $oNewsletter->getIsHtml(),
							$oNewsletterdata->getParamSet()->getParam(mofilmCommsNewsletterdata::PARAM_NL_ATTACH)
						);
						commsOutboundManager::setCustomerInMessageStack($oQueue, $userid);
						commsOutboundManager::setRecipientInMessageStack($oQueue, $email);
						$oQueue->setTransactionID($oTransaction->getId());
						if ( !$oQueue->send() ) {
							$this->notify(
								new cliApplicationEvent(
									cliApplicationEvent::EVENT_WARNING,
									$oQueue->getLastException()->getMessage(),
									null
								)
							);

							$oQueue->resetStack();
							commsOutboundQueue::reset();
						}

						mofilmCommsTransactions::destroy($oTransaction->getId());
						unset($oTransaction, $email, $userid, $inId , $oQueue, $messageHTML, $oInstanceID, $UnsubscriptionUrl);
						gc_collect_cycles();
					}

					unset($count);
					unset($listArr);
					unset($userListArr);
					mofilmCommsNewsletter::destroy($oNewsletter->getNlid());
					gc_collect_cycles();

				} else {
					$this->notify(
						new cliApplicationEvent(
							cliApplicationEvent::EVENT_INFORMATIONAL,
							'Newsletter sent already ('.$oNewsletterdata->getStatus().'), or not ready to be sent ('.$diff.')',
							null
						)
					);
				}

				$end_time = time();
				$total = $end_time - $start_time;
				$oNewsletterdata = null;
				unset($oNewsletterdata);

				$this->notify(
					new cliApplicationEvent(
						cliApplicationEvent::EVENT_INFORMATIONAL,
						'Process completed in '.utilityStringFunction::humanReadableTime($total)
					)
				);
			}

			$resultarr = null;
			unset($resultarr);
			gc_collect_cycles();

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