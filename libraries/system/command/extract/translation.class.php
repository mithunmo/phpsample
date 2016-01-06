<?php
/**
 * systemCommandExtractTranslation Class
 * 
 * Stored in systemCommandExtractTranslation.class.php
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage cli
 * @category systemCommandExtractTranslation
 * @version $Rev: 650 $
 */


/**
 * systemCommandExtractTranslation class
 * 
 * Provides a means to pull data out of files and templates that should be translated.
 * The text must be appropriately marked up first and in the case of a website
 * resource, the site must be defined in the system and the options set in the site
 * config file.
 *
 * @package scorpio
 * @subpackage cli
 * @category systemCommandExtractTranslation
 */
class systemCommandExtractTranslation extends cliCommand {
	
	const COMMAND = 'i18n';
	const COMMAND_EXTRACTOR = 'parser';
	const COMMAND_COMPILER = 'compiler';
	const COMMAND_RESOURCE = 'resource';
	const COMMAND_TAG_FUNCTION = 'keyword';
	const COMMAND_TAG_OPENER = 'tag-open';
	const COMMAND_TAG_CLOSER = 'tag-close';
	const COMMAND_LANGUAGE_SOURCE = 'source-locale';
	const COMMAND_LANGUAGE_TARGET = 'target-locale';
	const COMMAND_OUTFILE = 'output-file';
	
	/**
	 * Creates a new command
	 *
	 * @param cliRequest $inRequest
	 */
	function __construct(cliRequest $inRequest) {
		parent::__construct($inRequest, self::COMMAND,
			new cliCommandChain(
				array(
					new cliCommandNull($inRequest, self::COMMAND_EXTRACTOR, 'Extraction engine to use when parsing resources. Supported engines are: '.implode(', ', translateExtractor::getExtractorBackends()), true, true, false),
					new cliCommandNull($inRequest, self::COMMAND_COMPILER, 'Compiler engine to use for building the language data resource. Supported engines are: '.implode(', ', translateExtractor::getExtractorCompilers()), true, true, false),
					new cliCommandNull($inRequest, self::COMMAND_RESOURCE, 'The resource type to analyse either the file path, directory or website domain name.', true, true, false),
					new cliCommandRecurse($inRequest),
					new cliCommandNull($inRequest, self::COMMAND_LANGUAGE_SOURCE, 'Set the language the resources are currently in.', true, true, false),
					new cliCommandNull($inRequest, self::COMMAND_LANGUAGE_TARGET, 'Set the target language the resource will likely be translated into. This is required by some compilers.', true, true),
					new cliCommandNull($inRequest, self::COMMAND_TAG_FUNCTION, 'Specifies the function (method name) that encloses text to be translated, default is __ (double underscore).', true, true, true),
					new cliCommandNull($inRequest, self::COMMAND_TAG_OPENER, 'Specifies the full opening tag for templates that use tags (e.g. Smarty). For websites resources, this value comes from the site config. Default is {t}.', true, true, true),
					new cliCommandNull($inRequest, self::COMMAND_TAG_CLOSER, 'Specifies the full closing tag for templates that use tags (e.g. Smarty). For websites resources, this value comes from the site config. Default is {/t}.', true, true, true),
					new cliCommandNull($inRequest, self::COMMAND_OUTFILE, 'Write output to the specified file. If output is set to -, output is written to standard output.', true),
				)
			)
		);
		
		$this->setCommandHelp(
			'Extracts text to be translated from either a file, directory or website. Directories '.
			'can be optionally recursed (default is not to). A website must exist in the system '.
			'before it can be used. The target must contain marked up data otherwise no extraction '.
			'can be performed. Depending on the extraction type, both an opener and closer markup '.
			'may be required.'
		);
	}
	
	/**
	 * Executes the command
	 *
	 * @return void
	 */
	function execute() {
		$extractor = strtolower($this->getRequest()->getParam(self::COMMAND_EXTRACTOR));
		$compiler = strtolower($this->getRequest()->getParam(self::COMMAND_COMPILER));
		$resource = $this->getRequest()->getParam(self::COMMAND_RESOURCE);
		if ( strlen($extractor) < 3 ) {
			throw new cliApplicationCommandException($this, 'Please specify the extraction engine using --'.self::COMMAND_EXTRACTOR);
		}
		if ( strlen($compiler) < 2 ) {
			throw new cliApplicationCommandException($this, 'Please specify the compiler engine using --'.self::COMMAND_COMPILER);
		}
		if ( strlen($resource) < 5 ) {
			throw new cliApplicationCommandException($this, 'Please specify a resource. Either a file, directory or a registered domain');
		}
		
		if ( file_exists($resource) && is_readable($resource) && !is_dir($resource) ) {
			$resourceType = translateExtractorBackend::OPTIONS_RESOURCE_FILE;
		} elseif ( file_exists($resource) && is_readable($resource) && is_dir($resource) ) {
			$resourceType = translateExtractorBackend::OPTIONS_RESOURCE_DIR;
		} else {
			$resourceType = translateExtractorBackend::OPTIONS_RESOURCE_WEBSITE;
		}
		
		try {
			$backendOptions = array(
				translateExtractorBackend::OPTIONS_SCAN => $resourceType,
				translateExtractorBackend::OPTIONS_RESOURCE_LOCATION => $resource,
				translateExtractorBackend::OPTIONS_TRANSLATION_OPENING_MARKER => $this->getRequest()->getParam(self::COMMAND_TAG_OPENER),
				translateExtractorBackend::OPTIONS_TRANSLATION_CLOSING_MARKER => $this->getRequest()->getParam(self::COMMAND_TAG_CLOSER),
			);
			if ( $this->getRequest()->getSwitch(cliCommandRecurse::COMMAND) ) {
				$backend[translateExtractorBackend::OPTIONS_RESOURCE_RECURSE] = true;
			}
			
			$compilerOptions = array(
				translateExtractorCompiler::OPTIONS_RESOURCE => $resource,
				translateExtractorCompiler::OPTIONS_SOURCE_LANGUAGE => $this->getRequest()->getParam(self::COMMAND_LANGUAGE_SOURCE),
			);
			if ( strlen($this->getRequest()->getParam(self::COMMAND_LANGUAGE_TARGET)) > 1 ) {
				$compilerOptions[translateExtractorCompiler::OPTIONS_TARGET_LANGUAGE] = $this->getRequest()->getParam(self::COMMAND_LANGUAGE_TARGET);
			}
			
			$oExtractor = new translateExtractor(
				$extractor,
				$compiler,
				array(
					translateExtractor::OPTIONS_BACKEND => $backendOptions,
					translateExtractor::OPTIONS_COMPILER => $compilerOptions,
				)
			);
			$res = $oExtractor->execute();
			
			$outFile = $this->getRequest()->getParam(self::COMMAND_OUTFILE); 
			if ( $outFile == '-' ) {
				$this->getRequest()->getApplication()->getResponse()->addResponse($res);
			} else {
				if ( strlen($outFile) > 1 ) {
					if ( !is_writable($outFile) ) {
						throw new Exception("Output file: $outFile is not writable");
					}
				} else {
					if ( strlen($this->getRequest()->getParam(self::COMMAND_LANGUAGE_TARGET)) > 1 ) {
						$locale = $this->getRequest()->getParam(self::COMMAND_LANGUAGE_TARGET);
					} else {
						$locale = $this->getRequest()->getParam(self::COMMAND_LANGUAGE_SOURCE);
					}
					
					$outFile = 'messages.'.$locale.'.'.$compiler;
					if ( $resourceType == translateExtractorBackend::OPTIONS_RESOURCE_WEBSITE ) {
						$outFile = utilityStringFunction::cleanDirSlashes(system::getConfig()->getPathWebsites().'/'.$resource.'/libraries/lang/'.$locale.'/'.$outFile);
						if ( !file_exists(dirname($outFile)) ) {
							$this->getRequest()->getApplication()->notify(
								new cliApplicationEvent(
									cliApplicationEvent::EVENT_INFORMATIONAL,
									'Attempting to create directory: '.dirname($outFile)
								)
							);
							mkdir(dirname($outFile), 0755, true);
						}
					}
				}
				$this->getRequest()->getApplication()->notify(
					new cliApplicationEvent(
						cliApplicationEvent::EVENT_INFORMATIONAL,
						'Attempting to create file: '.$outFile
					)
				);
				$bytes = file_put_contents($outFile, $res);
				
				if ( $bytes > 0 ) {
					$this->getRequest()->getApplication()->notify(
						new cliApplicationEvent(
							cliApplicationEvent::EVENT_INFORMATIONAL,
							'Created file '.$outFile.' successfully'
						)
					);
					$this->getRequest()->getApplication()->getResponse()
						->addResponse("Created file $outFile successfully");
				} else {
					$this->getRequest()->getApplication()->notify(
						new cliApplicationEvent(
							cliApplicationEvent::EVENT_WARNING,
							'Failed to create file: '.$outFile
						)
					);
					$this->getRequest()->getApplication()->getResponse()
						->addResponse("Failed to create $outFile or there was no data to write to file");
				}
			}
			
			$this->getRequest()->getApplication()->notify(
				new cliApplicationEvent(
					cliApplicationEvent::EVENT_OK,
					'Command '.__CLASS__.' completed successfully'
				)
			);
		} catch ( Exception $e ) {
			throw new cliApplicationCommandException($this, $e->getMessage());
		}
	}
}