<?php
defined('ABSPATH') or die('Not Authorized!');

use Zonos\ZonosSdk\Connectors\Enums\ZonosConnectorType;
use Zonos\ZonosSdk\Connectors\ZonosConnectorFactory;
use Zonos\ZonosSdk\Requests\Inputs\CheckoutSettingUpdateInput;

class Zonos_Wordpress_Plugin
{

    public function __construct()
    {

        // Plugin uninstall hook
        register_uninstall_hook(WPS_FILE, array('Zonos_Wordpress_Plugin', 'plugin_uninstall'));

        // Plugin activation/deactivation hooks
        register_activation_hook(WPS_FILE, array($this, 'plugin_activate'));
        register_deactivation_hook(WPS_FILE, array($this, 'plugin_deactivate'));

        // Plugin Actions
        add_action('plugins_loaded', array($this, 'plugin_init'));
        add_action('wp_enqueue_scripts', array($this, 'plugin_enqueue_scripts'));
        add_action('admin_enqueue_scripts', array($this, 'plugin_enqueue_admin_scripts'));
        add_action('admin_menu', array($this, 'plugin_admin_menu_function'));

    }

    /**
     * Plugin uninstall function
     * called when the plugin is uninstalled
     * @method plugin_activate
     */
    public static function plugin_uninstall()
    {
    }

    /**
     * Plugin activation function
     * called when the plugin is activated
     * @method plugin_activate
     */
    public function plugin_activate()
    {
    }

    /**
     * Plugin deactivate function
     * is called during plugin deactivation
     * @method plugin_deactivate
     */
    public function plugin_deactivate()
    {
    }

    /**
     * Plugin init function
     * init the polugin textDomain
     * @method plugin_init
     */
    function plugin_init()
    {
        load_plugin_textDomain(WPS_TEXT_DOMAIN, false, dirname(WPS_DIRECTORY_BASENAME) . '/languages');
    }

    function plugin_admin_menu_function()
    {
        add_menu_page(__('Zonos Woocommerce Plugin', WPS_TEXT_DOMAIN), __('Zonos', WPS_TEXT_DOMAIN), 'administrator', 'wps-general', null, 'dashicons-admin-generic', 4);

        // create top level submenu page which point to main menu page
        add_submenu_page('wps-general', __('General', WPS_TEXT_DOMAIN), __('General', WPS_TEXT_DOMAIN), 'manage_options', 'wps-general', array($this, 'plugin_settings_page'));

        // add the support page
        add_submenu_page('wps-general', __('Plugin Support Page', WPS_TEXT_DOMAIN), __('Support', WPS_TEXT_DOMAIN), 'manage_options', 'wps-support', array($this, 'plugin_support_page'));

        //call register settings function
        add_action('admin_init', array($this, 'plugin_register_settings'));

    }

    /**
     * Register the main Plugin Settings
     * @method plugin_register_settings
     */
    function plugin_register_settings()
    {
//        register_setting('wps-settings-group', 'example_option');
//        register_setting('wps-settings-group', 'another_example_option');
    }

    /**
     * Enqueue the main Plugin admin scripts and styles
     * @method plugin_enqueue_scripts
     */
    function plugin_enqueue_admin_scripts()
    {
//        wp_register_style('wps-zonos-style', WPS_DIRECTORY_URL . '/assets/dist/css/zonos.css', array(), null); // Let's add scss processing for this
//        wp_enqueue_script('jquery');
//        wp_enqueue_style('wps-admin-style');
//        wp_enqueue_script('wps-admin-script');
    }

    /**
     * Enqueue the main Plugin user scripts and styles
     * @method plugin_enqueue_scripts
     */
    function plugin_enqueue_scripts()
    {
//        wp_register_style('wps-zonos-style', WPS_DIRECTORY_URL . '/assets/dist/css/zonos.css', array(), null); // Let's add scss processing for this
//        wp_enqueue_script('jquery');
//        wp_enqueue_style('wps-user-style');
//        wp_enqueue_script('wps-user-script');
    }

    /**
     * Plugin main settings page
     * @method plugin_settings_page
     */
    function plugin_settings_page()
    {
        $sdk = ZonosConnectorFactory::createConnector( ZonosConnectorType::Wordpress, '','');
        $thing = CheckoutSettingUpdateInput::fromArray(['successRedirectUrl' => 'https://www.zonos.com/']);
        $res = $sdk->checkoutSettingsUpdate($thing)->get();
        $allowedDomains = implode(", ", $res->allowedDomains);

      ?>

      <div class="">

        <h1><?php _e('Zonos', WPS_TEXT_DOMAIN); ?></h1>
        <div>
          <h3>allowedCharacterSets</h3>
          <p><?php $res->allowedCharacterSets?></p>
        </div>
        <div>
          <h3>allowedDomains</h3>
          <p><?php $allowedDomains ?></p>
        </div>
        <div>
          <h3></h3>
          <p></p>
        </div>
        <div>
          <h3></h3>
          <p></p>
        </div>
        <div>
          <h3></h3>
          <p></p>
        </div>
        <div>
          <h3></h3>
          <p></p>
        </div>

      </div>

    <?php }

    /**
     * Plugin support page
     * in this page there are listed some useful debug informations
     * and a quick link to write a mail to the plugin author
     * @method plugin_support_page
     */
    function plugin_support_page()
    {

        global $wpdb, $wp_version;
        $plugin = get_plugin_data(WPS_FILE, true, true);
        $wptheme = wp_get_theme();
        $current_user = wp_get_current_user();

        $user_fullname = ($current_user->user_firstname || $current_user->user_lastname) ?
            ($current_user->user_lastname . ' ' . $current_user->user_firstname) : $current_user->display_name; ?>

      <div class="">

        <!-- support page title -->
        <h1><?php _e('Zonos', WPS_TEXT_DOMAIN); ?></h1>


      </div>

    <?php }

}

new Zonos_Wordpress_Plugin;
