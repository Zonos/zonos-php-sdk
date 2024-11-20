<?php

/**
 * Loads plugin internationalization
 *
 * @Class ZFW_i18n
 */
class ZFW_i18n
{
  /**
   * Load the plugin text domain
   * @method load_plugin_textdomain
   */
  public function load_plugin_textdomain()
  {
    load_plugin_textdomain(
      'zonos_for_woocommerce',
      false,
      ZFW_DIRECTORY_BASENAME . '/languages/'
    );
  }
}