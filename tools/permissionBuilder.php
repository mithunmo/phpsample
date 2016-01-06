<?php
/**
 * permissiongBuilder.php
 * 
 * Extracts basic permissions from the controllers in the baseAdminSite.
 * 
 * @author Dave Redfern
 * @copyright MOFILM Ltd (c) 2009-2010
 * @package mofilm
 * @subpackage tools
 * @category permissionBuilder
 * @version $Rev: 9 $
 */

/*
 * Load dependencies
 */
require_once dirname(dirname(__FILE__)).'/libraries/system.inc';
require_once system::getConfig()->getPathWebsites().'/baseAdminSite/libraries/controller.class.php';
require_once system::getConfig()->getPathWebsites().'/baseAdminSite/libraries/daoController.class.php';

echo "Permission Builder for Admin Site\n--\nThis tool will extract all controller actions from baseAdminSite and create any missing permissions.\n\nSearching for files...\n";

$files = fileObject::parseDir(system::getConfig()->getPathWebsites().'/baseAdminSite/controllers', true);

echo "Found ", count($files), " files for processing\n";

foreach ( $files as $oFile ) {
	if ( strpos($oFile->getFilename(), '.svn') === false && strpos($oFile->getFilename(), 'Controller.class.php') !== false ) {
		echo $oFile->getOriginalFilename(), "\n";
		
		include_once($oFile->getOriginalFilename());
		
		$oReflectObj = new ReflectionClass(str_replace('.class.php', '', $oFile->getFilename()));
		foreach ( $oReflectObj->getConstants() as $constant => $value ) {
			if ( strpos($constant, 'ACTION_') === 0 ) {
				echo "\tAdding permission: ",'admin.'.$oReflectObj->getName().'.'.$value, "\n";
				$oPerm = mofilmPermission::getInstanceByPermission('admin.'.$oReflectObj->getName().'.'.$value);
				$oPerm->setName('admin.'.$oReflectObj->getName().'.'.$value);
				$oPerm->setDescription('Allows access to the controller action: '.$value);
				$oPerm->save();
			}
		}
		echo "Done ", $oReflectObj->getName(), "\n--\n";
	}
}