<?php
/**
 * wurflCommandDownload Class
 * 
 * Stored in wurflCommandDownload.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage cli
 * @category wurflCommandDownload
 * @version $Rev: 654 $
 */


/**
 * wurflCommandDownload class
 * 
 * Downloads the latest version of the main WURFL XML file from
 * the main WURFL website and stores it in the temp folder.
 *
 * @package scorpio
 * @subpackage cli
 * @category wurflCommandDownload
 */
class wurflCommandDownload extends cliCommand {
	
	/**
	 * Creates a new command
	 *
	 * @param cliRequest $inRequest
	 */
	function __construct(cliRequest $inRequest) {
		parent::__construct($inRequest, 'download', null, 'd');
		
		$this->setCommandHelp('Downloads the latest WURFL file from the project homepage to ../temp/wurfl.xml');
		$this->setCommandRequiresValue(false);
		$this->setCommandIsSwitch(true);
		$this->setHaltAppAfterExecute(false);
	}
	
	/**
	 * Executes the command
	 *
	 * @return void
	 */
	function execute() {
		$this->getRequest()->getApplication()->getResponse()
			->addResponse("Downloading latest WURFL file, please wait...")
			->addResponse("\nUsing: ".system::getConfig()->getWurflUriLocation()->getParamValue());
		
		$remote = system::getConfig()->getWurflUriLocation()->getParamValue();
		$target = system::getConfig()->getPathTemp().system::getDirSeparator().basename($remote);
		
		try {
			utilityCurl::downloadFile($remote, $target);
			$this->getRequest()->getApplication()->getResponse()->addResponse("\t...Download complete");
			
			$pieces = explode('.', basename($target));
			$extn = strtolower(array_pop($pieces));
			if ( in_array($extn, array('gz','zip')) ) {
				$cmd = false;
				switch ( $extn ) {
					case 'gz':
						$res = explode("\n", `which gunzip`);
						if ( is_array($res) && isset($res[0]) && strlen($res[0]) > 5 && stripos($res[0], 'which: no ') === false ) {
							$cmd = trim($res[0]).' '.escapeshellarg($target);
						}
					break;
					
					case 'zip':
						$res = explode("\n", `which unzip`);
						if ( is_array($res) && isset($res[0]) && strlen($res[0]) > 5 && stripos($res[0], 'which: no ') === false ) {
							$cmd = trim($res[0]).' '.escapeshellarg($target).' -d '.escapeshellarg(dirname($target).system::getDirSeparator());
						}
					break;
				}
				if ( $cmd ) {
					$this->getRequest()->getApplication()->getResponse()->addResponse("\t...Compressed file detected, decompressing");
					$this->getRequest()->getApplication()->notify(
						new cliApplicationEvent(
							cliApplicationEvent::EVENT_INFORMATIONAL, "Running command: $cmd"
						)
					);
					`$cmd`;
					$this->getRequest()->getApplication()->getResponse()->addResponse("\t...Decompress done");
				}
			}
		} catch ( Exception $e ) {
			throw new cliApplicationCommandException($this, 'Error downloading WURFL XML file from '.system::getConfig()->getWurflUriLocation().':'.$e->getMessage());
		}
	}
}