<?php
/**
 * mofilmCommsNewsletterFilterBriefdownload
 *
 * Stored in mofilmCommsNewsletterFilterBriefdownload.class.php
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmCommsNewsletterFilterBriefdownload
 * @category mofilmCommsNewsletterFilterBriefdownload
 * @version $Rev: 73 $
 */
class mofilmCommsNewsletterFilterBriefdownload extends mofilmCommsNewsletterFilterlist {

	/**
	 * Constructs a new object based on param
	 *
	 * @param object mofilmCommsNewsletterdata
	 */
	function __construct($inNewsletterData) {
		parent::__construct($inNewsletterData);
	}

	/**
	 * Gets the Array of userID per event who downloaded the briefs
	 *
	 * @return array
	 */
	function apply() {
		$query = "
			SELECT DISTINCT users.ID
			  FROM ".system::getConfig()->getDatabase('mofilm_content').".userDownloads
			       INNER JOIN ".system::getConfig()->getDatabase('mofilm_content').".downloadFiles ON (
			           downloadFiles.ID = userDownloads.downloadID 
				   )
			       INNER JOIN ".system::getConfig()->getDatabase('mofilm_content').".users ON (
				       users.ID = userDownloads.userID
			       )
			 WHERE ";

		$where = array('downloadFiles.filetype = :FileType');
		if ( $this->getNewsletterData()->getParamSet()->getParam(mofilmCommsNewsletterdata::PARAM_NL_EVENTID) !== 0 ) {
			$where[] = ' userDownloads.eventID = :EventID ';
		}
		if ( count($where) == 0 ) {
			return false;
		}

		$query .= implode(' AND ', $where);

		$oStmt = dbManager::getInstance()->prepare($query);
		$oStmt->bindValue(':FileType', mofilmDownloadFile::FILETYPE_BRIEF);
		if ( $this->getNewsletterData()->getParamSet()->getParam(mofilmCommsNewsletterdata::PARAM_NL_EVENTID) !== 0 ) {
			$oStmt->bindValue(':EventID', $this->getNewsletterData()->getParamSet()->getParam(mofilmCommsNewsletterdata::PARAM_NL_EVENTID), PDO::PARAM_INT);
		}

		$return = array();
		if ( $oStmt->execute() ) {
			foreach ( $oStmt as $row ) {
				if ( $this->getNewsletterData()->getMessageType() == mofilmCommsNewsletterdata::NEWSLETTER_MKT_MESSAGE ) {
					if ( mofilmCommsEmail::getInstanceByUser($row['ID'])->getSubscribedStatus() ) {
						$resultList[] = $row;
					}

				} else {
					$resultList[] = $row;
				}

			}
		}
		$oStmt->closeCursor();

		return $resultList;
	}

}