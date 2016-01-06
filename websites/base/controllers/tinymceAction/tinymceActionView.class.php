<?php
/**
 * tinymceActionView.class.php
 * 
 * tinymceActionView class
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package websites_base
 * @subpackage controllers
 * @category tinymceActionView
 * @version $Rev: 634 $
 */


/**
 * tinymceActionView class
 * 
 * Provides the "tinymceActionView" page
 * 
 * @package websites_base
 * @subpackage controllers
 * @category tinymceActionView
 */
class tinymceActionView extends mvcView {


	function  __construct($inController) {
		parent::__construct($inController);
	}
	
	/**
	 * @see mvcViewBase::setupInitialVars()
	 */
	function setupInitialVars() {
		parent::setupInitialVars();
		$this->addJavascriptResource(new mvcViewJavascript('core', mvcViewJavascript::TYPE_FILE, '/libraries/core_js/core.js'));
		$this->addJavascriptResource(new mvcViewJavascript('jquery_min', mvcViewJavascript::TYPE_FILE, '/libraries/jquery/jquery.min.js'));
		$this->addJavascriptResource(new mvcViewJavascript('jquery_ui', mvcViewJavascript::TYPE_FILE, '/libraries/jquery-ui/jquery-ui.min.js'));
		$this->addJavascriptResource(new mvcViewJavascript('jqueryValidate', mvcViewJavascript::TYPE_FILE, '/libraries/jquery-validate/jquery-validate.min.js'));
		$this->addJavascriptResource(new mvcViewJavascript('tinypop', mvcViewJavascript::TYPE_FILE, '/libraries/jquery-plugins/tinymce/jscripts/tiny_mce/tiny_mce_popup.js'));
		$this->addJavascriptResource(new mvcViewJavascript('dialog', mvcViewJavascript::TYPE_FILE, '/libraries/jquery-plugins/tinymce/jscripts/tiny_mce/plugins/browser/tinypop.js'));
		$this->addJavascriptResource(new mvcViewJavascript('tinymce_Validate', mvcViewJavascript::TYPE_FILE, '/libraries/mofilm/tinymceValidation.js'));

	}

	/**
	 * Shows the tiny mce browse folder page based on the folder requestd
	 */
	function showBrowsePage() {
		$this->getEngine()->assign("imageFile",  utilityOutputWrapper::wrap($this->getModel()->getImageFiles($this->getModel()->getFolderPath())));
		$this->getEngine()->assign("oModel",  utilityOutputWrapper::wrap($this->getModel()));
		$this->getEngine()->assign("formaction","/tinymceAction/upload");
		$this->getEngine()->assign("parentPath",$this->getModel()->getParentPath());
		$this->render($this->getTpl('tiny'));
	}

	/**
	 * Shows the error page
	 *
	 * @param string $inMessage
	 */
	function showErrorPage($inMessage) {
		$this->getEngine()->assign("oModel",  utilityOutputWrapper::wrap($this->getModel()));
		$this->getEngine()->assign("parentPath",$this->getModel()->getParentPath());
		$this->getEngine()->assign('message',$inMessage);
		$this->render($this->getTpl('error'));
	}

	/**
	 * Shows the directory page
	 *
	 * @return void
	 */
	function showDirPage() {
		$this->getEngine()->assign("oModel",  utilityOutputWrapper::wrap($this->getModel()));
		$this->render($this->getTpl('newdir'));
	}

	/**
	 * Shows the upload page
	 *
	 * @return void
	 */
	function showUploadPage() {
		$this->getEngine()->assign("formaction","/tinymceAction/upload");
		$this->getEngine()->assign("oModel",  utilityOutputWrapper::wrap($this->getModel()));
		$this->render($this->getTpl('showupload'));
	}



}