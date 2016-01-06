<?php
/**
 * systemLogWriterScreen class
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage system
 * @category systemLogWriterScreen
 * @version $Rev: 707 $
 */


/**
 * systemLogWriterScreen Class
 * 
 * Writes log messages in HTML format for web processes. Optionally a "pretty"
 * flag causes the messages to be further marked up with span and classes for
 * styling. Otherwise the message is dumped in a <code><pre></pre></code> block.
 * There can only be a single screen writer used in the systemLog.
 * 
 * @package scorpio
 * @subpackage system
 * @category systemLogWriterScreen
 */
class systemLogWriterScreen extends systemLogWriter {
	
	/**
	 * Stores $_Pretty
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_Pretty;
	
	
	
	/**
	 * @see systemLogWriter::__construct()
	 * 
	 * @param systemLogFilter $inLogFilter
	 * @return systemLogWriter
	 */
	function __construct($inLogFilter) {
		parent::__construct('screen', $inLogFilter);
	}
	
	
	
	/**
	 * Main Methods
	 */
	
	/**
	 * @see systemLogWriter::_put()
	 */
	protected function _put($inMessage, $inSource) {
		/*
		 * Remove any carriage returns (ascii 13)
		 */
		$inMessage = preg_replace("/\r/", '', $inMessage);
		
		/*
		 * Add source prefix
		 */
		if ( $this->getPretty() ) {
			$inMessage = (($inSource) ? "<span class=\"systemLogMessageSource\">$inSource</span>" : '')." <span class=\"systemLogMessageText\">$inMessage</span>";
		} else {
			$inMessage = (($inSource) ? "$inSource" : '')." $inMessage";
		}
		
		if ( $this->getPretty() ) {
			echo '<div class="systemLogMessage">'.preg_replace("/\n/", '<br />', $inMessage).'</div>';
		} else {
			echo '<div class="systemLogMessage"><pre>'.$inMessage.'</pre></div>';
		}
	}
	
	/**
	 * Returns $_Pretty
	 *
	 * @return boolean
	 */
	function getPretty() {
		return $this->_Pretty;
	}
	 
	/**
	 * Sets $_Pretty to $inPretty
	 *
	 * @param boolean $inPretty
	 * @return systemLogWriterScreen
	 */
	function setPretty($inPretty) {
		if ( $this->_Pretty !== $inPretty ) {
			$this->_Pretty = $inPretty;
			$this->_Modified = true;
		}
		return $this;
	}
}