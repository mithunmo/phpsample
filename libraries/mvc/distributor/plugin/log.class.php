<?php
/**
 * log.class.php
 * 
 * mvcDistributorPluginLog class
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage mvc
 * @category mvcDistributorPluginLog
 * @version $Rev: 650 $
 */


/**
 * mvcDistributorPluginLog class
 * 
 * Sets up custom logging based on the distributor servername and other parameters
 *
 * @package scorpio
 * @subpackage mvc
 * @category mvcDistributorPluginLog
 */
class mvcDistributorPluginLog extends mvcDistributorPlugin {
	
	/**
	 * Sets up logging
	 *
	 * @return void
	 */
	function executeOnDispatcherInitialise() {
		$logFileName = $this->_getLogFolder().'main.log';
		
		$oWriter = new systemLogWriterFile(
			$logFileName,
			new systemLogFilter(
				systemLogLevel::ALWAYS, $this->getRequest()->getDistributor()->getSiteConfig()->getSiteLogLevel()->getParamValue()
			)
		);
		systemLog::getInstance()->resetWriters()->setWriter($oWriter);
	}
}