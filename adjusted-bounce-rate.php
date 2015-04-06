<?php
/**
 * Plugin Name: Adjusted Bounce Rate
 *
 * Description: A well-designed plugin that improves the accuracy of your bounce rate, time on page, and session duration metrics in Google Analytics.
 *
 * Plugin URI: http://wordpress.org/extend/plugins/adjusted-bounce-rate/
 * Version: 2.0.0-develop
 * Author: Grant K Norwood
 * Author URI: http://grantnorwood.com/
 * License: GPLv2
 * @package adjusted-bounce-rate
 */

/**
 * Configuration (safe to edit.)
 */

//Example.
//define('GKN_ABR_TEST_MODE', false);




/**
 * Constants. (Do NOT edit below, configuration is done in the constants above.)
 */

/**
 * Example.
 */
//define('GKN_ABR_TWILIO_MAGIC_NUMBER_NO_ERROR', '+15005550006');




/**
 * Requires.
 */

require_once('class/OptionsModel.php');
require_once('class/AjaxActions.php');
require_once('class/AjaxResponse.php');
require_once('class/UserMessage.php');
require_once('class/UserMessageTypes.php');
require_once('pages/OptionsPage.php');
require_once('class/AdjustedBounceRate.php');

global $adjustedBounceRate;
$adjustedBounceRate = new gkn_abr\AdjustedBounceRate();

register_activation_hook(__FILE__, array(&$adjustedBounceRate, 'activate'));
register_deactivation_hook(__FILE__, array(&$adjustedBounceRate, 'deactivate'));

?>