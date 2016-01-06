<?php
/**
 * Scorpio POSIX patch file
 * 
 * For windows platforms that do not have posix support, this provides
 * the functions and basic return states so that the CLI apps can still
 * run - in a "kind of", "sort of" way.
 * 
 * Note: processes will NOT run properly. This file is only to prevent 
 * "undefined function" errors on Windows platforms.
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @version $Rev: 650 $
 * @package scorpio
 * @subpackage system
 * @category posix_patch
 */


/*
 * POSIX Library Constants, all are integers
 */
if ( !defined('POSIX_F_OK') )     define('POSIX_F_OK',      0);
if ( !defined('POSIX_R_OK') )     define('POSIX_R_OK ',     1);
if ( !defined('POSIX_W_OK') )     define('POSIX_W_OK ',     2);
if ( !defined('POSIX_X_OK') )     define('POSIX_X_OK ',     3);
if ( !defined('POSIX_S_IFBLK') )  define('POSIX_S_IFBLK ',  4);
if ( !defined('POSIX_S_IFCHR') )  define('POSIX_S_IFCHR ',  5);
if ( !defined('POSIX_S_IFIFO') )  define('POSIX_S_IFIFO ',  6);
if ( !defined('POSIX_S_IFREG') )  define('POSIX_S_IFREG ',  7);
if ( !defined('POSIX_S_IFSOCK') ) define('POSIX_S_IFSOCK ', 8);


/*
 * POSIX Library Functions
 * 
 * All functions return false, error or whatever the normal return state is
 */
if ( !function_exists('posix_access') ) {
	function posix_access($file, $mode = null) {
		return false;
	}
}

if ( !function_exists('posix_ctermid') ) {
	function posix_ctermid() {
		return false;
	}
}

if ( !function_exists('posix_get_last_error') ) {
	function posix_get_last_error() {
		return 0;
	}
}

if ( !function_exists('posix_getcwd') ) {
	function posix_getcwd() {
		return false;
	}
}

if ( !function_exists('posix_getegid') ) {
	function posix_getegid() {
		return 0;
	}
}
	
if ( !function_exists('posix_geteuid') ) {
	function posix_geteuid() {
		return 0;
	}
}
	
if ( !function_exists('posix_getgid') ) {
	function posix_getgid() {
		return 0;
	}
}
	
if ( !function_exists('posix_getgrgid') ) {
	function posix_getgrgid($gid) {
		return Array(
			'name'    => 'PHP_Process',
			'passwd'  => '*',
			'members' => Array(
				0 => 'PHP_Process'
			),
			'gid' => 0
		);
	}
}
	
if ( !function_exists('posix_getgrnam') ) {
	function posix_getgrnam($name) {
		return Array(
			'name'    => 'PHP_Process',
			'passwd'  => '*',
			'members' => Array(
				0 => 'PHP_Process'
			),
			'gid' => 0
		);
	}
}
	
if ( !function_exists('posix_getgroups') ) {
	function posix_getgroups() {
		return array(0 => 0);
	}
}
	
if ( !function_exists('posix_getlogin') ) {
	function posix_getlogin() {
		return 'PHPProcess';
	}
}
	
if ( !function_exists('posix_getpgid') ) {
	function posix_getpgid($pid) {
		return 0;
	}
}
	
if ( !function_exists('posix_getpgrp') ) {
	function posix_getpgrp() {
		return 0;
	}
}
	
if ( !function_exists('posix_getpid') ) {
	function posix_getpid() {
		return 0;
	}
}
	
if ( !function_exists('posix_getppid') ) {
	function posix_getppid() {
		return 0;
	}
}
	
if ( !function_exists('posix_getpwnam') ) {
	function posix_getpwnam($username) {
		return Array(
			'name'    => 'PHP_Process',
			'passwd'  => '*',
			'uid'     => 0,
			'gid'     => 0,
			'geocs'   => "PHP_Process,,,",
			'dir'     => system::getConfig()->getBasePath(),
			'shell'   => "C:\\command.com"
		);
	}
}
	
if ( !function_exists('posix_getpwuid') ) {
	function posix_getpwuid($uid) {
		return Array(
			'name'    => 'PHP_Process',
			'passwd'  => '*',
			'uid'     => 0,
			'gid'     => 0,
			'geocs'   => "PHP_Process,,,",
			'dir'     => system::getConfig()->getBasePath(),
			'shell'   => "C:\\command.com"
		);
	}
}
	
if ( !function_exists('posix_getrlimit') ) {
	function posix_getrlimit() {
		return Array(
			'soft core' => 0,
			'hard core' => 'unlimited',
			'soft data' => 'unlimited',
			'hard data' => 'unlimited',
			'soft stack' => 8388608,
			'hard stack' => 'unlimited',
			'soft totalmem' => 'unlimited',
			'hard totalmem' => 'unlimited',
			'soft rss' => 'unlimited',
			'hard rss' => 'unlimited',
			'soft maxproc' => 'unlimited',
			'hard maxproc' => 'unlimited',
			'soft memlock' => 'unlimited',
			'hard memlock' => 'unlimited',
			'soft cpu' => 'unlimited',
			'hard cpu' => 'unlimited',
			'soft filesize' => 'unlimited',
			'hard filesize' => 'unlimited',
			'soft openfiles' => 1024,
			'hard openfiles' => 1024
		);
	}
}
	
if ( !function_exists('posix_getsid') ) {
	function posix_getsid($pid) {
		return false;
	}
}
	
if ( !function_exists('posix_getuid') ) {
	function posix_getuid() {
		return 0;
	}
}
	
if ( !function_exists('posix_initgroups') ) {
	function posix_initgroups($name, $base_group_id) {
		return false;
	}
}
	
if ( !function_exists('posix_isatty') ) {
	function posix_isatty($fd) {
		return false;
	}
}
	
if ( !function_exists('posix_kill') ) {
	function posix_kill($pid, $sig) {
		return false;
	}
}
	
if ( !function_exists('posix_mkfifo') ) {
	function posix_mkfifo($pathname, $mode) {
		return false;
	}
}
	
if ( !function_exists('posix_mknod') ) {
	function posix_mknod($pathname, $mode, $major = null, $minor = null) {
		return false;
	}
}
	
if ( !function_exists('posix_setegid') ) {
	function posix_setegid($gid) {
		return false;
	}
}
	
if ( !function_exists('posix_seteuid') ) {
	function posix_seteuid($uid) {
		return false;
	}
}
	
if ( !function_exists('posix_setgid') ) {
	function posix_setgid($gid) {
		return false;
	}
}
	
if ( !function_exists('posix_setpgid') ) {
	function posix_setpgid($pid, $pgid) {
		return false;
	}
}
	
if ( !function_exists('posix_setsid') ) {
	function posix_setsid() {
		return -1;
	}
}
	
if ( !function_exists('posix_setuid') ) {
	function posix_setuid($uid) {
		return false;
	}
}
	
if ( !function_exists('posix_strerror') ) {
	function posix_strerror($errno) {
		return 'POSIX is not supported on Win32/64 platform';
	}
}

if ( !function_exists('posix_times') ) {
	function posix_times() {
		return Array(
			'ticks' => 0,
			'utime' => 1,
			'stime' => 1,
			'cutime' => 0,
			'cstime' => 0
		);
	}
}
	
if ( !function_exists('posix_ttyname') ) {
	function posix_ttyname($fd) {
		return false;
	}
}
	
if ( !function_exists('posix_uname') ) {
	function posix_uname() {
		return Array(
			'sysname' => PHP_OS,
			'nodename' => '',
			'release' => PHP_VERSION,
			'version' => PHP_VERSION,
			'machine' => 'i686'
		);
	}
}