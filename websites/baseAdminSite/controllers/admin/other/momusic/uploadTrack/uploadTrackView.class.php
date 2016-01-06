<?php
/**
 * uploadTrackView.class.php
 * 
 * uploadTrackView class
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category uploadTrackView
 * @version $Rev: 634 $
 */


/**
 * uploadTrackView class
 * 
 * Provides the "uploadTrackView" page
 * 
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category uploadTrackView
 */
class uploadTrackView extends mvcView {

	/**
	 * @see mvcViewBase::setupInitialVars()
	 */
	function setupInitialVars() {
		parent::setupInitialVars();

		/*
		 * Add any further custom setup for the view that is needed on every request
		 */
	}
	
	/**
	 * Shows the uploadTrackView page
	 *
	 * @return void
	 */
	function showUploadTrackPage() {
		$this->setCacheLevelNone();
		
		$this->render($this->getTpl('uploadTrack'));
	}
	
	function showMusicEditPage($inID) {
		$this->setCacheLevelNone();				
		$this->getEngine()->assign('oWork', momusicWork::getInstance($inID));		
		systemLog::message(momusicType::listOfObjectsByType(1));
		$this->getEngine()->assign('oMood', momusicType::completeListOfObjectsByType(1));		
		$this->getEngine()->assign('oStyle', momusicType::completeListOfObjectsByType(2));		
		
		$genre = momusicType::completeListOfObjectsByType(3);
		$genreSub = momusicTypeLeaf::completeListOfObjectsByType(3);
		$genreList = array_merge($genre, $genreSub);
		$this->getEngine()->assign('oGenre', $genreList);		
		

		$inst = momusicType::completeListOfObjectsByType(4);
		$instSub = momusicTypeLeaf::completeListOfObjectsByType(4);
		$instList = array_merge($inst, $instSub);
		systemLog::message($instList);
		$this->getEngine()->assign('oInst', $instList);		
		
		
		
		$this->render($this->getTpl('musicEdit'));
		
	}
}