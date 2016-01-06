#!/usr/bin/env php
<?php
/**
 * scoreUpdate.php
 *
 * Used to update the mofilm_content.userPoints table via cron task.
 *
 * @author Dave Redfern
 * @copyright Mofilm Ltd (c) 2010
 * @package mofilm
 * @subpackage tools
 * @category scoreUpdate
 * @version $Rev: 9 $
 */


/*
 * Load dependencies
 */
require_once(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'libraries'.DIRECTORY_SEPARATOR.'system.inc');


mofilmLeaderboardUtilities::resetPoints();
mofilmLeaderboardUtilities::buildStats();
mofilmLeaderboardUtilities::buildHighScoreStats();