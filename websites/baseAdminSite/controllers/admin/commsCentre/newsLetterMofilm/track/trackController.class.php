<?php
/**
 * trackController
 *
 * Stored in trackController.class.php
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category trackController
 * @version $Rev: 624 $
 */


/**
 * trackController
 *
 * trackController class
 *
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category trackController
 */
class trackController extends mvcDaoController {


	const ACTION_NLVIEW = 'nlview';
	const ACTION_NLPLOT = 'nlplot';
	const ACTION_UNSUBS = 'unsubs';

	/**
	 * @see mvcControllerBase::initialise()
	 */
	function initialise() {
		parent::initialise();

		$this->setControllerView('trackView');
		$this->getControllerActions()->addAction(self::ACTION_NLVIEW);
		$this->getControllerActions()->addAction(self::ACTION_NLPLOT);
		//$this->getMenuItems()->reset();
	}

	/**
	 * @see mvcControllerBase::launch()
	 */
	function  launch() {
		$this->getMenuItems()->reset();
		if ( $this->getAction() == self::ACTION_NLVIEW ) {

			if ( $this->getRequest()->isAjaxRequest() ) {
				$this->getInputManager()->setLookupGlobals(utilityInputManager::LOOKUPGLOBALS_GET);
				$data = $this->getInputManager()->doFilter();
				$inNlid = $data["Nlid"];
				$this->addInputToModel($data, $this->getModel());
				$total = $this->getModel()->getTotalObjectsOfNl($inNlid);
				$read = $this->getModel()->getTotalObjectsReadOfNl($inNlid);
				$percentage = ($read / $total) * 100;
				$percentage = floor($percentage);
				$oView = new trackView($this);
				$oView->sendCountView($percentage);
				return;

			}

		}

		if ( $this->getAction() == self::ACTION_NLPLOT ) {

			if ( $this->getRequest()->isAjaxRequest() ) {
				$this->getInputManager()->setLookupGlobals(utilityInputManager::LOOKUPGLOBALS_GET);
				$data = $this->getInputManager()->doFilter();
				$inNlid = $data["Nlid"];
				$this->addInputToModel($data, $this->getModel());
				$list = $this->getModel()->getReadArray($inNlid);
				$tickInterval = $this->getModel()->getTickInterval($inNlid);
				$tickInterval = floor($tickInterval/10);
				if ( $tickInterval <= 0 ) {
					$tickInterval = 1;
				}
				$json_arr = array();
				$json_arr["name"] = $list;
				$json_arr["tickInterval"] = $tickInterval;
				$list = json_encode($json_arr);
				echo $list;
				return;

			}

		}
		parent::launch();

	}

	/**
	 * @see mvcControllerBase::addInputFilters()
	 */
	function addInputFilters() {

		$this->getInputManager()->addFilter('PrimaryKey', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('ID', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('NewsletterID', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('TransactionID', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('UserID', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('Status', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Nlid', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('newslettertri', utilityInputFilter::filterString());

	}


	/**
	 * Handles listing objects and search options
	 *
	 * @return void
	 */
	function actionView() {
		$this->getInputManager()->setLookupGlobals(utilityInputManager::LOOKUPGLOBALS_GET);
		$this->getInputManager()->addFilter('newslettertri', utilityInputFilter::filterInt());
		$data = $this->getInputManager()->doFilter();
		$oUser = $this->getRequest()->getSession()->getUser();
		$this->setSearchOptionFromRequestData($data, 'newslettertri');
		parent::actionView();
	}


	/**
	 * @see mvcControllerBase::addInputToModel()
	 *
	 * @param array $inData
	 * @param trackModel $inModel
	 */
	function addInputToModel($inData, $inModel) {
		$inModel->setID($inData['PrimaryKey']);
		$inModel->setNewsletterID($inData['newslettertri']);
		$inModel->setTransactionID($inData['TransactionID']);
		$inModel->setUserID($inData['UserID']);
		$inModel->setStatus($inData['Status']);
	}

	/**
	 * Builds the model
	 *
	 * @return void
	 */
	function buildModel() {
		$oModel = new trackModel();
		$this->setModel($oModel);
	}
}