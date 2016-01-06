<?php
/**
 * uploadView.class.php
 * 
 * uploadView class
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package websites_mofilm.com
 * @subpackage controllers
 * @category uploadView
 * @version $Rev: 393 $
 */


/**
 * uploadView class
 * 
 * Provides the "uploadView" page
 * 
 * @package websites_mofilm.com
 * @subpackage controllers
 * @category uploadView
 */
class uploadView extends mvcView {

	/**
	 * Assigns some default values to template engine that are always needed
	 *
	 * @return void
	 */
	function setupInitialVars() {
		parent::setupInitialVars();
		
		$this->getEngine()->assign('uploadUri', $this->getController()->buildUriPath(uploadController::ACTION_UPLOAD));
		$this->getEngine()->assign('doUploadUri', $this->getController()->buildUriPath(uploadController::ACTION_DO_UPLOAD));
		$this->getEngine()->assign('doMovieSave', $this->getController()->buildUriPath(uploadController::ACTION_MOVIE_SAVE));
	}

	
	/**
	 * Displays the user profile complete page
	 *
	 * @return void
	 */
	function showUploadCompletePage() {
		$this->setCacheLevelNone();
		$this->render($this->getTpl('uploadComplete', '/account'));		
	
	}
	/**
	 * Displays the users profile
	 *
	 * @return void
	 */
	function showUploadPage() {
		$this->setCacheLevelNone();
		$this->getEngine()->assign('oLicenseSet', $this->getModel()->getLicenseList($this->getController()->getRequest()->getSession()->getUser()->getID()));
		$this->getEngine()->assign('eventID', $this->getModel()->getEventID());
		//$this->addCssResource(new mvcViewCss('uploadifycss', mvcViewCss::TYPE_FILE, '/libraries/plupload/js/jquery.ui.plupload/css/jquery.ui.plupload.css'));
		$this->addCssResource(new mvcViewCss('jscss', mvcViewCss::TYPE_FILE, '/libraries/jquery-ui/themes/smoothness/jquery-ui.css'));
		$this->addCssResource(new mvcViewCss('styleWizardCss', mvcViewCss::TYPE_FILE, '/libraries/jquery-plugins/smartWizard2/styles/smart_wizard.css'));
		
		$this->addCssResource(new mvcViewCss('uploadifycss', mvcViewCss::TYPE_FILE, '/libraries/uploadify-v3.1/uploadify.css'));
		$this->addJavascriptResource(new mvcViewJavascript('uploadify', mvcViewJavascript::TYPE_FILE, '/libraries/uploadify-v3.1/jquery.uploadify-3.1.min.js'));

		$this->addJavascriptResource(new mvcViewJavascript('uploadifv3', mvcViewJavascript::TYPE_INLINE, "$('#FileUpload').uploadify({
		height        : 30,
		fileObjName   : 'Files',	
		swf           : '/libraries/uploadify-v3.1/uploadify.swf',
		uploader      : '{$this->buildUriPath(uploadController::ACTION_DO_UPLOAD)}',
		width         : 120,
		'fileSizeLimit' : '500MB',	
		fileTypeExts  : '*.mp4;*.mov;*.avi;*.mpg;*.wmv;*.m4v',
		'onUploadSuccess' : function(file, data, response) {
				$('#fileNameStored').val(data);
				$('#uploadStatus').val(\"\");
				$('#msg_filename').text(\"Upload done : \"+file.name);
        },
		'onUploadError': function (file, errorCode, errorMsg, errorString) {
			$('#msg_filename').text(\"\");
			alert('The file ' + file.name + ' could not be uploaded: ' + errorString);
		},
		'onSelect' : function(file) {
			$('#uploadStatus').val(\"started\");
			$('#msg_filename').text(\"\");
		},
		'buttonText' : 'Upload Video',	
		formData : { '{$this->getRequest()->getSession()->getSessionName()}' : '{$this->getRequest()->getSession()->getSessionID()}'
		}		
		});
		"));
		
		//$this->addCssResource(new mvcViewCss('uploadifycss', mvcViewCss::TYPE_FILE, '/libraries/jquery-uploadify/uploadify.css'));

		//$this->addJavascriptResource(new mvcViewJavascript('swfobject', mvcViewJavascript::TYPE_FILE, '/libraries/swfobject/swfobject.js'));
		//$this->addJavascriptResource(new mvcViewJavascript('uploadify', mvcViewJavascript::TYPE_FILE, '/libraries/jquery-uploadify/jquery.uploadify.min.js'));
		/*
		$this->addJavascriptResource(new mvcViewJavascript('uploadifyInit', mvcViewJavascript::TYPE_INLINE, "$('#FileUpload').uploadify({
				'uploader'  : '/libraries/jquery-uploadify/uploadify.swf',
				'script'    : '{$this->buildUriPath(uploadController::ACTION_DO_UPLOAD)}',
				'cancelImg' : '/libraries/jquery-uploadify/cancel.png',
				'auto'      : true,
				'fileExt'   : '*.mov;*.avi;*.wmv;*.mp4;*.mpg;*.m4v',
				'fileDesc'  : 'Video Files',
				'sizeLimit'  : 500000000,
				'fileDataName': 'Files',
				'scriptData': {
					'{$this->getRequest()->getSession()->getSessionName()}': '{$this->getRequest()->getSession()->getSessionID()}',
					'ajax': true
				},
				'onComplete'  : function(event, ID, fileObj, response, data) {
					if ( response != 'failed' ) {					
					 	$('#fileNameStored').val(response);
					 	$('#uploadStatus').val(\"\");
						//alert('upload done');	
						 $('#msg_filename').text(\"Upload done : \"+fileObj.name);
					} else {
						alert('upload failed');
					}
				},
				'onSelectOnce' : function(event,data) {
					$('#uploadStatus').val(\"started\");	
				}	

				});
		"));
		 * 
		 */

		/*
		$this->addJavascriptResource(new mvcViewJavascript('plupload', mvcViewJavascript::TYPE_FILE, '/libraries/plupload/js/plupload.js'));
		$this->addJavascriptResource(new mvcViewJavascript('pluploadfull', mvcViewJavascript::TYPE_FILE, '/libraries/plupload/js/plupload.full.js'));
		$this->addJavascriptResource(new mvcViewJavascript('pluploadflash', mvcViewJavascript::TYPE_FILE, '/libraries/plupload/js/plupload.flash.js'));
		$this->addJavascriptResource(new mvcViewJavascript('pluploadhtml4', mvcViewJavascript::TYPE_FILE, '/libraries/plupload/js/plupload.html4.js'));
		$this->addJavascriptResource(new mvcViewJavascript('pluploadhtml5', mvcViewJavascript::TYPE_FILE, '/libraries/plupload/js/plupload.html5.js'));
		$this->addJavascriptResource(new mvcViewJavascript('pluploadjs', mvcViewJavascript::TYPE_FILE, '/libraries/plupload/js/jquery.ui.plupload/jquery.ui.plupload.js'));
		$this->addJavascriptResource(new mvcViewJavascript('progressBar', mvcViewJavascript::TYPE_FILE, '/libraries/jquery-plugins/jquery.progressbar.min.js'));
		$this->addJavascriptResource(new mvcViewJavascript('fileStyle', mvcViewJavascript::TYPE_FILE, '/libraries/jquery-plugins/fileStyle/jquery.filestyle.mini.js'));
		$this->addJavascriptResource(new mvcViewJavascript('html5Uploader', mvcViewJavascript::TYPE_FILE, '/libraries/mofilm/customHTML5Uploader.js'));
		*/

		$this->getEngine()->assign('newGenres', utilityOutputWrapper::wrap(mofilmTag::listOfObjects(null, null, mofilmTag::TYPE_GENRE)));

		$this->addJavascriptResource(new mvcViewJavascript('smartwizard', mvcViewJavascript::TYPE_FILE, '/libraries/jquery-plugins/smartWizard2/js/jquery.smartWizard-2.0.min.js'));
		$this->addJavascriptResource(new mvcViewJavascript('creditAutomcomplete', mvcViewJavascript::TYPE_FILE, '/libraries/mofilm/creditAutocomplete.js?'.mofilmConstants::JS_VERSION));
		$this->getEngine()->assign('eventsall', utilityOutputWrapper::wrap(mofilmEvent::listOfObjects(null, null, true)));
		$this->getEngine()->assign('oUser', $this->getController()->getRequest()->getSession()->getUser());
		$list = mofilmRole::listOfObjects();
		$tmp = array();
		foreach ( $list as $oObject ) {
			//$tmp[] = $oObject->getDescription();
			$tmp[] = array("label" => $oObject->getDescription(),"value" => $oObject->getDescription(),"key" => $oObject->getID());

		}
		$this->getEngine()->assign('availableRoles', json_encode($tmp));
		$this->getEngine()->assign('index', 0);
		$this->addJavascriptResource(new mvcViewJavascript('jqueryautocompletehtml', mvcViewJavascript::TYPE_FILE, '/libraries/jqueryautocomplete/jquery.ui.autocomplete.html.js'));
		$this->render($this->getTpl('upload', '/account'));
	}

	/**
	 * Displays a response for ajax requests
	 *
	 * @return void
	 */
	function showUploadResponse() {
		$this->setCacheLevelNone();
		$response = json_encode(
			array(
				'status' => $this->getModel()->isUpdated() === 0 ? 'info' : ($this->getModel()->isUpdated() ? 'success' : 'error'),
				'message' => $this->getModel()->getMessage(),
			)
		);
		echo $response;
	}
	
	/**
	 * Displays the upload page when there is no Javascript
	 * 
	 * @return void
	 */
	function showUploadNoJSPage() {
		
		$this->render($this->getTpl('uploadNojs', '/account'));
	}
	
	/**
	 * Gets the sources correspoding to the event in xml
	 * 
	 * @return void
	 */
	function showGetEventSources() {
		$this->getEngine()->assign('oModel', utilityOutputWrapper::wrap($this->getModel()));
		$this->render($this->getTpl('sourceList', '/account'));
	}
	
	/**
	 * 
	 */
	function showAutocompleteSearchTag() {
		$this->setCacheLevelNone();
		$oResult = $this->getModel()->autocompleteSearchTag();
		$oResponse = json_encode($oResult);
		echo $oResponse;
	}
	
	function showPlUpload() {		
		
		$this->getEngine()->assign('eventsall', utilityOutputWrapper::wrap(mofilmEvent::listOfObjects(null, null, true)));
		$this->addCssResource(new mvcViewCss('uploadifycss', mvcViewCss::TYPE_FILE, '/libraries/plupload/js1/jquery.ui.plupload/css/jquery.ui.plupload.css'));
		$this->addJavascriptResource(new mvcViewJavascript('plupload', mvcViewJavascript::TYPE_FILE, '/libraries/plupload/js1/plupload.js'));		
		$this->addJavascriptResource(new mvcViewJavascript('pluploadflash', mvcViewJavascript::TYPE_FILE, '/libraries/plupload/js1/plupload.flash.js'));		
		$this->addJavascriptResource(new mvcViewJavascript('plupload4', mvcViewJavascript::TYPE_FILE, '/libraries/plupload/js1/plupload.html4.js'));				
		$this->addJavascriptResource(new mvcViewJavascript('plupload5', mvcViewJavascript::TYPE_FILE, '/libraries/plupload/js1/plupload.html5.js'));				
		$this->addJavascriptResource(new mvcViewJavascript('pljquery', mvcViewJavascript::TYPE_FILE, '/libraries/plupload/js1/jquery.ui.plupload/jquery.ui.plupload.js'));				
		$this->addJavascriptResource(new mvcViewJavascript('mofilmupload', mvcViewJavascript::TYPE_FILE, '/libraries/mofilm/mofilmupload.js?'.mofilmConstants::JS_VERSION));
		$this->render($this->getTpl('plupload', '/account'));
	}
	
	/**
	 * Displays the photo upload page
	 * 
	 * @return void
	 */
	function showUploadPhoto() {
		$this->setCacheLevelNone();
		$event = $this->getModel()->getEvent();
		$source = $this->getModel()->getSource();
		
		if ( $event->getID() == $source->getEventID() ) {
			$this->getEngine()->assign('event', utilityOutputWrapper::wrap($event));
			$this->getEngine()->assign('source', utilityOutputWrapper::wrap($source));

			$this->addJavascriptResource(new mvcViewJavascript('multifile', mvcViewJavascript::TYPE_FILE, '/libraries/mofilm/jquery.MultiFile.js'));
			$this->addJavascriptResource(new mvcViewJavascript('photoupload', mvcViewJavascript::TYPE_FILE, '/libraries/mofilm/mofilmphotoupload.js'));
			$this->addJavascriptResource(new mvcViewJavascript('blockui', mvcViewJavascript::TYPE_FILE, '/libraries/mofilm/jquery.blockUI.js'));
			$this->getEngine()->assign('error', FALSE);
		} else {
			$this->getEngine()->assign('error', TRUE);
		}
		
		$this->render($this->getTpl('uploadPhoto', '/account'));
	}
}
