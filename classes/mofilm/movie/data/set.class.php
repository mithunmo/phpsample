<?php
/**
 * mofilmMovieDataSet
 * 
 * Stored in dataSet.class.php
 * 
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage movie
 * @category mofilmMovieDataSet
 * @version $Rev: 10 $
 */


/**
 * mofilmMovieDataSet
 * 
 * Provides access to the movieData table by handling the relationship
 * between the movie and the dataname values. Also provides a method for
 * bulk loading multiple data points into multiple movie records.
 * 
 * @package mofilm
 * @subpackage movie
 * @category mofilmMovieDataSet
 */
class mofilmMovieDataSet extends baseSet implements systemDaoInterface {

	/**
	 * Stores $_MovieID
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_MovieID;
	
	
	
	/**
	 * Returns new instance of mofilmMovieDataSet
	 * 
	 * @param integer $inMovieID
	 * @return mofilmMovieDataSet
	 */
	function __construct($inMovieID = null) {
		$this->reset();
		if ( $inMovieID !== null ) {
			$this->setMovieID($inMovieID);
			$this->load();
		}
	}
	
	

	/**
	 * Attaches the mofilmMovieDataSet to each movie in $inMovies. Expects $inMovies to be indexed by the movie ID
	 *
	 * @param array $inMovies
	 * @return boolean
	 * @static
	 */
	static function loadArrayOfMoviesWithProperties(array $inMovies) {
		 $return = false;
		 $properties = array();
		 if ( count($inMovies) > 0 ) {
		 	$query = '
		 		SELECT movies.ID AS movieID, datanames.name, movieData.value
		 		  FROM '.system::getConfig()->getDatabase('mofilm_content').'.movies
		 		       INNER JOIN '.system::getConfig()->getDatabase('mofilm_content').'.movieData ON (movies.ID = movieData.movieID)
		 		       INNER JOIN '.system::getConfig()->getDatabase('mofilm_content').'.datanames ON (movieData.datanameID = datanames.ID)
		 		 WHERE movies.ID IN ('.implode(',', array_keys($inMovies)).')';
		 	
		 	$oStmt = dbManager::getInstance()->prepare($query);
		 	if ( $oStmt->execute() ) {
		 		foreach ( $oStmt as $row ) {
		 			$properties[$row['movieID']][$row['name']] = $row['value'];
		 		}
		 	}
		 	$oStmt->closeCursor();
		 	
		 	if ( false ) $oMovie = new mofilmMovie();
		 	foreach ( $inMovies as $oMovie ) {
		 		if ( $oMovie instanceof mofilmMovie ) {
		 			if ( array_key_exists($oMovie->getID(), $properties) ) {
		 				$oObject = new mofilmMovieDataSet();
		 				$oObject->setMovieID($oMovie->getID());
		 				$oObject->setProperty($properties[$oMovie->getID()]);
		 				$oObject->setModified(false);
		 				
		 				$oMovie->setDataSet($oObject);
		 				$return = true;
		 			}
		 		}
		 	}
		 }
		 return $return;
	}

	
	
	/**
	 * Deletes the properties
	 * 
	 * @return boolean 
	 * @see systemDaoInterface::delete()
	 */
	function delete() {
		if ( $this->_MovieID ) {
			$query = '
				DELETE FROM '.system::getConfig()->getDatabase('mofilm_content').'.movieData
				 WHERE movieID = :MovieID';
			
			$oStmt = dbManager::getInstance()->prepare($query);
			$oStmt->bindValue(':MovieID', $this->_MovieID);
			if ( $oStmt->execute() ) {
				$oStmt->closeCursor();
				return true;
			}
		}
		return false;
	}

	/**
	 * Loads the set with data
	 * 
	 * @return boolean 
	 * @see systemDaoInterface::load()
	 */
	function load() {
		if ( $this->_MovieID ) {
			$query = '
				SELECT datanames.name, movieData.value
				  FROM '.system::getConfig()->getDatabase('mofilm_content').'.movieData
				       INNER JOIN '.system::getConfig()->getDatabase('mofilm_content').'.datanames ON (movieData.datanameID = datanames.ID)
				 WHERE movieData.movieID = :MovieID';
			
			$oStmt = dbManager::getInstance()->prepare($query);
			$oStmt->bindValue(':MovieID', $this->_MovieID);
			if ( $oStmt->execute() ) {
				foreach ( $oStmt as $row ) {
					$this->setProperty($row['name'], $row['value']);
				}
				$oStmt->closeCursor();
			}
			$this->setModified(false);
			return true;
		}
		return false;
	}

	/**
	 * Resets set to defaults
	 * 
	 * @return systemDaoInterface 
	 * @see systemDaoInterface::reset()
	 */
	function reset() {
		$this->_MovieID = 0;
		return $this->_resetSet();
	}

	/**
	 * Saves object
	 * 
	 * @return boolean 
	 * @see systemDaoInterface::save()
	 */
	function save() {
		if ( $this->_MovieID ) {
			if ( $this->isModified() ) {
				/*
 				 * Delete existing records so we dont get duplicates or old records
 				 */
	 			$this->delete();
				
				if ( $this->getCount() > 0 ) {
					$query = '
						INSERT INTO '.system::getConfig()->getDatabase('mofilm_content').'.movieData
							(movieID, datanameID, value)
						VALUES ';
					
					$values = array();
	 				foreach ( $this as $property => $value ) {
	 					if ( !is_null($value) && $value != '' ) {
		 					$oPropType = mofilmDataname::getInstanceByDataname($property);
		 					if ( !$oPropType->getID() ) {
		 						$oPropType->setName($property);
		 						$oPropType->save();
		 					}
		 					$values[] = "($this->_MovieID, ".$oPropType->getID().", ".dbManager::getInstance()->quote($value).")";
	 					}
	 				}
	 				
	 				if ( count($values) > 0 ) {
		 				$query .= implode(', ', $values);
		 				
		 				$res = dbManager::getInstance()->exec($query);
		 				if ( $res > 0 ) {
		 					$this->setModified(false);
		 				}
	 				}
				}
				return true;
			}
		}
		return false;
	}
	
	

	/**
	 * Returns object properties as array
	 * 
	 * @return array 
	 * @see systemDaoInterface::toArray()
	 */
	function toArray() {
		return get_object_vars($this);
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
	 * Sets $_MovieID to $inMovieID
	 *
	 * @param integer $inMovieID
	 * @return mofilmMovieDataSet
	 */
	function setMovieID($inMovieID) {
		if ( $this->_MovieID !== $inMovieID ) {
			$this->_MovieID = $inMovieID;
			$this->_Modified = true;
		}
		return $this;
	}
	
	/**
	 * Return value of $inProperty
	 *
	 * @param string $inProperty
	 * @return mixed
	 */
	function getProperty($inProperty) {
		return $this->_getItem($inProperty);
	}
	
	/**
	 * Set property to $inValue
	 *
	 * @param string $inProperty
	 * @param mixed $inValue
	 * @return mofilmMovieDataSet
	 */
	function setProperty($inProperty, $inValue = null) {
		return $this->_setItem($inProperty, $inValue);
	}
	
	/**
	 * Returns the country code from the uploaders IP address
	 * 
	 * @return string
	 */
	function getUserCountryCode() {
		$cn = $this->getProperty(mofilmDataname::DATA_USER_COUNTRY_CODE);
		if ( !$cn && $this->getProperty(mofilmDataname::DATA_USER_IP) ) {
			$cn = mofilmUtilities::getCountryFromIpAddress($this->getProperty(mofilmDataname::DATA_USER_IP));
			if ( $cn ) {
				$this->setProperty(mofilmDataname::DATA_USER_COUNTRY_CODE, $cn);
				$this->save();
			}
		}
		return $cn;
	}
}