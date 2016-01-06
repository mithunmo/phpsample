<?php
/**
 * generatorReport
 * 
 * Stored in generatorReport.class.php
 *
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage generator
 * @category generatorReport
 * @version $Rev: 805 $
 */


/**
 * generatorReport class
 *
 * Builds report shells from the passed data via configurable templates.
 * 
 * The results are held within the generator class.
 * 
 * As with generator, rendering is achieved via Smarty.
 *
 * @package scorpio
 * @subpackage generator
 * @category generatorReport
 */
class generatorReport extends generatorBase {
	
	/**
	 * Stores $_Class
	 *
	 * @var string
	 * @access protected
	 */
	protected $_Class;

	/**
	 * Stores $_IsCollection
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_IsCollection;
	
	
	
	/**
	 * @see generatorBase::reset()
	 */
	function reset() {
		parent::reset();
		
		$this->getOptionsSet()->setOptions(
			array(
				self::OPTION_TEMPLATE_DIR_DEFAULT =>
					utilityStringFunction::cleanDirSlashes(system::getConfig()->getPathLibraries().'/generator/templates/report'),
				self::OPTION_TEMPLATE_DIR_USER => system::getConfig()->getGeneratorUserTemplatePath(),
				self::OPTION_TEMPLATE_DEFAULT => 'report.tpl',
			)
		);
		$this->_Class = null;
		$this->_IsCollection = false;
	}
	
	/**
	 * @see generatorBase::buildDataSource()
	 */
	function buildDataSource() {
		if ( $this->getClass() ) {
			$package = $this->getPackage();
			if ( !$package ) {
				$package = utilityStringFunction::convertCapitalizedString($this->getClass(), '_');
				$package = substr($package, 0, strpos($package, '_'));
			}

			$this->getEngine()->assign('package', $package);
			$this->getEngine()->assign('subpackage', $this->getSubPackage());
			$this->getEngine()->assign('classname', $this->getClass());
			$this->getEngine()->assign('isCollection', $this->getIsCollection());
		} else {
			throw new generatorException('No class has been set in generator');
		}
	}
	
	/**
	 * @see generatorBase::build()
	 */
	function build() {
		$this->addGeneratedContent(
			$this->getEngine()->fetch(
				$this->getTemplateFile($this->getTemplate())
			), 'report'
		);
	}
	
	

	/**
	 * @see generatorBase::_resolveUserTemplate()
	 */
	protected function _resolveUserTemplateName($inTemplate) {
		return $inTemplate;
	}
	
	/**
	 * @see generatorBase::_resolveDefaultTemplate()
	 */
	protected function _resolveDefaultTemplateName($inTemplate) {
		return $inTemplate;
	}

	/**
	 * @see generatorBase::_findTemplate()
	 * 
	 * If this method is called in report generator, template was not found so die
	 */
	protected function _findTemplate($inTemplate) {
		throw new generatorException("Unable to locate $inTemplate in any system path");
	}
	
	
	
	/**
	 * Returns $_Class
	 *
	 * @return string
	 */
	function getClass() {
		return $this->_Class;
	}
	
	/**
	 * Set $_Class to $inClass
	 *
	 * @param string $inClass
	 * @return generatorReport
	 */
	function setClass($inClass) {
		if ( $inClass !== $this->_Class ) {
			$this->_Class = $inClass;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns the value of $_IsCollection
	 *
	 * @return boolean
	 */
	function getIsCollection() {
		return $this->_IsCollection;
	}

	/**
	 * Set $_IsCollection to $inIsCollection
	 *
	 * @param boolean $inIsCollection
	 * @return generatorReport
	 */
	function setIsCollection($inIsCollection) {
		if ( $inIsCollection !== $this->_IsCollection ) {
			$this->_IsCollection = $inIsCollection;
			$this->setModified();
		}
		return $this;
	}
}