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
 * @version $Rev: 623 $
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
	 * @see mvcDaoView::getObjectListView()
	 */
	function getObjectListView() {
		return $this->getTpl('{$controllerName}List');
	}
	
	/**
	 * @see mvcDaoView::getObjectFormView()
	 */
	function getObjectFormView() {
		return $this->getTpl('{$controllerName}Form');
	}
}