<?php
/**
 * sendCCAController
 *
 * Stored in sendCCAController.class.php
 * 
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category sendCCAController
 * @version $Rev: 624 $
 */


/**
 * sendCCAController
 *
 * sendCCAController class
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category sendCCAController
 */
class sendCCAController extends mvcDaoController {
	
	const ACTION_DO_NEW_OBJECT = "doNewObject";
	const ACTION_DIS = "disp";
	/**
	 * @see mvcControllerBase::initialise()
	 */
	function initialise() {
		parent::initialise();
		$this->getControllerActions()
				->addAction(self::ACTION_DO_NEW_OBJECT)
				->addAction(self::ACTION_DIS);
		$this->setControllerView('sendCCAView');

		$this->getMenuItems()
				->getItem(self::ACTION_VIEW)
				->addItem(
					new mvcControllerMenuItem(
						'/admin/commsCentre/newsLetterMofilm/newsletter/newObject', 'Create CCA Email', newsletterController::ACTION_NEW, 'CCA Email', false, mvcControllerMenuItem::PATH_TYPE_URI
					)
				);
	}
	
	/**
	 * @see mvcControllerBase::launch()
	 */
	function launch() {
		if ( $this->getAction() == self::ACTION_DO_NEW_OBJECT) {
			$this->saveNewObject();
		} elseif ( $this->getAction() == self::ACTION_DIS ) {
			$this->getInputManager()->setLookupGlobals(utilityInputManager::LOOKUPGLOBALS_GET);
			$data = $this->getInputManager()->doFilter();

			$path_parts = pathinfo($data['path']);
			$ext = strtolower($path_parts["extension"]);
			switch( $ext ){
				case "pdf": $ctype="application/pdf"; break;
				case "doc": $ctype="application/msword"; break;
				default: $ctype = "application/octet-stream";
			}

			header("HTTP/1.0 200 OK");
			header("Cache-Control: public");
			header("Content-Type: $ctype");
			header("Content-Disposition: attachment; filename=\"".basename($data["path"])."\"");
			header("Content-Length: ".filesize($data['path']));
			readfile($data["path"]);
			exit;
		} else {
			parent::launch();
		}	
	}
	
	/**
	 * 
	 */
	function saveNewObject() {
		$this->addInputFilters();
		$data = $this->getInputManager()->doFilter();
		$this->addInputToModel($data, $this->getModel());
		
		$this->redirect($this->buildUriPath(self::ACTION_VIEW));
	}
	
	/**
	 * @see mvcControllerBase::addInputFilters()
	 */
	function addInputFilters() {
		$this->getInputManager()->addFilter('PrimaryKey', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('Id', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('NlidS', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('NlidNs', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('NlidNw', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('Status', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('Classname', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('EventParams', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('EventID', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('SourceID', utilityInputFilter::filterStringArray());
		$this->getInputManager()->addFilter('Params_list', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('EmailName', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('MessageType', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('ScheduledDate', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('path', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('videoRating', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('Type', utilityInputFilter::filterString());
	}
	
	/**
	 * @see mvcControllerBase::addInputToModel()
	 *
	 * @param array $inData
	 * @param sendCCAModel $inModel
	 */
	function addInputToModel($inData, $inModel) {
	    
		if ( $this->getAction() == self::ACTION_DO_NEW_OBJECT ) {

			//$this->validateParams($inData);
			
			if ($inData['Type'] == 'shortlist') {
				if ($inData['NlidS'] > 0) {
					$inModel->saveObject($inData, 'NlidS');
				}

				if ($inData['NlidNs'] > 0) {
					$inModel->saveObject($inData, 'NlidNs');
				}
			} elseif ($inData['Type'] == 'nonwinners') {
				if ($inData['NlidNw'] > 0) {
					$inModel->saveObject($inData, 'NlidNw');
				}
			}
		}
		
		/*
		try {
			$oFileUpload = new mvcFileUpload();
			$oFileUpload->setSubFolderFormat("/");
			$oFileUpload->setFileStore(mofilmConstants::getEmailAttachment());
			$oFileUpload->setFieldName('Cca');
			$oFileUpload->initialise();
			$oFileUpload->setUseOriginalFileName(true);
			$oFileUpload->setWriteFilesImmediately(true);
			$oFileSet = $oFileUpload->process();
			$inModel->getParamSet()->setParam(mofilmCommsNewsletterdata::PARAM_NL_ATTACH, mofilmConstants::getEmailAttachment()."/".$oFileSet->getFirst()->getName());
		}
		
		catch (mvcFileUploadException $e) {
				systemLog::error($e->getMessage());
		}
		 * 
		 */
	}

	/**
	 * Validates the Event params
	 *
	 * @param array $inData
	 * @return void
	 */
	function validateParams($inData) {
		$oMofilmCommsNewsletterFilterClass = mofilmCommsNewsletterFilterclass::getInstance($inData['EventParams']);
		$paramArray = preg_split("/\//",$oMofilmCommsNewsletterFilterClass->getDefaultParams());
		foreach ( $paramArray as $value ) {
			if ( $inData[$value] == "" || $inData[$value] == 0 ) {
				throw new mofilmException("Invalid Param for ".$value);
			}
		}
	}
	
	
	/**
	 * Builds the model
	 *
	 * @return void
	 */
	function buildModel() {
		$oModel = new sendCCAModel();
		$this->setModel($oModel);
	}
}