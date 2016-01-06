<?php
/**
 * translateExtractorCompilerQt class
 * 
 * Stored in translateExtractorCompilerQt.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage translate
 * @category translateExtractorCompilerQt
 * @version $Rev: 650 $
 */


/**
 * translateExtractorCompilerQt
 *
 * Creates QT style TS XML language files from the translation table.
 * All strings are encoded into CDATA sections to allow for any illegal XML
 * characters.
 * 
 * @package scorpio
 * @subpackage translate
 * @category translateExtractorCompilerQt
 */
class translateExtractorCompilerQt extends translateExtractorCompiler {
	
	/**
	 * @see translateExtractorCompiler->_compile
	 */
	protected function _compile() {
		$oDocType = DOMImplementation::createDocumentType('TS');
		
		$oDom = DOMImplementation::createDocument(null, null, $oDocType);
		$oDom->formatOutput = true;
		$oDom->preserveWhiteSpace = true;
		
		$oTS = $oDom->createElement('TS');
		$oContext = $oDom->createElement('context');
		$oName = $oDom->createElement('name', $this->getOptions(self::OPTIONS_RESOURCE));
		
		$oContext->appendChild($oName);
		
		foreach ( $this->_TranslationTable as $key => $string ) {
			$oMessage = $oDom->createElement('message');
			
			if ( $this->getOptions(self::OPTIONS_USE_CDATA_SECTIONS) ) {
				$oSource = $oDom->createElement('source');
				$oTarget = $oDom->createElement('translation');
				
				$oSourceC = $oDom->createCDATASection($key);
				$oTargetC = $oDom->createCDATASection($string);
				
				$oSource->appendChild($oSourceC);
				$oTarget->appendChild($oTargetC);
			} else {
				$oSource = $oDom->createElement('source', utilityStringFunction::xmlString($key));
				$oTarget = $oDom->createElement('translation', utilityStringFunction::xmlString($string));
			}
			
			$oMessage->appendChild($oSource);
			$oMessage->appendChild($oTarget);
			
			$oContext->appendChild($oMessage);
		}
		
		$oTS->appendChild($oContext);
		$oDom->appendChild($oTS);
		
		$this->setLanguageResource($oDom->saveXML());
	}
	
	/**
	 * @see translateExtractorCompiler->_validateOptions
	 */
	protected function _validateOptions() {
		
	}
}