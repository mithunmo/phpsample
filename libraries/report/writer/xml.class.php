<?php
/**
 * reportWriterXml
 * 
 * Stored in reportWriterXml.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage report
 * @category reportWriterXml
 * @version $Rev: 719 $
 */


/**
 * reportWriterXml
 * 
 * Converts the reportData object into an XML format. The XML is generated via
 * xmlwriter allowing for large XML files to be built without requiring a large
 * amount of RAM to store the temporary structures.
 * 
 * @package scorpio
 * @subpackage report
 * @category reportWriterXml
 */
class reportWriterXml extends reportWriterBase {
	
	/**
	 * @see reportWriterBase::initialise()
	 */
	function initialise() {
		$this->setExtension('xml');
		$this->setMimeType('application/xml');
	}
	
	/**
	 * @see reportWriterBase::_compile()
	 */
	function _compile() {
		$cols = $this->getReport()->getDisplayColumns();
		$dataCols = $this->getReport()->getDataColumns();
		
		$oXmlWriter = new XMLWriter();
		$oXmlWriter->openUri($this->getFullPathToOutputFile());
		$oXmlWriter->setIndent(true);
		$oXmlWriter->setIndentString('	');
		$oXmlWriter->startDocument('1.0', 'UTF-8');
		$oXmlWriter->flush();
		
		$oXmlWriter->startElement('report');
		$oXmlWriter->startElement('details');
		$oXmlWriter->writeElement('title', $this->_escape(system::getConfig()->getParam('app', 'title', 'Scorpio Framework').': '.$this->getReport()->getReportName()));
		$oXmlWriter->writeElement('description', $this->_escape($this->getReport()->getReportDescription()));
		$oXmlWriter->endElement();
		$oXmlWriter->flush();
		
		$oXmlWriter->startElement('data');
		$oXmlWriter->flush();
		
		foreach ( $this->getReport()->getReportData() as $id => $row ) {
			$oXmlWriter->startElement('row');
			$oXmlWriter->writeAttribute('id', $id);
			
			foreach ( $dataCols as $id => $field ) {
				if ( array_key_exists($field, $row) ) {
					if ( is_numeric($field) ) {
						$oXmlWriter->writeElement($cols[$id], $this->_escape($row[$field]));
					} else {
						$oXmlWriter->writeElement($field, $this->_escape($row[$field]));
					}
				}
			}
			$oXmlWriter->endElement();
			$oXmlWriter->flush();
		}
		$oXmlWriter->endElement();
		$oXmlWriter->endDocument();
		$oXmlWriter->flush();
		return true;
	}
	
	/**
	 * Escapes a string for XML
	 * 
	 * @param string $inString
	 * @return string
	 * @access private
	 */
	private function _escape($inString) {
		return htmlentities($inString, ENT_QUOTES, 'UTF-8');
	}
}