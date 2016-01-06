<?php
/**
 * memTracker.class.php
 * 
 * mvcDistributorPluginMemTracker class
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage mvc
 * @category mvcDistributorPluginMemTracker
 * @version $Rev: 650 $
 */


/**
 * mvcDistributorPluginMemTracker class
 * 
 * Allows dispatch memory usage to be tracked per request. Similar to
 * {@link mvcDistributorPluginDispatchTimer dispatchTimer}, all results are
 * logged on a per site log file with request information.
 * 
 * Note: this tracks memory usage during dispatch and not for the entire
 * framework. If you need to test memory usage from using Scorpio you need
 * to record memory usage before including any Scorpio files and then after
 * the dispatcher has finished. In a web context, simply use:
 * memory_get_peak_usage(true) before including system.inc and then after the
 * call to dispatch().
 *
 * @package scorpio
 * @subpackage mvc
 * @category mvcDistributorPluginMemTracker
 */
class mvcDistributorPluginMemTracker extends mvcDistributorPlugin {
	
	/**
	 * Stores $_MemoryTracker
	 *
	 * @var utilityMemUsage
	 * @access protected
	 */
	protected $_MemoryTracker;
	
	
	
	/**
	 * Sets up memory usage logging
	 *
	 * @return void
	 */
	function executeOnDispatcherInitialise() {
		$this->setMemoryTracker(new utilityMemUsage());
		$this->getMemoryTracker()->takeReading();
	}
	
	/**
	 * Record memory usage for execution
	 *
	 * @return void
	 */
	function executeOnShutdown() {
		$usage = $this->getMemoryTracker()->takeReading();
		$oLog = systemLog::getInstance();
		$oldSource = $oLog->getSource();
		
		$logFileName = $this->_getLogFolder().'dispatchMemoryUsage.log';
		
		$oWriter = new systemLogWriterFile(
			$logFileName,
			new systemLogFilter(
				999997, 999997
			)
		);
		$oLog->setWriter($oWriter);
		
		$oLog->setSource(
			array(
				'Request' => $this->getRequest()->getRequestUri(),
			)
		);
		$oLog->log('Memory used: '.utilityStringFunction::humanReadableSize($usage), 999997);
		$oLog->setSource($oldSource);
		$oLog->removeWriter($oWriter);
	}
	
	

	/**
	 * Returns $_MemoryTracker
	 *
	 * @return utilityMemUsage
	 */
	function getMemoryTracker() {
		return $this->_MemoryTracker;
	}
	
	/**
	 * Set $_MemoryTracker to $inMemoryTracker
	 *
	 * @param utilityMemUsage $inMemoryTracker
	 * @return mvcDistributorPluginMemTracker
	 */
	function setMemoryTracker($inMemoryTracker) {
		if ( $inMemoryTracker !== $this->_MemoryTracker ) {
			$this->_MemoryTracker = $inMemoryTracker;
		}
		return $this;
	}
}