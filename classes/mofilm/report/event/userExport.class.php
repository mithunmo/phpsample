<?php
/**
 * mofilmReportEventUserExport
 * 
 * Stored in mofilmReportEventUserExport.class.php
 * 
 * @author Dave Redfern
 * @copyright MOFILM Ltd (c) 2009-2010
 * @package mofilm
 * @subpackage report
 * @category mofilmReportEventUserExport
 * @version $Rev: 10 $
 */


/**
 * mofilmReportEventUserExport
 * 
 * Exports user details who downloaded a brief from an event, organised by source
 *
 * @package mofilm
 * @subpackage report
 * @category mofilmReportEventUserExport
 */
class mofilmReportEventUserExport extends mofilmReportCollectionBase {
	
	/**
	 * @see reportBase::isValid()
	 */
	function isValid() {
		if ( !$this->getEventID() || !is_numeric($this->getEventID()) ) {
			throw new reportCentreException('Invalid value supplied for EventID');
		}
		return true;
	}
	
	/**
	 * @see reportBase::initialise()
	 */
	function initialise() {
		/*
		 * Foreach source, add a new instance of the source user export report
		 * but set the source as target
		 */
		$oSet = $this->getEvent()->getSourceSet();
		foreach ( $oSet as $oSource ) {
			$this->addReport(
				new mofilmReportSourceUserExport(
					$this->getOptionsSet()->setOptions(
						array(mofilmReportBase::OPTION_SOURCE_ID => $oSource->getID())
					)->getOptions(),
					$this->getReportStyle()
				)
			);
		}
	}

	/**
	 * @see reportBase::getReportName()
	 *
	 * @return string
	 */
	function getReportName() {
		return 'Event User Export';
	}

	/**
	 * @see reportBase::getReportDescription()
	 *
	 * @return string
	 */
	function getReportDescription() {
		return 'Details of users who downloaded a brief from any source in '.$this->getEvent()->getName();
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
}