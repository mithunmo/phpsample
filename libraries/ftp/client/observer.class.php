<?php
/**
 * ftpClientObserver class
 *
 * This class implements the Observer part of a Subject-Observer
 * design pattern. It listens to the events sent by a ftpClient instance.
 *
 * PHP version 5
 *
 * LICENSE: This source file is subject to version 3.0 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_0.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @package scorpio
 * @subpackage ftp
 * @category ftpClientObserver
 * @author Dave Redfern
 * @author Tobias Schlitt <toby@php.net>
 * @author Laurent Laville <pear@laurent-laville.org>
 * @author Chuck Hagenbuch <chuck@horde.org>
 * @copyright 2009 Dave Redfern
 * @copyright 1997-2008 The PHP Group
 * @license http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version CVS: $Id: Observer.php,v 1.5 2008/01/07 14:23:00 jschippers Exp $
 * @version $Rev: 468 $
 */


/**
 * This class implements the Observer part of a Subject-Observer
 * design pattern. It listens to the events sent by a ftpClient instance.
 * This module had many influences from the Log_observer code.
 *
 * This is a port of the Net_FTP_Observer PEAR class to be fully PHP5 compliant.
 * It revises the error model to throw exceptions as well as tidying up the options.
 * 
 * @package scorpio
 * @subpackage ftp
 * @category ftpClientObserver
 * @access public
 * @abstract 
 */
abstract class ftpClientObserver {
	
    /**
     * Instance-specific unique identification number.
     *
     * @var integer
     * @access private
     */
    private $_id;

    /**
     * Creates a new basic Net_FTP_Observer instance.
     *
     * @access public
     */
    function __construct() {
        $this->_id = md5(microtime());
    }

    /**
     * Returns the listener's identifier
     *
     * @return string The listener's identifier
     * @access public
     */
    function getId() {
        return $this->_id;
    }

    /**
     * This is a stub method to make sure that Net_FTP_Observer classes do
     * something when they are notified of a message.  The default behavior
     * is to just do nothing.
     * You should override this method.
     *
     * @param mixed $event A hash describing the net event.
     *
     * @return void
     * @access public
     * @abstract 
     */
    abstract function notify($event);
}