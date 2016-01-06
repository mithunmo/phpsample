<?php
/**
 * Smarty plugin
 * @package scorpio
 * @subpackage mvc
 * @category smarty_plugin
 * @version $Rev: 641 $
 */

/**
 * Smarty {includeView} plugin
 *
 * Type:     function<br>
 * Name:     includeView<br>
 * Purpose:  Runs an external controller with a specific view to add additional data to a controllers output
 * @author Dave Redfern
 * @param array Format:
 * <pre>
 * array('controller' => controller name (without Controller suffix) e.g. login,
 *       'path' => path to this controller as it appears on the site e.g. /user/login,
 *       'view' => name of view to display)
 * </pre>
 * @param Smarty
 * @return string
 */
function smarty_function_includeView($params, $smarty) {
	$display = '';
	try {
		$controllerName = $params['controller'];
		if ( $controllerName == null ) {
			throw new mvcViewException("Missing required param 'controller'. Please set controller when calling this function");
		}
		$controllerPath = $params['path'];
		if ( $controllerPath == null ) {
			throw new mvcViewException("Missing required param 'path'. Please specify the path when calling this function");
		}
		$view = $params['view'];
		if ( $view == null ) {
			throw new mvcViewException("Missing required param 'view'. Please specify the view when calling this function");
		}
		
		/**
		 * @var oController mvcControllerBase
		 */
		$controller = $controllerName.'Controller';
		
		/*
		 * Attempt to load the specified controller
		 */
		$oRequest = $smarty->getTemplateVars('oRequest');
		if ( $oRequest instanceof utilityOutputWrapper ) {
			$oRequest = $oRequest->getSeed();
		}
		$oRequest->getDistributor()->includeControllerUnit($controllerName, $controllerPath);
		
		if ( false ) $oController = new mvcControllerBase();
		$oController = new $controller($oRequest);
		
		if ( $oController->isValidView($view) ) {
			$display = utf8_encode($oController->fetchStandaloneView($params));
		} else {
			throw new mvcViewException("Failed to load view ($view). This is not a valid view for controller $controller");
		}
	} catch (Exception $oException) {
		echo $str = "Error loading view <strong>{$params['view']}</strong> from controller <strong>{$controller}</strong>.";
		systemLog::error($str);
		throw $oException;
	}
	return $display;
}