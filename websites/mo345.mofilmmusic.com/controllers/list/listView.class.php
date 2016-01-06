<?php

/**
 * listView.class.php
 * 
 * listView class
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package websites_mofilmmusic.com
 * @subpackage controllers
 * @category listView
 * @version $Rev: 634 $
 */

/**
 * listView class
 * 
 * Provides the "listView" page
 * 
 * @package websites_mofilmmusic.com
 * @subpackage controllers
 * @category listView
 */
class listView extends mvcView {

	/**
	 * @see mvcViewBase::setupInitialVars()
	 */
	function setupInitialVars() {
		parent::setupInitialVars();
		$this->getEngine()->assign('oModel', utilityOutputWrapper::wrap($this->getModel()));

		$this->getEngine()->assign('oLogged', utilityOutputWrapper::wrap($this->getRequest()->getSession()->isLoggedIn()));

		if ( $this->getRequest()->getSession()->isLoggedIn() ) {
			$this->getEngine()->assign('oName', utilityOutputWrapper::wrap($this->getRequest()->getSession()->getUser()->getFirstname()." !"));
		}
		
		$this->addCssResource(new mvcViewCss('mm', mvcViewCss::TYPE_FILE, '/themes/mofilm/css/mm.css?' . mofilmConstants::CSS_VERSION));
		
		$this->addCssResource(new mvcViewCss('sm-css', mvcViewCss::TYPE_FILE, '/libraries/soundmanager/mp3-player-button.css'));
		$this->addJavascriptResource(new mvcViewJavascript('sm', mvcViewJavascript::TYPE_FILE, '/libraries/soundmanager/soundmanager2.js'));
		$this->addJavascriptResource(new mvcViewJavascript('sm-mp3', mvcViewJavascript::TYPE_FILE, '/libraries/soundmanager/mp3-player-button.js'));
		
		$this->addJavascriptResource(new mvcViewJavascript('sm-swf', mvcViewJavascript::TYPE_INLINE, '
			soundManager.setup({
			  // required: path to directory containing SM2 SWF files
			  url: "/libraries/soundmanager/swf/"
			});
	
		'));
		
		/*
		 * Add any further custom setup for the view that is needed on every request
		 */
	}

	/**
	 * Shows the listView page
	 *
	 * @return void
	 */
	function showListPage() {
		$this->setCacheLevelNone();

		$this->render($this->getTpl('list'));
	}

	/**
	 * Shows the listView page
	 *
	 * @return void
	 */
	function showMoodListPage() {
		$this->setCacheLevelNone();

		if ( $this->getModel()->getMoodList() ) {
			$this->getEngine()->assign("oList", $this->getModel()->getMoodList());
			$this->getEngine()->assign("id", $this->getModel()->getID());
			$this->render($this->getTpl('mood'));
		} else {
			$this->getController()->redirect("/music/solrSearch/?mood=" . urlencode(momusicType::getInstance($this->getModel()->getID())->getName()));
		}
	}

	/**
	 * Shows the listView page
	 *
	 * @return void
	 */
	function showStyleListPage() {
		$this->setCacheLevelNone();

		if ( $this->getModel()->getStyleList() ) {
			$this->getEngine()->assign("oList", $this->getModel()->getStyleList());
			$this->getEngine()->assign("id", $this->getModel()->getID());
			$this->render($this->getTpl('style'));
		} else {
			$this->getController()->redirect("/music/solrSearch/?style=" . urlencode(momusicType::getInstance($this->getModel()->getID())->getName()));
		}
	}
	
	/**
	 * Shows the listView page
	 *
	 * @return void
	 */
	function showTempoPage() {
		$this->setCacheLevelNone();
		$this->render($this->getTpl('tempo'));
	}
	
	
	/**
	 * Shows the listView page
	 *
	 * @return void
	 */
	function showInstListPage() {
		$this->setCacheLevelNone();
		
		if ( $this->getModel()->getInstList() ) {
			$this->getEngine()->assign("oList", $this->getModel()->getInstList());
			$this->getEngine()->assign("id", $this->getModel()->getID());
			$this->getEngine()->assign("oModel", $this->getModel());
			$this->render($this->getTpl('inst'));
		} else {
			$this->getController()->redirect("/music/solrSearch/?instrument=" . urlencode(momusicType::getInstance($this->getModel()->getID())->getName()));
		}		
	}

	
	/**
	 * Shows the listView page
	 *
	 * @return void
	 */
	function showGenreListPage() {
		$this->setCacheLevelNone();
		
		if ( $this->getModel()->getGenreList() ) {
			$this->getEngine()->assign("oList", $this->getModel()->getGenreList());
			$this->getEngine()->assign("id", $this->getModel()->getID());
			$this->getEngine()->assign("oModel", $this->getModel());
			$this->render($this->getTpl('genre'));
		} else {
			$this->getController()->redirect("/music/solrSearch/?genre=" . urlencode(momusicType::getInstance($this->getModel()->getID())->getName()));
		}		
            
  /*          
		$this->setCacheLevelNone();
		$this->getEngine()->assign("oList", $this->getModel()->getGenreList());
		$this->getEngine()->assign("id", $this->getModel()->getID());
		$this->getEngine()->assign("oModel", $this->getModel());
		$this->render($this->getTpl('genre'));
             * 
             */
        }
	
	
	
	/**
	 * Shows the listView page
	 *
	 * @return void
	 */
	function showSubListPage() {
		$this->setCacheLevelNone();
		if ( $this->getModel()->getSubList() ) {
			$this->getEngine()->assign("id", $this->getModel()->getID());
			$this->getEngine()->assign("oModel", $this->getModel());
			$this->getEngine()->assign("oList", $this->getModel()->getSubList());
			$arr =  $this->getModel()->getSubList();
			$this->getEngine()->assign("oFamily", $arr[0]);
			$this->render($this->getTpl('sub'));
		} else {
			$this->getController()->redirect("/music/solrSearch/?" . momusicTypeLeaf::getInstance($this->getModel()->getID())->getTypeName() . "=" . urlencode(momusicTypeLeaf::getInstance($this->getModel()->getID())->getName()));
		}
	}

}