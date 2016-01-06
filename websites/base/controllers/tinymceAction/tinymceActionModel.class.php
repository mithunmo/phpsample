<?php
/**
 * tinymceActionModel.class.php
 * 
 * tinymceActionModel class
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package websites_base
 * @subpackage controllers
 * @category tinymceActionModel
 * @version $Rev: 623 $
 */


/**
 * tinymceActionModel class
 * 
 * Provides the "tinymceAction" page
 * 
 * @package websites_base
 * @subpackage controllers
 * @category tinymceActionModel
 */
class tinymceActionModel extends mvcModelBase {

	/**
	 * Stores the folder path
	 *
	 * @var string
	 * @protected
	 */
	protected $_FolderPath;

	/**
	 * Stores the new dir name
	 *
	 * @var string
	 * @protected
	 */
	protected $_DirName;

	/**
	 * Stores the list of folders that are not accessible to the user
	 *
	 * @var array
	 * @protected
	 */
	protected $_ExcludeFolder = array("profiles" => "profiles");


	
	/**
	 * @see mvcModelBase::__construct()
	 */
	function __construct() {
		parent::__construct();
	}

	/**
	 *  geta all the files in a folder
	 *
	 * @param string $dir
	 * @return array 
	 *
	 */
	function getImageFiles($dir) {
		$files = fileObject::parseDir($dir, false);
		$imageList = array();
		foreach ( $files as $oFile ) {
			if ( preg_match("/^\..*/" , $oFile->getFilename() ) || $this->getExcludeFolders( $oFile->getFilename() ) ) {
				systemLog::debug("Invalid File".$oFile->getFilename());
			} else {
				$imageList[] = $oFile;
			}
		}
		return $imageList;
	}

	/**
	 * Gets the folder path for the upload
	 *
	 * @return string
	 */
    	function getFolderPath() {
		return $this->_FolderPath;
	}

	/**
	 * Sets the folder path based on the upload folder
	 *
	 * @param string $inFolderPath
	 * @return void
	 * 
	 */
	function setFolderPath($inFolderPath = 'resources') {
		if ( $inFolderPath == "resources" ) {
			$this->_FolderPath = mofilmConstants::getResourcesFolder();
		} else {
			$this->_FolderPath = $inFolderPath;
		}
	}

	/**
	 * Returns the parent path of a directory
	 *
	 * @return string
	 */
	function getParentPath() {
		return dirname($this->getFolderPath());
	}

	/**
	 * Gets the directory name
	 *
	 * @return string
	 */
	function getDirName() {
		return $this->_DirName;
	}

	/**
	 * Sets the directory name
	 *
	 * @param string $inDirName
	 */
	function setDirName($inDirName) {
		$this->_DirName = $inDirName;
	}


	/**
	 * Attempts to create the file store folder
	 *
	 * @return void
	 */
	function createFolder() {
		if ( !mkdir(utilityStringFunction::cleanDirSlashes(system::getConfig()->getPathWebsites().system::getDirSeparator().'base'."/".$this->getFolderPath()."/".$this->getDirName()), 0777, false) ) {
			return false;
		} 
		return true;
	}

	/**
	 * Gets the absolute path of a directory
	 *
	 * @return string
	 */
	function getFullPath() {
		return system::getConfig()->getPathWebsites().system::getDirSeparator().'base'."/";
	}

	/**
	 * Gets the file and dir count in a directory
	 *
	 * @param string $inDir
	 * @return boolean
	 */
	function getFilesCount($inDir) {
		$count = fileObject::countFilesInDir($inDir);
		$dcount = fileObject::countFilesInDir($inDir, 'dir');
		if ( $count == 0 && $dcount == 0 ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Removes the directory based on the path
	 *
	 * @param string $inDir
	 * @return boolean
	 */
	function removeDir($inDir) {
		return rmdir($inDir);
	}

	/**
	 * Checks if the particular folder is listed in the exclude list
	 *
	 * @param string $inFilename
	 * @return boolean
	 */
	function getExcludeFolders($inFilename) {
		return array_key_exists($inFilename, $this->_ExcludeFolder);
	}
}