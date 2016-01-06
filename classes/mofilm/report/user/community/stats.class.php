<?php

/**
 * mofilmReportUserCommunityStats
 *
 * Stored in mofilmReportUserCommunityStats.class.php
 * 
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage report
 * @category mofilmReportUserCommunityStats
 * @version $Rev: 736 $
 */

/**
 * mofilmReportUserCommunityStats
 *
 * mofilmReportUserCommunityStats report class.
 * 
 * @package mofilm
 * @subpackage report
 * @category mofilmReportUserCommunityStats
 */
class mofilmReportUserCommunityStats extends reportBase {

    const OPTION_FROM = 'report.from';

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
        //$this->addReportColumn(new reportColumn('type', ' Type', 40));        


        $this->addReportColumn(new reportColumn('year', 'Year', 20));
        $this->addReportColumn(new reportColumn('month', 'Month', 40));
        //$this->addReportColumn(new reportColumn('count', 'Count', 40));

        $this->addReportColumn(new reportColumn('FinalistRepeat', 'Finalist-Repeat', 40));
        $this->addReportColumn(new reportColumn('FinalistNew', 'Finalist-New', 40));

        $this->addReportColumn(new reportColumn('WinnerRepeat', 'Winner-Repeat', 40));
        $this->addReportColumn(new reportColumn('WinnerNew', 'Winner-New', 40));

        $this->addReportColumn(new reportColumn('BriefDownloadsNew', 'BriefDownloads-New', 40));
        $this->addReportColumn(new reportColumn('BriefDownloadsRepeat', 'BriefDownloads-Repeat', 40));

        $this->addReportColumn(new reportColumn('SubmittersNew', 'Submitters-New', 40));
        $this->addReportColumn(new reportColumn('SubmittersRepeat', 'Submitters-Repeat', 40));

        $this->addReportColumn(new reportColumn('GrantNew', 'Grant-New', 40));
        $this->addReportColumn(new reportColumn('GrantRepeat', 'Grant-Repeat', 40));

        $this->addReportColumn(new reportColumn('GrantApprovedRepeat', 'GrantApproved-Repeat', 40));
        $this->addReportColumn(new reportColumn('GrantApprovedNew', 'GrantApproved-New', 40));


        //$this->addReportColumn(new reportColumn('Grant Approved - NewTotal', 'Grant Approved - NewTotal', 40));
        //$this->addReportColumn(new reportColumn('Grant Approved - New', 'Grant Approved - New', 40));
        //$this->addReportColumn(new reportColumn('Grant Approved - Repeat', 'Grant Approved - Repeat', 40));
    }

    /**
     * @see reportBase::_run()
     *
     * @return boolean
     */
    function _run() {

        $query = "SELECT year,month,count,type  FROM 
                    " . system::getConfig()->getDatabase('mofilm_content') . ".community 
                    where year = :from ";



        $oStmt = dbManager::getInstance()->prepare($query);
        $oStmt->setFetchMode(PDO::FETCH_ASSOC);

        $oStmt->bindValue(':from', $this->getStartYear(), PDO::PARAM_STR);

        if ($oStmt->execute()) {
            $this->getReportData()->query()->beginTransaction();

            $TotalRows = $oStmt->fetchAll();
            $rowtoAdd = "";

            foreach ($TotalRows as $trow) {
                $trow["type"] = preg_replace("/ /", "", $trow["type"]);
                $trow["type"] = preg_replace("/-/", "", $trow["type"]);
                $rowtoAdd[$trow["month"]]["month"] = $trow["month"];
                $rowtoAdd[$trow["month"]]["year"] = $trow["year"];
                $rowtoAdd[$trow["month"]][$trow["type"]] = $trow["count"];
            }

            foreach ($rowtoAdd as $row) {
                $this->getReportData()->addRow($row);
            }

            $this->getReportData()->query()->commit();
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
        return 'User Activity by Month';
    }

    /**
     * @see reportBase::getReportDescription()
     *
     * @return string
     */
    function getReportDescription() {
        return 'Breakdown of brief downloads, grants / ideas, submissions, and wins. Separated by first-time and repeat users. ';
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
    function getStartYear() {
        //return $this->getOption(self::OPTION_FROM);
        $year = $this->getOption(self::OPTION_FROM, '');
        return $year["Year"];
    }

}
