<?php
/**
 * generatorBase class
 * 
 * Stored in base.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage generator
 * @category generatorBase
 * @version $Rev: 650 $
 */


/**
 * generatorBase class
 * 
 * Provides the basic methods for a code generator system including access
 * to a building engine (Smarty), assignment of data, ability to build
 * dynamic template paths and names and an options system for extended
 * properties.
 * 
 * All generators extend from this base class and implement whatever
 * additional logic is required to build the component.
 * 
 * Generated content can be stored in the internal array $_GeneratedContent
 * how this is indexed is up to the generator class. It might be classname
 * class data, or path and class data depending on requirements.
 * 
 * @package scorpio
 * @subpackage generator
 * @category generatorBase
 */
abstract class generatorBase {

	/**
	 * Stores $_Modified
	 * 
	 * @var boolean
	 * @access protected
	 */
	protected $_Modified = false;
	
	/**
	 * Stores $_GeneratorName
	 *
	 * @var string
	 * @access protected
	 */
	protected $_GeneratorName;
	
	/**
	 * Holds instance of the templating engine
	 *
	 * @var Smarty
	 * @access protected
	 */
	protected $_Engine;
	
	/**
	 * Holds an instance of baseOptionsSet
	 *
	 * @var baseOptionsSet
	 * @access protected
	 */
	protected $_Options;
	
	const OPTION_PROPERTY_PACKAGE = 'props.package';
	const OPTION_PROPERTY_SUB_PACKAGE = 'props.subpackage';
	const OPTION_PROPERTY_CATEGORY = 'props.category';
	
	const OPTION_TEMPLATE_DIR_USER = 'dir.user.templates';
	const OPTION_TEMPLATE_DIR_DEFAULT = 'dir.default.templates';
	const OPTION_TEMPLATE_USER = 'template.user';
	const OPTION_TEMPLATE_DEFAULT = 'template.default';
	
	/**
	 * Stores $_DataSources
	 *
	 * @var array
	 * @access protected
	 */
	protected $_DataSources;
	
	/**
	 * Stores $_GeneratedContent
	 *
	 * @var array
	 * @access protected
	 */
	protected $_GeneratedContent;
	
	
	
	/**
	 * Creates a new generator object
	 *
	 * @param array $inOptions
	 */
	function __construct(array $inOptions = array()) {
		$this->reset();
		$this->getOptionsSet()->setOptions($inOptions);
		$this->initialise();
	}
	
	/**
	 * Performs custom initialisation on the object
	 *
	 * @return void
	 * @abstract
	 */
	function initialise() {}
	
	/**
	 * Acquires external data required for building objects
	 *
	 * @return void
	 * @abstract 
	 */
	function buildDataSource() {}
	
	/**
	 * Builds the code from the specified data
	 *
	 * @return void
	 * @abstract 
	 */
	abstract function build();
	
	/**
	 * Resets the object
	 *
	 * @return void
	 */
	function reset() {
		$this->_GeneratorName = get_class($this);
		$this->_Engine = null;
		$this->_Options = null;
		$this->_DataSources = array();
		$this->_GeneratedContent = array();
		$this->setModified(false);
	}
	
	
	
	/**
	 * Returns true if object has been modified
	 * 
	 * @return boolean
	 */
	function isModified() {
		$modified = $this->_Modified;
		if ( !$modified && $this->_Options instanceof baseOptionsSet ) {
			$modified = $modified || $this->_Options->isModified();
		}
		return $modified;
	}
	
	/**
	 * Set the status of the object if it has been changed
	 * 
	 * @param boolean $status
	 * @return generatorTestCase
	 */
	function setModified($status = true) {
		$this->_Modified = $status;
		return $this;
	}

	/**
	 * Returns $_GeneratorName
	 *
	 * @return string
	 */
	function getGeneratorName() {
		return $this->_GeneratorName;
	}
	
	/**
	 * Set $_GeneratorName to $inGeneratorName
	 *
	 * @param string $inGeneratorName
	 * @return generatorBase
	 */
	function setGeneratorName($inGeneratorName) {
		if ( $inGeneratorName !== $this->_GeneratorName ) {
			$this->_GeneratorName = $inGeneratorName;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_Engine
	 *
	 * @return systemSmartyBase
	 */
	function getEngine() {
		if ( !$this->_Engine instanceof systemSmartyBase ) {
			$this->_Engine = new systemSmartyBase();
			$this->_Engine->setCompileDir($this->getGeneratorName());
			$this->_Engine->setCompileCheck(true);
			$this->_Engine->setCaching(false);
			
			$this->_Engine->assign('oGenerator', $this);
			$this->_Engine->assign('oConfig', system::getConfig());
			$this->_Engine->assign('textUtil', new utilityInflectorWrapper());
			$this->_Engine->assign('appAuthor', system::getConfig()->getParam('app','author'));
			$this->_Engine->assign('appCopyright', system::getConfig()->getParam('app','copyright'));
			$this->_Engine->assign('appVersion', system::getConfig()->getParam('app','version'));
			
			$this->_Engine->assign('package', $this->getPackage());
			$this->_Engine->assign('subPackage', $this->getSubPackage());
			$this->_Engine->assign('category', $this->getCategory());
		}
		return $this->_Engine;
	}
	
	/**
	 * Set $_Engine to $inEngine
	 *
	 * @param systemSmartyBase $inEngine
	 * @return generatorTestCase
	 */
	function setEngine($inEngine) {
		if ( $inEngine !== $this->_Engine ) {
			$this->_Engine = $inEngine;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_DataSources
	 *
	 * @return array
	 */
	function getDataSources() {
		return $this->_DataSources;
	}
	
	/**
	 * Adds a data source with an optional key name - Note the order: VALUE first
	 *
	 * @param mixed $inValue
	 * @param string $inKey (optional)
	 * @return generatorBase
	 */
	function addDataSource($inValue, $inKey = null) {
		if ( $inKey !== null ) {
			$this->_DataSources[$inKey] = $inValue;
		} else {
			$this->_DataSources[] = $inValue;
		}
		return $this;
	}
	
	/**
	 * Returns true if there are data sources to process
	 *
	 * @return boolean
	 */
	function hasDataSources() {
		return count($this->_DataSources) > 0;
	}
	
	/**
	 * Set $_DataSources to $inDataSources
	 *
	 * @param array $inDataSources
	 * @return generatorBase
	 */
	function setDataSources($inDataSources) {
		if ( $inDataSources !== $this->_DataSources ) {
			$this->_DataSources = $inDataSources;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_GeneratedContent
	 *
	 * @return array
	 */
	function getGeneratedContent() {
		return $this->_GeneratedContent;
	}

	/**
	 * Adds generated content to the internal array, note the order: VALUE first
	 *
	 * @param mixed $inValue
	 * @param string $inKey (optional)
	 * @return generatorBase
	 */
	function addGeneratedContent($inValue, $inKey = null) {
		if ( $inKey !== null ) {
			$this->_GeneratedContent[$inKey] = $inValue;
		} else {
			$this->_GeneratedContent[] = $inValue;
		}
		return $this;
	}
	
	/**
	 * Returns true if there is generated content in the class
	 *
	 * @return boolean
	 */
	function hasGeneratedContent() {
		return count($this->_GeneratedContent) > 0;
	}
	
	/**
	 * Set $_GeneratedContent to $inGeneratedContent
	 *
	 * @param array $inGeneratedContent
	 * @return generatorBase
	 */
	function setGeneratedContent($inGeneratedContent) {
		if ( $inGeneratedContent !== $this->_GeneratedContent ) {
			$this->_GeneratedContent = $inGeneratedContent;
			$this->setModified();
		}
		return $this;
	}
	
	
	
	/**
	 * Returns the full path to $inTemplate, resolving the name and path as appropriate
	 *
	 * @param string $inTemplate
	 * @return string
	 */
	function getTemplateFile($inTemplate) {
		$userTemplate = $this->_resolveUserTemplateName($inTemplate);
		$defaultTemplate = $this->_resolveDefaultTemplateName($inTemplate);
		
		systemLog::info("Testing for template file: $userTemplate with fallback $defaultTemplate");
		
		if ( $this->_doesTemplateFileExistInPaths($userTemplate) ) {
			return $this->_getTemplateFile($userTemplate);
		}
		
		/*
		 * Fetch the best match based on the default template 
		 */
		return $this->_findTemplate($defaultTemplate);
	}
	
	/**
	 * Returns the full path to the template, or false if not found in either user or default templates
	 *
	 * @param string $inTemplate
	 * @return string
	 * @access protected
	 */
	protected function _doesTemplateFileExistInPaths($inTemplate) {
		if ( @file_exists($this->getUserTemplateDir().system::getDirSeparator().$inTemplate) ) {
			return true;
		}
		if ( @file_exists($this->getTemplateDir().system::getDirSeparator().$inTemplate) ) {
			return true;
		}
		return false;
	}
	
	/**
	 * Returns the full path to the template, or false if not found in either user or default templates
	 *
	 * @param string $inTemplate
	 * @return string
	 * @access protected
	 */
	protected function _getTemplateFile($inTemplate) {
		if ( @file_exists($this->getUserTemplateDir().system::getDirSeparator().$inTemplate) ) {
			return $this->getUserTemplateDir().system::getDirSeparator().$inTemplate;
		}
		if ( @file_exists($this->getTemplateDir().system::getDirSeparator().$inTemplate) ) {
			return $this->getTemplateDir().system::getDirSeparator().$inTemplate;
		}
		return false;
	}
	
	/**
	 * Resolves the user template name by appending or prepending the name
	 * with specific attributes for the generator e.g. a site name or version
	 *
	 * @param string $inFilename
	 * @return string
	 * @access protected
	 * @abstract 
	 */
	protected function _resolveUserTemplateName($inTemplate) {
		throw new generatorException('Missing implementation for '.__METHOD__);
	}
	
	/**
	 * Resolves the default template name by appending or prepending the name
	 * with specific attributes for the generator e.g. a site name or version
	 *
	 * @param string $inFilename
	 * @return string
	 * @access protected
	 * @abstract 
	 */
	protected function _resolveDefaultTemplateName($inTemplate) {
		throw new generatorException('Missing implementation for '.__METHOD__);
	}

	/**
	 * Performs whatever steps necessary to locate a template for the current generator.
	 * This method requires an implementation e.g. site lookup etc.
	 *
	 * @param string $inTemplate
	 * @return string
	 * @access protected
	 * @abstract 
	 */
	protected function _findTemplate($inTemplate) {
		throw new generatorException('Missing implementation for '.__METHOD__);
	}
	
	
	
	/**
	 * Returns $_TemplateDir
	 *
	 * @return string
	 */
	function getTemplateDir() {
		return $this->getOptionsSet()->getOptions(self::OPTION_TEMPLATE_DIR_DEFAULT);
	}
	
	/**
	 * Set $_TemplateDir to $inTemplateDir
	 *
	 * @param string $inTemplateDir
	 * @return generatorTestCase
	 */
	function setTemplateDir($inTemplateDir) {
		if ( $inTemplateDir !== $this->getTemplateDir() ) {
			$this->getOptionsSet()->setOptions(array(self::OPTION_TEMPLATE_DIR_DEFAULT => $inTemplateDir));
		}
		return $this;
	}

	/**
	 * Returns $_UserTemplateDir
	 *
	 * @return string
	 */
	function getUserTemplateDir() {
		return $this->getOptionsSet()->getOptions(self::OPTION_TEMPLATE_DIR_USER);
	}
	
	/**
	 * Set $_UserTemplateDir to $inTemplateDir
	 *
	 * @param string $inDefaultTemplates
	 * @return generatorBase
	 */
	function setUserTemplateDir($inTemplateDir) {
		if ( $inTemplateDir !== $this->getUserTemplateDir() ) {
			$this->getOptionsSet()->setOptions(array(self::OPTION_TEMPLATE_DIR_USER => $inTemplateDir));
		}
		return $this;
	}

	/**
	 * Returns $_Package
	 *
	 * @return string
	 */
	function getPackage() {
		return $this->getOptionsSet()->getOptions(self::OPTION_PROPERTY_PACKAGE);
	}
	
	/**
	 * Set $_Package to $inPackage
	 *
	 * @param string $inPackage
	 * @return generatorTestCase
	 */
	function setPackage($inPackage) {
		if ( $inPackage !== $this->getPackage() ) {
			$this->getOptionsSet()->setOptions(array(self::OPTION_PROPERTY_PACKAGE => $inPackage));
		}
		return $this;
	}

	/**
	 * Returns $_SubPackage
	 *
	 * @return string
	 */
	function getSubPackage() {
		return $this->getOptionsSet()->getOptions(self::OPTION_PROPERTY_SUB_PACKAGE);
	}
	
	/**
	 * Set $_SubPackage to $inSubPackage
	 *
	 * @param string $inSubPackage
	 * @return generatorTestCase
	 */
	function setSubPackage($inSubPackage) {
		if ( $inSubPackage !== $this->getSubPackage() ) {
			$this->getOptionsSet()->setOptions(array(self::OPTION_PROPERTY_SUB_PACKAGE => $inSubPackage));
		}
		return $this;
	}

	/**
	 * Returns $_Category
	 *
	 * @return string
	 */
	function getCategory() {
		return $this->getOptionsSet()->getOptions(self::OPTION_PROPERTY_CATEGORY);
	}
	
	/**
	 * Set $_Category to $inCategory
	 *
	 * @param string $inCategory
	 * @return generatorTestCase
	 */
	function setCategory($inCategory) {
		if ( $inCategory !== $this->getCategory() ) {
			$this->getOptionsSet()->setOptions(array(self::OPTION_PROPERTY_CATEGORY => $inCategory));
		}
		return $this;
	}
	
	/**
	 * Returns the name of the template to be used during generation
	 *
	 * @return string
	 */
	function getTemplate() {
		return $this->getOptionsSet()->getOptions(self::OPTION_TEMPLATE_USER, $this->getDefaultTemplate());
	}

	/**
	 * Returns the name of the user specified template
	 *
	 * @return string
	 */
	function getUserTemplate() {
		return $this->getOptionsSet()->getOptions(self::OPTION_TEMPLATE_USER);
	}

	/**
	 * Returns the name of the default specified template
	 *
	 * @return string
	 */
	function getDefaultTemplate() {
		return $this->getOptionsSet()->getOptions(self::OPTION_TEMPLATE_DEFAULT);
	}
	
	/**
	 * Set the user specified template name
	 *
	 * @param string $inTemplate
	 * @return generatorBase
	 */
	function setTemplate($inTemplate) {
		if ( $inTemplate !== $this->getUserTemplate() ) {
			$this->getOptionsSet()->setOptions(array(self::OPTION_TEMPLATE_USER => $inTemplate));
		}
		return $this;
	}
	
	
	
	/**
	 * Returns the options set object, creating it if not set
	 *
	 * @return baseOptionsSet
	 */
	function getOptionsSet() {
		if ( !$this->_Options instanceof baseOptionsSet ) {
			$this->_Options = new baseOptionsSet();
		}
		return $this->_Options;
	}
	
	/**
	 * Sets the options object externally
	 *
	 * @param baseOptionsSet $inOptions
	 * @return generatorBase
	 */
	function setOptionsSet(baseOptionsSet $inOptions) {
		$this->_Options = $inOptions;
		$this->setModified();
		return $this;
	}
}