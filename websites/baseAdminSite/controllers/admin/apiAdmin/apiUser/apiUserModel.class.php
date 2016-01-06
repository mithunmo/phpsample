<?php
/**
 * apiUserModel.class.php
 * 
 * apiUserModel class
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category apiUserModel
 * @version $Rev: 624 $
 */


/**
 * apiUserModel class
 * 
 * Provides the "apiUser" page
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category apiUserModel
 */
class apiUserModel extends mofilmSystemAPIUser implements mvcDaoModelInterface {
	
	/**
	 * Returns a list of objects, optionally from $inOffset for $inLimit
	 *
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @return array
	 */
	function getObjectList($inOffset = null, $inLimit = 30) {
		return mofilmSystemAPIUser::listOfObjects($inOffset, $inLimit);
	}
	
	/**
	 * Returns the object primary key value
	 *
	 * @return string
	 */
	function getPrimaryKey() {
		return parent::getPrimaryKey();
	}
	
	/**
	 * Returns total object count for this table
	 *
	 * @return integer
	 */
	function getTotalObjects() {
		$query = '
			SELECT COUNT(*) AS Count
			  FROM '.system::getConfig()->getDatabase('mofilm_system').'.mofilmAPIUsers';
		
		$oRes = dbManager::getInstance()->query($query);
		$res = $oRes->fetch();
		if ( is_array($res) && count($res) > 0 ) {
			return $res['Count'];
		} else {
			return 0;
		}
	}
	
	/**
	 * Returns the limit needed to get to the last page of results
	 *
	 * @param integer $inLimit
	 * @return integer
	 */
	function getLastPageOffset($inLimit) {
		$total = $this->getTotalObjects();
		
		if ( $inLimit > 0 ) {
			return $inLimit*floor($total/$inLimit);
		} else {
			return 0;
		}
	}

	/**
	 * Returns a new blank object
	 *
	 * @return systemDaoInterface
	 */
	function getNewObject() {
		return new mofilmSystemAPIUser();
	}
	
	/**
	 * Loads an existing object with $inPrimaryKey
	 *
	 * @param string $inPrimaryKey
	 * @return systemDaoInterface
	 */
	function getExistingObject($inPrimaryKey) {
		$this->setID($inPrimaryKey);
		$this->load();
		return $this;
	}

	/**
	 * Returns the Private key based on the ID
	 * 
	 * @param string $inKeyID
	 * @return string
	 */
	function getPrivateKey($inKeyID) {
		return mofilmSystemAPIKey::getInstance($inKeyID)->getPrivateKey();
	}

	/**
	 * Returns the Private key based on the ID
	 *
	 * @param string $inKeyID
	 * @return string
	 */
	function getPublicKey($inKeyID) {
		return mofilmSystemAPIKey::getInstance($inKeyID)->getPublicKey();
	}

	/**
	 * Returns the MofilmKeyID
	 *
	 * @return integer
	 */
	function getMofilmKey() {
		$ignore = array('~', '`', '"', "'", '\\', '/', '?', '{', '}', '(', ')', '[', ']', '^', '!', '|', chr(44), '=', '>', '<', '.', ';', ':', '_');
		$oMofilmAPIKey = new mofilmSystemAPIKey();
		$oMofilmAPIKey->setPrivateKey(utilityEncrypt::createKeyString(64, 45, 122, $ignore));
		$oMofilmAPIKey->setPublicKey(utilityEncrypt::createKeyString(32, 45, 122, $ignore));
		$oMofilmAPIKey->setActive(1);
		$oMofilmAPIKey->save();
		return $oMofilmAPIKey->getID();
	}
}