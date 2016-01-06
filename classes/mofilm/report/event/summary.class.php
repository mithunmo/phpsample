<?php
/**
 * mofilmReportEventSummary
 * 
 * Stored in mofilmReportEventSummary.class.php
 * 
 * @author Dave Redfern
 * @copyright MOFILM Ltd (c) 2009-2010
 * @package mofilm
 * @subpackage report
 * @category mofilmReportEventSummary
 * @version $Rev: 10 $
 */


/**
 * mofilmReportEventSummary
 * 
 * Aggregates all individual Event reports into a single report covering all data
 * for the event. This includes all sources attached to the event.
 *
 * @package mofilm
 * @subpackage report
 * @category mofilmReportEventSummary
 */
class mofilmReportEventSummary extends mofilmReportCollectionBase {
	
	/**
	 * @see reportBase::initialise()
	 */
	function initialise() {
		$this->addReport(
			new mofilmReportEventDownloads(
				$this->getOptionsSet()->getOptions(), $this->getReportStyle()
			)
		);
		$this->addReport(
			new mofilmReportEventUserDownloads(
				$this->getOptionsSet()->getOptions(), $this->getReportStyle()
			)
		);
		$this->addReport(
			new mofilmReportEventCountryDownloads(
				$this->getOptionsSet()->getOptions(), $this->getReportStyle()
			)
		);
		$this->addReport(
			new mofilmReportEventCountryDownloadsBySource(
				$this->getOptionsSet()->getOptions(), $this->getReportStyle()
			)
		);
		$this->addReport(
			new mofilmReportEventUploads(
				$this->getOptionsSet()->getOptions(), $this->getReportStyle()
			)
		);
	}

	/**
	 * @see reportBase::getReportName()
	 *
	 * @return string
	 */
	function getReportName() {
		return 'Event Summary Report';
			
	}

	/**
	 * @see reportBase::getReportDescription()
	 *
	 * @return string
	 */
	function getReportDescription() {
		return
			'Summary report for '.$this->getEvent()->getName().
			' between '.$this->getEvent()->getStartDate().' and '.$this->getEvent()->getEndDate();
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