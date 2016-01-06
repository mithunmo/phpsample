<?php
/**
 * downloadController
 *
 * Stored in downloadController.class.php
 * 
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_mofilm.com
 * @subpackage controllers
 * @category downloadController
 * @version $Rev: 368 $
 */


/**
 * downloadController
 *
 * downloadController class
 * 
 * @package websites_mofilm.com
 * @subpackage controllers
 * @category downloadController
 */
class downloadController extends mvcController {
	
	const ACTION_VIEW = 'view';
	const ACTION_MOVIE = 'movie';
	const ACTION_GENERAL_DOWNLOADS = 'generalDownloads';
	const ACTION_GRANT_DOWNLOADS = 'grantDownloads';
	const ACTION_CCA_DOWNLOADS = 'ccaDownloads';
        const ACTION_INVOICE_DOWNLOADS = 'invoiceDownloads';
	
	/**
	 * @see mvcControllerBase::initialise()
	 */
	function initialise() {
		parent::initialise();

		$this->setDefaultAction(self::ACTION_VIEW);
		$this->setRequiresAuthentication(true);
		
		$this->getControllerActions()
			->addAction(self::ACTION_VIEW)
			->addAction(new mvcControllerAction('downloadID', '/^\d+/'))
			->addAction(self::ACTION_MOVIE)
			->addAction(self::ACTION_GENERAL_DOWNLOADS)
			->addAction(self::ACTION_GRANT_DOWNLOADS)
			->addAction(self::ACTION_CCA_DOWNLOADS)
                        ->addAction(self::ACTION_INVOICE_DOWNLOADS)
			->addAction(new mvcControllerAction('downloadHash', '/^\w+/'));
	}
	
	/**
	 * @see mvcControllerBase::launch()
	 */
	function launch() {
		if ( $this->getAction() == self::ACTION_VIEW ) {
			/*
			 * No downloads route page
			 */
			$this->redirect('/home');
		} elseif ( $this->getAction() == self::ACTION_MOVIE ) {
			$this->getInputManager()->setLookupGlobals(utilityInputManager::LOOKUPGLOBALS_GET);
			$inData = $this->getInputManager()->doFilter();
			$this->sendMovieFile($inData["url"]);
		} elseif ( $this->getAction() == self::ACTION_GENERAL_DOWNLOADS ) {
			$this->getInputManager()->setLookupGlobals(utilityInputManager::LOOKUPGLOBALS_GET);
			$inData = $this->getInputManager()->doFilter();
			$this->sendGeneralDownloadFile($inData['url']);
		} elseif ( $this->getAction() == self::ACTION_GRANT_DOWNLOADS ) {
			$this->sendGrantsDownloadFile();
		} elseif ( $this->getAction() == self::ACTION_CCA_DOWNLOADS ) {
			$this->sendCcaDownloadFile();
		}elseif ( $this->getAction() == self::ACTION_INVOICE_DOWNLOADS ) {
			$this->sendInvoiceDownloadFile();
		}else {
			$this->downloadAction();
		}
	}
	
	/**
	 * Handle the download request
	 * 
	 * @return void
	 */
	protected function downloadAction() {
		$oView = new downloadView($this);
		
		$oFile = $this->getModel()->getFile($this->getAction());
		if ( !$oFile instanceof mofilmDownloadFile || !$oFile->getID() ) {
			systemLog::error("File not found with reference {$this->getAction()}");
			$oView->show404Page();
			return;
		}
		if ( !$oFile->isAvailable() ) {
			systemLog::error("File not available for download with reference {$this->getAction()}");
			$oView->showNotAvailablePage();
			return;
		}
		if ( $oFile->getDownloadHash() && $this->getRequest()->getServerName() != 'admin.mofilm.com' ) {
			$oSource = $oFile->getSourceSet()->getObjectByHash($oFile->getDownloadHash());
			if ( $oSource instanceof mofilmSource ) {
				if ( $oSource->isClosed() ) {
					systemLog::error("Event / source has expired, file not available with reference {$this->getAction()}");
					$oView->showDownloadExpiredPage();
					return;
				}
			}
		}
		
		if ( $this->getActionFromRequest(false, 1) ) {
			$this->getModel()->logStats($oFile, $this->getRequest()->getSession()->getUser()->getID());
			$this->sendFile($oFile);
		} else {
			$oView->showDownloadPage();
		}
	}
	
	
	/**
	 * Send the binary file to the user via x-sendfile
	 * 
	 * @param Video link
	 * @return void 
	 */
	private function sendMovieFile( $inFile) {
		$name = basename($inFile);
		
		/*
		 * IE is rubbish and doesn't like multiple . (dots) in the file name
		 */
		if ( strstr($_SERVER['HTTP_USER_AGENT'], "MSIE") ) {
			$name = preg_replace('/\./', '%2e', $name, substr_count($name, '.') - 1);
		}
		
		systemLog::info("Delivering file {$name}");
		//$filesize = filesize($inFile);
		systemLog::message($filesize);
		
		/*
		 * Fetch mime-type from actual file lookup
		 */
		$file = escapeshellarg($inFile);
		$mime = shell_exec("file -bi " . $file);
		
		if ( ! preg_match('/\//', $mime) ) {
			$mime = "application/octet-stream";
		}

		/*
		 * Send the file to the browser via X-Sendfile (apache module) if supported
		 */
		header("HTTP/1.0 200 OK");
		header("Content-Description: File Transfer");
		header("Content-Type: $mime");
		//header("Content-Length: $filesize");
		header("Content-Disposition: attachment; filename=\"$name\"");
		header("Content-Transfer-Encoding: binary");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Pragma: public");
		
		if ( function_exists('apache_get_modules') && in_array('mod_xsendfile', apache_get_modules()) ) {
			header("X-Sendfile: ".$inFile);
		} else {
			/*
			 * Ensure output buffer is clean and stopped
			 */
			ob_clean();
			flush();
			
			readfile($inFile);
		}
	}
	
	
	/**
	 * Send the binary file to the user via x-sendfile
	 * 
	 * @param mofilmDownloadFile $inFile
	 * @return void 
	 */
	private function sendFile(mofilmDownloadFile $inFile) {
		$name = basename($inFile->getFilename());
		
		/*
		 * IE is rubbish and doesn't like multiple . (dots) in the file name
		 */
		if ( strstr($_SERVER['HTTP_USER_AGENT'], "MSIE") ) {
			$name = preg_replace('/\./', '%2e', $name, substr_count($name, '.') - 1);
		}
		
		systemLog::info("Delivering file {$name}");
		$filesize = filesize($inFile->getFileLocation());
		
		/*
		 * Fetch mime-type from actual file lookup
		 */
		$file = escapeshellarg($inFile->getFileLocation());
		$mime = shell_exec("file -bi " . $file);
		
		if ( ! preg_match('/\//', $mime) ) {
			$mime = "application/octet-stream";
		}

		/*
		 * Send the file to the browser via X-Sendfile (apache module) if supported
		 */
		header("HTTP/1.0 200 OK");
		header("Content-Description: File Transfer");
		header("Content-Type: $mime");
		header("Content-Length: $filesize");
		header("Content-Disposition: attachment; filename=\"$name\"");
		header("Content-Transfer-Encoding: binary");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Pragma: public");
		
		if ( function_exists('apache_get_modules') && in_array('mod_xsendfile', apache_get_modules()) ) {
			header("X-Sendfile: ".$inFile->getFileLocation());
		} else {
			/*
			 * Ensure output buffer is clean and stopped
			 */
			ob_clean();
			flush();
			
			//readfile($inFile->getFileLocation());
			$file = @fopen($inFile->getFileLocation(),"rb");
			while(!feof($file))
			{
				print(@fread($file, 1024*8));
				ob_flush();
				flush();
			}			
		}
	}
	
	/**
	 * Send the binary file to the user via x-sendfile
	 * 
	 * @param string filename
	 * @param string filepath
	 * @return void
	 */
	private function sendGeneralDownloadFile($fileName=null) {
		$name = basename($fileName);

		if (!file_exists($fileName)) {
			systemLog::error("File not available for download");
			$oView = new downloadView($this);
			$oView->showNotAvailablePage();
			return;
		}

		/*
		 * IE is rubbish and doesn't like multiple . (dots) in the file name
		 */
		if ( strstr($_SERVER['HTTP_USER_AGENT'], "MSIE") ) {
			$name = preg_replace('/\./', '%2e', $name, substr_count($name, '.') - 1);
		}

		$filesize = filesize($fileName);

		/*
		 * Fetch mime-type from actual file lookup
		 */
		$file = escapeshellarg($name);
		$mime = shell_exec("file -bi " . $file);

		if ( ! preg_match('/\//', $mime) ) {
			$mime = "application/octet-stream";
		}

		/*
		 * Send the file to the browser via X-Sendfile (apache module) if supported
		 */
		header("HTTP/1.0 200 OK");
		header("Content-Description: File Transfer");
		header("Content-Type: $mime");
		header("Content-Length: $filesize");
		header("Content-Disposition: attachment; filename=\"$name\"");
		header("Content-Transfer-Encoding: binary");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Pragma: public");

		if ( function_exists('apache_get_modules') && in_array('mod_xsendfile', apache_get_modules()) ) {
			header("X-Sendfile: ".$name);
		} else {
			/*
			 * Ensure output buffer is clean and stopped
			 */
			ob_clean();
			flush();

			//readfile($inFile->getFileLocation());
			$file = @fopen($fileName,"rb");
			while(!feof($file))
			{
				print(@fread($file, 1024*8));
				ob_flush();
				flush();
			}
		}
	}

	/**
	 * Send the binary file to the user via x-sendfile
	 * 
	 * @param string filename
	 * @param string filepath
	 * @return void 
	 */
	private function sendGrantsDownloadFile() {
	    
		$inCategory = $this->getActionFromRequest(false, 1);
		$inID = $this->getActionFromRequest(false, 2);
		$inUserID = $this->getRequest()->getSession()->getUser()->getID();
		
		$oUserMovieGrants = mofilmUserMovieGrants::getInstance($inID);
		
                if (strpos($inCategory,'ReferenceImage') !== false) 
                        $fileName = $oUserMovieGrants->getParamSet()->getParam($inCategory);
                else
                    $fileName = $oUserMovieGrants->getParamSet()->getParam($inCategory.'Path');
		$name = basename($fileName);

		if ( !file_exists($fileName) || ( $this->getRequest()->getDistributorServerName() != 'admin.mofilm.com' && $oUserMovieGrants->getUserID() != $inUserID ) ) {
			systemLog::error("File not available for download");
			$oView = new downloadView($this);
			$oView->showNotAvailablePage();
			return;
		}

		/*
		 * IE is rubbish and doesn't like multiple . (dots) in the file name
		 */
		if ( strstr($_SERVER['HTTP_USER_AGENT'], "MSIE") ) {
			$name = preg_replace('/\./', '%2e', $name, substr_count($name, '.') - 1);
		}

		$filesize = filesize($fileName);

		/*
		 * Fetch mime-type from actual file lookup
		 */
		$file = escapeshellarg($name);
		$mime = shell_exec("file -bi " . $file);

		if ( ! preg_match('/\//', $mime) ) {
			$mime = "application/octet-stream";
		}

		/*
		 * Send the file to the browser via X-Sendfile (apache module) if supported
		 */
		header("HTTP/1.0 200 OK");
		header("Content-Description: File Transfer");
		header("Content-Type: $mime");
		header("Content-Length: $filesize");
		header("Content-Disposition: attachment; filename=\"$name\"");
		header("Content-Transfer-Encoding: binary");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Pragma: public");

		if ( function_exists('apache_get_modules') && in_array('mod_xsendfile', apache_get_modules()) ) {
			header("X-Sendfile: ".$name);
		} else {
			/*
			 * Ensure output buffer is clean and stopped
			 */
			ob_clean();
			flush();

			//readfile($inFile->getFileLocation());
			$file = @fopen($fileName,"rb");
			while(!feof($file))
			{
				print(@fread($file, 1024*8));
				ob_flush();
				flush();
			}
		}
	}
        
        
        private function sendInvoiceDownloadFile() {
	    
		$paymentID = $this->getActionFromRequest(false, 1);
		$invoiceID = $this->getActionFromRequest(false, 2);
		$inUserID = $this->getRequest()->getSession()->getUser()->getID();
		
		$query = '
			SELECT fileName
			  FROM '.system::getConfig()->getDatabase('mofilm_content').'.paymentInvoice WHERE paymentInvoice.ID ='.$invoiceID;
		
		$oRes = dbManager::getInstance()->query($query);
		$res = $oRes->fetch();
                $fileName = mofilmConstants::getInvoiceDocsFolder().$paymentID.'/'.$res['fileName'];
		$name = basename($fileName);

		if ( !file_exists($fileName) || ( $this->getRequest()->getDistributorServerName() != 'admin.mofilm.com' && $oUserMovieGrants->getUserID() != $inUserID ) ) {
			systemLog::error("File not available for download");
			$oView = new downloadView($this);
			$oView->showNotAvailablePage();
			return;
		}

		/*
		 * IE is rubbish and doesn't like multiple . (dots) in the file name
		 */
		if ( strstr($_SERVER['HTTP_USER_AGENT'], "MSIE") ) {
			$name = preg_replace('/\./', '%2e', $name, substr_count($name, '.') - 1);
		}

		$filesize = filesize($fileName);

		/*
		 * Fetch mime-type from actual file lookup
		 */
		$file = escapeshellarg($name);
		$mime = shell_exec("file -bi " . $file);

		if ( ! preg_match('/\//', $mime) ) {
			$mime = "application/octet-stream";
		}

		/*
		 * Send the file to the browser via X-Sendfile (apache module) if supported
		 */
		header("HTTP/1.0 200 OK");
		header("Content-Description: File Transfer");
		header("Content-Type: $mime");
		header("Content-Length: $filesize");
		header("Content-Disposition: attachment; filename=\"$name\"");
		header("Content-Transfer-Encoding: binary");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Pragma: public");

		if ( function_exists('apache_get_modules') && in_array('mod_xsendfile', apache_get_modules()) ) {
			header("X-Sendfile: ".$name);
		} else {
			/*
			 * Ensure output buffer is clean and stopped
			 */
			ob_clean();
			flush();

			//readfile($inFile->getFileLocation());
			$file = @fopen($fileName,"rb");
			while(!feof($file))
			{
				print(@fread($file, 1024*8));
				ob_flush();
				flush();
			}
		}
	}
	
	/**
	 * Send the binary file to the user via x-sendfile
	 * 
	 * @param string filename
	 * @param string filepath
	 * @return void 
	 */
	private function sendCcaDownloadFile() {
	    
		$inMovieID = $this->getActionFromRequest(false, 1);
		$inUserID = $this->getRequest()->getSession()->getUser()->getID();
		
		$oMovie = mofilmMovieManager::getInstanceByID($inMovieID);
		$oMovieAsset = $oMovie->getAssetSet()->getObjectByAssetType(mofilmMovieAsset::TYPE_CCA)->getFirst();

                if (strpos($oMovieAsset->getFilename(),'img') !== false) {
                    $path = substr($oMovieAsset->getFilename(), 4);
                    $dirpath = mofilmConstants::getWebFolder().$path;
                    $dirpath = dirname($dirpath).DIRECTORY_SEPARATOR;
                } else {
                    $dirpath = dirname($oMovieAsset->getFilename()).DIRECTORY_SEPARATOR;
                }
                
		//$dirpath = dirname($oMovieAsset->getFilename()).DIRECTORY_SEPARATOR;
		
		$zipname = time();
		$zip = new ZipArchive();
		
		if ( $zip->open($dirpath.$inMovieID.'.zip', ZipArchive::CREATE) == TRUE ) {
			
			$files = array_diff( scandir( $dirpath ), Array( ".", ".." ) );
			
			foreach ($files as $file) {
			    $zip->addFile($dirpath.$file, $file);
			}
			
			$zip->close();
		}
		
		$fileName = $dirpath.$inMovieID.'.zip';
		
		$name = basename($fileName);
		
		if ( !file_exists($fileName) || ( $this->getRequest()->getDistributorServerName() != 'admin.mofilm.com' && $oMovie->getUserID() != $inUserID ) ) {
			systemLog::error("File not available for download");
			$oView = new downloadView($this);
			$oView->showNotAvailablePage();
			return;
		}

		/*
		 * IE is rubbish and doesn't like multiple . (dots) in the file name
		 */
		if ( strstr($_SERVER['HTTP_USER_AGENT'], "MSIE") ) {
			$name = preg_replace('/\./', '%2e', $name, substr_count($name, '.') - 1);
		}

		$filesize = filesize($fileName);
		
		/*
		 * Fetch mime-type from actual file lookup
		 */
		$file = escapeshellarg($name);
		$mime = shell_exec("file -bi " . $file);

		if ( ! preg_match('/\//', $mime) ) {
			$mime = "application/octet-stream";
		}

		/*
		 * Send the file to the browser via X-Sendfile (apache module) if supported
		 */
		header("HTTP/1.0 200 OK");
		header("Content-Description: File Transfer");
		header("Content-Type: $mime");
		header("Content-Length: $filesize");
		header("Content-Disposition: attachment; filename=\"$name\"");
		header("Content-Transfer-Encoding: binary");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Pragma: public");

		if ( function_exists('apache_get_modules') && in_array('mod_xsendfile', apache_get_modules()) ) {
			header("X-Sendfile: ".$name);
		} else {
			/*
			 * Ensure output buffer is clean and stopped
			 */
			ob_clean();
			flush();

			//readfile($inFile->getFileLocation());
			$file = @fopen($fileName,"rb");
			while(!feof($file))
			{
				print(@fread($file, 1024*8));
				ob_flush();
				flush();
			}
		}
		
		if( file_exists($fileName) ) {
		    chmod($fileName, 0777);
		    unlink($fileName);
		}
	}
	
	/**
	 * @see mvcControllerBase::addInputFilters()
	 */
	function addInputFilters() {
		$this->getInputManager()->addFilter("url", utilityInputFilter::filterString());
	}
	
	/**
	 * @see mvcControllerBase::addInputToModel()
	 */
	function addInputToModel($inData, $inModel) {
		
	}
	
	/**
	 * Fetches the model
	 *
	 * @return downloadModel
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
		$oModel = new downloadModel();
		$this->setModel($oModel);
	}
}