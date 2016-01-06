<?php
/**
 * system.class.php
 * 
 * Scorpio Framework System File
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage system
 * @category systemPackager
 * @version $Rev: 707 $
 */


/**
 * systemPackager
 * 
 * Compiles all specified classes into a single file ready for inclusion.
 * Recommended for production systems to increase performance of the framework.
 * systemPackager will remove most of the comments and white space from the
 * classes and will remove any calls to include / require in the libraries.
 * 
 * Example usage:
 * <code>
 * // define some classes to package
 * $classes = array(
 *     'system', 'systemAutoload', 'systemConfigBase', 'systemConfig',
 * );
 * 
 * // creates bootstrap.php in /tmp
 * systemPackager::package($classes, '/tmp/', 'bootstrap');
 * </code>
 * 
 * This code is borrowed from the Symfony 2 ClassCollectionLoader code.
 * 
 * Originally by and copyright:
 * 
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 * 
 * @package scorpio
 * @subpackage system
 * @category systemPackager
 */
class systemPackager {
	
	/**
	 * Compiles $classes into a single file called $name.php in $cacheDir
	 * 
	 * @param array $classes
	 * @param string $cacheDir
	 * @param string $name
	 * @return void
	 * @static
	 */
	static public function package($classes, $cacheDir, $name) {
		$cache = $cacheDir . '/' . $name . '.php';
		
		$files = array();
		$content = '';
		$search = array(
			'/^\s*<\?php/', // remove opening PHP tags
			'/\?>\s*$/', // and trailing
			'/\ninclude_once.*/', // strip out inline inclues e.g. dependency includes
			'/\nrequire_once.*/', // and requires
			"/\r{1,}/", // remove Mac / Windows line ending
			"/\n{2,}/", // replace multiple line breaks with none
			"/\t\n/", // strip single line tabs
		);
		
		foreach ( $classes as $class ) {
			if ( !class_exists($class) && !interface_exists($class) ) {
				throw new InvalidArgumentException(sprintf('Unable to load class "%s"', $class));
			}
			
			$r = new ReflectionClass($class);
			if ( !in_array($r->getFileName(), $files) ) {
				$files[] = $r->getFileName();
				
				$content .= "\n".preg_replace($search, '', self::stripComments(file_get_contents($r->getFileName())));
			}
		}
		
		// cache the core classes
		if ( !is_dir(dirname($cache)) ) {
			mkdir(dirname($cache), 0777, true);
		}
		
		$header = '<?php
/**
 * Scorpio Bootstrap Package - Generated on '.date(DATE_COOKIE).'
 * 
 * @package scorpio
 * @subpackage bootstrap
 * @author '.system::getConfig()->getParam('app', 'author', 'ScorpioPackager').'
 * @copyright '.system::getConfig()->getParam('app', 'copyright', 'Copyright (c) '.date('Y-m-d')).'
 * @version '.system::getConfig()->getParam('app', 'version', '0.1').'
 *
 * Source classes:
 * '.wordwrap(implode(', ', $classes), 80, "\n * ", false).'
 */
';
		
		self::writeCacheFile($cache, $header.$content);
	}
	
	/**
	 * Creates a cache file
	 * 
	 * @param string $file
	 * @param string $content
	 * @return void
	 * @static 
	 */
	static protected function writeCacheFile($file, $content) {
		$tmpFile = tempnam(dirname($file), basename($file));
		if ( !$fp = @fopen($tmpFile, 'wb') ) {
			die(sprintf('Failed to write cache file "%s".', $tmpFile));
		}
		@fwrite($fp, $content);
		@fclose($fp);
		
		if ( $content != file_get_contents($tmpFile) ) {
			die(sprintf('Failed to write cache file "%s" (cache corrupted).', $tmpFile));
		}
		
		rename($tmpFile, $file);
		chmod($file, 0644);
	}
	
	/**
	 * Strips comments from PHP source code
	 * 
	 * @param string $source
	 * @return string
	 * @static
	 */
 	static public function stripComments($source) {
		if ( !function_exists('token_get_all') ) {
			return $source;
		}
		
		$ignore = array(T_COMMENT => true, T_DOC_COMMENT => true);
		$output = '';
		foreach ( token_get_all($source) as $token ) {
			// array
			if ( isset($token[1]) ) {
				// no action on comments
				if ( !isset($ignore[$token[0]]) ) {
					// anything else -> output "as is"
					$output .= $token[1];
				}
			} else {
				// simple 1-character token
				$output .= $token;
			}
		}
		
		return $output;
	}
	

	/**
	 * Returns an array of critical core files
	 * 
	 * @return array
	 * @static
	 */
	static function getCoreFiles() {
		return array(
			'baseSet', 'baseOptionsSet', 'baseTableParamSet', 'baseObjectSet', 'baseSearch', 'baseSearchInterface', 'baseResultSet',
			'systemDaoInterface', 'systemDaoValidatorInterface',
			'system', 'systemException', 'systemAutoload', 'systemAutoloadException',
			'systemConfigBase', 'systemConfig', 'systemConfigParam', 'systemConfigParamSet', 'systemConfigSection', 'systemConfigException',
			'systemDateTime', 'systemDateTimeZone', 'systemDateEvent', 'systemDateCalendar', 'systemLocale',
			'systemEvent', 'systemEventDispatcher',
			'systemLog', 'systemLogException', 'systemLogFilter', 'systemLogQueue', 'systemLogSource', 'systemLogWriter', 'systemLogWriterFile',
			'systemRegistry', 'systemRegistryException',
			'utilityStringFunction', 'utilityXmlFunction',
			'dbManager', 'dbOptions', 'dbException',
		);
	}
	
	/**
	 * Returns an array of critical core files and the MVC system
	 * 
	 * @return array
	 * @static
	 */
	static function getMvcFiles() {
		return array_merge(self::getCoreFiles(),
			array(
				'mvcSessionBase',
				'mvcAutoload',
				'mvcException',
				'mvcRequest', 'mvcResponse', 'mvcSiteConfig', 'mvcSiteException',
				'mvcDistributorBase', 'mvcDistributor', 'mvcDistributorException', 'mvcDistributorPluginSet', 'mvcDistributorPlugin',
				'mvcDistributorPluginLog', 'mvcDistributorPluginSession', 'mvcDistributorPluginLocale',
				'mvcControllerMapper', 'mvcControllerMap', 'mvcControllerMapPath', 'mvcControllerException',
				'mvcControllerAction', 'mvcControllerActions', 'mvcControllerViews',
				'mvcModelBase', 'mvcModelException',
				'mvcViewBase', 'mvcViewException', 'mvcViewGeneric', 'mvcViewHelperInterface', 'mvcViewResource', 'mvcViewCss', 'mvcViewJavascript',
				'mvcViewEngineBase', 'mvcViewEngineFactory', 'mvcViewEngineGeneric', 'mvcViewEnginePhp', 'mvcViewEngineSmarty',
				'mvcViewHelperFactory', 'mvcViewHelper',
				'utilityOutputWrapper', 'utilityOutputWrapperArray', 'utilityOutputWrapperIterator',
			)
		);
	} 
}