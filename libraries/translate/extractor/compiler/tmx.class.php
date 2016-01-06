<?php
/**
 * translateExtractorCompilerTmx class
 * 
 * Stored in translateExtractorCompilerTmx.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage translate
 * @category translateExtractorCompilerTmx
 * @version $Rev: 650 $
 */


/**
 * translateExtractorCompilerTmx
 *
 * Creates pseudo TMX XML language files from the translation table.
 * 
 * @package scorpio
 * @subpackage translate
 * @category translateExtractorCompilerTmx
 */
class translateExtractorCompilerTmx extends translateExtractorCompiler {
	
	/**
	 * @see translateExtractorCompiler->_compile
	 */
	protected function _compile() {
		$oDom = new DOMDocument('1.0', 'UTF-8');
		$oDom->formatOutput = true;
		$oDom->preserveWhiteSpace = true;
		
		$oTmx = $oDom->createElement('tmx');
		$oTmx->setAttribute('version', '1.4');
		
		$oHeader = $oDom->createElement('header');
		$oHeader->setAttribute('creationtool', 'Scorpio Translate TMX Extractor');
		$oHeader->setAttribute('creationtoolversion', '$Rev: 650 $');
		$oHeader->setAttribute('datatype', 'unknown');
		$oHeader->setAttribute('segtype', 'sentence');
		$oHeader->setAttribute('o-tmf', 'abc');
		$oHeader->setAttribute('adminlang', $this->getOptions(self::OPTIONS_SOURCE_LANGUAGE));
		$oHeader->setAttribute('srclang', $this->getOptions(self::OPTIONS_SOURCE_LANGUAGE));
		$oHeader->setAttribute('creationdate', gmdate('Ymd\THis\Z'));
		
		$oTmx->appendChild($oHeader);
		$oBody = $oDom->createElement('body');
		
		foreach ( $this->_TranslationTable as $key => $string ) {
			$oTu = $oDom->createElement('tu');
			$oTuv = $oDom->createElement('tuv');
			$oTuv->setAttribute('xml:lang', $this->getOptions(self::OPTIONS_SOURCE_LANGUAGE));
			
			if ( $this->getOptions(self::OPTIONS_USE_CDATA_SECTIONS) ) {
				$oSeg = $oDom->createElement('seg');
				$oSegC = $oDom->createCDATASection($string);
				$oSeg->appendChild($oSegC);
			} else {
				$oSeg = $oDom->createElement('seg', utilityStringFunction::xmlString($string));
			}
			
			$oTuv->appendChild($oSeg);
			$oTu->appendChild($oTuv);
			$oBody->appendChild($oTu);
		}
		
		$oTmx->appendChild($oBody);
		$oDom->appendChild($oTmx);
		
		$this->setLanguageResource($oDom->saveXML());
	}
	
	/**
	 * @see translateExtractorCompiler->_validateOptions
	 */
	protected function _validateOptions() {
		
	}
}