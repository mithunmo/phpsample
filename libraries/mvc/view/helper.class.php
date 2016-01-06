<?php
/**
 * mvcViewHelper.class.php
 * 
 * mvcViewHelper class
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage mvc
 * @category mvcViewHelper
 * @version $Rev: 668 $
 */


/**
 * mvcViewHelper class
 * 
 * mvcViewHelper is an abstract class to be used for creating helper
 * objects in the view system. Implementation is similar to Zend_View_Helper
 * in that you extend this class to your helper, and then create a method
 * named after what you want the helper to do. The class should then be named
 * mvcViewHelperMethodName - capitalising the first letter of the method name.
 * 
 * Helpers are then used directly from the view object in the template.
 * Parameters are passed through to the method.
 * 
 * @package scorpio
 * @subpackage mvc
 * @category mvcViewHelper
 */
abstract class mvcViewHelper implements mvcViewHelperInterface {
	
	/**
	 * Stores instance of view requesting the helper 
	 *
	 * @var mvcViewBase
	 * @access protected
	 */
	protected $_View = null;
	
	
	
	/**
	 * Returns $_View
	 *
	 * @return mvcViewBase
	 */
	function getView() {
		return $this->_View;
	}
	
	/**
	 * Set $_View to $inView
	 *
	 * @param mvcViewBase $inView
	 * @return mvcViewHelper
	 */
	function setView(mvcViewBase $inView) {
		if ( $inView !== $this->_View ) {
			$this->_View = $inView;
		}
		return $this;
	}
	
	/**
	 * Will render the helper out of context
	 *
	 * @return string
	 */
	function render() {
		
	}
}