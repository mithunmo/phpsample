<?php
/**
 * mofilmMessages
 * 
 * Stored in mofilmMessages.class.php
 * 
 * @author Dave Redfern
 * @copyright Mofilm (c) 2009-2010
 * @package mofilm
 * @subpackage mofilmMessages
 * @category mofilmMessages
 * @version $Rev: 393 $
 */


/**
 * mofilmMessages Class
 * 
 * Interface between Mofilm and commsCentre.
 * 
 * @package mofilm
 * @subpackage mofilmMessages
 * @category mofilmMessages
 */
class mofilmMessages {
	
	const MSG_GRP_CLIENT_WELCOME = 1;
	const MSG_GRP_CLIENT_PASSWORD_RESET = 2;
	const MSG_GRP_CLIENT_REGISTRATION = 3;
	const MSG_GRP_CLIENT_PRIVATE_MESSAGE = 7;
	
	const MSG_GRP_ADMIN_WELCOME = 4;
	const MSG_GRP_ADMIN_PROMOTION = 5;
	const MSG_GRP_ADMIN_REGISTRATION = 6;
	const MSG_GRP_ADMIN_MOVIE_APPROVED = 20;
	
	const MSG_GRP_USR_UPLOADED_VIDEO = 23;
	const MSG_GRP_USR_VIDEO_ENCODED = 24;
	const MSG_GRP_CLIENT_REGISTRATIONCN = 25;
	const MSG_GRP_USR_CREDIT = 26;
	const MSG_GRP_USR_CREDIT_NEWREGISTER = 27;
	const MSG_GRP_USR_GRNT_RECEIVED = 28;
	const MSG_GRP_USR_GRNT_APPROVED = 29;
	const MSG_GRP_USR_GRNT_REJECTED = 30;
	const MSG_GRP_USR_GRNT_PENDING = 31;
	const MSG_GRP_USR_GRNT_RESUBMIT = 32;
	const MSG_GRP_UPLOADED_FILES_APPROVED = 33;
	const MSG_GRP_UPLOADED_FILES_REJECTED = 34;
	const MSG_GRP_CREW = 35;
	const MSG_PARAM_MOVIE_ID = 'mofilm.movie.id';
	const MSG_PARAM_PM_ID = 'mofilm.pm.id';
	const MSG_GRP_REFERRAL = 36;
	const MSG_GRP_REFERRAL_CN = 37;
        const MSG_PAYMENT_REQURIES_APPROVAL = 38;
	const MSG_PAYMENT_APPROVAL_STATUS_CHANGE = 39;
	const MSG_PAYMENT_NOTICE = 40;
        const MSG_GRP_MOPHOTO = 38;
        
}
