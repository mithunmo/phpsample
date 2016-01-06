<?php
/**
 * translateExtractorBackendSmarty class
 * 
 * Stored in translateExtractorBackendSmarty.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage translate
 * @category translateExtractorBackendSmarty
 * @version $Rev: 650 $
 */


/**
 * translateExtractorBackendSmarty
 *
 * Handles Smarty template files that contain text to be translated.
 * 
 * @package scorpio
 * @subpackage translate
 * @category translateExtractorBackendSmarty
 */
class translateExtractorBackendSmarty extends translateExtractorBackend {
	
	/**
	 * @see translateExtractorBackend::_extract()
	 *
	 * @param fileObject $inFile
	 * @return boolean
	 */
	protected function _extract(fileObject $inFile) {
		$content = $inFile->get();
		$opener = preg_quote($this->getOptions(self::OPTIONS_TRANSLATION_OPENING_MARKER), '!');
		$closer = preg_quote($this->getOptions(self::OPTIONS_TRANSLATION_CLOSING_MARKER), '!');
		
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
			throw new translateException("Missing translation opening marker, this must be specified");
		}
		if ( !$this->getOptions(self::OPTIONS_TRANSLATION_CLOSING_MARKER) ) {
			throw new translateException("Missing translation closing marker, this must be specified");
		}
	}
}