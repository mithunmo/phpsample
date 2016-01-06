<?php
/**
 * accountView.class.php
 * 
 * accountView class
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_mofilm.com
 * @subpackage controllers
 * @category accountView
 * @version $Rev: 345 $
 */


/**
 * accountView class
 * 
 * Provides the "accountView" page
 * 
 * @package websites_mofilm.com
 * @subpackage controllers
 * @category accountView
 */
class accountView extends mvcView {

	/**
	 * Assigns some default values to template engine that are always needed
	 *
	 * @return void
	 */
	function setupInitialVars() {
		parent::setupInitialVars();

		$this->getEngine()->assign('doLoginUri', $this->getController()->buildUriPath(accountController::ACTION_DO_LOGIN));
		$this->getEngine()->assign('doLogoutUri', $this->getController()->buildUriPath(accountController::ACTION_DO_LOGOUT));
		$this->getEngine()->assign('doForgotPasswordUri', $this->getController()->buildUriPath(accountController::ACTION_DO_FORGOT_PASSWORD));
		$this->getEngine()->assign('forgotPasswordUri', $this->getController()->buildUriPath(accountController::ACTION_FORGOT_PASSWORD));
		$this->getEngine()->assign('forgotcnPasswordUri', $this->getController()->buildUriPath(accountController::ACTION_FORGOT_PASSWORDCN));
		$this->getEngine()->assign('loginUri', $this->getController()->buildUriPath(accountController::ACTION_LOGIN));
		$this->getEngine()->assign('logincnUri', $this->getController()->buildUriPath(accountController::ACTION_LOGINCN));
		$this->getEngine()->assign('logoutUri', $this->getController()->buildUriPath(accountController::ACTION_LOGOUT));
		$this->getEngine()->assign('redirect', $this->getModel()->getRedirect());
		
		$this->getEngine()->assign('profileUri', $this->getController()->buildUriPath(profileController::ACTION_PROFILE));
		$this->getEngine()->assign('doProfileUpdateUri', $this->getController()->buildUriPath(profileController::ACTION_UPDATE_PROFILE));
                if ( $this->getModel()->getRedirect() == "/" || $this->getModel()->getRedirect() == "/account/login"){
                    $this->getEngine()->assign('registerUri', $this->getController()->buildUriPath(accountController::ACTION_REGISTER));
                } else {
                    $this->getEngine()->assign('registerUri', $this->getController()->buildUriPath(accountController::ACTION_REGISTER."?redirect=".$this->getModel()->getRedirect()));
                }
                //$this->getEngine()->assign('registerUri', $this->getController()->buildUriPath(accountController::ACTION_REGISTER));
		$this->getEngine()->assign('registercnUri', $this->getController()->buildUriPath(accountController::ACTION_REGISTERCN));
		$this->getEngine()->assign('doRegisterUri', $this->getController()->buildUriPath(accountController::ACTION_DO_REGISTER));
		$this->getEngine()->assign('docnRegisterUri', $this->getController()->buildUriPath(accountController::ACTION_DO_REGISTERCN));
		$this->getEngine()->assign('activationUri', $this->getController()->buildUriPath(accountController::ACTION_ACTIVATION));
		$this->getEngine()->assign('activationcnUri', $this->getController()->buildUriPath(accountController::ACTION_ACTIVATIONCN));
		$this->getEngine()->assign('doActivationUri', $this->getController()->buildUriPath(accountController::ACTION_ACTIVATION));
	}
	/**
	 * Shows the mofilm referral page
	 */
	function showReferralPage(){
		$this->getEngine()->assign('doReferralUri', $this->getController()->buildUriPath(accountController::ACTION_DO_REFERRAL));
		$this->render($this->getTpl('referral'));
	}
	
	function showReferralThankYou(){
		$this->render($this->getTpl('thankyou'));		
	}
	
	function showReferralTerms(){
		$this->render($this->getTpl('terms'));				
	}
	/**
	 * Shows the accountView page
	 *
	 * @return void
	 */
	function showLoginPage() {
		$this->setCacheLevelNone();
		
		$this->getEngine()->assign('formSessionKey', $this->getRequest()->getSession()->getFormToken());
		
		$this->addJavascriptResource(new mvcViewJavascript('fbconnect', mvcViewJavascript::TYPE_FILE, 'https://connect.facebook.net/en_US/all.js'));
		$this->addJavascriptResource(new mvcViewJavascript('facebookScripts', mvcViewJavascript::TYPE_FILE, '/libraries/mofilm/facebookScripts.js'));
		$this->addJavascriptResource(
			new mvcViewJavascript(
				'fbInlineScript', mvcViewJavascript::TYPE_INLINE, "
					FB.getLoginStatus(function(response) {
						if (response.status === 'connected') {
							$('#fb_link_display').show();
							$('#facebookId').val(response.authResponse.userID);
							$('#accessToken').val(response.authResponse.accessToken);
							$('#fb_button_display').hide();
						} else {
							$('#fb_link_display').hide();
							$('#fb_button_display').show();
							
							FB.Event.subscribe('auth.login', function(response ) {
								if ( response.status == 'connected' ) {
									$('#facebookId').val(response.authResponse.userID);
									$('#accessToken').val(response.authResponse.accessToken);
									$('#loginForm').submit();
								}
							});
						
						}
					});
					
				if ( $('.loginFbLink').length > 0 ) { 
					$('.loginFbLink').click(function(){
						$('#loginForm').submit();
					});
				}"
			)
		);
		
		$this->render($this->getTpl('login'));
	}


	/**
	 * Shows the accountView page
	 *
	 * @return void
	 */
	function showLogincnPage() {
		$this->setCacheLevelNone();
		
		$this->getEngine()->assign('formSessionKey', $this->getRequest()->getSession()->getFormToken());
		$this->render($this->getTpl('logincn'));
	}

	
	
	/**
	 * Displays the logged in / authorised page
	 */
	function showLoggedInPage() {
		$this->setCacheLevelNone();
		$this->render($this->getTpl('loggedIn'));
	}

	/**
	 * Displays the logout confirmation page
	 *
	 * @return void
	 */
	function showLogoutPage() {
		$this->setCacheLevelNone();

		$oSession = $this->getRequest()->getSession();
		$this->render($this->getTpl('logout'));
	}

	/**
	 * Displays the logged out page
	 *
	 * @return void
	 */
	function showLoggedOutPage() {
		$this->setCacheLevelNone();
		
		$this->getEngine()->assign('metaRedirect', system::getConfig()->getParam('mofilm', 'wwwMofilmUri', 'www.mofilm.com')->getParamValue());
		$this->getEngine()->assign('metaTimeout', 1);
		$this->render($this->getTpl('loggedOut'));
	}

	/**
	 * Displays the not authorised page
	 *
	 * @return void
	 */
	function showNotAuthorisedPage() {
		$this->setCacheLevelNone();
		$this->render($this->getTpl('notAuthorised'));
	}

	/**
	 * Displays the lost password page
	 *
	 * @return void
	 */
	function showLostPasswordPage() {
		$this->setCacheLevelNone();
		
		if ( $this->getController()->getAction() == "forgotpw" ) {
			$this->render($this->getTpl('lostPassword'));
		} else {
			$this->render($this->getTpl('lostcnPassword'));
		}
	}
	
	/**
	 * Displays the registration page
	 *
	 * @return void
	 */
	function showRegisterPage() {
		$this->setCacheLevelNone();
		$this->addJavascriptResource(new mvcViewJavascript('bindDelay', mvcViewJavascript::TYPE_FILE, '/libraries/jquery-plugins/jquery.bindwithdelay.js'));
		$this->addJavascriptResource(new mvcViewJavascript('fbconnect', mvcViewJavascript::TYPE_FILE, 'http://connect.facebook.net/en_US/all.js'));
		$this->addJavascriptResource(new mvcViewJavascript('facebookScripts', mvcViewJavascript::TYPE_FILE, '/libraries/mofilm/facebookScripts.js'));
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
								FB.api('/me', function(response ) {
									$('#fb_button_display').hide();
									populateData(response);
								});
							});
					
						}
					});
										
					if ( $('.registerFbLink').length > 0 ) { 
						$('.registerFbLink').click(function(){
							FB.getLoginStatus(function(response) {
								if (response.status === 'connected') {
									FB.api('/me', function(response ) {
										$('#fb_link_display').hide();
										populateData(response);
									});
								}
							});
						});
					}
	
					function populateData(response) {
						
						if ( response.id ) {
							$('#regSource').val('facebook');
							$('#facebookID').val(response.id);
							$('#facebooklink-show').hide();
						}
							
						if ( response.birthday ) {
							dob = response.birthday.split('/');
							$('#dobID:nth-child(1)').val(dob[0]);
							$('#dobID:nth-child(2)').val(dob[1]);
							$('#dobID:nth-child(3)').val(dob[2]);
						}
							
						

						if ( response.email ) {
							$('#emailAddress').val(response.email);
						}

						if ( response.first_name ) {
							$('#firstName').val(response.first_name);
						}
						
						if ( response.last_name ) {
							$('#surName').val(response.last_name); 
						}
						
						if ( response.location.name ) {
							txt1 = '<dt>City</dt>';
							txt1 += '<dd><input id=\"city\" name=\"City\" type=\"text\" value=\"'+response.location.name+'\" /></dd>';
							$('#cityField').html(txt1);
						}
					}"
			)
		);
		
		$this->getEngine()->assign('oCountry', utilityOutputWrapper::wrap($this->getModel()->getGeoLocatedCountry()));
		
		$this->getEngine()->assign('roles', mofilmRole::listOfObjects());
		$this->getEngine()->assign('oSignUpCodes', mofilmUserSignupCode::listOfObjects(NULL, 30, TRUE));
		$path = mofilmConstants::getWebFolder()."libraries/lang/";
		if ( $this->getRequest()->getDistributor()->getSiteConfig()->getI18nDefaultLanguage()->getParamValue() == "zh" ) {
			$translate = new mofilmTranslator('zh',$path);
		} else {
			$translate = new mofilmTranslator('en',$path);
		}
                $this->getEngine()->assign('affiliate', $_GET['affiliate']); 
		$this->getEngine()->assign('trs', $translate);

		$this->render($this->getTpl('register'));
	}

	/**
	 * Displays the registration page for event based regis
	 *
	 * @return void
	 */
	function showEventRegistercnPage($inEventID) {
		$this->setCacheLevelNone();
		
		$this->addJavascriptResource(new mvcViewJavascript('bindDelay', mvcViewJavascript::TYPE_FILE, '/libraries/jquery-plugins/jquery.bindwithdelay.js'));
		
		$this->getEngine()->assign('oImagePath', "/resources/admin/events/".$inEventID.".jpg");
		$this->getEngine()->assign('oSmallImagePath', "/resources/client/events/");
		$this->getEngine()->assign('oCountry', utilityOutputWrapper::wrap($this->getModel()->getGeoLocatedCountry()));
		$this->getEngine()->assign('oCampaignID', $inEventID);
		$this->getEngine()->assign('oText', mofilmEvent::getInstance($inEventID)->getInstructions());
		$this->getEngine()->assign('docnEventRegisterUri', $this->getController()->buildUriPath(accountController::ACTION_DO_EVENTREGISTERCN));
		$this->getEngine()->assign('oEvents', mofilmEvent::listOfObjects(0, 30 , true));
		$this->render($this->getTpl('eventRegistercn'));
	}

	
	/**
	 * Displays the registration page
	 *
	 * @return void
	 */
	function showRegistercnPage() {
		$this->setCacheLevelNone();
		
		$this->addJavascriptResource(new mvcViewJavascript('bindDelay', mvcViewJavascript::TYPE_FILE, '/libraries/jquery-plugins/jquery.bindwithdelay.js'));
		$this->getEngine()->assign('oCountry', utilityOutputWrapper::wrap($this->getModel()->getGeoLocatedCountry()));
		$this->render($this->getTpl('registercn'));
	}
	
	
	
	
	/**
	 * Displays the registration page
	 *
	 * @return void
	 */
	function showActivationPage() {
		$this->setCacheLevelNone();
		if ( $this->getController()->getAction() == "activation" ) {
			$this->render($this->getTpl('activation'));
		} else {
			$this->render($this->getTpl('activationcn'));
		}
	}
	
	/**
	 * Displays the welcome page
	 * 
	 * @return void
	 */
	function showWelcomePage() {
		$this->setCacheLevelNone();
		
		$this->getEngine()->assign('oUser', utilityOutputWrapper::wrap($this->getModel()->getUser()));
		
		$this->render($this->getTpl('welcome'));
	}

	
	/**
	 * Displays the welcome page
	 * 
	 * @return void
	 */
	function showWelcomecnPage() {
		$this->setCacheLevelNone();
		
		$this->getEngine()->assign('oUser', utilityOutputWrapper::wrap($this->getModel()->getUser()));
		$this->getEngine()->assign('oSmallImagePath', "/resources/client/events/");
		$this->getEngine()->assign('oEvents', mofilmEvent::listOfObjects(0, 30 , true));
		$this->addJavascriptResource(
			new mvcViewJavascript(
				'facebookScript', mvcViewJavascript::TYPE_INLINE, "
				//Miaozhen Base Code Start
		  var _mzh=_mzh || [],_mzt=_mzt || [],_mz_dp=_mz_dp || [];
		  _mzh.push(
			['evt._x_utm'], ['evt._x_lid'], ['imp._x_lid'], ['clk._x_lid'],
			['evt._urlpre', 'http://msg.cn.miaozhen.com/e.gif'],
			['imp._urlpre', 'http://g.cn.miaozhen.com/x.gif'],
			['clk._urlpre', 'http://e.cn.miaozhen.com/r.gif']
		  );
		  (function(){
			var mz=document.createElement('script');
			mz.type='text/javascript';mz.async=true;mz.src='http://js.miaozhen.com/t.js';
			var t=document.getElementsByTagName('script')[0];
			t.parentNode.insertBefore(mz,t);
		  })();
		  function _mz_evt(ae,n) {_mzh.push(['evt._set_ae', ae], ['evt._set_n', n], ['evt._send']);}
		  function _mz_imp(k,p) {_mzh.push(['imp._set_k', k], ['imp._set_p', p],['imp._send']);}
		  function _mz_clk(k,p) {_mzh.push(['clk._set_k', k], ['clk._set_p', p],['clk._send']);}
		//Miaozhen Base Code End 
		
		_mz_evt('1000224', '100002916');_mz_imp('1001482','3xe7L0');

		"));
		
		

		
		$this->render($this->getTpl('welcomecn'));
	}
	
	
	/**
	 * Displays the welcome page
	 * 
	 * @return void
	 */
	function showRegistrationDonePage() {
		$this->setCacheLevelNone();
		
		$this->getEngine()->assign('email', utilityOutputWrapper::wrap($this->getRequest()->getSession()->getParam('email')));
		
		if ( $this->getController()->getAction() == "registerDone" ) {
			
			if ( $this->getRequest()->getDistributor()->getSiteConfig()->getI18nDefaultLanguage()->getParamValue() == "zh") {
				
			$this->addJavascriptResource(
				new mvcViewJavascript(
					'chinaevent', mvcViewJavascript::TYPE_INLINE, "
					(function() {
					   var c = document.createElement('script'); 
					   c.type = 'text/javascript';
					   c.async = true;
					   c.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'sitemonitor.cn.miaozhen.com/boot/45269';
					   var h = document.getElementsByTagName('script')[0];
					   h.parentNode.insertBefore(c, h);
					})();						
					"));
					$this->render($this->getTpl('registrationDone'));
			} else {
			
				$this->render($this->getTpl('registrationDone'));
			}	
			
		} else {
			
			$this->addJavascriptResource(
				new mvcViewJavascript(
					'facebookScript', mvcViewJavascript::TYPE_INLINE, "
					//Miaozhen Base Code Start
			  var _mzh=_mzh || [],_mzt=_mzt || [],_mz_dp=_mz_dp || [];
			  _mzh.push(
				['evt._x_utm'], ['evt._x_lid'], ['imp._x_lid'], ['clk._x_lid'],
				['evt._urlpre', 'http://msg.cn.miaozhen.com/e.gif'],
				['imp._urlpre', 'http://g.cn.miaozhen.com/x.gif'],
				['clk._urlpre', 'http://e.cn.miaozhen.com/r.gif']
			  );
			  (function(){
				var mz=document.createElement('script');
				mz.type='text/javascript';mz.async=true;mz.src='http://js.miaozhen.com/t.js';
				var t=document.getElementsByTagName('script')[0];
				t.parentNode.insertBefore(mz,t);
			  })();
			  function _mz_evt(ae,n) {_mzh.push(['evt._set_ae', ae], ['evt._set_n', n], ['evt._send']);}
			  function _mz_imp(k,p) {_mzh.push(['imp._set_k', k], ['imp._set_p', p],['imp._send']);}
			  function _mz_clk(k,p) {_mzh.push(['clk._set_k', k], ['clk._set_p', p],['clk._send']);}
			//Miaozhen Base Code End 


			 _mz_evt('1000224', '100002915');_mz_imp('1001482','3xe7K0');


			"));

			
			$this->render($this->getTpl('registrationcnDone'));
		}
	}
}