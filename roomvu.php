<?php
/**
 * Plugin Name: Roomvu marketing
 * Description: Roomvu Marketing Plugin allows you to integrate your wordpress with roomvu, and automatically post your roomvu videos to your wordpress website.
 * Author URI: https://roomvu.com
 * Version: 1.3.0
 * Plugin URI: https://wordpress.org/plugins/roomvu-marketing/
 */

/**
 * load utility class
 */
require(dirname(__FILE__) . '/lib/class-roomvu-marketing-roomvu.php');
require(dirname(__FILE__) . '/lib/class-roomvu-marketing-post-manager.php');
require(dirname(__FILE__) . '/lib/class-roomvu-marketing-layout.php');
require(dirname(__FILE__) . '/lib/class-roomvu-marketing-rest-api.php');
require_once(dirname(__FILE__) . '/lib/class-roomvu-marketing-cronjob.php');

add_action('init', 'initializeRoomvuMarketingPlugin');

function initializeRoomvuMarketingPlugin()
{
    new Roomvu_Marketing_Roomvu(__FILE__);
}
