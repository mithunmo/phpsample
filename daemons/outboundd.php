#!/usr/bin/php
<?php
/**
 * outboundd
 *
 * Stored in outboundd.class.php
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage daemons
 * @category outboundd
 * @version $Rev: 5 $
 */


/*
 * Load dependencies
 */
require_once(dirname(dirname(__FILE__)).'/libraries/system.inc');

/*
 * Declare ticks to allow signal handling to be registered
 */
declare(ticks=1);

/*
 * Set our logging
 */
systemLog::getInstance()->setSource('StartUp');
systemLog::message('--------------------------------------------------');
systemLog::message('Initialising Outbound Daemon');

/**
 * outboundd
 *
 * Outbound Daemon. Processes outbound messages via the messages defined
 * gateway and gateway account.
 *
 * @package mofilm
 * @subpackage daemons
 * @category outboundd
 */
class outboundDaemon extends cliDaemon {
	
	/**
	 * Stores $_Adaptors
	 *
	 * @var array
	 * @access protected
	 */
	protected $_Adaptors = array();
	
	
	
	/**
	 * @see cliDaemon::__construct()
	 */
	function __construct() {
		parent::__construct('outboundd', 'Outbound Messaging Daemon');
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
		$this->getQueueStats();
		$this->updateStatus();
		
		/**
		 * Main daemon loop
		 */
		$loop = true;
		do {
			if ( time() - $this->getLastStatusUpdate() > 60 ) {
				$this->getQueueStats();
				$this->updateStatus();
			}
			
			if ( $this->signalTrapped() ) {
				$loop = false;
			}
			
			
			$oMessage = commsOutboundManager::getNextMessage();
			if ( $oMessage instanceof commsOutboundMessage ) {
				$this->notify(
					new cliApplicationEvent(
						cliApplicationEvent::EVENT_INFORMATIONAL,
						'Processing message: '.$oMessage->getMessageID(),
						null,
						array(
							'log.source' => array(
								'MsgID' => $oMessage->getMessageID(),
								'TypID' => $oMessage->getOutboundTypeID(),
								'To' => $oMessage->getRecipient(),
								'TransID' => $oMessage->getTransactionMap()->getTransactionID()
							)
						)
					)
				);
				
				$oGateway = commsGateway::getInstance($oMessage->getGatewayID());
				$oAdaptor = $this->getAdaptor($oGateway->getClassName());
				if ( $oAdaptor instanceof commsGatewayAdaptorBase ) {
					$oAdaptor->__construct($oMessage);
				} else {
					$adaptorClass = $oGateway->getClassName();
					$oAdaptor = new $adaptorClass($oMessage);
					$this->addAdaptor($oAdaptor);
				}
				$oAdaptor->send();
				
				$this->notify(
					new cliApplicationEvent(
						cliApplicationEvent::EVENT_INFORMATIONAL,
						'Message processing complete',
						null,
						array(
							'log.source' => array()
						)
					)
				);
			}
			$oMessage = null;
			unset($oMessage);
			
			if ( (time() - $this->getLastStatusUpdate()) >= 60 ) {
				commsOutboundQueue::cleanupQueue();
				$this->getQueueStats();
				$this->updateStatus();
			} else {
				sleep(1);
			}
			
			if ( $this->signalTrapped() ) {
				$loop = false;
			}
		} while ( $loop === true );
	}
	
	/**
	 * Gets the number of log messages in the queue, and sets to daemon params
	 *
	 * @return void
	 */
	private function getQueueStats() {
		$queueStats = commsOutboundQueue::getQueueStats();
		foreach ( $queueStats as $msgType => $count ) {
			$this->setStatusParam($msgType, $count);
		}
	}
	
	/**
	 * @see cliDaemon::terminate()
	 */
	function terminate() {
		$this->notify(
			new cliApplicationEvent(
				cliApplicationEvent::EVENT_INFORMATIONAL,
				'Shutting down outbound daemon...'
			)
		);
		$this->getQueueStats();
		$this->setStatusParam('Status', 'Stopped');
		$this->updateStatus();
		return true;
	}

	/**
	 * Returns $_Adaptors
	 *
	 * @return array
	 */
	function getAdaptors() {
		return $this->_Adaptors;
	}
	
	/**
	 * Add the adaptor to the internal cache
	 * 
	 * @param commsGatewayAdaptorBase $inAdaptor
	 * @return outboundDaemon
	 */
	function addAdaptor(commsGatewayAdaptorBase $inAdaptor) {
		if ( !array_key_exists(get_class($inAdaptor), $this->getAdaptors()) ) {
			$this->_Adaptors[get_class($inAdaptor)] = $inAdaptor;
		}
		return $this;
	}
	
	/**
	 * Returns the adaptor named $inAdaptorName, null if not found
	 * 
	 * @param string $inAdaptorName
	 * @return commsGatewayAdaptorBase
	 */
	function getAdaptor($inAdaptorName) {
		if ( array_key_exists($inAdaptorName, $this->getAdaptors()) ) {
			return $this->_Adaptors[$inAdaptorName];
		}
		return null;
	}
	
	/**
	 * Set $_Adaptors to $inAdaptors
	 *
	 * @param array $inAdaptors
	 * @return outboundDaemon
	 */
	function setAdaptors($inAdaptors) {
		if ( $inAdaptors !== $this->_Adaptors ) {
			$this->_Adaptors = $inAdaptors;
		}
		return $this;
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



/**
 * Initialise process controls
 */
cliProcessControls::initialise($oRequest, 'outboundDaemon');

/**
 * Attempt to daemonise process
 */
cliProcessControls::daemonise();

/**
 * @var outboundDaemon $oDaemon
 */
$oDaemon = new outboundDaemon();
$oDaemon->setPosixId(cliProcessControls::getPosixId());
$oDaemon->setPidFile(cliProcessControls::getPidFile());
$oDaemon->setPosixUser(system::getConfig()->getSystemUserId());
$oDaemon->setPosixGroup(system::getConfig()->getSystemGroupGid());
$oDaemon->trapSignal(SIGINT, SIGHUP, SIGTERM);
$oDaemon->getListeners()->attachListener(new cliApplicationListenerLog());
$oDaemon->execute();