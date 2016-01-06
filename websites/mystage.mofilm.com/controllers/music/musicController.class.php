<?php

/**
 * musicController
 *
 * Stored in musicController.class.php
 * 
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package websites_my.mofilm.com
 * @subpackage controllers
 * @category musicController
 * @version $Rev: 736 $
 */

/**
 * musicController
 *
 * musicController class
 * 
 * @package websites_my.mofilm.com
 * @subpackage controllers
 * @category musicController
 */
class musicController extends mvcController {
	const ACTION_VIEW = 'view';
	const ACTION_UPLOAD = 'upload';
	const ACTION_MEDIA = "media";
	const ACTION_UPLOAD_AUDIO = 'uploadAudio';
	const ACTION_LICENSE = 'license';
	const ACTION_UPlOAD_LATER = 'afterUpload';
	const ACTION_SEARCH = "search";
	const ACTION_SEARCH_XML = "searchXML";
	const ACTION_CLEAR_WS = "clear";
	const ACTION_MUSIC_DOWNLOAD = "download";
	

	const PAGING_VAR_OFFSET = 'offset';
	const PAGING_VAR_LIMIT = 'limit';
	const PAGING_DEFAULT_LIMIT = 20;
	

	
	/**
	 * Stores $_PagingOptions
	 *
	 * @var array
	 * @access protected
	 */
	protected $_PagingOptions;
	
	
	/**
	 * @see mvcControllerBase::initialise()
	 */
	function initialise() {
		parent::initialise();

		$this->setDefaultAction(self::ACTION_VIEW);
		$this->setRequiresAuthentication(false);

		$this->getControllerActions()->addAction(self::ACTION_VIEW);
		$this->getControllerActions()->addAction(self::ACTION_UPLOAD);
		$this->getControllerActions()->addAction(self::ACTION_MEDIA);
		$this->getControllerActions()->addAction(self::ACTION_UPLOAD_AUDIO);
		$this->getControllerActions()->addAction(self::ACTION_LICENSE);
		$this->getControllerActions()->addAction(self::ACTION_UPlOAD_LATER);
		$this->getControllerActions()->addAction(self::ACTION_SEARCH);
		$this->getControllerActions()->addAction(self::ACTION_SEARCH_XML);
		$this->getControllerActions()->addAction(self::ACTION_CLEAR_WS);
		$this->getControllerActions()->addAction(self::ACTION_MUSIC_DOWNLOAD);
	}

	/**
	 * @see mvcControllerBase::launch()
	 */
	function launch() {
		if ( $this->getAction() == self::ACTION_VIEW ) {
			$this->getInputManager()->setLookupGlobals(utilityInputManager::LOOKUPGLOBALS_GET);
			$data = $this->getInputManager()->doFilter();
			if ( isset($data['Offset']) && $data['Offset'] >= 0 ) {
				$offset = $data['Offset'];
				$limit = (isset($data['Limit']) && $data['Limit'] > 0) ? $data['Limit'] : self::PAGING_DEFAULT_LIMIT;
			} else {
				$offset = 0;
				$limit = self::PAGING_DEFAULT_LIMIT;				
			}

			$this->setPagingOptions(
				array(
					self::PAGING_VAR_OFFSET => $offset,
					self::PAGING_VAR_LIMIT => $limit
				)
			);
			
			$this->getModel()->getUserSearch()->setLimit($limit);
			$this->getModel()->getUserSearch()->setOffset($offset);			

			$oView = new musicView($this);
			$oView->showMusicPage();
		} elseif ( $this->getAction() == self::ACTION_CLEAR_WS ) {
			
			$response = file_get_contents(mofilmConstants::getWebResourcesFolder()."/xml/media_video_".$this->getRequest()->getSession()->getUser()->getID().".xml");
			$oXML = simplexml_load_string($response);	

			foreach ( $oXML as $inXML ) {		
						systemLog::message($inXML["id"]);
						$oSyncMovie = momusicSyncMovies::getInstanceByUniqID($inXML["id"]);
						systemLog::message("path".$oSyncMovie->getPath());
						$dom=dom_import_simplexml($inXML);
						$dom->parentNode->removeChild($dom);
						$oSyncMovie->setStatus(2);
						$oSyncMovie->save();

			}
			file_put_contents(mofilmConstants::getWebResourcesFolder()."/xml/media_video_".$this->getRequest()->getSession()->getUser()->getID().".xml",$oXML->asXML());
			file_put_contents(mofilmConstants::getWebResourcesFolder()."/xml/media_audio_".$this->getRequest()->getSession()->getUser()->getID().".xml","<moviemasher> </moviemasher>");
			
			
		} elseif ( $this->getAction() == self::ACTION_SEARCH_XML ) {
			
			$this->getInputManager()->setLookupGlobals(utilityInputManager::LOOKUPGLOBALS_GET);
			$data = $this->getInputManager()->doFilter();
			if ( isset($data['Offset']) && $data['Offset'] >= 0 ) {
				$offset = $data['Offset'];
				$limit = (isset($data['Limit']) && $data['Limit'] > 0) ? $data['Limit'] : self::PAGING_DEFAULT_LIMIT;
			} else {
				$offset = 0;
				$limit = self::PAGING_DEFAULT_LIMIT;				
			}
			
			$this->getModel()->getUserSearch()->setLimit($limit);
			$this->getModel()->getUserSearch()->setOffset($offset);
			
			if ( isset ($data["keyword"]) && strlen($data["keyword"]) > 3 ) {
				//$this->getModel()->getUserSearch()->setKeywords($data["keyword"]);
				$this->getModel()->setKeywords($data["keyword"]);
			} else {
				systemLog::message("keyword def");
				$this->getModel()->setKeywords("");
			}
			
			
			$oView = new musicView($this);
			$oView->showMusicSearchResult();
			
		} elseif ( $this->getAction() == self::ACTION_SEARCH ) {
			
			$this->getInputManager()->setLookupGlobals(utilityInputManager::LOOKUPGLOBALS_GET);
			$data = $this->getInputManager()->doFilter();
			if ( isset($data['Offset']) && $data['Offset'] >= 0 ) {
				$offset = $data['Offset'];
				$limit = (isset($data['Limit']) && $data['Limit'] > 0) ? $data['Limit'] : self::PAGING_DEFAULT_LIMIT;
			} else {
				$offset = 0;
				$limit = self::PAGING_DEFAULT_LIMIT;				
			}

			$this->setPagingOptions(
				array(
					self::PAGING_VAR_OFFSET => $offset,
					self::PAGING_VAR_LIMIT => $limit
				)
			);
			
			systemLog::message($data);
			
			$this->getModel()->getUserSearch()->setLimit($limit);
			$this->getModel()->getUserSearch()->setOffset($offset);
			
			//if ( isset ($data["keyword"]) && strlen($data["keyword"]) > 3 ) {
			//	$this->getModel()->getUserSearch()->setKeywords($data["keyword"]);
			//}
			if ( isset ($data["keyword"]) && strlen($data["keyword"]) > 3 ) {
				//$this->getModel()->getUserSearch()->setKeywords($data["keyword"]);
				$this->getModel()->setKeywords($data["keyword"]);
			} else {
				systemLog::message("keyword def");
				$this->getModel()->setKeywords("");
			}
				
			

			$oView = new musicView($this);
			$oView->showMusicPage();
				
			
		} elseif ( $this->getAction() == self::ACTION_UPLOAD ) {
			if ( $this->getRequest()->getSession()->isLoggedIn() ) {
				$err = '';
				$upload_dir = mofilmConstants::getVideoFolder(); // needs to be writable by web server process
				$media_file = mofilmConstants::getWebResourcesFolder() . "/xml/media_video_" . $this->getRequest()->getSession()->getUser()->getID() . ".xml";
				if ( empty($_FILES) || empty($_FILES['Filedata']) )
					$err = 'No files supplied';

				if ( !$err ) {
					$file = $_FILES['Filedata'];
					if ( !empty($file['error']) )
						$err = 'Problem with your file: ' . $file['error'];
					elseif ( !is_uploaded_file($file['tmp_name']) )
						$err = 'Not an uploaded file';
				}

				if ( !$err ) {
					$extension = strtolower(substr($file['name'], strrpos($file['name'], '.') + 1));
					switch ( $extension ) {
						case 'mp4':
							$extension = 'mp4';
							break;
						case 'flv':
							$extension = 'flv';
							break;
						case 'mov':
							$extension = 'mov';
							break;
						case 'giff':
							$extension = 'gif';
						case 'gif':
							break;
						default:
							$err = 'Unsupported file extension ' . $extension;
					}
				}


				if ( !$err ) {
					$type = 'video';
					$label = $file['name'];
					$id = md5(uniqid(time() . 'media' . $label));
					//$id = uniqid('media' . $label);
					//$url = mofilmConstants::getVideoFolder() ."/". $id . '.' . $extension;
					$url = "/resources/video/" . $id . '.' . $extension;
					$path = $upload_dir . "/" . $id . '.' . $extension;
					if ( !@move_uploaded_file($file['tmp_name'], $path) )
						$err = 'Problem moving file to ' . $path;
					elseif ( !@chmod($path, 0777) )
						$err = 'Problem setting permissions';
				}
				
                if ( $extension != "flv" ) {
						$extension = "flv";
                        $flvurl = "/resources/video/" . $id . '.' . $extension;
                        $flvpath = $upload_dir . "/" . $id . '.' . $extension;
                        shell_exec("ffmpeg -i ".$path." -ar 44100 -ab 96 -f flv ".$flvpath);
						$url = $flvurl;
                }
				
				if ( isset( $flvpath ) ) {
					$path = $flvpath;
				} 
				
				try {
					ob_start();
					passthru("ffmpeg -i \"{$path}\" 2>&1");
					$duration = ob_get_contents();
					ob_end_clean();
					$search='/duration .*: ([0-9]+)/';
					$duration = preg_match($search, $duration, $matches);
					$videoDuration = $matches[1];
				} catch ( Exception $e ) {
					systemLog::message($e);
					$videoDuration = 60;
				}
				
				
				
				if ( $videoDuration == "" ) {
					$videoDuration = 60;
				}
				
				$oSyncMovie = new momusicSyncMovies();
				$oSyncMovie->setName($label);
				$oSyncMovie->setPath($path);
				$oSyncMovie->setStatus(0);
				$oSyncMovie->setUserID($this->getRequest()->getSession()->getUser()->getID());
				$oSyncMovie->setUniqID($id);
				//$oSyncMovie->setDate(now());
				$oSyncMovie->save();
				
				if ( !$err ) {
					// try reading in media.xml file containing existing media items
					if ( !$err ) {
						$xml_str = @file_get_contents($media_file);
						if ( !$xml_str )
							$err = 'Problem loading ' . $media_file;
						else {
							$media_file_xml = @simplexml_load_string($xml_str);
							if ( !is_object($media_file_xml) )
								$err = 'Problem parsing ' . $xml_str;
						}
					}

					// add media data to existing media.xml file
					if ( !$err ) {
						// reset type, in case it was changed by Movie Masher Server - audio in video format?
						// start with an unattributed media tag document
						$media_xml = simplexml_load_string('<moviemasher><media /></moviemasher>');

						// add required attributes
						$media_xml->media->addAttribute('type', $type);
						$media_xml->media->addAttribute('id', $id);

						// add standard attributes
						$media_xml->media->addAttribute('label', $label);
						$media_xml->media->addAttribute('group', $type);

						// add required for rendering
						//$media_xml->media->addAttribute('audio', $url);

						$media_xml->media->addAttribute('url', $url);
						$media_xml->media->addAttribute('icon', "/themes/mofilm/images/mm/video.png");
						$media_xml->media->addAttribute('duration', $videoDuration);

						// build XML string
						$xml_str = '<moviemasher>';
						$xml_str .= "\n\t" . (string) $media_xml->media->asXML() . "\n";

						$children = $media_file_xml->children();
						$z = sizeof($children);
						for ( $i = 0; $i < $z; $i++ )
							$xml_str .= "\t" . $children[$i]->asXML() . "\n";
						$xml_str .= '</moviemasher>' . "\n";

						// write file
						if ( !@file_put_contents($media_file, $xml_str) )
							$err = 'Problem writing ' . $media_file;
					}
				}
				
				if ( $err )
					$attibs = 'get=\'javascript:alert("' . $err . '");\'';
				else
					$attibs = 'trigger="browser.parameters.group=video"';
				print '<moviemasher ' . $attibs . '	/>' . "\n\n";
				
			} else {
				
				systemLog::message("not logged in uplod");
				$err = "not looged in";
				//$attibs = 'get=\'javascript:alert("' .  $err . '");\'';
				$attibs = 'get=\'javascript:window.location = "/account/login" ; \' ';
				print '<moviemasher ' . $attibs . '	/>' . "\n\n";
			}
		} elseif ( $this->getAction() == self::ACTION_MEDIA ) {
			if ( !$this->getRequest()->getSession()->getUser() ) {
				$count = (empty($_GET['count']) ? 10 : $_GET['count']);
				$index = (empty($_GET['index']) ? 0 : $_GET['index']);
				$group = (empty($_GET['group']) ? '' : $_GET['group']);

				print '<moviemasher>' . "\n";

				if ( $group ) {
					//$xml_path = '../xml/media';
					systemLog::message(mofilmConstants::getWebFolder());
					//$xml_path = "/Library/WebServer/Documents/trunk/websites/base/libraries/moviemasher/xml/media";
					$xml_path = mofilmConstants::getWebFolder()."libraries/moviemasher/xml/media";
					if ( $group != 'image' )
						$xml_path .= '_' . $group;
					$xml_path .= '.xml';

					// try reading in XML file
					$xml_str = file_get_contents($xml_path, 1);
					$media_xml = @simplexml_load_string($xml_str);
					if ( $media_xml ) { // loop through 'media' tags within XML file
						foreach ( $media_xml->media as $tag ) {
							$ok = 1;
							reset($_GET);
							foreach ( $_GET as $k => $v ) {
								switch ( $k ) {
									case 'index':
									case 'count':
										break;
									default:
										$a = (string) $tag[$k];
										// will match if parameter is empty, equal to or (for label) within attribute
										$ok = ((!$v) || ($v == $a) || ( ($k == 'label') && (strpos(strtolower($a), strtolower($v)) !== FALSE)));
								}
								if ( !$ok )
									break;
							}
							if ( $ok ) {
								if ( $index )
									$index--;
								else { // tag is within specified range
									print "\t" . $tag->asXML() . "\n";
									$count--;
									if ( !$count )
										break; // tag is last in range - done
								}
							}
						}
					}
				}
				print '</moviemasher>' . "\n";
			} else {
				$path = mofilmConstants::getWebResourcesFolder() . "/xml/media_video_" . $this->getRequest()->getSession()->getUser()->getID() . ".xml";

				if ( file_exists($path) ) {

					$count = (empty($_GET['count']) ? 10 : $_GET['count']);
					$index = (empty($_GET['index']) ? 0 : $_GET['index']);
					$group = (empty($_GET['group']) ? '' : $_GET['group']);

					print '<moviemasher>' . "\n";

					if ( $group ) {
						//$xml_path = '../xml/media';
						//$xml_path = "/Library/WebServer/Documents/trunk/websites/base/libraries/moviemasher/xml/media";
						$xml_path = mofilmConstants::getWebResourcesFolder() . "/xml/media";
						if ( $group != 'image' )
							$xml_path .= '_' . $group;
						$xml_path .= "_" . $this->getRequest()->getSession()->getUser()->getID() . '.xml';

						// try reading in XML file
						$xml_str = file_get_contents($xml_path, 1);
						$media_xml = @simplexml_load_string($xml_str);
						if ( $media_xml ) { // loop through 'media' tags within XML file
							foreach ( $media_xml->media as $tag ) {
								$ok = 1;
								reset($_GET);
								foreach ( $_GET as $k => $v ) {
									switch ( $k ) {
										case 'index':
										case 'count':
											break;
										default:
											$a = (string) $tag[$k];
											// will match if parameter is empty, equal to or (for label) within attribute
											$ok = ((!$v) || ($v == $a) || ( ($k == 'label') && (strpos(strtolower($a), strtolower($v)) !== FALSE)));
									}
									if ( !$ok )
										break;
								}
								if ( $ok ) {
									if ( $index )
										$index--;
									else { // tag is within specified range
										print "\t" . $tag->asXML() . "\n";
										$count--;
										if ( !$count )
											break; // tag is last in range - done
									}
								}
							}
						}
					}
					print '</moviemasher>' . "\n";
				} else {
					$audiopath = mofilmConstants::getWebResourcesFolder() . "/xml/media_audio_" . $this->getRequest()->getSession()->getUser()->getID() . ".xml";
					file_put_contents($path, "<moviemasher> </moviemasher> ");
					chmod($path, 0777);
					file_put_contents($audiopath, "<moviemasher> </moviemasher> ");
					chmod($audiopath, 0777);
				}
			}
		} elseif ( $this->getAction() == self::ACTION_UPLOAD_AUDIO ) {
			systemLog::message($_POST);
			$duration = $_POST["duration"];
			$url = $_POST["url"];
			$type = "audio";
			$name = $_POST["name"];
			$id = md5(uniqid(time() . 'media' . $name));

			systemLog::message("here");
			if ( $this->getRequest()->getSession()->isLoggedIn() ) {
				systemLog::message("inside here");
				$err = '';
				$upload_dir = mofilmConstants::getVideoFolder(); // needs to be writable by web server process
				$media_file = mofilmConstants::getWebResourcesFolder() . "/xml/media_audio_" . $this->getRequest()->getSession()->getUser()->getID() . ".xml";

				if ( !$err ) {
					// try reading in media.xml file containing existing media items
					if ( !$err ) {
						$xml_str = @file_get_contents($media_file);

						if ( !$xml_str )
							$err = 'Problem loading ' . $media_file;
						else {
							$media_file_xml = @simplexml_load_string($xml_str);
							if ( !is_object($media_file_xml) )
								$err = 'Problem parsing ' . $xml_str;
						}
					}

					// add media data to existing media.xml file
					if ( !$err ) {
						// reset type, in case it was changed by Movie Masher Server - audio in video format?
						// start with an unattributed media tag document
						$media_xml = simplexml_load_string('<moviemasher><media /></moviemasher>');

						$media_xml->media->addAttribute('type', $type);
						$media_xml->media->addAttribute('id', $id);

						// add standard attributes
						$media_xml->media->addAttribute('label', $name);
						$media_xml->media->addAttribute('group', $type);

						// add required for rendering
						$media_xml->media->addAttribute('audio', $url);

						$media_xml->media->addAttribute('icon', "/themes/mofilm/images/mm/audio.png");
						$media_xml->media->addAttribute('wave', "../media/audio/Swing/audio.png");
						$media_xml->media->addAttribute('duration', trim($duration));


						// build XML string
						$xml_str = '<moviemasher>';
						$xml_str .= "\n\t" . (string) $media_xml->media->asXML() . "\n";

						$children = $media_file_xml->children();
						$z = sizeof($children);
						for ( $i = 0; $i < $z; $i++ )
							$xml_str .= "\t" . $children[$i]->asXML() . "\n";
						$xml_str .= '</moviemasher>' . "\n";
						systemLog::message($xml_str);
						// write file
						if ( !@file_put_contents($media_file, $xml_str) )
							$err = 'Problem writing ' . $media_file;
					}
				}
				//if ( $err )
					//$attibs = 'get=\'javascript:alert("' . $err . '");\'';
				//else
					//$attibs = 'trigger="browser.parameters.group=audio"';
				//print '<moviemasher ' . $attibs . '	/>' . "\n\n";
			} else {
				systemLog::message("not logged in uplod");
				$err = "not looged in";
				//$attibs = 'get=\'javascript:alert("' .  $err . '");\'';
				//$attibs = 'get=\'javascript:window.location = "/account/login" ; \' ';
				//print '<moviemasher ' . $attibs . '	/>' . "\n\n";
				print "false";
			}
		} elseif ( $this->getAction() == self::ACTION_LICENSE ) {
			if ( $this->getRequest()->getSession()->isLoggedIn() ) {
				$ID = $this->getActionFromRequest(false, 1);
				$oView = new musicView($this);
				$oView->showMusicLicensePage($ID);
			} else {
				$this->redirect("/account/authorise?redirect=/music");
			}
		} elseif ( $this->getAction() == self::ACTION_MUSIC_DOWNLOAD ) {
				$ID = $this->getActionFromRequest(false, 1);
				systemLog::message("ID".$ID);
				$oObject = momusicWorks::getInstance($ID);
				$oUserLicense = new mofilmUserMusicLicense();
				$oUserLicense->setLicenseID(md5(time()));
				$oUserLicense->setTrackName($oObject->getTrackName());
				$oUserLicense->setUserID($this->getRequest()->getSession()->getUser()->getID());
				$oUserLicense->setStatus(0);
				$oUserLicense->setMusicSource($oObject->getSource());
				$oUserLicense->setExpiryDate("2099-02-05 05:12:31");
				$oUserLicense->save();
				
				$this->redirect("/download/movie?url={$oObject->getPath()}");
		}
	}

	/**
	 * @see mvcControllerBase::addInputFilters()
	 */
	function addInputFilters() {
		$this->getInputManager()->addFilter('Offset', utilityInputFilter::filterInt());
		$this->getInputManager()->addFilter('Limit', utilityInputFilter::filterInt());		
		$this->getInputManager()->addFilter('keyword', utilityInputFilter::filterString());		
	}

	/**
	 * @see mvcControllerBase::addInputToModel()
	 */
	function addInputToModel($inData, $inModel) {
		
	}

	/**
	 * Fetches the model
	 *
	 * @return musicModel
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
		$oModel = new musicModel();
		$this->setModel($oModel);
	}

	/**
	 * Returns $_PagingOptions, or optionally var named $inVarName, returns false if not found
	 *
	 * @param string $inVarName
	 * @return array(offset, limit, var...)
	 */
	function getPagingOptions($inVarName = null) {
		if ( $inVarName !== null ) {
			if ( array_key_exists($inVarName, $this->_PagingOptions) ) {
				return $this->_PagingOptions[$inVarName];
			} else {
				return false;
			}
		}
		return $this->_PagingOptions;
	}

	/**
	 * Set $_PagingOptions to $inPagingOptions
	 *
	 * @param array $inPagingOptions
	 * @return mvcDaoController
	 */
	function setPagingOptions(array $inPagingOptions = array()) {
		if ( $inPagingOptions !== $this->_PagingOptions ) {
			$this->_PagingOptions = $inPagingOptions;
			$this->setModified();
		}
		return $this;
	}
	
	
}