<?php
/**
 * feedReaderRss2
 * 
 * Stored in feedReaderRss2
 *
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage feed
 * @category feedReaderRss2
 * @version $Rev: 650 $
 */


/**
 * feedReaderRss2
 * 
 * Parses RSS2 feeds into {@link feedChannel} objects.
 * 
 * @package scorpio
 * @subpackage feed
 * @category feedReaderRss2
 */
class feedReaderRss2 extends feedReaderBase {
	
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
		
		if ( count($oXml->channel->item) > 0 ) {
			for ( $i=0; $i<count($oXml->channel->item); $i++ ) {
				$oEntry = $oXml->channel->item[$i];
				$oFeedChannel->getItemSet()->addItem(feedItem::factoryFromXml($oEntry));
			}
		}
		
		return $oFeedChannel;
	}
}