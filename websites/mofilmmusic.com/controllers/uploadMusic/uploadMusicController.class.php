<?php

/**
 * uploadMusicController
 *
 * Stored in uploadMusicController.class.php
 * 
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package websites_mofilmmusic.com
 * @subpackage controllers
 * @category uploadMusicController
 * @version $Rev: 736 $
 */

/**
 * uploadMusicController
 *
 * uploadMusicController class
 * 
 * @package websites_mofilmmusic.com
 * @subpackage controllers
 * @category uploadMusicController
 */
class uploadMusicController extends mvcController {
	const ACTION_VIEW = 'view';
	const ACTION_DO_PL_UPLOAD = "doplupload";
	const ACTION_DO_BASIC_SAVE = "doBasicSave";
	const ACTION_DONE = "done";

	/**
	 * @see mvcControllerBase::initialise()
	 */
	function initialise() {
		parent::initialise();

		$this->setDefaultAction(self::ACTION_VIEW);

		$this->setRequiresAuthentication(false);

		$this->getControllerActions()->addAction(self::ACTION_VIEW);
		$this->getControllerActions()->addAction(self::ACTION_DO_PL_UPLOAD);
		$this->getControllerActions()->addAction(self::ACTION_DO_BASIC_SAVE);
		$this->getControllerActions()->addAction(self::ACTION_DONE);
	}

	/**
	 * @see mvcControllerBase::launch()
	 */
	function launch() {

		switch ( $this->getAction() ) {
			case self::ACTION_DO_PL_UPLOAD: $this->doPlUploadAction();
				break;
			case self::ACTION_DO_BASIC_SAVE: $this->doBasicSaveAction();
				break;
			case self::ACTION_DONE: $this->doneAction();
				break;
			default:
				$this->uploadAction();
				break;
		}
	}

	function doneAction() {
		if ( $this->getRequest()->getSession()->isLoggedIn() ) {
			$oView = new uploadMusicView($this);
			$oView->showDoneMusicPage();
		} else {
			$this->redirect("/account/authorise?redirect=/uploadMusic");
		}
	}

	function uploadAction() {

		if ( $this->getRequest()->getSession()->isLoggedIn() ) {
			$oView = new uploadMusicView($this);
			$oView->showUploadMusicPage();
		} else {
			$this->redirect("/account/authorise?redirect=/uploadMusic");
		}
	}

	/**
	 * Does the basic save of a video with out credits,tag and music license
	 * 
	 * 
	 */
	function doBasicSaveAction() {


		$data = $this->getInputManager()->doFilter();
		systemLog::message($data);
		if ( strlen($data["fileName"]) > 2 ) {

			$targetDir = mofilmConstants::getUploadedMusic();


			$s3 = new AmazonS3("AKIAJ2BVKYYHHMXARDDQ", "RqrpvkP22U//T4m6ND8fARVhxRyPvoUqQnZTWa7b");
			$bucket = "momusic-contest";
			$s3->putBucket($bucket, AmazonS3::ACL_PUBLIC_READ);

			$inSong = mofilmUtilities::removeSpecialChars($data["Title"]);

			$event = mofilmEvent::getInstance($data["EventID"])->getName();
			$source = mofilmSource::getInstance($data["sourceID"])->getName();

			$inArtist = mofilmUtilities::removeSpecialChars($event);

			$inAlbum = mofilmUtilities::removeSpecialChars($source);


			//$path_parts = pathinfo($oFiles->getFirst()->getName());
			systemLog::message($targetDir . "/" . $data["fileName"]);

			$path_parts = pathinfo($targetDir . "/" . $data["fileName"]);
			$extension = $path_parts["extension"];

			systemLog::message($extension);

			$dllink = $inArtist . "/" . $inAlbum . "/" . $inSong . "." . $extension;
			$fullpath = mofilmConstants::getUploadedMusic() . "/" . $data["fileName"];
			if ( $s3->putObjectFile($fullpath, $bucket, $dllink, AmazonS3::ACL_PUBLIC_READ) ) {


				$oMusic = new momusicMusic();
				$oMusic->setName($data["Title"]);
				$oMusic->setUserID($this->getRequest()->getSession()->getUser()->getID());
				$oMusic->setDescription($data["Description"]);
				


				
				$path = "http://momusic-contest.s3.amazonaws.com/" . $inArtist . "/" . $inAlbum . "/" . $inSong . "." . $extension;
				$time = exec("ffmpeg -i " . escapeshellarg($path) . " 2>&1 | grep 'Duration' | cut -d ' ' -f 4 | sed s/,//");
				list($hms, $milli) = explode('.', $time);
				list($hours, $minutes, $seconds) = explode(':', $hms);
				$total_seconds = ($hours * 3600) + ($minutes * 60) + $seconds;
				$oMusic->setDuration($total_seconds);
				$oMusic->setPath($path);
				systemLog::message($path);
				
				$oMusic->save();
				
				$oMusicSource = new momusicSources();
				$oMusicSource->setEventID($data["EventID"]);
				$oMusicSource->setSourceID($data["sourceID"]);
				$oMusicSource->setMusicID($oMusic->getID());
				$oMusicSource->save();
				
				$this->redirect("/uploadMusic/done");
			}
		}
	}

	function doPlUploadAction() {



		$oUser = $this->getRequest()->getSession()->getUser();

		//$targetDir = mofilmConstants::getVideoFolder() . DIRECTORY_SEPARATOR . "plupload";
		$targetDir = mofilmConstants::getUploadedMusic();


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
	}

	/**
	 * @see mvcControllerBase::addInputToModel()
	 */
	function addInputToModel($inData, $inModel) {
		
	}

	/**
	 * Fetches the model
	 *
	 * @return uploadMusicModel
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
		$oModel = new uploadMusicModel();
		$this->setModel($oModel);
	}

}