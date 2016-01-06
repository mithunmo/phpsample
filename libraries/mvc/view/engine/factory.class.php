<?php
/**
 * mvcViewEngineFactory.class.php
 * 
 * mvcViewEngineFactory class
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage mvc
 * @category mvcViewEngineFactory
 * @version $Rev: 650 $
 */


/**
 * mvcViewEngineFactory class
 * 
 * Returns the template engine for the mvcView layer
 * 
 * @package scorpio
 * @subpackage mvc
 * @category mvcViewEngineFactory
 */
class mvcViewEngineFactory {
	
	/**
	 * Array of supported adaptors
	 *
	 * @var array
	 * @access private
	 * @static
	 */
	private static $_Engines = array(
		'php',
		'phptal',
		'savant3',
		'smarty',
		'twig',
	);
	
	
	
	/**
	 * Returns the appropriate engine based on the supplied type, must be in self::$_Engines
	 *
	 * @param string $inEngine
	 * @return mvcViewEngineBase
	 * @throws mvcViewException
	 */
	static function getEngine($inEngine) {
		if ( $inEngine instanceof systemConfigParam ) {
			$inEngine = $inEngine->getParamValue();
		}
		if ( in_array(strtolower($inEngine), self::$_Engines) ) {
			$engineClass = 'mvcViewEngine'.ucwords(strtolower($inEngine));
			
			return new $engineClass();
		}
		throw new mvcViewException("Requested engine ($inEngine) is not supported");
	}
	
	/**
	 * Returns the array of supported engines
	 *
	 * @return array
	 * @static
	 */
	static function getSupportedEngines() {
		return self::$_Engines;
	}
	
	/**
	 * Add a new engine to the list of supported engines.
	 * The engine class should be named the same as the engine short name and exist
	 * in the view/engine folder. The class name should be mvcViewEngineShortName.
	 *
	 * @param string $inEngine
	 * @return void
	 * @static
	 */
	static function addEngine($inEngine) {
		if ( !in_array($inEngine, self::$_Engines) ) {
			self::$_Engines[] = $inEngine;
		}
	}
	
	/**
	 * Removes engine named $inEngine
	 *
	 * @param string $inEngine
	 * @return void
	 * @static
	 */
	static function removeEngine($inEngine) {
		$key = array_search($inEngine, self::$_Engines);
		if ( $key !== false ) {
			unset(self::$_Engines[$key]);
		}
	}
}