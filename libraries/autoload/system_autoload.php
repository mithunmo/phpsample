<?php
/**
 * system Autoload component
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage system
 * @category systemAutoload
 */
return array(
	'system' => 'system/system.class.php',
	'systemDaoInterface' => 'system/dao.iface.php',
	'systemDaoValidatorInterface' => 'system/daoValidator.iface.php',
	'systemDateCalendar' => 'system/date/calendar.class.php',
	'systemDateEvent' => 'system/date/event.class.php',
	'systemDateTime' => 'system/date/time.class.php',
	'systemDateTimeZone' => 'system/date/timeZone.class.php',
	'systemException' => 'system/exception.class.php',
	'systemLocale' => 'system/locale.class.php',
	'systemSmartyBase' => 'system/smarty/base.class.php',
	'systemPackager' => 'system/packager.class.php',
	'systemReporterInterface' => 'system/reporter.iface.php',
	
	'systemAutoload' => 'system/autoload/autoload.class.php',
	'systemAutoloadException' => 'system/autoload/exception.class.php',
	'systemAutoloadFileDoesNotExist' => 'system/autoload/exception.class.php',
	'systemAutoloadFileIsNotReadable' => 'system/autoload/exception.class.php',
	'systemAutoloadClassCouldNotBeLoaded' => 'system/autoload/exception.class.php',
	'systemAutoloadClassDoesNotExistInAutoloadCache' => 'system/autoload/exception.class.php',
	
	'systemCommandExtract' => 'system/command/extract.class.php',
	'systemCommandSite' => 'system/command/site.class.php',
	'systemCommandDeleteSiteCache' => 'system/command/delete/siteCache.class.php',
	'systemCommandExtractAutoload' => 'system/command/extract/autoload.class.php',
	'systemCommandExtractMvcAutoload' => 'system/command/extract/mvcAutoload.class.php',
	'systemCommandExtractBootstrap' => 'system/command/extract/bootstrap.class.php',
	'systemCommandExtractTranslation' => 'system/command/extract/translation.class.php',
	'systemCommandListDatabase' => 'system/command/list/database.class.php',
	'systemCommandListSites' => 'system/command/list/sites.class.php',
	'systemCommandNewController' => 'system/command/new/controller.class.php',
	'systemCommandNewDao' => 'system/command/new/dao.class.php',
	'systemCommandNewReport' => 'system/command/new/report.class.php',
	'systemCommandNewSite' => 'system/command/new/site.class.php',
	'systemCommandNewTestCase' => 'system/command/new/testCase.class.php',
	
	'systemConfig' => 'system/config/config.class.php',
	'systemConfigBase' => 'system/config/base.class.php',
	'systemConfigException' => 'system/config/exception.class.php',
	'systemConfigFileCannotBeWritten' => 'system/config/exception.class.php',
	'systemConfigFileNotReadable' => 'system/config/exception.class.php',
	'systemConfigFileNotValidXml' => 'system/config/exception.class.php',
	'systemConfigParam' => 'system/config/param.class.php',
	'systemConfigParamCannotBeOverridden' => 'system/config/exception.class.php',
	'systemConfigParamSet' => 'system/config/paramSet.class.php',
	'systemConfigRootConfigFileMissing' => 'system/config/exception.class.php',
	'systemConfigSection' => 'system/config/section.class.php',

	'systemEvent' => 'system/event.class.php',
	'systemEventDispatcher' => 'system/event/dispatcher.class.php',

	'systemLog' => 'system/log/log.class.php',
	'systemLogException' => 'system/log/exception.class.php',
	'systemLogFilter' => 'system/log/filter.class.php',
	'systemLogLevel' => 'system/log/level.class.php',
	'systemLogNoLogFileSpecified' => 'system/log/exception.class.php',
	'systemLogSource' => 'system/log/source.class.php',
	'systemLogSummary' => 'system/log/summary.class.php',
	'systemLogQueue' => 'system/log/queue.class.php',
	'systemLogWriter' => 'system/log/writer/writer.class.php',
	'systemLogWriterFile' => 'system/log/writer/file.class.php',
	'systemLogWriterFirePhp' => 'system/log/writer/firePhp.class.php',
	'systemLogWriterDb' => 'system/log/writer/db.class.php',
	'systemLogWriterScreen' => 'system/log/writer/screen.class.php',
	'systemLogWriterEmail' => 'system/log/writer/email.class.php',
	'systemLogWriterCli' => 'system/log/writer/cli.class.php',
	'systemLogWritingToFileFailed' => 'system/log/exception.class.php',
	
	'systemRegistry' => 'system/registry/registry.class.php',
	'systemRegistryException' => 'system/registry/exception.class.php',
	'systemRegistryInstanceNotFound' => 'system/registry/exception.class.php',
	'systemRegistryKeyWasNull' => 'system/registry/exception.class.php',
);