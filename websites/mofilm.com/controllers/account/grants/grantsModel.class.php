<?php
/**
 * grantsModel.class.php
 * 
 * grantsModel class
 *
 * @author Pavan Kumar P G
 * @copyright Mofilm (c) 2009-2010
 * @package websites_mofilm.com
 * @subpackage controllers
 * @category grantsModel
 * @version $Rev: 623 $
 */


/**
 * grantsModel class
 * 
 * Provides the "grants" page
 * 
 * @package websites_mofilm.com
 * @subpackage controllers
 * @category grantsModel
 */
class grantsModel extends mofilmUserMovieGrants {
	
	/**
	 * Stores $_Offset
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_Offset;
	
	/**
	 * Returns a list of objects, optionally from $inOffset for $inLimit
	 *
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @return array
	 */
	function getObjectList($inOffset = null, $inLimit = 30) {
		return mofilmUserMovieGrants::listOfObjects($this->getUserID(), $inOffset, $inLimit);
	}
	
	/**
	 * Returns total object count for this table
	 *
	 * @return integer
	 */
	function getTotalObjects() {
		$query = '
			SELECT COUNT(*) AS Count
			  FROM '.system::getConfig()->getDatabase('mofilm_content').'.userMovieGrants WHERE userID ='.$this->getUserID();
		
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
		return new mofilmUserMovieGrants();
	}
	
	/**
	 * Return the current value of the property $_Offset
	 *
	 * @return integer
 	 */
	function getOffset() {
		return $this->_Offset;
	}

	/**
	 * Set the object property _Offset to $inOffset
	 *
	 * @param integer $inOffset
	 * @return Offset
	 */
	function setOffset($inOffset) {
		if ( $inOffset !== $this->_Offset ) {
			$this->_Offset = $inOffset;
			$this->setModified();
		}
		return $this;
	}
	
	/**
	 * Triggers an email when grant application is received
	 */
	function sendGrantsReciptEmail() {
		$oQueue = commsOutboundManager::newQueueFromApplicationMessageGroup(
			0, mofilmMessages::MSG_GRP_USR_GRNT_RECEIVED
		);
		
		$oUser = mofilmUserManager::getInstanceByID($this->getUserID());
		$oObject = $this->getGrants()->getSource();
		
		commsOutboundManager::setCustomerInMessageStack($oQueue, $this->getUserID());
		commsOutboundManager::setRecipientInMessageStack($oQueue, $oUser->getEmail());
		commsOutboundManager::replaceDataInMessageStack($oQueue, array('%mofilm.username%', '%mofilm.brand%', '%mofilm.event%'), array($oUser->getFullname(), $oObject->getName(), $oObject->getEvent()->getName()));

		return $oQueue->send();
	}
	
	/**
	 * Triggers an email when grant application is edited and re-submited
	 */
	function sendGrantsResubmitEmail() {
		$oQueue = commsOutboundManager::newQueueFromApplicationMessageGroup(
			0, mofilmMessages::MSG_GRP_USR_GRNT_RESUBMIT
		);
		
		$oUser = mofilmUserManager::getInstanceByID($this->getUserID());
		$oObject = $this->getGrants()->getSource();
		
		commsOutboundManager::setCustomerInMessageStack($oQueue, $this->getUserID());
		commsOutboundManager::setRecipientInMessageStack($oQueue, $oUser->getEmail());
		commsOutboundManager::replaceDataInMessageStack($oQueue, array('%mofilm.username%', '%mofilm.brand%', '%mofilm.event%'), array($oUser->getFullname(), $oObject->getName(), $oObject->getEvent()->getName()));

		return $oQueue->send();
	}
	
	/**
	 * Updates showreelurl value in userData table
	 */
	function saveShowReel($inShowReelURL) {
		if ( isset ($inShowReelURL) ) {
			$oUser = mofilmUserManager::getInstanceByID($this->getUserID());
			$oUser->getParamSet()->setParam(mofilmUser::PARAM_SHOW_REEL_URL, $inShowReelURL);
			$oUser->save();
		}
	}
}