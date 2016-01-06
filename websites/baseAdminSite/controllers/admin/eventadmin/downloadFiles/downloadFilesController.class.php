<?php
/**
 * downloadFilesController
 *
 * Stored in downloadFilesController.class.php
 * 
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category downloadFilesController
 * @version $Rev: 11 $
 */


/**
 * downloadFilesController
 *
 * downloadFilesController class
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category downloadFilesController
 */
class downloadFilesController extends mvcDaoController {
	
	/**
	 * Handles listing objects and search options
	 * 
	 * @return void
	 */
	function actionView() {
		$this->getInputManager()->setLookupGlobals(utilityInputManager::LOOKUPGLOBALS_GET);
		$this->getInputManager()->addFilter('SourceID', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('FileType', utilityInputFilter::filterString());
		$data = $this->getInputManager()->doFilter();
		
		$this->setSearchOptionFromRequestData($data, 'SourceID');
		$this->setSearchOptionFromRequestData($data, 'FileType');
		
		parent::actionView();
	}
	
	/**
	 * @see mvcControllerBase::initialise()
	 */
	function initialise() {
		parent::initialise();
		
		$this->setControllerView('downloadFilesView');
		
		$this->getMenuItems()->getItem(self::ACTION_VIEW)->addItem(
			new mvcControllerMenuItem(
				$this->buildUriPath(self::ACTION_SEARCH), 'Search', self::IMAGE_ACTION_SEARCH, 'Search', false, mvcControllerMenuItem::PATH_TYPE_URI, true
			)
		);
	}
	
	/**
	 * @see mvcControllerBase::addInputFilters()
	 */
	function addInputFilters() {
		$this->getInputManager()->addFilter('PrimaryKey', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('DateModified', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Description', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Filetype', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Filename', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Language', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Properties', utilityInputFilter::filterStringArray());
		$this->getInputManager()->addFilter('NewProperty', utilityInputFilter::filterStringArray());
		$this->getInputManager()->addFilter('Sources', utilityInputFilter::filterStringArray());
	}
	
	/**
	 * @see mvcControllerBase::addInputToModel()
	 *
	 * @param array $inData
	 * @param downloadFilesModel $inModel
	 */
	function addInputToModel($inData, $inModel) {
		$inModel->setID($inData['PrimaryKey']);
		$inModel->setDateModified($inData['DateModified']);
		$inModel->setDescription($inData['Description']);
		$inModel->setFiletype($inData['Filetype']);
		$inModel->setFilename($inData['Filename']);
		$inModel->setLang($inData['Language']);

		if ( isset($inData['Properties']) && is_array($inData['Properties']) ) {
			foreach ( $inData['Properties'] as $param => $value ) {
				$inModel->getParamSet()->setParam($param, $value);
			}
		}
		if ( isset($inData['NewProperty']) && is_array($inData['NewProperty']) && count($inData['NewProperty']) == 2 ) {
			if ( isset($inData['NewProperty']['Name']) && strlen($inData['NewProperty']['Name']) > 0 ) {
				$inModel->getParamSet()->setParam($inData['NewProperty']['Name'], $inData['NewProperty']['Value']);
			}
		}
		if ( isset($inData['Sources']) && is_array($inData['Sources']) ) {
			$inModel->getSourceSet()->reset();
			foreach ( $inData['Sources'] as $sourceID ) {
				$inModel->getSourceSet()->setObject(mofilmSource::getInstance($sourceID));
			}
		}
		
		if ( $this->getAction() == self::ACTION_DO_EDIT && $this->getPrimaryKey() > 0 ) {
			systemLog::notice('Updating download resource for dlFile: '.$this->getPrimaryKey());
			$oDlFile = mofilmDownloadFile::getInstance((int)$this->getPrimaryKey());
			if ( $oDlFile->getID() > 0 ) {
				/*
				 * @todo DR: may have to change this to write immediately
				 */
				$oFileUpload = new mvcFileUpload(
					array(
						mvcFileUpload::OPTION_AUTO_CREATE_FILESTORE => false,
						mvcFileUpload::OPTION_CHECK_PERMISSIONS => false,
						mvcFileUpload::OPTION_FIELD_NAME => 'Files',
						mvcFileUpload::OPTION_SUB_FOLDER_FORMAT => '',
						mvcFileUpload::OPTION_WRITE_IMMEDIATE => false,
						mvcFileUpload::OPTION_STORE_RAW_DATA => true,
						mvcFileUpload::OPTION_USE_ORIGINAL_NAME => true,
					)
				);
				$oFileUpload->initialise();
				try {
					$oFiles = $oFileUpload->process();
					$oFile = $oFiles->getFirst();
					if ( $oFile instanceof mvcFileObject ) {
						if ( !$oDlFile->getFilename() ) {
							$oDlFile->setFilename($oDlFile->getFiletype().system::getDirSeparator().$oFile->getName());
						}
						
						systemLog::info('Checking file target location');
						$fileloc = mofilmConstants::getDownloadsFolder().system::getDirSeparator().$oDlFile->getFilename();
						if ( !file_exists(dirname($fileloc)) ) {
							mkdir(dirname($fileloc), 0755, true);
						}
						
						$bytes = file_put_contents($fileloc, $oFile->getRawFileData());
						systemLog::notice("Wrote $bytes bytes to the file system for fileID: {$oDlFile->getID()}");
						$oDlFile->save();
					}
				} catch ( mvcFileUploadException $e ) {
					systemLog::error($e->getMessage());
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
		$oModel = new downloadFilesModel();
		$this->setModel($oModel);
	}
}