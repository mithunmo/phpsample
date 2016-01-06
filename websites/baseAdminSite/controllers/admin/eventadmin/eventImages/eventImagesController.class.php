<?php
/**
 * eventImagesController
 *
 * Stored in eventImagesController.class.php
 * 
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category eventImagesController
 * @version $Rev: 623 $
 */


/**
 * eventImagesController
 *
 * eventImagesController class
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category eventImagesController
 */
class eventImagesController extends mvcController {
	
	const ACTION_VIEW = 'view';
	const ACTION_EDIT = 'edit';
	const ACTION_DO_EDIT = 'doEdit';
	
	/**
	 * @see mvcControllerBase::initialise()
	 */
	function initialise() {
		parent::initialise();
		
		$this->setDefaultAction(self::ACTION_VIEW);
		$this->getControllerActions()
			->addAction(self::ACTION_VIEW)
			->addAction(self::ACTION_EDIT)
			->addAction(self::ACTION_DO_EDIT);
	}
	
	/**
	 * @see mvcControllerBase::launch()
	 */
	function launch() {
		switch ( $this->getAction() ) {
			case self::ACTION_EDIT:		$this->editAction(); break;
			case self::ACTION_DO_EDIT:	$this->doEditAction(); break;

			case self::ACTION_VIEW:
			default:
				$oView = new eventImagesView($this);
				$oView->showEventImagesPage();
		}
	}

	/**
	 * Handles displaying the editing form
	 *
	 * @return void
	 */
	function editAction() {
		$eventID = $this->getActionFromRequest(false, 1);
		$this->getModel()->setEventID($eventID);
		if ( $this->getModel()->getEvent()->getID() > 0 ) {
			$oView = new eventImagesView($this);
			$oView->showEditPage();
		} else {
			$this->getRequest()->getSession()->setStatusMessage('Missing or invalid EventID provided', mvcSession::MESSAGE_ERROR);
			$this->redirect($this->buildUriPath(self::ACTION_VIEW));
		}
	}

	/**
	 * Handles committing changes to the objects
	 *
	 * @return void
	 */
	function doEditAction() {
		$eventID = $this->getActionFromRequest(false, 1);
		$this->getModel()->setEventID($eventID);
		if ( !$this->getModel()->getEvent()->getID() > 0 ) {
			$this->getRequest()->getSession()->setStatusMessage('Missing or invalid EventID provided', mvcSession::MESSAGE_ERROR);
			$this->redirect($this->buildUriPath(self::ACTION_VIEW));
			return;
		}
		
		$res = $this->_processEventImage();
		$res2 = $this->_processSourceImages();

		if ( $res === null && $res2 === null ) {
			$this->getRequest()->getSession()->setStatusMessage('No files were uploaded', mvcSession::MESSAGE_INFO);
		} else {
			if ( $res && $res2 ) {
				$this->getRequest()->getSession()->setStatusMessage('All files uploaded successfully', mvcSession::MESSAGE_OK);
			} else {
				$this->getRequest()->getSession()->setStatusMessage('One or more of your files did not upload. Please contact it@mofilm.com if you continue to see this message.', mvcSession::MESSAGE_ERROR);
			}
		}

		$this->redirect($this->buildUriPath(self::ACTION_VIEW));
	}

	/**
	 * Processes the event image (if any)
	 *
	 * @return boolean
	 */
	protected function _processEventImage() {
		$return = true;

		$oFileUpload = new mvcFileUpload(
			array(
				mvcFileUpload::OPTION_AUTO_CREATE_FILESTORE => false,
				mvcFileUpload::OPTION_CHECK_PERMISSIONS => false,
				mvcFileUpload::OPTION_FIELD_NAME => 'EventImage',
				mvcFileUpload::OPTION_SUB_FOLDER_FORMAT => '',
				mvcFileUpload::OPTION_WRITE_IMMEDIATE => false,
				mvcFileUpload::OPTION_STORE_RAW_DATA => true,
			)
		);
		$oFileUpload->initialise();
		try {
			$oFiles = $oFileUpload->process();
			if ( $oFiles->getCount() > 0 ) {
				systemLog::message('Adding images for '.$this->getModel()->getEvent()->getName());

				$oImageConv = new imageConvertor(
					array(
						imageConvertor::OPTION_OUTPUT_FILENAME => $this->getModel()->getEvent()->getLogoName(),
						imageConvertor::OPTION_OUTPUT_OVERWRITE_FILES => true,
					)
				);

				$images = array(
					'client' => array(
						imageConvertor::OPTION_OUTPUT_LOCATION => mofilmConstants::getClientEventsFolder(),
						imageConvertor::OPTION_OUTPUT_FORMAT => 'jpeg',
						imageConvertor::OPTION_OUTPUT_QUALITY => 90,
						imageConvertor::OPTION_OUTPUT_WIDTH => 261,
						imageConvertor::OPTION_OUTPUT_HEIGHT => 139,
						imageConvertor::OPTION_OUTPUT_PAD_IMAGE => true,
						imageConvertor::OPTION_OUTPUT_PAD_COLOUR => 'white',
					),
					'admin' => array(
						imageConvertor::OPTION_OUTPUT_LOCATION => mofilmConstants::getAdminEventsFolder(),
						imageConvertor::OPTION_OUTPUT_FORMAT => 'jpeg',
						imageConvertor::OPTION_OUTPUT_WIDTH => 50,
						imageConvertor::OPTION_OUTPUT_HEIGHT => 28,
						imageConvertor::OPTION_OUTPUT_QUALITY => 90,
						imageConvertor::OPTION_OUTPUT_PAD_IMAGE => true,
						imageConvertor::OPTION_OUTPUT_PAD_COLOUR => 'white',
					),
				);

				foreach ( $images as $type => $options ) {
					systemLog::message("Creating $type source image");
					$oImageConv->setOptions($options);
					$oImageConv->process($oFiles->getFirst()->getRawFileData());
				}
			}
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
	 * Processes the uploaded source images (if any)
	 *
	 * @return boolean
	 */
	protected function _processSourceImages() {
		$return = true;

		$oFileUpload = new mvcFileUpload(
			array(
				mvcFileUpload::OPTION_AUTO_CREATE_FILESTORE => false,
				mvcFileUpload::OPTION_CHECK_PERMISSIONS => false,
				mvcFileUpload::OPTION_FIELD_NAME => 'Source',
				mvcFileUpload::OPTION_SUB_FOLDER_FORMAT => '',
				mvcFileUpload::OPTION_WRITE_IMMEDIATE => false,
				mvcFileUpload::OPTION_STORE_RAW_DATA => true,
			)
		);

		try {
			$oFileUpload->initialise();
			$oFileUpload->process();
		} catch ( mvcFileUploadNoFileUploadedException $e ) {
			systemLog::warning($e->getMessage());
			return null;
		} catch ( mvcFileUploadException $e ) {
			systemLog::warning($e->getMessage());
			if ( $oFileUpload->getUploadedFiles()->getCount() == 0 ) {
				$return = null;
			} else {
				$return = false;
			}
		}
		
		$oFiles = $oFileUpload->getUploadedFiles();
		if ( $oFiles->getCount() > 0 ) {
			/* @var mvcFileObject $oFile */
			foreach ( $oFiles as $oFile ) {
				systemLog::message('Adding images for '.$oFile->getUploadKey());
				$oSource = $this->getModel()->getEvent()->getSourceSet()->getObjectByID($oFile->getUploadKey());
				if ( $oSource instanceof mofilmSource && $oSource->getID() > 0 ) {
					$oImageConv = new imageConvertor(
						array(
							imageConvertor::OPTION_OUTPUT_FILENAME => $oSource->getLogoName(),
							imageConvertor::OPTION_OUTPUT_OVERWRITE_FILES => true,
						)
					);

					$images = array(
						'client' => array(
							imageConvertor::OPTION_OUTPUT_LOCATION => mofilmConstants::getClientSourceFolder(),
							imageConvertor::OPTION_OUTPUT_FORMAT => 'jpeg',
							imageConvertor::OPTION_OUTPUT_QUALITY => 90,
							imageConvertor::OPTION_OUTPUT_WIDTH => 261,
							imageConvertor::OPTION_OUTPUT_HEIGHT => 139,
							imageConvertor::OPTION_OUTPUT_PAD_IMAGE => true,
							imageConvertor::OPTION_OUTPUT_PAD_COLOUR => 'white',
						),
						'admin' => array(
							imageConvertor::OPTION_OUTPUT_LOCATION => mofilmConstants::getAdminSourceFolder(),
							imageConvertor::OPTION_OUTPUT_FORMAT => 'jpeg',
							imageConvertor::OPTION_OUTPUT_WIDTH => 50,
							imageConvertor::OPTION_OUTPUT_HEIGHT => 28,
							imageConvertor::OPTION_OUTPUT_QUALITY => 90,
							imageConvertor::OPTION_OUTPUT_PAD_IMAGE => true,
							imageConvertor::OPTION_OUTPUT_PAD_COLOUR => 'white',
						),
					);

					foreach ( $images as $type => $options ) {
						systemLog::message("Creating $type source image");
						$oImageConv->setOptions($options);
						$oImageConv->process($oFile->getRawFileData());
					}
				}
			}
		}

		return $return;
	}
	
	
	/**
	 * @see mvcControllerBase::addInputFilters()
	 */
	function addInputFilters() {
		
	}
	
	/**
	 * @see mvcControllerBase::addInputToModel()
	 */
	function addInputToModel($inData, $inModel) {

	}
	
	/**
	 * Fetches the model
	 *
	 * @return eventImagesModel
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
		$oModel = new eventImagesModel();
		$this->setModel($oModel);
	}
}