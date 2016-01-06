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
 * @version $Rev: 624 $
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
class {$controllerClass} extends mvcDaoController {
	
	/**
	 * @see mvcControllerBase::initialise()
	 */
	function initialise() {
		parent::initialise();
		
		$this->setControllerView('{$controllerName}View');
	}
	
	/**
	 * @see mvcControllerBase::addInputFilters()
	 */
	function addInputFilters() {
		$this->getInputManager()->addFilter('PrimaryKey', utilityInputFilter::filterString());
{foreach item=value from=$daoObjectVars}
		$this->getInputManager()->addFilter('{$value|replace:"_":""}', utilityInputFilter::filterString());
{/foreach}
	}
	
	/**
	 * @see mvcControllerBase::addInputToModel()
	 *
	 * @param array $inData
	 * @param {$modelClass} $inModel
	 */
	function addInputToModel($inData, $inModel) {
		/**
		 * @todo set the primary key here
		 */
		//$inModel->setPrimaryKey($inData['PrimaryKey']);
{foreach item=value from=$daoObjectVars}
		$inModel->set{$value|replace:"_":""}($inData['{$value|replace:"_":""}']);
{/foreach}
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