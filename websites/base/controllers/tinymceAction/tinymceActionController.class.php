<?php
/**
 * tinymceActionController
 *
 * Stored in tinymceActionController.class.php
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package websites_base
 * @subpackage controllers
 * @category tinymceActionController
 * @version $Rev: 736 $
 */


/**
 * tinymceActionController
 *
 * tinymceActionController class
 *
 * @package websites_base
 * @subpackage controllers
 * @category tinymceActionController
 */
class tinymceActionController extends mvcController {

	const ACTION_BROWSE = 'browse';
	const ACTION_UPLOAD = 'upload';
	const ACTION_CREATEDIR = 'createdir';
	const ACTION_DELETEDIR = 'deletedir';
	const ACTION_SHOWDIR = 'showdir';
	const ACTION_SHOWUPLOAD = 'showupload';


	/**
	 * @see mvcControllerBase::initialise()
	 */
	function initialise() {
		parent::initialise();
		
		$this->setRequiresAuthentication(true);
		$this->getControllerActions()->addAction(self::ACTION_BROWSE);
		$this->getControllerActions()->addAction(self::ACTION_UPLOAD);
		$this->getControllerActions()->addAction(self::ACTION_CREATEDIR);
		$this->getControllerActions()->addAction(self::ACTION_SHOWDIR);
		$this->getControllerActions()->addAction(self::ACTION_SHOWUPLOAD);
		$this->getControllerActions()->addAction(self::ACTION_DELETEDIR);
	}

	/**
	 * @see mvcControllerBase::launch()
	 */
	function launch() {
		switch ( $this->getAction() ) {
			case self::ACTION_BROWSE:     $this->doActionBrowse();	    break;
			case self::ACTION_UPLOAD:     $this->doActionUpload();	    break;
			case self::ACTION_CREATEDIR:  $this->doActionCreateDir();   break;
			case self::ACTION_SHOWDIR:    $this->doActionShowDir();	    break;
			case self::ACTION_SHOWUPLOAD:  $this->doActionShowUpload(); break;
			case self::ACTION_DELETEDIR:   $this->doActionDeleteDir();  break;
			default: throw new mvcDistributorInvalidRequestException($this->getAction()); break;
		}
	}

	/**
	 * @see mvcControllerBase::addInputFilters()
	 */
	function addInputFilters() {
		$this->getInputManager()->addFilter("folder", utilityInputFilter::filterString());
		$this->getInputManager()->addFilter("newdir", utilityInputFilter::filterString());
		$this->getInputManager()->addFilter("path", utilityInputFilter::filterString());
	}

	/**
	 * @see mvcControllerBase::addInputToModel()
	 */
	function addInputToModel($inData, $inModel) {
		$inModel->setDirName($inData['newdir']);
		$inModel->setFolderPath($inData['folder']);
	}

	/**
	 * Fetches the model
	 *
	 * @return tinymceActionModel
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
		$oModel = new tinymceActionModel();
		$this->setModel($oModel);
	}

	/**
	 * Shows the new directory page
	 *
	 * @return void
	 */
	function doActionShowDir() {
		$this->addInputFilters();
		$this->getInputManager()->setLookupGlobals(utilityInputManager::LOOKUPGLOBALS_GET);
		$data = $this->getInputManager()->doFilter();
		$type = $data["folder"];
		$this->addInputToModel($data, $this->getModel());
		if ( $type == "" ) {
			$this->getModel()->setFolderPath("resources");
		} else {
			$this->getModel()->setFolderPath($type);
		}
		$oView = new tinymceActionView($this);
		$oView->showDirPage();
	}

	/**
	 * Shows the upload page
	 *
	 * @return void
	 */
	function doActionShowUpload() {
		$this->addInputFilters();
		$this->getInputManager()->setLookupGlobals(utilityInputManager::LOOKUPGLOBALS_GET);
		$data = $this->getInputManager()->doFilter();
		$type = $data["folder"];
		$this->addInputToModel($data, $this->getModel());
		if ( $type == "" ) {
			$this->getModel()->setFolderPath("resources");
		} else {
			$this->getModel()->setFolderPath($type);
		}
		$oView = new tinymceActionView($this);
		$oView->showUploadPage();
	}

	/**
	 * Hanndles the create directory functionality.This is only valid for root and mofilm management
	 *
	 * @return void
	 */
	function doActionCreateDir() {
		$this->addInputFilters();
		$data = $this->getInputManager()->doFilter();
		$this->addInputToModel($data, $this->getModel());
		if ( $this->getModel()->createFolder() ) {
			$this->redirect("/tinymceAction/browse?folder=" . $data['folder']);
		} else {
			$oView = new tinymceActionView($this);
			$oView->showErrorPage("Folder Permission denied or some error ocurred");
		}
	}

	/**
	 * Handles the upload functionality
	 *
	 * @return void
	 */
	function doActionUpload() {
		$this->getInputManager()->setLookupGlobals(utilityInputManager::LOOKUPGLOBALS_GET);
		$data = $this->getInputManager()->doFilter();
		$type = $data["folder"];
		$this->getModel()->setFolderPath($type);
		try {
			$oFileUpload = new mvcFileUpload();
			$oFileUpload->setSubFolderFormat("/");
			$oFileUpload->setFileStore($this->getModel()->getFolderPath());
			$oFileUpload->setFieldName('tinyMCEImagefile');
			$oFileUpload->initialise();
			$oFileUpload->setUseOriginalFileName(true);
			$oFileUpload->setWriteFilesImmediately(true);
			$oFileUpload->process();
			$this->redirect("/tinymceAction/browse?folder=" . $type);
		}
		catch (mvcFileException $e) {
			$oView = new tinymceActionView($this);
			$oView->showErrorPage($e->getOriginalErrorMessage());
		}
	}

	/**
	 * Handles the action browse in the tinymce image editor
	 *
	 * @return void
	 *
	 */
	function doActionBrowse() {
		$this->getInputManager()->setLookupGlobals(utilityInputManager::LOOKUPGLOBALS_GET);
		$data = $this->getInputManager()->doFilter();
		$type = $data["folder"];
		$this->addInputToModel($data, $this->getModel());
		if ( $type == "" ) {
			$this->getModel()->setFolderPath("resources");
		} else {
			$this->getModel()->setFolderPath($type);
		}
		$oView = new tinymceActionView($this);
		$oView->showBrowsePage();

	}
	
	/**
	 * Deletes the directory if it is empty
	 *
	 * @return void
	 */
	function doActionDeleteDir() {
		$data = $this->getInputManager()->doFilter();
		if ( $this->getModel()->getFilesCount( $data['path'] ) ) {
			if ( $this->getModel()->removeDir( $data['path'] ) ) {
				echo "deleted";
			} else {
				echo "could not delete";
			}
		} else {
			echo "counld not delete";
		}
	}
}