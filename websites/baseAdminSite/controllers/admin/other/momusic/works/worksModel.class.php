<?php
/**
 * worksModel.class.php
 * 
 * worksModel class
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category worksModel
 * @version $Rev: 624 $
 */


/**
 * worksModel class
 * 
 * Provides the "works" page
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category worksModel
 */
class worksModel extends momusicWork implements mvcDaoModelInterface {
	
	
	
	/**
	 * Stores $_Duration
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_Tags;
	
	
	
	/**
	 * Returns a list of objects, optionally from $inOffset for $inLimit
	 *
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @return array
	 */
	function getObjectList($inOffset = null, $inLimit = 30) {
		return momusicWork::listOfObjects($inOffset, $inLimit);
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
	 * Return the current value of the property $_Duration
	 *
	 * @return integer
 	 */
	function getTags() {
		return $this->_Tags;
	}

	/**
	 * Set the object property _Duration to $inDuration
	 *
	 * @param integer $inDuration
	 * @return momusicWorks
	 */
	function setTags($inTags) {
		if ( $inTags !== $this->_Tags ) {
			$this->_Tags = $inTags;
			$this->setModified();
		}
		return $this;
	}
	
	
	
	/**
	 * Returns total object count for this table
	 *
	 * @return integer
	 */
	function getTotalObjects() {
		$query = '
			SELECT COUNT(*) AS Count
			  FROM '.system::getConfig()->getDatabase('momusic_content').'.work where status =0';
		
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
		return new momusicWork();
	}
	
	/**
	 * Loads an existing object with $inPrimaryKey
	 *
	 * @param string $inPrimaryKey
	 * @return systemDaoInterface
	 */
	function getExistingObject($inPrimaryKey) {
		/**
		 * @todo set primary key for this object
		 */
		$this->setID($inPrimaryKey);
		$this->load();
		return $this;
	}
	
	function musicSave() {
		$this->save();
		try { 
			//$tags = explode(",", $this->getTags());
			//$tags[] =  $this->getArtistID();
			//$tags[] =  $this->getTrackName();
			//foreach ( $tags as $oTag ) {
			$this->setTags($this->getTags(). " ".$this->getArtistID()." ". $this->getTrackName());
				$oMofilmTag = momusicTag::getInstanceByTag($this->getTags());
				if ( $oMofilmTag  && $oMofilmTag->getID() > 0 ) {
					$oMomusicTags = new momusicTags();
					$oMomusicTags->setTagID($oMofilmTag->getID());
					$oMomusicTags->setWorksID($this->getID());
					$oMomusicTags->save();
				} else {
					systemLog::message("creating a new tag");
					$oMofilmTag = new momusicTag();
					$oMofilmTag->setName($this->getTags());
					$oMofilmTag->setType(mofilmTag::TYPE_TAG);
					$oMofilmTag->save();

					$oMomusicTags = new momusicTags();
					$oMomusicTags->setTagID($oMofilmTag->getID());
					$oMomusicTags->setWorksID($this->getID());
					$oMomusicTags->save();
				}	

			//}
		} catch ( Exception $e) {
			systemLog::message($e);
		}	
	}
	
	/**
	 * Saves the musicWorks object on edit
	 * 
	 */
	function musicEditSave() {
		$this->save();
		try { 
			//$this->setTags($this->getTags(). " ".$this->getArtistID()." ". $this->getTrackName());
			//momusicTag::getInstance(momusicTags::getInstance($this->getID())->getTagID())->setName($this->getTags())->save();;
		} catch ( Exception $e) {
			systemLog::message($e);
		}	
	}
	
	
}