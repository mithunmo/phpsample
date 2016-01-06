<?php
/**
 * mvcDistributorPluginQueryStats.class.php
 *
 * mvcDistributorPluginQueryStats class
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package baseAdminSite
 * @subpackage websites_baseAdminSite_libraries
 * @category mvcDistributorPluginQueryStats
 * @version $Rev: 11 $
 */


/**
 * mvcDistributorPluginQueryStats class
 *
 * Logs out some information on the number of queries executed and the types.
 *
 * @package scorpio
 * @subpackage mvc
 * @category mvcDistributorPluginQueryStats
 */
class mvcDistributorPluginQueryStats extends mvcDistributorPlugin {
	
	/**
	 * Enables query stats logging during dispatch
	 * 
	 * @return void
	 */
	function executeOnDispatcherInitialise() {
		dbManager::getInstance()->getDbOptions()->setStats(true);
	}
	
	/**
	 * Outputs query stats to a log file
	 *
	 * @return void
	 */
	function executeOnShutdown() {
		$stats = dbManager::getInstance()->getQueryStats();
		
		$statsText = '';
		foreach ( $stats as $type => $data ) {
			$statsText .= $type.': '.$data['count'].'; ';
		}
		
		$oLog = systemLog::getInstance();
		$oldSource = $oLog->getSource();
		
		$logFileName = $this->_getLogFolder().'dispatchQueryStats.log';
		
		$oWriter = new systemLogWriterFile(
			$logFileName,
			new systemLogFilter(
				999996, 999996
			)
		);
		$oLog->setWriter($oWriter);
		
		$oLog->setSource(
			array(
				'Request' => $this->getRequest()->getRequestUri(),
				'Query' => $_SERVER['QUERY_STRING'],
			)
		);
		$oLog->log('Query stats: '.$statsText, 999996);
		$oLog->setSource($oldSource);
		$oLog->removeWriter($oWriter);
	}
}