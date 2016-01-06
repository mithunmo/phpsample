<?php
/**
 * mvcViewEngineTwig.class.php
 * 
 * mvcViewEngineTwig class
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage mvc
 * @category mvcViewEngineTwig
 * @version $Rev: 668 $
 */


/**
 * mvcViewEngineTwig class
 * 
 * Interface to Twig for the MVC view system, requires Twig be setup
 * and the appropriate autoload cache file created. This interface
 * has been tested with Twig version 0.9.4 and 0.9.5-DEV.
 *
 * To enable it, set the config option templateEngine to twig in the
 * site section of the websites config.xml file.
 * 
 * @package scorpio
 * @subpackage mvc
 * @category mvcViewEngineTwig
 */
class mvcViewEngineTwig extends mvcViewEngineBase {
	
	/**
	 * Stores variables to be passed to template
	 *
	 * @var array
	 * @access private
	 */
	private $_TplVars;
	
	/**
	 * @see mvcViewEngineBase::initialise()
	 */
	function initialise() {
		/*
		 * Check the Twig version since loads of stuff changed from 0.9.0 on
		 */
		if ( version_compare('0.9.4', Twig_Environment::VERSION, '>=') ) {
			throw new mvcViewException(
				'Twig engine integration requires Twig >= 0.9.4, please upgrade your Twig installation.'
			);
		}

		/*
		 * Set the base options, cache path will be updated later
		 */
		$options = array(
			'base_template_class' => 'Twig_ScorpioTemplate',
			'cache' => system::getConfig()->getPathTemplateTemp()->getParamValue(),
		);
		if ( !system::getConfig()->isProduction() ) {
			$options['debug'] = true;
			$options['auto_reload'] = true;
		}

		/*
		 * Twig requires a loader, we will need to update the folders later
		 */
		$this->_Engine = new Twig_Environment(
			new Twig_Loader_ScorpioFilesystem(
				system::getConfig()->getPathWebsites()->getParamValue()
			),
			$options
		);
	}
	
	/**
	 * Return engine
	 *
	 * @return Twig_Environment
	 */
	function getEngine() {
		return $this->_Engine;
	}
	
	/**
	 * @see mvcViewEngineBase::setCompileCheck()
	 */
	function setCompileCheck($inStatus) {
		$this->getEngine()->setAutoReload($inStatus);
		return $this;
	}
	
	/**
	 * @see mvcViewEngineBase::setCaching()
	 */
	function setCaching($inStatus) {
		return $this;
	}
	
	/**
	 * @see mvcViewEngineBase::setCacheLifetime()
	 */
	function setCacheLifetime($inLifetime) {
		return $this;
	}
	
	/**
	 * @see mvcViewEngineBase::setTemplateDir()
	 */
	function setTemplateDir($inTemplateDir) {
		if ( false ) $oLoader = new Twig_Loader_ScorpioFilesystem();
		$oLoader = $this->getEngine()->getLoader();
		if ( $oLoader instanceof Twig_Loader_ScorpioFilesystem ) {
			$oLoader->addTemplateFolder($inTemplateDir);
		}
		return $this;
	}
	
	/**
	 * @see mvcViewEngineBase::setConfigDir()
	 */
	function setConfigDir($inConfigDir) {
		return $this;
	}
	
	/**
	 * @see mvcViewEngineBase::setCompileDir()
	 */
	function setCompileDir($inCompileDir) {
		return $this;
	}
	
	/**
	 * @see mvcViewEngineBase::setCacheDir()
	 */
	function setCacheDir($inCacheDir) {
		$this->getEngine()
			->setCache(
				system::getConfig()->getPathTemplateCache().system::getDirSeparator().$inCacheDir
			);
		return $this;
	}
	
	/**
	 * @see mvcViewEngineBase::setUseSubDirs()
	 */
	function setUseSubDirs($inStatus) {
		return $this;
	}
	
	/**
	 * Twig does not have assigned template vars, so we have to simulate it.
	 * Any vars assigned to the engine will be passed when the template is
	 * rendered.
	 * 
	 * @see mvcViewEngineBase::assign()
	 */
	function assign($inVarName, $inVar = null) {
		if ( is_array($inVarName) && is_null($inVar) ) {
			foreach ( $inVarName as $var => $value ) {
				$this->_TplVars[$var] = $value;
			}
		} elseif ( is_string($inVarName) ) {
			$this->_TplVars[$inVarName] = $inVar;
		}
		return $this;
	}
	
	/**
	 * @see mvcViewEngineBase::getTemplateVar()
	 */
	function getTemplateVar($inVarName) {
		if ( array_key_exists($inVarName, $this->_TplVars) ) {
			return $this->_TplVars[$inVarName];
		}
		return null;
	}
	
	/**
	 * @see mvcViewEngineBase::isCached()
	 */
	function isCached($inTemplate, $inCacheID = null, $inCompileID = null) {
		return false;
	}
	
	/**
	 * @see mvcViewEngineBase::clearCache()
	 */
	function clearCache() {
		return true;
	}
	
	
	
	/**
	 * @see mvcViewEngineBase::compile()
	 */
	function compile($inTemplate, $inCacheID = null, $inCompileID = null) {
		if ( false ) $oTemplate = new Twig_Template();
		$oTemplate = $this->getEngine()->loadTemplate($this->_stripTemplateResource($inTemplate));
		return $oTemplate->render($this->_TplVars);
	}
	
	/**
	 * @see mvcViewEngineBase::render()
	 */
	function render($inTemplate, $inCacheID = null, $inCompileID = null) {
		if ( false ) $oTemplate = new Twig_Template();
		$oTemplate = $this->getEngine()->loadTemplate($this->_stripTemplateResource($inTemplate));
		$oTemplate->display($this->_TplVars);
	}
	
	/**
	 * Removes the 'file:' or 'res:' and full path prefix that templates have 
	 *
	 * @param string $inTemplate
	 * @return string
	 * @access private
	 */
	private function _stripTemplateResource($inTemplate) {
		if ( strpos($inTemplate, ':') !== false ) {
			 $inTemplate = substr($inTemplate, strpos($inTemplate, ':')+1);
		}
		
		if ( false ) $oLoader = new Twig_Loader_ScorpioFilesystem();
		$oLoader = $this->getEngine()->getLoader();
		$res = $oLoader->getTemplateFolders();
		if ( is_array($res) && count($res) > 0 && $res[0] != './' ) {
			$inTemplate = str_replace($res[0], '', $inTemplate);
		}
		return $inTemplate;
	}
}



/**
 * Twig_Loader_ScorpioFilesystem
 * 
 * Extension of the default twig filesystem loader class to make it more useful.
 *
 * @package scorpio
 * @subpackage mvc
 * @category Twig_Loader_ScorpioFilesystem
 */
class Twig_Loader_ScorpioFilesystem extends Twig_Loader_Filesystem {
	
	/**
	 * @see Twig_Loader_Filesystem::getSource()
	 *
	 * Intercept calls and strip the source prefix from file paths.
	 */
	public function getSource($name) {
		if ( strpos($name, ':') !== false ) {
			 $name = substr($name, strpos($name, ':')+1);
		}
		
		$res = $this->getTemplateFolders();
		if ( is_array($res) && count($res) > 0 && $res[0] != './' ) {
			$name = str_replace($res[0], '', $name);
		}
		return parent::getSource($name);
	}
	
	/**
	 * Returns all defined template folder locations
	 *
	 * @return array
	 */
	function getTemplateFolders() {
		return $this->paths;
	}
	
	/**
	 * Adds a template folder to the list of permitted template locations
	 *
	 * @param string|array $inFolder Folder location or an array of folders
	 * @return Twig_Loader_ScorpioFilesystem
	 */
	function addTemplateFolder($inFolder) {
		if (!is_array($inFolder)) {
			$inFolder = array($inFolder);
		}
		
		foreach ($inFolder as $inFolder) {
			$folder = realpath($folder);
			if ( !in_array($folder, $this->paths) ) {
				$this->paths[] = $folder;
			} 
		}
		return $this;
	}
	
	/**
	 * Removes a template folder location
	 *
	 * @param string $inFolder
	 * @return Twig_Loader_ScorpioFilesystem
	 */
	function removeTemplateFolder($inFolder) {
		$inFolder = realpath($inFolder);
		$key = array_search($inFolder, $this->paths);
		if ( $key !== false ) {
			unset($this->paths[$key]);
		}
		return $this;
	}
}



/**
 * Twig_ScorpioTemplate
 *
 * Twigs default Twig_Template->getAttribute() method does not work with
 * the Scorpio utilityOutputWrapper classes as the output wrapper is doing
 * a similar job to Twig - security checks on permitted method calls.
 * To by-pass the issue, we intercept the wrapped objects and pull the seed
 * object from them and use that instead.
 *
 * @package scorpio
 * @subpackage mvc
 * @category Twig_ScorpioTemplate
 */
abstract class Twig_ScorpioTemplate extends Twig_Template {

	/**
	 * @see Twig_Template::getAttribute()
	 *
	 * @param mixed $object
	 * @param mixed $item
	 * @param array $arguments
	 * @param boolean $arrayOnly
	 * @return mixed
	 */
	protected function getAttribute($object, $item, array $arguments = array(), $arrayOnly = false) {
		if ( 
			$object instanceof utilityOutputWrapper
			|| $object instanceof utilityOutputWrapperArray
			|| $object instanceof utilityOutputWrapperIterator
		) {
			$object = $object->getSeed();
		}
		return parent::getAttribute($object, $item, $arguments, $arrayOnly);
	}
}