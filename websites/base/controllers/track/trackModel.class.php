<?php
/**
 * trackModel.class.php
 * 
 * trackModel class
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package websites_base
 * @subpackage controllers
 * @category trackModel
 * @version $Rev: 623 $
 */


/**
 * trackModel class
 * 
 * Provides the "track" page
 * 
 * @package websites_base
 * @subpackage controllers
 * @category trackModel
 */
class trackModel extends mvcModelBase {
	
	/**
	 * @see mvcModelBase::__construct()
	 */
	function __construct() {
		parent::__construct();
	}

	/**
	 * Updates the read status of the user
	 * 
	 * @param integer $inId
	 * @return void
	 */
	function updateReadStatus($inId) {
		mofilmCommsNewsletterhistory::updateReadStatus($inId, mofilmCommsNewsletterhistory::VIEWED_NEWSLETTER);
	}

	/**
	 * Creates a newsletter link
	 *
	 * @param integer $inNewsletterID
	 * @param string $inLinkName
	 * @param integer $inUserId
	 * @param integer $inNewsletterHistoryId
	 * @return void
	 */
	function createNewsletterLink($inNewsletterID,$inLinkName,$inUserId,$inNewsletterHistoryId) {
		$oNewsletterLink = new mofilmCommsNewsletterlink();
		$oNewsletterLink->setLinkName($inLinkName);
		$oNewsletterLink->setNewsletterID($inNewsletterID);
		$oNewsletterLink->setStatus(1);
		$oNewsletterLink->setUserID($inUserId);
		$oNewsletterLink->save();
		mofilmCommsNewsletterhistory::updateReadStatus($inNewsletterHistoryId, mofilmCommsNewsletterhistory::VIEWED_NEWSLETTER);
		unset($oNewsletterLink);
	}

	/**
	 * Unsubscribes the user
	 *
	 * @param integer $inEmailId
	 * @param integer $inUserId
	 * @param integer $inNewsletterId
	 * @return void
	 */
	function unsubscribeEmail($inEmailId,$inUserId,$inNewsletterId) {
		$oSubscriptions = new mofilmCommsSubscription();
		$oSubscriptions->deleteByEmailID($inEmailId);

		$mofilmCommsUnsubscribe = new mofilmCommsNewsletterunsubscription();
		$mofilmCommsUnsubscribe->setEmailID($inEmailId);
		$mofilmCommsUnsubscribe->setUserID($inUserId);
		$mofilmCommsUnsubscribe->setNewsletterID($inNewsletterId);
		$mofilmCommsUnsubscribe->save();
	}
}