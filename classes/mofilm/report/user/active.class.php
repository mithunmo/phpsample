<?php

/**
 * mofilmReportUserActive
 *
 * Stored in mofilmReportUserActive.class.php
 * 
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage report
 * @category mofilmReportUserActive
 * @version $Rev: 736 $
 */

/**
 * mofilmReportUserActive
 *
 * mofilmReportUserActive report class.
 * 
 * @package mofilm
 * @subpackage report
 * @category mofilmReportUserActive
 */
class mofilmReportUserActive extends reportBase {

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
        $this->addReportColumn(new reportColumn('year', 'Year', 20));
        $this->addReportColumn(new reportColumn('month', 'Month', 40));
        $this->addReportColumn(new reportColumn('total', 'Total', 40));
    }

    /**
     * @see reportBase::_run()
     *
     * @return boolean
     */
    function _run() {
        
        $query = "SELECT year,month,total  FROM 
                    " . system::getConfig()->getDatabase('mofilm_reports') . ".activeUsers ";
            
        

        $oStmt = dbManager::getInstance()->prepare($query);
        $oStmt->setFetchMode(PDO::FETCH_ASSOC);

        //$oStmt->bindValue(':from', $this->getStartYear(), PDO::PARAM_STR);
        
        if ($oStmt->execute()) {
            $this->getReportData()->query()->beginTransaction();

            foreach ($oStmt as $row) {
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
        return 'MOFILM Active users';
    }

    /**
     * @see reportBase::getReportDescription()
     *
     * @return string
     */
    function getReportDescription() {
        return 'MOFILM Active Users ';
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
