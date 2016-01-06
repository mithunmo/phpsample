<?php
/**
 * CoverImageController
 *
 * Stored in CoverImageController.class.php
 * 
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category CoverImageController
 * @version $Rev: 624 $
 */


/**
 * CoverImageController
 *
 * CoverImageController class
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category CoverImageController
 */
class CoverImageController extends mvcDaoController {
	
	/**
	 * @see mvcControllerBase::initialise()
	 */
	function initialise() {
		parent::initialise();
		
		$this->setControllerView('CoverImageView');
		$this->getControllerActions()->addAction(self::ACTION_DO_NEW);
                $this->getControllerActions()->addAction(self::ACTION_NEW);
                
	}
	
        function launch() {
            
            switch ( $this->getAction() ) {
                case self::ACTION_NEW:    $this->actionNew();    break;
                case self::ACTION_DO_NEW:    $this->actionDoNew();    break;
                default:
                        parent::launch();
			break;
            }
        }
        
        function actionNew() {
            
            $oView = new CoverImageView($this);
            $oView->showUploadPage();            
        }
	/**
	 * Handles creating a new object and storing it
	 * 
	 * @return void
	 */
	function actionDoNew() {
		try {
			$this->buildModel();

			//$this->getInputManager()->setLookupGlobals(utilityInputManager::LOOKUPGLOBALS_POST);
			$data = $this->getInputManager()->doFilter();

			$oFileUpload = new mvcFileUpload(
					array(
						mvcFileUpload::OPTION_AUTO_CREATE_FILESTORE => false,
						mvcFileUpload::OPTION_CHECK_PERMISSIONS => false,
						mvcFileUpload::OPTION_FIELD_NAME => 'PathImage',
						mvcFileUpload::OPTION_SUB_FOLDER_FORMAT => '',
						mvcFileUpload::OPTION_WRITE_IMMEDIATE => true,
						mvcFileUpload::OPTION_STORE_RAW_DATA => false,
						mvcFileUpload::OPTION_USE_ORIGINAL_NAME => false
					)
			);

			$oFileUpload->setFileStore(mofilmConstants::getUploadedMusic()."/featuredArtist");
			$oFileUpload->initialise();
			$oFiles = $oFileUpload->process();
			
			systemLog::message( $oFiles->getFirst()->getName() );
			systemLog::message( $oFiles->getFirst()->getOriginalName() );
			systemLog::message( mofilmConstants::getUploadedMusic()."/featuredArtist/" . $oFiles->getFirst()->getName() );
                        
                        
                        $data["PathImage"] = mofilmConstants::getUploadedMusic()."/featuredArtist/" . $oFiles->getFirst()->getName();
			$this->addInputToModel($data, $this->getModel());
			$this->getModel()->save();

			$msg = 'New '.get_class($this->getModel()).' with ID '.$this->getModel()->getPrimaryKey().' created successfully';
			$this->buildActivityLog($msg)->save();

			systemLog::notice($msg);
			$this->getRequest()->getSession()->setStatusMessage($msg, mvcSession::MESSAGE_OK);

			$this->redirect($this->buildUriPath(self::ACTION_VIEW));

		} catch (Exception $e) {
			systemLog::error(__CLASS__.'::'.__FUNCTION__.' '.$e->getMessage());
			$this->buildActivityLog(
				$this->getRequest()->getSession()->getUser()->getUsername().' tried store a new object but it failed with error: '.$e->getMessage()
			)->save();

			$this->setAction(self::ACTION_NEW);
			$this->getRequest()->getSession()->setStatusMessage($e->getMessage(), mvcSession::MESSAGE_ERROR);

			$oView = new $this->_ControllerView($this);
			$oView->showDaoPage();
		}
	}
        
        
	/**
	 * @see mvcControllerBase::addInputFilters()
	 */
	function addInputFilters() {
		$this->getInputManager()->addFilter('PrimaryKey', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('ID', utilityInputFilter::filterString());
                $this->getInputManager()->addFilter('Name', utilityInputFilter::filterString());
		//$this->getInputManager()->addFilter('PathImage', utilityInputFilter::filterString());
	}
	
	/**
	 * @see mvcControllerBase::addInputToModel()
	 *
	 * @param array $inData
	 * @param CoverImageModel $inModel
	 */
	function addInputToModel($inData, $inModel) {
		/**
		 * @todo set the primary key here
		 */
		//$inModel->setPrimaryKey($inData['PrimaryKey']);
		//$inModel->setID($inData['ID']);
		$inModel->setPathImage($inData['PathImage']);
                $inModel->setName($inData['Name']);
	}
	
	/**
	 * Builds the model
	 *
	 * @return void
	 */
	function buildModel() {
		$oModel = new CoverImageModel();
		$this->setModel($oModel);
	}
}