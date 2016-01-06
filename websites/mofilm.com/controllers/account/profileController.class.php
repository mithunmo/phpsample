<?php
/**
 * profileController
 *
 * Stored in profileController.class.php
 * 
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_mofilm.com
 * @subpackage controllers
 * @category profileController
 * @version $Rev: 371 $
 */


/**
 * profileController
 *
 * profileController class
 * 
 * @package websites_mofilm.com
 * @subpackage controllers
 * @category profileController
 */
class profileController extends mvcController {
	
	const ACTION_VIEW = 'view';
	const ACTION_PROFILE = 'profile';
	const ACTION_UPDATE_PROFILE = 'doUpdate';
	const ACTION_FB_UPDATE_PROFILE = 'doFBUpdate';
	const ACTION_DO_UPLOAD = 'doUpload';
	const ACTION_CHECK_PROFILE_NAME = 'checkName';
	const ACTION_UPDATE_PROFILE_MOVIE = 'updateProfileMovie';

	const MAX_PROFILE_MOVIES = 5;
	
	/**
	 * @see mvcControllerBase::initialise()
	 */
	function initialise() {
		parent::initialise();
		
		$this->setDefaultAction(self::ACTION_VIEW);
		if ( $this->getAction() == self::ACTION_CHECK_PROFILE_NAME ) {
			$this->setRequiresAuthentication(false);
		} else {
			$this->setRequiresAuthentication(true);
		}
		$this
			->getControllerActions()
				->addAction(self::ACTION_PROFILE)
				->addAction(self::ACTION_UPDATE_PROFILE)
				->addAction(self::ACTION_FB_UPDATE_PROFILE)
				->addAction(self::ACTION_CHECK_PROFILE_NAME)
				->addAction(self::ACTION_DO_UPLOAD)
				->addAction(self::ACTION_UPDATE_PROFILE_MOVIE);

		$this->addInputFilters();
	}
	
	/**
	 * @see mvcControllerBase::launch()
	 */
	function launch() {
		switch ( $this->getAction() ) {
			case self::ACTION_PROFILE:				$this->profileUpdateAction(); break;
			case self::ACTION_UPDATE_PROFILE:		$this->doProfileUpdateAction(); break;
			case self::ACTION_FB_UPDATE_PROFILE:		$this->doProfileFBUpdateAction(); break;
			case self::ACTION_CHECK_PROFILE_NAME:	$this->checkProfileNameAction(); break;
			case self::ACTION_DO_UPLOAD:			$this->uploadImageAction(); break;
			case self::ACTION_UPDATE_PROFILE_MOVIE:	$this->updateProfileMovie(); break;
			
			default:
				$this->profileUpdateAction();
			break;
		}
	}
	
	/**
	 * Shows the profile update form
	 * 
	 * @return void
	 */
	protected function profileUpdateAction() {
		$this->getModel()->setUser($this->getRequest()->getSession()->getUser());
		
		$oView = new profileView($this);
		$oView->showProfilePage();
	}
	
	/**
	 * Updates a users profile
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
			systemLog::error($e->getMessage());
			$this->getModel()->setUpdated(false);
			$this->getModel()->setMessage($e->getMessage());
		}

		if ( $this->getRequest()->isAjaxRequest() ) {
			$oView = new profileView($this);
			$oView->showProfileUpdateResponse();
			return;
		} else {
			if ( $this->getModel()->isUpdated() == 0 ) {
				$status = mvcSession::MESSAGE_INFO; 
			} elseif ( $this->getModel()->isUpdated() ) {
				$status = mvcSession::MESSAGE_OK;
			} else {
				$status = mvcSession::MESSAGE_ERROR;
			}
			$oSession->setStatusMessage($this->getModel()->getMessage(), $status);
			$this->redirect($this->buildUriPath(self::ACTION_PROFILE));
		}
	}
	
	/**
	 * Updates Facebook ID
	 * 
	 * @return void
	 */
	protected function doProfileFBUpdateAction() {
		$this->getInputManager()->addFilter('facebookID', utilityInputFilter::filterInt());
		$data = $this->getInputManager()->doFilter();
		$oUser = $this->getRequest()->getSession()->getUser();
		
		$oUser->setFacebookID($data['facebookID']);
		$oUser->save();
		
		if ( $this->getRequest()->isAjaxRequest() ) {
			return true;
		} else {
			$this->redirect($this->buildUriPath(self::ACTION_PROFILE));
		}
	}
	
	/**
	 * Checks if a profile name has been taken or not, only works for ajax requests
	 * 
	 * @return void
	 */
	protected function checkProfileNameAction() {
		if ( !$this->getRequest()->isAjaxRequest() ) {
			$this->redirect($this->buildUriPath(self::ACTION_PROFILE));
		} else {
			$data = $this->getInputManager()->doFilter();
			if ( array_key_exists('ProfileName', $data) && strlen($data['ProfileName']) > 2 ) {
				systemLog::info("Looking up: {$data['ProfileName']} in profiles table");
				$oUser = mofilmUserProfile::getInstanceByProfileName($data['ProfileName']);
					
				if ( $oUser instanceof mofilmUserProfile && $oUser->getID() > 0 ) {
					$message = "{$data['ProfileName']} is already in use by another user";
					$updated = false;
				} else {
					$message = "{$data['ProfileName']} is available";
					$updated = true;
				}
			} else {
				$message = "{$data['ProfileName']} is too short, please use something longer";
				$updated = 0;
			}
			systemLog::info($message);
			$this->getModel()->setMessage($message)->setUpdated($updated);
			
			$oView = new profileView($this);
			$oView->showProfileUpdateResponse();
			return;
		}
	}
	
	/**
	 * Handles file uploads for the profile image
	 * 
	 * @return void
	 */
	protected function uploadImageAction() {
		$userID = $this->getActionFromRequest(false, 1);
		$oUser = $this->getRequest()->getSession()->getUser();
		$response = 'failed';
		systemLog::message("Received upload request for $userID");
		
		if ( $userID && $userID == $oUser->getID() ) {
			try {
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
					systemLog::message('Processing files for profile image for user '.$userID);
					
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
							imageConvertor::OPTION_OUTPUT_FILENAME => md5($userID.':'.$oUser->getEmail()),
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
					
					$response = $oUser->getAvatar()->getImageFilename();
				}
				
			} catch ( Exception $e ) {
				systemLog::error($e->getMessage());
				$response = 'failed';
			}
		}
		if ( $this->getRequest()->isAjaxRequest() ) {
			echo $response;
		} else {
			$this->redirect($this->buildUriPath(self::ACTION_PROFILE));
		}
	}
	
	/**
	 * Updates a profile movie
	 * 
	 * @return void
	 */
	protected function updateProfileMovie() {
		$this->getInputManager()->addFilter('id', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('value', utilityInputFilter::filterString());
		$data = $this->getInputManager()->doFilter();
		
		if ( preg_match('/^movID\-\d+/', $data['id']) ) {
			$id = (int) substr($data['id'], 6);
			
			$oUser = $this->getRequest()->getSession()->getUser();
			$oMovieProfile = $oUser->getProfileMovieSet()->getObjectByMovieID($id);
			if ( $oMovieProfile instanceof mofilmUserProfileMovie && $oMovieProfile->getMovieID() == $id ) {
				$oMovieProfile->setTitle($data['value']);
			} else {
				if ( $oUser->getProfileMovieSet()->getCount() < self::MAX_PROFILE_MOVIES ) {
					$oMovieProfile = new mofilmUserProfileMovie();
					$oMovieProfile->setMovieID($id);
					$oMovieProfile->setTitle($data['value']);
					$oMovieProfile->setPosition($oUser->getProfileMovieSet()->getCount()+1);
					$oUser->getProfileMovieSet()->setObject($oMovieProfile);
				}
			}
			systemLog::notice("Updating profile movie: $id to {$data['value']}");
			$oUser->save();
		}
		
		if ( $this->getRequest()->isAjaxRequest() ) {
			echo $data['value'];
		} else {
			$this->redirect($this->buildUriPath(self::ACTION_PROFILE));
		}
	}
	
	
	
	/**
	 * @see mvcControllerBase::addInputFilters()
	 */
	function addInputFilters() {
		$this->getInputManager()->addFilter('username', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('password', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('curPassword', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Password', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('ConfirmPassword', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('facebookID', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('Firstname', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Surname', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('SignupCode', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('DateOfBirth', utilityInputFilter::filterStringArray());
		$this->getInputManager()->addFilter('Description', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Occupation', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Company', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Website', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('ShowReelURL', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Phone', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('MobilePhone', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Skype', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Address1', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Address2', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('City', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Postcode', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('County', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('territory', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('optIn', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('Prefs', utilityInputFilter::filterStringArray());
		//$this->getInputManager()->addFilter('Skills', utilityInputFilter::f;
		$this->getInputManager()->addFilter('autoCommitStatus', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('SchoolName', utilityInputFilter::filterString());
		
		$this->getInputManager()->addFilter('ProfileName', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('ProfileActive', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('ProfileMovies', utilityInputFilter::filterStringArray());
		$this->getInputManager()->addFilter('ProfileMovieTitles', utilityInputFilter::filterStringArray());
	}
	
	/**
	 * @see mvcControllerBase::addInputToModel()
	 *
	 * @param array $inData
	 * @param profileModel $inModel
	 */
	function addInputToModel($inData, $inModel) {
		if ( $this->getAction() == self::ACTION_UPDATE_PROFILE ) {
			$inModel->setUser($this->getRequest()->getSession()->getUser());
			$inModel->getUser()->setFirstname(trim(strip_tags($_POST['Firstname'])));
			$inModel->getUser()->setSurname(trim(strip_tags($_POST['Surname'])));
			$inModel->getUser()->setTerritoryID($inData['territory']);
			$inModel->getUser()->setFacebookID($inData['facebookID']);
			$inModel->getUser()->setAutoCommitStatus($inData['autoCommitStatus']);

			if ( isset($inData['Password']) && strlen($inData['Password']) > 5 ) {
				if ( $inModel->getUser()->hasPassword() ) {
					if ( isset($inData['curPassword']) && md5($inData['curPassword']) == $inModel->getUser()->getPassword() ) {
						$inModel->getUser()->setPassword($inData['Password']);
					} else {
						throw new mofilmException('Your current password does not match what is stored, please enter it.');
					}
				} else {
					$inModel->getUser()->setPassword($inData['Password']);
				}
			}
			
			$inModel->getUser()->getParamSet()->setParam(mofilmUser::PARAM_ADDRESS1, trim(strip_tags($_POST['Address1'])));
			$inModel->getUser()->getParamSet()->setParam(mofilmUser::PARAM_ADDRESS2, trim(strip_tags($_POST['Address2'])));
			$inModel->getUser()->getParamSet()->setParam(mofilmUser::PARAM_CITY, trim(strip_tags($_POST['City'])));
			$inModel->getUser()->getParamSet()->setParam(mofilmUser::PARAM_COUNTY, trim(strip_tags($_POST['County'])));
			$inModel->getUser()->getParamSet()->setParam(mofilmUser::PARAM_POSTCODE, trim(strip_tags($_POST['Postcode'])));
			$inModel->getUser()->getParamSet()->setParam(mofilmUser::PARAM_DESCRIPTION, trim(strip_tags($_POST['Description'])));
			
			if ( isset($_POST["Skills"]) ) {
				$skills = implode(", ", $_POST['Skills']);
				$inModel->getUser()->getParamSet()->setParam(mofilmUser::PARAM_SKILLS, $skills);
			}
			
			if ( isset ($inData['SchoolName']) ) {
				$inModel->getUser()->getParamSet()->setParam(mofilmUser::PARAM_SCHOOL_NAME, $inData['SchoolName']);
			}
			
			$inModel->getUser()->getParamSet()->setParam(mofilmUser::PARAM_OCCUPATION, trim(strip_tags($_POST['Occupation'])));
			$inModel->getUser()->getParamSet()->setParam(mofilmUser::PARAM_COMPANY, trim(strip_tags($_POST['Company'])));
			$inModel->getUser()->getParamSet()->setParam(mofilmUser::PARAM_WEBSITE, trim(strip_tags($_POST['Website'])));
			$inModel->getUser()->getParamSet()->setParam(mofilmUser::PARAM_SHOW_REEL_URL, trim(strip_tags($_POST['ShowReelURL'])));
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
					$dob = $inData['DateOfBirth']['Year'].'-'.$inData['DateOfBirth']['Month'].'-'.$inData['DateOfBirth']['Day'];
					
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
				$oProfile->setActive(mofilmUserProfile::PROFILE_ACTIVE);
				
				$inModel->getUser()->setProfile($oProfile);
			}
			
			/*
			 * Mark everything for deletion so that we can handle empty checkboxes
			 */
			$inModel->getUser()->getProfileMovieSet()->markAllForDeletion();
			if ( isset($inData['ProfileMovies']) && is_array($inData['ProfileMovies']) ) {
				if ( count($inData['ProfileMovies']) > self::MAX_PROFILE_MOVIES ) {
					$inData['ProfileMovies'] = array_slice($inData['ProfileMovies'], 0, self::MAX_PROFILE_MOVIES);
				}
				
				foreach ( $inData['ProfileMovies'] as $index => $movieID ) {
					$oProfileMovie = $inModel->getUser()->getProfileMovieSet()->getObjectByPosition($index+1);
					if ( !$oProfileMovie instanceof mofilmUserProfileMovie ) {
						$oProfileMovie = new mofilmUserProfileMovie();
						$inModel->getUser()->getProfileMovieSet()->setObject($oProfileMovie);
					}
					
					if ( $oProfileMovie->getMovieID() != $movieID ) {
						$oMovie = mofilmMovieManager::getInstanceByID($movieID);
						$oProfileMovie->setTitle($oMovie->getTitle());
					}
					if ( array_key_exists($movieID, $inData['ProfileMovieTitles']) ) {
						$oProfileMovie->setTitle($inData['ProfileMovieTitles'][$movieID]);
					}
					$oProfileMovie->setMovieID($movieID);
					$oProfileMovie->setPosition($index+1);
					$oProfileMovie->setMarkForDeletion(false);
				}
			}
		}
	}
	
	/**
	 * Fetches the model
	 *
	 * @return profileModel
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
		$oModel = new profileModel();
		$oModel->setRequest($this->getRequest());
		$oModel->setLanguage($this->getRequest()->getLocale());
		$this->setModel($oModel);
	}
}