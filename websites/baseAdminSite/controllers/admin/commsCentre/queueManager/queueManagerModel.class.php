<?php
/**
 * queueManagerModel.class.php
 * 
 * queueManagerModel class
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category queueManagerModel
 * @version $Rev: 272 $
 */


/**
 * queueManagerModel class
 * 
 * Provides the "queueManager" page
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category queueManagerModel
 */
class queueManagerModel extends commsOutboundMessageQueue implements mvcDaoModelInterface {
	
	/**
	 * Returns a list of objects, optionally from $inOffset for $inLimit
	 *
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @return array
	 */
	function getObjectList($inOffset = null, $inLimit = 30) {
		return commsOutboundMessageQueue::listOfObjects($inOffset, $inLimit);
	}
	
	/**
	 * Returns the object primary key value
	 *
	 * @return string
	 */
	function getPrimaryKey() {
		return parent::getPrimaryKey();
	}
	
	/**
	 * Returns total object count for this table
	 *
	 * @return integer
	 */
	function getTotalObjects() {
		$query = '
			SELECT COUNT(*) AS Count
			  FROM '.system::getConfig()->getDatabase('comms').'.outboundMessagesQueue';
		
		$oRes = dbManager::getInstance()->query($query);
		$res = $oRes->fetch();
		if ( is_array($res) && count($res) > 0 ) {
			return $res['Count'];
		} else {
			return 0;
		}
	}
	
	/**
	 * Returns the limit needed to get to the last page of results
	 *
	 * @param integer $inLimit
	 * @return integer
	 */
	function getLastPageOffset($inLimit) {
		$total = $this->getTotalObjects();
		
		if ( $inLimit > 0 ) {
			return $inLimit*floor($total/$inLimit);
		} else {
			return 0;
		}
	}

	/**
	 * Returns a new blank object
	 *
	 * @return systemDaoInterface
	 */
	function getNewObject() {
		return new commsOutboundMessageQueue();
	}
	
	/**
	 * Loads an existing object with $inPrimaryKey
	 *
	 * @param string $inPrimaryKey
	 * @return systemDaoInterface
	 */
	function getExistingObject($inPrimaryKey) {
		$this->setMessageID($inPrimaryKey);
		$this->load();
		return $this;
	}
	
	/**
	 * Clears all messages in the outbound queue, returning rows affected
	 * 
	 * @return integer
	 */
	function clearQueue() {
		systemLog::message('Preparing to clear all queued outbound messages');
		$count = 0;

		$total = $this->getTotalObjects();
		if ( $total > 10000 ) {
			$total = 10000;
		}
		systemLog::message("Clearing $total messages from queue");
		
		$objects = $this->listOfObjects(0, $total);
		if ( count($objects) > 0 ) {
			foreach ( $objects as $oQueueItem ) {
				$count += (int) commsOutboundQueue::failMessage($oQueueItem->getMessage());
				$oQueueItem->delete();
			}
		}
		systemLog::message("Cleared $count messages from the queue");
		return $count;
	}
}