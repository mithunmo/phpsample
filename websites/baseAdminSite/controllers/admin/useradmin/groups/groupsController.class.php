<?php
/**
 * groupsController
 *
 * Stored in groupsController.class.php
 * 
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category groupsController
 * @version $Rev: 11 $
 */


/**
 * groupsController
 *
 * groupsController class
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category groupsController
 */
class groupsController extends mvcDaoController {
	
	/**
	 * @see mvcControllerBase::initialise()
	 */
	function initialise() {
		parent::initialise();
		
		$this->setControllerView('groupsView');
	}
	
	/**
	 * @see mvcControllerBase::addInputFilters()
	 */
	function addInputFilters() {
		$this->getInputManager()->addFilter('PrimaryKey', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('Description', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Permissions', utilityInputFilter::filterStringArray());
		$this->getInputManager()->addFilter('CopyPermissions', utilityInputFilter::filterInt());
		
		$this->getInputManager()->addFilter('GrantBasePermissions', utilityInputFilter::filterInt());
	}
	
	/**
	 * @see mvcControllerBase::addInputToModel()
	 */
	function addInputToModel($inData, $inModel) {
		$inModel->setID($inData['PrimaryKey']);
		$inModel->setDescription($inData['Description']);
		
		if ( isset($inData['CopyPermissions']) && is_numeric($inData['CopyPermissions']) && $inData['CopyPermissions'] > 0 ) {
			systemLog::message('Preparing to copy permissions from: '.$inData['CopyPermissions']);
			$oGroup = mofilmPermissionGroup::getInstance($inData['CopyPermissions']);
			if ( $oGroup->getID() > 0 ) {
				systemLog::message('Copying '.$oGroup->getPermissions()->getCount().' permissions to new group');
				$inData['Permissions'] = $oGroup->getPermissions()->toArray();
			} else {
				systemLog::message('Group not found');
			}
		}
		
		if ( is_array($inData['Permissions']) ) {
			$inModel->getPermissions()->reset();
			foreach ( $inData['Permissions'] as $permission ) {
				if ( strlen($permission) > 1 ) {
					$inModel->getPermissions()->addPermission($permission);
				}
			}
		}
			
		if ( isset($inData['GrantBasePermissions']) && $inData['GrantBasePermissions'] == 1 ) {
			if ( false ) $oPermission = new mofilmPermission();
			foreach ( mofilmPermission::listOfObjects(null, null, '%Controller%') as $oPermission ) {
				if ( strpos($oPermission->getName(), 'view') !== false || strpos($oPermission->getName(), 'editObject') !== false  ) {
					$inModel->getPermissions()->addPermission($oPermission->getName());
				}
			}
		}
	}
	
	/**
	 * Builds the model
	 *
	 * @return void
	 */
	function buildModel() {
		$oModel = new groupsModel();
		$this->setModel($oModel);
	}
}