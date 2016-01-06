<?php
/**
 * motdController
 *
 * Stored in motdController.class.php
 * 
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category motdController
 * @version $Rev: 11 $
 */


/**
 * motdController
 *
 * motdController class
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category motdController
 */
class motdController extends mvcDaoController {
	
	const VIEW_MOTD = 'motd';
	
	/**
	 * Renders a standalone view
	 * 
	 * @param array $params
	 * @return string
	 */
	function fetchStandaloneView(array $params = array()) {
		switch ( $params['view'] ) {
			case self::VIEW_MOTD:
				$oView = new motdView($this);
				return $oView->getCurrentMotd();
			break;
		}
	}
	
	/**
	 * @see mvcControllerBase::initialise()
	 */
	function initialise() {
		parent::initialise();
		
		$this->setControllerView('motdView');
		
		$this->getControllerViews()->addView(self::VIEW_MOTD);
	}
	
	/**
	 * @see mvcControllerBase::addInputFilters()
	 */
	function addInputFilters() {
		$this->getInputManager()->addFilter('PrimaryKey', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('Title', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Content', utilityInputFilter::filterUnsafeRaw());
		$this->getInputManager()->addFilter('Active', utilityInputFilter::filterInt());
	}
	
	/**
	 * @see mvcControllerBase::addInputToModel()
	 *
	 * @param array $inData
	 * @param motdModel $inModel
	 */
	function addInputToModel($inData, $inModel) {
		$inModel->setMotdID($inData['PrimaryKey']);
		if ( !$inModel->getUserID() ) {
			$inModel->setUserID($this->getRequest()->getSession()->getUser()->getID());
		}
		$inModel->setLastEditedBy($this->getRequest()->getSession()->getUser()->getID());
		$inModel->setTitle($inData['Title']);
		$inModel->setContent($inData['Content']);
		$inModel->setActive($inData['Active']);
	}
	
	/**
	 * Builds the model
	 *
	 * @return void
	 */
	function buildModel() {
		$oModel = new motdModel();
		$this->setModel($oModel);
	}
}