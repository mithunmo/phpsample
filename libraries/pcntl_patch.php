<?php
/**
 * Scorpio PCNTL patch file
 * 
 * For windows platforms that do not have pcntl support, this provides
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
 * @category pcntl_patch
 */


/*
 * PCNTL Library Constants, all are integers
 */
if ( !defined('WNOHANG') )   define('WNOHANG',   0);
if ( !defined('WUNTRACED') ) define('WUNTRACED', 1);
if ( !defined('SIG_IGN') )   define('SIG_IGN',   2);
if ( !defined('SIG_DFL') )   define('SIG_DFL',   3);
if ( !defined('SIG_ERR') )   define('SIG_ERR',   4);
if ( !defined('SIGHUP') )    define('SIGHUP',    5);
if ( !defined('SIGINT') )    define('SIGINT',    6);
if ( !defined('SIGQUIT') )   define('SIGQUIT',   7);
if ( !defined('SIGILL') )    define('SIGILL',    8);
if ( !defined('SIGTRAP') )   define('SIGTRAP',   9);
if ( !defined('SIGABRT') )   define('SIGABRT',   10);
if ( !defined('SIGIOT') )    define('SIGIOT',    11);
if ( !defined('SIGBUS') )    define('SIGBUS',    12);
if ( !defined('SIGFPE') )    define('SIGFPE',    13);
if ( !defined('SIGKILL') )   define('SIGKILL',   14);
if ( !defined('SIGUSR1') )   define('SIGUSR1',   15);
if ( !defined('SIGSEGV') )   define('SIGSEGV',   16);
if ( !defined('SIGUSR2') )   define('SIGUSR2',   17);
if ( !defined('SIGPIPE') )   define('SIGPIPE',   18);
if ( !defined('SIGALRM') )   define('SIGALRM',   19);
if ( !defined('SIGTERM') )   define('SIGTERM',   20);
if ( !defined('SIGSTKFLT') ) define('SIGSTKFLT', 21);
if ( !defined('SIGCLD') )    define('SIGCLD',    22);
if ( !defined('SIGCHLD') )   define('SIGCHLD',   23);
if ( !defined('SIGCONT') )   define('SIGCONT',   24);
if ( !defined('SIGSTOP') )   define('SIGSTOP',   25);
if ( !defined('SIGTSTP') )   define('SIGTSTP',   26);
if ( !defined('SIGTTIN') )   define('SIGTTIN',   27);
if ( !defined('SIGTTOU') )   define('SIGTTOU',   28);
if ( !defined('SIGURG') )    define('SIGURG',    29);
if ( !defined('SIGXCPU') )   define('SIGXCPU',   30);
if ( !defined('SIGXFSZ') )   define('SIGXFSZ',   31);
if ( !defined('SIGVTALRM') ) define('SIGVTALRM', 32);
if ( !defined('SIGPROF') )   define('SIGPROF',   33);
if ( !defined('SIGWINCH') )  define('SIGWINCH',  34);
if ( !defined('SIGPOLL') )   define('SIGPOLL',   35);
if ( !defined('SIGIO') )     define('SIGIO',     36);
if ( !defined('SIGPWR') )    define('SIGPWR',    37);
if ( !defined('SIGSYS') )    define('SIGSYS',    38);
if ( !defined('SIGBABY') )   define('SIGBABY',   39);


/*
 * PCNTL Library Functions
 * 
 * All functions return false, error or whatever the normal return state is
 */
if ( !function_exists('pcntl_alarm') ) {
	function pcntl_alarm($seconds) {
		return 0;
	}
}

if ( !function_exists('pcntl_exec') ) {
	function pcntl_exec($path, $args = null, $envs = null) {
		// returns void
	}
}

if ( !function_exists('pcntl_fork') ) {
	function pcntl_fork() {
		return -1;
	}
}

if ( !function_exists('pcntl_getpriority') ) {
	function pcntl_getpriority($pid = null, $process_identifier = null) {
		return false;
	}
}

if ( !function_exists('pcntl_setpriority') ) {
	function pcntl_setpriority($priority, $pid = null, $process_identifier = null) {
		return false;
	}
}

if ( !function_exists('pcntl_signal') ) {
	function pcntl_signal($signo, $handler, $restart_syscalls = null) {
		return false;
	}
}

if ( !function_exists('pcntl_wait') ) {
	function pcntl_wait(&$status, $options = null) {
		return -1;
	}
}

if ( !function_exists('pcntl_waitpid') ) {
	function pcntl_waitpid($pid, &$status, $options = null) {
		return -1;
	}
}

if ( !function_exists('pcntl_wexitstatus') ) {
	function pcntl_wexitstatus($status) {
		return 0;
	}
}

if ( !function_exists('pcntl_wifexited') ) {
	function pcntl_wifexited($status) {
		return false;
	}
}

if ( !function_exists('pcntl_wifsignaled') ) {
	function pcntl_wifsignaled($status) {
		// this should likely return true...
		return false;
	}
}

if ( !function_exists('pcntl_wifstopped') ) {
	function pcntl_wifstopped($status) {
		// this should likely return true...
		return false;
	}
}

if ( !function_exists('pcntl_wstopsig') ) {
	function pcntl_wstopsig($status) {
		return 0;
	}
}

if ( !function_exists('pcntl_wtermsig') ) {
	function pcntl_wtermsig($status) {
		return 0;
	}
}