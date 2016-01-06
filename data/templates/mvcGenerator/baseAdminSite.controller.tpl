{'<?php'}
/**
 * {$controllerClass}
 *
 * Stored in {$controllerClass}.class.php
 * 
 * @author {$appAuthor}
 * @copyright {$appCopyright}
 * @package {$package}
 * @subpackage controllers
 * @category {$controllerClass}
 * @version $Rev: 623 $
 */


/**
 * {$controllerClass}
 *
 * {$controllerClass} class
 * 
 * @package {$package}
 * @subpackage controllers
 * @category {$controllerClass}
 */
class {$controllerClass} extends mvcController {
	
	const ACTION_VIEW = 'view';
	
	/**
	 * @see mvcControllerBase::initialise()
	 */
	function initialise() {
		parent::initialise();
		
		$this->setDefaultAction(self::ACTION_VIEW);
		$this->getControllerActions()->addAction(self::ACTION_VIEW);
	}
	
	/**
	 * @see mvcControllerBase::launch()
	 */
	function launch() {
		$oView = new {$viewClass}($this);
		$oView->show{$functionName}Page();
	}
	
	
	
	/**
	 * @see mvcControllerBase::addInputFilters()
	 */
	function addInputFilters() {
		
	}
	
	/**
	 * @see mvcControllerBase::addInputToModel()
	 */
	function addInputToModel($inData, $inModel) {
		
	}
	
	/**
	 * Fetches the model
	 *
	 * @return {$modelClass}
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
		$oModel = new {$modelClass}();
		$this->setModel($oModel);
	}
}