<?php
/**
 * dispatchTimer.class.php
 * 
 * mvcDistributorPluginDispatchTimer class
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage mvc
 * @category mvcDistributorPluginDispatchTimer
 * @version $Rev: 707 $
 */


/**
 * mvcDistributorPluginDispatchTimer class
 * 
 * Allows the dispatch to be timed and the output to be sent to a timer log.
 * Timer data is logged on a per-site basis. This times only from preDispatch
 * to postDispatch and does not include any set-up time for the environment
 * or preloading of other plugins.
 *
 * @package scorpio
 * @subpackage mvc
 * @category mvcDistributorPluginDispatchTimer
 */
class mvcDistributorPluginDispatchTimer extends mvcDistributorPlugin {
	
	/**
	 * Stores $_StopWatch
	 *
	 * @var utilityStopWatch
	 * @access protected
	 */
	protected $_StopWatch;
	
	
	
	/**
	 * Sets up logging
	 *
	 * @return void
	 */
	function executeOnDispatcherInitialise() {
		$this->setStopWatch(new utilityStopWatch());
	}
	

	/**
	 * Executes the plugin in pre-dispatch phase
	 *
	 * @return void
	 * @abstract 
	 */
	function executePreDispatch() {
		$this->getStopWatch()->start();
	}
	
	/**
	 * Executes the plugin in post-dispatch phase
	 *
	 * @return void
	 * @abstract 
	 */
	function executePostDispatch() {
		$this->getStopWatch()->stop();
		$oLog = systemLog::getInstance();
		$oldSource = $oLog->getSource();
		
		$logFileName = $this->_getLogFolder().'dispatchTime.log';
		
		$oWriter = new systemLogWriterFile(
			$logFileName,
			new systemLogFilter(
				999998, 999998
			)
		);
		$oLog->setWriter($oWriter);
		
		$oLog->setSource(
			array(
				'Request' => $this->getRequest()->getRequestUri(),
			)
		);
		$oLog->log($this->getStopWatch()->__toString()."\n--", 999998);
		$oLog->setSource($oldSource);
		$oLog->removeWriter($oWriter);
	} 
	
	

	/**
	 * Returns $_StopWatch
	 *
	 * @return utilityStopWatch
	 */
	function getStopWatch() {
		return $this->_StopWatch;
	}
	
	/**
	 * Set $_StopWatch to $inStopWatch
	 *
	 * @param utilityStopWatch $inStopWatch
	 * @return mvcDistributorPluginDispatchTimer
	 */
	function setStopWatch($inStopWatch) {
		if ( $inStopWatch !== $this->_StopWatch ) {
			$this->_StopWatch = $inStopWatch;
		}
		return $this;
	}
}