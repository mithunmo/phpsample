<?php
/**
 * translateExtractor class
 * 
 * Stored in translateExtractor.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage translate
 * @category translateExtractor
 * @version $Rev: 707 $
 */


/**
 * translateExtractor
 *
 * translateExtractor provides a framework for extracting terms to be translated by
 * parsing templates and classes for a project. It requires a backend system to do
 * the actual extraction of terms. Several are available in the package, however
 * others can be created by implementing the abstract base.
 * 
 * The various extractor backends should compile data into an array which can then
 * be used by the compilers. These build actual language files from the data and
 * ensure it is the correct format for the various translateAdaptor types. Again
 * additional output formats can be supported by adding another compiler
 * implementation.
 * 
 * For some formats e.g. Gettext, additional transformation is required. Additionally
 * this format can be created using the gettext development package and tools.
 * 
 * A basic example to extract all marked up text from a website called example.com:
 * <code>
 * $oExtractor = new translateExtractor(
 *     translateExtractor::BACKEND_TEMPLATE_SMARTY,
 *     translateExtractor::COMPILER_QT,
 *     array(
 *         translateExtractor::OPTIONS_BACKEND => array(
 *             translateExtractorBackend::OPTIONS_SCAN => translateExtractorBackend::OPTIONS_RESOURCE_WEBSITE,
 *             translateExtractorBackend::OPTIONS_RESOURCE_RECURSE => true,
 *             translateExtractorBackend::OPTIONS_RESOURCE_EXTN => 'tpl',
 *             translateExtractorBackend::OPTIONS_RESOURCE_LOCATION => 'example.com',
 *             translateExtractorBackend::OPTIONS_TRANSLATION_MARKER_TYPE =>  OPTIONS_TRANSLATION_MARKER_TAG,
 *             translateExtractorBackend::OPTIONS_TRANSLATION_OPENING_MARKER = '{t}',
 *             translateExtractorBackend::OPTIONS_TRANSLATION_CLOSING_MARKER = '{/t}',
 *         ),
 *         translateExtractor::OPTIONS_COMPILER => array(
 *             translateExtractorCompiler::OPTIONS_SOURCE_LANGUAGE => 'en',
 *             translateExtractorCompiler::OPTIONS_TARGET_LANGUAGE => 'fr',
 *             translateExtractorCompiler::OPTIONS_USE_CDATA_SECTIONS => false,
 *         )
 *     )
 * );
 * $langData = $oExtractor->execute();
 * </code>
 * 
 * @package scorpio
 * @subpackage translate
 * @category translateExtractor
 */
class translateExtractor {
	
	/**
	 * Stores $_Backend
	 *
	 * @var translateExtractorBackend
	 * @access protected
	 */
	protected $_Backend;
	
	const BACKEND_PHP_FILE = 'php';
	const BACKEND_TEMPLATE_SMARTY = 'smarty';
	
	/**
	 * Stores $_Compiler
	 *
	 * @var translateExtractorCompiler
	 * @access protected
	 */
	protected $_Compiler;
	
	const COMPILER_ARRAY = 'array';
	const COMPILER_CSV = 'csv';
	const COMPILER_INI = 'ini';
	const COMPILER_GETTEXT = 'gettext';
	const COMPILER_QT = 'qt';
	const COMPILER_TMX = 'tmx';
	const COMPILER_XLIFF = 'xliff';
	
	/**
	 * Stores $_Options
	 *
	 * @var array
	 * @access protected
	 */
	protected $_Options;
	
	const OPTIONS_BACKEND = 'Backend';
	const OPTIONS_COMPILER = 'Compiler';
	
	/**
	 * Array of permitted backends
	 *
	 * @var array
	 * @access private
	 * @static
	 */
	private static $_Backends = array(
		self::BACKEND_PHP_FILE, self::BACKEND_TEMPLATE_SMARTY
	);
	
	/**
	 * Array of permitted compilers
	 *
	 * @var array
	 * @access private
	 * @static
	 */
	private static $_Compilers = array(
		self::COMPILER_ARRAY, self::COMPILER_CSV, self::COMPILER_GETTEXT, self::COMPILER_INI,
		self::COMPILER_QT, self::COMPILER_TMX, self::COMPILER_XLIFF
	);
	
	
	
	/**
	 * Creates a new extractor object, using $inBackend for extraction and $inCompiler for compiling
	 * 
	 * $inOptions is an associative array containing options for both the backend and compiler
	 * and has the following format:
	 * <code>
	 * $options = array(
	 *     translateExtractor::OPTIONS_BACKEND => array(),
	 *     translateExtractor::OPTIONS_COMPILER => array(),
	 * );
	 * </code>
	 *
	 * @param string $inBackend
	 * @param string $inCompiler
	 * @param array $inOptions
	 * @return translateExtractor
	 */
	function __construct($inBackend, $inCompiler, array $inOptions = array()) {
		$this->reset();
		if ( count($inOptions) > 0 ) {
			$this->setOptions($inOptions);
		}
		$this->setBackend($inBackend, $this->getOptions(self::OPTIONS_BACKEND));
		$this->setCompiler($inCompiler, $this->getOptions(self::OPTIONS_COMPILER));
	}

	
	
	/**
	 * Returns the array of valid backend engines
	 *
	 * @return array
	 * @static
	 */
	static function getExtractorBackends() {
		return self::$_Backends;
	}
	
	/**
	 * Returns the array of valid compiler engines
	 *
	 * @return array
	 * @static
	 */
	static function getExtractorCompilers() {
		return self::$_Compilers;
	}
	
	/**
	 * Returns true if $inBackend is a valid backend
	 *
	 * @return boolean
	 * @static
	 */
	static function isValidBackend($inBackend) {
		return in_array($inBackend, self::$_Backends);
	}
	
	/**
	 * Returns true if $inCompiler is a valid compiler
	 *
	 * @return boolean
	 * @static
	 */
	static function isValidCompiler($inCompiler) {
		return in_array($inCompiler, self::$_Compilers);
	}
	
	
	/**
	 * Runs the extraction and compilation processes, returns the compiled data
	 *
	 * @return string
	 * @throws translateException
	 */
	function execute() {
		$this->extract();
		$this->compile();
		return $this->getLanguageData();
	}
	
	/**
	 * Attempts to locate and extract the terms for translation
	 *
	 * @return void
	 * @throws translateException
	 */
	function extract() {
		$this->getBackend()->parseResource();
		$this->getBackend()->extract();
	}
	
	/**
	 * Attempts to compile the extracted strings into the requested format
	 *
	 * @return void
	 * @throws translateException
	 */
	function compile() {
		$this->getCompiler()->setOptions(
			array(
				translateExtractorCompiler::OPTIONS_RESOURCE => $this->getBackendOptions(
					translateExtractorBackend::OPTIONS_RESOURCE_LOCATION
				)
			)
		);
		$this->getCompiler()->setTranslationTable($this->getBackend()->getTranslationTable());
		$this->getCompiler()->compile();
	}
	
	/**
	 * Returns the translation table from the extractor backend
	 *
	 * @return array
	 */
	function getTranslationTable() {
		return $this->getBackend()->getTranslationTable();
	}
	
	/**
	 * Returns the compiled language data
	 *
	 * @return string
	 */
	function getLanguageData() {
		return $this->getCompiler()->getLanguageResource();
	}
	
	/**
	 * Resets the object to defaults
	 *
	 * @return void
	 */
	function reset() {
		$this->_Backend = null;
		$this->_Compiler = null;
		$this->_Options = array(
			'Backend' => array(),
			'Compiler' => array(),
		);
	}
	
	
	
	/**
	 * Returns $_Backend
	 *
	 * @return translateExtractorBackend
	 */
	function getBackend() {
		return $this->_Backend;
	}
	
	/**
	 * Set $_Backend to $inBackend
	 *
	 * @param string $inBackend
	 * @param array $inOptions
	 * @return translateExtractor
	 * @throws translateException
	 */
	function setBackend($inBackend, array $inOptions = array()) {
		if ( !self::isValidBackend($inBackend) ) {
			throw new translateException("Extractor backend $inBackend is not a valid implementation");
		}
		$adaptor = 'translateExtractorBackend'.ucfirst($inBackend);
		
		$this->_Backend = new $adaptor($inOptions);
		if ( !$this->_Backend instanceof translateExtractorBackend ) {
			throw new translateException("Backend " . $adaptor . " does not extend translateExtractorBackend");
		}
		return $this;
	}
	
	/**
	 * Returns $_Compiler
	 *
	 * @return translateExtractorCompiler
	 */
	function getCompiler() {
		return $this->_Compiler;
	}
	
	/**
	 * Set $_Compiler to $inCompiler
	 *
	 * @param string $inCompiler
	 * @param array $inOptions
	 * @return translateExtractor
	 */
	function setCompiler($inCompiler, array $inOptions = array()) {
		if ( !self::isValidCompiler($inCompiler) ) {
			throw new translateException("Extractor compiler $inCompiler is not a valid implementation");
		}
		$adaptor = 'translateExtractorCompiler'.ucfirst($inCompiler);
		
		$this->_Compiler = new $adaptor($inOptions);
		if ( !$this->_Compiler instanceof translateExtractorCompiler ) {
			throw new translateException("Compiler " . $adaptor . " does not extend translateExtractorCompiler");
		}
		return $this;
	}

	/**
	 * Returns options for the component or a specific option, null if not found 
	 *
	 * @param string $inComponent (required) Either Backend or Compiler
	 * @param string $inOption (optional) The option to get, null for all
	 * @return mixed
	 */
	function getOptions($inComponent, $inOption = null) {
		if ( in_array($inComponent, array('Backend', 'Compiler')) ) {
			if ( $inOption === null ) {
				return $this->_Options[$inComponent];
			}
			
			if ( isset($this->_Options[$inComponent][$inOption]) === true ) {
				return $this->_Options[$inComponent][$inOption];
			}
		}
		return null;
	}
	
	/**
	 * Returns the backend option or all of them if $inOption is null
	 *
	 * @param string $inOption
	 * @return mixed
	 */
	function getBackendOptions($inOption = null) {
		return $this->getOptions(self::OPTIONS_BACKEND, $inOption);
	}

	/**
	 * Returns the compiler option or all of them if $inOption is null
	 *
	 * @param string $inOption
	 * @return mixed
	 */
	function getCompilerOptions($inOption = null) {
		return $this->getOptions(self::OPTIONS_COMPILER, $inOption);
	}
	
	/**
	 * Set options to $inOptions
	 * 
	 * $inOptions should be an array containing the component key and then key
	 * value pairs of options. Alternatively use {@link translateExtractor::setBackendOptions}
	 * or {@link translateExtractor::setCompilerOptions} to set those individually.
	 *
	 * @param array $inOptions
	 * @return translateExtractor
	 */
	function setOptions(array $inOptions = array()) {
		if ( count($inOptions) > 0 ) {
			foreach ( $inOptions as $component => $options ) {
				if ( count($this->_Options[$component]) == 0 ) {
					$this->_Options[$component] = $options;
				} else {
					foreach ( $options as $key => $option ) {
						if ( (isset($this->_Options[$component][$key]) && ($this->_Options[$component][$key] != $option)) || !isset($this->_Options[$component][$key]) ) {
							$this->_Options[$component][$key] = $option;
						}
					}
				}
			}
		}
		return $this;
	}
	
	/**
	 * Sets the backend options
	 *
	 * @param array $inOptions
	 * @return translateExtractor
	 * @link translateExtractor::setOptions
	 */
	function setBackendOptions(array $inOptions = array()) {
		return $this->setOptions(array(self::OPTIONS_BACKEND => $inOptions));
	}
	
	/**
	 * Sets the compiler options
	 *
	 * @param array $inOptions
	 * @return translateExtractor
	 * @link translateExtractor::setOptions
	 */
	function setCompilerOptions(array $inOptions = array()) {
		return $this->setOptions(array(self::OPTIONS_COMPILER => $inOptions));
	}
}