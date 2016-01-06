<?php
/**
 * competitionsView.class.php
 * 
 * competitionsView class
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_mofilm.com
 * @subpackage controllers
 * @category competitionsView
 * @version $Rev: 303 $
 */


/**
 * competitionsView class
 * 
 * Provides the "competitionsView" page
 * 
 * @package websites_mofilm.com
 * @subpackage controllers
 * @category competitionsView
 */
class competitionsView extends mvcView {

	/**
	 * @see mvcViewBase::setupInitialVars()
	 */
	function setupInitialVars() {
		parent::setupInitialVars();

		if ( system::getConfig()->isProduction() ) {
			$this->setCacheLevelMedium();
		} else {
			$this->setCacheLevelNone();
		}
	}
	
	/**
	 * Shows the competitionsView page
	 *
	 * @return void
	 */
	function showAllCompetitionsPage() {
		if ( !$this->isCached($this->getTpl('competitions')) ) {
			
		}
		$this->render($this->getTpl('competitions'));
	}
	
	/**
	 * Shows the specific competition page
	 *
	 * @return void
	 */
	function showCompetitionPage() {
		$tpl = 'competitionDetail';
		$id = md5($this->getController()->getAction());
		
		if ( !$this->isCached($this->getTpl($tpl), $id) ) {
			$this->getEngine()->assign('oModel', utilityOutputWrapper::wrap($this->getModel()));
			$this->getEngine()->assign('oEvent', utilityOutputWrapper::wrap($this->getModel()->getEvent()));
		}
		$this->render($this->getTpl($tpl), $id);
	}
}