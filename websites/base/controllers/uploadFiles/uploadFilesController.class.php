<?php
/**
 * uploadFilesController
 *
 * Stored in uploadFilesController.class.php
 * 
 * @author Pavan Kumar P G
 * @copyright Mofilm (c) 2009-2010
 * @package websites_mofilm.com
 * @subpackage controllers
 * @category uploadFilesController
 * @version $Rev: 1 $
 */


/**
 * uploadFilesController
 *
 * uploadFilesController class
 * 
 * @package wwebsites_mofilm.com
 * @subpackage controllers
 * @category uploadFilesController
 */
class uploadFilesController extends mvcController {
    
	const ACTION_LIST = 'uploadedFilesList';
	const ACTION_SEARCH = 'doSearch';
	const ACTION_UPLOAD = 'uploadFile';
	const ACTION_UPLOAD_STATUS = 'uploadFileStatus';
	const ACTION_DO_UPLOAD = 'doUploadAction';
	const ACTION_PROCESS = 'process';
	
	/**
	 * Stores $_UploadedFileName
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_UploadedFileName;
	
	/**
	 * Stores $_SearchQuery
	 *
	 * @var array
	 * @access protected
	 */
	protected $_SearchQuery;
	
	/**
	 * @see mvcControllerBase::initialise()
	 */
	function initialise() {
		parent::initialise();
		
		$this->setDefaultAction(self::ACTION_UPLOAD);
		$this->setRequiresAuthentication(true);
		
		$this->getControllerActions()
			->addAction(self::ACTION_LIST)
			->addAction(self::ACTION_SEARCH)
			->addAction(self::ACTION_UPLOAD)
			->addAction(self::ACTION_UPLOAD_STATUS)
			->addAction(self::ACTION_DO_UPLOAD)
			->addAction(self::ACTION_PROCESS);
		
		$this->addInputFilters();
	}
	
	/**
	 * @see mvcControllerBase::launch()
	 */
	function launch() {
		switch ( $this->getAction() ) {
			case self::ACTION_LIST:
			case self::ACTION_SEARCH: $this->search(); break;
			case self::ACTION_DO_UPLOAD: $this->uploadDocToServer();  break;
			case self::ACTION_PROCESS: $this->approvalProcess(); break;
			case self::ACTION_UPLOAD_STATUS: $this->uploadStatus(); break;
			default:
				$this->uploadDoc();
			break;
		}
	}
	
	/**
	 * 
	 */
	function uploadFilesList() {
		$oView = new uploadFilesView($this);
		$oView->showUploadedFilesList();
	}
	
	/**
	 * 
	 */
	function approvalProcess() {
		$this->getInputManager()->setLookupGlobals(utilityInputManager::LOOKUPGLOBALS_GET);
		$data = $this->getInputManager()->doFilter();
		
		$data['userID'] = $this->getRequest()->getSession()->getUser()->getID();
		$res = $this->getModel()->adminModeration($data);
		
		if ( $res == false) {
			$message = "An error occured while processing. Try again later";
			$level = mvcSession::MESSAGE_ERROR;
		} else {
			$message = $res;
			$level = mvcSession::MESSAGE_OK;
		}
		
		$oView = new uploadFilesView($this);
		$oView->sendJsonResult($message, $level);
	}
	
	/*
	 * 
	 */
	function search() {
		$this->getInputManager()->setLookupGlobals(utilityInputManager::LOOKUPGLOBALS_GET);
		$data = $this->getInputManager()->doFilter();
				
		$this->addInputToModel($data, $this->getModel());

		$oView = new uploadFilesView($this);
		$oView->showUploadedFilesList();
	}
	
	/**
	 * 
	 */
	function uploadDoc() {
		$this->getInputManager()->setLookupGlobals(utilityInputManager::LOOKUPGLOBALS_GET);
		$data = $this->getInputManager()->doFilter();

		$this->getModel()->setSourceID($data['SourceID']);
		
		$oView = new uploadFilesView($this);
		$oView->showUploadFilesPage();
	}
	
	/**
	 * 
	 */
	function uploadStatus($message=null) {
		$oView = new uploadFilesView($this);
		$oView->showUploadStatus($message);
	}
	
	/**
	 * 
	 */
	function uploadDocToServer() {
		$data = $this->getInputManager()->doFilter();
		
		$filename = 'NDA_'.mofilmSource::getInstance($data['SourceID'])->getName().'_'.mofilmSource::getInstance($data['SourceID'])->getEvent()->getName().'_';
		$result1 = $this->_ProcessUploadFile($filename);
		$siteLang = $this->getRequest()->getDistributor()->getSiteConfig()->getI18nDefaultLanguage()->getParamValue();
		
		if ( $result1 == null ) {
			if ( $siteLang == "zh" ) {
				$message = '没有文件被上传';
			} else {
				$message = 'No files were uploaded';
			}
		} else {
			if ( $result1 ) {
				$data['userID'] = $this->getRequest()->getSession()->getUser()->getID();
				$data['fileName'] = $this->_UploadedFileName;
				$data['preferredLanguage'] = $siteLang;
				$result = $this->getModel()->saveUploadedFileDetails($data);
				if ( $result ) {
					if ( $siteLang == "zh" ) {
						$message = '上传成功';
					} else {
						$message = 'File uploaded successfully';
					}
				} else {
					if ( $siteLang == "zh" ) {
						$message = '保存时出错，请重试';
					} else {
						$message = 'Problem occured while saving, please try again.';
					}
				}
			} else {
				if ( $siteLang == "zh" ) {
					$message = '上传时出错，请重试';
				} else {
					$message = 'Problem occured while uploading, please try again.';
				}
			}
		}

		$this->uploadStatus($message);
	}
	
	/**
	 * 
	 */
	private function _ProcessUploadFile($filename=null) {
		$return = true;
		try {
			$oFileUpload = new mvcFileUpload(
				array(
					mvcFileUpload::OPTION_FIELD_NAME => 'uploadDocuments',
					mvcFileUpload::OPTION_SUB_FOLDER_FORMAT => '',
					mvcFileUpload::OPTION_WRITE_IMMEDIATE => true,
					mvcFileUpload::OPTION_STORE_RAW_DATA => true,
					mvcFileUpload::OPTION_USE_ORIGINAL_NAME => false,
				)
			);
			
			$oFileUpload->setAddFilenamePrefix($filename);
			$oFileUpload->setFileStore(mofilmConstants::getUploadedFilesFolder());
			$oFileUpload->initialise();
			$oFiles = $oFileUpload->process();
			$this->_UploadedFileName = $oFiles->getFirst()->getName();

		} catch ( mvcFileUploadNoFileUploadedException $e ) {
			systemLog::warning($e->getMessage());
			$return = null;
		} catch ( mvcFileUploadException $e ) {
			systemLog::error($e->getMessage());
			$return = false;
		}
		return $return;
	}


	/**
	 * @see mvcControllerBase::addInputFilters()
	 */
	function addInputFilters() {
		$this->getInputManager()->addFilter('EventID', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('SourceID', utilityInputFilter::filterInt());

		if ( $this->getAction() == self::ACTION_SEARCH ) {
			$this->getInputManager()->addFilter('Status', utilityInputFilter::filterInt());
			$this->getInputManager()->addFilter('Offset', utilityInputFilter::filterInt());
			$this->getInputManager()->addFilter('Limit', utilityInputFilter::filterInt());
			$this->getInputManager()->addFilter('Status', utilityInputFilter::filterString());
			$this->getInputManager()->addFilter('EventID', utilityInputFilter::filterInt());
			$this->getInputManager()->addFilter('SourceID', utilityInputFilter::filterString());
			$this->getInputManager()->addFilter('OrderBy', utilityInputFilter::filterString());
		}
		
		if ( $this->getAction() == self::ACTION_PROCESS) {
			$this->getInputManager()->addFilter('Status', utilityInputFilter::filterString());
			$this->getInputManager()->addFilter('fileID', utilityInputFilter::filterInt());
		    
		}
	}
	
	/**
	 * @see mvcControllerBase::addInputToModel()
	 */
	function addInputToModel($inData, $inModel) {
		if ( !$inData['Limit'] || $inData['Limit'] > 30 ) {
			$inData['Limit'] = 30;
		}
		if ( !$inData['Offset'] || $inData['Offset'] < 0 ) {
			$inData['Offset'] = 0;
		}
		if ( $this->getAction() == self::ACTION_SEARCH || $this->getAction() == self::ACTION_LIST ) {
			/*
			 * Restrict search to only events / sources user can see unless they can search
			 */
			$inModel->getUploadedFilesSearch()->addEvent($inData['EventID']);

			if ( $inData['EventID'] == 0 ) {
				$res = mofilmSource::listOfDistinctSourceIDsByName($inData['SourceID']);
				foreach ($res as $re) {
					$inModel->getUploadedFilesSearch()->addSource($re);
				}
			} else {
				$inModel->getUploadedFilesSearch()->addSource($inData['SourceID']);
			}

			if ( in_array($inData['Status'], mofilmUploadedFiles::getAvailableUploadedFilesStatus()) ) {
				$inModel->getUploadedFilesSearch()->setStatus($inData['Status']);
			}

			$inModel->getUploadedFilesSearch()->setOffset($inData['Offset']);
			$inModel->getUploadedFilesSearch()->setLimit($inData['Limit']);

			if ( array_key_exists('OrderBy', $inData) && strlen($inData['OrderBy']) > 1 ) {
				$inModel->getUploadedFilesSearch()->setOrderBy($inData['OrderBy']);
			}

			unset($inData['Offset'], $inData['Limit'], $inData['OrderBy']);

			$this->setSearchQuery($inData);
		}
	}

	/**
	 * Returns the search query parameters as an array
	 *
	 * @return array
	 */
	function getSearchQuery() {
		return $this->_SearchQuery;
	}
	
	/**
	 * Returns the search query as a string
	 * 
	 * @return string
	 */
	function getSearchQueryAsString() {
		return http_build_query($this->getSearchQuery());
	}
	
	/**
	 * Set $_SearchQuery to $inSearchQuery
	 *
	 * @param array $inSearchQuery
	 * @return usersController
	 */
	function setSearchQuery($inSearchQuery) {
		if ( $inSearchQuery !== $this->_SearchQuery ) {
			$this->_SearchQuery = $inSearchQuery;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Fetches the model
	 *
	 * @return downloadModel
	 */
	function getModel() {
		if ( !parent::getModel() ) {
			$this->buildModel();
		}
		return parent::getModel();
	}
	
	/**
	 * Builds the model
	 *
	 * @return void
	 */
	function buildModel() {
		$oModel = new uploadFilesModel();
		$this->setModel($oModel);
	}
}