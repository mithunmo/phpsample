<?php
/**
 * generic.class.php
 * 
 * mvcViewGeneric class
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage mvc
 * @category mvcViewGeneric
 * @version $Rev: 707 $
 */


/**
 * mvcView
 * 
 * Generic View class that does not rely on a controller for initialisation.
 * This class is useful for plugins, or in situations where you either do not
 * require or do not want a controller.
 *
 * @package scorpio
 * @subpackage mvc
 * @category mvcViewGeneric
 */
class mvcViewGeneric extends mvcViewBase {
	
	/**
	 * Stores $_MvcRequest
	 *
	 * @var mvcRequest
	 * @access protected
	 */
	protected $_MvcRequest;
	
	
	
	/**
	 * Returns a new mvcViewGeneric
	 *
	 * @param mvcRequest $inMvcRequest
	 * @return mvcViewGeneric
	 */
	function __construct(mvcRequest $inMvcRequest) {
		$this->setRequest($inMvcRequest);
		$this->setupEngine();
		$this->setupInitialVars();
	}
	
	

	/**
	 * Returns $_MvcRequest
	 *
	 * @return mvcRequest
	 */
	function getRequest() {
		return $this->_MvcRequest;
	}
	
	/**
	 * Set $_MvcRequest to $inMvcRequest
	 *
	 * @param mvcRequest $inMvcRequest
	 * @return mvcViewGeneric
	 */
	function setRequest(mvcRequest $inMvcRequest) {
		if ( $inMvcRequest !== $this->_MvcRequest ) {
			$this->_MvcRequest = $inMvcRequest;
		}
		return $this;
	}
}