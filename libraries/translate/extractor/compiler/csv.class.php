<?php
/**
 * translateExtractorCompilerCsv class
 * 
 * Stored in translateExtractorCompilerCsv.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage translate
 * @category translateExtractorCompilerCsv
 * @version $Rev: 650 $
 */


/**
 * translateExtractorCompilerCsv
 *
 * Compiles the data to a CSV file. If the delimiter, length or enclosure are
 * missing, the defaults set in translateAdaptorCsv are used instead.
 * 
 * @package scorpio
 * @subpackage translate
 * @category translateExtractorCompilerCsv
 */
class translateExtractorCompilerCsv extends translateExtractorCompiler {
	
	const OPTIONS_DELIMITER = 'csv.delimiter';
	const OPTIONS_LENGTH = 'csv.length';
	const OPTIONS_ENCLOSURE = 'csv.enclosure';
	
	/**
	 * @see translateExtractorCompiler->_compile
	 */
	protected function _compile() {
		$this->_LanguageResource = '
# Language data auto-generated on '.date('Y-m-d H:i:s').'
#
# Source language: '.$this->getOptions(self::OPTIONS_SOURCE_LANGUAGE).'
# messageid:translation
';
		foreach ( $this->_TranslationTable as $key => $string ) {
			$key = $this->escape($key);
			$string = $this->escape($string);
			$this->_LanguageResource .= $key.$this->getOptions(self::OPTIONS_DELIMITER).$string."\n";
		}
	}
	
	/**
	 * @see translateExtractorCompiler->_validateOptions
	 */
	protected function _validateOptions() {
		$options = array();
		if ( !$this->getOptions(self::OPTIONS_DELIMITER) ) {
			$options[self::OPTIONS_DELIMITER] = ';';
		}
		if ( !$this->getOptions(self::OPTIONS_LENGTH) ) {
			$options[self::OPTIONS_LENGTH] = 0;
		}
		if ( !$this->getOptions(self::OPTIONS_ENCLOSURE) ) {
			$options[self::OPTIONS_ENCLOSURE] = '"';
		}
		$this->setOptions($options);
		
		if ( strlen($this->getOptions(self::OPTIONS_DELIMITER)) > 1 ) {
			throw new translateException("CSV delimiter cannot be longer than 1 character");
		}
		if ( strlen($this->getOptions(self::OPTIONS_ENCLOSURE)) > 1 ) {
			throw new translateException("CSV enclosure cannot be longer than 1 character");
		}
	}
	
	/**
	 * Checks the string for the delimiter and escapes it with the enclosure if present
	 *
	 * @param string $inString
	 * @return string
	 */
	function escape($inString) {
		if ( strpos($inString, $this->getOptions(self::OPTIONS_DELIMITER)) !== false ) {
			return $this->getOptions(self::OPTIONS_ENCLOSURE).$inString.$this->getOptions(self::OPTIONS_ENCLOSURE);
		}
		return $inString;
	}
}