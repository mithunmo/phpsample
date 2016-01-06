<?php
/**
 * xmlModel.class.php
 * 
 * xmlModel class
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_base
 * @subpackage controllers
 * @category xmlModel
 * @version $Rev: 11 $
 */


/**
 * xmlModel class
 * 
 * Provides the "xml" page
 * 
 * @package websites_base
 * @subpackage controllers
 * @category xmlModel
 */
class xmlModel extends mvcModelBase {
	
	/**
	 * Stores $_MovieID
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_MovieID;
	
	/**
	 * Stores $_Movie
	 *
	 * @var mofilmMovie
	 * @access protected
	 */
	protected $_Movie;
	
	
	
	/**
	 * @see mvcModelBase::__construct()
	 */
	function __construct() {
		parent::__construct();
		
		$this->_MovieID = null;
		$this->_Movie = null;
	}

	
	
	/**
	 * Returns $_MovieID
	 *
	 * @return integer
	 */
	function getMovieID() {
		return $this->_MovieID;
	}
	
	/**
	 * Set $_MovieID to $inMovieID
	 *
	 * @param integer $inMovieID
	 * @return videosModel
	 */
	function setMovieID($inMovieID) {
		if ( $inMovieID !== $this->_MovieID ) {
			$this->_MovieID = $inMovieID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns a mofilmMovie object if a movieID is present
	 *
	 * @return mofilmMovie
	 */
	function getMovie() {
		if ( !$this->_Movie instanceof mofilmMovie ) {
			if ( $this->getMovieID() ) {
				$this->_Movie = mofilmMovieManager::getInstanceByID($this->getMovieID());
			}
		}
		return $this->_Movie;
	}
	
	/**
	 * Set $_Movie to $inMovie
	 *
	 * @param mofilmMovie $inMovie
	 * @return videosModel
	 */
	function setMovie($inMovie) {
		if ( $inMovie !== $this->_Movie ) {
			$this->_Movie = $inMovie;
			$this->setModified();
		}
		return $this;
	}
}