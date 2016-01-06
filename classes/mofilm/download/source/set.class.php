<?php

/**
 * mofilmDownloadSourceSet
 * 
 * Stored in mofilmDownloadSourceSet.class.php
 * 
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmDownloadSourceSet
 * @category mofilmDownloadSourceSet
 * @version $Rev: 10 $
 */

/**
 * mofilmDownloadSourceSet Class
 * 
 * Handles mappings between the download asset and the sources allowing
 * for different hash references for each source but to the same
 * download file.
 * 
 * @package mofilm
 * @subpackage mofilmDownloadSourceSet
 * @category mofilmDownloadSourceSet
 */
class mofilmDownloadSourceSet extends baseSet implements systemDaoInterface {

    /**
     * Stores $_DownloadID
     *
     * @var integer
     * @access protected
     */
    protected $_DownloadID;

    /**
     * Creates a new instance of downloadSourceSet
     * 
     * @param integer $inDownloadID
     */
    function __construct($inDownloadID = null) {
        $this->reset();
        if ($inDownloadID !== null) {
            $this->setDownloadID($inDownloadID);
            $this->load();
        }
    }

    /**
     * Deletes object relations
     * 
     * @return boolean
     */
    public function delete() {
        if ($this->getDownloadID()) {
            $query = '
				DELETE FROM ' . system::getConfig()->getDatabase('mofilm_content') . '.downloadSources
				 WHERE downloadID = :DownloadID';

            $oStmt = dbManager::getInstance()->prepare($query);
            $oStmt->bindValue(':DownloadID', $this->getDownloadID());
            if ($oStmt->execute()) {
                $oStmt->closeCursor();
                return true;
            }
        }
        return false;
    }

    /**
     * Loads the set with data
     * 
     * @return boolean
     */
    public function load() {
        if ($this->getDownloadID()) {
            $query = '
				SELECT sources.*, downloadSources.downloadHash
				  FROM ' . system::getConfig()->getDatabase('mofilm_content') . '.downloadSources
				       INNER JOIN ' . system::getConfig()->getDatabase('mofilm_content') . '.sources ON (downloadSources.sourceID = sources.ID)
				 WHERE downloadSources.downloadID = :DownloadID';

            $oStmt = dbManager::getInstance()->prepare($query);
            $oStmt->bindValue(':DownloadID', $this->getDownloadID());
            if ($oStmt->execute()) {
                foreach ($oStmt as $row) {
                    $oObject = new mofilmSource();
                    $oObject->loadFromArray($row);
                    $this->setObject($oObject);
                }
                $oStmt->closeCursor();
            }
            $this->setModified(false);
            return true;
        }
        return false;
    }

    /**
     * Resets the set
     * 
     * @return void
     */
    public function reset() {
        $this->_DownloadID = null;
        $this->_resetSet();
    }

    /**
     * Saves changes to the set
     * 
     * @return boolean
     */
    public function save() {
        if ($this->getDownloadID()) {
            if ($this->isModified()) {
                /*
                 * Delete existing records so we dont get duplicates or old records
                 */
                $this->delete();

                if ($this->getCount() > 0) {
                    $query = '
						INSERT INTO ' . system::getConfig()->getDatabase('mofilm_content') . '.downloadSources
							(downloadID, sourceID, downloadHash)
						VALUES ';

                    $values = array();
                    if (false)
                        $oObject = new mofilmSource();

                    foreach ($this as $oObject) {
                        $oObject->save();
                   $values[] = "({$this->getDownloadID()}, {$oObject->getID()}, " . dbManager::getInstance()->quote(mofilmUtilities::buildMiniHash(mofilmDownloadFile::getInstance($this->getDownloadID())->getFilename())) . ')';
                    }
                    $query .= implode(', ', $values);

                    $res = dbManager::getInstance()->exec($query);
                    if ($res > 0) {
                        $this->setModified(false);
                    }
                }
                return true;
            }
        }
        return false;
    }

    /**
     * Returns object properties as an array
     * 
     * @return array
     */
    public function toArray() {
        return get_object_vars($this);
    }

    /**
     * Returns true if object or sub-objects have been changed
     * 
     * @return boolean
     */
    function isModified() {
        $modified = $this->_Modified;
        if (!$modified && $this->getCount() > 0) {
            foreach ($this as $oObject) {
                $modified = $oObject->isModified() || $modified;
            }
        }
        return $modified;
    }

    /**
     * Returns $_DownloadID
     *
     * @return integer
     */
    function getDownloadID() {
        return $this->_DownloadID;
    }

    /**
     * Set $_DownloadID to $inDownloadID
     *
     * @param integer $inDownloadID
     * @return mofilmDownloadSourceSet
     */
    function setDownloadID($inDownloadID) {
        if ($inDownloadID !== $this->_DownloadID) {
            $this->_DownloadID = $inDownloadID;
            $this->setModified();
        }
        return $this;
    }

    /**
     * Returns the object by ID, returns null if not found
     *
     * @param integer $inObjectID
     * @return mofilmSource
     */
    function getObjectByID($inObjectID) {
        if ($this->getCount() > 0) {
            foreach ($this as $oObject) {
                if ($oObject->getID() == $inObjectID) {
                    return $oObject;
                }
            }
        }
        return null;
    }

    /**
     * Returns the object by hash, returns null if not found
     *
     * @param string $inObjectID
     * @return mofilmSource
     */
    function getObjectByHash($inHash) {
        if ($this->getCount() > 0) {
            foreach ($this as $oObject) {
                if ($oObject->getDownloadHash() == $inHash) {
                    return $oObject;
                }
            }
        }
        return null;
    }

    /**
     * Returns an array containing only the object IDs
     *
     * @return array
     */
    function getObjectIDs() {
        $tmp = array();
        if ($this->getCount() > 0) {
            foreach ($this as $oObject) {
                $tmp[] = $oObject->getID();
            }
        }
        return $tmp;
    }

    /**
     * Sets an array of objects to the set
     *
     * @param array $inArray Array of mofilmSource objects
     * @return mofilmDownloadSourceSet
     */
    function setObjects(array $inArray = array()) {
        return $this->_setItem($inArray);
    }

    /**
     * Add the object to the set
     *
     * @param mofilmSource $inObject
     * @return mofilmDownloadSourceSet
     */
    function setObject(mofilmSource $inObject) {
        return $this->_setValue($inObject);
    }

    /**
     * Removes the object from the set
     *
     * @param mofilmSource $inObject
     * @return mofilmDownloadSourceSet
     */
    function removeObject(mofilmSource $inObject) {
        return $this->_removeItemWithValue($inObject);
    }

}
