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
 * @version $Rev: 624 $
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
class {$viewClass} extends mvcDaoView {

	/**
	 * @see mvcDaoView::assignCustomViewData()
	 */
	function assignCustomViewData() {
		/**
		 * @todo set these parameters
		 */
		$this->getEngine()->assign('parentController', 'admin');
	}
	
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