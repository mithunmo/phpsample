<?php
/**
 * wurflCommandTest Class
 * 
 * Stored in wurflCommandTest.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage cli
 * @category wurflCommandTest
 * @version $Rev: 650 $
 */


/**
 * wurflCommandTest class
 * 
 * Tests a user-agent against the parsed data.
 *
 * @package scorpio
 * @subpackage cli
 * @category wurflCommandTest
 */
class wurflCommandTest extends cliCommand {
	
	/**
	 * Creates a new command
	 *
	 * @param cliRequest $inRequest
	 */
	function __construct(cliRequest $inRequest) {
		parent::__construct($inRequest, 'ua');
		
		$this->setCommandHelp('Use to load a device by user agent to test parsing results e.g. ua SonyEricssonW880i');
		$this->setCommandRequiresValue(true);
	}
	
	/**
	 * Executes the command
	 *
	 * @return void
	 */
	function execute() {
		$this->getRequest()->getApplication()->getResponse()
			->addResponse("Testing user agent: ".$this->getRequest()->getParam('ua'));
		
		$oDevice = wurflManager::getInstanceByUserAgent($this->getRequest()->getParam('ua'));
		$oDevice->getCapabilities();
		
		$this->getRequest()->getApplication()->getResponse()
			->addResponse(print_r($oDevice, 1))
			->addResponse("Device loaded with ".$oDevice->getCapabilities()->countCapabilities()." capabilities in profile");
	}
}