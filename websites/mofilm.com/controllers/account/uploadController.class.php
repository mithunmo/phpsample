<?php

/**
 * uploadController
 *
 * Stored in uploadController.class.php
 * 
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package websites_mofilm.com
 * @subpackage controllers
 * @category uploadController
 * @version $Rev: 393 $
 */

/**
 * uploadController
 *
 * Handles file uploads
 * 
 * @package websites_mofilm.com
 * @subpackage controllers
 * @category uploadController
 */
class uploadController extends mvcController {
	const ACTION_UPLOAD = 'upload';
	const ACTION_UPLOAD_NO_JS = 'uploadNojs';
	const ACTION_DO_UPLOAD = 'doUpload';
	const ACTION_MOVIE_SAVE = 'doMovieSave';
	const ACTION_GET_SOURCE = 'eventSources';
	const ACTION_SEARCH_TAG = 'searchTag';
	const ACTION_PL_UPLOAD = "plupload";
	const ACTION_DO_PL_UPLOAD = "doplupload";
	const ACTION_UPLOAD_PHOTO = "uploadPhoto";
	const ACTION_DO_UPLOAD_PHOTO_SAVE = "doUploadPhotoSave";
	const ACTION_DO_BASIC_SAVE = "doBasicSave";

	/**
	 * @see mvcControllerBase::initialise()
	 */
	function initialise() {
		parent::initialise();
		
		if ( ( $this->getAction() == self::ACTION_MOVIE_SAVE || $this->getAction() == self::ACTION_DO_BASIC_SAVE ) && !$this->getRequest()->getSession()->isLoggedIn() ) {
			$this->redirect("/account/upload");
		}	
		
		$this->setDefaultAction(self::ACTION_UPLOAD);
		$this->setRequiresAuthentication(true);
		$this->getControllerActions()
			->addAction(self::ACTION_UPLOAD)
			->addAction(self::ACTION_MOVIE_SAVE)
			->addAction(self::ACTION_UPLOAD_NO_JS)
			->addAction(self::ACTION_GET_SOURCE)
			->addAction(self::ACTION_DO_UPLOAD)
			->addAction(self::ACTION_SEARCH_TAG)
			->addAction(self::ACTION_PL_UPLOAD)
			->addAction(self::ACTION_DO_PL_UPLOAD)
			->addAction(self::ACTION_UPLOAD_PHOTO)
			->addAction(self::ACTION_DO_UPLOAD_PHOTO_SAVE)
			->addAction(self::ACTION_DO_BASIC_SAVE);

		$this->addInputFilters();
	}

	/**
	 * @see mvcControllerBase::launch()
	 */
	function launch() {
		switch ( $this->getAction() ) {
			case self::ACTION_DO_UPLOAD: $this->doUploadAction();
				break;
			case self::ACTION_MOVIE_SAVE: $this->doMovieSaveAction();
				break;
			case self::ACTION_UPLOAD_NO_JS: $this->doUploadNoJSAction();
				break;
			case self::ACTION_GET_SOURCE: $this->doGetSourceAction();
				break;
			case self::ACTION_SEARCH_TAG: $this->doAutocompleteSearchTag();
				break;
			case self::ACTION_PL_UPLOAD: $this->doPUploadAction();
				break;
			case self::ACTION_DO_PL_UPLOAD: $this->doPlUploadAction();
				break;
			case self::ACTION_UPLOAD_PHOTO: $this->doUploadPhoto();
				break;
			case self::ACTION_DO_UPLOAD_PHOTO_SAVE: $this->doUploadPhotoSave();
				break;
			case self::ACTION_DO_BASIC_SAVE: $this->doBasicSaveAction();
				break;

			default:
				$this->uploadAction();
				break;
		}
	}

	/**
	 * Shows the movie upload page
	 * 
	 * @return void
	 */
	protected function uploadAction() {
		$oUser = $this->getRequest()->getSession()->getUser();
		if ( $oUser->getFirstname() != null && $oUser->getSurname() != null && $oUser->getProfile()->getActive() != mofilmUserProfile::PROFILE_DISABLED && $oUser->getParamSet()->getParam('Phone') != null 
			&& $oUser->getParamSet()->getParam('Skills') != null  ) {
			$this->getInputManager()->setLookupGlobals(utilityInputManager::LOOKUPGLOBALS_GET);
			$this->addInputFilters();
			$data = $this->getInputManager()->doFilter();

			$this->addInputToModel($data, $this->getModel());
			$oView = new uploadView($this);
			$oView->showUploadPage();
		} else {
			$oView = new uploadView($this);
			$oView->showUploadCompletePage();
		}
	}

	/**
	 * SHows the mofilm basic uploader
	 * 
	 */
	function doPUploadAction() {
		$oView = new uploadView($this);
		$oView->showPlUpload();
	}
	
	/**
	 * Shows the mofilm photo uploader
	 * 
	 */
	function doUploadPhoto() {
		$this->getInputManager()->setLookupGlobals(utilityInputManager::LOOKUPGLOBALS_GET);
		$this->addInputFilters();
		$data = $this->getInputManager()->doFilter();

		$this->addInputToModel($data, $this->getModel());

		$oView = new uploadView($this);
		$oView->showUploadPhoto();
	}

	/**
	 * 
	 */
	function doUploadPhotoSave() {
		$this->addInputFilters();
		$inData = $this->getInputManager()->doFilter();

		if ( count($_FILES['Photos']) > 0 ) {
			$this->addInputToModel($inData, $this->getModel());
			$inUserID = $this->getRequest()->getSession()->getUser()->getID();
			
			$movieID = $this->getModel()->savePhoto($inData, $inUserID);
			
			if ( $movieID ) {
				$this->__UploadPhoto($inData, $movieID, $inUserID);
			}
			
			$this->redirect("/account/myVideo");
		} else {
			$this->getRequest()->getSession()->setStatusMessage('Upload Problem. Try again later.', mvcSession::MESSAGE_ERROR);
			$this->redirect("/account/myVideo");
		}
		
	}

	/**
	 * Shows the page when there in no js
	 * 
	 * @return void
	 */
	protected function doUploadNoJSAction() {
		$oView = new uploadView($this);
		$oView->showUploadNoJSPage();
	}

	/**
	 * Does the basic save of a video with out credits,tag and music license
	 * 
	 * 
	 */
	function doBasicSaveAction() {

		$inData = $this->getInputManager()->doFilter();
		systemLog::message($inData);
		if ( strlen($inData["fileName"]) > 2 ) {
			$inUserID = $this->getRequest()->getSession()->getUser()->getID();
			
			$oUserTerms = new mofilmUserTerms();
			$oUserTerms->setUserID($inUserID);
			$oUserTerms->setTermsID(0);
			$oUserTerms->save();
			
			

			$oMovie = new mofilmMovie();
			$oMovie->setUserID($inUserID);
			$oMovie->setLongDesc($inData["Description"]);
			$oMovie->setShortDesc($inData["Title"]);

			if ( !empty($inData["Duration"]) ) {
				systemLog::message("here");
				$oMovie->setRuntime($inData["Duration"]);
			}

			$oMovie->setCredits("No credit");

			if ( mofilmUserManager::getInstanceByID($inUserID)->getAutoCommitStatus() == mofilmUserBase::AUTO_COMMIT_STATUS_DISABLED ) {
				$oMovie->setActive(mofilmMovie::ACTIVE_Y);
			} else {
				$oMovie->setActive(mofilmMovie::ACTIVE_Y);
			}
			$oMovie->save();

			$oMovie->getSourceSet()->setObject(mofilmSource::getInstance($inData["sourceID"]));

			$oMofilmMovieAsset = new mofilmMovieAsset();
			$oMofilmMovieAsset->setMovieID($oMovie->getID());
			$originalPath = mofilmConstants::getVideoFolder() . "/plupload" . "/" . $inData["fileName"];
			$finalPath = "/share/content/_platform" . "/" . $oMovie->getID() . "/" . $inData["fileName"];
			mkdir("/share/content/_platform" . "/" . $oMovie->getID(), 0755, true);
			copy($originalPath, $finalPath);
			unlink($originalPath);
			$oMofilmMovieAsset->setFilename($finalPath);
			$oMofilmMovieAsset->setType(mofilmMovieAsset::TYPE_SOURCE);
			$oMofilmMovieAsset->setHeight(0);
			$oMofilmMovieAsset->setWidth(0);
			$path_parts = pathinfo($inData["fileName"]);
			$oMofilmMovieAsset->setExt("mov");
			$oMofilmMovieAsset->setDescription("source");
			$oMovie->getAssetSet()->setObject($oMofilmMovieAsset);


			$oMovie->getDataSet()->setMovieID($oMovie->getID());
			$oMovie->getDataSet()->setProperty(mofilmDataname::DATA_USER_IP, $_SERVER["REMOTE_ADDR"]);
			$oMovie->getDataSet()->setProperty(mofilmDataname::DATA_USER_COUNTRY_CODE, $oMovie->getDataSet()->getUserCountryCode());
			$oMovie->getDataSet()->setProperty(mofilmDataname::DATA_MOVIE_LICENSEID, $inData["customLicense"]);

			$oMovie->save();

			$oUploadQueue = new mofilmUploadQueue();
			$oUploadQueue->setMovieID($oMovie->getID());
			$oUploadQueue->setUserID($oMovie->getUserID());
			$oUploadQueue->setStatus(mofilmUploadQueue::STATUS_QUEUED);
			$oUploadQueue->save();

			$tags[] = mofilmEvent::getInstance($inData["EventID"])->getName();
			$tags[] = mofilmSource::getInstance($inData["sourceID"])->getName();
			$tags[] = date("Y");
			$tags[] = $inData["Title"];
			$unique_tags = $this->getModel()->array_iunique($tags);
			foreach ( $unique_tags as $tag ) {
				if ( trim($tag) ) {
					$oTagID = mofilmTag::getInstanceByTag(trim($tag))->getID();
					if ( $oTagID == 0 ) {
						$oTag = new mofilmTag();
						$oTag->setName(substr(trim($tag), 0, 40));
						$oTag->setType(mofilmTag::TYPE_TAG);
						$oTag->save();

						$oTagID = mofilmTag::getInstanceByTag(trim($tag))->getID();
					}

					if ( $oTagID ) {
						$oMovieTagSet[] = mofilmTag::getInstance($oTagID);
					}
				}
			}

			$oMovieTag = new mofilmMovieTagSet($oMovie->getID());
			$oMovieTag->setObjects($oMovieTagSet);
			$oMovieTag->save();
			$this->getModel()->sendEmail($inUserID, $oMovie->getID(), $oMovie->getSource()->getEvent()->getName(), $oMovie->getSource()->getName());			
			$this->getModel()->linkUserMovieGrants($inData["sourceID"], $inUserID, $oMovie->getID());
			$this->redirect("/account/myVideo");
		} else {
			systemLog::message("_________________ upload problem ____________________");
			$this->redirect("/account/myVideo");
		}
	}

	function doPlUploadAction() {

		$oUser = $this->getRequest()->getSession()->getUser();

		$targetDir = mofilmConstants::getVideoFolder() . DIRECTORY_SEPARATOR . "plupload";
		$cleanupTargetDir = true; // Remove old files
		$maxFileAge = 5 * 3600; // Temp file age in seconds

		$chunk = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 0;
		$chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 0;
		$fileName = isset($_REQUEST["name"]) ? $_REQUEST["name"] : '';

		$fileName = preg_replace('/[^\w\._]+/', '_', $fileName);


		if ( $chunks < 2 && file_exists($targetDir . DIRECTORY_SEPARATOR . $fileName) ) {
			$ext = strrpos($fileName, '.');
			$fileName_a = substr($fileName, 0, $ext);
			$fileName_b = substr($fileName, $ext);

			$count = 1;
			while ( file_exists($targetDir . DIRECTORY_SEPARATOR . $fileName_a . '_' . $count . $fileName_b) )
				$count++;

			$fileName = $fileName_a . '_' . $count . $fileName_b;
		}

		$filePath = $targetDir . DIRECTORY_SEPARATOR . $fileName;

		if ( !file_exists($targetDir) )
			@mkdir($targetDir);

		if ( $cleanupTargetDir && is_dir($targetDir) && ($dir = opendir($targetDir)) ) {
			while ( ($file = readdir($dir)) !== false ) {
				$tmpfilePath = $targetDir . DIRECTORY_SEPARATOR . $file;

				// Remove temp file if it is older than the max age and is not the current file
				if ( preg_match('/\.part$/', $file) && (filemtime($tmpfilePath) < time() - $maxFileAge) && ($tmpfilePath != "{$filePath}.part") ) {
					@unlink($tmpfilePath);
				}
			}

			closedir($dir);
		}

		if ( isset($_SERVER["HTTP_CONTENT_TYPE"]) )
			$contentType = $_SERVER["HTTP_CONTENT_TYPE"];

		if ( isset($_SERVER["CONTENT_TYPE"]) )
			$contentType = $_SERVER["CONTENT_TYPE"];

		if ( strpos($contentType, "multipart") !== false ) {
			if ( isset($_FILES['file']['tmp_name']) && is_uploaded_file($_FILES['file']['tmp_name']) ) {
				// Open temp file
				$out = fopen("{$filePath}.part", $chunk == 0 ? "wb" : "ab");
				if ( $out ) {
					// Read binary input stream and append it to temp file
					$in = fopen($_FILES['file']['tmp_name'], "rb");

					if ( $in ) {
						while ( $buff = fread($in, 4096) )
							fwrite($out, $buff);
					} else
						systemLog::message("101 Failed to open input stream.");
					fclose($in);
					fclose($out);
					@unlink($_FILES['file']['tmp_name']);
				} else
					systemLog::message("102 Failed to open output stream.");
			} else
				systemLog::message("103 Failed to move uploaded file.");
		}

		if ( !$chunks || $chunk == $chunks - 1 ) {
			// Strip the temp .part suffix off 
			rename("{$filePath}.part", $filePath);
			systemLog::message("done file" . $filePath);
		}
	}

	/**
	 * Handles file uploads
	 * 
	 * @return void
	 */
	/*
	  protected function doUploadAction() {
	  $oUser = $this->getRequest()->getSession()->getUser();
	  $chunk = $_REQUEST["chunk"];
	  $chunks = $_REQUEST["chunks"];
	  $name = $_REQUEST['name'];
	  $inSuffix = "Part".$chunk;
	  $uploaderType = $_REQUEST['uploaderType'];
	  $response = 'success';
	  systemLog::message($_FILES);
	  systemLog::message($_REQUEST);
	  try {
	  $oFileUpload = new mvcFileUpload(
	  array(
	  mvcFileUpload::OPTION_AUTO_CREATE_FILESTORE => false,
	  mvcFileUpload::OPTION_CHECK_PERMISSIONS => false,
	  mvcFileUpload::OPTION_FIELD_NAME => 'file',
	  mvcFileUpload::OPTION_SUB_FOLDER_FORMAT => '',
	  mvcFileUpload::OPTION_WRITE_IMMEDIATE => true,
	  mvcFileUpload::OPTION_STORE_RAW_DATA => false,
	  mvcFileUpload::OPTION_USE_ORIGINAL_NAME => false
	  )
	  );

	  $oFileUpload->setAddFilenameSuffix($inSuffix);
	  $oFileUpload->setAddFilenamePrefix($oUser->getID().$name);
	  $oFileUpload->setFileStore(mofilmConstants::getVideoFolder());
	  $oFileUpload->initialise();
	  $oFiles = $oFileUpload->process();

	  } catch ( Exception $e ) {
	  systemLog::error($e->getMessage());
	  $this->getRequest()->getSession()->setStatusMessage($e->getMessage(), mvcSession::MESSAGE_ERROR);
	  $response = 'failed';
	  }
	  $chunks--;
	  if ( intval($chunks) == intval($chunk) ) {
	  $this->getModel()->setFileName($name);
	  $this->getModel()->setUserID($oUser->getID());
	  $this->getModel()->setUploaderType($uploaderType);
	  $this->getModel()->mergeVideoFile();
	  }

	  }
	 */

	protected function doUploadAction() {
		systemLog::message("==========================================Uplaoder   ===========================================" . $this->getRequest()->getSession()->getUser()->getID());
		$response = "success";
		try {
			$oFileUpload = new mvcFileUpload(
					array(
						mvcFileUpload::OPTION_AUTO_CREATE_FILESTORE => false,
						mvcFileUpload::OPTION_CHECK_PERMISSIONS => false,
						mvcFileUpload::OPTION_FIELD_NAME => 'Files',
						mvcFileUpload::OPTION_SUB_FOLDER_FORMAT => '',
						mvcFileUpload::OPTION_WRITE_IMMEDIATE => true,
						mvcFileUpload::OPTION_STORE_RAW_DATA => false,
						mvcFileUpload::OPTION_USE_ORIGINAL_NAME => false
					)
			);

			//$oFileUpload->setAddFilenameSuffix($inSuffix);
			//$oFileUpload->setAddFilenamePrefix($oUser->getID().$name);
			$oFileUpload->setFileStore(mofilmConstants::getVideoFolder());
			$oFileUpload->initialise();
			$oFiles = $oFileUpload->process();
			systemLog::message("inside request" . $oFiles->getFirst()->getName());
			systemLog::message($oFiles->getFirst()->getOriginalName());
			echo $oFiles->getFirst()->getName();
		} catch ( Exception $e ) {
			systemLog::error($e->getMessage());
			$this->getRequest()->getSession()->setStatusMessage($e->getMessage(), mvcSession::MESSAGE_ERROR);
			$response = 'failed';
			//echo $response;
		}
	}

	/**
	 * Handles the movie saving part
	 * 
	 * @return void
	 */
	function doMovieSaveAction() {
		$this->addInputFilters();
		$data = $this->getInputManager()->doFilter();
		systemLog::message($data);
		if ( isset($data["fileName"]) && strlen($data["fileName"]) > 2 ) {
			$this->addInputToModel($data, $this->getModel());
			$movieID = $this->getModel()->saveMovie($data, $this->getRequest()->getSession()->getUser()->getID());
			$this->redirect("/account/myVideo");
		} else {
			systemLog::message("_________________ upload problem ____________________");
			$this->redirect("/account/upload");			
		}
	}

	/**
	 * Gets the sources for a particuar event
	 * 
	 * @return void
	 */
	function doGetSourceAction() {
		$this->getInputManager()->setLookupGlobals(utilityInputManager::LOOKUPGLOBALS_GET);
		$data = $this->getInputManager()->doFilter();
		$this->addInputToModel($data, $this->getModel());
		$oView = new uploadView($this);
		$oView->showGetEventSources();
	}

	/**
	 * Gets the tags list matching the alphabets
	 * 
	 * @return void
	 */
	function doAutocompleteSearchTag() {
		$this->getInputManager()->setLookupGlobals(utilityInputManager::LOOKUPGLOBALS_GET);
		$data = $this->getInputManager()->doFilter();
		$this->addInputToModel($data, $this->getModel());
		$oView = new uploadView($this);
		$oView->showAutocompleteSearchTag();
		return;
	}
	
	protected function __UploadPhoto($inData, $inMovieID, $inUserID) {
		$return = true;

		$oFileUpload = new mvcFileUpload(
			array(
				mvcFileUpload::OPTION_AUTO_CREATE_FILESTORE => false,
				mvcFileUpload::OPTION_CHECK_PERMISSIONS => false,
				mvcFileUpload::OPTION_FIELD_NAME => 'Photos',
				mvcFileUpload::OPTION_SUB_FOLDER_FORMAT => '',
				mvcFileUpload::OPTION_WRITE_IMMEDIATE => false,
				mvcFileUpload::OPTION_STORE_RAW_DATA => true,
			)
		);

		try {
			$oFileUpload->initialise();
			$oFileUpload->process();
			$oFiles = $oFileUpload->getUploadedFiles();

			if ( $oFiles->getCount() > 0 ) {
				/* @var mvcFileObject $oFile */
				systemLog::notice('------------- Photo Upload By User : '.$inUserID.' -------------');
				$path = "/share/content/_platform".DIRECTORY_SEPARATOR.$inMovieID;
				$thumbspath = $path.DIRECTORY_SEPARATOR."thumbs";
				mkdir($thumbspath, 0755, true);
				$oMovie = new mofilmMovie($inMovieID);
				$i = 0;
				foreach ( $oFiles as $oFile ) {
					if ( $oFile instanceof mvcFileObject ) {
						systemLog::info('Checking file target location');
						$finalPath = $path.DIRECTORY_SEPARATOR.$oFile->getName();
						$dbpath = str_replace('/share/content', '/resources', $finalPath);
						$bytes = file_put_contents($finalPath, $oFile->getRawFileData());
						systemLog::notice("Wrote $bytes bytes to the file system for filename ".$inMovieID.'/'.$oFile->getName());
						
						$oMofilmMovieAsset = new mofilmMovieAsset();
						$oMofilmMovieAsset->setMovieID($inMovieID);
						$oMofilmMovieAsset->setFilename($dbpath);
						$oMofilmMovieAsset->setType(mofilmMovieAsset::TYPE_SOURCE);
						$oMofilmMovieAsset->setHeight(0);
						$oMofilmMovieAsset->setWidth(0);
						$oMofilmMovieAsset->setExt("JPG");
						$oMofilmMovieAsset->setDescription("source");
						if ( isset ($inData['Caption'][$i]) ) {
							$oMofilmMovieAsset->setNotes($inData['Caption'][$i]);
						}
						$i++;
						$oMovie->getAssetSet()->setObject($oMofilmMovieAsset);
						$oMovie->save();
						
						$oImageConv = new imageConvertor(
							array(
								imageConvertor::OPTION_OUTPUT_FILENAME => $oFile->getName(),
								imageConvertor::OPTION_OUTPUT_OVERWRITE_FILES => true,
							)
						);

						$images = array(
								imageConvertor::OPTION_OUTPUT_LOCATION => $thumbspath,
								imageConvertor::OPTION_OUTPUT_FORMAT => 'jpeg',
								imageConvertor::OPTION_OUTPUT_QUALITY => 90,
								imageConvertor::OPTION_OUTPUT_WIDTH => 100,
								imageConvertor::OPTION_OUTPUT_HEIGHT => 100,
								imageConvertor::OPTION_OUTPUT_PAD_IMAGE => true,
								imageConvertor::OPTION_OUTPUT_PAD_COLOUR => 'white',
						);

						$oImageConv->setOptions($images);
						$oImageConv->process($oFile->getRawFileData());
					}
				}
				
				$oMofilmMovieAsset = new mofilmMovieAsset();
				$oMofilmMovieAsset->setMovieID($inMovieID);
				$oMofilmMovieAsset->setType(mofilmMovieAsset::TYPE_THUMBNAIL);
				$oMofilmMovieAsset->setExt("JPG");
				$oMofilmMovieAsset->setDescription("ThumbNail_150x84");
				$oMofilmMovieAsset->setWidth(150);
				$oMofilmMovieAsset->setHeight(84);
				$oMofilmMovieAsset->setCdnURL(str_replace($inMovieID, $inMovieID.'/thumbs', $dbpath));
				$oMovie->getAssetSet()->setObject($oMofilmMovieAsset);
				$oMovie->save();
				
			}
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
		
		return $return;
	}

	/**
	 * @see mvcControllerBase::addInputFilters()
	 */
	function addInputFilters() {
		$this->getInputManager()->addFilter("Title", utilityInputFilter::filterString());
		$this->getInputManager()->addFilter("Description", utilityInputFilter::filterString());
		$this->getInputManager()->addFilter("EventID", utilityInputFilter::filterString());
		$this->getInputManager()->addFilter("sourceID", utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter("shortCredit", utilityInputFilter::filterString());
		$this->getInputManager()->addFilter("Duration", utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter("fileName", utilityInputFilter::filterString());
		$this->getInputManager()->addFilter("LicenseID", utilityInputFilter::filterStringArray());
		$this->getInputManager()->addFilter("customLicense", utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('Contributors', utilityInputFilter::filterStringArray());

		$this->getInputManager()->addFilter("Tags", utilityInputFilter::filterStringArray());
		$this->getInputManager()->addFilter("mofilmMovieTags", utilityInputFilter::filterString());
		
		if ( $this->getAction() == self::ACTION_DO_UPLOAD_PHOTO_SAVE ) {
			$this->getInputManager()->addFilter("Caption", utilityInputFilter::filterStringArray());
		}
	}

	/**
	 * @see mvcControllerBase::addInputToModel()
	 *
	 * @param array $inData
	 * @param uploadModel $inModel
	 */
	function addInputToModel($inData, $inModel) {
		$inModel->setEventID($inData["EventID"]);
		$inModel->setAutocompleteSearhTag($inData["tag"]);
		//$inModel->setMovieTag($inData["mofilmMovieTags"]);
		$inModel->setSourceID($inData["sourceID"]);
	}

	/**
	 * Fetches the model
	 *
	 * @return uploadModel
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
		$oModel = new uploadModel();
		$oModel->setRequest($this->getRequest());
		$this->setModel($oModel);
	}

}
