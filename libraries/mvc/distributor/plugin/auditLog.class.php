<?php
/**
 * log.class.php
 * 
 * mvcDistributorPluginAuditLog class
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage mvc
 * @category mvcDistributorPluginAuditLog
 * @version $Rev: 650 $
 */


/**
 * mvcDistributorPluginAuditLog class
 * 
 * Sets a per-site Audit log for capturing all AUDIT_SUCCESS/FAILURES
 *
 * @package scorpio
 * @subpackage mvc
 * @category mvcDistributorPluginAuditLog
 */
class mvcDistributorPluginAuditLog extends mvcDistributorPlugin {
	
	/**
	 * Sets up logging
	 *
	 * @return void
	 */
	function executeOnDispatcherInitialise() {
		$logFileName = $this->_getLogFolder().'audit.log';
		
		$oWriter = new systemLogWriterFile(
			$logFileName,
			new systemLogFilter(
				systemLogLevel::AUDIT_SUCCESS, systemLogLevel::AUDIT_FAILURE
			)
		);
		systemLog::getInstance()->setWriter($oWriter);
	}
}