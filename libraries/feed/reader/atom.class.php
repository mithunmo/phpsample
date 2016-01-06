<?php
/**
 * feedReaderAtom
 * 
 * Stored in feedReaderAtom
 *
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage feed
 * @category feedReaderAtom
 * @version $Rev: 650 $
 */


/**
 * feedReaderAtom
 * 
 * Parses Atom feeds into {@link feedChannel} objects.
 * 
 * @package scorpio
 * @subpackage feed
 * @category feedReaderAtom
 */
class feedReaderAtom extends feedReaderBase {
	
	/**
	 * @see feedReaderBase::parse()
	 */
	function parse() {
		$oXml = $this->getDomAsSimpleXml();
		
		$oFeedChannel = new feedChannel();
		$oFeedChannel->setTitle(utilityXmlFunction::getValue($oXml, 'title', ''));
		$oFeedChannel->setDescription(utilityXmlFunction::getValue($oXml, 'description', ''));
		$oFeedChannel->setLanguage(utilityXmlFunction::getValue($oXml, 'language', 'en'));
		$oFeedChannel->setGenerator(utilityXmlFunction::getValue($oXml, 'generator', ''));
		$oFeedChannel->setTtl(utilityXmlFunction::getValue($oXml, 'ttl', ''));
		$oFeedChannel->setLink(utilityXmlFunction::getAttribute($oXml->link, 'href', ''));
		
		if ( count($oXml->entry) > 0 ) {
			for ( $i=0; $i<count($oXml->entry); $i++ ) {
				$oEntry = $oXml->entry[$i];
				
				$oItem = new feedItem();
				$oItem->setTitle(utilityXmlFunction::getValue($oEntry, 'title', ''));
				$oItem->setDescription(utilityXmlFunction::getValue($oEntry, 'summary', ''));
				$oItem->setGuid(utilityXmlFunction::getValue($oEntry, 'id', ''));
				$oItem->setLink(utilityXmlFunction::getAttribute($oEntry->link, 'href', ''));
				$oItem->setPublishDate(utilityXmlFunction::getValue($oEntry, 'updated', ''));
				
				$oFeedChannel->getItemSet()->addItem($oItem);
			}
		}
		
		return $oFeedChannel;
	}
}