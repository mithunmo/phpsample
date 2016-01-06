<?php
/**
 * systemLogWriterDb class
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage system
 * @category systemLogWriterDb
 * @version $Rev: 707 $
 */


/**
 * systemLogWriterDb Class
 * 
 * Writes log messages to a database that will then be written to files by
 * a single process e.g. loggingd. This implementation uses the object
 * {@link systemLogQueue} for MySQL.
 * 
 * The database writer inherits from the {@link systemLogWriterFile}, therefore
 * the same formatting is applied to messages in this as they are in the file
 * writer.
 * 
 * @package scorpio
 * @subpackage system
 * @category systemLogWriterDb
 */
class systemLogWriterDb extends systemLogWriterFile {
	
	/**
	 * Main Methods
	 */
	
	/**
	 * @see systemLogWriter::_put()
	 */
	protected function _put($inMessage, $inSource) {
		$inMessage = $this->formatMessage($inMessage, $inSource);
		
		try {
			$oQueue = new systemLogQueue();
			$oQueue->setLogFile($this->getLogLocation());
			$oQueue->setLogMessage($inMessage);
			$oQueue->save();
		} catch ( Exception $e ) {
			
		}
	}
}