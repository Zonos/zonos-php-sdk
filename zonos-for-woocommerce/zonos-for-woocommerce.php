<?php

/**
 * Zonos for Woocommerce
 *
 *  TODO: CON-35 Create a Description
 *
 * @package           Zonos\WoocommercePlugin
 * @author            Zonos
 * @copyright         2024 Zonos
 * @license           GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       Zonos for Woocommerce
 * Plugin URI:        https://example.com/plugin-name
 * Description:       Description of the plugin.
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Zonos
 * Author URI:        https://zonos.com
 * Text Domain:       zonos-woocommerce
 * License:           GPL v2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Update URI:        https://example.com/my-plugin/
 * Requires Plugins:  woocommerce
 */

// Block direct access to file
if (!defined('ABSPATH')) {
  exit;
}

// Plugin Defines
define('ZFW_FILE', __FILE__);
define('ZFW_DIRECTORY', dirname(ZFW_FILE));
define('ZFW_PATH', plugin_dir_path(ZFW_FILE));
define('ZFW_DIRECTORY_PATH', plugin_dir_path(ZFW_FILE));
define('WPS_DIRECTORY_BASENAME', plugin_basename(ZFW_FILE));

function activate_zonos_for_woocommerce()
{
  require_once ZFW_PATH . 'includes/zfw-activator.php';
  ZFW_Activator::activate();
}

function deactivate_zonos_for_woocommerce()
{
  require_once ZFW_PATH . 'includes/zfw-deactivator.php';
  ZFW_Deactivator::deactivate();
}

register_activation_hook(ZFW_FILE, 'activate_zonos_for_woocommerce');
register_deactivation_hook(ZFW_FILE, 'deactivate_zonos_for_woocommerce');

require_once ZFW_PATH . 'includes/zfw-main.php';
new ZFW_Main;