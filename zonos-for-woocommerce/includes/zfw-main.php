<?php

/**
 * Performs the plugin deactivation
 *
 * @Class ZFW_Main
 */
class ZFW_Main
{
  public $admin;

  public $public;

  public function __construct()
  {
    $this->load_dependencies();
    $this->set_locale();
    $this->define_admin_hooks();
    $this->define_public_hooks();
  }

  private function load_dependencies()
  {
    require_once ZFW_DIRECTORY_PATH . 'includes/zfw-i18n.php';

    require_once ZFW_DIRECTORY_PATH . 'admin/zfw-admin.php';

    require_once ZFW_DIRECTORY_PATH . 'public/zfw-public.php';
  }

  private function set_locale()
  {
    $plugin_i18n = new ZFW_i18n();
    add_action('plugins_loaded', array($plugin_i18n, 'load_plugin_textdomain'));
  }

  private function define_admin_hooks()
  {
    $this->admin = new ZFW_Admin();
    add_action('admin_menu', array($this->admin, 'create_admin_menu'));
    add_action('admin_init', array($this->admin, 'plugin_register_settings'));
  }

  private function define_public_hooks()
  {
    $this->public = new ZFW_Public();
    add_action('wp_enqueue_scripts', array($this->public, 'load_zonos_elements_script'));
  }
}
