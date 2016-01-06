<?php
/**
 * system.inc
 * 
 * Core system initialisation file used for all system components.
 * Provides autoload and registry functionality and sets initial
 * framework values.
 * 
 * @author Dave Redfern
 * @copyright Dave Redfern (c) 2007-2010
 * @package scorpio
 * @subpackage system
 * @category systemInit
 * @version $Rev: 670 $
 */


/*
 * Load required libraries
 */
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'system.class.php');
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'autoload'.DIRECTORY_SEPARATOR.'autoload.class.php');

/*
 * Initialise system
 */
system::init(
	array(
		system::INIT_OPTION_BASE_PATH => dirname(dirname(__FILE__))
	)
);