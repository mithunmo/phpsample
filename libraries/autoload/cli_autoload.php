<?php
/**
 * system Autoload component
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage system
 * @category systemAutoload
 * @version $Rev: 740 $
 */
return array(
	'cliApplication' => 'cli/application.class.php',
	'cliApplicationEvent' => 'cli/application/event.class.php',
	'cliApplicationListener' => 'cli/application/listener.iface.php',
	'cliApplicationListeners' => 'cli/application/listeners.class.php',
	'cliApplicationListenerEmail' => 'cli/application/listener/email.class.php',
	'cliApplicationListenerLog' => 'cli/application/listener/log.class.php',
	'cliApplicationListenerMemUsage' => 'cli/application/listener/memUsage.class.php',
	'cliApplicationListenerTimer' => 'cli/application/listener/timer.class.php',

	'cliCommand' => 'cli/command.class.php',
	'cliCommandAliases' => 'cli/command/aliases.class.php',
	'cliCommandChain' => 'cli/command/chain.class.php',
	'cliCommandConfig' => 'cli/command/config.class.php',
	'cliCommandDelete' => 'cli/command/delete.class.php',
	'cliCommandHelp' => 'cli/command/help.class.php',
	'cliCommandList' => 'cli/command/list.class.php',
	'cliCommandLog' => 'cli/command/log.class.php',
	'cliCommandLogToConsole' => 'cli/command/logToConsole.class.php',
	'cliCommandNew' => 'cli/command/new.class.php',
	'cliCommandNull' => 'cli/command/null.class.php',
	'cliCommandPassword' => 'cli/command/password.class.php',
	'cliCommandProcessLogger' => 'cli/command/processLogger.class.php',
	'cliCommandOption' => 'cli/command/option.class.php',
	'cliCommandRecurse' => 'cli/command/recurse.class.php',
	'cliCommandSerialise' => 'cli/command/serialise.class.php',
	'cliCommandSwitch' => 'cli/command/switch.class.php',
	'cliCommandUpdate' => 'cli/command/update.class.php',
	'cliCommandVersion' => 'cli/command/version.class.php',

	'cliConsoleTools' => 'cli/console/tools.class.php',

	'cliConstants' => 'cli/constants.class.php',

	'cliDaemon' => 'cli/daemon.class.php',

	'cliException' => 'cli/exception.class.php',
	'cliApplicationException' => 'cli/exception.class.php',
	'cliApplicationCommandException' => 'cli/exception.class.php',

	'cliProcessControls' => 'cli/process/controls.class.php',
	'cliProcessInformation' => 'cli/process/information.class.php',
	
	'cliRequest' => 'cli/request.class.php',
	'cliResponse' => 'cli/response.class.php',

	'Console_Color' => 'cli/console/color.class.php',
);