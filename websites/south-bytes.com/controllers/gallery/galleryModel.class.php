<?php
/**
 * galleryModel.class.php
 * 
 * galleryModel class
 *
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package websites_south-bytes.com
 * @subpackage controllers
 * @category galleryModel
 * @version $Rev: 11 $
 */


/**
 * galleryModel class
 * 
 * Provides the "gallery" page
 * 
 * @package websites_south-bytes.com
 * @subpackage controllers
 * @category galleryModel
 */
class galleryModel extends mvcModelBase {

	const SOUTH_BYTES_EVENT = 20;
	const SOUTH_BYTES_SOURCE = 138;

	/**
	 * @see mvcModelBase::__construct()
	 */
	function __construct() {
		parent::__construct();
	}

	/**
	 * Gets the top 5 newest additions
	 *
	 * @return mofilmMovieSearchResult
	 */
	function getLatestAdditions() {
		$oSearch = new mofilmMovieSearch();
		$oSearch->setUser(new mofilmUser());
		$oSearch->addEvent(self::SOUTH_BYTES_EVENT);
		$oSearch->setOrderBy(mofilmMovieSearch::ORDERBY_DATE);
		$oSearch->setOrderDirection(mofilmMovieSearch::ORDER_DESC);
		$oSearch->setLoadMovieData(true);
		$oSearch->setLimit(20);

		return $oSearch->search();
	}

	/**
	 * Gets the top 5 rated movies
	 *
	 * @return mofilmMovieSearchResult
	 */
	function getTopRated() {
		$oSearch = new mofilmMovieSearch();
		$oSearch->setUser(new mofilmUser());
		$oSearch->addEvent(self::SOUTH_BYTES_EVENT);
		$oSearch->setStatus(mofilmMovie::STATUS_APPROVED);
		$oSearch->setOrderBy(mofilmMovieSearch::ORDERBY_RATING);
		$oSearch->setOrderDirection(mofilmMovieSearch::ORDER_DESC);
		$oSearch->setLoadMovieData(true);
		$oSearch->setLimit(5);

		return $oSearch->search();
	}
}