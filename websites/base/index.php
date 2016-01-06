<?php
/**
 * index.php 
 * 
 * Base website MVC implementation; handles requests into the system.
 * This is an example of how to use the MVC. You are free to use this
 * or modify it to your needs. The system requires system.inc at a
 * minimum for the auto-loaders and MVC classes.
 *
 * This file can be the single entry point for multiple applications,
 * or you can have one for each - it is up to you and your requirements.
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2009
 * @package scorpio
 * @subpackage websites_base
 * @version $Rev: 11 $
 */

/*
 * Load dependencies
 */
require_once(dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR.'libraries'.DIRECTORY_SEPARATOR.'system.inc');

/*
 * mvcDistributor supports a flag for the system being offline.
 *
 * If not set, check for an 'offline' file in the website root folder
 */
if ( !defined('SYSTEM_OFFLINE') ) {
	define('SYSTEM_OFFLINE', @file_exists(system::getConfig()->getPathWebsites().system::getDirSeparator().'offline'));
}
/*
 * mvcDistributor supports a flag for the database being offline
 *
 * This can be implemented as a 'ping' to the DB or via a socket
 * check or some other system (depending on DB / infrastructure).
 */
if ( !defined('SYSTEM_DATABASE_OFFLINE') ) {
	define('SYSTEM_DATABASE_OFFLINE', false);
}

/**
 * @var mvcDistributor $oDistributor
 */
$oDistributor = new mvcDistributor(mvcRequest::getInstance());
$oDistributor->initialise();
$oDistributor->dispatch();