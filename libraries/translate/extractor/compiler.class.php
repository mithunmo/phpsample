<?php
/**
 * translateExtractorCompiler class
 * 
 * Stored in translateExtractorCompiler.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage translate
 * @category translateExtractorCompiler
 * @version $Rev: 650 $
 */


/**
 * translateExtractorCompiler
 *
 * Compiler that converts an array of language strings into an appropriate
 * format for the specified translation adaptor. Each adaptor can add
 * additional options or requirements. If a compiler is not present for 
 * your chosen language file, simply extend the abstract class and
 * implement _compile() and _validateOptions().
 * 
 * For XML files there is an option to set whether or not the text strings
 * should be placed into CDATA sections or not. This may break compatibility
 * with the various formats, but does make it easier when dealing with
 * embedded HTML entities.
 * 
 * If it is not set, then the strings are escaped to be XML safe.
 * 
 * @package scorpio
 * @subpackage translate
 * @category translateExtractorCompiler
 */
abstract class translateExtractorCompiler {
	
	const OPTIONS_RESOURCE = 'resource';
	const OPTIONS_SOURCE_LANGUAGE = 'lang.source';
	const OPTIONS_TARGET_LANGUAGE = 'lang.target';
	const OPTIONS_USE_CDATA_SECTIONS = 'xml.use.cdata';
	
	/**
	 * Stores $_Options
	 *
	 * @var array
	 * @access protected
	 */
	protected $_Options;
	
	/**
	 * Stores $_TranslationTable
	 *
	 * @var array
	 * @access protected
	 */
	protected $_TranslationTable;
	
	/**
	 * Stores $_LanguageResource
	 *
	 * @var string
	 * @access protected
	 */
	protected $_LanguageResource;
	
	
	
	/**
	 * Creates a new backend object
	 *
	 * @param array $inOptions
	 */
	function __construct(array $inOptions = array()) {
		$this->reset();
		$this->setOptions($inOptions);
	}
	
	/**
	 * Resets the object
	 *
	 * @return void
	 */
	function reset() {
		$this->_Options = array();
		$this->_TranslationTable = array();
		$this->_LanguageResource = '';
	}
	
	/**
	 * Checks that all options have been set before processing methods are called
	 *
	 * @throws translateException
	 */
	function validateOptions() {
		if ( !$this->getOptions(self::OPTIONS_SOURCE_LANGUAGE) ) {
			throw new translateException("Missing source language, this must be specified");
		}
		if ( !systemLocale::isValidLocale($this->getOptions(self::OPTIONS_SOURCE_LANGUAGE), false) ) {
			throw new translateException("Supplied source locale ({$this->getOptions(self::OPTIONS_SOURCE_LANGUAGE)}) is not a valid locale");
		}
		if ( $this->getOptions(self::OPTIONS_TARGET_LANGUAGE) && !systemLocale::isValidLocale($this->getOptions(self::OPTIONS_TARGET_LANGUAGE), false) ) {
			throw new translateException("Supplied target locale ({$this->getOptions(self::OPTIONS_TARGET_LANGUAGE)}) is not a valid locale");
		}
		$this->_validateOptions();
	}
	
	/**
	 * Compiler specific validation instructions
	 *
	 * @throws translateException
	 * @abstract
	 */
	abstract protected function _validateOptions();
	
	/**
	 * Compiles the language resources
	 *
	 * @return void
	 * @throws translateException
	 */
	function compile() {
		$this->validateOptions();
		
		if ( count($this->getTranslationTable()) > 0 ) {
			$this->_compile();
		} else {
			throw new translateException("No translation data to compile");
		}
	}
	
	/**
	 * Compiles the translation table to a resource
	 *
	 * @return void
	 */
	abstract protected function _compile();
	
	
	
	/**
	 * Returns options or a specific option, null if not found 
	 *
	 * @param string $inOption (optional) The option to get, null for all
	 * @return mixed
	 */
	function getOptions($inOption = null) {
		if ( $inOption === null ) {
			return $this->_Options;
		}
		
		if ( isset($this->_Options[$inOption]) === true ) {
			return $this->_Options[$inOption];
		}
		return null;
	}
	
	/**
	 * Set options to $inOptions
	 * 
	 * $inOptions should be an array containing the key value pairs of options.
	 *
	 * @param array $inOptions
	 * @return translateExtractor
	 */
	function setOptions(array $inOptions = array()) {
		if ( count($inOptions) > 0 ) {
			foreach ( $inOptions as $key => $option ) {
				if ( (isset($this->_Options[$key]) && ($this->_Options[$key] != $option)) || !isset($this->_Options[$key]) ) {
					$this->_Options[$key] = $option;
				}
			}
		}
		return $this;
	}

	/**
	 * Returns $_TranslationTable
	 *
	 * @return array
	 */
	function getTranslationTable() {
		return $this->_TranslationTable;
	}
	
	/**
	 * Set $_TranslationTable to $inTranslationTable
	 *
	 * @param array $inTranslationTable
	 * @return translateExtractorBackend
	 */
	function setTranslationTable($inTranslationTable) {
		if ( $inTranslationTable !== $this->_TranslationTable ) {
			$this->_TranslationTable = $inTranslationTable;
		}
		return $this;
	}

	/**
	 * Returns $_LanguageResource
	 *
	 * @return string
	 */
	function getLanguageResource() {
		return $this->_LanguageResource;
	}
	
	/**
	 * Set $_LanguageResource to $inLanguageResource
	 *
	 * @param string $inLanguageResource
	 * @return translateExtractorCompiler
	 */
	function setLanguageResource($inLanguageResource) {
		if ( $inLanguageResource !== $this->_LanguageResource ) {
			$this->_LanguageResource = $inLanguageResource;
		}
		return $this;
	}
}