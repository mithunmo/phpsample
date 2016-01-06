<?php
/**
 * translateExtractorCompilerArray class
 * 
 * Stored in translateExtractorCompilerArray.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage translate
 * @category translateExtractorCompilerArray
 * @version $Rev: 650 $
 */


/**
 * translateExtractorCompilerArray
 *
 * Compiles the data to a PHP array
 * 
 * @package scorpio
 * @subpackage translate
 * @category translateExtractorCompilerArray
 */
class translateExtractorCompilerArray extends translateExtractorCompiler {
	
	/**
	 * @see translateExtractorCompiler->_compile
	 */
	protected function _compile() {
		$this->_LanguageResource = '<?php
/**
 * Language data auto-generated on '.date('Y-m-d H:i:s').'
 *
 * Source language: '.$this->getOptions(self::OPTIONS_SOURCE_LANGUAGE).'
 * messageid:translation
 */
return array(
';
		foreach ( $this->_TranslationTable as $key => $string ) {
			$this->_LanguageResource .= " '$key' => '$string',\n";
		}
		
		$this->_LanguageResource .= '
);';
	}
	
	/**
	 * @see translateExtractorCompiler->_validateOptions
	 */
	protected function _validateOptions() {
		
	}
}