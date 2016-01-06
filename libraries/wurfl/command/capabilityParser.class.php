<?php
/**
 * wurflCommandCapabilityParser Class
 * 
 * Stored in wurflCommandCapabilityParser.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage cli
 * @category wurflCommandCapabilityParser
 * @version $Rev: 650 $
 */


/**
 * wurflCommandCapabilityParser class
 * 
 * Reads the CSV file from the CLI containing WURFL capability descriptions
 * and variable types and adds them to the system.
 *
 * @package scorpio
 * @subpackage cli
 * @category wurflCommandCapabilityParser
 */
class wurflCommandCapabilityParser extends cliCommand {
	
	/**
	 * Creates a new command
	 *
	 * @param cliRequest $inRequest
	 */
	function __construct(cliRequest $inRequest) {
		parent::__construct($inRequest, 'file');
		
		$this->setCommandHelp('Processes a CSV file of WURFL capabilities that is in the format: "Capability_Name","Var Type","Description"');
		$this->setCommandRequiresValue(true);
	}
	
	/**
	 * Executes the command
	 *
	 * @return void
	 */
	function execute() {
		if ( $this->getRequest()->getParam('file') && is_readable($this->getRequest()->getParam('file')) ) {
			$hndl = fopen($this->getRequest()->getParam('file'), 'rb');
			if ( !$hndl ) {
				throw new cliApplicationCommandException($this, 'Failed to open file: '.$this->getRequest()->getParam('file'));
			}
			while ( !feof($hndl) ) {
				$row = fgetcsv($hndl, null, ',', '"');
				$capability = str_replace(array('"',' '), '', trim($row[0]));
				$varType = str_replace(array('"',' '), '', trim($row[1]));
				$description = trim($row[2]);
				
				if ( $capability && $varType ) {
					$this->getRequest()->getApplication()->notify(
						new cliApplicationEvent(
							cliApplicationEvent::EVENT_INFORMATIONAL,
							"Attempting to update capability: $capability with $varType"
						)
					);
					
					$oCap = wurflCapability::getInstance($capability);
					if ( $oCap instanceof  wurflCapability && $oCap->getCapabilityID() > 0 ) {
						$this->getRequest()->getApplication()->notify(
							new cliApplicationEvent(
								cliApplicationEvent::EVENT_INFORMATIONAL,
								"\tMatched: $capability to record with ID {$oCap->getCapabilityID()}; updating"
							)
						);
						
						$oCap->setVarType($varType);
						$oCap->setHelpText($description);
						$oCap->save();
					}
					$oCap = null;
					unset($oCap);
				}
			}
			fclose($hndl);
		} else {
			throw new cliApplicationCommandException($this, 'Missing a CSV file to process');
		}
	}
}