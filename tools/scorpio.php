<?php
/**
 * scorpio.php
 * 
 * The main Scorpio CLI utility is for creating components, sites and DAO objects. It
 * includes additional commands for extracting language resources, autoload information
 * and for creating bootstrap files via the {@link systemPackager}.
 * 
 * General usage is:
 * <code>
 * // help system
 * php scorpio.php help
 * 
 * // getting command help
 * php scorpio.php help <command> <subcommand>
 * </code>
 * 
 * For building new objects and sites:
 * <code>
 * // building objects, e.g. a website
 * php scorpio.php new <type> ... 
 * php scorpio.php new site example.com --type=site
 * php scorpio.php new site admin.example.com --type=admin
 * 
 * // to add a new controller to the site
 * php scorpio.php new controller path/to/the/controller --site=example.com
 * 
 * // to create basic data access objects from a table
 * php scorpio.php new dao databasename.tablename --prefix=MyPackage --classname=MyPackageClassTableName
 * 
 * // to create a test case skeleton 
 * php scorpio.php new test MyPackageClassTableName --package=MyPackage
 * </code>
 * 
 * Extracting data from a resource:
 * <code>
 * php scorpio.php extract <type> ...
 * 
 * // extract web autoload data
 * php scorpio.php extract autoload example.com
 * 
 * // extract language data
 * php scorpio.php extract i18n --parser=smarty --compiler=xliff -R \
 *    --resource=example.com --source-locale=en --target-locale=de
 *    
 * // create a bootstrap file
 * php scorpio.php extract bootstrap ../data/config/bootstrap-core.xml
 * </code>
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage tools
 * @category scorpio
 * @version $Rev: 9 $
 */

/*
 * Load dependencies
 */
require_once(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'libraries'.DIRECTORY_SEPARATOR.'system.inc');

/**
 * scorpio
 * 
 * The main Scorpio CLI utility is for creating components, sites and DAO objects. It
 * includes additional commands for extracting language resources, autoload information
 * and for creating bootstrap files via the {@link systemPackager}.
 * 
 * General usage is:
 * <code>
 * // help system
 * php scorpio.php help
 * 
 * // getting command help
 * php scorpio.php help <command> <subcommand>
 * </code>
 * 
 * For building new objects and sites:
 * <code>
 * // building objects, e.g. a website
 * php scorpio.php new <type> ... 
 * php scorpio.php new site example.com --type=site
 * php scorpio.php new site admin.example.com --type=admin
 * 
 * // to add a new controller to the site
 * php scorpio.php new controller path/to/the/controller --site=example.com
 * 
 * // to create basic data access objects from a table
 * php scorpio.php new dao databasename.tablename --prefix=MyPackage --classname=MyPackageClassTableName
 * 
 * // to create a test case skeleton 
 * php scorpio.php new test MyPackageClassTableName --package=MyPackage
 * </code>
 * 
 * Extracting data from a resource:
 * <code>
 * php scorpio.php extract <type> ...
 * 
 * // extract web autoload data
 * php scorpio.php extract autoload example.com
 * 
 * // extract language data
 * php scorpio.php extract i18n --parser=smarty --compiler=xliff -R \
 *    --resource=example.com --source-locale=en --target-locale=de
 *    
 * // create a bootstrap file
 * php scorpio.php extract bootstrap ../data/config/bootstrap-core.xml
 * </code>
 *
 * @package scorpio
 * @subpackage tools
 * @category scorpio
 */
$oApp = new cliApplication(
	'scorpio',
	"A command line tool for creating objects, resources and more in the scorpio system. ".
	"Additional help can be found on each command by using help <command> <command>.\nExample ".
	"usage:\n  php ".basename(__FILE__)." new site example.com\n  php ".basename(__FILE__).
	" help new dao"
);
$oRequest = cliRequest::getInstance()->setApplication($oApp);
$oApp->getCommandChain()
	->addCommand(new cliCommandVersion($oRequest))
	->addCommand(new cliCommandLog($oRequest))
	->addCommand(new cliCommandLogToConsole($oRequest))
	->addCommand(new cliCommandHelp($oRequest))
	->addCommand(
		new cliCommandList(
			$oRequest,
			new cliCommandChain(
				array(
					new systemCommandListDatabase($oRequest),
					new systemCommandListSites($oRequest),
				)
			)
		)
	)
	->addCommand(
		new cliCommandNew(
			$oRequest,
			new cliCommandChain(
				array(
					new systemCommandNewDao($oRequest),
					new systemCommandNewSite($oRequest),
					new systemCommandNewController($oRequest),
					new systemCommandNewReport($oRequest),
					new systemCommandNewTestCase($oRequest),
				)
			)
		)
	)
	->addCommand(
		new cliCommandDelete(
			$oRequest,
			new cliCommandChain(
				array(
					new systemCommandDeleteSiteCache($oRequest),
				)
			)
		)
	)
	->addCommand(new systemCommandExtract($oRequest))
	->addCommand(new systemCommandReporter($oRequest));
$oApp->getListeners()->attachListener(new cliApplicationListenerLog());
$oApp->execute($oRequest);