{'<?php'}
/**
 * {$viewClass}.class.php
 * 
 * {$viewClass} class
 *
 * @author {$appAuthor}
 * @copyright {$appCopyright}
 * @package {$package}
 * @subpackage controllers
 * @category {$viewClass}
 * @version $Rev: 634 $
 */


/**
 * {$viewClass} class
 * 
 * Provides the "{$viewClass}" page
 * 
 * @package {$package}
 * @subpackage controllers
 * @category {$viewClass}
 */
class {$viewClass} extends mvcView {

	/**
	 * @see mvcViewBase::setupInitialVars()
	 */
	function setupInitialVars() {
		parent::setupInitialVars();

		/*
		 * Add any further custom setup for the view that is needed on every request
		 */
	}
	
	/**
	 * Shows the {$viewClass} page
	 *
	 * @return void
	 */
	function show{$functionName}Page() {
		$this->setCacheLevelNone();
		
		$this->render($this->getTpl('{$controllerName}'));
	}
}