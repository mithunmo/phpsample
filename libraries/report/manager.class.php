<?php
/**
 * reportManager
 * 
 * Stored in reportManager.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage report
 * @category reportManager
 * @version $Rev: 771 $
 */


/**
 * reportManager
 * 
 * Report management and utility class. This class contains methods for
 * returning the permitted writers and for creating the writer instances.
 *
 * @package scorpio
 * @subpackage report
 * @category reportManager
 */
class reportManager {
	
	const OUTPUT_CSV = 'csv';
	const OUTPUT_HTML = 'html';
	const OUTPUT_ODS = 'ods';
	const OUTPUT_PDF = 'pdf';
	const OUTPUT_XLS = 'xls';
	const OUTPUT_XLSX = 'xlsx';
	const OUTPUT_XML = 'xml';
	
	/**
	 * Returns an array of permitted output writers
	 *
	 * @return array
	 * @static
	 */
	static function getReportWriters() {
		return array(
			self::OUTPUT_CSV => 'Comma Separated Values (CSV)',
			self::OUTPUT_HTML => 'HTML Table',
			self::OUTPUT_ODS => 'OpenDocument Spreadsheet (ODS)',
			self::OUTPUT_PDF => 'Portable Document Format (PDF)',
			self::OUTPUT_XLS => 'Excel Spreadsheet 2003 (XLS)',
			self::OUTPUT_XLSX => 'Excel Spreadsheet 2007 (XLSX)',
			self::OUTPUT_XML => 'eXtensible Markup Language (XML)',
		);
	}
	
	/**
	 * Creates a reportWriter object from the report object
	 * 
	 * @param reportBase $inReport
	 * @return reportWriterBase
	 * @static
	 */
	static function factoryWriter(reportBase $inReport) {
		$class = 'reportWriter'.ucfirst(strtolower($inReport->getOutputType()));
		if ( class_exists($class) ) {
			return new $class($inReport);
		} else {
			throw new reportManagerUnknownOutputFormatException($inReport->getOutputType(), $class);
		}
	}
	
	/**
	 * Returns the full path to the filestore for the specified report
	 * 
	 * @param reportBase $inReport
	 * @return string
	 * @static
	 */
	static function buildFileStorePath(reportBase $inReport) {
		$filestore = false;
		if ( !$filestore ) {
			$filestore = utilityStringFunction::cleanDirSlashes(
				system::getConfig()->getPathTemp().'/reports/'.
				utilityStringFunction::normaliseStringCharactersForUri($inReport->getReportName(), '_')
			);
		}
		
		return $filestore;
	}
}