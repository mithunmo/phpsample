<?php
/**
 * videoPlatformModel.class.php
 * 
 * videoPlatformModel class
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package websites_my.mofilm.in
 * @subpackage controllers
 * @category videoPlatformModel
 * @version $Rev: 623 $
 */


/**
 * videoPlatformModel class
 * 
 * Provides the "videoPlatform" page
 * 
 * @package websites_my.mofilm.in
 * @subpackage controllers
 * @category videoPlatformModel
 */
class videoPlatformModel extends mvcModelBase {
	
	
	/**
	 * Stores $_MovieID
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_MovieID;

	
	
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
		}
		return $this;
	}
	
	/**
	 * Gets the list of renditions based on the movieID
	 * 
	 * @return array 
	 */
	function getMovieDetails() {
			$url = "http://api.brightcove.com/services/library?command=find_video_by_reference_id&media_delivery=http&reference_id=".$this->getMovieID() ."&video_fields=name,renditions&token=Ekg-LmhL4QrFPEdtjwJlyX2Zi4l6mgdiPnWGP0bKIyKKT_94PTKHrw..";
			$ch = curl_init();
			curl_setopt($ch,CURLOPT_URL,$url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$jsonResponse = curl_exec($ch);
			curl_close($ch);
			$result = json_decode($jsonResponse);
		return $result->renditions;
	}
}