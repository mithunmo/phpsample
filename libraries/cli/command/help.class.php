<?php
/**
 * cliCommandHelp Class
 * 
 * Stored in help.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage cli
 * @category cliCommandHelp
 * @version $Rev: 707 $
 */


/**
 * cliCommandHelp class
 * 
 * Displays help information for the current cliApplication.
 * Help is formatted to the current set console width. Supports
 * command help e.g. help <command> for more detailed information.
 * 
 * Help is generated from the commands and sub-commands that make up
 * the current application. It can intelligently build parameters,
 * values and display what is required vs. optional. For an example
 * see the Scorpio CLI tool. This has extensive help for all commands
 * including options, switches, required values etc.
 * 
 * This class replaces the previous cliHelp.
 * 
 * <code>
 * // add help output to any application
 * $oApp = new cliApplication('example', 'A simple example.');
 * $oRequest = cliRequest::getInstance()->setApplication($oApp);
 * $oApp->getCommandChain()
 *     ->addCommand(
 *         new cliCommandHelp($oRequest)
 *     )
 * $oApp->execute($oRequest);
 * </code>
 * 
 * @package scorpio
 * @subpackage cli
 * @category cliCommandHelp
 */
class cliCommandHelp extends cliCommand {
	
	const COMMAND = 'help';
	
	/**
	 * Creates a new help command
	 *
	 * @param cliRequest $inRequest
	 */
	function __construct(cliRequest $inRequest) {
		parent::__construct($inRequest, self::COMMAND, null, 'h');
		
		$this->setCommandHelp(
			"Displays help information about the application commands. Use help <command> or help <command> <sub-command> ".
			"for more detailed information on specific commands.\n"
		);
	}
	
	/**
	 * Figures out what to display based on the request. If help <command> has been
	 * used and <command> cannot be found, throws an exception.
	 *
	 * @return void
	 * @throws cliApplicationCommandException
	 */
	function execute() {
		if ( $this->getRequest()->getCount() == 0 || $this->getRequest()->getParam(self::COMMAND) == 1 ) {
			$this->showHelp();
			foreach ( $this->getRequest()->getApplication()->getCommandChain() as $oCommand ) {
				$this->displayArguments($oCommand);
			}
			$this->showNotes();
		} else {
			$parent = null;
			$oCommand = $this->getRequest()->getApplication()->getCommandChain()->getCommand($this->getRequest()->getParam(self::COMMAND));
			if ( $oCommand instanceof cliCommand ) {
				if (
					$this->getRequest()->getParam($oCommand->getCommandPattern())
					&& strlen($this->getRequest()->getParam($oCommand->getCommandPattern())) > 1 
					&& $oCommand->getCommandChain()->hasCommand($this->getRequest()->getParam($oCommand->getCommandPattern()))
					) {
					$parent = $oCommand->getCommandPattern();
					$oCommand = $oCommand->getCommandChain()->getCommand($this->getRequest()->getParam($oCommand->getCommandPattern()));
				}
				
				$this->showHelp($oCommand, $parent);
				if ( $oCommand->getCommandChain() ) {
					foreach ( $oCommand->getCommandChain() as $subCommand ) {
						$this->displayArguments($subCommand, "    ");
					}
				}
				$this->showNotes();
			} else {
				throw new cliApplicationCommandException($this, $this->getRequest()->getParam('help').' is an invalid command');
			}
		}
	}
	
	/**
	 * Displays a help message if the application is run with no parameters
	 *
	 * @param cliCommand $inCommand
	 * @param string $inParent
	 * @return void
	 */
	function showHelp($inCommand = null, $inParent = null) {
		$oResponse = $this->getRequest()->getApplication()->getResponse();
		if ( $inCommand instanceof cliCommand ) {
			$oResponse->addResponse("\nApplication Help System for {$this->getRequest()->getApplication()->getApplicationName()} -> help ".($inParent != null ? $inParent.' ' : '').$inCommand->getCommandPattern());
		} else {
			$oResponse->addResponse("\nApplication Help System for {$this->getRequest()->getApplication()->getApplicationName()}");
		}
		$oResponse->addResponse(cliConsoleTools::drawSeparator());
		if ( $inCommand instanceof cliCommand ) {
			$oResponse->addResponse("\nCommand: \n".system::getScriptFilename().' '.($inParent != null ? $inParent.' ' : '').$inCommand->getCommandPattern());
			$oResponse->addResponse("\nSummary:\n".wordwrap($inCommand->getCommandHelp(), cliConstants::CONSOLE_WIDTH)."\n");
		} else {
			$oResponse->addResponse(wordwrap($this->getRequest()->getApplication()->getApplicationDescription(), cliConstants::CONSOLE_WIDTH)."\n");
		}
		$oResponse->addResponse("Arguments:");
	}
	
	/**
	 * Displays notes about commands and switches
	 *
	 * @return void
	 */
	function showNotes() {
		$this->getRequest()->getApplication()->getResponse()
			->addResponse("\nNotes:")
			->addResponse(cliConsoleTools::drawSeparator())
			->addResponse(
				wordwrap(
					'The order of commands is important. They are processed in the order they appear. Ensure '.
					'all switch commands are placed before actual commands. Aliases (switches) are always '.
					'processed before commands',
					cliConstants::CONSOLE_WIDTH
				)
			);
	}
	
	/**
	 * Creates a preformatted set of text that can be echoed to the command line
	 *
	 * @param cliCommand $inCommand
	 * @param string $inPrefix
	 * @return void
	 */
	function displayArguments($inCommand, $inPrefix = null) {
		$dispLen = 22;
		$return = '  ';
		if ( $inPrefix !== null ) {
			$return .= $inPrefix;
		}
		if ( $inCommand->getCommandAliases()->getCount() > 0 ) {
			$aliases = array();
			foreach ( $inCommand->getCommandAliases() as $alias ) {
				$aliases[] = '-'.$alias;
			}
			$return .= implode(', ', $aliases);
			if ( $inCommand->getCommandPattern() ) {
				$return .= ', ';
			}
		}
		if ( $inCommand->getCommandPattern() ) {
			if ( $inCommand->getCommandIsSwitch() ) {
				 $return .= '--'.$inCommand->getCommandPattern();
			} else {
				$return .= $inCommand->getCommandPattern();
			}
		}
		if ( $inCommand->getCommandRequiresValue() ) {
			if ( $inCommand->getCommandChain()->getCount() > 0 ) {
				$retValues = '[';
				$values = array();
				if ( false ) $oCommand = new cliCommand();
				foreach ( $inCommand->getCommandChain() as $oCommand ) {
					if ( !$oCommand->getCommandIsSwitch() ) {
						$values[] = $oCommand->getCommandPattern();
					}
				}
				$retValues .= implode('|', $values).']';
			} else {
				$retValues = '<value>';
			}
			
			$return .= ($inCommand->getCommandIsSwitch() ? '=' : ' ').$retValues;
		}
		
		if ( strlen($return) < ($dispLen) ) {
			$return = str_pad($return, $dispLen, ' ', STR_PAD_RIGHT);
		}
		
		$argLen = strlen($return);
		$desc = explode("\n", wordwrap($inCommand->getCommandHelp().($inCommand->getCommandIsOptional() ? ' (optional)' : ''), cliConstants::CONSOLE_WIDTH-$dispLen));
		$cnt = count($desc);
		array_walk($desc, 'trim');
		
		for ( $i=0; $i<$cnt; $i++ ) {
			if ( $i == 0 ) {
				if ( ($argLen > $dispLen) || ($argLen == $dispLen && substr($return, -1) != ' ') ) {
					$return .= "\n".str_pad(' ', $dispLen, ' ', STR_PAD_LEFT).$desc[$i];
				} else {
					$return .= $desc[$i];
				}
			} else {
				$return .= str_pad(' ', $dispLen, ' ', STR_PAD_LEFT).$desc[$i];
			}
			if ( $cnt > 1 && isset($desc[$i+1]) ) {
				$return .= "\n";
			}
		}
		$this->getRequest()->getApplication()->getResponse()->addResponse($return);
	}
}