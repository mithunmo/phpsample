<?php
/**
 * profileView.class.php
 * 
 * profileView class
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_mofilm.com
 * @subpackage controllers
 * @category profileView
 * @version $Rev: 321 $
 */


/**
 * profileView class
 * 
 * Provides the "profileView" page
 * 
 * @package websites_mofilm.com
 * @subpackage controllers
 * @category profileView
 */
class profileView extends mvcView {

	/**
	 * Assigns some default values to template engine that are always needed
	 *
	 * @return void
	 */
	function setupInitialVars() {
		parent::setupInitialVars();
		
		$this->getEngine()->assign('profileUri', $this->getController()->buildUriPath(profileController::ACTION_PROFILE));
		$this->getEngine()->assign('doProfileUpdateUri', $this->getController()->buildUriPath(profileController::ACTION_UPDATE_PROFILE));
	}
	
	/**
	 * Displays the users profile
	 *
	 * @return void
	 */
	function showProfilePage() {
		$this->setCacheLevelNone();		
		
		$this->addJavascriptResource(new mvcViewJavascript('textcount', mvcViewJavascript::TYPE_FILE, '/libraries/jquery-plugins/jquery.textareaCount.js'));
		$this->addJavascriptResource(new mvcViewJavascript('jeditable', mvcViewJavascript::TYPE_FILE, '/libraries/jquery-plugins/jquery.jeditable.min.js'));
		$this->addJavascriptResource(new mvcViewJavascript('bindDelay', mvcViewJavascript::TYPE_FILE, '/libraries/jquery-plugins/jquery.bindwithdelay.js'));
		$this->addJavascriptResource(new mvcViewJavascript('swfobject', mvcViewJavascript::TYPE_FILE, '/libraries/swfobject/swfobject.js'));
		$this->addJavascriptResource(new mvcViewJavascript('uploadify', mvcViewJavascript::TYPE_FILE, '/libraries/jquery-uploadify/jquery.uploadify.min.js'));
		$this->addJavascriptResource(new mvcViewJavascript('uploadifyInit', mvcViewJavascript::TYPE_INLINE, "$('#FileUpload').uploadify({
				'uploader'  : '/libraries/jquery-uploadify/uploadify.swf',
				'script'    : '{$this->buildUriPath(profileController::ACTION_DO_UPLOAD, $this->getEngine()->getTemplateVar('oUser')->getID())}',
				'cancelImg' : '/libraries/jquery-uploadify/cancel.png',
				'auto'      : true,
				'fileDataName': 'Files',
				'fileExt'   : '*.jpg;*.gif;*.png',
				'fileDesc'  : 'Image Files',
				'queueSizeLimit' : 1,
				'sizeLimit' : 1200000,
				'scriptData': {
					'{$this->getRequest()->getSession()->getSessionName()}': '{$this->getRequest()->getSession()->getSessionID()}',
					'ajax': true
				},
				'onComplete'  : function(event, ID, fileObj, response, data) {
					if ( response != 'failed' ) {
						if ( $('img.profileImage').length > 0 ) {
							d = new Date();
							$('img.profileImage').attr('src', response+'?'+d.getTime()).slideDown(200);
							$('#upload').hide();
						} else {
							$('#upload').hide();
							$('#profileImageContainer').append('<img src=\"'+response+'\" width=\"150\" height=\"150\" border=\"0\" alt=\"Profile Image\" class=\"profileImage\" />');
							$('img.profileImage').click(function(event){
								$(this).slideUp(200, function() {
									$('#upload').show();
								});
							});
						}
					} else {
						alert('Your file upload failed. Please try again.');
					}
				}
			});
		"));
		$this->addJavascriptResource(
			new mvcViewJavascript(
				'textcountInit', mvcViewResource::TYPE_INLINE, "if ( $('#ProfileText').length > 0 ) {
					$('#ProfileText').textareaCount({
						maxCharacterSize: ".mofilmConstants::PROFILE_TEXT_LENGTH.",
						originalStyle: 'textareaCountInfo',
						warningStyle: 'warning',
						warningNumber: 50,
						'displayFormat': '#input Characters | #left Characters Left'
					});
				}"
			)
		);

		$this->addJavascriptResource(new mvcViewJavascript('fbconnect', mvcViewJavascript::TYPE_FILE, 'http://connect.facebook.net/en_US/all.js'));
		$this->addJavascriptResource(new mvcViewJavascript('facebookInit', mvcViewJavascript::TYPE_FILE, '/libraries/mofilm/facebookScripts.js'));
		$this->addJavascriptResource(
			new mvcViewJavascript(
				'facebookScript', mvcViewJavascript::TYPE_INLINE, "
					FB.getLoginStatus(function(response) {
						if (response.status === 'connected') {
							$('#fb_link_display').show();
							$('#fb_button_display').html('');
						} else {
							$('#fb_link_display').html('');
							$('#fb_button_display').show();
							
							FB.Event.subscribe('auth.login', function(response ) {
								if ( response.status == 'connected' ) {
									enableFBLogin(response.authResponse.userID);
								}
							});
						}
					});
					

					if ( $('.enableFBLoginLink').length > 0 ) { 
						$('.enableFBLoginLink').click(function(){
							FB.getLoginStatus(function(response) {
								if (response.status === 'connected') {
									enableFBLogin(response.authResponse.userID);
								}
							});	
						});
					}
					
					function enableFBLogin(fbID) {
						$.post (
							'/account/profile/doFBUpdate',
							{
								facebookID: fbID
							},
							function(data, testStatus, XMLHttpRequest) {
								$('#fbStatus').text('Enabled');
								$('#showFbButton').hide();
								$('#disableFBLogin').attr('checked', false);
								$('#showDisableFBLogin').show();
							},
							'json'
						);
					
					}"
			)
		);
		
		$this->addJavascriptResource(
			new mvcViewJavascript(
				'disableFBLogin', mvcViewJavascript::TYPE_INLINE, "
				    $('#disableFBLogin').change(function() {
					$.post (
					    '/account/profile/doFBUpdate',
					    {
						    facebookID: ''
					    },
					    function(data, testStatus, XMLHttpRequest) {
						    $('#fbStatus').text('Disabled');
						    $('#showDisableFBLogin').hide();
						    $('#showFbButton').show();
					    },
					    'json'
					);
				    });
				    "
			)
		);

		$tags = array();
		$counties = mofilmTerritoryState::listOfObjects();
		foreach ( $counties as $oCounty ) {
			$tags[] = $oCounty->getDescription();
		}
		
		$this->addJavascriptResource(new mvcViewJavascript('countyAutoComplete', mvcViewJavascript::TYPE_INLINE, '
			var availableTags = [
				"'.implode('", "', $tags).'"
			];
		'));
		
		$this->getEngine()->assign('oModel', utilityOutputWrapper::wrap($this->getModel()));
		$this->getEngine()->assign('countries', utilityOutputWrapper::wrap(mofilmTerritory::listOfObjects()));
		$this->getEngine()->assign('roles', mofilmRole::listOfObjects());
		
		$path = mofilmConstants::getWebFolder()."libraries/lang/";
		if ( $this->getRequest()->getDistributor()->getSiteConfig()->getI18nDefaultLanguage()->getParamValue() == "zh" ) {
			$translate = new mofilmTranslator('zh',$path);
		} else {
			$translate = new mofilmTranslator('en',$path);
		}
		$this->getEngine()->assign('trs', $translate);
		$this->render($this->getTpl('profile', '/account'));
	}

	/**
	 * Displays a response for ajax requests
	 *
	 * @return void
	 */
	function showProfileUpdateResponse() {
		$this->setCacheLevelNone();
		$response = json_encode(
			array(
				'status' => $this->getModel()->isUpdated() === 0 ? 'info' : ($this->getModel()->isUpdated() ? 'success' : 'error'),
				'message' => $this->getModel()->getMessage(),
			)
		);
		echo $response;
	}
}