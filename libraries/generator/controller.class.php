<?php
/**
 * generatorController
 * 
 * Stored in generatorController.class.php
 *
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage generator
 * @category generatorController
 * @version $Rev: 810 $
 */


/**
 * generatorController class
 *
 * Site generator is used to build controller units (modules) for a specific site. The
 * generator will build the controller, model, view and a default template view. Additionally
 * systemDaoInterface classnames can be provided and a skeleton interface / controller setup
 * created tailored for that class. 
 * 
 * Again a site is required to be able to use the generator, and similar to the class
 * generator, each site can have its own set of templates.
 * 
 * To define custom templates, they must be located in /data/templates/mvcGenerator and be
 * prefixed with the site domain name. If no file exists, the default site (base) is used instead.
 * base and baseAdminSite are reserved for the main base components.
 * 
 * For DAO objects, the templates must be named site_domain_name.dao.
 * 
 * With all templates configured a new component can be generated. This will build a controller,
 * model and view and if a DAO has been specified will pre-populate most of the properties and
 * view information for the DAO object. Reflection is used to find the object properties.
 * 
 * For other requests the basic templates are used. These basic templates are normally your sites
 * default page layout and includes.
 * 
 * Smarty is used to render the components, so any valid smarty code or plugins can be used to
 * build mvc components.
 *
 * @package scorpio
 * @subpackage generator
 * @category generatorController
 */
class generatorController extends generatorBase {
	
	const FILE_CONTROLLER_TEMPLATE = 'controller.tpl';
	const FILE_MODEL_TEMPLATE = 'model.tpl';
	const FILE_VIEW_TEMPLATE = 'view.tpl';
	
	const FILE_HTML_PAGE_TEMPLATE = 'html.tpl';
	const FILE_HTML_FORM_TEMPLATE = 'htmlForm.tpl';
	const FILE_HTML_LIST_TEMPLATE = 'htmlList.tpl';
	
	const OPTION_DAO_OBJECT = 'dao.object';
	
	/**
	 * Stores $_SiteID
	 * 
	 * @var integer
	 * @access protected
	 */
	protected $_SiteID					= false;
	
	/**
	 * Stores $_Site
	 *
	 * @var mvcSiteTools
	 * @access protected
	 */
	protected $_Site					= null;
	
	
	
	/**
	 * @see generatorBase::reset()
	 */
	function reset() {
		parent::reset();
		
		$this->getOptionsSet()->setOptions(
			array(
				self::OPTION_TEMPLATE_DIR_DEFAULT => 
					utilityStringFunction::cleanDirSlashes(system::getConfig()->getPathLibraries().'/generator/templates/mvc'),
				self::OPTION_TEMPLATE_DIR_USER => system::getConfig()->getMvcGeneratorUserTemplatePath(),
				self::OPTION_TEMPLATE_DEFAULT => 'controller.tpl',
				self::OPTION_DAO_OBJECT => false,
			)
		);
		
		$this->_SiteID = false;
		$this->_Site = null;
	}
	
	/**
	 * @see generatorBase::buildDataSource()
	 */
	function buildDataSource() {
		if ( $this->getDaoObject() ) {
			try {
				$class = $this->getDaoObject();
				$oObject = new $class;
				
				if ( $oObject instanceof systemDaoInterface ) {
					$allVars = $oObject->toArray();
					$objectVars = array();
					foreach ( array_keys($allVars) as $name ) {
						$method = 'get'.str_replace('_', '', $name);
						
						switch ( true ) {
							case $name == '_Modified':
							case $name == '_CreateDate':
							case $name == '_UpdateDate':
							case $name == '_Password':
							case $name == '_MarkForDeletion':
							case preg_match('/_[a-zA-z0-9]{1,}Set/', $name):
							case method_exists($oObject, $method) == false:
							case method_exists($oObject, $method) && (is_array($oObject->$method()) || is_object($oObject->$method())):
								break;
							
							default:
								$objectVars[] = $name;
						}
					}
					$this->getEngine()->assign('daoObjectVars', utilityOutputWrapper::wrap($objectVars));
					
					if ( in_array('_Description', $objectVars) ) {
						$this->getEngine()->assign('daoRecordDisplayMethod', 'getDescription');
					} elseif ( in_array('_Name', $objectVars) ) {
						$this->getEngine()->assign('daoRecordDisplayMethod', 'getName');
					} elseif ( in_array('_Fullname', $objectVars) ) {
						$this->getEngine()->assign('daoRecordDisplayMethod', 'getFullname');
					}  elseif ( in_array('_Title', $objectVars) ) {
						$this->getEngine()->assign('daoRecordDisplayMethod', 'getTitle');
					}
					
				}
			} catch ( Exception $e ) {
				systemLog::error($e->getMessage());
			}
		}
	}
	
	/**
	 * @see generatorBase::build()
	 */
	function build() {
		$this->getEngine()->assign('package', 'websites_'.$this->getSite()->getDomainName());
		$this->getEngine()->assign('daoObjectClass', $this->getDaoObject());
		
		$this
		 ->addGeneratedContent(
			$this->getEngine()->fetch($this->getTemplateFile(self::FILE_CONTROLLER_TEMPLATE)), 'controllerFile'
		)->addGeneratedContent(
			$this->getEngine()->fetch($this->getTemplateFile(self::FILE_MODEL_TEMPLATE)), 'modelFile'
		)->addGeneratedContent(
			$this->getEngine()->fetch($this->getTemplateFile(self::FILE_VIEW_TEMPLATE)), 'viewFile'
		);
		
		if ( $this->getDaoObject() ) {
			$this
			 ->addGeneratedContent(
				$this->getEngine()->fetch($this->getTemplateFile(self::FILE_HTML_FORM_TEMPLATE)), 'templateFileForm'
			)->addGeneratedContent(
				$this->getEngine()->fetch($this->getTemplateFile(self::FILE_HTML_LIST_TEMPLATE)), 'templateFileList'
			);
		} else {
			$this->addGeneratedContent(
				$this->getEngine()->fetch($this->getTemplateFile(self::FILE_HTML_PAGE_TEMPLATE)), 'templateFile'
			);
		}
	}

	/**
	 * Adds the controller data to the generator
	 *
	 * @param string $inControllerName
	 */
	function buildControllerData($inControllerName) {
		$this->getEngine()->assign('controllerName', $inControllerName);
		$this->getEngine()->assign('controllerClass', $inControllerName.'Controller');
		$this->getEngine()->assign('modelClass', $inControllerName.'Model');
		$this->getEngine()->assign('viewClass', $inControllerName.'View');
		$this->getEngine()->assign('functionName', ucwords($inControllerName));
	}
	
	
	
	/**
	 * @see generatorBase::_resolveUserTemplate()
	 */
	protected function _resolveUserTemplateName($inTemplate) {
		if ( $this->getDaoObject() ) {
			$siteFile = $this->getSite()->getDomainName().'.dao.'.$inTemplate;
		} else {
			$siteFile = $this->getSite()->getDomainName().'.'.$inTemplate;
		}
		return $siteFile;
	}
	
	/**
	 * @see generatorBase::_resolveDefaultTemplate()
	 */
	protected function _resolveDefaultTemplateName($inTemplate) {
		if ( $this->getDaoObject() ) {
			$defaultFile = '.dao.'.$inTemplate;
		} else {
			$defaultFile = '.'.$inTemplate;
		}
		return $defaultFile;
	}
	
	/**
	 * @see generatorBase::_findTemplate()
	 */
	protected function _findTemplate($inTemplate) {
		/*
		 * Loop over the site config, finding the parent templates, but allow for templates
		 * to be domain specific first then falling back to the actual defaults.
		 * e.g. site my.example.com inherits from example.com that inherits from base
		 * we check first for my.example.com, then example.com finally base. 
		 */
		$oConfig = $this->getSite()->getSiteConfig();
		$oParentSite = null;
		if ( $oConfig instanceof mvcSiteConfig ) {
			$oParentSite = $this->getSite()->getSiteConfig()->getParentSite();
		}
		while ( $oParentSite !== null ) {
			$templateFile = $oParentSite->getParamValue().$inTemplate;
			
			systemLog::info("Testing for parent template file: $templateFile");
			if ( $this->_doesTemplateFileExistInPaths($templateFile) ) {
				return $this->_getTemplateFile($templateFile);
			}
			
			$oConfig = $oConfig->getParentConfig();
			if ( $oConfig instanceof mvcSiteConfig ) {
				$oParentSite = $oConfig->getParentSite();
			} else {
				$oParentSite = null;
			}
		}
		throw new generatorException("Unable to locate a template match for ($inTemplate) in any template folder or via site hierarchy");
	}
	
	
	
	/**
	 * Return SiteID
	 * 
	 * @return string
	 */
	function getSiteID() {
		return $this->_SiteID;
	}
	
	/**
	 * Set $_SiteID to $inSiteID
	 * 
	 * @param string $inSiteID
	 * @return generatorController
	 */
	function setSiteID($inSiteID) {
		if ( $inSiteID !== $this->_SiteID ) {
			$this->_SiteID = $inSiteID;
			$this->_Modified = true;
		}
		return $this;
	}
	
	/**
	 * Returns the site object
	 *
	 * @return mvcSiteTools
	 */
	function getSite() {
		if ( !$this->_Site instanceof mvcSiteTools ) {
			$this->_Site = mvcSiteTools::getInstance($this->_SiteID);
		}
		return $this->_Site;
	}
	
	/**
	 * Sets the mvcSiteTools site object
	 *
	 * @param mvcSiteTools $inSite
	 * @return generatorController
	 */
	function setSite(mvcSiteTools $inSite) {
		if ( $inSite !== $this->_Site ) {
			$this->_Site = $inSite;
		}
		return $this;
	}
	
	/**
	 * Returns the systemDaoInterface name to use during generation
	 *
	 * @return string
	 */
	function getDaoObject() {
		return $this->getOptionsSet()->getOptions(self::OPTION_DAO_OBJECT);
	}
	
	/**
	 * Set $_DaoObject to $inDaoObject
	 * 
	 * @param string $inDaoObject
	 * @return generatorController
	 */
	function setDaoObject($inDaoObject) {
		$this->getOptionsSet()->setOptions(array(self::OPTION_DAO_OBJECT => $inDaoObject));
		return $this;
	}
}