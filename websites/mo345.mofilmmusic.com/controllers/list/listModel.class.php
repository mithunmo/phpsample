<?php
/**
 * listModel.class.php
 * 
 * listModel class
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package websites_mofilmmusic.com
 * @subpackage controllers
 * @category listModel
 * @version $Rev: 623 $
 */


/**
 * listModel class
 * 
 * Provides the "list" page
 * 
 * @package websites_mofilmmusic.com
 * @subpackage controllers
 * @category listModel
 */
class listModel extends mvcModelBase {
	
	
	/**
	 *
	 * @var int 
	 */
	protected $_ID;
		
	
	
	/**
	 * @see mvcModelBase::__construct()
	 */
	function __construct() {
		parent::__construct();
	}
	
	
	/**
	 * Set $_Artist to $inArtist
	 *
	 * @param string $inArtist
	 * @return string
	 */
	function setID($inID) {
		if ( $inID !== $this->_ID ) {
			$this->_ID = $inID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_Artist
	 *
	 * @return string
	 */
	function getID() {
		return $this->_ID;
	}
		
	function getMoodList() {
		
		if ( $this->getID() ) {
			
			$arr = momusicTypeLeaf::getChildren($this->getID(),1);
			if ( $arr ) {
				return $arr;
			} else {
				return false;
			}
			
		} else {
			return momusicType::listOfObjectsByType(1);
		}
	}
	
	/**
	 * Gets the list of style
	 * 
	 * 
	 * @return array 
	 */
	function getStyleList() {
		
		if ( $this->getID() ) {
			
			$arr = momusicTypeLeaf::getChildren($this->getID(),2);
			if ( $arr ) {
				return $arr;
			} else {
				return false;
			}
			
		} else {
			return momusicType::listOfObjectsByType(momusicType::MUSIC_STYLE);
		}
	}
	

	function getGenreList() {
		
		if ( $this->getID() ) {
			$arr =  momusicTypeLeaf::getChildren($this->getID(),3);
			if ( $arr ) {
				return $arr;
			} else {
				return false;
			}
                        
                        
		} else {
			return momusicType::listOfObjectsByType(3);
		}
	}
	

	function getInstList() {
		
		if ( $this->getID() ) {
			$arr = momusicTypeLeaf::getChildren($this->getID(),4);
			if ( $arr ) {
				return $arr;
			} else {
				return false;
			}
			
			
		} else {
			return momusicType::listOfObjectsByType(4);
		}
	}
	
	
	
	
	/**
	 *
	 * Gets the sub-category list of a particular music type
	 * 
	 * @return array
	 */
	function getSubList() {
		return momusicTypeLeaf::getChildrenByParentID($this->getID());
	}
	
	
	function getParentName($inID) {		
		return momusicType::getInstance($inID)->getName();
	}

	
	function getFamilyName($inID) {		
		$oLeaf = new momusicTypeLeaf();
		return $oLeaf->getParents($inID); 
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