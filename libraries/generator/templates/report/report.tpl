{'<?php'}
/**
 * {$classname}
 *
 * Stored in {$classname}.class.php
 * 
 * @author {$appAuthor}
 * @copyright {$appCopyright}
 * @package {$package}
 * @subpackage report
 * @category {$classname}
 * @version $Rev: 736 $
 */


/**
 * {$classname}
 *
 * {$classname} report class.
 * 
 * @package {$package}
 * @subpackage report
 * @category {$classname}
 */
class {$classname} extends {if $isCollection}reportCollectionBase{else}reportBase{/if} {
{if $isCollection}

	/**
	 * @see reportBase::initialise()
	 */
	function initialise() {
		/**
		 * @todo Add the sub-reports to the collection
		 */
		$this->addReport(/* new myOtherReport($this->getOptionsSet()->getOptions(), $this->getReportStyle()))*/);
	}

	/**
	 * @see reportBase::getReportName()
	 *
	 * @return string
	 */
	function getReportName() {
		/**
		 * @todo Set the report name, will be used for cache name too
		 */
		return '{$classname}';
	}

	/**
	 * @see reportBase::getReportDescription()
	 *
	 * @return string
	 */
	function getReportDescription() {
		/**
		 * @todo Set the report title
		 */
		return 'Report for {$classname}';
	}
{else}

	/**
	 * @see reportBase::isValid()
	 */
	function isValid() {
		/**
		 * @todo Added validation criteria, if any
		 */
		return true;
	}

	/**
	 * @see reportBase::initialise()
	 */
	function initialise() {
		/**
		 * @todo Add the columns required for this report
		 */
		$this->addReportColumn(new reportColumn('column1', 'Column 1', 20));
		$this->addReportColumn(new reportColumn('column2', 'Column 2', 20));
		$this->addReportColumn(new reportColumn('column3', 'Column 3', 20));
		$this->addReportColumn(new reportColumn('column4', 'Column 4', 20));
	}

	/**
	 * @see reportBase::_run()
	 *
	 * @return boolean
	 */
	function _run() {
		/**
		 * @todo Create report query from a source and attach to report data
		 */
		$query = "";

		$oStmt = dbManager::getInstance()->prepare($query);
		$oStmt->setFetchMode(PDO::FETCH_ASSOC);

		if ( $oStmt->execute() ) {
			$this->getReportData()->query()->beginTransaction();

			foreach ( $oStmt as $row ) {
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
		/**
		 * @todo Set the report name, will be used for cache name too
		 */
		return '{$classname}';
	}

	/**
	 * @see reportBase::getReportDescription()
	 *
	 * @return string
	 */
	function getReportDescription() {
		/**
		 * @todo Set the report title
		 */
		return 'Report for {$classname}';
	}

	/**
	 * @see reportBase::getValidGroupByOptions()
	 */
	function getValidGroupByOptions() {
		/**
		 * @todo Set any group by options if appropriate
		 */
		return array();
	}

	/**
	 * @see reportBase::getValidOrderByOptions()
	 */
	function getValidOrderByOptions() {
		/**
		 * @todo Set any order by options, if appropriate
		 */
		return array();
	}
{/if}
}