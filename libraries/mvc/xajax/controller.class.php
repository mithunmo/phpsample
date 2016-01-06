<?php
/**
 * mvcXajaxController.class.php
 * 
 * mvcXajaxController class
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage mvc
 * @category mvcXajaxController
 * @version $Rev: 650 $
 */


/**
 * mvcXajaxController
 * 
 * Implements xajax for the Scorpio MVC system. In Scorpio <0.3 xajax
 * was integrated into {@link mvcControllerBase}, however it has now been
 * moved out with a view to removing it from the framework as ajax is
 * better handled by jQuery, Prototype or Mootools.
 * 
 * This class has been included as a method to still use xajax in a similar
 * manner as before. There are a few minor differences, firstly: xajax is
 * added as an action to the controller so calls can now be made to the
 * controller itself instead of to an action and then ajaxserver e.g.
 * 
 * Before:
 *   /my/controller/action/ajaxserver
 * 
 * After:
 *   /my/controller/ajaxserver
 * 
 * This change now allows the use of the normal isAuthorised() methods to
 * authenticate the ajax request instead of relying on an additional method.
 *
 * @package scorpio
 * @subpackage mvc
 * @category mvcXajaxController
 */
abstract class mvcXajaxController extends mvcController {
	
	const AJAX_REQUEST_URI = "ajaxserver";
	
	/**
	 * Stores $_Xajax
	 *
	 * @var xajax
	 * @access protected
	 */
	protected $_Xajax;
	
	
	
	/**
	 * @see mvcControllerBase::initialise()
	 */
	function initialise() {
		parent::initialise();
		
		$this->setXajax(new xajax($this->buildUriPath(self::AJAX_REQUEST_URI)));
		$this->getControllerActions()->addAction(self::AJAX_REQUEST_URI);
		$this->addAjaxControls();
	}
	
	/**
	 * Binds additional xajax functions to the xajax object
	 * 
	 * This method should be overridden to add the methods to xajax that
	 * can be executed by calls into the controller.
	 *
	 * @return void
	 * @abstract 
	 */
	function addAjaxControls() {}
	
	/**
	 * Intercept and launch xajax actions
	 *
	 * @return void
	 */
	function launch() {
		if ( $this->getAction() == self::AJAX_REQUEST_URI ) {
			$this->getXajax()->processRequest();
			exit;
		}
	}
	
	
	
	/**
	 * Returns xajax object
	 *
	 * @return xajax
	 */
	function getXajax() {
		return $this->_Xajax;
	}
	
	/**
	 * Set $_Xajax to $inXajax
	 *
	 * @param xajax $inXajax
	 * @return mvcXajaxController
	 */
	function setXajax($inXajax) {
		if ( $inXajax !== $this->_Xajax ) {
			$this->_Xajax = $inXajax;
			$this->setModified();
		}
		return $this;
	}
}