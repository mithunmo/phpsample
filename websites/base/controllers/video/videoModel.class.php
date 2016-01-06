<?php
/**
 * videoModel.class.php
 * 
 * videoModel class
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_base
 * @subpackage controllers
 * @category videoModel
 * @version $Rev: 11 $
 */


/**
 * videoModel class
 * 
 * Provides the "video" page
 * 
 * @package websites_base
 * @subpackage controllers
 * @category videoModel
 */
class videoModel extends mvcModelBase {
	
	/**
	 * Stores $_MovieReference
	 *
	 * @var string
	 * @access protected
	 */
	protected $_MovieReference;
	
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
		$this->reset();
	}
	
	/**
	 * Reset object
	 * 
	 * @return void
	 */
	function reset() {
		$this->_MovieReference = null;
		$this->_Movie = null;
		$this->setModified(false);
	}
	
	
	
	/**
	 * Returns $_MovieReference
	 *
	 * @return string
	 */
	function getMovieReference() {
		return $this->_MovieReference;
	}
	
	/**
	 * Set $_MovieReference to $inMovieReference
	 *
	 * @param string $inMovieReference
	 * @return videoModel
	 */
	function setMovieReference($inMovieReference) {
		if ( $inMovieReference !== $this->_MovieReference ) {
			$this->_MovieReference = $inMovieReference;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns the mofilmMovie instance from the movie reference
	 *
	 * @return mofilmMovie
	 */
	function getMovie() {
		if ( !$this->_Movie instanceof mofilmMovie ) {

			if ( $this->getMovieReference() ) {
				$this->_Movie = mofilmMovieManager::getInstanceByHash($this->getMovieReference());
			}
			
			if ( !$this->_Movie  ) {
				try {
					$this->_Movie = mofilmMovieManager::getInstanceByID($this->getMovieReference());
				}
				catch (Exception $e) {
					
				}
			} 
			
			if ( !$this->_Movie instanceof mofilmMovie ) {
				throw new mvcDistributorInvalidRequestException($this->getMovieReference());
			}
		}
		return $this->_Movie;
	}
	
	/**
	 * Set $_Movie to $inMovie
	 *
	 * @param mofilmMovie $inMovie
	 * @return videoModel
	 */
	function setMovie(mofilmMovieBase $inMovie) {
		if ( $inMovie !== $this->_Movie ) {
			$this->_Movie = $inMovie;
			$this->setModified();
		}
		return $this;
	}
	
	
	/**
	 * Returns the Fullname from email address
	 * 
	 * 
	 * @param string $inEmail
	 * @return string 
	 */
	function getUserName($inEmail) {
		return mofilmUserManager::getInstanceByUsername($inEmail)->getPropername();
	}
	
	/**
	 * Checks if its a valid user or not based on the email address
	 * 
	 * @param string $inEmail
	 * @return boolean 
	 */
	function getValidUser($inEmail) {
		if ( mofilmUserManager::getInstanceByUsername($inEmail) ) {
			return true;
		} else {
			return false;
		}
			
		
	}
	
	/**
	 * Gets the userObject w.r.t emailAddress
	 * 
	 * @param string $inEmail
	 * @return mofilmUser 
	 */
	function getContributingUser($inEmail) {
		return mofilmUserManager::getInstanceByUsername($inEmail); 
	}
	
	
	/**
	 * Checks if the user agent is on Iphone
	 * 
	 * @param type $user_agent
	 * @return type 
	 */
	function isIphone($user_agent=NULL) {
		if(!isset($user_agent)) {
			$user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
		}
		return (strpos($user_agent, 'iPhone') !== FALSE);
	}
	
	/**
	 * Checks if the user agent is on Ipad
	 * 
	 * @param type $user_agent
	 * @return type 
	 */
	function isIpad($user_agent=NULL) {
		if(!isset($user_agent)) {
			$user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
		}
		return (strpos($user_agent, 'iPad') !== FALSE);
	}
		
}