<?php
/**
 * uploadMusicModel.class.php
 * 
 * uploadMusicModel class
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package websites_mofilmmusic.com
 * @subpackage controllers
 * @category uploadMusicModel
 * @version $Rev: 623 $
 */


/**
 * uploadMusicModel class
 * 
 * Provides the "uploadMusic" page
 * 
 * @package websites_mofilmmusic.com
 * @subpackage controllers
 * @category uploadMusicModel
 */
class uploadMusicModel extends mvcModelBase {
	
	/**
	 * @see mvcModelBase::__construct()
	 */
	function __construct() {
		parent::__construct();
	}
	
	/**
	 * Returns total object count for this table
	 *
	 * @return integer
	 */
	function getTotalObjects() {
		$query = '
			SELECT COUNT(*) AS Count
			  FROM '.system::getConfig()->getDatabase('momusic_content').'.work';
		
		$oRes = dbManager::getInstance()->query($query);
		$res = $oRes->fetch();
		if ( is_array($res) && count($res) > 0 ) {
			return $res['Count'];
		} else {
			return 0;
		}
	}
	
	/**
	 * Returns total object count for this table
	 *
	 * @return integer
	 */
	function getTotalActiveObjects() {
		$query = '
			SELECT COUNT(*) AS Count
			  FROM '.system::getConfig()->getDatabase('momusic_content').'.work where status = 0';
		
		$oRes = dbManager::getInstance()->query($query);
		$res = $oRes->fetch();
		if ( is_array($res) && count($res) > 0 ) {
			return $res['Count'];
		} else {
			return 0;
		}
	}
	
	
}