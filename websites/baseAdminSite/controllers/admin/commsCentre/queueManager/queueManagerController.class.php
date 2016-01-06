<?php
/**
 * queueManagerController
 *
 * Stored in queueManagerController.class.php
 * 
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category queueManagerController
 * @version $Rev: 11 $
 */


/**
 * queueManagerController
 *
 * queueManagerController class
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category queueManagerController
 */
class queueManagerController extends mvcDaoController {
	
	const ACTION_CLEAR_QUEUE = 'clearQueue';
	const IMAGE_ACTION_CLEAR_QUEUE = 'action-clear-queue';
	
	/**
	 * @see mvcControllerBase::initialise()
	 */
	function initialise() {
		parent::initialise();
		
		$this->setControllerView('queueManagerView');
		
		$this->getControllerActions()->addAction(self::ACTION_CLEAR_QUEUE);
		
		$this->getMenuItems()->reset();
		
		/*
		 * Re-register menu items
		 *
		 * First item is the list view
		 */
		$oItem = new mvcControllerMenuItem(self::ACTION_VIEW, 'View', self::IMAGE_ACTION_VIEW, 'Default view');
		$oItem->addItem(
			new mvcControllerMenuItem(
				$this->buildUriPath(self::ACTION_VIEW), 'Refresh', self::IMAGE_ACTION_VIEW, 'Refresh list', false, mvcControllerMenuItem::PATH_TYPE_URI
			)
		);
		$oItem->addItem(
			new mvcControllerMenuItem(
				$this->buildUriPath(self::ACTION_CLEAR_QUEUE), 'Clear Queue', self::IMAGE_ACTION_CLEAR_QUEUE, 'Clear Queue', false, mvcControllerMenuItem::PATH_TYPE_URI
			)
		);
		$this->getMenuItems()->addItem($oItem);

		/*
		 * Add actions for the delete object view
		 */
		$oItem = new mvcControllerMenuItem(self::ACTION_DELETE, 'Delete', self::IMAGE_ACTION_DELETE, 'Delete the record');
		$oItem->addItem(
			new mvcControllerMenuItem(
				$this->buildUriPath(self::ACTION_DO_DELETE), 'Delete', self::IMAGE_ACTION_DO_DELETE, 'Delete record', true, mvcControllerMenuItem::PATH_TYPE_URI, true
			)
		);
		$oItem->addItem(
			new mvcControllerMenuItem(
				$this->buildUriPath(self::ACTION_VIEW), 'Cancel', self::IMAGE_ACTION_CANCEL, 'Cancel delete record', false, mvcControllerMenuItem::PATH_TYPE_URI
			)
		);
		$this->getMenuItems()->addItem($oItem);
	}
	
	/**
	 * @see mvcDaoController::launch()
	 */
	function launch() {
		if ( $this->getAction() == self::ACTION_CLEAR_QUEUE ) {
			$this->actionClearQueue();
		} else {
			parent::launch();
		}
	}
	
	/**
	 * Handles clearing the current queued items
	 * 
	 * @return void
	 */
	function actionClearQueue() {
		$this->getModel()->clearQueue();
		$this->redirect($this->buildUriPath(self::ACTION_VIEW));
	}
	
	/**
	 * @see mvcControllerBase::addInputFilters()
	 */
	function addInputFilters() {
		$this->getInputManager()->addFilter('PrimaryKey', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Scheduled', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('MessageID', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('TransactionID', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('MarkForDeletion', utilityInputFilter::filterString());
	}
	
	/**
	 * @see mvcControllerBase::addInputToModel()
	 *
	 * @param array $inData
	 * @param queueManagerModel $inModel
	 */
	function addInputToModel($inData, $inModel) {
		$inModel->setScheduled($inData['Scheduled']);
		$inModel->setMessageID($inData['PrimaryKey']);
		$inModel->setTransactionID($inData['TransactionID']);
		$inModel->setMarkForDeletion($inData['MarkForDeletion']);
	}
	
	/**
	 * Builds the model
	 *
	 * @return void
	 */
	function buildModel() {
		$oModel = new queueManagerModel();
		$this->setModel($oModel);
	}
}