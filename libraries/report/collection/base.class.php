<?php
/**
 * reportCollectionBase
 * 
 * Stored in reportCollectionBase.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage report
 * @category reportCollectionBase
 * @version $Rev: 773 $
 */


/**
 * reportCollectionBase
 * 
 * A report collection allows several reports to be aggregated together into one
 * report. Each sub-report is run individually and uses it's own caching system
 * so if the individual reports are run the results are still cached.
 * 
 * Using this report is slightly different to the main {@link reportBase} class.
 * Instead of having to implement a _run and isValid() methods, you just need the
 * {@link reportBase::initialise()} method and then in this method add the reports
 * that should be aggregated together.
 * 
 * There are a few points to note: aggregated reports require ALL parameters for
 * all sub-reports be set before it can be run. If these are shared, they only need
 * to be set once. It is not possible for aggregate reports to run with separate
 * settings per report.
 * 
 * Report options should be passed to the sub-reports when they are created - the
 * same with the report style. This ensures that the options and style is available
 * when the initialise() method is called on the sub-reports during instantiation.
 * 
 * Finally: only certain output writers support report collections. These are:
 * {@link reportWriterHtml html}, {@link reportWriterExcel Excel}, {@link reportWriterOds OpenDocument}
 * and {@link reportWriterPdf PDF}. CSV and XML are not supported because the data
 * will not make any sense if it is compiled whereas ODS, Excel (both XLS and XLSX),
 * HTML and PDF allow multiple reports to be presented in a meaningful manner.
 * 
 * An example that combines 3 reports into one, each sub-report should exist already.
 * <code>
 * class myCustomerReportCollection extends reportCollectionBase {
 * 
 *     function initialise() {
 *         $this->addReport(new myNewCustomerReport($this->getOptionsSet()->getOptions(), $this->getReportStyle()));
 *         $this->addReport(new myCustomerPurchasesReport($this->getOptionsSet()->getOptions(), $this->getReportStyle()));
 *         $this->addReport(new myCustomerIssuesReport($this->getOptionsSet()->getOptions(), $this->getReportStyle()));
 *     }
 * }
 * 
 * // run report & output
 * $oReport = new myCustomerReportCollection(
 *     array(
 *         reportBase::OPTION_USE_CACHE => true,
 *         reportBase::OPTION_OUTPUT_TYPE => reportManager::OUTPUT_HTML,
 *     )
 * );
 * $oReport->run();
 * $oWriter = $oReport->getReportWriter();
 * $oWriter->compile();
 * 
 * header("Content-Type: ".$oWriter->getMimeType());
 * readfile($oWriter->getFullPathToOutputFile());
 * </code>
 *
 * @package scorpio
 * @subpackage report
 * @category reportCollectionBase
 */
abstract class reportCollectionBase extends reportBase implements IteratorAggregate, Countable {
	
	/**
	 * Stores the array of reports to compile together
	 * 
	 * @var array
	 * @access protected
	 */
	protected $_Reports = array();
	
	

	/**
	 * Returns true if all report options are valid
	 *
	 * @return boolean
	 */
	function isValid() {
		$valid = true;
		foreach ( $this as $oReport ) {
			$valid = $oReport->isValid() && $valid;
		}
		return $valid;
	}
	
	/**
	 * Overridden run because we dont need caching in this report
	 *
	 * @return boolean
	 */
	function run() {
		return $this->_run();
	}
	
	/**
	 * Runs all sub-reports with the current options
	 *
	 * @return boolean
	 * @throws reportException
	 */
	function _run() {
		if ( false ) $oReport = new reportBase();
		foreach ( $this as $oReport ) {
			systemLog::message('Running sub-report: '.$oReport->getReportName());
			$oReport->run();
		}
		return true;
	}
	
	/**
	 * Returns the report description, should be overridden in child objects
	 *
	 * @return string
	 * @abstract 
	 */
	function getReportDescription() {
		return '';
	}

	/**
	 * Returns an array of supported output writers for the collection report
	 * 
	 * @return array
	 */
	function getSupportedReportWriters() {
		return array(
			reportManager::OUTPUT_HTML,
			reportManager::OUTPUT_ODS,
			reportManager::OUTPUT_PDF,
			reportManager::OUTPUT_XLS,
			reportManager::OUTPUT_XLSX,
		);
	}
	
	
	
	/**
	 * Returns the number of sub-reports
	 * 
	 * @return integer
	 */
	function count() {
		return count($this->_Reports);
	}
	
	/**
	 * Alias of count()
	 * 
	 * @return integer
	 */
	function getCount() {
		return $this->count();
	}
	
	/**
	 * Returns the iterator allowing the object to be iterated in foreach loops
	 * 
	 * @return ArrayIterator
	 */
	function getIterator() {
		return new ArrayIterator($this->_Reports);
	}
	
	/**
	 * Adds a report to the collection, injecting options from this report
	 * 
	 * @param reportBase $inReport
	 * @return reportCollectionBase
	 */
	function addReport(reportBase $inReport) {
		if ( !array_key_exists($inReport->getReportName(), $this->_Reports) ) {
			$this->_Reports[$inReport->getReportName()] = $inReport;
		}
		return $this;
	}
	
	/**
	 * Returns the reports in this collection
	 * 
	 * @return array
	 */
	function getReports() {
		return $this->_Reports;
	}
	
	/**
	 * Sets an array of reports to the collection
	 * 
	 * @param array $inArray
	 * @return reportCollectionBase 
	 */
	function setReports(array $inArray = array()) {
		foreach ( $inArray as $oReport ) {
			$this->addReport($oReport);
		}
		return $this;
	}
}