<?php
/**
 * commsGatewayAdaptorAppLoopBack
 *
 * Stored in commsGatewayAdaptorAppLoopBack.class.php
 *
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package comms
 * @subpackage gateway
 * @category commsGatewayAdaptorAppLoopBack
 * @version $Rev: 10 $
 */


/**
 * commsGatewayAdaptorAppLoopBack
 * 
 * Provides the interface for sending app loop back commands.
 *
 * @package comms
 * @subpackage gateway
 * @category commsGatewayAdaptorAppLoopBack
 */
class commsGatewayAdaptorAppLoopBack extends commsGatewayAdaptorBase {
	
	/**
	 * @see commsGatewayAdaptorBase::_preProcess() 
	 */
	protected function _preProcess() {
		
	}
	
	/**
	 * @see commsGatewayAdaptorBase::_postProcess()
	 * 
	 * @param transportResponse $inResponse
	 */
	protected function _postProcess(transportResponse $inResponse) {
		if ( $this->getSent() ) {
			systemLog::message('Message sent successfully');
		} else {
			systemLog::error('Message sending failed');
			/*
			 * Fail all remaining messages in this transaction
			 */
			$res = commsOutboundQueue::failMessagesByTransactionId($this->getMessage());
			systemLog::info('Failed '.$res.' other messages in queue');
			
			/*
			 * Purge the queue of this transactions messages
			 */
			$res = commsOutboundQueue::purgeMessagesByTransactionId($this->getMessage());
			systemLog::info('Purged '.$res.' other messages from queue');
		}
		$this->getMessage()->setSentDate(date(system::getConfig()->getDatabaseDatetimeFormat()));
		$this->getMessage()->setStatusID($this->getSent() ? commsOutboundStatus::S_COMPLETE : commsOutboundStatus::S_FAILED);
		$this->getMessage()->setComment($inResponse->getResponse());
		$this->getMessage()->save();
		
		$this->getMessage()->getQueue()->delete();
	}
}