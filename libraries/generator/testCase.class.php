<?php
/**
 * generatorTestCase
 * 
 * Stored in generatorTestCase.class.php
 *
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage generator
 * @category generatorTestCase
 * @version $Rev: 650 $
 */


/**
 * generatorTestCase class
 *
 * A specific generator class for building test cases from existing classes.
 * This class uses PHP5s reflection API to interrogate the class and to then
 * create a test case for all the found methods.
 * 
 * Abstract, interfaces and protected / private members are ignored.
 * 
 * The class should have full docblock comments to aid the generation of the
 * code as the docblock package / subpackage will be read. If this is missing
 * the class specified package will be used in place.
 * 
 * The results are held within the generator class.
 * 
 * As with generator, rendering is achieved via Smarty.
 *
 * @package scorpio
 * @subpackage generator
 * @category generatorTestCase
 */
class generatorTestCase extends generatorBase {
	
	/**
	 * Stores $_Class
	 *
	 * @var string
	 * @access protected
	 */
	protected $_Class;
	
	
	
	/**
	 * @see generatorBase::reset()
	 */
	function reset() {
		parent::reset();
		
		$this->getOptionsSet()->setOptions(
			array(
				self::OPTION_TEMPLATE_DIR_DEFAULT =>
					utilityStringFunction::cleanDirSlashes(system::getConfig()->getPathLibraries().'/generator/templates/test'),
				self::OPTION_TEMPLATE_DIR_USER => system::getConfig()->getGeneratorUserTemplatePath(),
				self::OPTION_TEMPLATE_DEFAULT => 'scorpioTestCase.tpl',
			)
		);
		$this->_Class = null;
	}
	
	/**
	 * @see generatorBase::buildDataSource()
	 */
	function buildDataSource() {
		if ( $this->getClass() ) {
			$oReflectObj = new ReflectionClass($this->_Class);
			if ( $oReflectObj->isAbstract() || $oReflectObj->isInterface() ) {
				throw new systemException("Class {$this->_Class} is abstract or an interface");
			}
			$this->buildPackage($oReflectObj->getDocComment());
			$methods = $oReflectObj->getMethods(ReflectionMethod::IS_PUBLIC);
			usort($methods, array($this, 'sortMethod'));
			
			$this->getEngine()->assign('methods', $methods);
			$this->getEngine()->assign('package', $this->getPackage());
			$this->getEngine()->assign('subpackage', $this->getSubPackage());
			$this->getEngine()->assign('class', $this->getClass());
			$this->getEngine()->assign('testClassName', $this->getCategory());
			$this->getEngine()->assign('instantiable', $oReflectObj->isInstantiable());
		} else {
			throw new generatorException('No class has been set in generator');
		}
	}

	/**
	 * Sorts methods, where the methods are ReflectionMethod objects
	 *
	 * @param ReflectionMethod $inMethod1
	 * @param ReflectionMethod $inMethod2
	 * @return integer
	 * @static
	 */
	static function sortMethod($inMethod1, $inMethod2) {
		$al = strtolower($inMethod1->getName());
		$bl = strtolower($inMethod2->getName());
		if ($al == $bl) {
			return 0;
		}
		return ($al > $bl) ? +1 : -1;
	}
	
	/**
	 * Builds a package base from the docblock comment
	 *
	 * @param string $inDocBlockComment
	 * @return void
	 */
	function buildPackage($inDocBlockComment = null) {
		if ( (!$this->getPackage() && !$this->getSubPackage()) && $inDocBlockComment !== null && strlen($inDocBlockComment) > 0 ) {
			$matches = array();
			preg_match('/@package ([a-zA-Z0-9\-\_]{1,})/', $inDocBlockComment, $matches);
			if ( isset($matches[1]) ) {
				$this->setPackage($matches[1]);
			} else {
				$this->setPackage('scorpio');
			}
			preg_match('/@subpackage ([a-zA-Z0-9\-\_]{1,})/', $inDocBlockComment, $matches);
			if ( isset($matches[1]) ) {
				$this->setSubPackage($matches[1]);
			} else {
				$pieces = explode(' ', utilityStringFunction::convertCapitalizedString($this->getClass(), ' '));
				$this->setSubPackage($pieces[0]);
			}
		}
		$this->setCategory('test'.ucfirst($this->getClass()));
	}
	
	/**
	 * @see generatorBase::build()
	 */
	function build() {
		$this->addGeneratedContent(
			$this->getEngine()->fetch(
				$this->getTemplateFile($this->getTemplate())
			), 'testcase'
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
	 * If this method is called in testCase generator, then template was not found so die
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
	 * @return generatorTestCase
	 */
	function setClass($inClass) {
		if ( $inClass !== $this->_Class ) {
			$this->_Class = $inClass;
			$this->setModified();
		}
		return $this;
	}
}