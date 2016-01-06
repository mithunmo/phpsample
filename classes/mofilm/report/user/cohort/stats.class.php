<?php

/**
 * mofilmReportUserCohortStats
 *
 * Stored in mofilmReportUserCohortStats.class.php
 * 
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage report
 * @category mofilmReportUserCommunityStats
 * @version $Rev: 736 $
 */

/**
 * mofilmReportUserCohortStats
 *
 * mofilmReportUserCohortStats report class.
 * 
 * @package mofilm
 * @subpackage report
 * @category mofilmReportUserCohortStats
 */
class mofilmReportUserCohortStats extends reportBase {

    const OPTION_CSV = 'report.csvlink';
    const OPTION_ID = 'report.reportID';

    /**
     * @see reportBase::isValid()
     */
    function isValid() {

        return true;
    }

    /**
     * @see reportBase::initialise()
     */
    function initialise() {
        $this->addReportColumn(new reportColumn('email', 'email', 40));
        $this->addReportColumn(new reportColumn('briefDownloads', 'briefDownloads', 40));
        $this->addReportColumn(new reportColumn('Submitters', 'Submitters', 20));
        $this->addReportColumn(new reportColumn('Grants', 'Grants', 40));
        $this->addReportColumn(new reportColumn('GrantsApproved', 'GrantsApproved', 40));
        $this->addReportColumn(new reportColumn('Finalist', 'Finalist', 40));
        $this->addReportColumn(new reportColumn('Winner', 'Winner', 40));
    }

    /**
     * @see reportBase::_run()
     *
     * @return boolean
     */
    function _run() {
        //echo "dd" . $this->getCSVLink();
        //systemLog::message("dd".$this->getCSVLink());
        //echo "ddd" . $this->getReportID();

        $row = 3;
        $query = "";

        if (($handle = fopen($this->getCSVLink(), "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $num = count($data);
                //echo "<p> $num fields in line $row: <br /></p>\n";
                $row++;
                //for ($c=0; $c < $num; $c++) {
                //echo $data[0] . "\n";
                //}

                $query = "SELECT ID  FROM 
                    " . system::getConfig()->getDatabase('mofilm_content') . ".users 
                    where email = :email ";

                $oStmt = dbManager::getInstance()->prepare($query);
                $oStmt->bindValue(':email', $data[0], PDO::PARAM_STR);
                $oStmt->setFetchMode(PDO::FETCH_ASSOC);
                if ($oStmt->execute()) {
                    foreach ($oStmt as $row) {
                        //$row["ID"];
                        if (count($row) > 0) {
                            $query = "select email, 
                    (select count(*) from " . system::getConfig()->getDatabase('mofilm_content') . ".userDownloads where userDownloads.userID = users.ID ) as briefDownloads, 
                    (select count(*) from " . system::getConfig()->getDatabase('mofilm_content') . ".movies where movies.userID = users.ID ) as Submitters,
                    (select count(*) from " . system::getConfig()->getDatabase('mofilm_content') . ".userMovieGrants where userMovieGrants.userID = users.ID ) as Grants,
                    (select count(*) from " . system::getConfig()->getDatabase('mofilm_content') . ".userMovieGrants where userMovieGrants.userID = users.ID and status = 'Approved' ) as GrantsApproved,
                    (select count(*) from " . system::getConfig()->getDatabase('mofilm_content') . ".movieAwards where movieAwards.userID = users.ID and type in ( 'Shortlisted', 'Finalist')) as Finalist,
                    (select count(*) from " . system::getConfig()->getDatabase('mofilm_content') . ".movieAwards where movieAwards.userID = users.ID and type='Finalist') as Winner 
                    from " . system::getConfig()->getDatabase('mofilm_content') . ".users where ID = :userID ";
                            $oStmt = dbManager::getInstance()->prepare($query);
                            $oStmt->bindValue(':userID', $row[ID], PDO::PARAM_STR);
                            $oStmt->setFetchMode(PDO::FETCH_ASSOC);
                            if ($oStmt->execute()) {
                                foreach ($oStmt as $row) {
                                    $this->getReportData()->addRow($row);
                                }
                            }
                        }
                    }
                }
            }
            //$oStmt->closeCursor();
            fclose($handle);


            //$oStmt->bindValue(':from', $this->getStartYear(), PDO::PARAM_STR);
            /*
              if ($oStmt->execute()) {
              $this->getReportData()->query()->beginTransaction();

              foreach ($oStmt as $row) {
              $this->getReportData()->addRow($row);
              }

              $this->getReportData()->query()->commit();
              }
             */
        }

        return true;
    }

    /**
     * @see reportBase::getReportName()
     *
     * @return string
     */
    function getReportName() {
        return 'MOFILM Community Stats';
    }

    /**
     * @see reportBase::getReportDescription()
     *
     * @return string
     */
    function getReportDescription() {
        return 'MOFILM Community Stats ';
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
     * Returns the affiliate
     *
     * @return string
     */
    function getCSVLink() {
        return $this->getOption(self::OPTION_CSV);
    }

    function getReportID() {
        return $this->getOption(self::OPTION_ID);
    }

}
