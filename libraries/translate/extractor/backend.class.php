<?php
/**
 * translateExtractorBackend class
 * 
 * Stored in translateExtractorBackend.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage translate
 * @category translateExtractorBackend
 * @version $Rev: 650 $
 */


/**
 * translateExtractorBackend
 *
 * translateExtractorBackend provides a means of handling different types of
 * files and markup of text to be translated. It requires a concrete
 * implementation into a specific type e.g. the requirements for parsing a
 * PHP class or script file are different to a Smarty (or other) template.
 * 
 * The extractor should be able to operate on a single file or directory and
 * should create an internal array of all strings to be translated. The array
 * is structured as: "messageID => current text" so whatever the marked up
 * text is becomes the key that it will be identified by.
 * 
 * There are some restrictions placed on text. For example within PHP files
 * the text to be translated should be entirely enclosed in quotes - double or
 * single, but the string should not be separated. If variables need to be
 * added or displayed then ideally these should be replaced with using
 * {@link http://www.php.net/sprintf sprintf}. In Smarty templates any var
 * can be embedded as the template is pre-filtered to translate the text with
 * the template then being parsed again for variables.
 * 
 * 
 * <b>Extractor Backend Options</b>
 * 
 * The backend can have its own options, but the following are shared:
 * 
 * scan.type -- either file, directory or website
 * scan.location -- path of the file or directory
 * scan.dir.recurse -- (optional) set to recurse directories, default no
 * scan.extension -- (optional) an array of extensions that will be scanned
 * lang.source -- source language
 * lang.target -- the target language, required by some compilers e.g. xliff
 * trans.marker.type -- translation marker type, either function or tag
 * trans.marker.open -- the identifying opening tag or function name
 * trans.marker.close -- the closing tag, not required for function
 * 
 * @package scorpio
 * @subpackage translate
 * @category translateExtractorBackend
 */
abstract class translateExtractorBackend {
	
	/*
	 * Constants for generic options
	 */
	const OPTIONS_SCAN = 'scan.type';
	const OPTIONS_RESOURCE_LOCATION = 'scan.location';
	const OPTIONS_RESOURCE_FILE = 'file';
	const OPTIONS_RESOURCE_DIR = 'directory';
	const OPTIONS_RESOURCE_WEBSITE = 'website';
	
	const OPTIONS_RESOURCE_RECURSE = 'scan.dir.recurse';
	const OPTIONS_RESOURCE_EXTN = 'scan.extension';
	
	const OPTIONS_TRANSLATION_MARKER_TYPE = 'trans.marker.type';
	const OPTIONS_TRANSLATION_MARKER_FUNC = 'function';
	const OPTIONS_TRANSLATION_MARKER_TAG = 'tag';
	const OPTIONS_TRANSLATION_OPENING_MARKER = 'trans.marker.open';
	const OPTIONS_TRANSLATION_CLOSING_MARKER = 'trans.marker.close';
	
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
	 * Stores $_Resources
	 *
	 * @var array
	 * @access protected
	 */
	protected $_Resources;
	
	
	
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
		$this->_Resources = array();
	}
	
	/**
	 * Checks that all options have been set before processing methods are called
	 *
	 * @throws translateException
	 */
	function validateOptions() {
		if ( !$this->getOptions(self::OPTIONS_SCAN) ) {
			throw new translateException("Missing scan type option, this should be either file, directory or website");
		}
		if ( !$this->getOptions(self::OPTIONS_RESOURCE_LOCATION) ) {
			throw new translateException("Missing scan location, this should be either a file or directory");
		}
		$this->_validateOptions();
	}
	
	/**
	 * Backend specific validation instructions
	 *
	 * @throws translateException
	 * @abstract
	 */
	abstract protected function _validateOptions();
	
	/**
	 * Locates all files that should be processed by the backend storing the results in $_Resources
	 *
	 * @return void
	 */
	function parseResource() {
		$this->validateOptions();
		
		if ( $this->getOptions(self::OPTIONS_SCAN) == self::OPTIONS_RESOURCE_FILE ) {
			$this->_parseFile();
		} elseif ( $this->getOptions(self::OPTIONS_SCAN) == self::OPTIONS_RESOURCE_DIR ) {
			$this->_parseDirectory();
		} elseif ( $this->getOptions(self::OPTIONS_SCAN) == self::OPTIONS_RESOURCE_WEBSITE ) {
			$this->_parseWebsite();
		}
	}
	
	/**
	 * Parses a single file resource
	 * 
	 * @throws translateException
	 */
	protected function _parseFile() {
		$oFile = new fileObject($this->getOptions(self::OPTIONS_RESOURCE_LOCATION));
		if ( $oFile->exists() && $oFile->isReadable() ) {
			$this->setResources(array($oFile));
		} else {
			throw new translateException("File {$oFile->getOriginalFilename()} does not exist or could not be read");
		}
	}
	
	/**
	 * Parses a directory resource
	 * 
	 * @throws translateException
	 */
	protected function _parseDirectory() {
		if ( is_dir($this->getOptions(self::OPTIONS_RESOURCE_LOCATION)) && is_readable($this->getOptions(self::OPTIONS_RESOURCE_LOCATION)) ) {
			$files = fileObject::parseDir($this->getOptions(self::OPTIONS_RESOURCE_LOCATION), (is_null($this->getOptions(self::OPTIONS_RESOURCE_RECURSE)) ? false : true));
			$extensions = $this->getOptions(self::OPTIONS_RESOURCE_EXTN);
			foreach ( $files as $oFile ) {
				$add = false;
				if ( $oFile->exists() && $oFile->isReadable() ) {
					if ( is_array($extensions) && count($extensions) > 0 ) {
						if ( in_array($oFile->getExtension(), $extensions) ) {
							$add = true;
						}
					} else {
						$add = true;
					}
					
					if ( $add ) {
						$this->addResource($oFile);
					}
				}
			}
		} else {
			throw new translateException("Directory {$this->getOptions(self::OPTIONS_RESOURCE_LOCATION)} is not readable or does not exist");
		}
	}
	
	/**
	 * Parses a website resource
	 *
	 * @throws translateException
	 */
	protected function _parseWebsite() {
		$oSite = mvcSiteTools::getInstance($this->getOptions(self::OPTIONS_RESOURCE_LOCATION));
		if ( file_exists($oSite->getSitePath()) ) {
			try {
				$oSiteConfig = $oSite->getSiteConfig();
				$arrMap = $oSiteConfig->getControllerMapper()->getMapAsControllers();
				if ( false ) $oController = new mvcControllerMap();
				foreach ( $arrMap as $oController ) {
					$this->_findTranslationTargets($oSiteConfig, $oController);
				}
			} catch ( Exception $e ) {
				throw new translateException($e->getMessage());
			}
		} else {
			throw new translateException("Website {$this->getOptions(self::OPTIONS_RESOURCE_LOCATION)} is not registered in the system");
		}
	}
	
	/**
	 * Interrogates the controller to locate all the template files
	 *
	 * @param mvcSiteConfig $inSiteConfig
	 * @param mvcControllerMap $inController
	 */
	protected function _findTranslationTargets(mvcSiteConfig $inSiteConfig, mvcControllerMap $inController) {
		$location = $inSiteConfig->getFilePath('controllers'.system::getDirSeparator().$inController->getFilePath());
		if ( strlen($location) > 0 ) {
			$location = str_replace(
				system::getDirSeparator().'controllers'.system::getDirSeparator(),
				system::getDirSeparator().'views'.system::getDirSeparator(),
				dirname($location)
			);
			
			if ( file_exists($location) && is_dir($location) ) {
				$files = fileObject::parseDir($location, false);
				foreach ( $files as $oFile ) {
					if ( $oFile->exists() && $oFile->isReadable() ) {
						$this->addResource($oFile);
					}
				}
			}
		}
		if ( $inController->hasSubControllers() ) {
			foreach ( $inController->getSubControllers() as $oCtrl ) {
				$this->_findTranslationTargets($inSiteConfig, $oCtrl);
			}
		}
	}
	
	/**
	 * Extracts strings from the resources
	 *
	 * @return void
	 */
	function extract() {
		$this->validateOptions();
		
		if ( count($this->_Resources) > 0 ) {
			foreach ( $this->_Resources as $oFile ) {
				$this->_extract($oFile);
			}
		} else {
			throw new translateException("No resources found to process");
		}
	}
	
	/**
	 * The custom extraction implementation
	 *
	 * @param fileObject $inFile
	 * @return boolean
	 * @abstract
	 */
	abstract protected function _extract(fileObject $inFile);
	
	
	
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
				if ( $key == self::OPTIONS_RESOURCE_EXTN ) {
					$this->_Options[$key] = (array) $option;
				} elseif ( (isset($this->_Options[$key]) && ($this->_Options[$key] != $option)) || !isset($this->_Options[$key]) ) {
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
	 * Adds the string to the translation array, but only if it does not exist already
	 *
	 * @param string $inString
	 * @return translateExtractorBackend
	 */
	function addTranslation($inString) {
		if ( !array_key_exists($inString, $this->_TranslationTable) ) {
			$this->_TranslationTable[$inString] = $inString;
		}
		return $this;
	}
	
	/**
	 * Removes $inString from the translation table
	 *
	 * @param string $inString
	 * @return translateExtractorBackend
	 */
	function removeTranslation($inString) {
		if ( array_key_exists($inString, $this->_TranslationTable) ) {
			unset($this->_TranslationTable[$inString]);
		}
		return $this;
	}
	
	/**
	 * Returns true if $inString is in the translation table
	 *
	 * @param string $inString
	 * @return boolean
	 */
	function hasTranslation($inString) {
		return array_key_exists($inString, $this->_TranslationTable);
	}

	/**
	 * Returns $_Resources
	 *
	 * @return array
	 */
	function getResources() {
		return $this->_Resources;
	}
	
	/**
	 * Set $_Resources to $inResources
	 *
	 * @param array $inResources
	 * @return translateExtractorBackend
	 */
	function setResources($inResources) {
		if ( $inResources !== $this->_Resources ) {
			$this->_Resources = $inResources;
		}
		return $this;
	}
	
	/**
	 * Adds the file to the list of resources, only if it does not already exist
	 *
	 * @param fileObject $inFile
	 * @return translateExtractorBackend
	 */
	function addResource(fileObject $inFile) {
		if ( !in_array($inFile, $this->_Resources, true) ) {
			$this->_Resources[] = $inFile;
		}
		return $this;
	}
}