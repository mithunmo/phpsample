<?php

/**
 * mofilmReportUserGenre
 *
 * Stored in mofilmReportUserGenre.class.php
 * 
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage report
 * @category mofilmReportUserGenre
 * @version $Rev: 736 $
 */

/**
 * mofilmReportUserGenre
 *
 * mofilmReportUserGenre report class.
 * 
 * @package mofilm
 * @subpackage report
 * @category mofilmReportUserGenre
 */
class mofilmReportUserGenre extends reportBase {

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
        
        $query = "SELECT name from 
                    " . system::getConfig()->getDatabase('mofilm_content') . ".filmGenres  ";                    
        $oStmt = dbManager::getInstance()->prepare($query);
        $oStmt->setFetchMode(PDO::FETCH_ASSOC);
        $oStmt->execute();        
        $names = $oStmt->fetchAll();
        //print_r($names);
        $menu = "";
        foreach($names as $row){
            $name = $row["name"];
            $name = preg_replace('/[^A-Za-z0-9\-]/', '', $name);
            $menu[] = $name;
            //print($name);
            $this->addReportColumn(new reportColumn($name,$name, 40));
        }
        
        //print_r($menu);
    }

    /**
     * @see reportBase::_run()
     *
     * @return boolean
     */
    function _run() {
        
        $query = "SELECT year,month,count,name  FROM 
                    " . system::getConfig()->getDatabase('mofilm_reports') . ".UserGenre 
                    where year = :from ";
            
        

        $oStmt = dbManager::getInstance()->prepare($query);
        $oStmt->setFetchMode(PDO::FETCH_ASSOC);

        $oStmt->bindValue(':from', $this->getStartYear(), PDO::PARAM_STR);
        
        if ($oStmt->execute()) {
            $this->getReportData()->query()->beginTransaction();
            
            $TotalRows = $oStmt->fetchAll();
            $rowtoAdd = "";
                        
            foreach($TotalRows as $trow){
                    $trow["name"] = preg_replace('/[^A-Za-z0-9\-]/', '', $trow["name"]);
                    $rowtoAdd[$trow["month"]]["month"] = $trow["month"];
                    $rowtoAdd[$trow["month"]]["year"] = $trow["year"];                
                    $rowtoAdd[$trow["month"]][$trow["name"]] = $trow["count"];
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
        return 'User Preferred Genres';
    }

    /**
     * @see reportBase::getReportDescription()
     *
     * @return string
     */
    function getReportDescription() {
        return 'Summary of user preferred film genres as entered upon registration or through edit profile. ';
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
