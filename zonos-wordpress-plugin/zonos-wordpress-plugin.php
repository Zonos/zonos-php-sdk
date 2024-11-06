<?php

/**
 * Zonos Plugin for Woocommerce
 *
 * @package     Zonos Plugin for Woocommerce
 * @author
 * @copyright
 * @license
 *
 * @wordpress-plugin
 * Plugin Name: Zonos Plugin for Woocommerce
 * Plugin URI:
 * Description:
 * Version:     0.0.1
 * Author:
 * Author URI:
 * Text Domain:
 * License:
 * License URI:
 *
 */

// Block direct access to file
defined( 'ABSPATH' ) or die( 'Not Authorized!' );

// Plugin Defines
define( "WPS_FILE", __FILE__ );
define( "WPS_DIRECTORY", dirname(__FILE__) );
define( "WPS_TEXT_DOMAIN", dirname(__FILE__) );
define( "WPS_DIRECTORY_BASENAME", plugin_basename( WPS_FILE ) );
define( "WPS_DIRECTORY_PATH", plugin_dir_path( WPS_FILE ) );
define( "WPS_DIRECTORY_URL", plugins_url( null, WPS_FILE ) );

// Require the main class file
require_once( WPS_DIRECTORY . '/include/main-class.php' );
