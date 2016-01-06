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
	'translateException' => 'translate/exception.class.php',
	'translateAdaptorException' => 'translate/exception.class.php',
	'translateAdaptorRequestedLanguageNotAvailableException' => 'translate/exception.class.php',
	'translateAdaptorTranslationNotAvailableException' => 'translate/exception.class.php',
	'translateManager' => 'translate/manager.class.php',
	'translateAdaptor' => 'translate/adaptor.class.php',
	'translateExtractor' => 'translate/extractor.class.php',

	'translateAdaptorArray' => 'translate/adaptor/array.class.php',
	'translateAdaptorCsv' => 'translate/adaptor/csv.class.php',
	'translateAdaptorGettext' => 'translate/adaptor/gettext.class.php',
	'translateAdaptorIni' => 'translate/adaptor/ini.class.php',
	'translateAdaptorQt' => 'translate/adaptor/qt.class.php',
	'translateAdaptorTbx' => 'translate/adaptor/tbx.class.php',
	'translateAdaptorTmx' => 'translate/adaptor/tmx.class.php',
	'translateAdaptorXliff' => 'translate/adaptor/xliff.class.php',
	'translateAdaptorXmlTm' => 'translate/adaptor/xmlTm.class.php',

	'translateExtractorBackend' => 'translate/extractor/backend.class.php',
	'translateExtractorBackendPhp' => 'translate/extractor/backend/php.class.php',
	'translateExtractorBackendSmarty' => 'translate/extractor/backend/smarty.class.php',
	'translateExtractorCompiler' => 'translate/extractor/compiler.class.php',
	'translateExtractorCompilerArray' => 'translate/extractor/compiler/array.class.php',
	'translateExtractorCompilerCsv' => 'translate/extractor/compiler/csv.class.php',
	'translateExtractorCompilerGettext' => 'translate/extractor/compiler/gettext.class.php',
	'translateExtractorCompilerIni' => 'translate/extractor/compiler/ini.class.php',
	'translateExtractorCompilerQt' => 'translate/extractor/compiler/qt.class.php',
	'translateExtractorCompilerTmx' => 'translate/extractor/compiler/tmx.class.php',
	'translateExtractorCompilerXliff' => 'translate/extractor/compiler/xliff.class.php',
);