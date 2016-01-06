<?php
/**
 * mofilmMovieManager
 *
 * Stored in manager.class.php
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage movie
 * @category mofilmMovieManager
 * @version $Rev: 371 $
 */


/**
 * mofilmMovieManager Class
 *
 * This is the main movie object loader. It allows movie objects to be
 * loaded by id or from an array of ids and to populate movie objects
 * in a bulk fashion.
 *
 * By default movieManager only loads active movie records. To load
 * inactive, set the LoadOnlyActive to false.
 *
 * <code>
 * // fetch a movie instance
 * $oMovie = mofilmMovieManager::getInstanceByID(12345);
 *
 * // load all movie details
 * $oMovie = mofilmMovieManager::getInstance()
 *	 ->setLoadMovieDetails(true)
 *	 ->getMovieByID(12345);
 * </code>
 *
 * @package mofilm
 * @subpackage movie
 * @category mofilmMovieManager
 */
class mofilmMovieManager {

	/**
	 * Stores $_LoadOnlyActive
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_LoadOnlyActive;

	/**
	 * Stores $_LoadMovieDetails
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_LoadMovieDetails;


	/**
	 * Returns a new mofilmMovieManager instance
	 *
	 * @return mofilmMovieManager
	 */
	function __construct() {
		$this->_LoadOnlyActive = true;
		$this->_LoadMovieDetails = false;
	}


	/**
	 * Returns an instance of the mofilmMovieManager
	 *
	 * @return mofilmMovieManager
	 * @static
	 */
	static function getInstance() {
		return new mofilmMovieManager();
	}

	/**
	 * Static method to load a movie based on the supplied ID
	 *
	 * @param integer $inMovieID
	 * @return mofilmMovieBase
	 * @throws mofilmException
	 * @static
	 */
	static function getInstanceByID($inMovieID) {
		$oMovieMan = new mofilmMovieManager();
		return $oMovieMan->getMovieByID($inMovieID);
	}

	/**
	 * Static method to load a movie based on the supplied hash string
	 *
	 * @param string $inHash
	 * @return mofilmMovieBase
	 * @throws mofilmException
	 * @static
	 */
	static function getInstanceByHash($inHash) {
		$oMovieMan = new mofilmMovieManager();
		return $oMovieMan->getMovieByHash($inHash);
	}

	/**
	 * Static method that fetches an array of movies preventing multiple SQL queries
	 *
	 * @param array $inArray
	 * @return array(mofilmMovieBase)
	 * @throws mofilmException
	 * @static
	 */
	static function loadInstancesByArray($inArray) {
		$oMovieMan = new mofilmMovieManager();
		return $oMovieMan->loadMoviesByArray($inArray);
	}

	/**
	 * Loads an array of movie objects with additional data, used with search results
	 *
	 * @param array $inArray
	 * @static
	 */
	static function loadMovieObjectArrayWithData(array $inArray = array()) {
		$oMovieMan = new mofilmMovieManager();
		$oMovieMan->_loadMovieDetails($inArray);
	}

	/**
	 * Returns an array of available movie statuses
	 *
	 * @return array
	 * @static
	 */
	static function getAvailableMovieStatuses() {
		return array(
			mofilmMovieBase::STATUS_APPROVED,
			mofilmMovieBase::STATUS_DISPUTED,
			mofilmMovieBase::STATUS_ENCODING,
			mofilmMovieBase::STATUS_FAILED_ENCODING,
			mofilmMovieBase::STATUS_PENDING,
			mofilmMovieBase::STATUS_REJECTED,
			mofilmMovieBase::STATUS_REMOVED,
		);
	}



	/**
	 * Loads a movie based on the supplied ID
	 *
	 * @param integer $inMovieID
	 * @return mofilmMovieBase
	 * @throws mofilmException
	 */
	function getMovieByID($inMovieID) {
		if ( empty($inMovieID) || strlen($inMovieID) < 1 ) {
			throw new mofilmException('Expected movie ID, nothing given');
		}
		if ( !is_numeric($inMovieID) ) {
			throw new mofilmException('Expected movie ID to be numeric');
		}

		$query = '
			SELECT movies.*
			  FROM ' . $this->_buildPermissionsTableSql() . '
			 WHERE movies.ID = ' . $inMovieID . $this->_buildWhereSql();

		return $this->_executeSqlQuery($query, false);
	}
	/**
	 * Loads a movie based on the supplied hash
	 *
	 * @param string $inHash
	 * @return mofilmMovieBase
	 * @throws mofilmException
	 */
	function getMovieByHash($inHash) {
		if ( empty($inHash) || strlen($inHash) < 1 ) {
			throw new mofilmException('Expected movie hash, nothing given');
		}
		if ( !is_string($inHash) ) {
			throw new mofilmException('Expected movie hash to be a string');
		}

		$query = '
			SELECT movies.*
			  FROM ' . $this->_buildPermissionsTableSql() . '
			       INNER JOIN ' . system::getConfig()->getDatabase('mofilm_comms') . '.movieLinks ON (movies.ID = movieLinks.movieID)
			 WHERE movieLinks.hash = ' . dbManager::getInstance()->quote($inHash) . $this->_buildWhereSql();

		return $this->_executeSqlQuery($query, false);
	}

	/**
	 * Fetches an array of movies populating them in one go preventing multiple SQL queries
	 *
	 * @param array $inArray
	 * @return array(mofilmMovieBase)
	 * @throws mofilmException
	 */
	function loadMoviesByArray(array $inArray = array()) {
		if ( count($inArray) < 1 ) {
			throw new mofilmException('Array contains no movies to load');
		}
		$query = '
			SELECT movies.*
			  FROM ' . $this->_buildPermissionsTableSql() . '
			 WHERE movies.ID IN (' . implode(',', $inArray) . ')' . $this->_buildWhereSql();

		$query .= ' ORDER BY FIELD(ID, ' . implode(',', $inArray) . ')';

		return $this->_executeSqlQuery($query, true);
	}

	/**
	 * Builds the permissions links as necessary
	 *
	 * @return string
	 * @access private
	 */
	private function _buildPermissionsTableSql() {
		$return = system::getConfig()->getDatabase('mofilm_content') . '.movies ';

		return $return;
	}

	/**
	 * Builds up an additional where clause
	 *
	 * @return string
	 * @access private
	 */
	private function _buildWhereSql() {
		$return = '';
		if ( $this->getLoadOnlyActive() ) {
			$return .= ' AND movies.active = "Y" ';
		}
		return $return;
	}

	/**
	 * Executes the SQL query, populating the result as necessary, $inFetchAll controls
	 * whether one or all results are returned
	 *
	 * @param string $inSql
	 * @param boolean $inFetchAll
	 * @return mixed
	 * @access private
	 */
	private function _executeSqlQuery($inSql, $inFetchAll = false) {
		$return = array();
		$oStmt = dbManager::getInstance()->prepare($inSql);
		if ( $oStmt->execute() ) {
			foreach ( $oStmt as $row ) {
				$oObject = new mofilmMovie();
				$oObject->loadFromArray($row);
				$return[] = $oObject;

				if ( $inFetchAll === false ) {
					break;
				}
			}
		}
		$oStmt->closeCursor();

		if ( $this->getLoadMovieDetails() ) {
			$this->_loadMovieDetails($return);
		}

		if ( $inFetchAll ) {
			return $return;
		} else {
			if ( isset($return[0]) && is_object($return[0]) ) {
				return $return[0];
			}
		}
		return false;
	}

	/**
	 * Pre-loads movie data
	 *
	 * @param array $inArray
	 * @return array
	 * @access private
	 */
	private function _loadMovieDetails(array $inArray = array()) {
		$inArray = $this->_createIndexedArray($inArray);

		mofilmMovieAssetSet::loadArrayOfMoviesWithProperties($inArray);
		mofilmMovieAwardSet::loadArrayOfMoviesWithProperties($inArray);
		mofilmMovieCategorySet::loadArrayOfMoviesWithProperties($inArray);
		mofilmMovieCommentSet::loadArrayOfMoviesWithProperties($inArray);
		mofilmMovieContributorSet::loadArrayOfMoviesWithProperties($inArray);
		mofilmMovieDataSet::loadArrayOfMoviesWithProperties($inArray);
		mofilmMovieRatingSet::loadArrayOfMoviesWithProperties($inArray);
		mofilmMovieSourceSet::loadArrayOfMoviesWithProperties($inArray);
		mofilmMovieTagSet::loadArrayOfMoviesWithProperties($inArray);
		mofilmMovieTrackSet::loadArrayOfMoviesWithProperties($inArray);
		mofilmUserManager::loadArrayOfMoviesWithProperties($inArray);
                mofilmMovieBroadcastSet::loadArrayOfMoviesWithProperties($inArray);
	}

	/**
	 * Converts a simple array so that it is indexed by the movie ID
	 *
	 * @param array $inArray
	 * @return array
	 * @access private
	 */
	private function _createIndexedArray(array $inArray = array()) {
		$return = array();
		foreach ( $inArray as $oMovie ) {
			if ( $oMovie instanceof mofilmMovieBase ) {
				$return[$oMovie->getID()] = $oMovie;
			} else {
				throw new mofilmException(__CLASS__ . '::' . __METHOD__ . ' Operation failed on a none movie object');
			}
		}
		return $return;
	}


	/**
	 * Returns $_LoadOnlyActive
	 *
	 * @return boolean
	 * @access public
	 */
	function getLoadOnlyActive() {
		return $this->_LoadOnlyActive;
	}

	/**
	 * Set $_LoadOnlyActive to $inLoadOnlyActive
	 *
	 * @param boolean $inLoadOnlyActive
	 * @return mofilmMovieManager
	 * @access public
	 */
	function setLoadOnlyActive($inLoadOnlyActive) {
		if ( $this->_LoadOnlyActive !== $inLoadOnlyActive ) {
			$this->_LoadOnlyActive = $inLoadOnlyActive;
		}
		return $this;
	}

	/**
	 * Returns $_LoadMovieDetails
	 *
	 * @return boolean
	 * @access public
	 */
	function getLoadMovieDetails() {
		return $this->_LoadMovieDetails;
	}

	/**
	 * Set $_LoadMovieDetails to $inLoadMovieDetails
	 *
	 * @param boolean $inLoadMovieDetails
	 * @return mofilmMovieManager
	 * @access public
	 */
	function setLoadMovieDetails($inLoadMovieDetails) {
		if ( $this->_LoadMovieDetails !== $inLoadMovieDetails ) {
			$this->_LoadMovieDetails = $inLoadMovieDetails;
		}
		return $this;
	}
}