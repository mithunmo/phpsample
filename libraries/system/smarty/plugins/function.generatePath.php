<?php
/**
 * Smarty plugin
 * @package scorpio
 * @subpackage mvc
 * @category smarty_plugin
 * @version $Rev: 641 $
 */

/**
 * Smarty {generatePath} plugin
 *
 * Type:     function<br>
 * Name:     generatePath<br>
 * Purpose:  Builds the path to the current controller starting from a specific controller
 * @author Dave Redfern
 * @param array Format:
 * <pre>
 * array('controller' => current controller name without Controller appended,
 *       'parent' => parent controller to build path from
 * </pre>
 * @param Smarty
 * @return string
 */
function smarty_function_generatePath($params, $smarty) {
	$display = '';
	
	$controllerName = $params['controller'];
	if ( $controllerName == null ) {
		throw new mvcViewException("Missing required param 'controller'. Please set controller when calling this function");
	}
	$controllerParent = $params['parent'];
	if ( $controllerParent == null ) {
		throw new mvcViewException("Missing required param 'parent'. Please set parent when calling this function");
	}
	
	/*
	 * work out our target controller
	 */
	$controllerPath = explode('/', $controllerName);
	$lastController = array_pop($controllerPath);
	
	/*
	 * Attempt to get the controller and build out the path
	 */
	try {
		$oRequest = $smarty->getTemplateVars('oRequest');
		if ( $oRequest instanceof utilityOutputWrapper ) {
			$oRequest = $oRequest->getSeed();
		}
		
		systemLog::info("Hunting for: $controllerName from parent: $controllerParent");
		$oMap = $oRequest->getDistributor()->getSiteConfig()->getControllerMapper()->getController($controllerName);
		if ( $oMap ) {
			/*
			 * fetch path components and cycle over until we find the parent
			 * point and then use all paths from there
			 */
			$arrComponents = $oMap->getControllerPath();
			$matched = false;
			if ( count($arrComponents) > 0 ) {
				if ( false ) $oPath = new mvcControllerMap();
				$path = array();
				foreach ( $arrComponents as $oPath ) {
					if ( $oPath->getName() == $controllerParent ) {
						$matched = true;
					}
					
					if ( $matched ) {
						if ( $oPath->getName() != $lastController ) {
							$path[] = $oPath;
						}
					}
				}
				
				if ( count($path) > 0 ) {
					$oView = new mvcViewGeneric($oRequest);
					$oView->getEngine()->assign('oUser', utilityOutputWrapper::wrap($oRequest->getSession()->getUser()));
					$oView->getEngine()->assign('path', utilityOutputWrapper::wrap($path));
					
					$themeName = $oRequest->getDistributor()->getSiteConfig()->getTheme()->getParamValue();
					$oView->getEngine()->assign('themename', $themeName);
					$oView->getEngine()->assign('themefolder', '/themes/'.$themeName);
					$oView->getEngine()->assign('themeimages', '/themes/'.$themeName.'/images');
					$oView->getEngine()->assign('themeicons', '/themes/'.$themeName.'/images/icons');
					$display = utf8_encode($oView->compile($oView->getTpl('controllerPathList', '/shared')));
				}
			}
		}
	} catch ( Exception $e ) {
		systemLog::error("Error building path: ".$e->getMessage());
	}
	return $display;
}