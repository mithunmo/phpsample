<?php
/**
 * translateExtractorBackendPhp class
 * 
 * Stored in translateExtractorBackendPhp.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage translate
 * @category translateExtractorBackendPhp
 * @version $Rev: 650 $
 */


/**
 * translateExtractorBackendPhp
 *
 * Handles PHP templates and files that contain text to be translated.
 * 
 * @package scorpio
 * @subpackage translate
 * @category translateExtractorBackendPhp
 */
class translateExtractorBackendPhp extends translateExtractorBackend {
	
	/**
	 * @see translateExtractorBackend::_extract()
	 *
	 * @param fileObject $inFile
	 * @return boolean
	 */
	protected function _extract(fileObject $inFile) {
		$content = $inFile->get();
		$opener = preg_quote($this->getOptions(self::OPTIONS_TRANSLATION_OPENING_MARKER)."('", '!');
		$closer = preg_quote("')", '!');
		
		/* Grab all of the tagged strings */
		$matches = array();
		preg_match_all("!{$opener}(.*?){$closer}!s", $content, $matches);
		
		if ( count($matches) > 0 && isset($matches[1]) && count($matches[1]) > 0 ) {
			foreach( $matches[1] as $str) {
				$this->addTranslation($str);
			}
		}
	}

	/**
	 * @see translateExtractorBackend::_validateOptions()
	 */
	protected function _validateOptions() {
		if ( !$this->getOptions(self::OPTIONS_TRANSLATION_OPENING_MARKER) ) {
			throw new translateException("Missing translation marker, this should be the function or method name");
		}
	}
}