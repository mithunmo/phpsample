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
 * @version $Rev: 835 $
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
	
	/**
	 * Stores $_PrimaryKey
	 *
	 * @var string
	 * @access protected
	 */
	protected $_PrimaryKey;
	
	/**
	 * @see mvcControllerBase::initialise()
	 */
	function initialise() {
		parent::initialise();
		
		/**
		 * @todo add additional initialisation properties
		 */
	}
	
	/**
	 * @see mvcControllerBase::launch()
	 */
	function launch() {
		/**
		 * @todo implement the launch functions
		 */
	}
	
	/**
	 * @see mvcControllerBase::addInputFilters()
	 */
	function addInputFilters() {
		$this->getInputManager()->addFilter('PrimaryKey', utilityInputFilter::filterString());
{foreach $daoObjectVars as $value}
		$this->getInputManager()->addFilter('{$value|replace:"_":""}', utilityInputFilter::filterString());
{/foreach}
	}
	
	/**
	 * @see mvcControllerBase::addInputToModel()
	 */
	function addInputToModel($inData, $inModel) {
		/**
		 * @todo set the primary key here
		 */
		//$inModel->setPrimaryKey($inData['PrimaryKey']);
{foreach $daoObjectVars as $value}
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

	/**
	 * Returns current model objects primary key
	 *
	 * @return string
	 */
	function getPrimaryKey() {
		return $this->_PrimaryKey;
	}
	
	/**
	 * Set the current model objects primary key to $inPrimaryKey
	 *
	 * @param string $inPrimaryKey
	 * @return mvcDaoController
	 */
	function setPrimaryKey($inPrimaryKey) {
		if ( $inPrimaryKey !== $this->_PrimaryKey ) {
			$this->_PrimaryKey = $inPrimaryKey;
			$this->setModified();
		}
		return $this;
	}
}