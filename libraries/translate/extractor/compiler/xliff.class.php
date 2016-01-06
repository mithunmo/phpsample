<?php
/**
 * translateExtractorCompilerXliff class
 * 
 * Stored in translateExtractorCompilerXliff.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage translate
 * @category translateExtractorCompilerXliff
 * @version $Rev: 650 $
 */


/**
 * translateExtractorCompilerXliff
 *
 * Creates Xliff XML language files. This adaptor requires the target language.
 * 
 * @package scorpio
 * @subpackage translate
 * @category translateExtractorCompilerXliff
 */
class translateExtractorCompilerXliff extends translateExtractorCompiler {
	
	/**
	 * @see translateExtractorCompiler->_compile
	 */
	protected function _compile() {
		$oDom = new DOMDocument('1.0', 'UTF-8');
		$oDom->formatOutput = true;
		$oDom->preserveWhiteSpace = true;
		
		$oXliff = $oDom->createElementNS('urn:oasis:names:tc:xliff:document:1.1', 'xliff');
		$oXliff->setAttribute('version', '1.1');
		
		$oFile = $oDom->createElement('file');
		$oFile->setAttribute('original', $this->getOptions(self::OPTIONS_RESOURCE));
		$oFile->setAttribute('source-language', $this->getOptions(self::OPTIONS_SOURCE_LANGUAGE));
		$oFile->setAttribute('target-language', $this->getOptions(self::OPTIONS_TARGET_LANGUAGE));
		$oFile->setAttribute('datatype', 'plaintext');
		
		$oBody = $oDom->createElement('body');
		
		$i = 0;
		foreach ( $this->_TranslationTable as $key => $string ) {
			$oTransUnit = $oDom->createElement('trans-unit');
			$oTransUnit->setAttribute('id', ++$i);
			
			if ( $this->getOptions(self::OPTIONS_USE_CDATA_SECTIONS) ) {
				$oSource = $oDom->createElement('source');
				$oTarget = $oDom->createElement('target');
				
				$oSourceC = $oDom->createCDATASection($key);
				$oTargetC = $oDom->createCDATASection($string);
				
				$oSource->appendChild($oSourceC);
				$oTarget->appendChild($oTargetC);
			} else {
				$oSource = $oDom->createElement('source', utilityStringFunction::xmlString($key));
				$oTarget = $oDom->createElement('target', utilityStringFunction::xmlString($string));
			}
			
			$oTransUnit->appendChild($oSource);
			$oTransUnit->appendChild($oTarget);
			$oBody->appendChild($oTransUnit);
		}
		
		$oFile->appendChild($oBody);
		$oXliff->appendChild($oFile);
		$oDom->appendChild($oXliff);
		
		$this->setLanguageResource($oDom->saveXML());
	}
	
	/**
	 * @see translateExtractorCompiler->_validateOptions
	 */
	protected function _validateOptions() {
		if ( !$this->getOptions(self::OPTIONS_TARGET_LANGUAGE) ) {
			throw new translateException("Missing target language, this must be specified");
		}
	}
}