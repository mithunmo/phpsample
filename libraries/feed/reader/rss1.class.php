<?php
/**
 * feedReaderRss1
 * 
 * Stored in feedReaderRss1
 *
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage feed
 * @category feedReaderRss1
 * @version $Rev: 650 $
 */


/**
 * feedReaderRss1
 * 
 * Parses RSS1 feeds into {@link feedChannel} objects.
 * 
 * @package scorpio
 * @subpackage feed
 * @category feedReaderRss1
 */
class feedReaderRss1 extends feedReaderBase {
	
	/**
	 * @see feedReaderBase::parse()
	 */
	function parse() {
		$oXml = $this->getDomAsSimpleXml();
		
		$oFeedChannel = new feedChannel();
		$oFeedChannel->setTitle(utilityXmlFunction::getValue($oXml->channel, 'title', ''));
		$oFeedChannel->setDescription(utilityXmlFunction::getValue($oXml->channel, 'description', ''));
		$oFeedChannel->setLanguage(utilityXmlFunction::getValue($oXml->channel, 'language', 'en'));
		$oFeedChannel->setGenerator(utilityXmlFunction::getValue($oXml->channel, 'generator', ''));
		$oFeedChannel->setTtl(utilityXmlFunction::getValue($oXml->channel, 'ttl', ''));
		$oFeedChannel->setLink(utilityXmlFunction::getValue($oXml->channel, 'link', ''));
		
		if ( count($oXml->item) > 0 ) {
			for ( $i=0; $i<count($oXml->item); $i++ ) {
				$oEntry = $oXml->item[$i];
				$oFeedChannel->getItemSet()->addItem(feedItem::factoryFromXml($oEntry));
			}
		}
		return $oFeedChannel;
	}
}