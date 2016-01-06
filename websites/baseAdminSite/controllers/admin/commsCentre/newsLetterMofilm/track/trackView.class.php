<?php
/**
 * trackView.class.php
 *
 * trackView class
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category trackView
 * @version $Rev: 624 $
 */


/**
 * trackView class
 *
 * Provides the "trackView" page
 *
 * @package websites_baseAdminSite
 * @subpackage controllers
 * @category trackView
 */
class trackView extends mvcDaoView {

	/**
	 * @see mvcDaoView::assignCustomViewData()
	 */
	function assignCustomViewData() {
		$this->getEngine()->assign('parentController', 'admin');
		$this->getEngine()->assign('newslettersent', utilityOutputWrapper::wrap(mofilmCommsNewsletter::listOfObjects()));
		$this->getEngine()->assign('newsl', utilityOutputWrapper::wrap($inID));
		$this->getEngine()->assign('percentage', 0);
		
	}

	/**
	 * @see mvcDaoView::getObjectListView()
	 */
	function getObjectListView($inID = 2) {
		$this->addJavascriptResource(new mvcViewJavascript('progressBar', mvcViewJavascript::TYPE_FILE, '/libraries/jquery-plugins/jquery.progressbar.min.js'));
		$this->addJavascriptResource(new mvcViewJavascript('jqplot', mvcViewJavascript::TYPE_FILE, '/libraries/jquery-plugins/jqplot/jquery.jqplot.min.js'));
		$this->addJavascriptResource(new mvcViewJavascript('jqplotdate', mvcViewJavascript::TYPE_FILE, '/libraries/jquery-plugins/jqplot/plugins/jqplot.dateAxisRenderer.min.js'));
		$this->addJavascriptResource(new mvcViewJavascript('jqplotcanvasrender', mvcViewJavascript::TYPE_FILE, '/libraries/jquery-plugins/jqplot/plugins/jqplot.canvasTextRenderer.min.js'));
		$this->addJavascriptResource(new mvcViewJavascript('jqplotcanvasaxistick', mvcViewJavascript::TYPE_FILE, '/libraries/jquery-plugins/jqplot/plugins/jqplot.canvasAxisTickRenderer.min.js'));
		$this->addJavascriptResource(new mvcViewJavascript('jqplotcataxis', mvcViewJavascript::TYPE_FILE, '/libraries/jquery-plugins/jqplot/plugins/jqplot.categoryAxisRenderer.min.js'));	
		$this->addCssResource(new mvcViewCss('jqplotcss', mvcViewCss::TYPE_FILE, '/libraries/jquery-plugins/jqplot/jquery.jqplot.css'));
		return $this->getTpl('trackList');

	}

	/**
	 * Returns the count
	 * @param integer list
	 * @return integer count
	 *
	 */
	function sendCountView($count = 0) {

		$arr = array();
		$arr["count"] = $count;
		$response = json_encode($arr);
		echo $response;
	}


	/**
	 * @see mvcDaoView::getObjectFormView()
	 */
	function getObjectFormView() {
		return $this->getTpl('trackForm');
	}
}