<?php
/**
 * clientsController
 *
 * Stored in clientsController.class.php
 * 
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category clientsController
 * @version $Rev: 11 $
 */


/**
 * clientsController
 *
 * clientsController class
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category clientsController
 */
class clientsController extends mvcDaoController {
	
	/**
	 * @see mvcControllerBase::initialise()
	 */
	function initialise() {
		parent::initialise();
		
		$this->setControllerView('clientsView');
	}
	
	/**
	 * @see mvcControllerBase::addInputFilters()
	 */
	function addInputFilters() {
		$this->getInputManager()->addFilter('PrimaryKey', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('CompanyName', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('DisableUsers', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('Sources', utilityInputFilter::filterStringArray());
	}
	
	/**
	 * @see mvcControllerBase::addInputToModel()
	 * 
	 * @param array $inData
	 * @param clientsModel $inModel
	 */
	function addInputToModel($inData, $inModel) {
		$inModel->setID($inData['PrimaryKey']);
		$inModel->setCompanyName($inData['CompanyName']);
		
		if ( isset($inData['DisableUsers']) ) {
			$inModel->setDisableAllUsers(true);
		}
		
		if ( $this->hasAuthority('clientsController.canEditSources') ) {
			$inModel->getSourceSet()->reset();
			foreach ( $inData['Sources'] as $sourceID ) {
				$inModel->getSourceSet()->setObject(mofilmSource::getInstance($sourceID));
			}
		}
		
		if ( $this->getAction() == self::ACTION_DO_EDIT || $this->getAction() == self::ACTION_DO_NEW ) {
			$oFileUpload = new mvcFileUpload(
				array(
					mvcFileUpload::OPTION_AUTO_CREATE_FILESTORE => false,
					mvcFileUpload::OPTION_CHECK_PERMISSIONS => false,
					mvcFileUpload::OPTION_FIELD_NAME => 'Logo',
					mvcFileUpload::OPTION_FILE_STORE => mofilmConstants::getBrandLogosFolder(),
					mvcFileUpload::OPTION_SUB_FOLDER_FORMAT => '',
					mvcFileUpload::OPTION_WRITE_IMMEDIATE => false,
					mvcFileUpload::OPTION_STORE_RAW_DATA => true,
				)
			);
			$oFileUpload->initialise();
			try {
				$oFiles = $oFileUpload->process();
				if ( $oFiles->getCount() > 0 ) {
					systemLog::message('Adding logo for '.$inModel->getCompanyName());
					
					$oImageConv = new imageConvertor(
						array(
							imageConvertor::OPTION_OUTPUT_LOCATION => mofilmConstants::getBrandLogosFolder(),
							imageConvertor::OPTION_OUTPUT_FILENAME => $inModel->getLogoName(),
							imageConvertor::OPTION_OUTPUT_OVERWRITE_FILES => true,
							
							imageConvertor::OPTION_OUTPUT_FORMAT => 'jpeg',
							imageConvertor::OPTION_OUTPUT_QUALITY => 90,
							imageConvertor::OPTION_OUTPUT_HEIGHT => 150,
							imageConvertor::OPTION_OUTPUT_WIDTH => 150,
							imageConvertor::OPTION_OUTPUT_PAD_IMAGE => true,
							imageConvertor::OPTION_OUTPUT_PAD_COLOUR => 'white',
							
						)
					);
					$oImageConv->process($oFiles->getFirst()->getRawFileData());
				}
			} catch ( mvcFileUploadException $e ) {
				systemLog::error($e->getMessage());
			}
		}
	}
	
	/**
	 * Builds the model
	 *
	 * @return void
	 */
	function buildModel() {
		$oModel = new clientsModel();
		$this->setModel($oModel);
	}
}