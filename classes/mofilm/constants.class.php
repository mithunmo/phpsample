<?php
/**
 * mofilmConstants
 * 
 * Stored in mofilmConstants.class.php
 * 
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmConstants
 * @category mofilmConstants
 * @version $Rev: 338 $
 */


/**
 * mofilmConstants Class
 * 
 * A collection of application wide constants and additional configuration data.
 * 
 * @package mofilm
 * @subpackage mofilmConstants
 * @category mofilmConstants
 */
class mofilmConstants {
	
	/**
	 * Mofilm version number
	 * 
	 * @var string
	 */
	const VERSION = '2.1.0';

	/**
	 * Javascript version number to be incremented when changes are made to JS files
	 *
	 * @var string
	 */
	const JS_VERSION = '50304678';

	/**
	 * CSS version number to be incremented when changes are made to css files
	 *
	 * @var string
	 */
	const CSS_VERSION = '6883878497';

	/**
	 * Maximum length of text allowed in the my.mofilm profile
	 *
	 * @var integer
	 */
	const PROFILE_TEXT_LENGTH = 500;
	
	/**
	 * The base folder where NAS content should be stored. On production and staging this folder is automounted.
	 * 
	 * @var string
	 */
	const NAS_FOLDER = '/opt/content';
	
	/**
	 * Mofilm main resources folder name
	 * 
	 * @var string
	 */
	const RESOURCES_FOLDER = 'resources';
	
	/**
	 * The name of the brandlogos folder
	 * 
	 * @var string
	 */
	const FOLDER_BRAND_LOGOS = 'brandlogos';
	
	/**
	 * The name of the admin logos host folder
	 * 
	 * @var string
	 */
	const FOLDER_ADMIN_RESOURCES = 'admin';
	
	/**
	 * The name/path of the admin event logo folder
	 * 
	 * @var string
	 */
	const FOLDER_ADMIN_EVENT_LOGOS = 'events';
	
	/**
	 * The name/path of the admin source logo folder
	 * 
	 * @var string
	 */
	const FOLDER_ADMIN_SOURCE_LOGOS = 'sources';
	
	/**
	 * Front-end client resources folder
	 * 
	 * @var string
	 */
	const FOLDER_CLIENT_RESOURCES = 'client';
	
	/**
	 * The name/path of the client event folder
	 * 
	 * @var string
	 */
	const FOLDER_CLIENT_EVENT = 'events';
	
	/**
	 * The name/path of the client event logo folder
	 * 
	 * @var string
	 */
	const FOLDER_CLIENT_EVENT_LOGOS = 'events/logo';
	
	/**
	 * The name/path of the client event baner folder
	 * 
	 * @var string
	 */
	const FOLDER_CLIENT_EVENT_BANNER = 'events/banner';
	
	/**
	 * The name/path of the client event filler folder
	 * 
	 * @var string
	 */
	const FOLDER_CLIENT_EVENT_FILLER = 'events/filler';
	
	/**
	 * The name/path of the admin source folder
	 * 
	 * @var string
	 */
	const FOLDER_CLIENT_SOURCE = 'sources';
	
	/**
	 * The name/path of the admin source logo folder
	 * 
	 * @var string
	 */
	const FOLDER_CLIENT_SOURCE_LOGOS = 'sources/logo';
	
	/**
	 * The name/path of the client source baner folder
	 * 
	 * @var string
	 */
	const FOLDER_CLIENT_SOURCE_BANNER = 'sources/banner';
	
	/**
	 * The name/path of the client source filler folder
	 * 
	 * @var string
	 */
	const FOLDER_CLIENT_SOURCE_FILLER = 'sources/filler';
	
	/**
	 * The name of the partner logos folder
	 * 
	 * @var string
	 */
	const FOLDER_PARTNER_LOGOS = 'partnerlogos';
	
	/**
	 * The name of the "ticker" folder used by the scrolling flash applet
	 * 
	 * @var string
	 */
	const FOLDER_TICKER = 'ticker';
	
	/**
	 * The name of the main file downloads folder
	 * 
	 * @var string
	 */
	const FOLDER_DOWNLOADS = 'downloads/competitions';
	
	/**
	 * The name of the main filmmaker profile upload folder
	 * 
	 * @var string
	 */
	const FOLDER_FILMMAKER_PROFILES = 'profiles';
	
	/**
	 * The name of the folder where pdf will be generated and will be ziped
	 */
	const FOLDER_GENERATE_PDF_TMP = 'tcpdf';

	/**
	 * The name of the main folder where grand documents are uploaded
	 * 
	 * @var string
	 */
	const FOLDER_GRANTDOCS = 'grantDocs';
	
	/**
	 * The name of the main folder where cca documents are uploaded
	 * 
	 * @var string
	 */
	const FOLDER_CCADOCS = 'ccaDocs';
	
	/**
	 * Cookie name for userID
	 * 
	 * @var string
	 */
	const COOKIE_USER_ID = 'user_ID';
	
	/**
	 * Cookies name for email address
	 * 
	 * @var string
	 */
	const COOKIE_EMAIL_ADDRESS = 'user_emailAddr';
	
	/**
	 * Cookies name for the user hash value
	 * 
	 * @var string
	 */
	const COOKIE_LOGIN_HASH = 'mofilmIdent';

	/**
	 * Has all the newsletter attachements
	 *
	 *@var string
	 */
	const EMAIL_ATTACHMENT = 'email/CCA';
        
	/**
	 * The name of the directory where help images files are stored
	 *
	 * @var string
	 */
	const FOLDER_HELP_IMAGES = 'helpimages';
	
	/**
	 * Stores all the videos parts before merging
	 * 
	 * @var string
	 */
	const FOLDER_VIDEOS = "video";
	
	/**
	 * Stores all the mp3s
	 * 
	 * @var string
	 */
	const FOLDER_MOMUSIC = "momusic";
		
	
	/**
	 * Stores all the uploaded files
	 * 
	 * @var string
	 */
	const FOLDER_UPLOADED_FILES = "uploadedFiles";

        
	/**
	 * Stores all the csv reports
	 * 
	 * @var string
	 */
	const FOLDER_REPORT = "report";
        
        
        /**
	 * Stores all the payment invoices
	 * 
	 * @var string
	 */
	const FOLDER_INVOICE_DOCS = "invoiceDocs";
        
	
	/*
	 * This is a static class
	 */
	private function __construct() {}
	
	
	
	/**
	 * Returns the full path to the NAS storage folder
	 * 
	 * @return string
	 * @static
	 */
	static function getNasFolder() {
		return system::getConfig()->getParam('mofilm', 'nas', self::NAS_FOLDER);
	}
	
	/**
	 * Returns the full path to the NAS resources storage folder
	 * 
	 * @return string
	 * @static
	 */
	static function getNasResourcesFolder() {
		return self::getNasFolder().system::getDirSeparator().self::RESOURCES_FOLDER;
	}
	
	/**
	 * Returns the full path to the resources folder
	 * 
	 * @return string
	 * @static
	 */
	static function getWebResourcesFolder() {
		return system::getConfig()->getPathWebsites().system::getDirSeparator().'base'.system::getDirSeparator().self::RESOURCES_FOLDER;
	}
	
	/**
	 * Returns the full path to the web folder
	 * 
	 * @return string
	 * @static
	 */
	static function getWebFolder() {
		return system::getConfig()->getPathWebsites().system::getDirSeparator().'base'.system::getDirSeparator();
	}
	
	
	
	/**
	 * Returns the full path to the admin resources folder
	 * 
	 * @return string
	 * @static
	 */
	static function getAdminResourcesFolder() {
		return self::getWebResourcesFolder().system::getDirSeparator().self::FOLDER_ADMIN_RESOURCES;
	}
	
	/**
	 * Returns the full path to the admin events folder
	 * 
	 * @return string
	 * @static
	 */
	static function getAdminEventsFolder() {
		return self::getAdminResourcesFolder().system::getDirSeparator().self::FOLDER_ADMIN_EVENT_LOGOS;
	}

	/**
	 * Returns the full path to the admin source folder
	 * 
	 * @return string
	 * @static
	 */
	static function getAdminSourceFolder() {
		return self::getAdminResourcesFolder().system::getDirSeparator().self::FOLDER_ADMIN_SOURCE_LOGOS;
	}
	
	/**
	 * Returns the full path to the client resources folder
	 * 
	 * @return string
	 * @static
	 */
	static function getClientResourcesFolder() {
		return self::getWebResourcesFolder().system::getDirSeparator().self::FOLDER_CLIENT_RESOURCES;
	}

	/**
	 * Returns the full path to the client events logo folder
	 * 
	 * @return string
	 * @static
	 */
	static function getClientEventsFolder() {
		return self::getClientResourcesFolder().system::getDirSeparator().self::FOLDER_CLIENT_EVENT;
	}
	
	/**
	 * Returns the full path to the client events logo folder
	 * 
	 * @return string
	 * @static
	 */
	static function getClientEventsLogoFolder() {
		return self::getClientResourcesFolder().system::getDirSeparator().self::FOLDER_CLIENT_EVENT_LOGOS;
	}
	
	/**
	 * Returns the full path to the client events banner folder
	 * 
	 * @return string
	 * @static
	 */
	static function getClientEventsBannerFolder() {
		return self::getClientResourcesFolder().system::getDirSeparator().self::FOLDER_CLIENT_EVENT_BANNER;
	}
	
	/**
	 * Returns the full path to the client events filler folder
	 * 
	 * @return string
	 * @static
	 */
	static function getClientEventsFillerFolder() {
		return self::getClientResourcesFolder().system::getDirSeparator().self::FOLDER_CLIENT_EVENT_FILLER;
	}

	/**
	 * Returns the full path to the client source logo folder
	 * 
	 * @return string
	 * @static
	 */
	static function getClientSourceLogoFolder() {
		return self::getClientResourcesFolder().system::getDirSeparator().self::FOLDER_CLIENT_SOURCE_LOGOS;
	}
	
	/**
	 * Returns the full path to the client source banner folder
	 * 
	 * @return string
	 * @static
	 */
	static function getClientSourceBannerFolder() {
		return self::getClientResourcesFolder().system::getDirSeparator().self::FOLDER_CLIENT_SOURCE_BANNER;
	}
	
	/**
	 * Returns the full path to the client source filler folder
	 * 
	 * @return string
	 * @static
	 */
	static function getClientSourceFillerFolder() {
		return self::getClientResourcesFolder().system::getDirSeparator().self::FOLDER_CLIENT_SOURCE_FILLER;
	}
	
	/**
	 * Returns the full path to the client source folder
	 * 
	 * @return string
	 * @static
	 */
	static function getClientSourceFolder() {
		return self::getClientResourcesFolder().system::getDirSeparator().self::FOLDER_CLIENT_SOURCE;
	}
	
	/**
	 * Returns the full path to the brand logos folder
	 * 
	 * @return string
	 * @static
	 */
	static function getBrandLogosFolder() {
		return self::getWebResourcesFolder().system::getDirSeparator().self::FOLDER_BRAND_LOGOS;
	}

	/**
	 * Returns the full path to the partner logos folder
	 * 
	 * @return string
	 * @static
	 */
	static function getPartnerLogosFolder() {
		return self::getWebResourcesFolder().system::getDirSeparator().self::FOLDER_PARTNER_LOGOS;
	}
	
	/**
	 * Returns the full path to the ticker folder
	 * 
	 * @return string
	 * @static
	 */
	static function getTickerFolder() {
		return self::getWebResourcesFolder().system::getDirSeparator().self::FOLDER_TICKER;
	}
	
	/**
	 * Returns the full path to the filmmaker profile image folder
	 * 
	 * @return string
	 * @static
	 */
	static function getFilmmakerImageFolder() {
		return self::getNasResourcesFolder().system::getDirSeparator().self::FOLDER_FILMMAKER_PROFILES;
	}

	/**
	 * Returns the full path to the downloads folder
	 * 
	 * @return string
	 * @static
	 */
	static function getDownloadsFolder() {
		return self::getWebResourcesFolder().system::getDirSeparator().self::FOLDER_DOWNLOADS;
	}
	
	/**
	 * Returns the full path to the tcpdf folder in temp directory
	 * 
	 * @return string
	 * @static
	 */
	static function getTcpdfFolder() {
		$tmp_path = system::getConfig()->getPathTemp()->getParamValue().system::getDirSeparator().self::FOLDER_GENERATE_PDF_TMP;
		if ( !(file_exists($tmp_path)) ) {
		    mkdir($tmp_path, 0777);
		}
		return $tmp_path.system::getDirSeparator();
	}
	
	/**
	 * Returns the full path to the help images folder
	 * 
	 * @return string
	 * @static
	 */
	static function getHelpImageFolder() {
		return self::getNasResourcesFolder().system::getDirSeparator().self::FOLDER_HELP_IMAGES;
	}
	
	/**
	 * Returns the registration URI link
	 * 
	 * @return string
	 * @static
	 */
	static function getRegistrationUri() {
		return system::getConfig()->getParam('mofilm', 'myMofilmUri').'/account/register/';
	}

	/**
	 * Returns the registration URI link
	 * 
	 * @return string
	 * @static
	 */
	static function getCNRegistrationUri() {
		return system::getConfig()->getParam('mofilm', 'myMofilmUri').'/account/registercn/';
	}
	
	
	/**
	 * Returns the activation URI link
	 * 
	 * @return string
	 * @static
	 */
	static function getActivationUri() {
		return system::getConfig()->getParam('mofilm', 'myMofilmUri').'/account/register/';
	}

	/**
	 * Returns the relative path to the email attachment folder
	 *
	 * @return string
	 * @static
	 */
	static function getEmailAttachment() {
		return self::getWebResourcesFolder().system::getDirSeparator().self::EMAIL_ATTACHMENT;
	}

	/**
	 * Returns the relative path to the resource folder
	 *
	 * @return string
	 * @static
	 */
	static function getResourcesFolder() {
		return self::RESOURCES_FOLDER;
	}

	/**
	 * Returns the full path to the events assets folder
	 *
	 * @return string
	 * @static
	 */
	static function getEventAssetsFolder() {
		return self::getNasResourcesFolder().'/events';
	}

	/**
	 * Returns the relative path to the email attachment folder
	 *
	 * @return string
	 * @static
	 */
	static function getVideoFolder() {
		return self::getWebResourcesFolder().system::getDirSeparator().self::FOLDER_VIDEOS;
	}
	
	/**
	 * Returns the relative path to the uploadedFiles folder
	 *
	 * @return string
	 * @static
	 */
	static function getUploadedFilesFolder() {
		return self::getWebResourcesFolder().system::getDirSeparator().self::FOLDER_UPLOADED_FILES;
	}
	
	/**
	 * Returns the relative path to the uploadedFiles folder
	 *
	 * @return string
	 * @static
	 */
	static function getUploadedMusic() {
		return self::getWebResourcesFolder().system::getDirSeparator().self::FOLDER_MOMUSIC;
	}
	
	/*
	 * Returns the relative path to the grantDocs folder
	 * 
	 * @return string
	 * @static
	 */
	static function getGrantDocsFolder() {
		return self::getWebResourcesFolder().system::getDirSeparator().self::FOLDER_GRANTDOCS.system::getDirSeparator();
	}
	
	/*
	 * Returns the relative path to the ccaDocs folder
	 * 
	 * @return string
	 * @static
	 */
	static function getCcaDocsFolder() {
		return self::getWebResourcesFolder().system::getDirSeparator().self::FOLDER_CCADOCS.system::getDirSeparator();
	}
        
	/**
	 * Returns the relative path to the report folder
	 *
	 * @return string
	 * @static
	 */
	static function getReportFolder() {
		return self::getWebResourcesFolder().system::getDirSeparator().self::FOLDER_REPORT.system::getDirSeparator();
	}
        
        /**
	 * Returns the relative path to the payment invoices folder
	 *
	 * @return string
	 * @static
	 */
	static function getInvoiceDocsFolder() {
		return self::getWebResourcesFolder().system::getDirSeparator().self::FOLDER_INVOICE_DOCS.system::getDirSeparator();
	}
        
}
