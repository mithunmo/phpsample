<?php
/**
 * uploadTrackController
 *
 * Stored in uploadTrackController.class.php
 * 
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category uploadTrackController
 * @version $Rev: 623 $
 */


/**
 * uploadTrackController
 *
 * uploadTrackController class
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category uploadTrackController
 */
class uploadTrackController extends mvcController {
	
	const ACTION_VIEW = 'view';
	const ACTION_DO_UPLOAD = 'doUpload';
	const ACTION_EDIT = "edit";
	const ACTION_DO_EDIT = "doEdit";
	
	/**
	 * @see mvcControllerBase::initialise()
	 */
	function initialise() {
		parent::initialise();
		
		$this->setDefaultAction(self::ACTION_VIEW);
		$this->getControllerActions()->addAction(self::ACTION_VIEW);
		$this->getControllerActions()->addAction(self::ACTION_DO_UPLOAD);
		$this->getControllerActions()->addAction(self::ACTION_EDIT);
		$this->getControllerActions()->addAction(self::ACTION_DO_EDIT);
	}
	
	/**
	 * @see mvcControllerBase::launch()
	 */
	function launch() {
		
		if ( $this->getAction() == self::ACTION_VIEW ) {
			
			$oView = new uploadTrackView($this);
			$oView->showUploadTrackPage();
		
		} else if ( $this->getAction() == self::ACTION_DO_UPLOAD ) {
			$this->doUploadAction();
		} else if ( $this->getAction() == self::ACTION_EDIT ) {
			$this->getInputManager()->setLookupGlobals(utilityInputManager::LOOKUPGLOBALS_GET);
			$data = $this->getInputManager()->doFilter();
			$oView = new uploadTrackView($this);
			$oView->showMusicEditPage($data["ID"]);
		} else if ( $this->getAction() == self::ACTION_DO_EDIT ) {
			//$this->getInputManager()->setLookupGlobals(utilityInputManager::LOOKUPGLOBALS_GET);
			$data = $this->getInputManager()->doFilter();			
			systemLog::message($data);
			$this->doEditAction($data);
		}
	}
	
	
	/**
	 * Uploads the mp3 to Amazon S3 sever
	 * 
	 * 
	 */
	protected function doUploadAction() {
		
		$data = $this->getInputManager()->doFilter();
		
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

			$oFileUpload->setFileStore(mofilmConstants::getUploadedMusic());
			$oFileUpload->initialise();
			$oFiles = $oFileUpload->process();
			
			systemLog::message( $oFiles->getFirst()->getName() );
			systemLog::message( $oFiles->getFirst()->getOriginalName() );
			systemLog::message( mofilmConstants::getUploadedMusic()."/" . $oFiles->getFirst()->getName() );
						
			
			
			$s3 = new AmazonS3("AKIAI4HCNO3U37FJLVNA", "ug562esrHehmnzGlB1DUv/vpDpBR0rS5AhGrZQHd");
			$bucket = "momusic";
			$s3->putBucket($bucket, AmazonS3::ACL_PUBLIC_READ);
			
			$inSong = mofilmUtilities::removeSpecialChars($data["SongName"]);
			$inArtist = mofilmUtilities::removeSpecialChars($data["ArtistName"]);
			$inAlbum = mofilmUtilities::removeSpecialChars($data["AlbumName"]);
			$path_parts = pathinfo($oFiles->getFirst()->getName());
			$extension = $path_parts["extension"];
			
			systemLog::message($extension);
			
			$dllink = $inArtist . "/". $inAlbum . "/" . $inSong. "." .$extension;
			$fullpath = mofilmConstants::getUploadedMusic() . "/" . $oFiles->getFirst()->getName();
			if($s3->putObjectFile($fullpath, $bucket , $dllink, AmazonS3::ACL_PUBLIC_READ) ) {
				
				
				$oWork = new momusicWork();
				$oWork->setArtistName($data["ArtistName"]);
				$oWork->setAlbum($data["AlbumName"]);
				$oWork->setSongName($data["SongName"]);
				
				$momusicURL = "http://momusic.s3.amazonaws.com/".$inArtist . "/" . $inAlbum . "/" . $inSong. ".".$extension;
				
				$oWork->setPath($momusicURL);
				$oWork->save();
				
				$this->redirect("/admin/other/momusic/uploadTrack/edit?ID=".$oWork->getID());
				
				

			}			
			
			
			
		} catch ( Exception $e ) {
			systemLog::error($e->getMessage());
			$this->getRequest()->getSession()->setStatusMessage($e->getMessage(), mvcSession::MESSAGE_ERROR);
			$response = 'failed';
		}
	}
	
	/**
	 * Saves the music metadata
	 * 
	 */
	function doEditAction($data){
					
					systemLog::message($data);
		
					$oObject = momusicWork::getInstance($data["ID"]);
		
					$context = $data["context"];
					if ( isset($context) ) {
						$oObject->setContext($context);
					}
					
					$album = $data["AlbumName"];
					$oObject->setAlbum($album);

					$genre1 = $data["genre1"];
					if ( isset($genre1)) {
						$oObject->setGenre1($genre1);
					}
					$genre2 = $data["genre2"];
					if ( isset($genre2)) {
						$oObject->setGenre2($genre2);
					}
					$genre3 = $data["genre3"];
					if ( isset($genre3) ) {
						$oObject->setGenre3(strval($genre3));
					}	

					$mood1 = $data["mood1"];
					if ( isset($mood1) ) {
						$oObject->setMood1($mood1);
					}
					$mood2 = $data["mood2"];
					if ( isset($mood2) ) {
						$oObject->setMood2($mood2);
					}
					$mood3 = $data["mood3"];
					if ( isset($mood3) ) {
						$oObject->setMood3($mood3);
					}


					$style1 = $data["style1"];
					if ( isset($style1) ) {
						$oObject->setStyle1($style1);
					}
					$style2 = $data["style2"];
					if ( isset($style2) ) {
						$oObject->setStyle2($style2);
					}
					$style3 = $data["style3"];
					if ( isset($style3) ) {
						$oObject->setStyle3($style3);
					}

					$keyword = $data["keywords"];
					if ( isset($keyword) ) {
						$oObject->setKeywords($keyword);
					}


					$inst1 = $data["inst1"];
					if ( isset($inst1) && is_string($inst1) ) {
						$oObject->setInstrument1($inst1);
					}
					$inst2 = $data["inst2"];
					if ( isset($inst2) && is_string($inst2) ) {
						$oObject->setInstrument2($inst2);
					}
					$inst3 = $data["inst3"];
					if ( isset($inst3) && is_string($inst3) ) {
						$oObject->setInstrument3($inst3);
					}
					$inst4 = $data["inst4"];
					if ( isset($inst4) && is_string($inst4) ) {
						$oObject->setInstrument4($inst4);
					}


					$sounds_like1 = $data["sl1"];
					if ( isset($sounds_like1) ) {
						$oObject->setSoundsLike1($sounds_like1);
					}
					$res_song1 = $data["rs1"];
					if ( isset($res_song1) ) {
						$oObject->setResemblesSong1($res_song1);
					}

					$sounds_like2 = $data["sl2"];
					if ( isset($sounds_like2) ) {
						$oObject->setSoundsLike2(strval($sounds_like2));
					}
					$res_song2 = $data["rs2"];
					if ( isset($res_song2) ) {
						$oObject->setResemblesSong2(strval($res_song2));
					}

					$sounds_like3 = $data["sl3"];
					if ( isset($sounds_like3) ) {
						$oObject->setSoundsLike3($sounds_like3);
					}
					
					$res_song3 = $data["rs3"];
					if ( isset($res_song3) ) {
						$oObject->setResemblesSong3(strval($res_song3));
					}
					
					$composer = $data["composer"];
					if ( isset($composer) && strlen($composer) > 0  ) {
						$oObject->setComposer($composer);
					}
					
					$writer = $data["writer"];
					if ( isset($writer) && strlen($writer) > 0  ) {
						$oObject->setWriter($writer);
					}
					
					$path = $oObject->getPath();
					$time = exec("ffmpeg -i " . escapeshellarg($path) . " 2>&1 | grep 'Duration' | cut -d ' ' -f 4 | sed s/,//");
					list($hms, $milli) = explode('.', $time);
					list($hours, $minutes, $seconds) = explode(':', $hms);
					$total_seconds = ($hours * 3600) + ($minutes * 60) + $seconds;
					$oObject->setDuration($total_seconds);
					
					$publisher = $data["publisher"];
					$oObject->setPublisher(strval($publisher));				
					$desc = $data["desc"];
					$oObject->setDescription($desc);
					$oObject->setStatus($data["status"]);
					$oObject->setPriority($data["priority"]);
					$oObject->setMusicSource($data["musicsource"]);			
					$oObject->save();
					$this->redirect("/admin/other/momusic/uploadTrack/");
					
		
	}
	
	
	/**
	 * @see mvcControllerBase::addInputFilters()
	 */
	function addInputFilters() {
		$this->getInputManager()->addFilter('ID', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('SongName', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('ArtistName', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('AlbumName', utilityInputFilter::filterString());
		
		
		$this->getInputManager()->addFilter('mood1', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('mood2', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('mood3', utilityInputFilter::filterString());
		

		$this->getInputManager()->addFilter('genre1', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('genre2', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('genre3', utilityInputFilter::filterString());
		
		
		$this->getInputManager()->addFilter('style1', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('style2', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('style3', utilityInputFilter::filterString());


		
		$this->getInputManager()->addFilter('inst1', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('inst2', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('inst3', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('inst4', utilityInputFilter::filterString());
		
		$this->getInputManager()->addFilter('sl1', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('sl2', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('sl3', utilityInputFilter::filterString());
		
		$this->getInputManager()->addFilter('rs1', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('rs2', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('rs3', utilityInputFilter::filterString());
		
		$this->getInputManager()->addFilter('writer', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('composer', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('publisher', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('musicsource', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('status', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('priority', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('desc', utilityInputFilter::filterString());
		$this->getInputManager()->addFilter('keywords', utilityInputFilter::filterString());
		
	}
	
	/**
	 * @see mvcControllerBase::addInputToModel()
	 */
	function addInputToModel($inData, $inModel) {
		
	}
	
	/**
	 * Fetches the model
	 *
	 * @return uploadTrackModel
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
		$oModel = new uploadTrackModel();
		$this->setModel($oModel);
	}
}