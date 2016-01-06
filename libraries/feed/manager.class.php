<?php
/**
 * feedManager
 * 
 * Stored in feedManager
 *
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage feed
 * @category feedManager
 * @version $Rev: 650 $
 */


/**
 * feedManager
 * 
 * feedManager is the primary interface used for reading and parsing website
 * RSS and content feeds. It can handle auto-detection of the feed type and
 * will return a {@link feedChannel} object that represents the feed.
 * 
 * feedManager can be used optionally with the {@link cacheController} for
 * feed caching.
 * 
 * feedManager supports RSS1/2 and Atom feeds. Additional types can be added
 * by updating the ReaderMap array and implementing a new reader.
 * 
 * Example usage:
 * <h4>Fetch a feed and iterate</h4>
 * <code>
 * $oFeed = feedManager::getInstance()->fetch('http://www.theregister.co.uk/headlines.atom');
 * foreach ( $oFeed->getItemSet() as $oFeedItem ) {
 *     // do something...
 *     echo $oFeedItem->getTitle(), '<br />';
 * }
 * </code>
 * 
 * <h4>Fetch a feed, cache the results and iterate</h4>
 * <code>
 * $oCacheWriter = new cacheWriterFile(system::getConfig()->getPathTemp().'/feeds');
 * $oCacheWriter->setUseSubFolders(false);
 * feedManager::setCache(new cacheController($oCacheWriter));
 * $oFeed = feedManager::getInstance()->fetch('http://www.theregister.co.uk/headlines.atom');
 * foreach ( $oFeed->getItemSet() as $oFeedItem ) {
 *     // do something...
 *     echo $oFeedItem->getTitle(), '<br />';
 * }
 * </code>
 * 
 * @package scorpio
 * @subpackage feed
 * @category feedManager
 */
class feedManager {
	
	/**
	 * Stores an instance of the cacheController
	 * 
	 * @var cacheController
	 * @access private
	 * @static
	 */
	private static $_Cache = null;
	
	/**
	 * Maps feed types to reader classes
	 * 
	 * @var array
	 * @access private
	 * @static
	 */
	private static $_ReaderMap = array(
		self::TYPE_ATOM_1 => 'atom',
		self::TYPE_RSS_1 => 'rss1',
		self::TYPE_RSS_2 => 'rss2',
	);
	
	/**
	 * Stores $_FeedUri
	 *
	 * @var string
	 * @access protected
	 */
	protected $_FeedUri = null;
	
	/**
	 * Stores $_FeedType
	 *
	 * @var string
	 * @access protected
	 */
	protected $_FeedType = null;
	
	/**
	 * Stores $_DomDocument
	 *
	 * @var DOMDocument
	 * @access protected
	 */
	protected $_DomDocument = null;
	
	const NAMESPACE_RDF = 'http://www.w3.org/1999/02/22-rdf-syntax-ns#';
	const NAMESPACE_RSS_1 = 'http://purl.org/rss/1.0/';
	const NAMESPACE_ATOM_1 = 'http://www.w3.org/2005/Atom';
	
	const TYPE_AUTO = 'auto';
	const TYPE_RSS_ANY = 'rss';
	const TYPE_RSS_1 = 'rss1';
	const TYPE_RSS_2 = 'rss2';
	const TYPE_ATOM_ANY = 'atom';
	const TYPE_ATOM_1 = 'atom1';
	
	
	
	/**
	 * Returns a new instance of the feedManager
	 * 
	 * @return feedManager
	 * @static
	 */
	static function getInstance() {
		return new feedManager();
	}
	
	/**
	 * Set the cacheController
	 * 
	 * @param cacheController $inCache
	 * @return void
	 * @static
	 */
	static function setCache(cacheController $inCache) {
		self::$_Cache = $inCache;
	}
	
	/**
	 * Returns the cacheController instance
	 * 
	 * @return cacheController
	 * @static
	 */
	static function getCache() {
		return self::$_Cache;
	}
	
	/**
	 * Returns true if a cacheController instance has been set
	 * 
	 * @return boolean
	 * @static
	 */
	static function hasCache() {
		return (self::$_Cache instanceof cacheController);
	}
	
	
	
	/**
	 * Fetches the feed specified in $inURI and returns a feedChannel object
	 * 
	 * @param string $inUri
	 * @param string $inType A TYPE_ constant, defaults to AUTO
	 * @return feedChannel
	 * @throws feedManagerUnableToReadFeedException
	 */
	function fetch($inUri, $inType = self::TYPE_AUTO) {
		$this->setFeedUri($inUri);
		$this->setFeedType($inType);
		if ( self::hasCache() ) {
			$oCache = self::getCache();
			$oCache->setCacheId('FeedManager'.sha1($inUri));
			if ( $oCache->isCached() && !$oCache->isExpired() ) {
				$data = $oCache->getData();
				if ( $data instanceof feedChannel ) {
					return $data;
				}
			}
		}
		
		$feed = utf8_encode(trim(utilityCurl::fetchContent($inUri)));
		if ( !$feed || strlen($feed) < 1 ) {
			throw new feedManagerUnableToReadFeedException($inUri);
		}
		
		$oDom = new DOMDocument();
		$oDom->loadXML($feed);
		$this->setDomDocument($oDom);
		
		if ( $this->getFeedType() == self::TYPE_AUTO ) {
			$this->setFeedType($this->detectType());
		}
		
		$oFeed = $this->parseFeed();
		if ( self::hasCache() && $oFeed instanceof feedChannel ) {
			self::getCache()->setCacheId('FeedManager'.sha1($inUri));
			self::getCache()->cache($oFeed);
		}
		return $oFeed;
	}
	
	/**
	 * Detects the type of feed
	 * 
	 * @return string
	 * @throws feedManagerUnableToDetectFeedException
	 */
	function detectType() {
        $oPath = new DOMXPath($this->getDomDocument());
        if ( $oPath->query('/rss')->length ) {
			$version = $oPath->evaluate('string(/rss/@version)');
			if ( strlen($version) > 0 ) {
				switch ( $version ) {
					case '2.0':
						return self::TYPE_RSS_2;
				}
			}
		}
		
		$oPath->registerNamespace('rdf', self::NAMESPACE_RDF);
		if ( $oPath->query('/rdf:RDF')->length ) {
			$oPath->registerNamespace('rss', self::NAMESPACE_RSS_1);
			
			if (
				$oPath->query('/rdf:RDF/rss:channel')->length || $oPath->query('/rdf:RDF/rss:image')->length ||
				$oPath->query('/rdf:RDF/rss:item')->length || $oPath->query('/rdf:RDF/rss:textinput')->length
			) {
				return self::TYPE_RSS_1;
			}
		}
		
		$type = self::TYPE_ATOM_ANY;
		$oPath->registerNamespace('atom', self::NAMESPACE_ATOM_1);
		if ( $oPath->query('//atom:feed')->length ) {
			return self::TYPE_ATOM_1;
		}
		
		throw new feedManagerUnableToDetectFeedException($this->getFeedUri());
	}
	
	/**
	 * Creates the appropriate parser for the feed, and parses the feed
	 * 
	 * @return feedChannel
	 * @throws feedManagerUnsupportedFeedTypeException
	 */
	function parseFeed() {
		if ( !array_key_exists($this->getFeedType(), self::$_ReaderMap) ) {
			throw new feedManagerUnsupportedFeedTypeException($this->getFeedUri(), $this->getFeedType());
		}
		
		$class = 'feedReader'.ucfirst(self::$_ReaderMap[$this->getFeedType()]);
		$oReader = new $class($this->getDomDocument());
		return $oReader->parse();
	}
	
	
	
	/**
	 * Returns $_DomDocument
	 *
	 * @return DOMDocument
	 * @access public
	 */
	function getDomDocument() {
		return $this->_DomDocument;
	}
	
	/**
	 * Set $_DomDocument to $inDomDocument
	 *
	 * @param DOMDocument $inDomDocument
	 * @return feedManager
	 * @access public
	 */
	function setDomDocument($inDomDocument) {
		if ( $this->_DomDocument !== $inDomDocument ) {
			$this->_DomDocument = $inDomDocument;
		}
		return $this;
	}

	/**
	 * Returns $_FeedUri
	 *
	 * @return string
	 * @access public
	 */
	function getFeedUri() {
		return $this->_FeedUri;
	}
	
	/**
	 * Set $_FeedUri to $inFeedUri
	 *
	 * @param string $inFeedUri
	 * @return feedManager
	 * @access public
	 */
	function setFeedUri($inFeedUri) {
		if ( $this->_FeedUri !== $inFeedUri ) {
			$this->_FeedUri = $inFeedUri;
		}
		return $this;
	}

	/**
	 * Returns $_FeedType
	 *
	 * @return string
	 * @access public
	 */
	function getFeedType() {
		return $this->_FeedType;
	}
	
	/**
	 * Set $_FeedType to $inFeedType
	 *
	 * @param string $inFeedType
	 * @return feedManager
	 * @access public
	 */
	function setFeedType($inFeedType) {
		if ( $this->_FeedType !== $inFeedType ) {
			$this->_FeedType = $inFeedType;
		}
		return $this;
	}
}