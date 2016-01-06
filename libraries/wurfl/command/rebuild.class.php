<?php
/**
 * wurflCommandRebuild Class
 * 
 * Stored in wurflCommandRebuild.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage cli
 * @category wurflCommandRebuild
 * @version $Rev: 650 $
 */


/**
 * wurflCommandRebuild class
 * 
 * Rebuilds the wurfl data adding any missing manufacturer and model names
 * by searching for all root level devices, building the full profile and
 * then assigning manufacturer and model information to the device record.
 * This needs to be done after an initial parse of the master WURFL.xml
 * file if it is downloaded and parsed to ensure that all devices are
 * appropriately mapped.
 *
 * @package scorpio
 * @subpackage cli
 * @category wurflCommandRebuild
 */
class wurflCommandRebuild extends cliCommand {
	
	/**
	 * Creates a new rebuild command
	 *
	 * @param cliRequest $inRequest
	 */
	function __construct(cliRequest $inRequest) {
		parent::__construct($inRequest, 'rebuild', null, 'R');
		
		$this->setCommandHelp('Rebuild device data adding missing manufacturer and modelnames');
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
		$this->getRequest()->getApplication()->getResponse()->addResponse("Beginning WURFL data rebuild, please wait...");
		$oParser = new wurflParser();
		$oParser->rebuildDeviceData();
		$this->getRequest()->getApplication()->getResponse()->addResponse("...Rebuild completed");
	}
}