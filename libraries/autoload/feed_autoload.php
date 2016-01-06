<?php
/**
 * system Autoload component
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage system
 * @category systemAutoload
 */
return array(
	'feedManager' => 'feed/manager.class.php',
	'feedChannel' => 'feed/channel.class.php',
	'feedItem' => 'feed/item.class.php',
	'feedItemSet' => 'feed/item/set.class.php',

	'feedException' => 'feed/exception.class.php',
	'feedManagerException' => 'feed/exception.class.php',
	'feedManagerUnableToDetectFeedException' => 'feed/exception.class.php',
	'feedManagerUnableToReadFeedException' => 'feed/exception.class.php',
	'feedManagerUnsupportedFeedTypeException' => 'feed/exception.class.php',

	'feedReaderBase' => 'feed/reader/base.class.php',
	'feedReaderAtom' => 'feed/reader/atom.class.php',
	'feedReaderRss1' => 'feed/reader/rss1.class.php',
	'feedReaderRss2' => 'feed/reader/rss2.class.php',
);