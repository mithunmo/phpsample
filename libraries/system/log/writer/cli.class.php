<?php
/**
 * systemLogWriterCli class
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage system
 * @category systemLogWriterCli
 * @version $Rev: 707 $
 */


/**
 * systemLogWriterCli Class
 * 
 * Writes log messages to the command line. Messages are written using a 
 * normal echo rather than to STDERR or other location. There can only ever
 * be a single CLI writer attached to the systemLog instance.
 * 
 * @package scorpio
 * @subpackage system
 * @category systemLogWriterCli
 */
class systemLogWriterCli extends systemLogWriter {
	
	/**
	 * Stores $_LineLength
	 * 
	 * @var integer
	 * @access protected
	 */
	protected $_LineLength			= false;
	
	
	
	/**
	 * @see systemLogWriter::__construct()
	 * 
	 * @param systemLogFilter $inLogFilter
	 * @return systemLogWriter
	 */
	function __construct($inLogFilter) {
		parent::__construct('cli', $inLogFilter);
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
		$inMessage = (($inSource) ? "$inSource" : '')." $inMessage";
		echo wordwrap($inMessage, ($this->getLineLength() !== false ? $this->getLineLength() : systemLog::MAX_LINE_WIDTH))."\n";
	}
	
	
	
	/**
	 * Return LineLength
	 * 
	 * @return integer
	 */
	function getLineLength() {
		return $this->_LineLength;
	}
	
	/**
	 * Set $_LineLength to $inLineLength
	 * 
	 * @param integer $inLineLength
	 * @return systemLogWriterCli
	 */
	function setLineLength($inLineLength) {
		if ( $inLineLength !== $this->_LineLength ) {
			$this->_LineLength = $inLineLength;
			$this->_changed = true;
		}
		return $this;
	}
}