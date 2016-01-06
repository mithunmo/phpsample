<?php
/**
 * usersController
 *
 * Stored in usersController.class.php
 * 
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category usersController
 * @version $Rev: 115 $
 */


/**
 * usersController
 *
 * usersController class
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category usersController
 */
class usersController extends mvcController {
	
	const ACTION_VIEW = 'view';
	const ACTION_SEARCH = 'doSearch';
	const ACTION_MESSAGE = 'message';
	const ACTION_DO_MESSAGE = 'doMessage';
	const ACTION_EDIT = 'edit';
	const ACTION_DO_EDIT = 'doEdit';
	const ACTION_SU = 'su';
	const ACTION_MESSAGE_TEMPLATE = 'msgtmp';
	const ACTION_UPLOAD = 'upload';
	
	const VIEW_USER_STATS = 'userStats';
	
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
		
		$this->setDefaultAction(self::ACTION_VIEW);
		$this->getControllerActions()
			->addAction(self::ACTION_VIEW)
			->addAction(self::ACTION_SEARCH)
			->addAction(self::ACTION_MESSAGE)
			->addAction(self::ACTION_DO_MESSAGE)
			->addAction(self::ACTION_DO_EDIT)
			->addAction(self::ACTION_EDIT)
			->addAction(self::ACTION_SU)
			->addAction(self::ACTION_MESSAGE_TEMPLATE)
			->addAction(self::ACTION_UPLOAD);
		
		$this->getControllerViews()
			->addView(self::VIEW_USER_STATS);
		
		$this->setSearchQuery(array());
	}
	
	/**
	 * @see mvcControllerBase::launch()
	 */
	function launch() {
		switch ( $this->getAction() ) {
			case self::ACTION_EDIT:
				$userID = $this->getActionFromRequest(false, 1);
				$oView = new usersView($this);
				try {
					systemLog::message('User is editing user record '.$userID);
					
					$this->getModel()->getUser($userID);
					$oView->showUserEditPage();
				} catch ( mvcModelException $e ) {
					$this->getRequest()->getSession()->setStatusMessage($e->getMessage(), mvcSession::MESSAGE_ERROR);
					$this->redirect($this->buildUriPath(self::ACTION_VIEW));
				}
			break;
			
			case self::ACTION_DO_EDIT:
				$data = $this->getInputManager()->doFilter();
				
				try {
					$this->addInputToModel($data, $this->getModel());
					$this->getModel()->getUser()->save();
					systemLog::message('User successfully edited user record '.$data['UserID']);
					$message = 'User details updated successfully';

					if ( $this->hasAuthority('usersController.upload') && isset($_FILES) && $_FILES['error'] == UPLOAD_ERR_OK ) {
						try {
							$this->uploadImageAction($this->getModel()->getUser());
							$message .= ' and profile image updated successfully';
						} catch ( Exception $e ) {
							$message .= ' but: '.$e->getMessage();
						}
					}

					$this->getRequest()->getSession()->setStatusMessage($message, mvcSession::MESSAGE_OK);
					$this->redirect($this->buildUriPath(self::ACTION_EDIT, $this->getModel()->getUser()->getID()));
				} catch ( Exception $e ) {
					$this->getRequest()->getSession()->setStatusMessage($e->getMessage(), mvcSession::MESSAGE_ERROR);
					$this->redirect($this->buildUriPath(self::ACTION_VIEW));
				}
			break;
			
			case self::ACTION_MESSAGE:
				$userID = $this->getActionFromRequest(false, 1);
				$oView = new usersView($this);
				try {
					$this->getInputManager()->setLookupGlobals(utilityInputManager::LOOKUPGLOBALS_GET);
					$data = $this->getInputManager()->doFilter();
					$this->addInputToModel($data, $this->getModel());
					
					$this->getModel()->getUser($userID);
					$oView->showUserMessagePage();
				} catch ( mvcModelException $e ) {
					$this->getRequest()->getSession()->setStatusMessage($e->getMessage(), mvcSession::MESSAGE_ERROR);
					$this->redirect($this->buildUriPath(self::ACTION_VIEW));
				}
			break;
			
			case self::ACTION_DO_MESSAGE:
				$data = $this->getInputManager()->doFilter();
				
				$this->addInputToModel($data, $this->getModel());
				
				try {
					$this->getModel()->sendMessage();
					$this->getRequest()->getSession()->setStatusMessage('Message sent successfully', mvcSession::MESSAGE_OK);
					systemLog::message('Sent message to user '.$data['UserID']);
					
					if ( $this->getModel()->getMovieID() ) {
						$this->redirect('/videos/edit/'.$this->getModel()->getMovieID());
					} else {
						$this->redirect($this->buildUriPath(self::ACTION_EDIT, $this->getModel()->getUser()->getID()));
					}
				} catch ( Exception $e ) {
					$this->getRequest()->getSession()->setStatusMessage($e->getMessage(), mvcSession::MESSAGE_ERROR);
					$this->redirect($this->buildUriPath(self::ACTION_VIEW));
				}
			break;
			
			case self::ACTION_SU:
				if ( !$this->getRequest()->getSession()->getUser()->getPermissions()->isRoot() ) {
					$this->getRequest()->getSession()->setStatusMessage('You are not authorised to perform the action', mvcSession::MESSAGE_ERROR);
					$this->redirect('/home');
					return;
				}
				
				$data = $this->getInputManager()->doFilter();
				if ( !isset($data['UserID']) || !is_numeric($data['UserID']) ) {
					$this->getRequest()->getSession()->setStatusMessage('Invalid UserID supplied for change user', mvcSession::MESSAGE_ERROR);
					$this->redirect('/home');
					return;
				}
				
				$oUser = mofilmUserManager::getInstanceByID($data['UserID']);
				if ( !$oUser instanceof mofilmUser || !$oUser->getID() || $oUser->getEnabled() != mofilmUserBase::ENABLED_Y ) {
					$this->getRequest()->getSession()->setStatusMessage('Specified UserID is not valid or is not active', mvcSession::MESSAGE_ERROR);
					$this->redirect('/home');
					return;
				}
				if ( !$oUser->getPermissions()->isAuthorised('canLogin') ) {
					$this->getRequest()->getSession()->setStatusMessage('Specified UserID is not permitted to login to the admin system', mvcSession::MESSAGE_ERROR);
					$this->redirect('/home');
					return;
				}
				
				$message = "SU Login by {$this->getRequest()->getSession()->getUser()->getID()} to {$data['UserID']} OK";
				systemLog::message($message);
				
				mofilmUserLog::factory($oUser->getID(), date('Y-m-d H:i:s'), mofilmUserLog::TYPE_LOGIN, $message)->save();
				$this->getRequest()->getSession()->setStatusMessage($message, mvcSession::MESSAGE_INFO);
				
				$this->getRequest()->getSession()->setUser($oUser);
				$this->redirect('/home');
			break;
			
			case self::ACTION_MESSAGE_TEMPLATE:
				if ( $this->getRequest()->isAjaxRequest() ) {
					$this->getInputManager()->setLookupGlobals(utilityInputManager::LOOKUPGLOBALS_GET);
					$data = $this->getInputManager()->doFilter();
					
					$this->addInputToModel($data, $this->getModel());
					
					$oView = new usersView($this);
					$oView->sendJsonResult($this->getModel()->getMessageTemplate(), mvcSession::MESSAGE_OK);
				} else {
					$this->redirect($this->buildUriPath(self::ACTION_VIEW));
				}
			break;
			
			case self::ACTION_SEARCH:
			default:
				$this->getInputManager()->setLookupGlobals(utilityInputManager::LOOKUPGLOBALS_GET);
				$data = $this->getInputManager()->doFilter();
				
				$this->addInputToModel($data, $this->getModel());
				
				$oView = new usersView($this);
				$oView->showUsersPage();
		}
	}
	
	/**
	 * Handles standalone view requests into the users controller
	 * 
	 * @param array $params
	 * @return string
	 */
	function fetchStandaloneView($params = array()) {
		switch ( $params['view'] ) {
			case self::VIEW_USER_STATS:
				$oView = new usersView($this);
				return $oView->getUserStatsView($params);
			break;
		}
	}

	/**
	 * Handles file uploads for the profile image
	 *
	 * @param mofilmUser $inUser
	 * @return void
	 * @access protected
	 * @throws mofilmException
	 */
	protected function uploadImageAction(mofilmUser $oUser) {
		$oFileUpload = new mvcFileUpload(
			array(
				mvcFileUpload::OPTION_AUTO_CREATE_FILESTORE => false,
				mvcFileUpload::OPTION_CHECK_PERMISSIONS => false,
				mvcFileUpload::OPTION_FIELD_NAME => 'Files',
				mvcFileUpload::OPTION_SUB_FOLDER_FORMAT => '',
				mvcFileUpload::OPTION_WRITE_IMMEDIATE => false,
				mvcFileUpload::OPTION_STORE_RAW_DATA => true,
				mvcFileUpload::OPTION_USE_ORIGINAL_NAME => true,
			)
		);
		$oFileUpload->initialise();
		$oFiles = $oFileUpload->process();

		if ( $oFiles->getCount() > 0 ) {
			systemLog::message('Processing files for profile image for user '.$oUser->getID());

			if ( $oUser->getAvatar()->getImageFilename() ) {
				$iterate = false;
				$outputLocation = mofilmConstants::getNasFolder().dirname($oUser->getAvatar()->getImageFilename());
			} else {
				$iterate = true;
				$outputLocation = mofilmConstants::getFilmmakerImageFolder();
			}

			if ( $iterate || (!file_exists($outputLocation) || !is_dir($outputLocation)) ) {
				if ( $iterate ) {
					$res = true;
					foreach ( array(date('Y'), date('m'), date('d')) as $path ) {
						$outputLocation .= DIRECTORY_SEPARATOR.$path;
						if ( !is_dir($outputLocation) ) {
							systemLog::message("Building path: $outputLocation");
							$oldmask = umask(0);
							$res = mkdir($outputLocation, 0777) && $res;
							umask($oldmask);

							if ( !$res ) {
								throw new mofilmException("Unable to create directories in outputLocation ($outputLocation)");
							}
						}
					}
				} else {
					$oldmask = umask(0);
					$res = mkdir($outputLocation, 0777, true);
					umask($oldmask);
				}

				if ( !$res ) {
					throw new mofilmException("Unable to create filestore @ $outputLocation");
				}
			}

			if ( !is_readable($outputLocation) || !is_writable($outputLocation) ) {
				throw new mofilmException("Filestore @ $outputLocation is not readable and/or writable");
			}

			$oImageConv = new imageConvertor(
				array(
					imageConvertor::OPTION_OUTPUT_FILENAME => md5($oUser->getID().':'.$oUser->getEmail()),
					imageConvertor::OPTION_OUTPUT_OVERWRITE_FILES => true,
				)
			);

			$options = array(
				imageConvertor::OPTION_OUTPUT_LOCATION => $outputLocation,
				imageConvertor::OPTION_OUTPUT_FORMAT => 'jpeg',
				imageConvertor::OPTION_OUTPUT_WIDTH => 200,
				imageConvertor::OPTION_OUTPUT_HEIGHT => 200,
				imageConvertor::OPTION_OUTPUT_QUALITY => 90,
				imageConvertor::OPTION_OUTPUT_PAD_IMAGE => true,
				imageConvertor::OPTION_OUTPUT_PAD_COLOUR => 'white',
			);

			$oImageConv->setOptions($options);
			$oImageConv->process($oFiles->getFirst()->getRawFileData());

			$oUser->getAvatar()->setImageFilename(
				str_replace(mofilmConstants::getNasFolder(), '', $oImageConv->getOutputLocation()).
				'/'.
				$oImageConv->getOutputFilename()
			);
			$oUser->save();
		}
	}
	
	
	
	/**
	 * @see mvcControllerBase::addInputFilters()
	 */
	function addInputFilters() {
		if ( $this->getAction() == self::ACTION_SEARCH ) {
			$this->getInputManager()->addFilter('ClientID', utilityInputFilter::filterInt());
			$this->getInputManager()->addFilter('CountryID', utilityInputFilter::filterInt());
			$this->getInputManager()->addFilter('Status', utilityInputFilter::filterInt());
			$this->getInputManager()->addFilter('Offset', utilityInputFilter::filterInt());
			$this->getInputManager()->addFilter('Limit', utilityInputFilter::filterInt());
			$this->getInputManager()->addFilter('Status', utilityInputFilter::filterString());
			$this->getInputManager()->addFilter('Finalists', utilityInputFilter::filterInt());
			$this->getInputManager()->addFilter('UploadedMovie', utilityInputFilter::filterInt());
			$this->getInputManager()->addFilter('OnlyEmail', utilityInputFilter::filterInt());
			$this->getInputManager()->addFilter('Keywords', utilityInputFilter::filterString());
		}
		if ( $this->getAction() == self::ACTION_DO_MESSAGE || $this->getAction() == self::ACTION_MESSAGE ) {
			$this->getInputManager()->addFilter('UserID', utilityInputFilter::filterInt());
			$this->getInputManager()->addFilter('Subject', utilityInputFilter::filterString());
			$this->getInputManager()->addFilter('Message', utilityInputFilter::filterString());
			$this->getInputManager()->addFilter('MovieID', utilityInputFilter::filterInt());
			$this->getInputManager()->addFilter('MsgParams', utilityInputFilter::filterStringArray());
		}
		if ( $this->getAction() == self::ACTION_DO_EDIT ) {
			$this->getInputManager()->addFilter('UserID', utilityInputFilter::filterInt());
			$this->getInputManager()->addFilter('Firstname', utilityInputFilter::filterString());
			$this->getInputManager()->addFilter('Surname', utilityInputFilter::filterString());
			$this->getInputManager()->addFilter('DateOfBirth', utilityInputFilter::filterStringArray());
			$this->getInputManager()->addFilter('Occupation', utilityInputFilter::filterString());
			$this->getInputManager()->addFilter('Company', utilityInputFilter::filterString());
			$this->getInputManager()->addFilter('Website', utilityInputFilter::filterString());
			$this->getInputManager()->addFilter('mofilmmaker', utilityInputFilter::filterString());
			$this->getInputManager()->addFilter('Phone', utilityInputFilter::filterString());
			$this->getInputManager()->addFilter('Skype', utilityInputFilter::filterString());
			$this->getInputManager()->addFilter('Address1', utilityInputFilter::filterString());
			$this->getInputManager()->addFilter('Address2', utilityInputFilter::filterString());
			$this->getInputManager()->addFilter('City', utilityInputFilter::filterString());
			$this->getInputManager()->addFilter('Postcode', utilityInputFilter::filterString());
			$this->getInputManager()->addFilter('County', utilityInputFilter::filterString());
			$this->getInputManager()->addFilter('territory', utilityInputFilter::filterInt());
			$this->getInputManager()->addFilter('Enabled', utilityInputFilter::filterString());
			$this->getInputManager()->addFilter('ProfileActive', utilityInputFilter::filterInt());
			$this->getInputManager()->addFilter('ProfileName', utilityInputFilter::filterString());
			$this->getInputManager()->addFilter('SchoolName', utilityInputFilter::filterString());
                        $this->getInputManager()->addFilter('ShowReel', utilityInputFilter::filterString());
		}
		if ( $this->getAction() == self::ACTION_SU ) {
			$this->getInputManager()->addFilter('UserID', utilityInputFilter::filterInt());
		}
		if ( $this->getAction() == self::ACTION_MESSAGE_TEMPLATE ) {
			$this->getInputManager()->addFilter('TemplateID', utilityInputFilter::filterInt());
		}
	}
	
	/**
	 * @see mvcControllerBase::addInputToModel()
	 * 
	 * @param array $inData
	 * @param usersModel $inModel
	 */
	function addInputToModel($inData, $inModel) {
		if ( array_key_exists('MsgParams', $inData) && is_array($inData['MsgParams']) ) {
			foreach ( $inData['MsgParams'] as $param => $value ) {
				$inModel->addMessageParam($param, $value);
			}
		}
		if ( array_key_exists('MovieID', $inData) && is_numeric($inData['MovieID']) ) {
			$inModel->setMovieID($inData['MovieID']);
		}
		
		if ( $this->getAction() == self::ACTION_SEARCH ) {
			if ( !$inData['Limit'] || $inData['Limit'] > 30 ) {
				$inData['Limit'] = 20;
			}
			if ( !$inData['Offset'] || $inData['Offset'] < 0 ) {
				$inData['Offset'] = 0;
			}
			
			if ( $this->hasAuthority('usersController.canSearchByClient') ) {
				$inModel->getUserSearch()->setClientID($inData['ClientID']);
			}
			if ( $this->hasAuthority('usersController.canSearchByStatus') ) {
				$inModel->getUserSearch()->setEnabled($inData['Status']);
				$inModel->getUserSearch()->setOnlyActiveUsers(false);
			}
			if ( $this->hasAuthority('usersController.canSearchByCountry') ) {
				$inModel->getUserSearch()->setTerritoryID($inData['CountryID']);
			}
			$inModel->getUserSearch()->setHasUploadedMovie($inData['UploadedMovie']);
			
			if ( strlen($inData['Keywords']) > 2 && strtolower($inData['Keywords']) != 'search for name' ) {
				$matches = array();
				if ( preg_match('/^email:([\w+.]*)/i', $inData['Keywords'], $matches) ) {
					$inModel->getUserSearch()->setUserEmailAddress((string) $matches[1]);
					
				} elseif ( preg_match('/^user:(\d+)$/i', $inData['Keywords'], $matches) ) {
					$inModel->getUserSearch()->setUserID((int) $matches[1]);
					
				} elseif ( is_numeric($inData['Keywords']) && $inData['Keywords'] > 0 ) {
					$inModel->getUserSearch()->setUserID($inData['Keywords']);
					
				} elseif ( isset($inData['OnlyEmail']) && $inData['OnlyEmail'] == 1 ) {
					$inModel->getUserSearch()->setUserEmailAddress($inData['Keywords']);
					
				} else {
					$inModel->getUserSearch()->setKeywords($inData['Keywords']);
					
				}
			}
			
			$inModel->getUserSearch()->setLimit($inData['Limit']);
			$inModel->getUserSearch()->setOffset($inData['Offset']);
			$inModel->getUserSearch()->setOnlyFinalists($inData['Finalists']);
			
			unset($inData['Offset'], $inData['Limit']);
			$this->setSearchQuery($inData);
		}
		
		if ( $this->getAction() == self::ACTION_DO_MESSAGE ) {
			$inModel->getUser($inData['UserID']);
			$inModel->getUserMessage()->setFromUserID($this->getRequest()->getSession()->getUser()->getID());
			$inModel->getUserMessage()->setMessage(utf8_encode($inData['Message']));
			$inModel->getUserMessage()->setStatus(mofilmUserPrivateMessage::STATUS_NEW);
			$inModel->getUserMessage()->setSubject(utf8_encode($inData['Subject']));
			$inModel->getUserMessage()->setToUserID($inData['UserID']);
		}
		if ( $this->getAction() == self::ACTION_DO_EDIT ) {
			$inModel->getUser($inData['UserID']);
			$inModel->getUser()->setFirstname($inData['Firstname']);
			$inModel->getUser()->setSurname($inData['Surname']);
			$inModel->getUser()->setTerritoryID($inData['territory']);
			
			if ( isset($inData['DateOfBirth']) && is_array($inData['DateOfBirth']) && count($inData['DateOfBirth']) == 3 ) {
				if ( checkdate($inData['DateOfBirth']['Month'], $inData['DateOfBirth']['Day'], $inData['DateOfBirth']['Year']) ) {
					$dob = $inData['DateOfBirth']['Year'].'-'.$inData['DateOfBirth']['Month'].'-'.$inData['DateOfBirth']['Day'];
					if ( $dob != date('Y-m-d') ) {
						$inModel->getUser()->getParamSet()->setParam('DateOfBirth', $dob);
					}
				} else {
					throw new mofilmException('The date you entered is not valid.');
				}
			}
			if ( $this->hasAuthority('admin.usersController.canChangeStatus') ) {
				$inModel->getUser()->setEnabled($inData['Enabled']);
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
			$inModel->getUser()->getParamSet()->setParam(mofilmUser::PARAM_MOFILMMAKER, trim(strip_tags($_POST['mofilmmaker'])));
			$inModel->getUser()->getParamSet()->setParam(mofilmUser::PARAM_PROFILE_TEXT, substr(trim(strip_tags($_POST['ProfileText'])), 0, mofilmConstants::PROFILE_TEXT_LENGTH));
			$inModel->getUser()->getParamSet()->setParam(mofilmUser::PARAM_PHONE, $inData['Phone']);
			$inModel->getUser()->getParamSet()->setParam(mofilmUser::PARAM_MOBILE_PHONE, $inData['MobilePhone']);
			$inModel->getUser()->getParamSet()->setParam(mofilmUser::PARAM_SKYPE, $inData['Skype']);

			if ( isset ($inData['SchoolName']) ) {
				$inModel->getUser()->getParamSet()->setParam(mofilmUser::PARAM_SCHOOL_NAME, $inData['SchoolName']);
			}
                        
                        if ( isset ($inData['ShowReel']) ) {
				$inModel->getUser()->getParamSet()->setParam(mofilmUser::PARAM_SHOWREEL_URL, $inData['ShowReel']);
			}
			
			if ( $this->hasAuthority('admin.usersController.canEditProfile') ) {
				if ( isset($inData['ProfileName']) && strlen($inData['ProfileName']) > 0 ) {
					$oProfileCheck = mofilmUserProfile::getInstanceByProfileName($inData['ProfileName']);
					if ( $oProfileCheck instanceof mofilmUserProfile && $oProfileCheck->getID() > 0 ) {
						if ( $oProfileCheck->getUserID() != $inModel->getUser()->getID() ) {
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
			}
		}
		if ( $this->getAction() == self::ACTION_MESSAGE_TEMPLATE ) {
			$inModel->setMessageTemplateID($inData['TemplateID']);
		}
	}
	
	/**
	 * Fetches the model
	 *
	 * @return usersModel
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
		$oModel = new usersModel();
		$oModel->setCurrentUser($this->getRequest()->getSession()->getUser());
		$oModel->getUserSearch()->setUser($this->getRequest()->getSession()->getUser());
		$oModel->getUserSearch()->setEnabled('Y');
		$this->setModel($oModel);
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
}
