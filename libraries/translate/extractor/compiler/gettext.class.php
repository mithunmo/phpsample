<?php
/**
 * translateExtractorCompilerGettext class
 * 
 * Stored in translateExtractorCompilerGettext.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage translate
 * @category translateExtractorCompilerGettext
 * @version $Rev: 650 $
 */


/**
 * translateExtractorCompilerGettext
 *
 * Creates gettext PO data from the translation table. This can also
 * be generated using the gettext CLI development tools. The PO files
 * will need to be further processed by msgfmt to create the .MO
 * binaries required by the translateAdaptorGettext.
 * 
 * @package scorpio
 * @subpackage translate
 * @category translateExtractorCompilerGettext
 */
class translateExtractorCompilerGettext extends translateExtractorCompiler {
	
	/**
	 * @see translateExtractorCompiler->_compile
	 */
	protected function _compile() {
		$this->_LanguageResource = '
# Language data auto-generated on '.date('Y-m-d H:i:s').'
# 
# Source language: '.$this->getOptions(self::OPTIONS_SOURCE_LANGUAGE).'
msgid ""
msgstr ""
"Project-Id-Version: i18n_'.$this->getOptions(self::OPTIONS_SOURCE_LANGUAGE).'\n"
"POT-Creation-Date: '.date('Y-m-d H:iO').'\n"
"PO-Revision-Date: '.date('Y-m-d H:iO').'\n"
"MIME-Version: 1.0\n"
"Content-Type: text/plain; charset=utf-8\n"
"Content-Transfer-Encoding: 8bit\n"
"X-Poedit-SourceCharset: utf-8\n"

';
		foreach ( $this->_TranslationTable as $key => $string ) {
			$key = $this->escape($key);
			$string = $this->escape($string);
			$this->_LanguageResource .= 'msgid "'.$key.'"'."\n".'msgstr "'.$string.'"'."\n\n";
		}
	}
	
	/**
	 * @see translateExtractorCompiler->_validateOptions
	 */
	protected function _validateOptions() {
		
	}
	
	/**
	 * Escapes quotes in $inString
	 *
	 * @param string $inString
	 * @return string
	 */
	function escape($inString) {
		$escape = array('"', "\n", "\t", "\r");
		$replace = array('\"', '\\n"' . "\n" . '"', '\\t', '\\r');
		return str_replace($escape, $replace, $inString);
	}
}