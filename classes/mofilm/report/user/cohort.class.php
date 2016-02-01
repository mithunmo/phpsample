<?php

/**
 * mofilmReportUserCohort
 *
 * Stored in mofilmReportUserCohort.class.php
 * 
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage report
 * @category mofilmReportUserCohort
 * @version $Rev: 736 $
 */

/**
 * mofilmReportUserCohort
 *
 * mofilmReportUserCohort report class.
 * 
 * @package mofilm
 * @subpackage report
 * @category mofilmReportUserCohort
 */
class mofilmReportUserCohort extends reportBase {

    const OPTION_FROM = 'report.from';
    const OPTION_TYPE = 'report.type';

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

        $this->addReportColumn(new reportColumn('count', 'Count', 20));
        $this->addReportColumn(new reportColumn('year', 'Year', 20));
        $this->addReportColumn(new reportColumn('month', 'Month', 40));
        $this->addReportColumn(new reportColumn('signupyear', 'Signupyear', 40));
        $this->addReportColumn(new reportColumn('signupmonth', 'Signupmonth', 40));
        $this->addReportColumn(new reportColumn('total', 'total', 40));

        /*
          $query = "SELECT name from
          " . system::getConfig()->getDatabase('mofilm_content') . ".filmGenres  ";
          $oStmt = dbManager::getInstance()->prepare($query);
          $oStmt->setFetchMode(PDO::FETCH_ASSOC);
          $oStmt->execute();
          $names = $oStmt->fetchAll();
          $menu = "";
          foreach($names as $row){
          $name = $row["name"];
          $name = preg_replace('/[^A-Za-z0-9\-]/', '', $name);
          $menu[] = $name;
          $this->addReportColumn(new reportColumn($name,$name, 40));
          }
         * 
         */
    }

    /**
     * @see reportBase::_run()
     *
     * @return boolean
     */
    function _run() {
        $currentYear = date("Y");
$months = array (1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'May',6=>'Jun',7=>'Jul',8=>'Aug',9=>'Sep',10=>'Oct',11=>'Nov',12=>'Dec');
//echo $months[(int)$m];        
        if ($this->getStartYear() != "alltime") {
            for ($i = 1; $i <= 12; $i++) {

                $query = "SELECT count(*) as total  FROM 
                    " . system::getConfig()->getDatabase('mofilm_content') . ".users
                    where year(registered) = " . $this->getStartYear() . " and month(registered) = " . $i;
                $oStmt = dbManager::getInstance()->prepare($query);
                $oStmt->execute();
                $total = $oStmt->fetchAll();

                $query = "SELECT count(*) as count,mm as month ,yy as year  FROM 
                    " . system::getConfig()->getDatabase('mofilm_content') . ".cohort
                    where year(signup) = :from and  type = :type and month(signup) = " . $i . " GROUP BY mm, yy order by mm";

                $oStmt = dbManager::getInstance()->prepare($query);
                $oStmt->setFetchMode(PDO::FETCH_ASSOC);
                print($this->getStartYear());
                $oStmt->bindValue(':from', $this->getStartYear(), PDO::PARAM_STR);
                $oStmt->bindValue(':type', $this->getType(), PDO::PARAM_STR);

                if ($oStmt->execute()) {
                    $this->getReportData()->query()->beginTransaction();

                    $TotalRows = $oStmt->fetchAll();
                    //print_r($TotalRows);
                    /*
                      $rowtoAdd = "";

                      foreach ($TotalRows as $trow) {
                      $trow["name"] = preg_replace('/[^A-Za-z0-9\-]/', '', $trow["name"]);
                      $rowtoAdd[$trow["month"]]["month"] = $trow["month"];
                      $rowtoAdd[$trow["month"]]["year"] = $trow["year"];
                      $rowtoAdd[$trow["month"]][$trow["name"]] = $trow["count"];
                      }
                     */
                    foreach ($TotalRows as $row) {
                        $row["signupyear"] = $this->getStartYear();
                        $row["signupmonth"] = $months[$i];
                        $row["total"] = $total[0][0];
                        $row["month"] = $months[$row["month"]];
                        $this->getReportData()->addRow($row);
                    }

                    $this->getReportData()->query()->commit();
                }
            }

            $oStmt->closeCursor();
        } else {

            for ($j = 2007; $j <= $currentYear; $j++) {
                for ($i = 1; $i <= 12; $i++) {

                    $query = "SELECT count(*) as total  FROM 
                    " . system::getConfig()->getDatabase('mofilm_content') . ".users
                    where year(registered) = " . $j . " and month(registered) = " . $i;
                    $oStmt = dbManager::getInstance()->prepare($query);
                    $oStmt->execute();
                    $total = $oStmt->fetchAll();

                    $query = "SELECT count(*) as count,mm as month ,yy as year  FROM 
                    " . system::getConfig()->getDatabase('mofilm_content') . ".cohort
                    where year(signup) = :from and  type = :type and month(signup) = " . $i . " GROUP BY mm, yy order by mm";

                    $oStmt = dbManager::getInstance()->prepare($query);
                    $oStmt->setFetchMode(PDO::FETCH_ASSOC);
                    print($j);
                    $oStmt->bindValue(':from', $j, PDO::PARAM_STR);
                    $oStmt->bindValue(':type', $this->getType(), PDO::PARAM_STR);

                    if ($oStmt->execute()) {
                        $this->getReportData()->query()->beginTransaction();
                        $TotalRows = $oStmt->fetchAll();
                        //print_r($TotalRows);
                        /*
                          $rowtoAdd = "";

                          foreach ($TotalRows as $trow) {
                          $trow["name"] = preg_replace('/[^A-Za-z0-9\-]/', '', $trow["name"]);
                          $rowtoAdd[$trow["month"]]["month"] = $trow["month"];
                          $rowtoAdd[$trow["month"]]["year"] = $trow["year"];
                          $rowtoAdd[$trow["month"]][$trow["name"]] = $trow["count"];
                          }
                         */
                        foreach ($TotalRows as $row) {
                            $row["signupyear"] = $j;
                            $row["signupmonth"] = $months[$i];
                            $row["total"] = $total[0][0];
                            $row["month"] = $months[$row["month"]];
                            $this->getReportData()->addRow($row);
                        }

                        $this->getReportData()->query()->commit();
                    }
                }
            }
            $oStmt->closeCursor();
        }

        return true;
    }

    /**
     * @see reportBase::getReportName()
     *
     * @return string
     */
    function getReportName() {
        return 'User Cohort Report';
    }

    /**
     * @see reportBase::getReportDescription()
     *
     * @return string
     */
    function getReportDescription() {
        return 'Understand retention and engagement over time of our users by registration cohort.
    ';
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
        $year = $this->getOption(self::OPTION_FROM, '');
        return $year["Year"];
    }

    function getType() {
        return $this->getOption(self::OPTION_TYPE);
    }

}
