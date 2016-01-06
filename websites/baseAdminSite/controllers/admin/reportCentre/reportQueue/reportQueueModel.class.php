<?php
/**
 * reportQueueModel.class.php
 * 
 * reportQueueModel class
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category reportQueueModel
 * @version $Rev: 11 $
 */


/**
 * reportQueueModel class
 * 
 * Provides the "reportQueue" page
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category reportQueueModel
 */
class reportQueueModel extends reportCentreReportQueue implements mvcDaoModelInterface {
	
	/**
	 * Returns a list of objects, optionally from $inOffset for $inLimit
	 *
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @return array
	 */
	function getObjectList($inOffset = null, $inLimit = 30) {
		return reportCentreReportQueue::listOfObjects($inOffset, $inLimit);
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
			  FROM '.system::getConfig()->getDatabase('reports').'.reportQueue';
		
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
		return new reportCentreReportQueue();
	}
	
	/**
	 * Loads an existing object with $inPrimaryKey
	 *
	 * @param string $inPrimaryKey
	 * @return systemDaoInterface
	 */
	function getExistingObject($inPrimaryKey) {
		$scheduled = substr($inPrimaryKey, 0, strrpos($inPrimaryKey, ':'));
		$reportID = substr($inPrimaryKey, strrpos($inPrimaryKey, ':')+1);
		
		$this->setScheduled($scheduled);
		$this->setReportID($reportID);
		$this->load();
		return $this;
	}
	

	/**
	 * Deletes the object from the table
	 *
	 * @return boolean
	 */
	function delete() {
		$oReport = $this->getReport();
		$oReport->delete();
		
		return parent::delete();
	}

	/**
	 * Clears all messages in the outbound queue, returning rows affected
	 * 
	 * @return integer
	 */
	function clearQueue() {
		systemLog::message('Preparing to clear all queued reports');
		$count = 0;
		$objects = $this->listOfObjects(0, $this->getTotalObjects());
		if ( count($objects) > 0 ) {
			foreach ( $objects as $oQueueItem ) {
				$oQueueItem->getReport()->delete();
				$oQueueItem->delete();
				++$count;
			}
		}
		systemLog::message("Cleared $count messages from the queue");
		return $count;
	}
}