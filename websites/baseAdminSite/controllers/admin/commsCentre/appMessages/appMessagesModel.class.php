<?php
/**
 * appMessagesModel.class.php
 * 
 * appMessagesModel class
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category appMessagesModel
 * @version $Rev: 393 $
 */


/**
 * appMessagesModel class
 * 
 * Provides the "appMessages" page
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category appMessagesModel
 */
class appMessagesModel extends commsApplicationMessage implements mvcDaoModelInterface {

	/**
	 * Stores the array of dynamic propeties per Message group
	 *
	 * @var array $applicationMessageGroupProp
	 * @access protected
	 */
	protected $applicationMessageGroupProp = array(
		mofilmMessages::MSG_GRP_CLIENT_PASSWORD_RESET => array(0 => "%user.password%"),
		mofilmMessages::MSG_GRP_CLIENT_REGISTRATION => array(0 => "%emailAddr%", "1" => "%regUri%"),
		mofilmMessages::MSG_GRP_ADMIN_PROMOTION => array(0 => "%emailAddr%"),
		mofilmMessages::MSG_GRP_ADMIN_REGISTRATION => array(0 => "%emailAddr%", "1" => "%regUri%"),
		
		9 => array(0 => "[AUDIO_TRACK_ISSUE]", 1 => "[INSERT_LINK]",2 => "[support@mofilm.com]", 3 => "[DATE]"),
		20 => array(0 => "[INSERT_TITLE]",1 => "[INSERT_DATE]"),
		21 => array(0 => "[ISSUE]",1 => "[branding/logo/scene]",2 => "[support@mofilm.com]", 3 => "[DATE]"),
		mofilmMessages::MSG_GRP_USR_UPLOADED_VIDEO => array(0 => "%MOVIE_ID%",1 => "%EVENT_NAME%",2 => "%BRAND_NAME%",3 => "%mofilm.username%"),
                mofilmMessages::MSG_GRP_MOPHOTO => array(0 => "%MOVIE_ID%",1 => "%EVENT_NAME%",2 => "%BRAND_NAME%",3 => "%mofilm.username%"),
		mofilmMessages::MSG_GRP_USR_VIDEO_ENCODED => array(0 => "%MOVIE_ID%",1 => "%EVENT_NAME%",2 => "%BRAND_NAME%",3 => "%mofilm.username%", 4 => "%mofilm.downloadlink%"),
		mofilmMessages::MSG_GRP_CLIENT_REGISTRATIONCN => array(0 => "%emailAddr%", "1" => "%regUri%"),
		mofilmMessages::MSG_GRP_USR_CREDIT => array(0 => "%MOVIE_ID%", 1 => "%MOVIE_TITLE%", 2 => "%EMAIL_ADDRESS%", 4 => "%ROLE%", 5 => "%LINK%"),
		mofilmMessages::MSG_GRP_USR_CREDIT_NEWREGISTER => array(0 => "%MOVIE_ID%", 1 => "%MOVIE_TITLE%", 2 => "%EMAIL_ADDRESS%", 4 => "%ROLE%", 5 => "%LINK%"),
		mofilmMessages::MSG_GRP_USR_GRNT_RECEIVED => array(0 => "%mofilm.username%", 1 => "%mofilm.brand%", 2 => "%mofilm.event%"),
		mofilmMessages::MSG_GRP_USR_GRNT_RESUBMIT => array(0 => "%mofilm.username%", 1 => "%mofilm.brand%", 2 => "%mofilm.event%"),
		mofilmMessages::MSG_GRP_USR_GRNT_APPROVED => array(0 => "%mofilm.username%", 1 => "%mofilm.brand%", 2 => "%mofilm.event%", 3 => "%mofilm.grantedAmount%"),
		mofilmMessages::MSG_GRP_USR_GRNT_REJECTED => array(0 => "%mofilm.username%", 1 => "%mofilm.brand%", 2 => "%mofilm.event%"),
		mofilmMessages::MSG_GRP_USR_GRNT_PENDING => array(0 => "%mofilm.username%", 1 => "%mofilm.brand%", 2 => "%mofilm.event%", 3 => "%mofilm.comments%"),
		mofilmMessages::MSG_GRP_UPLOADED_FILES_APPROVED => array(0 => "%mofilm.username%", 1 => "%mofilm.brand%", 2 => "%mofilm.event%", 3 => "%mofilm.downloadlink%"),
		mofilmMessages::MSG_GRP_UPLOADED_FILES_REJECTED => array(0 => "%mofilm.username%", 1 => "%mofilm.brand%", 2 => "%mofilm.event%"),
		mofilmMessages::MSG_GRP_CREW => array(0 => "%mofilm.username%", 1 => "%mofilm.message%"),
		mofilmMessages::MSG_GRP_REFERRAL => array(0 => "%mofilm.user%", 1 => "%mofilm.userhash%"),
		mofilmMessages::MSG_GRP_REFERRAL_CN => array(0 => "%mofilm.user%", 1 => "%mofilm.userhash%")
		
	);

	
	
	/**
	 * Returns a list of objects, optionally from $inOffset for $inLimit
	 *
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @return array
	 */
	function getObjectList($inOffset = null, $inLimit = 30) {
		return commsApplicationMessage::listOfObjects($inOffset, $inLimit);
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
			  FROM '.system::getConfig()->getDatabase('comms').'.applicationMessages';
		
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
		return new commsApplicationMessage();
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
	 * Gets the list of dynamic properties per applicationMessageGroup
	 *
	 * @param integer $inMessageGroupID
	 * @return array
	 */
	function getDynamicProperties($inMessageGroupID) {
		if ( array_key_exists($inMessageGroupID, $this->applicationMessageGroupProp) ) {
			return $this->applicationMessageGroupProp[$inMessageGroupID];
		} else {
			return array();
		}
	}

}
