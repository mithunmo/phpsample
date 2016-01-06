<?php
/**
 * mvcSiteBuilder
 *
 * Stored in mvcSiteBuilder.class.php
 *
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage mvc
 * @category mvcSiteBuilder
 * @version $Rev: 791 $
 */


/**
 * mvcSiteBuilder Class
 *
 * A utility framework for building sites and setting up the default
 * configuration and files required for a site running under the
 * Scorpio MVC system.
 * 
 * This will automatically create all basic libraries and config files
 * needed by a site to run under the Scorpio MVC. This includes views for
 * home, static, redirect and all error templates, classes for mvcController,
 * mvcSession, mvcView and a default homeController configuration.
 *
 * @package scorpio
 * @subpackage mvc
 * @category mvcSiteBuilder
 */
class mvcSiteBuilder {

	/**
	 * Stores $_Modified
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_Modified = false;

	/**
	 * Stores $_Type
	 *
	 * @var string
	 * @access protected
	 */
	protected $_Type;

	/**
	 * Stores $_DomainName
	 *
	 * @var string
	 * @access protected
	 */
	protected $_DomainName;

	/**
	 * Stores parent site, used during site construction
	 *
	 * @var string
	 * @access protected
	 */
	protected $_ParentSite;
	
	/**
	 * Stores $_BuildDefaultFiles
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_BuildDefaultFiles;



	/**
	 * Creates a new site builder instance
	 *
	 * @param string $inType
	 * @param string $inDomainName
	 * @param string $inParentSite
	 * @param boolean $inBuildFiles
	 */
	function __construct($inType = null, $inDomainName = null, $inParentSite = null, $inBuildFiles = true) {
		$this->reset();
		$this->setType($inType);
		$this->setDomainName($inDomainName);
		$this->setParentSite($inParentSite);
		$this->setBuildDefaultFiles($inBuildFiles);
	}

	/**
	 * Builds the basic site structure
	 *
	 * @return boolean
	 */
	function build() {
		if ( !file_exists($this->getSitePath()) ) {
			if ( !is_writable(dirname($this->getSitePath())) ) {
				throw new mvcSiteToolsException('Path to websites ('.dirname($this->getSitePath()).') is not writable by the current user');
			}

			systemLog::message('Creating default site structure for '.$this->getDomainName());
			$oldmask = umask(0);
			mkdir($this->getSitePath(), 0755, true);
			if ( $this->getBuildDefaultFiles() ) {
				$folders = $this->defaultFoldersForFileBuild();
			} else {
				$folders = $this->defaultFolders();
			}
			foreach ( $folders as $folder ) {
				mkdir($this->getSitePath().$folder, 0755, true);
			}
			umask($oldmask);

			/*
			 * Create config files
			 */
			systemLog::message('Creating default config files');
			file_put_contents($this->getSitePath().'config.xml', $this->defaultConfigFile($this->getParentSite()));
			file_put_contents($this->getSitePath().'controllerMap.xml', $this->defaultControllerMap($this->getType()));
			
			/*
			 * Create libraries
			 */
			if ( $this->getBuildDefaultFiles() ) {
				systemLog::message('Creating default library files');
				file_put_contents($this->getSitePath().'/libraries/controller.class.php', $this->defaultMvcController());
				file_put_contents($this->getSitePath().'/libraries/session.class.php', $this->defaultMvcSession());
				file_put_contents($this->getSitePath().'/libraries/view.class.php', $this->defaultMvcView());
				
				/*
				 * Create home controller
				 */
				systemLog::message('Creating default home controller');
				file_put_contents($this->getSitePath().'/controllers/home/homeController.class.php', $this->defaultHomeController());
				file_put_contents($this->getSitePath().'/controllers/home/homeModel.class.php', $this->defaultHomeModel());
				file_put_contents($this->getSitePath().'/controllers/home/homeView.class.php', $this->defaultHomeView());
				
				/*
				 * Create default views
				 */
				systemLog::message('Creating default views');
				file_put_contents($this->getSitePath().'/views/redirect.html', $this->defaultRedirect());
				file_put_contents($this->getSitePath().'/views/home/home.html.tpl', $this->defaultHomeTemplate());
				file_put_contents($this->getSitePath().'/views/static/static.html.tpl', $this->defaultStaticTemplate());
				
				file_put_contents($this->getSitePath().'/views/error/404.html.tpl', $this->default404Template());
				file_put_contents($this->getSitePath().'/views/error/500.html.tpl', $this->default500Template());
				file_put_contents($this->getSitePath().'/views/error/503.html.tpl', $this->default503Template());
				file_put_contents($this->getSitePath().'/views/error/debug.html.tpl', $this->defaultDebugTemplate());
				file_put_contents($this->getSitePath().'/views/error/invalidAction.html.tpl', $this->defaultInvalidActionTemplate());
				file_put_contents($this->getSitePath().'/views/error/invalidRequest.html.tpl', $this->defaultInvalidRequestTemplate());
				file_put_contents($this->getSitePath().'/views/error/offline.html.tpl', $this->defaultOfflineTemplate());
			}
			
			systemLog::message('Site created');
			return true;
		} else {
			systemLog::error('Site ('.$this->getDomainName().') already exists');
			return false;
		}
	}

	/**
	 * Resets object properties to defaults
	 *
	 * @return mvcSiteTools
	 */
	function reset() {
		$this->_Type = null;
		$this->_DomainName = null;
		$this->_ParentSite = null;
		$this->_BuildDefaultFiles = true;
		$this->setModified(false);
		return $this;
	}

	/**
	 * Returns properties of object as an array
	 *
	 * @return array
	 */
	function toArray() {
		return get_object_vars($this);
	}



	/**
	 * Returns true if object has been modified
	 *
	 * @return boolean
	 */
	function isModified() {
		$modified = $this->_Modified;
		return $modified;
	}

	/**
	 * Set the status of the object if it has been changed
	 *
	 * @param boolean $status
	 * @return mvcSiteTools
	 */
	function setModified($status = true) {
		$this->_Modified = $status;
		return $this;
	}

	/**
	 * Returns the current site type, either an admin or normal site.
	 *
	 * @return string
	 * @access public
	 */
	function getType() {
		return $this->_Type;
	}

	/**
	 * Set $_Type to $inType
	 *
	 * @param string $inType
	 * @return mvcSiteTools
	 * @access public
	 */
	function setType($inType) {
		if ( $inType !== $this->_Type ) {
			$this->_Type = $inType;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns the current site domain, or rather the folder name
	 *
	 * @return string
	 * @access public
	 */
	function getDomainName() {
		return $this->_DomainName;
	}

	/**
	 * Set $_DomainName to DomainName
	 *
	 * @param string $inDomainName
	 * @return mvcSiteTools
	 * @access public
	 */
	function setDomainName($inDomainName) {
		if ( $inDomainName !== $this->_DomainName ) {
			$this->_DomainName = $inDomainName;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_ParentSite
	 *
	 * @return string
	 * @access public
	 */
	function getParentSite() {
		return $this->_ParentSite;
	}

	/**
	 * Set $_ParentSite to $inParentSite
	 *
	 * @param string $inParentSite
	 * @return mvcSiteTools
	 * @access public
	 */
	function setParentSite($inParentSite) {
		if ( $this->_ParentSite !== $inParentSite ) {
			$this->_ParentSite = $inParentSite;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_BuildDefaultFiles
	 *
	 * @return boolean
	 */
	function getBuildDefaultFiles() {
		return $this->_BuildDefaultFiles;
	}
	
	/**
	 * Set $_BuildDefaultFiles to $inBuildDefaultFiles
	 *
	 * @param boolean $inBuildDefaultFiles
	 * @return mvcSiteBuilder
	 */
	function setBuildDefaultFiles($inBuildDefaultFiles) {
		if ( $inBuildDefaultFiles !== $this->_BuildDefaultFiles ) {
			$this->_BuildDefaultFiles = $inBuildDefaultFiles;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns the full path to the current site folder in websites
	 *
	 * @return string
	 */
	function getSitePath() {
		return system::getConfig()->getPathWebsites().system::getDirSeparator().$this->getDomainName().system::getDirSeparator();
	}

	/**
	 * Replaces certain marked up place holders
	 *
	 * @param string $inString
	 * @return string
	 */
	function replaceVars($inString) {
		$search = array(
			'%package%',
			'%subpackage%',
			'%author%',
			'%copyright%',
		);
		$replace = array(
			$this->getDomainName(),
			'websites_'.$this->getDomainName().'_libraries',
			system::getConfig()->getParam('app', 'author')->getParamValue(),
			system::getConfig()->getParam('app', 'copyright')->getParamValue(),
		);
		return str_replace($search, $replace, $inString);
	}



	/**
	 * Returns an array of folders making up a default site
	 *
	 * @return array
	 */
	function defaultFolders() {
		return array(
			'controllers',
			'libraries',
			'libraries/smarty',
			'libraries/lang',
			'views',
			'views/error',
			'views/home',
		);
	}

	/**
	 * Returns an array of folders making up a default site when building with files
	 *
	 * @return array
	 */
	function defaultFoldersForFileBuild() {
		return array(
			'controllers',
			'controllers/home',
			'libraries',
			'libraries/images',
			'libraries/smarty',
			'libraries/lang',
			'views',
			'views/error',
			'views/home',
			'views/static',
		);
	}

	/**
	 * Creates a site specific controller for all site controllers
	 *
	 * @return string
	 */
	function defaultMvcController() {
		$controller = "<?php
/**
 * mvcController.class.php
 *
 * mvcController class
 *
 * @author %author%
 * @copyright %copyright%
 * @package %package%
 * @subpackage %subpackage%
 * @category mvcController
 */


/**
 * mvcController
 *
 * Main site mvcController implementation, holds base directives and defaults for the site
 *
 * @package %package%
 * @subpackage %subpackage%
 * @category mvcController
 */
abstract class mvcController extends mvcControllerBase {

	/**
	 * @see mvcControllerBase::authorise()
	 */
	function authorise() {
		/**
		 * @todo: redirect to the authorisation page
		 */
		\$this->redirect('/user/login');
	}

	/**
	 * @see mvcControllerBase::initialise()
	 */
	function initialise() {
		\$this->setRequiresAuthentication(false);

		/**
		 * @todo: Add valid actions for this controller
		 */
		\$this->getControllerActions()->addAction();

		/**
		 * @todo: Add valid views for this controller
		 */
		\$this->getControllerViews()->addView();
	}

	/**
	 * @see mvcControllerBase::isValidAction()
	 */
	function isValidAction() {
		/**
		 * @todo: Add action validation
		 */
		return false;
	}

	/**
	 * @see mvcControllerBase::isValidView()
	 */
	function isValidView(\$inView) {
		/**
		 * @todo: Add view validation
		 */
		return false;
	}

	/**
	 * @see mvcControllerBase::isAuthorised()
	 */
	function isAuthorised() {
		/**
		 * @todo: Implement authorisation layer
		 */
		return false;
	}

	/**
	 * @see mvcControllerBase::hasAuthority()
	 */
	function hasAuthority(\$inActivity) {
		/**
		 * @todo: Implement authorisation layer
		 */
		return false;
	}
}";
		return $this->replaceVars($controller);
	}

	/**
	 * Create a site specific session class
	 *
	 * @return string
	 */
	function defaultMvcSession() {
		$session = "<?php
/**
 * mvcSession.class.php
 *
 * mvcSession class
 *
 * @author %author%
 * @copyright %copyright%
 * @package %package%
 * @subpackage %subpackage%
 * @category mvcSession
 */


/**
 * mvcSession
 *
 * Main site mvcSession implementation, holds base directives and defaults for the site
 *
 * @package %package%
 * @subpackage %subpackage%
 * @category mvcSession
 */
class mvcSession extends mvcSessionBase {

}";

		return $this->replaceVars($session);
	}

	/**
	 * Creates a site specific view class
	 *
	 * @return string
	 */
	function defaultMvcView() {
		$view = "<?php
/**
 * mvcView.class.php
 *
 * mvcView class
 *
 * @author %author%
 * @copyright %copyright%
 * @package %package%
 * @subpackage %subpackage%
 * @category mvcView
 */


/**
 * mvcView
 *
 * Main site mvcView implementation, holds base directives and defaults for the site
 *
 * @package %package%
 * @subpackage %subpackage%
 * @category mvcView
 */
class mvcView extends mvcViewBase {

	/**
	 * @see mvcViewBase::setupInitialVars()
	 *
	 * @return void
	 */
	function setupInitialVars() {
		parent::setupInitialVars();

		/*
		 * Add any further custom setup for the view that is needed on every request
		 */
	}
}";
		return $this->replaceVars($view);
	}

	/**
	 * Creates a default redirect template
	 *
	 * @return string
	 */
	function defaultRedirect() {
		$view = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta http-equiv="refresh" content="10;url=%redirect.location%" />
		<title>%package% - Redirecting</title>
	</head>

	<body id="redirect">
		<h1>%package% - Redirect</h1>
		<p>Your request has been processed.</p>
		<p>If you are not automatically forwarded in the next 10 seconds, please follow <a href="%redirect.location%">this link</a>.</p>
	</body>
</html>';

		return $this->replaceVars($view);
	}

	/**
	 * Creates a default home page template
	 *
	 * @return string
	 */
	function defaultHomeTemplate() {
		$view = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>%package% - {$oMap->getDescription()}</title>
		<link rel="home" title="Home" href="{$oMap->getUriPath()}" />
	</head>

	<body id="home">
		<h1>%package%</h1>
		<p>This is %package%\'s home page.</p>
		<p>You now need to customise the views and build your site.</p>
	</body>
</html>';

		return $this->replaceVars($view);
	}

	/**
	 * Creates a default static page template
	 *
	 * @return string
	 */
	function defaultStaticTemplate() {
		$view = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>%package% - {$oMap->getDescription()}</title>
		<link rel="home" title="Home" href="{$oMap->getUriPath()}" />
	</head>

	<body id="home">
		<h1>%package% - Static Page View</h1>
		<div class="content">
			<div class="title">Static Pages</div>
			<div class="body">
				<p>
					Static pages are pages whose content rarely changes. If you are seeing this page, you
					either requested a page that does not exist, or did not request a page.
				</p>

				{assign var=pages value=$oModel->getStaticPages()}
				{if $pages->getArrayCount() > 0}
					<p>The following pages have been configured on this site:</p>
					<p>{foreach key=uriLink item=title from=$pages}
					<a href="/static/{$uriLink}" title="{$title}">{$title}</a><br />
					{/foreach}</p>
				{/if}
			</div>
		</div>
	</body>
</html>';

		return $this->replaceVars($view);
	}
	
	/**
	 * Creates a default error 404 template
	 * 
	 * @return string
	 */
	function default404Template() {
		return $this->replaceVars(
			$this->emptyHtmlTemplate(
				'- 404 - Request Not Found',
				'
				<h1>Request Not Found</h1>
				<p>The requested resource could not be located.</p>
				<p>If you continue to see this message, contact the site maintainer.</p>'
			)
		);
	}

	/**
	 * Creates a default error 500 template
	 * 
	 * @return string
	 */
	function default500Template() {
		return $this->replaceVars(
			$this->emptyHtmlTemplate(
				'- 500 - Internal Server Error',
				'
				<h1>Internal Server Error</h1>
				<p>An unrecoverable internal error was encountered. This has been logged.</p>
				{include file=$oView->getTemplateFile(\'debug\', \'/error\')}'
			)
		);
	}
	
	/**
	 * Creates a default error 503 template
	 * 
	 * @return string
	 */
	function default503Template() {
		return $this->replaceVars(
			$this->emptyHtmlTemplate(
				'- 503 - Server Not Available',
				'
				<h1>Server Not Available</h1>
				<p>The server is temporarily not available.</p>'
			)
		);
	}
	
	/**
	 * Creates a default invalid action template
	 * 
	 * @return string
	 */
	function defaultInvalidActionTemplate() {
		return $this->replaceVars(
			$this->emptyHtmlTemplate(
				'- Oops - Invalid Action',
				'<h1>%package% - Oops - Invalid Action</h1>
				<p>The action you requested is not permitted for this request.</p>
				<p>Please try again using the links and forms on the site.</p>
				<p>If you continue to have issues, contact the site maintainer.</p>'
			)
		);
	}
	
	/**
	 * Creates a default invalid request template
	 * 
	 * @return string
	 */
	function defaultInvalidRequestTemplate() {
		return $this->replaceVars(
			$this->emptyHtmlTemplate(
				'- Oops - Invalid Request',
				'<h1>%package% - Oops - Invalid Request</h1>
				<p>Sorry, but the resource you requested does not exist or is not configured.</p>
				<p>This has been logged.</p>
				<p><a href="/" title="Home">Home</a></p>'
			)
		);
	}
	
	/**
	 * Creates a default offline template
	 * 
	 * @return string
	 */
	function defaultOfflineTemplate() {
		return $this->replaceVars(
			$this->emptyHtmlTemplate(
				'- Offline',
				'<h1>%package% - Offline</h1>
				<p>The site is currently offline for routine maintenance.</p>
				<p>If you continue to see this message, the maintainer may have turned off the site.</p>'
			)
		);
	}
	
	/**
	 * Returns a basic HTML code segment with $inTitle and $inBody set inside it
	 * 
	 * @param string $inTitle
	 * @param string $inBody
	 * @return string
	 */
	function emptyHtmlTemplate($inTitle, $inBody) {
		return '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>%package% '.$inTitle.'</title>
		<style type="text/css">
		{literal}
			* { font-family: Verdana, sans-serif; font-size: 12px; }
			h1 { font-size: 18px; }
			h2 { font-size: 16px; }
			h3 { font-size: 14px; color: #f00; font-weight: bold; }
			table thead th { border-bottom: 2px solid #000; text-align: left; }
			table tbody td { border-bottom: 1px solid #666; text-align: left; }
			.debug { border: 2px dashed #f00; background-color: #fdd; padding: 5px; }
		{/literal}
		</style>
	</head>

	<body>
		'.$inBody.'
	</body>
</html>';
	}
	
	/**
	 * Returns a default debugging template for the 500 error template
	 * 
	 * @return string
	 */
	function defaultDebugTemplate() {
		return '{if !$isProduction}
	<div class="debug">
		<h2>Debug Data</h2>
		
		<h3>Base Path</h3>
		<p>{$basePath}</p>
		
		<h3>Error Details</h3>
		<p>Exception occured at line {$oException->getLine()} in file {$oException->getFile()|replace:$basePath:\'\'}</p>
		<p>The specific message was:<br /><em>{$oException->getMessage()|default:\'No error message in exception\'}</em></p>
		
		<h4>Partial source code snippet from line {$oException->getLine()-5} to {$oException->getLine()+5}</h4>
		{highlightSource file=$oException->getFile() start=$oException->getLine()-5 end=$oException->getLine()+5}
		
		<h3>Exception stack trace:</h3>
		<table>
			<thead>
				<tr>
					<th>Line</th>
					<th>Class</th>
					<th>Type</th>
					<th>Function</th>
					<th>File</th>
				</tr>
			</thead>
			<tbody>
			{foreach $oException->getTrace() as $data}
				<tr>
					<td>{$data->getArrayValue(\'line\')}</td>
					<td>{$data->getArrayValue(\'class\')}</td>
					<td>{$data->getArrayValue(\'type\')}</td>
					<td>{$data->getArrayValue(\'function\')}</td>
					<td>
						{if $data->getArrayValue(\'file\')}
							{assign var=it value=$data@iteration}
							<span id="fileSourceToggle{$it}" class="fileSourceToggle" onclick="toggleSource(\'fileSourceToggle{$it}\',\'fileSource{$it}\');" title="Toggle Partial Source">
								{$data->getArrayValue(\'file\')|replace:$basePath:\'\'} [+]
							</span>
							<div id="fileSource{$it}" class="fileSource" style="display: none;">
								<h4>Partial source dode snippet from line {$data->getArrayValue(\'line\')-5} to {$data->getArrayValue(\'line\')+5}</h4>
								{highlightSource file=$data->getArrayValue(\'file\') start=$data->getArrayValue(\'line\')-5 end=$data->getArrayValue(\'line\')+5}
							</div>
						{/if}
					</td>
				</tr>
			{/foreach}
			</tbody>
		</table>
	</div>
	<script type="text/javascript">
		function toggleSource(toggle, source) {
			oTog = document.getElementById(toggle);
			oSource = document.getElementById(source);
	
			if ( oSource.style.display == \'none\' ) {
				oSource.style.display = \'block\';
				oTog.innerHTML = oTog.innerHTML.replace(\'+\', \'-\');
			} else {
				oSource.style.display = \'none\';
				oTog.innerHTML = oTog.innerHTML.replace(\'-\', \'+\');
			}
		}
	</script>
{/if}';
	}

	/**
	 * Creates a basic config.xml file for a new site, inheriting from $inParentSite
	 *
	 * @param string $inParentSite Name of an existing site to inherit from
	 * @return string
	 */
	function defaultConfigFile() {
		if ( !$this->getParentSite() ) {
			$this->setParentSite('base');
		}
		$config = '<!DOCTYPE config SYSTEM "../../data/dtds/config.dtd">
<config>
	<section name="site" override="1">
		<option name="parent" value="%ParentSite%" override="1" />
		<option name="active" value="1" override="1" />
		<!-- <option name="theme" value="valid name of theme in base/themes folder" override="1" /> -->
		<option name="uriTextSeparator" value="-" override="1" />
		<option name="cacheStaticPages" value="1" override="1" />
		<option name="useCaptchaOnLoginForms" value="1" override="1" />
		<!-- Controls the individual sites log level, (integer) -->
		<option name="logLevel" value="value from systemLogLevel" override="1" />

		<!-- These options should not need to be changed, but can be -->
		<!--
		<option name="templateEngine" value="smarty" override="1" />
		<option name="controllerMapFilename" value="controllerMap.xml" override="1" />
		<option name="controllerMap" value="/alt/path/to/controllerMap/file.xml" override="1" />
		<option name="defaultController" value="home" override="1" />
		-->
	</section>
	<!-- If useCache is set to false (0), preloadSiteClasses MUST BE enabled -->
	<section name="autoload" override="1">
		<option name="useCache" value="1" override="1" />
		<option name="autoSave" value="1" override="1" />
		<option name="preloadSiteClasses" value="1" override="1" />
	</section>
	<!-- The following are example config options -->
	<!--
	// these are custom classes for the site, usually not changed
	// each site "normally" has its own mvcController, Session etc.
	// use the value to set the filename of the class in the sites /libraries folder
	<section name="classes" override="1">
		<option name="mvcController" value="controller.class.php" override="1" />
		<option name="mvcSession" value="session.class.php" override="1" />
		<option name="mvcView" value="view.class.php" override="1" />
	</section>
	// add additional distributor plugins that will be auto-added to the distributor
	<section name="distributorPlugins" override="1">
		<option name="mvcDistributorPluginLog" value="true" override="1" />
		<option name="mvcDistributorPluginSession" value="true" override="1" />
	</section>
	// internationalisation options, configure the adaptor and defaults per site
	<section name="i18n" override="1">
		<option name="active" value="false" override="1" />
		<option name="identifier" value="t" override="1" />
		<option name="defaultLanguage" value="en" override="1" />
		<option name="adaptor" value="qt" override="1" />
		<option name="adaptorOptions" value="disableNotices=true|scan=directory" override="1" />
	</section>
	-->
</config>';
		return str_replace('%ParentSite%', $this->getParentSite(), $config);
	}

	/**
	 * Returns a basic site controllerMap containing, home, static and captcha
	 *
	 * @return string
	 */
	function defaultControllerMap() {
		return '<!DOCTYPE config SYSTEM "../../data/dtds/controllerMap.dtd">
<controllers>
	<controller name="home" />
	<controller name="static" description="Static Pages" path="mvcStaticController.class.php" />
	<controller name="captcha" description="Captcha Generator" path="mvcCaptchaController.class.php" />
</controllers>';
	}
	
	/**
	 * Creates a default home controller
	 * 
	 * @return string
	 */
	function defaultHomeController() {
		return $this->replaceVars('<?php
/**
 * homeController.class.php
 * 
 * homeController class
 *
 * @author %author%
 * @copyright %copyright%
 * @package %package%
 * @subpackage %subpackage%
 * @category homeController
 */


/**
 * homeController class
 * 
 * Provides the "home" page defaults
 * 
 * @package %package%
 * @subpackage %subpackage%
 * @category homeController
 */
class homeController extends mvcController {
	
	const ACTION_HOME = \'home\';
	
	/**
	 * @see mvcControllerBase::initialise()
	 */
	function initialise() {
		parent::initialise();
		$this->setDefaultAction(self::ACTION_HOME);
	}
	
	/**
	 * @see mvcControllerBase::launch()
	 */
	function launch() {
		$oView = new homeView($this);
		$oView->showHomePage();
	}
	
	/**
	 * Fetches the model
	 *
	 * @return homeModel
	 */
	function getModel() {
		if ( !parent::getModel() ) {
			$this->buildModel();
		}
		return parent::getModel();
	}

	/**
	 * Builds the model
	 *
	 * @return void
	 */
	function buildModel() {
		$oModel = new homeModel();
		$this->setModel($oModel);
	}
}');
	}
	
	/**
	 * Creates a default home model class
	 * 
	 * @return string
	 */
	function defaultHomeModel() {
		return $this->replaceVars('<?php
/**
 * homeModel.class.php
 * 
 * homeModel class
 *
 * @author %author%
 * @copyright %copyright%
 * @package %package%
 * @subpackage %subpackage%
 * @category homeModel
 */


/**
 * homeModel class
 * 
 * Provides the "home" page defaults
 * 
 * @package %package%
 * @subpackage %subpackage%
 * @category homeModel
 */
class homeModel extends mvcModelBase {
	
}');
	}
	
	/**
	 * Creates a default home controller view class
	 * 
	 * @return string
	 */
	function defaultHomeView() {
		return $this->replaceVars('<?php
/**
 * homeView.class.php
 * 
 * homeView class
 *
 * @author %author%
 * @copyright %copyright%
 * @package %package%
 * @subpackage %subpackage%
 * @category homeView
 */


/**
 * homeView class
 * 
 * Provides the "home" page defaults
 * 
 * @package %package%
 * @subpackage %subpackage%
 * @category homeView
 */
class homeView extends mvcView {
	
	/**
	 * Shows the home page
	 *
	 * @return void
	 */
	function showHomePage() {
		if ( system::getConfig()->isProduction() ) {
			$this->setCacheLevelLow();
		} else {
			$this->setCacheLevelNone();
		}
		
		$cacheId = \'home\';
		if ( !$this->isCached($this->getTpl(\'home\'), $cacheId) ) {
			$this->getEngine()->assign(\'oModel\', utilityOutputWrapper::wrap($this->getModel()));
		}
		$this->render($this->getTpl(\'home\'), $cacheId);
	}
}');
	}
}