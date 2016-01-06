<?php
/**
 * translateExtractorCompilerIni class
 * 
 * Stored in translateExtractorCompilerIni.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage translate
 * @category translateExtractorCompilerIni
 * @version $Rev: 650 $
 */


/**
 * translateExtractorCompilerIni
 *
 * Compiles the data to an ini file. INI files have many restrictions on how
 * they can be formatted. For example: keys cannot contain anything other than
 * A-Z and 0-9. Additional punctuation {}[]() etc can have special meaning.
 * 
 * This compiler makes a "best" effort to encode the results into an INI file,
 * however you may have to edit both your templates and this file to get a
 * file that will actually be valid.
 * 
 * All string values are automatically enclosed in quotes regardless of length.
 * 
 * The option ini.gen.keys will generate fresh keys for the translations
 * automatically. This has to be enabled specifically.
 * 
 * @package scorpio
 * @subpackage translate
 * @category translateExtractorCompilerIni
 */
class translateExtractorCompilerIni extends translateExtractorCompiler {
	
	const OPTIONS_GENERATE_KEYS = 'ini.gen.keys';
	
	/**
	 * @see translateExtractorCompiler->_compile
	 */
	protected function _compile() {
		$this->_LanguageResource = '
[TranslationData]
; Language data auto-generated on '.date('Y-m-d H:i:s').'
;
; Source language: '.$this->getOptions(self::OPTIONS_SOURCE_LANGUAGE).'
; messageid:translation
';
		foreach ( $this->_TranslationTable as $key => $string ) {
			$key = $this->genKey($key);
			$string = $this->escape($string);
			$this->_LanguageResource .= $key.' = "'.$string.'"'."\n";
		}
	}
	
	/**
	 * @see translateExtractorCompiler->_validateOptions
	 */
	protected function _validateOptions() {
		
	}
	
	/**
	 * Builds a safe key value for the php parse_ini_file function
	 *
	 * @param string $inString
	 * @return string
	 */
	function genKey($inString) {
		if ( $this->getOptions(self::OPTIONS_GENERATE_KEYS) ) {
			return utilityStringFunction::normaliseStringCharactersForUri($inString, '_');
		}
		return $inString;
	}
	
	/**
	 * Escapes quotes in the string
	 *
	 * @param string $inString
	 * @return string
	 */
	function escape($inString) {
		return str_replace('"', '\"', $inString);
	}
}