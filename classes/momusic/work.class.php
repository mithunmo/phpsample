<?php
/**
 * momusicWork
 *
 * Stored in momusicWork.class.php
 *
 * @author Mithun Mohan
 * @copyright Mofilm (c) 2009-2010
 * @package momusic
 * @subpackage momusicWork
 * @category momusicWork
 * @version $Rev: 840 $
 */


/**
 * momusicWork Class
 *
 * Provides access to records in momusic.work
 *
 * Creating a new record:
 * <code>
 * $oMomusicWork = new momusicWork();
 * $oMomusicWork->setID($inID);
 * $oMomusicWork->setSongName($inSongName);
 * $oMomusicWork->setFileName($inFileName);
 * $oMomusicWork->setArtistName($inArtistName);
 * $oMomusicWork->setContext($inContext);
 * $oMomusicWork->setGenre1($inGenre1);
 * $oMomusicWork->setGenre2($inGenre2);
 * $oMomusicWork->setGenre3($inGenre3);
 * $oMomusicWork->setMood1($inMood1);
 * $oMomusicWork->setMood2($inMood2);
 * $oMomusicWork->setMood3($inMood3);
 * $oMomusicWork->setStyle1($inStyle1);
 * $oMomusicWork->setStyle2($inStyle2);
 * $oMomusicWork->setStyle3($inStyle3);
 * $oMomusicWork->setKeywords($inKeywords);
 * $oMomusicWork->setInstrument1($inInstrument1);
 * $oMomusicWork->setInstrument2($inInstrument2);
 * $oMomusicWork->setInstrument3($inInstrument3);
 * $oMomusicWork->setInstrument4($inInstrument4);
 * $oMomusicWork->setLanguage($inLanguage);
 * $oMomusicWork->setVocalType($inVocalType);
 * $oMomusicWork->setSoundsLike1($inSoundsLike1);
 * $oMomusicWork->setSoundsLike2($inSoundsLike2);
 * $oMomusicWork->setSoundsLike3($inSoundsLike3);
 * $oMomusicWork->setWriter($inWriter);
 * $oMomusicWork->setComposer($inComposer);
 * $oMomusicWork->setDuration($inDuration);
 * $oMomusicWork->setPlays($inPlays);
 * $oMomusicWork->setDownload($inDownload);
 * $oMomusicWork->setStatus($inStatus);
 * $oMomusicWork->setPath($inPath);
 * $oMomusicWork->setAlbum($inAlbum);
 * $oMomusicWork->setYear($inYear);
 * $oMomusicWork->setPublisher($inPublisher);
 * $oMomusicWork->setDescription($inDescription);
 * $oMomusicWork->setMusicSource($inMusicSource);
 * $oMomusicWork->setPriority($inPriority);
 * $oMomusicWork->setSku($inSku);
 * $oMomusicWork->setLastModified($inLastModified);
 * $oMomusicWork->save();
 * </code>
 *
 * Accessing a record by primary key on constructor:
 * <code>
 * $oMomusicWork = new momusicWork($inID);
 * </code>
 *
 * Access by manually calling load:
 * <code>
 * $oMomusicWork = new momusicWork();
 * $oMomusicWork->setID($inID);
 * $oMomusicWork->load();
 * </code>
 *
 * Accessing a record by instance:
 * <code>
 * $oMomusicWork = momusicWork::getInstance($inID);
 * </code>
 * If there are other unique keys, separate methods should exist for each key.
 *
 * @package momusic
 * @subpackage momusicWork
 * @category momusicWork
 */
class momusicWork implements systemDaoInterface, systemDaoValidatorInterface {

	/**
	 * Character used to separate values in a compound primary key
	 *
	 * @var string
 	 */
	const PRIMARY_KEY_SEPARATOR = '.';

	/**
	 * Container for static instances of momusicWork
	 *
	 * @var array
	 * @access protected
	 * @static
	 */
	protected static $_Instances = array();

	/**
	 * Stores $_Modified
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_Modified = false;

	/**
	 * Stores the validator for this object
	 *
	 * @var utilityValidator
	 * @access protected
	 */
	protected $_Validator;

	/**
	 * Stores $_ID
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_ID;

	/**
	 * Stores $_SongName
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_SongName;

	/**
	 * Stores $_FileName
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_FileName;

	/**
	 * Stores $_ArtistName
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_ArtistName;

	/**
	 * Stores $_Context
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_Context;

	/**
	 * Stores $_Genre1
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_Genre1;

	/**
	 * Stores $_Genre2
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_Genre2;

	/**
	 * Stores $_Genre3
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_Genre3;

	/**
	 * Stores $_Mood1
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_Mood1;

	/**
	 * Stores $_Mood2
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_Mood2;

	/**
	 * Stores $_Mood3
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_Mood3;

	/**
	 * Stores $_Style1
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_Style1;

	/**
	 * Stores $_Style2
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_Style2;

	/**
	 * Stores $_Style3
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_Style3;

	/**
	 * Stores $_Keywords
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_Keywords;

	/**
	 * Stores $_Instrument1
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_Instrument1;

	/**
	 * Stores $_Instrument2
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_Instrument2;

	/**
	 * Stores $_Instrument3
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_Instrument3;

	/**
	 * Stores $_Instrument4
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_Instrument4;

	/**
	 * Stores $_Language
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_Language;

	/**
	 * Stores $_VocalType
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_VocalType;

	/**
	 * Stores $_SoundsLike1
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_SoundsLike1;

	/**
	 * Stores $_SoundsLike2
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_SoundsLike2;

	/**
	 * Stores $_SoundsLike3
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_SoundsLike3;

	/**
	 * Stores $_ResemblesSong1
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_ResemblesSong1;

	/**
	 * Stores $_ResemblesSong2
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_ResemblesSong2;

	/**
	 * Stores $_ResemblesSong3
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_ResemblesSong3;

	/**
	 * Stores $_Writer
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_Writer;

	/**
	 * Stores $_Composer
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_Composer;

	/**
	 * Stores $_Duration
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_Duration;

	/**
	 * Stores $_Plays
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_Plays;

	/**
	 * Stores $_Download
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_Download;

	/**
	 * Stores $_Status
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_Status;

	/**
	 * Stores $_Path
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_Path;

	/**
	 * Stores $_Album
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_Album;

	/**
	 * Stores $_Year
	 *
	 * @var systemDateTime 
	 * @access protected
	 */
	protected $_Year;

	/**
	 * Stores $_Publisher
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_Publisher;

	/**
	 * Stores $_Description
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_Description;

	/**
	 * Stores $_MusicSource
	 *
	 * @var string 
	 * @access protected
	 */
	protected $_MusicSource;

	/**
	 * Stores $_Priority
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_Priority;

	/**
	 * Stores $_Sku
	 *
	 * @var integer 
	 * @access protected
	 */
	protected $_Sku;

	/**
	 * Stores $_LastModified
	 *
	 * @var systemDateTime 
	 * @access protected
	 */
	protected $_LastModified;

	/**
	 * If true, the object is scheduled to be deleted
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_MarkForDeletion = false;
        
        /**
	 * Store Vendor ID
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_VendorID;



	/**
	 * Returns a new instance of momusicWork
	 *
	 * @param integer $inID
	 * @return momusicWork
	 */
	function __construct($inID = null) {
		$this->reset();
		if ( $inID !== null ) {
			$this->setID($inID);
			$this->load();
		}
	}

	/**
	 * Object destructor, used to remove internal object instances
	 *
	 * @return void
 	 */
	function __destruct() {
		if ( $this->_Validator instanceof utilityValidator ) {
			$this->_Validator = null;
		}
	}

	/**
	 * Get an instance of momusicWork by primary key
	 *
	 * @param integer $inID
	 * @return momusicWork
	 * @static
	 */
	public static function getInstance($inID) {
		$key = $inID;

		/**
		 * Check for an existing instance
		 */
		if ( isset(self::$_Instances[$key]) ) {
			return self::$_Instances[$key];
		}

		/**
		 * No instance, create one
		 */
		$oObject = new momusicWork();
		$oObject->setID($inID);
		if ( $oObject->load() ) {
			self::$_Instances[$key] = $oObject;
		}

		return $oObject;
	}

	/**
	 * Returns an array of objects of momusicWork
	 *
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @return array
	 * @static
	 */
	public static function listOfObjects($inOffset = null, $inLimit = 30) {
		/*
		 * Holds values to be assigned during query execution. Values do not need
		 * to be escaped because they are injected into named place-holders in the
		 * prepared query. Add items using $values[':PlaceHolder'] = $value;
  		 */
		$values = array();

		$query = '
			SELECT ID, song_name, file_name, artist_name, context, genre1, genre2, genre3, mood1, mood2, mood3, style1, style2, style3, keywords, instrument1, instrument2, instrument3, instrument4, language, vocal_type, sounds_like1, sounds_like2, sounds_like3, resembles_song1, resembles_song2, resembles_song3, writer, composer, duration, plays, download, status, path, album, year, publisher, description, musicSource, priority, sku, last_modified, vendorID
			  FROM '.system::getConfig()->getDatabase('momusic_content').'.work
			 WHERE status = 0';

		if ( $inOffset !== null ) {
			$query .= ' LIMIT '.$inOffset.','.$inLimit;
		}

		$list = array();

		$oStmt = dbManager::getInstance()->prepare($query);
		if ( $oStmt->execute($values) ) {
			foreach ( $oStmt as $row ) {
				$oObject = new momusicWork();
				$oObject->loadFromArray($row);
				$list[] = $oObject;
			}
		}
		$oStmt->closeCursor();

		return $list;
	}


	/**
	 * Returns an array of objects of momusicWork
	 *
	 * @param integer $inOffset
	 * @param integer $inLimit
	 * @return array
	 * @static
	 */
	public static function getInstanceSongName($inName, $inArtist) {
		/*
		 * Holds values to be assigned during query execution. Values do not need
		 * to be escaped because they are injected into named place-holders in the
		 * prepared query. Add items using $values[':PlaceHolder'] = $value;
  		 */
		$values = array();

		$query = '
			SELECT ID, song_name, file_name, artist_name, context, genre1, genre2, genre3, mood1, mood2, mood3, style1, style2, style3, keywords, instrument1, instrument2, instrument3, instrument4, language, vocal_type, sounds_like1, sounds_like2, sounds_like3, resembles_song1, resembles_song2, resembles_song3, writer, composer, duration, plays, download, status, path, album, year, publisher, description, musicSource, priority, sku, last_modified
			  FROM '.system::getConfig()->getDatabase('momusic_content').'.work ';
		
		$where = array();
		$where[] = ' song_name = :song_name';
		$values[':song_name'] = $inName;
		$where[] = ' artist_name = :artist_name';
		$values[':artist_name'] = $inArtist;
			

		if ( count($where) == 0 ) {
			return false;
		}

		$query .= ' WHERE '.implode(' AND ', $where);
		
		$oWorkObject = null;

		$oStmt = dbManager::getInstance()->prepare($query);
		if ( $oStmt->execute($values) ) {
			foreach ( $oStmt as $row ) {
				$oObject = new momusicWork();
				$oObject->loadFromArray($row);
				$oWorkObject = $oObject;
			}
		}
		$oStmt->closeCursor();

		return $oWorkObject;
	}
	

	/**
	 * Loads a record from the database based on the primary key or first unique index
	 *
	 * @return boolean
	 */
	function load() {
		$return = false;
		$values = array();

		$query = '
			SELECT ID, song_name, file_name, artist_name, context, genre1, genre2, genre3, mood1, mood2, mood3, style1, style2, style3, keywords, instrument1, instrument2, instrument3, instrument4, language, vocal_type, sounds_like1, sounds_like2, sounds_like3, resembles_song1, resembles_song2, resembles_song3, writer, composer, duration, plays, download, status, path, album, year, publisher, description, musicSource, priority, sku, last_modified
			  FROM '.system::getConfig()->getDatabase('momusic_content').'.work';

		$where = array();
		if ( $this->_ID !== 0 ) {
			$where[] = ' ID = :ID ';
			$values[':ID'] = $this->getID();
		}

		if ( count($where) == 0 ) {
			return false;
		}

		$query .= ' WHERE '.implode(' AND ', $where);

		$oStmt = dbManager::getInstance()->prepare($query);

		$this->reset();
		if ( $oStmt->execute($values) ) {
			$row = $oStmt->fetch();
			if ( $row !== false && is_array($row) ) {
				$this->loadFromArray($row);
				$oStmt->closeCursor();
				$return = true;
			}
		}

		return $return;
	}

	/**
	 * Loads a record by array
	 *
	 * @param array $inArray
	 * @return void
 	 */
	function loadFromArray(array $inArray) {
		$this->setID((int)$inArray['ID']);
		$this->setSongName($inArray['song_name']);
		$this->setFileName($inArray['file_name']);
		$this->setArtistName($inArray['artist_name']);
		$this->setContext($inArray['context']);
		$this->setGenre1($inArray['genre1']);
		$this->setGenre2($inArray['genre2']);
		$this->setGenre3($inArray['genre3']);
		$this->setMood1($inArray['mood1']);
		$this->setMood2($inArray['mood2']);
		$this->setMood3($inArray['mood3']);
		$this->setStyle1($inArray['style1']);
		$this->setStyle2($inArray['style2']);
		$this->setStyle3($inArray['style3']);
		$this->setKeywords($inArray['keywords']);
		$this->setInstrument1($inArray['instrument1']);
		$this->setInstrument2($inArray['instrument2']);
		$this->setInstrument3($inArray['instrument3']);
		$this->setInstrument4($inArray['instrument4']);
		$this->setLanguage($inArray['language']);
		$this->setVocalType($inArray['vocal_type']);
		$this->setSoundsLike1($inArray['sounds_like1']);
		$this->setSoundsLike2($inArray['sounds_like2']);
		$this->setSoundsLike3($inArray['sounds_like3']);
		$this->setResemblesSong1($inArray['resembles_song1']);
		$this->setResemblesSong2($inArray['resembles_song2']);
		$this->setResemblesSong3($inArray['resembles_song3']);
		$this->setWriter($inArray['writer']);
		$this->setComposer($inArray['composer']);
		$this->setDuration((int)$inArray['duration']);
		$this->setPlays((int)$inArray['plays']);
		$this->setDownload((int)$inArray['download']);
		$this->setStatus((int)$inArray['status']);
		$this->setPath($inArray['path']);
		$this->setAlbum($inArray['album']);
		$this->setYear($inArray['year']);
		$this->setPublisher($inArray['publisher']);
		$this->setDescription($inArray['description']);
		$this->setMusicSource($inArray['musicSource']);
		$this->setPriority((int)$inArray['priority']);
		$this->setSku((int)$inArray['sku']);
		$this->setLastModified($inArray['last_modified']);
                $this->setVendorID($inArray['vendorID']);
		$this->setModified(false);
	}

	/**
	 * Saves object to the table
	 *
	 * @return boolean
	 */
	function save() {
		$return = false;
		if ( $this->isModified() ) {
			$message = '';
			if ( !$this->isValid($message) ) {
				throw new momusicException($message);
			}

			if ( $this->_Modified ) {
				$query = '
				INSERT INTO '.system::getConfig()->getDatabase('momusic_content').'.work
					( ID, song_name, file_name, artist_name, context, genre1, genre2, genre3, mood1, mood2, mood3, style1, style2, style3, keywords, instrument1, instrument2, instrument3, instrument4, language, vocal_type, sounds_like1, sounds_like2, sounds_like3, resembles_song1, resembles_song2, resembles_song3, writer, composer, duration, plays, download, status, path, album, year, publisher, description, musicSource, priority, sku, last_modified, vendorID )
				VALUES
					( :ID, :SongName, :FileName, :ArtistName, :Context, :Genre1, :Genre2, :Genre3, :Mood1, :Mood2, :Mood3, :Style1, :Style2, :Style3, :Keywords, :Instrument1, :Instrument2, :Instrument3, :Instrument4, :Language, :VocalType, :SoundsLike1, :SoundsLike2, :SoundsLike3, :ResemblesSong1, :ResemblesSong2, :ResemblesSong3, :Writer, :Composer, :Duration, :Plays, :Download, :Status, :Path, :Album, :Year, :Publisher, :Description, :MusicSource, :Priority, :Sku, :LastModified, :VendorID )
				ON DUPLICATE KEY UPDATE
					song_name=VALUES(song_name),
					file_name=VALUES(file_name),
					artist_name=VALUES(artist_name),
					context=VALUES(context),
					genre1=VALUES(genre1),
					genre2=VALUES(genre2),
					genre3=VALUES(genre3),
					mood1=VALUES(mood1),
					mood2=VALUES(mood2),
					mood3=VALUES(mood3),
					style1=VALUES(style1),
					style2=VALUES(style2),
					style3=VALUES(style3),
					keywords=VALUES(keywords),
					instrument1=VALUES(instrument1),
					instrument2=VALUES(instrument2),
					instrument3=VALUES(instrument3),
					instrument4=VALUES(instrument4),
					language=VALUES(language),
					vocal_type=VALUES(vocal_type),
					sounds_like1=VALUES(sounds_like1),
					sounds_like2=VALUES(sounds_like2),
					sounds_like3=VALUES(sounds_like3),
					resembles_song1=VALUES(resembles_song1),
					resembles_song2=VALUES(resembles_song2),
					resembles_song3=VALUES(resembles_song3),
					writer=VALUES(writer),
					composer=VALUES(composer),
					duration=VALUES(duration),
					plays=VALUES(plays),
					download=VALUES(download),
					status=VALUES(status),
					path=VALUES(path),
					album=VALUES(album),
					year=VALUES(year),
					publisher=VALUES(publisher),
					description=VALUES(description),
					musicSource=VALUES(musicSource),
					priority=VALUES(priority),
					sku=VALUES(sku),
					last_modified=VALUES(last_modified),
                                        last_modified=VALUES(vendorID)';

				$oDB = dbManager::getInstance();
				$oStmt = $oDB->prepare($query);
				$oStmt->bindValue(':ID', $this->getID());
				$oStmt->bindValue(':SongName', $this->getSongName());
				$oStmt->bindValue(':FileName', $this->getFileName());
				$oStmt->bindValue(':ArtistName', $this->getArtistName());
				$oStmt->bindValue(':Context', $this->getContext());
				$oStmt->bindValue(':Genre1', $this->getGenre1());
				$oStmt->bindValue(':Genre2', $this->getGenre2());
				$oStmt->bindValue(':Genre3', $this->getGenre3());
				$oStmt->bindValue(':Mood1', $this->getMood1());
				$oStmt->bindValue(':Mood2', $this->getMood2());
				$oStmt->bindValue(':Mood3', $this->getMood3());
				$oStmt->bindValue(':Style1', $this->getStyle1());
				$oStmt->bindValue(':Style2', $this->getStyle2());
				$oStmt->bindValue(':Style3', $this->getStyle3());
				$oStmt->bindValue(':Keywords', $this->getKeywords());
				$oStmt->bindValue(':Instrument1', $this->getInstrument1());
				$oStmt->bindValue(':Instrument2', $this->getInstrument2());
				$oStmt->bindValue(':Instrument3', $this->getInstrument3());
				$oStmt->bindValue(':Instrument4', $this->getInstrument4());
				$oStmt->bindValue(':Language', $this->getLanguage());
				$oStmt->bindValue(':VocalType', $this->getVocalType());
				$oStmt->bindValue(':SoundsLike1', $this->getSoundsLike1());
				$oStmt->bindValue(':SoundsLike2', $this->getSoundsLike2());
				$oStmt->bindValue(':SoundsLike3', $this->getSoundsLike3());
				$oStmt->bindValue(':ResemblesSong1', $this->getResemblesSong1());
				$oStmt->bindValue(':ResemblesSong2', $this->getResemblesSong2());
				$oStmt->bindValue(':ResemblesSong3', $this->getResemblesSong3());
				$oStmt->bindValue(':Writer', $this->getWriter());
				$oStmt->bindValue(':Composer', $this->getComposer());
				$oStmt->bindValue(':Duration', $this->getDuration());
				$oStmt->bindValue(':Plays', $this->getPlays());
				$oStmt->bindValue(':Download', $this->getDownload());
				$oStmt->bindValue(':Status', $this->getStatus());
				$oStmt->bindValue(':Path', $this->getPath());
				$oStmt->bindValue(':Album', $this->getAlbum());
				$oStmt->bindValue(':Year', $this->getYear());
				$oStmt->bindValue(':Publisher', $this->getPublisher());
				$oStmt->bindValue(':Description', $this->getDescription());
				$oStmt->bindValue(':MusicSource', $this->getMusicSource());
				$oStmt->bindValue(':Priority', $this->getPriority());
				$oStmt->bindValue(':Sku', $this->getSku());
				$oStmt->bindValue(':LastModified', $this->getLastModified());
                                $oStmt->bindValue(':VendorID', $this->getVendorID());

				if ( $oStmt->execute() ) {
					if ( !$this->getID() ) {
						$this->setID($oDB->lastInsertId());
					}
					$this->setModified(false);
					$return = true;
				}
			}
		}

		return $return;
	}

	/**
	 * Deletes the object from the table
	 *
	 * @return boolean
	 */
	function delete() {
		$query = '
			DELETE FROM '.system::getConfig()->getDatabase('momusic_content').'.work
			WHERE
				ID = :ID
			LIMIT 1';

		$oStmt = dbManager::getInstance()->prepare($query);
		$oStmt->bindValue(':ID', $this->getID());

		if ( $oStmt->execute() ) {
			$oStmt->closeCursor();
			$this->reset();
			return true;
		}

		return false;
	}

	/**
	 * Resets object properties to defaults
	 *
	 * @return momusicWork
	 */
	function reset() {
		$this->_ID = 0;
		$this->_SongName = '';
		$this->_FileName = '';
		$this->_ArtistName = '';
		$this->_Context = '';
		$this->_Genre1 = '';
		$this->_Genre2 = '';
		$this->_Genre3 = '';
		$this->_Mood1 = '';
		$this->_Mood2 = '';
		$this->_Mood3 = '';
		$this->_Style1 = '';
		$this->_Style2 = '';
		$this->_Style3 = '';
		$this->_Keywords = '';
		$this->_Instrument1 = '';
		$this->_Instrument2 = '';
		$this->_Instrument3 = '';
		$this->_Instrument4 = '';
		$this->_Language = '';
		$this->_VocalType = '';
		$this->_SoundsLike1 = '';
		$this->_SoundsLike2 = '';
		$this->_SoundsLike3 = '';
		$this->_ResemblesSong1 = '';
		$this->_ResemblesSong2 = '';
		$this->_ResemblesSong3 = '';
		$this->_Writer = '';
		$this->_Composer = '';
		$this->_Duration = 0;
		$this->_Plays = 0;
		$this->_Download = 0;
		$this->_Status = 0;
		$this->_Path = '';
		$this->_Album = '';
		$this->_Year = new systemDateTime('now', system::getConfig()->getSystemTimeZone()->getParamValue());
		$this->_Publisher = '';
		$this->_Description = '';
		$this->_MusicSource = '';
		$this->_Priority = 0;
		$this->_Sku = 0;
		$this->_LastModified = new systemDateTime('now', system::getConfig()->getSystemTimeZone()->getParamValue());
		$this->_Validator = null;
		$this->setModified(false);
		$this->setMarkForDeletion(false);
		return $this;
	}

	/**
	 * Returns properties of object as an array
	 *
	 * @return array
	 */
	function toArray() {
		return get_object_vars($this);
	}

	/**
	 * Returns the validator, creating one if not set
	 *
	 * @return utilityValidator
	 */
	function getValidator() {
		if ( !$this->_Validator instanceof utilityValidator ) {
			$this->_Validator = new utilityValidator();
		}
		return $this->_Validator;
	}

	/**
	 * Set a pre-built validator instance
	 *
	 * @param utilityValidator $inValidator
	 * @return momusicWork
	 */
	function setValidator(utilityValidator $inValidator) {
		$this->_Validator = $inValidator;
		return $this;
	}

	/**
	 * Returns true if object is valid, any errors are added to $inMessage
	 *
	 * @param string $inMessage
	 * @return boolean
	 */
	function isValid(&$inMessage = '') {
		$valid = true;

		$oValidator = $this->getValidator();
		$oValidator->reset();
		$oValidator->setData($this->toArray())->setRules($this->getValidationRules());
		if ( !$oValidator->isValid() ) {
			foreach ( $oValidator->getMessages() as $key => $messages ) {
				$inMessage .= "Error with $key: ".implode(', ', $messages)."\n";
			}
			$valid = false;
		}

		return $valid;
	}

	/**
	 * Returns the array of rules used to validate this object
	 *
	 * @return array
 	 */
	function getValidationRules() {
		return array(
			'_ID' => array(
				'number' => array(),
			),
			'_SongName' => array(
				'string' => array(),
			),
			'_FileName' => array(
				'string' => array(),
			),
			'_ArtistName' => array(
				'string' => array(),
			),
			'_Context' => array(
				'string' => array(),
			),
			'_Genre1' => array(
				'string' => array(),
			),
			'_Genre2' => array(
				'string' => array(),
			),
			'_Genre3' => array(
				'string' => array(),
			),
			'_Mood1' => array(
				'string' => array(),
			),
			'_Mood2' => array(
				'string' => array(),
			),
			'_Mood3' => array(
				'string' => array(),
			),
			'_Style1' => array(
				'string' => array(),
			),
			'_Style2' => array(
				'string' => array(),
			),
			'_Style3' => array(
				'string' => array(),
			),
			'_Instrument1' => array(
				'string' => array(),
			),
			'_Instrument2' => array(
				'string' => array(),
			),
			'_Instrument3' => array(
				'string' => array(),
			),
			'_Instrument4' => array(
				'string' => array(),
			),
			'_Language' => array(
				'string' => array(),
			),
			
			'_VocalType' => array(
				'string' => array(),
			),
			'_SoundsLike1' => array(
				'string' => array(),
			),
			'_SoundsLike2' => array(
				'string' => array(),
			),
			'_SoundsLike3' => array(
				'string' => array(),
			),
			'_ResemblesSong1' => array(
				'string' => array(),
			),
			'_ResemblesSong2' => array(
				'string' => array(),
			),
			'_ResemblesSong3' => array(
				'string' => array(),
			),
			'_Writer' => array(
				'string' => array(),
			),
			'_Composer' => array(
				'string' => array(),
			),
			'_Duration' => array(
				'number' => array(),
			),
			'_Plays' => array(
				'number' => array(),
			),
			'_Download' => array(
				'number' => array(),
			),
			'_Status' => array(
				'number' => array(),
			),
			'_Path' => array(
				'string' => array(),
			),
			'_Album' => array(
				'string' => array(),
			),
			'_Year' => array(
				'dateTime' => array(),
			),
			'_Publisher' => array(
				'string' => array(),
			),
			'_Description' => array(
				'string' => array(),
			),
			'_MusicSource' => array(
				'string' => array(),
			),
			'_Priority' => array(
				'number' => array(),
			),
			'_Sku' => array(
				'number' => array(),
			),
			'_LastModified' => array(
				'dateTime' => array(),
			),
		);
	}



	/**
	 * Returns true if object has been modified
	 *
	 * @return boolean
	 */
	function isModified() {
		$modified = $this->_Modified;

		return $modified;
	}

	/**
	 * Set the status of the object if it has been changed
	 *
	 * @param boolean $status
	 * @return momusicWork
	 */
	function setModified($status = true) {
		$this->_Modified = $status;
		return $this;
	}

	/**
	 * Returns the primaryKey
	 *
	 * @return string
	 */
	function getPrimaryKey() {
		return $this->_ID;
	}

	/**
	 * Sets the primaryKey for the object
	 *
	 * The primary key should be a string separated by the class defined
	 * separator string e.g. X.Y.Z where . is the character from:
	 * momusicWork::PRIMARY_KEY_SEPARATOR.
 	 *
	 * @param string $inKey
	 * @return momusicWork
  	 */
	function setPrimaryKey($inKey) {
		list($ID) = explode(self::PRIMARY_KEY_SEPARATOR, $inKey);
		$this->setID($ID);
	}

	/**
	 * Return the current value of the property $_ID
	 *
	 * @return integer
 	 */
	function getID() {
		return $this->_ID;
	}

	/**
	 * Set the object property _ID to $inID
	 *
	 * @param integer $inID
	 * @return momusicWork
	 */
	function setID($inID) {
		if ( $inID !== $this->_ID ) {
			$this->_ID = $inID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_SongName
	 *
	 * @return string
 	 */
	function getSongName() {
		return $this->_SongName;
	}

	/**
	 * Set the object property _SongName to $inSongName
	 *
	 * @param string $inSongName
	 * @return momusicWork
	 */
	function setSongName($inSongName) {
		if ( $inSongName !== $this->_SongName ) {
			$this->_SongName = $inSongName;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_FileName
	 *
	 * @return string
 	 */
	function getFileName() {
		return $this->_FileName;
	}

	/**
	 * Set the object property _FileName to $inFileName
	 *
	 * @param string $inFileName
	 * @return momusicWork
	 */
	function setFileName($inFileName) {
		if ( $inFileName !== $this->_FileName ) {
			$this->_FileName = $inFileName;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_ArtistName
	 *
	 * @return string
 	 */
	function getArtistName() {
		return $this->_ArtistName;
	}

	/**
	 * Set the object property _ArtistName to $inArtistName
	 *
	 * @param string $inArtistName
	 * @return momusicWork
	 */
	function setArtistName($inArtistName) {
		if ( $inArtistName !== $this->_ArtistName ) {
			$this->_ArtistName = $inArtistName;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_Context
	 *
	 * @return string
 	 */
	function getContext() {
		return $this->_Context;
	}

	/**
	 * Set the object property _Context to $inContext
	 *
	 * @param string $inContext
	 * @return momusicWork
	 */
	function setContext($inContext) {
		if ( $inContext !== $this->_Context ) {
			$this->_Context = $inContext;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_Genre1
	 *
	 * @return string
 	 */
	function getGenre1() {
		return $this->_Genre1;
	}

	/**
	 * Set the object property _Genre1 to $inGenre1
	 *
	 * @param string $inGenre1
	 * @return momusicWork
	 */
	function setGenre1($inGenre1) {
		if ( $inGenre1 !== $this->_Genre1 ) {
			$this->_Genre1 = $inGenre1;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_Genre2
	 *
	 * @return string
 	 */
	function getGenre2() {
		return $this->_Genre2;
	}

	/**
	 * Set the object property _Genre2 to $inGenre2
	 *
	 * @param string $inGenre2
	 * @return momusicWork
	 */
	function setGenre2($inGenre2) {
		if ( $inGenre2 !== $this->_Genre2 ) {
			$this->_Genre2 = $inGenre2;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_Genre3
	 *
	 * @return string
 	 */
	function getGenre3() {
		return $this->_Genre3;
	}

	/**
	 * Set the object property _Genre3 to $inGenre3
	 *
	 * @param string $inGenre3
	 * @return momusicWork
	 */
	function setGenre3($inGenre3) {
		if ( $inGenre3 !== $this->_Genre3 ) {
			$this->_Genre3 = $inGenre3;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_Mood1
	 *
	 * @return string
 	 */
	function getMood1() {
		return $this->_Mood1;
	}

	/**
	 * Set the object property _Mood1 to $inMood1
	 *
	 * @param string $inMood1
	 * @return momusicWork
	 */
	function setMood1($inMood1) {
		if ( $inMood1 !== $this->_Mood1 ) {
			$this->_Mood1 = $inMood1;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_Mood2
	 *
	 * @return string
 	 */
	function getMood2() {
		return $this->_Mood2;
	}

	/**
	 * Set the object property _Mood2 to $inMood2
	 *
	 * @param string $inMood2
	 * @return momusicWork
	 */
	function setMood2($inMood2) {
		if ( $inMood2 !== $this->_Mood2 ) {
			$this->_Mood2 = $inMood2;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_Mood3
	 *
	 * @return string
 	 */
	function getMood3() {
		return $this->_Mood3;
	}

	/**
	 * Set the object property _Mood3 to $inMood3
	 *
	 * @param string $inMood3
	 * @return momusicWork
	 */
	function setMood3($inMood3) {
		if ( $inMood3 !== $this->_Mood3 ) {
			$this->_Mood3 = $inMood3;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_Style1
	 *
	 * @return string
 	 */
	function getStyle1() {
		return $this->_Style1;
	}

	/**
	 * Set the object property _Style1 to $inStyle1
	 *
	 * @param string $inStyle1
	 * @return momusicWork
	 */
	function setStyle1($inStyle1) {
		if ( $inStyle1 !== $this->_Style1 ) {
			$this->_Style1 = $inStyle1;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_Style2
	 *
	 * @return string
 	 */
	function getStyle2() {
		return $this->_Style2;
	}

	/**
	 * Set the object property _Style2 to $inStyle2
	 *
	 * @param string $inStyle2
	 * @return momusicWork
	 */
	function setStyle2($inStyle2) {
		if ( $inStyle2 !== $this->_Style2 ) {
			$this->_Style2 = $inStyle2;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_Style3
	 *
	 * @return string
 	 */
	function getStyle3() {
		return $this->_Style3;
	}

	/**
	 * Set the object property _Style3 to $inStyle3
	 *
	 * @param string $inStyle3
	 * @return momusicWork
	 */
	function setStyle3($inStyle3) {
		if ( $inStyle3 !== $this->_Style3 ) {
			$this->_Style3 = $inStyle3;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_Keywords
	 *
	 * @return string
 	 */
	function getKeywords() {
		return $this->_Keywords;
	}

	/**
	 * Set the object property _Keywords to $inKeywords
	 *
	 * @param string $inKeywords
	 * @return momusicWork
	 */
	function setKeywords($inKeywords) {
		if ( $inKeywords !== $this->_Keywords ) {
			$this->_Keywords = $inKeywords;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_Instrument1
	 *
	 * @return string
 	 */
	function getInstrument1() {
		return $this->_Instrument1;
	}

	/**
	 * Set the object property _Instrument1 to $inInstrument1
	 *
	 * @param string $inInstrument1
	 * @return momusicWork
	 */
	function setInstrument1($inInstrument1) {
		if ( $inInstrument1 !== $this->_Instrument1 ) {
			$this->_Instrument1 = $inInstrument1;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_Instrument2
	 *
	 * @return string
 	 */
	function getInstrument2() {
		return $this->_Instrument2;
	}

	/**
	 * Set the object property _Instrument2 to $inInstrument2
	 *
	 * @param string $inInstrument2
	 * @return momusicWork
	 */
	function setInstrument2($inInstrument2) {
		if ( $inInstrument2 !== $this->_Instrument2 ) {
			$this->_Instrument2 = $inInstrument2;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_Instrument3
	 *
	 * @return string
 	 */
	function getInstrument3() {
		return $this->_Instrument3;
	}

	/**
	 * Set the object property _Instrument3 to $inInstrument3
	 *
	 * @param string $inInstrument3
	 * @return momusicWork
	 */
	function setInstrument3($inInstrument3) {
		if ( $inInstrument3 !== $this->_Instrument3 ) {
			$this->_Instrument3 = $inInstrument3;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_Instrument4
	 *
	 * @return string
 	 */
	function getInstrument4() {
		return $this->_Instrument4;
	}

	/**
	 * Set the object property _Instrument4 to $inInstrument4
	 *
	 * @param string $inInstrument4
	 * @return momusicWork
	 */
	function setInstrument4($inInstrument4) {
		if ( $inInstrument4 !== $this->_Instrument4 ) {
			$this->_Instrument4 = $inInstrument4;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_Language
	 *
	 * @return string
 	 */
	function getLanguage() {
		return $this->_Language;
	}

	/**
	 * Set the object property _Language to $inLanguage
	 *
	 * @param string $inLanguage
	 * @return momusicWork
	 */
	function setLanguage($inLanguage) {
		if ( $inLanguage !== $this->_Language ) {
			$this->_Language = $inLanguage;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_VocalType
	 *
	 * @return string
 	 */
	function getVocalType() {
		return $this->_VocalType;
	}

	/**
	 * Set the object property _VocalType to $inVocalType
	 *
	 * @param string $inVocalType
	 * @return momusicWork
	 */
	function setVocalType($inVocalType) {
		if ( $inVocalType !== $this->_VocalType ) {
			$this->_VocalType = $inVocalType;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_SoundsLike1
	 *
	 * @return string
 	 */
	function getSoundsLike1() {
		return $this->_SoundsLike1;
	}

	/**
	 * Set the object property _SoundsLike1 to $inSoundsLike1
	 *
	 * @param string $inSoundsLike1
	 * @return momusicWork
	 */
	function setSoundsLike1($inSoundsLike1) {
		if ( $inSoundsLike1 !== $this->_SoundsLike1 ) {
			$this->_SoundsLike1 = $inSoundsLike1;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_SoundsLike2
	 *
	 * @return string
 	 */
	function getSoundsLike2() {
		return $this->_SoundsLike2;
	}

	/**
	 * Set the object property _SoundsLike2 to $inSoundsLike2
	 *
	 * @param string $inSoundsLike2
	 * @return momusicWork
	 */
	function setSoundsLike2($inSoundsLike2) {
		if ( $inSoundsLike2 !== $this->_SoundsLike2 ) {
			$this->_SoundsLike2 = $inSoundsLike2;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_SoundsLike3
	 *
	 * @return string
 	 */
	function getSoundsLike3() {
		return $this->_SoundsLike3;
	}

	/**
	 * Set the object property _SoundsLike3 to $inSoundsLike3
	 *
	 * @param string $inSoundsLike3
	 * @return momusicWork
	 */
	function setSoundsLike3($inSoundsLike3) {
		if ( $inSoundsLike3 !== $this->_SoundsLike3 ) {
			$this->_SoundsLike3 = $inSoundsLike3;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_ResemblesSong1
	 *
	 * @return string
 	 */
	function getResemblesSong1() {
		return $this->_ResemblesSong1;
	}

	/**
	 * Set the object property _ResemblesSong1 to $inResemblesSong1
	 *
	 * @param string $inResemblesSong1
	 * @return momusicWork1
	 */
	function setResemblesSong1($inResemblesSong1) {
		if ( $inResemblesSong1 !== $this->_ResemblesSong1 ) {
			$this->_ResemblesSong1 = $inResemblesSong1;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_ResemblesSong2
	 *
	 * @return string
 	 */
	function getResemblesSong2() {
		return $this->_ResemblesSong2;
	}

	/**
	 * Set the object property _ResemblesSong2 to $inResemblesSong2
	 *
	 * @param string $inResemblesSong2
	 * @return momusicWork1
	 */
	function setResemblesSong2($inResemblesSong2) {
		if ( $inResemblesSong2 !== $this->_ResemblesSong2 ) {
			$this->_ResemblesSong2 = $inResemblesSong2;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_ResemblesSong3
	 *
	 * @return string
 	 */
	function getResemblesSong3() {
		return $this->_ResemblesSong3;
	}

	/**
	 * Set the object property _ResemblesSong3 to $inResemblesSong3
	 *
	 * @param string $inResemblesSong3
	 * @return momusicWork1
	 */
	function setResemblesSong3($inResemblesSong3) {
		if ( $inResemblesSong3 !== $this->_ResemblesSong3 ) {
			$this->_ResemblesSong3 = $inResemblesSong3;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_Writer
	 *
	 * @return string
 	 */
	function getWriter() {
		return $this->_Writer;
	}

	/**
	 * Set the object property _Writer to $inWriter
	 *
	 * @param string $inWriter
	 * @return momusicWork
	 */
	function setWriter($inWriter) {
		if ( $inWriter !== $this->_Writer ) {
			$this->_Writer = $inWriter;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_Composer
	 *
	 * @return string
 	 */
	function getComposer() {
		return $this->_Composer;
	}

	/**
	 * Set the object property _Composer to $inComposer
	 *
	 * @param string $inComposer
	 * @return momusicWork
	 */
	function setComposer($inComposer) {
		if ( $inComposer !== $this->_Composer ) {
			$this->_Composer = $inComposer;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_Duration
	 *
	 * @return integer
 	 */
	function getDuration() {
		return $this->_Duration;
	}

	/**
	 * Set the object property _Duration to $inDuration
	 *
	 * @param integer $inDuration
	 * @return momusicWork
	 */
	function setDuration($inDuration) {
		if ( $inDuration !== $this->_Duration ) {
			$this->_Duration = $inDuration;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_Plays
	 *
	 * @return integer
 	 */
	function getPlays() {
		return $this->_Plays;
	}

	/**
	 * Set the object property _Plays to $inPlays
	 *
	 * @param integer $inPlays
	 * @return momusicWork
	 */
	function setPlays($inPlays) {
		if ( $inPlays !== $this->_Plays ) {
			$this->_Plays = $inPlays;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_Download
	 *
	 * @return integer
 	 */
	function getDownload() {
		return $this->_Download;
	}

	/**
	 * Set the object property _Download to $inDownload
	 *
	 * @param integer $inDownload
	 * @return momusicWork
	 */
	function setDownload($inDownload) {
		if ( $inDownload !== $this->_Download ) {
			$this->_Download = $inDownload;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_Status
	 *
	 * @return integer
 	 */
	function getStatus() {
		return $this->_Status;
	}

	/**
	 * Set the object property _Status to $inStatus
	 *
	 * @param integer $inStatus
	 * @return momusicWork
	 */
	function setStatus($inStatus) {
		if ( $inStatus !== $this->_Status ) {
			$this->_Status = $inStatus;
			$this->setModified();
		}
		return $this;
	}
        
        /**
	 * Return the current value of the property $_VendorID
	 *
	 * @return integer
 	 */
	function getVendorID() {
		return $this->_VendorID;
	}

	/**
	 * Set the object property _VendorID to $inVendorID
	 *
	 * @param integer $inVendorID
	 * @return momusicWork
	 */
	function setVendorID($inVendorID) {
		if ( $inVendorID !== $this->_VendorID ) {
			$this->_VendorID = $inVendorID;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_Path
	 *
	 * @return string
 	 */
	function getPath() {
		return $this->_Path;
	}

	/**
	 * Set the object property _Path to $inPath
	 *
	 * @param string $inPath
	 * @return momusicWork
	 */
	function setPath($inPath) {
		if ( $inPath !== $this->_Path ) {
			$this->_Path = $inPath;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_Album
	 *
	 * @return string
 	 */
	function getAlbum() {
		return $this->_Album;
	}

	/**
	 * Set the object property _Album to $inAlbum
	 *
	 * @param string $inAlbum
	 * @return momusicWork
	 */
	function setAlbum($inAlbum) {
		if ( $inAlbum !== $this->_Album ) {
			$this->_Album = $inAlbum;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_Year
	 *
	 * @return systemDateTime
 	 */
	function getYear() {
		return $this->_Year;
	}

	/**
	 * Set the object property _Year to $inYear
	 *
	 * @param systemDateTime $inYear
	 * @return momusicWork
	 */
	function setYear($inYear) {
		if ( $inYear !== $this->_Year ) {
			if ( !$inYear instanceof DateTime ) {
				$inYear = new systemDateTime($inYear, system::getConfig()->getSystemTimeZone()->getParamValue());
			}
			$this->_Year = $inYear;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_Publisher
	 *
	 * @return string
 	 */
	function getPublisher() {
		return $this->_Publisher;
	}

	/**
	 * Set the object property _Publisher to $inPublisher
	 *
	 * @param string $inPublisher
	 * @return momusicWork
	 */
	function setPublisher($inPublisher) {
		if ( $inPublisher !== $this->_Publisher ) {
			$this->_Publisher = $inPublisher;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_Description
	 *
	 * @return string
 	 */
	function getDescription() {
		return $this->_Description;
	}

	/**
	 * Set the object property _Description to $inDescription
	 *
	 * @param string $inDescription
	 * @return momusicWork
	 */
	function setDescription($inDescription) {
		if ( $inDescription !== $this->_Description ) {
			$this->_Description = $inDescription;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_MusicSource
	 *
	 * @return string
 	 */
	function getMusicSource() {
		return $this->_MusicSource;
	}

	/**
	 * Set the object property _MusicSource to $inMusicSource
	 *
	 * @param string $inMusicSource
	 * @return momusicWork
	 */
	function setMusicSource($inMusicSource) {
		if ( $inMusicSource !== $this->_MusicSource ) {
			$this->_MusicSource = $inMusicSource;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_Priority
	 *
	 * @return integer
 	 */
	function getPriority() {
		return $this->_Priority;
	}

	/**
	 * Set the object property _Priority to $inPriority
	 *
	 * @param integer $inPriority
	 * @return momusicWork
	 */
	function setPriority($inPriority) {
		if ( $inPriority !== $this->_Priority ) {
			$this->_Priority = $inPriority;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_Sku
	 *
	 * @return integer
 	 */
	function getSku() {
		return $this->_Sku;
	}

	/**
	 * Set the object property _Sku to $inSku
	 *
	 * @param integer $inSku
	 * @return momusicWork
	 */
	function setSku($inSku) {
		if ( $inSku !== $this->_Sku ) {
			$this->_Sku = $inSku;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Return the current value of the property $_LastModified
	 *
	 * @return systemDateTime
 	 */
	function getLastModified() {
		return $this->_LastModified;
	}

	/**
	 * Set the object property _LastModified to $inLastModified
	 *
	 * @param systemDateTime $inLastModified
	 * @return momusicWork
	 */
	function setLastModified($inLastModified) {
		if ( $inLastModified !== $this->_LastModified ) {
			if ( !$inLastModified instanceof DateTime ) {
				$inLastModified = new systemDateTime($inLastModified, system::getConfig()->getSystemTimeZone()->getParamValue());
			}
			$this->_LastModified = $inLastModified;
			$this->setModified();
		}
		return $this;
	}

	/**
	 * Returns $_MarkForDeletion
	 *
	 * @return boolean
	 */
	function getMarkForDeletion() {
		return $this->_MarkForDeletion;
	}

	/**
	 * Set $_MarkForDeletion to $inMarkForDeletion
	 *
	 * @param boolean $inMarkForDeletion
	 * @return momusicWork
	 */
	function setMarkForDeletion($inMarkForDeletion) {
		if ( $inMarkForDeletion !== $this->_MarkForDeletion ) {
			$this->_MarkForDeletion = $inMarkForDeletion;
		}
		return $this;
	}
}