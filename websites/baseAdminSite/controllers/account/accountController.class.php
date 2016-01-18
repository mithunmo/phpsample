<?php
/**
 * accountController
 *
 * Stored in accountController.class.php
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category accountController
 * @version $Rev: 105 $
 */


/**
 * accountController
 *
 * accountController class
 *
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category accountController
 */
class accountController extends mvcController {

	const ACTION_AUTHORISE = 'authorise';
	const ACTION_NOT_AUTHORISED = 'notAuthorised';
	const ACTION_AUTHORISED = 'authorised';

	const ACTION_LOGIN = 'login';
	const ACTION_LOGOUT = 'logout';
	const ACTION_LOGGED_IN = 'loggedIn';
	const ACTION_DO_LOGIN = 'doLogin';
	const ACTION_DO_LOGOUT = 'doLogout';

	const ACTION_FORGOT_PASSWORD = 'forgotpw';
	const ACTION_DO_FORGOT_PASSWORD = 'doForgotpw';
	const ACTION_PROFILE = 'profile';
	const ACTION_UPDATE_PROFILE = 'profileUpdate';
	const ACTION_ADD_TO_FAVOURITES = 'addToFavourites';
	const ACTION_REMOVE_FROM_FAVOURITES = 'removeFromFavourites';
	const ACTION_MARK_MOTD = 'motd';
	const ACTION_BOOKMARK_EVENT = 'bookmarkEvent';
	const ACTION_BOOKMARK_REMOVE = 'bookmarkRemove';



	/**
	 * @see mvcControllerBase::initialise()
	 */
	function initialise() {
		parent::initialise();

		$this->setDefaultAction(self::ACTION_LOGIN);
		$this->setRequiresAuthentication(false);
		$this
			->getControllerActions()
			->addAction(self::ACTION_AUTHORISE)
			->addAction(self::ACTION_LOGIN)
			->addAction(self::ACTION_DO_FORGOT_PASSWORD)
			->addAction(self::ACTION_DO_LOGIN)
			->addAction(self::ACTION_DO_LOGOUT)
			->addAction(self::ACTION_FORGOT_PASSWORD)
			->addAction(self::ACTION_LOGOUT)
			->addAction(self::ACTION_PROFILE)
			->addAction(self::ACTION_UPDATE_PROFILE)
			->addAction(self::ACTION_ADD_TO_FAVOURITES)
			->addAction(self::ACTION_REMOVE_FROM_FAVOURITES)
			->addAction(self::ACTION_MARK_MOTD)
			->addAction(self::ACTION_BOOKMARK_EVENT)
			->addAction(self::ACTION_BOOKMARK_REMOVE);

		$this->addInputFilters();

		$authRequired = array(
			self::ACTION_PROFILE, self::ACTION_UPDATE_PROFILE, self::ACTION_ADD_TO_FAVOURITES,
			self::ACTION_REMOVE_FROM_FAVOURITES, self::ACTION_MARK_MOTD, self::ACTION_BOOKMARK_EVENT,
			self::ACTION_BOOKMARK_REMOVE,
		);
		if ( in_array($this->getAction(), $authRequired) ) {
			$this->setRequiresAuthentication(true);
		}
	}

	/**
	 * @see mvcControllerBase::launch()
	 */
	function launch() {
		switch ( $this->getAction() ) {
			case self::ACTION_AUTHORISE:
				$this->authoriseAction();
				break;
			case self::ACTION_NOT_AUTHORISED:
				$this->notAuthorisedAction();
				break;
			case self::ACTION_DO_LOGIN:
				$this->doLoginAction();
				break;
			case self::ACTION_LOGOUT:
				$this->logoutAction();
				break;
			case self::ACTION_DO_LOGOUT:
				$this->doLogoutAction();
				break;
			case self::ACTION_FORGOT_PASSWORD:
				$this->forgotPasswordAction();
				break;
			case self::ACTION_DO_FORGOT_PASSWORD:
				$this->doForgotPasswordAction();
				break;

			case self::ACTION_PROFILE:
				$this->profileUpdateAction();
				break;
			case self::ACTION_UPDATE_PROFILE:
				$this->doProfileUpdateAction();
				break;

			case self::ACTION_ADD_TO_FAVOURITES:
				$this->addToFavouritesAction();
				break;
			case self::ACTION_REMOVE_FROM_FAVOURITES:
				$this->removeFromFavouritesAction();
				break;
			case self::ACTION_MARK_MOTD:
				$this->markMotdAction();
				break;

			case self::ACTION_BOOKMARK_EVENT:
				$this->bookmarkEventAction();
				break;
			case self::ACTION_BOOKMARK_REMOVE:
				$this->bookmarkRemoveAction();
				break;

			case self::ACTION_LOGGED_IN:
			case self::ACTION_AUTHORISED:
				$this->loggedInAction();
				break;

			default:
				$this->loginAction();
				break;
		}
	}

	/**
	 * Handles the authorise action
	 *
	 * @return void
	 */
	protected function authoriseAction() {
		$oSession = $this->getRequest()->getSession();

		if ( !$oSession->isLoggedIn() ) {
			$this->setAction(self::ACTION_LOGIN);
			$this->getModel()->setMessage("You must login before you can view the resource");

			$oSession->setStatusMessage('You must login before you can view the resource', mvcSession::MESSAGE_ERROR);
			$this->loginAction();
		} else {
			$oLog = new mofilmUserLog();
			$oLog->setType(mofilmUserLog::TYPE_OTHER);
			$oLog->setDescription('User attempted to access restricted resource: ' . $this->getRequest()->getRequestUri());

			$oSession->getUser()->getLogSet()->setObject($oLog);
			$oSession->getUser()->save();

			$this->getModel()->setMessage("Sorry, but you do not have permission to access the resource");
			$oSession->setStatusMessage('Sorry, but you do not have permission to access the resource', mvcSession::MESSAGE_WARNING);

			systemLog::warning(
				$oSession->getUser()->getUsername() . ' attempted to access a resource (' .
					$this->getRequest()->getRequestUri() . ') they do not have permissions for'
			);

			$this->setAction(self::ACTION_NOT_AUTHORISED);
			$this->notAuthorisedAction();
		}
	}

	/**
	 * Shows the not authorised page
	 *
	 * @return void
	 */
	protected function notAuthorisedAction() {
		$oView = new accountView($this);
		$oView->showNotAuthorisedPage();
	}

	/**
	 * Handles actually authenticating a user
	 *
	 * @return void
	 */
	protected function doLoginAction() {
		$this->setAction(self::ACTION_LOGIN);
		$oSession = $this->getRequest()->getSession();
      

		if ( $oSession->isLoggedIn() ) {
			$this->setAction(self::ACTION_AUTHORISED);                   
			$this->loggedInAction();
		} else {
			$this->addInputToModel($this->getInputManager()->doFilter(), $this->getModel());
			if ( $this->getModel()->authenticate() ) {
                           
                            $oSession->regenerateSessionID();
                            $oSession->setUser($this->getModel()->getUser());
                            $oSession->setLoggedIn(true);
                            systemLog::notice("Successfully authenticated {$this->getModel()->getUsername()} ({$this->getModel()->getUser()->getID()})");
                            
                            $sessionUserID  = $this->getModel()->getUser()->getID();
                            $registeredDate = $this->getModel()->getUser()->getRegistered();
                            $token = md5($sessionUserID.$registeredDate);
                            $oSession->setToken($token);
                            setcookie("_AdminUserID",$this->getModel()->getUser()->getID(),time() + (6 * 60 * 60),'/','.mofilm.com');
                            $this->setAction(self::ACTION_LOGGED_IN);
                            //$oSession->setStatusMessage('Successfully logged in', mvcSession::MESSAGE_OK);

                            if ( $this->getModel()->getRedirect() ) {
                                    systemLog::notice("Attempting to redirect user to {$this->getModel()->getRedirect()}");
                                    $this->redirect($this->getModel()->getRedirect());
                            } else {
                                    $this->loggedInAction();
                            }
			} else {
				systemLog::error('Attempt to login as user ' . $this->getModel()->getUsername() . ' failed');
				$oSession->clearFormToken();
				$oSession->setStatusMessage('Sorry, your username and/or password were not valid', mvcSession::MESSAGE_ERROR);
				$this->loginAction();
			}
		}
	}

	/**
	 * Shows the logout page
	 *
	 * @return void
	 */
	protected function logoutAction() {
		$oView = new accountView($this);
		$oView->showLogoutPage();
	}

	/**
	 * Handles logging a user out from the system
	 *
	 * @return void
	 */
	protected function doLogoutAction() {
		$oSession = $this->getRequest()->getSession();
		if ( $oSession->getUser() ) {
			$oLog = new mofilmUserLog();
			$oLog->setType(mofilmUserLog::TYPE_OTHER);
			$oLog->setDescription('User logged out');

			$oSession->getUser()->getLogSet()->setObject($oLog);
			$oSession->getUser()->save();
			$oSession->setStatusMessage('Successfully Logged Out', mvcSession::MESSAGE_OK);

			systemLog::notice($oSession->getUser()->getEmail() . ' logged out');
		}
                setcookie ("_AdminUserID", "", time() - (6*60*60),'/','mofilm.com');
                unset($_COOKIE['_AdminUserID']);
		$oSession->setUser(null);
		$oSession->setLoggedIn(false);
		$oSession->regenerateSessionID();
		$oSession->destroy();
		$oSession = null;
		unset($oSession);

		$this->redirect($this->buildUriPath(self::ACTION_LOGIN));
	}

	/**
	 * Shows the forgotten password form
	 *
	 * @return void
	 */
	protected function forgotPasswordAction() {
		$oView = new accountView($this);
		$oView->showLostPasswordPage();
	}

	/**
	 * Handles sending the users password when forgotten
	 *
	 * @return void
	 */
	protected function doForgotPasswordAction() {
		$oSession = $this->getRequest()->getSession();
		$this->addInputToModel($this->getInputManager()->doFilter(), $this->getModel());
		if ( $this->getModel()->resetPassword() ) {
			$oSession->setStatusMessage('Your password has been reset and sent to your email address', mvcSession::MESSAGE_OK);
			$this->redirect($this->buildUriPath(self::ACTION_LOGIN));
		} else {
			$oSession->setStatusMessage('Sorry, but you entered an invalid or incorrect email address', mvcSession::MESSAGE_ERROR);
			$this->redirect($this->buildUriPath(self::ACTION_LOGIN));
		}
	}

	/**
	 * Shows the profile update form
	 *
	 * @return void
	 */
	protected function profileUpdateAction() {
		$oView = new accountView($this);
		$oView->showProfilePage();
	}

	/**
	 * Handles updating the users profile
	 *
	 * @return void
	 */
	protected function doProfileUpdateAction() {
		$oSession = $this->getRequest()->getSession();
		try {
			systemLog::message('Updating user profile');
			$this->addInputToModel($this->getInputManager()->doFilter(), $this->getModel());
			$this->getModel()->update();
			systemLog::message('User profile updated successfully');
		} catch ( Exception $e ) {
			systemLog::erro($e->getMessage());
			$this->getModel()->setUpdated(false);
			$this->getModel()->setMessage($e->getMessage());
		}

		if ( $this->getRequest()->isAjaxRequest() ) {
			$oView = new accountView($this);
			$oView->showProfileUpdateResponse();
			return;
		} else {
			$oSession->setStatusMessage(
				$this->getModel()->getMessage(),
				$this->getModel()->isUpdated() == 0 ? mvcSession::MESSAGE_INFO : ($this->getModel()->isUpdated() ? mvcSession::MESSAGE_OK : mvcSession::MESSAGE_ERROR)
			);
			$this->redirect($this->buildUriPath(self::ACTION_PROFILE));
		}
	}

	/**
	 * Shows the login page
	 *
	 * @return void
	 */
	protected function loginAction() {
		$oSession = $this->getRequest()->getSession();
		if ( $oSession->isLoggedIn() ) {
			$this->setAction(self::ACTION_AUTHORISED);
			$this->loggedInAction();
		} else {
			$oView = new accountView($this);
			$oView->showLoginPage();
		}
	}

	/**
	 * Shows the already logged in page
	 *
	 * @return void
	 */
	protected function loggedInAction() {
		$this->redirect(system::getConfig()->getParam('mofilm', 'platformUri')->getParamValue().'dashboard/?token='.$this->getRequest()->getSession()->getToken());		
		//$oView = new accountView($this);
		//$oView = new accountView($this);
		//$oView->showLoggedInPage();
	}

	/**
	 * Handles adding the movie to the users favourites list
	 *
	 * @return void
	 */
	protected function addToFavouritesAction() {
		$oSession = $this->getRequest()->getSession();
		try {
			$movieID = $this->getActionFromRequest(false, 1);
			if ( $movieID ) {
				$oMovie = mofilmMovieManager::getInstanceByID($movieID);
				if ( $oMovie instanceof mofilmMovie && $oMovie->getID() ) {
					$oUser = $oSession->getUser();
					if ( !$oUser->getFavourites()->isFavourite($oMovie) ) {
						$oUser->getFavourites()->addObject($oMovie);
						$oUser->save();
						$this->getModel()->setMessage('Movie added to favourites successfully');
						$this->getModel()->setUpdated(true);
					} else {
						$this->getModel()->setMessage('The movie is already in your favourites');
						$this->getModel()->setUpdated(0);
					}
				} else {
					$this->getModel()->setMessage('That was not a valid movie');
					$this->getModel()->setUpdated(false);
				}
			} else {
				$this->getModel()->setMessage('No movie was specified');
				$this->getModel()->setUpdated(false);
			}
		} catch ( Exception $e ) {
			systemLog::error($e->getMessage());
			$this->getModel()->setUpdated(false);
			$this->getModel()->setMessage($e->getMessage());
		}

		if ( $this->getRequest()->isAjaxRequest() ) {
			$oView = new accountView($this);
			$oView->showProfileUpdateResponse();
			return;
		} else {
			$oSession->setStatusMessage(
				$this->getModel()->getMessage(),
				$this->getModel()->isUpdated() == 0 ? mvcSession::MESSAGE_INFO : ($this->getModel()->isUpdated() ? mvcSession::MESSAGE_OK : mvcSession::MESSAGE_ERROR)
			);
			$this->redirect($this->buildUriPath(self::ACTION_PROFILE));
		}
	}

	/**
	 * Handles removing the movie from the users favourites list
	 *
	 * @return void
	 */
	protected function removeFromFavouritesAction() {
		$oSession = $this->getRequest()->getSession();
		try {
			$movieID = $this->getActionFromRequest(false, 1);
			if ( $movieID ) {
				$oMovie = mofilmMovieManager::getInstanceByID($movieID);
				if ( $oMovie instanceof mofilmMovie && $oMovie->getID() ) {
					$oUser = $oSession->getUser();
					if ( !$oUser->getFavourites()->isFavourite($oMovie) ) {
						$this->getModel()->setMessage('Movie is not in your favourites list');
						$this->getModel()->setUpdated(0);
					} else {
						$oTmp = $oUser->getFavourites()->getObject($oMovie);
						$oUser->getFavourites()->removeObject($oTmp);
						$oUser->save();

						$this->getModel()->setMessage('The movie has been removed from your favourites');
						$this->getModel()->setUpdated(true);
					}
				} else {
					$this->getModel()->setMessage('That was not a valid movie');
					$this->getModel()->setUpdated(false);
				}
			} else {
				$this->getModel()->setMessage('No movie was specified');
				$this->getModel()->setUpdated(false);
			}
		} catch ( Exception $e ) {
			systemLog::error($e->getMessage());
			$this->getModel()->setUpdated(false);
			$this->getModel()->setMessage($e->getMessage());
		}

		if ( $this->getRequest()->isAjaxRequest() ) {
			$oView = new accountView($this);
			$oView->showProfileUpdateResponse();
			return;
		} else {
			$oSession->setStatusMessage(
				$this->getModel()->getMessage(),
				$this->getModel()->isUpdated() == 0 ? mvcSession::MESSAGE_INFO : ($this->getModel()->isUpdated() ? mvcSession::MESSAGE_OK : mvcSession::MESSAGE_ERROR)
			);
			$this->redirect($this->buildUriPath(self::ACTION_PROFILE));
		}
	}

	/**
	 * Handles marking MOTD messages as read
	 *
	 * @return void
	 */
	protected function markMotdAction() {
		$oSession = $this->getRequest()->getSession();
		try {
			$motdID = $this->getActionFromRequest(false, 1);
			if ( $motdID ) {
				$oMotdLog = new mofilmMotdLog();
				$oMotdLog->setMotdID($motdID);
				$oMotdLog->setUserID($oSession->getUser()->getID());
				$oMotdLog->save();
				systemLog::message('User marked MOTD as read');

				$this->getModel()->setUpdated(true);
				$this->getModel()->setMessage('MOTD has been marked as read');
			} else {
				systemLog::message('No MOTD set in action (' . $motdID . ')');
			}
		} catch ( Exception $e ) {
			systemLog::error($e->getMessage());
			$this->getModel()->setUpdated(false);
			$this->getModel()->setMessage($e->getMessage());
		}

		if ( $this->getRequest()->isAjaxRequest() ) {
			$oView = new accountView($this);
			$oView->showProfileUpdateResponse();
			return;
		} else {
			$oSession->setStatusMessage(
				$this->getModel()->getMessage(),
				$this->getModel()->isUpdated() == 0 ? mvcSession::MESSAGE_INFO : ($this->getModel()->isUpdated() ? mvcSession::MESSAGE_OK : mvcSession::MESSAGE_ERROR)
			);
			$this->redirect('/home');
		}
	}

	/**
	 * Handles bookmark actions
	 *
	 * @return void
	 */
	protected function bookmarkEventAction() {
		$oSession = $this->getRequest()->getSession();
		try {
			$this->getInputManager()->addFilter('EventID', utilityInputFilter::filterInt());
			$data = $this->getInputManager()->doFilter();

			if ( is_array($data) && isset($data['EventID']) && $data['EventID'] > 0 ) {
				if ( !$oSession->getUser()->getEventFavourites()->isFavourite($data['EventID']) ) {
					$oSession
						->getUser()
						->getEventFavourites()
						->addObject($data['EventID'])
						->save();

					systemLog::message('User updated their event favourites');

					$this->getModel()->setUpdated(true);
					$this->getModel()->setMessage('The event has been added to your favourites');
				} else {
					$this->getModel()->setUpdated(0);
					$this->getModel()->setMessage('You have already bookmarked this event');
				}
			} else {
				systemLog::message('No EventID set in action (' . print_r($data, 1) . ')');
			}
		} catch ( Exception $e ) {
			systemLog::error($e->getMessage());
			$this->getModel()->setUpdated(false);
			$this->getModel()->setMessage($e->getMessage());
		}

		if ( $this->getRequest()->isAjaxRequest() ) {
			$oView = new accountView($this);
			$oView->showProfileUpdateResponse();
			return;
		} else {
			$oSession->setStatusMessage(
				$this->getModel()->getMessage(),
				$this->getModel()->isUpdated() == 0 ? mvcSession::MESSAGE_INFO : ($this->getModel()->isUpdated() ? mvcSession::MESSAGE_OK : mvcSession::MESSAGE_ERROR)
			);
			$this->redirect('/home');
		}
	}

	/**
	 * Handles bookmark actions
	 *
	 * @return void
	 */
	protected function bookmarkRemoveAction() {
		$oSession = $this->getRequest()->getSession();
		try {
			$this->getInputManager()->addFilter('EventID', utilityInputFilter::filterInt());
			$data = $this->getInputManager()->doFilter();

			if ( is_array($data) && isset($data['EventID']) && $data['EventID'] > 0 ) {

				if ( $oSession->getUser()->getEventFavourites()->isFavourite($data['EventID']) ) {
					$oSession
						->getUser()
						->getEventFavourites()
						->removeObject($oSession->getUser()->getEventFavourites()->getObject($data['EventID']))
						->save();

					systemLog::message('User updated their event favourites');

					$this->getModel()->setUpdated(true);
					$this->getModel()->setMessage('The event has been removed from your favourites');
				} else {
					$this->getModel()->setUpdated(0);
					$this->getModel()->setMessage('The selected event is not in your favourites');
				}
			} else {
				systemLog::message('No EventID set in action (' . print_r($data, 1) . ')');
			}
		} catch ( Exception $e ) {
			systemLog::error($e->getMessage());
			$this->getModel()->setUpdated(false);
			$this->getModel()->setMessage($e->getMessage());
		}

		if ( $this->getRequest()->isAjaxRequest() ) {
			$oView = new accountView($this);
			$oView->showProfileUpdateResponse();
			return;
		} else {
			$oSession->setStatusMessage(
				$this->getModel()->getMessage(),
				$this->getModel()->isUpdated() == 0 ? mvcSession::MESSAGE_INFO : ($this->getModel()->isUpdated() ? mvcSession::MESSAGE_OK : mvcSession::MESSAGE_ERROR)
			);
			$this->redirect('/home');
		}
	}


	/**
	 * @see mvcControllerBase::addInputFilters()
	 */
	function addInputFilters() {
		$this->getInputManager()->addFilter('username', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('password', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('redirect', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('_sk', utilityInputFilter::filterString());

		$this->getInputManager()->addFilter('curPassword', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Password', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Firstname', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Surname', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('DateOfBirth', utilityInputFilter::filterStringArray());
		$this->getInputManager()->addFilter('Occupation', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Company', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Website', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Phone', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Skype', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Address1', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Address2', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('City', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Postcode', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('County', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('territory', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('Prefs', utilityInputFilter::filterStringArray());
		$this->getInputManager()->addFilter('Favourites', utilityInputFilter::filterStringArray());
		$this->getInputManager()->addFilter('ExcludedEvents', utilityInputFilter::filterStringArray());

		$this->getInputManager()->addFilter('ProfileName', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('ProfileActive', utilityInputFilter::filterInt());
	}

	/**
	 * @see mvcControllerBase::addInputToModel()
	 *
	 * @param array $inData
	 * @param accountModel $inModel
	 */
	function addInputToModel($inData, $inModel) {
		$inModel->setUsername($inData['username']);
		$inModel->setPassword($inData['password']);
		$inModel->setFormSessionToken($inData['_sk']);

		$inData['redirect'] = urldecode($inData['redirect']);
		if (
			$inData['redirect'] == $this->buildUriPath(self::ACTION_LOGIN) ||
			$inData['redirect'] == $this->buildUriPath(self::ACTION_DO_LOGIN) ||
			(!$inData['redirect'] && $inModel->getRedirect() == $this->buildUriPath(self::ACTION_LOGIN)) ||
			(!$inData['redirect'] && $inModel->getRedirect() == $this->buildUriPath(self::ACTION_DO_LOGIN))
		) {
			/*
			 * Prevent recursion in redirection
			 */
			$inModel->setRedirect('');
		} else {
			$inModel->setRedirect($inData['redirect']);
		}

		if ( $this->getAction() == self::ACTION_UPDATE_PROFILE ) {
			$inModel->setUser($this->getRequest()->getSession()->getUser());

			$inModel->getUser()->setFirstname(trim(strip_tags($_POST['Firstname'])));
			$inModel->getUser()->setSurname(trim(strip_tags($_POST['Surname'])));
			$inModel->getUser()->setTerritoryID($inData['territory']);

			if ( isset($inData['Password']) && strlen($inData['Password']) > 7 ) {
				if ( isset($inData['curPassword']) && md5($inData['curPassword']) == $inModel->getUser()->getPassword() ) {
					$inModel->getUser()->setPassword($inData['Password']);
				} else {
					throw new mofilmException('Your current password does not match what is stored, please enter it.');
				}
			}

			$inModel->getUser()->getParamSet()->setParam(mofilmUser::PARAM_ADDRESS1, trim(strip_tags($_POST['Address1'])));
			$inModel->getUser()->getParamSet()->setParam(mofilmUser::PARAM_ADDRESS2, trim(strip_tags($_POST['Address2'])));
			$inModel->getUser()->getParamSet()->setParam(mofilmUser::PARAM_CITY, trim(strip_tags($_POST['City'])));
			$inModel->getUser()->getParamSet()->setParam(mofilmUser::PARAM_COUNTY, trim(strip_tags($_POST['County'])));
			$inModel->getUser()->getParamSet()->setParam(mofilmUser::PARAM_POSTCODE, trim(strip_tags($_POST['Postcode'])));
			$inModel->getUser()->getParamSet()->setParam(mofilmUser::PARAM_DESCRIPTION, trim(strip_tags($_POST['Description'])));
			$inModel->getUser()->getParamSet()->setParam(mofilmUser::PARAM_SKILLS, trim(strip_tags($_POST['Skills'])));
			$inModel->getUser()->getParamSet()->setParam(mofilmUser::PARAM_OCCUPATION, trim(strip_tags($_POST['Occupation'])));
			$inModel->getUser()->getParamSet()->setParam(mofilmUser::PARAM_COMPANY, trim(strip_tags($_POST['Company'])));
			$inModel->getUser()->getParamSet()->setParam(mofilmUser::PARAM_WEBSITE, trim(strip_tags($_POST['Website'])));
			$inModel->getUser()->getParamSet()->setParam(mofilmUser::PARAM_PROFILE_TEXT, substr(trim(strip_tags($_POST['ProfileText'])), 0, mofilmConstants::PROFILE_TEXT_LENGTH));
			$inModel->getUser()->getParamSet()->setParam(mofilmUser::PARAM_PHONE, $inData['Phone']);
			$inModel->getUser()->getParamSet()->setParam(mofilmUser::PARAM_MOBILE_PHONE, $inData['MobilePhone']);
			$inModel->getUser()->getParamSet()->setParam(mofilmUser::PARAM_SKYPE, $inData['Skype']);

			if ( isset($inData['Prefs']) && is_array($inData['Prefs']) && count($inData['Prefs']) > 0 ) {
				foreach ( $inData['Prefs'] as $pref => $value ) {
					$inModel->getUser()->getParamSet()->setParam($pref, $value);
				}
			}
			if ( isset($inData['DateOfBirth']) && is_array($inData['DateOfBirth']) && count($inData['DateOfBirth']) == 3 ) {
				if ( checkdate($inData['DateOfBirth']['Month'], $inData['DateOfBirth']['Day'], $inData['DateOfBirth']['Year']) ) {
					$dob = $inData['DateOfBirth']['Year'] . '-' . $inData['DateOfBirth']['Month'] . '-' . $inData['DateOfBirth']['Day'];
					if ( $dob != date('Y-m-d') ) {
						$inModel->getUser()->getParamSet()->setParam('DateOfBirth', $dob);
					}
				} else {
					throw new mofilmException('The date you entered is not valid.');
				}
			}
			if ( isset($inData['ProfileName']) && strlen($inData['ProfileName']) > 0 ) {
				$oProfileCheck = mofilmUserProfile::getInstanceByProfileName($inData['ProfileName']);
				if ( $oProfileCheck instanceof mofilmUserProfile && $oProfileCheck->getID() > 0 ) {
					if ( $oProfileCheck->getUserID() != $this->getRequest()->getSession()->getUser()->getID() ) {
						throw new mofilmException("The profile name ({$inData['ProfileName']}) is already in use by another user");
					}
					if ( $oProfileCheck->getProfileName() != $inData['ProfileName'] && $oProfileCheck->isDisabled() ) {
						throw new mofilmException("The profile name ({$inData['ProfileName']}) is already in use by another user");
					}
				}

				if ( $inModel->getUser()->getProfile()->getProfileName() ) {
					$oProfile = $inModel->getUser()->getProfile();
					if ( $oProfile->getProfileName() != $inData['ProfileName'] ) {
						$oProfile->delete();
						unset($oProfile);

						$oProfile = new mofilmUserProfile();
					}
				} else {
					$oProfile = new mofilmUserProfile();
				}

				$oProfile->setProfileName($inData['ProfileName']);
				$oProfile->setActive((int)$inData['ProfileActive']);

				$inModel->getUser()->setProfile($oProfile);
			}

			$inModel->getUser()->getFavourites()->reset();
			if ( isset($inData['Favourites']) && is_array($inData['Favourites']) && count($inData['Favourites']) > 0 ) {
				foreach ( $inData['Favourites'] as $index => $data ) {
					if ( !array_key_exists('Remove', $data) ) {
						$inModel->getUser()->getFavourites()->addObject($data['ID']);
					}
				}
			}

			$inModel->getUser()->getEventFilter()->reset();
			$inModel->getUser()->getEventFilter()->setModified();
			if ( isset($inData['ExcludedEvents']) && is_array($inData['ExcludedEvents']) && count($inData['ExcludedEvents']) > 0 ) {
				foreach ( $inData['ExcludedEvents'] as $eventID ) {
					$inModel->getUser()->getEventFilter()->setObject($eventID);
				}
			}
		}
	}

	/**
	 * Fetches the model
	 *
	 * @return accountModel
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
		$oModel = new accountModel();
		$oModel->setRequest($this->getRequest());
		$oModel->setRedirect($this->getRequest()->getRequestUri());
		$oModel->setLanguage($this->getRequest()->getLocale());
		$this->setModel($oModel);
	}
}