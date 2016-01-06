#!/usr/bin/env php
<?php
/**
 * Apps - ReportGenerator - Start
 *
 *
 * @author Mithun Mohan
 * @copyright MOFILM Ltd (c) 2009-2010
 * @package mofilm
 * @subpackage apps
 * @category momusic
 * @version $Rev: 1 $
 */


/*
 * Load dependencies
 */
require_once(dirname(dirname(dirname(__FILE__))).'/libraries/system.inc');


$alist = momusicSyncMovies::listOfObjectsByStatus(0);
$list1 = momusicSyncMovies::listOfObjectsByStatus(2);
$list = array_merge($list, $list1);

foreach ( $list as $oList ) {
	
	$diff = (  time() - strtotime($oList->getDate()) );
	
	if ( $oList->getStatus() ==  2 ) {
			$response = file_get_contents(mofilmConstants::getWebResourcesFolder()."/xml/media_video_".$oList->getUserID().".xml");
			$oXML = simplexml_load_string($response);	

			foreach ( $oXML as $inXML ) {		
				if ( $inXML["id"] == $oList->getUniqID() ) {
						$dom=dom_import_simplexml($inXML);
						$dom->parentNode->removeChild($dom);
						$oList->setStatus(1);
						$oList->save();
						unlink($oList->getPath());
				}

			}

			file_put_contents(mofilmConstants::getWebResourcesFolder()."/xml/media_video_".$oList->getUserID().".xml",$oXML->asXML());	
	}
}