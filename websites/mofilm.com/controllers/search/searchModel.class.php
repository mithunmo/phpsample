<?php
/**
 * searchModel.class.php
 * 
 * searchModel class
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_mofilm.com
 * @subpackage controllers
 * @category searchModel
 * @version $Rev: 11 $
 */


/**
 * searchModel class
 * 
 * Provides the "search" page
 * 
 * @package websites_mofilm.com
 * @subpackage controllers
 * @category searchModel
 */
class searchModel extends mofilmMovieSearch {
	
	/**
	 * Stores $_JsonCallback
	 *
	 * @var string
	 * @access protected
	 */
	protected $_JsonCallback = '';
	
	/**
	 * Returns $_JsonCallback
	 *
	 * @return string
	 */
	function getJsonCallback() {
		return $this->_JsonCallback;
	}
	
	/**
	 * Set $_JsonCallback to $inJsonCallback
	 *
	 * @param string $inJsonCallback
	 * @return searchModel
	 */
	function setJsonCallback($inJsonCallback) {
		if ( $inJsonCallback !== $this->_JsonCallback ) {
			$this->_JsonCallback = $inJsonCallback;
			$this->setModified();
		}
		return $this;
	}
}