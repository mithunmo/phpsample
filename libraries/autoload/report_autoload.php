<?php
/**
 * system Autoload component
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage system
 * @category systemAutoload
 */
return array(
	'reportBase' => 'report/base.class.php',
	'reportCollectionBase' => 'report/collection/base.class.php',
	'reportColumn' => 'report/column.class.php',

	'reportData' => 'report/data.class.php',
	'reportDataAbstract' => 'report/data/abstract.class.php',
	'reportDataAverage' => 'report/data/average.class.php',
	'reportDataSum' => 'report/data/sum.class.php',
	
	'reportException' => 'report/exception.class.php',
	'reportManagerException' => 'report/exception.class.php',
	'reportManagerUnknownOutputFormatException' => 'report/exception.class.php',
	'reportWriterException' => 'report/exception.class.php',
	'reportWriterOutputFileNotWritableException' => 'report/exception.class.php',
	
	'reportManager' => 'report/manager.class.php',
	'reportStyle' => 'report/style.class.php',

	'reportWriterBase' => 'report/writer/base.class.php',
	'reportWriterCsv' => 'report/writer/csv.class.php',
	'reportWriterExcel' => 'report/writer/excel.class.php',
	'reportWriterHtml' => 'report/writer/html.class.php',
	'reportWriterOds' => 'report/writer/ods.class.php',
	'reportWriterPdf' => 'report/writer/pdf.class.php',
	'reportWriterXls' => 'report/writer/xls.class.php',
	'reportWriterXlsx' => 'report/writer/xlsx.class.php',
	'reportWriterXml' => 'report/writer/xml.class.php',
);