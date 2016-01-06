<?php
/**
 * wurflCommandParser Class
 * 
 * Stored in wurflCommandParser.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage cli
 * @category wurflCommandParser
 * @version $Rev: 650 $
 */


/**
 * wurflCommandParser class
 * 
 * Parses a resource into the WURFL database. A resource can be a file
 * or folder. The files should be valid XML and conform to the WURFL
 * or WURFL Patch standard. Each file will be processed in turn as they
 * are located. On *nix systems, a check is made before each file to
 * intercept signals.
 *
 * @package scorpio
 * @subpackage cli
 * @category wurflCommandParser
 */
class wurflCommandParser extends cliCommand {
	
	/**
	 * Stores file resource information
	 *
	 * @var array
	 * @access protected
	 */
	protected $_Files = array();
	
	/**
	 * Array of extensions to search for
	 *
	 * @var array
	 * @access protected
	 */
	protected $_Extensions = array('xml');
	
	
	
	/**
	 * Creates a new parser command
	 *
	 * @param cliRequest $inRequest
	 */
	function __construct(cliRequest $inRequest) {
		parent::__construct($inRequest, 'parse');
		
		$this->setCommandHelp('Resource to process, can be a file or a directory of files: parse <file or dir>');
		$this->setCommandRequiresValue(true);
	}
	
	/**
	 * Executes the command
	 *
	 * @return void
	 */
	function execute() {
		if ( $this->findReadableResources() ) {
			foreach ( $this->_Files as $file ) {
				if ( $this->getRequest()->getApplication()->signalTrapped() ) {
					$this->getRequest()->getApplication()->notify(
						new cliApplicationEvent(
							cliApplicationEvent::EVENT_REGISTERED_SIGNAL_TRAPPED,
							'Command intercepted signal before processing '.$file,
							$this,
							array(
								cliApplicationEvent::OPTION_APP_NAME => $this->getRequest()->getApplication()->getApplicationName(),
								cliApplicationEvent::OPTION_APP_COMMAND => $this->getCommandPattern(),
							)
						)
					);
					break;
				}
				$oParser = new wurflParser($file, false, $this->getRequest()->getSwitch('c'));
				$oParser->process();
			}
		}
	}

	/**
	 * Returns true if the file could be read as a resource
	 *
	 * @return boolean
	 */
	function findReadableResources() {
		if ( $this->getRequest()->getParam('ext') && is_string($this->getRequest()->getParam('ext')) ) {
			$this->_Extensions = array_merge($this->_Extensions, explode(';', $this->getRequest()->getParam('ext')));
		}
		if ( $this->getRequest()->getParam('parse') && is_string($this->getRequest()->getParam('parse')) ) {
			$resource = $this->getRequest()->getParam('parse');
			if ( @file_exists($resource) && is_readable($resource) && !@is_dir($resource) ) {
				$this->_Files[] = $resource;
			} elseif ( @is_dir($resource) && @is_readable($resource) ) {
				$files = fileObject::parseDir($resource, $this->getRequest()->getSwitch('r'));
				if ( is_array($files) && count($files) > 0 ) {
					if ( false ) $oFile = new fileObject();
					foreach ( $files as $oFile ) {
						$bits = explode('.', $oFile->getFilename());
						$extn = array_pop($bits);
						if ( in_array($extn, $this->_Extensions) ) {
							$this->_Files[] = $oFile->getOriginalFilename();
						}
					}
				}
			}
		}
		if ( is_array($this->_Files) && count($this->_Files) > 0 ) {
			return true;
		}
		return false;
	}
}