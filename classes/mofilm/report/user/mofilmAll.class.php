<?php

/**
 * mofilmReportUserMofilmAll
 *
 * Stored in mofilmReportUserMofilmAll.class.php
 * 
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage report
 * @category mofilmReportUserMofilmAll
 * @version $Rev: 736 $
 */

/**
 * mofilmReportUserMofilmAll
 *
 * mofilmReportUserMofilmAll report class.
 * 
 * @package mofilm
 * @subpackage report
 * @category mofilmReportUserMofilmAll
 */
class mofilmReportUserMofilmAll extends reportBase {

    const OPTION_TERRITORY_ID = 'report.territory.id';

    /**
     * @see reportBase::isValid()
     */
    function isValid() {
        if ($this->getTerritoryID() && !is_numeric($this->getTerritoryID())) {
            throw new reportCentreException('Invalid territory selected for report');
        }
        return true;
    }

    /**
     * @see reportBase::initialise()
     */
    function initialise() {
        $this->addReportColumn(new reportColumn('userID', 'userID', 20));
        $this->addReportColumn(new reportColumn('Name', 'Name', 50));       
        $this->addReportColumn(new reportColumn('email', 'Email Address', 50));
        $this->addReportColumn(new reportColumn('territory', 'Territory', 50));
        $this->addReportColumn(new reportColumn('RegisteredDate', 'Registered Date', 50));
        $this->addReportColumn(new reportColumn('city', 'City', 60));
        $this->addReportColumn(new reportColumn('DOB', 'DOB', 60));
        $this->addReportColumn(new reportColumn('Phone', 'Phone', 40));
        $this->addReportColumn(new reportColumn('Role', 'Role', 40));
        $this->addReportColumn(new reportColumn('ProEnabled', 'ProEnabled', 40));
        $this->addReportColumn(new reportColumn('proOnly', 'proOnly', 40));
        
    }

    /**
     * @see reportBase::_run()
     *
     * @return boolean
     */
    function _run() {
        $query = '
			SELECT
				users.ID AS userID,ProEnabled, proOnly,
				users.registered as RegisteredDate,
				users.email,
                                IFNULL( primaryRole.name, "") AS Role,
				IFNULL( CONCAT(users.firstname, " ", users.surname), "") AS Name,
				IFNULL(territories.country, "Unknown") AS territory,
				 IFNULL((SELECT userData.paramValue FROM ' . system::getConfig()->getDatabase("mofilm_content") . '.userData WHERE userID=users.ID AND paramName="City"),"Unknow") AS city,
				 IFNULL((SELECT userData.paramValue FROM ' . system::getConfig()->getDatabase("mofilm_content") . '.userData WHERE userID=users.ID AND paramName="DateOfBirth"),"Unknown") AS DOB,
				 IFNULL((SELECT userData.paramValue FROM ' . system::getConfig()->getDatabase("mofilm_content") . '.userData WHERE userID=users.ID AND paramName="Phone"),"Unknown") AS Phone

			FROM ' . system::getConfig()->getDatabase('mofilm_content') . '.users
			     LEFT JOIN ' . system::getConfig()->getDatabase('mofilm_content') . '.territories ON (users.territoryID = territories.ID)
                            LEFT JOIN ' . system::getConfig()->getDatabase('mofilm_content') . '.primaryRole ON (users.userRole = primaryRole.ID)                                 
		   WHERE users.enabled = :Enabled
		     AND users.clientID = 0';

        if ($this->getTerritoryID()) {
            $query .= ' AND users.territoryID = :TerritoryID ';
        }

        $oStmt = dbManager::getInstance()->prepare($query);
        $oStmt->setFetchMode(PDO::FETCH_ASSOC);
        $oStmt->bindValue(':Enabled', mofilmUser::ENABLED_Y);
        if ($this->getTerritoryID()) {
            $oStmt->bindValue(':TerritoryID', $this->getTerritoryID());
        }

        if ($oStmt->execute()) {
            try {
                $this->getReportData()->query()->beginTransaction();

                foreach ($oStmt as $row) {
                    systemLog::message($row["userID"]);
                    $row['territory'] = utilityStringFunction::capitaliseEncodedString($row['territory']);
                    $this->getReportData()->addRow($row);
                }

                $this->getReportData()->query()->commit();
            } catch (Exception $e) {
                systemLog::message("Error here");
                systemLog::message($e->getMessage());
            }
        }
        $oStmt->closeCursor();

        return true;
    }

    /**
     * @see reportBase::getReportName()
     *
     * @return string
     */
    function getReportName() {
        if ($this->getTerritoryID()) {
            return 'All Users from a country';
        } else {
            return 'All Users';
        }
    }

    /**
     * @see reportBase::getReportDescription()
     *
     * @return string
     */
    function getReportDescription() {
        if ($this->getTerritoryID()) {
            return ' all users from country';
        } else {
            return 'Report all users from a country ';
        }
    }

    /**
     * @see reportBase::getValidGroupByOptions()
     */
    function getValidGroupByOptions() {
        return array();
    }

    /**
     * @see reportBase::getValidOrderByOptions()
     */
    function getValidOrderByOptions() {
        return array();
    }

    /**
     * Returns the territory to search on
     *
     * @return integer
     */
    function getTerritoryID() {
        return $this->getOption(self::OPTION_TERRITORY_ID);
    }

    /**
     * Returns the territory object
     *
     * @return mofilmTerritory
     */
    function getTerritory() {
        return mofilmTerritory::getInstance($this->getTerritoryID());
    }

    /**
     * Sets the territory id to search on
     *
     * @param integer $inOption
     * @return reportBase
     */
    function setTerritoryID($inOption) {
        return $this->setOptions(array(self::OPTION_TERRITORY_ID => $inOption));
    }

}
