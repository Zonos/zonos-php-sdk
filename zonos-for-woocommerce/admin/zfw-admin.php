<?php

/**
 * Contains the Admin functionality
 *
 * @Class ZFW_Admin
 */
class ZFW_Admin
{

  /**
   * Loads Zonos Admin Styles
   *
   * @method load_zonos_elements_script
   */
  function load_admin_styles()
  {
    wp_enqueue_style(
      'zfw_admin_styles',
      ZFW_DIRECTORY_URL . 'admin/css/zfw_admin_styles.css',
      null,
      ZFW_VERSION,
    );
  }

  /**
   * Create the admin Menu
   * @method create_admin_menu
   */
  function create_admin_menu()
  {
    add_menu_page(
      __('Zonos For Woocommerce', 'zonos_for_woocommerce'),
      __('Zonos', 'zonos_for_woocommerce'),
      'manage_options',
      'zonos_menu',
      array($this, 'generate_zonos_main_settings_page'),
      'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiPz48c3ZnIGlkPSJMYXllcl8xIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAyMCAyMCI+PGRlZnM+PHN0eWxlPi5jbHMtMXtmaWxsOiNmZmY7fTwvc3R5bGU+PC9kZWZzPjxwYXRoIGNsYXNzPSJjbHMtMSIgZD0iTTE2LjU3LDMuODVjLTEuMDUtMS4wMy0yLjM1LTEuOC0zLjc2LTIuMjEtLjkzLS4yNy0xLjg4LS40MS0yLjgxLS40MS0yLjQ4LDAtNC44MiwuOTctNi41NywyLjcyQzEuNjcsNS43LC43MSw4LjAzLC43MSwxMC41MmMwLC45NiwuMTUsMS45MiwuNDUsMi44NiwwLC4wMywuMDQsLjEyLC4wNiwuMTksLjAzLC4wOSwuMDcsLjIsLjA3LC4yMSwuMDMsLjA3LC4xLC4xMiwuMTcsLjEyLC4wMiwwLC4wNCwwLC4wNi0uMDFsMS4yMy0uNDJzMCwwLC4wMSwwYy41NywxLjM4LDEuNTIsMi41NywyLjc1LDMuNDQsMS4zMiwuOTIsMi44NiwxLjQxLDQuNDcsMS40MSwxLjE5LDAsMi4zNC0uMjYsMy40MS0uNzksMCwwLDAsMCwuMDEsMGwuNjMsMS4xNGMuMDMsLjA2LC4wOSwuMSwuMTYsLjEsLjAzLDAsLjA2LDAsLjA5LS4wMmwuMzctLjIxYzEuNzctMS4wMywzLjE3LTIuNjUsMy45My00LjU0LC44Mi0yLjAyLC45LTQuMjUsLjIyLTYuNDMtLjQzLTEuNC0xLjIyLTIuNjktMi4yNy0zLjcyWm0tNi4zLDQuMDdjMS4yMywuMTIsMi4yLDEuMSwyLjMzLDIuMzMsLjA4LC43NS0uMTYsMS40Ny0uNjYsMi4wMi0uNSwuNTUtMS4yLC44Ni0xLjk0LC44Ni0uMDksMC0uMTgsMC0uMjctLjAxLTEuMjMtLjEyLTIuMi0xLjEtMi4zMy0yLjMzLS4wOC0uNzUsLjE2LTEuNDcsLjY2LTIuMDIsLjUtLjU1LDEuMi0uODYsMS45NC0uODYsLjA5LDAsLjE4LDAsLjI3LC4wMVptMy44NSw5LjIyYzEuMDgtLjY3LDEuOTgtMS42MSwyLjYyLTIuNywuNjktMS4xOSwxLjA2LTIuNTQsMS4wNi0zLjkyLDAtMS4wOS0uMjItMi4xNS0uNjYtMy4xNC0uNDItLjk2LTEuMDMtMS44MS0xLjc5LTIuNTNsLS4zMS0uMjlzLS4wOC0uMDUtLjEzLS4wNS0uMSwuMDItLjEzLC4wNWMwLDAtLjI5LC4zMS0uODUsLjkzaC0uMDJjLTEuMjItLjk1LTIuNzctMS40My00LjMtMS4zNC0zLjE2LC4xOS01LjczLDIuNzEtNS45OCw1Ljg3LS4xLDEuMjQsLjE2LDIuNDYsLjc1LDMuNTQsLjU3LDEuMDQsMS40MSwxLjksMi40NCwyLjVsLjIsLjExcy4wOSwuMDUsLjEzLC4wN2wuMDQsLjAyYy4wOSwuMDUsLjIsMCwuMjUtLjA4bC42Mi0xLjE3czAsMCwuMDIsMGMuNjcsLjI5LDEuMzksLjQyLDIuMTEsLjM5LDEuMjUtLjA1LDIuNDItLjU3LDMuMy0xLjQ4LC44OC0uOSwxLjM3LTIuMDgsMS4zOS0zLjMzLC4wMi0xLjI4LS40Ni0yLjQ5LTEuMzQtMy40Mi0uODgtLjkyLTIuMDYtMS40Ni0zLjMzLTEuNTItLjAzLDAtLjExLDAtLjE5LDBoLS4wOGMtLjA3LDAtLjEzLDAtLjE2LDAtLjA1LDAtLjExLC4wMy0uMTQsLjA3LS4wMywuMDMtLjA0LC4wOC0uMDQsLjEydjEuM3MwLC4wMS0uMDEsLjAxYy0uODUsLjEtMS42NSwuNTQtMi4yLDEuMTktLjYsLjcxLS44OCwxLjYyLS43OCwyLjU1LC4xNywxLjYxLDEuNDYsMi44OCwzLjA3LDMuMDMsLjEsMCwuMjEsLjAxLC4zMSwuMDEsMS44OCwwLDMuNC0xLjUzLDMuNC0zLjQsMC0xLjY5LTEuMzItMy4xNy0zLTMuMzgsMCwwLS4wMSwwLS4wMS0uMDF2LS42NmguMDFjMS4wNCwuMDksMi4wMywuNjEsMi43MSwxLjQxLC43NCwuODcsMS4wNywxLjk4LC45NCwzLjEyLS4yMSwxLjgyLTEuNjMsMy4yOC0zLjQ1LDMuNTUtLjIsLjAzLS40MSwuMDUtLjYxLC4wNS0uNjEsMC0xLjE5LS4xMy0xLjc0LS4zOWwtLjM2LS4xOWMtLjA4LS4wNC0uMTktLjAyLS4yNSwuMDhsLS42LDEuMTVoLS4wMmMtLjg0LS41My0xLjUzLTEuMjctMS45OS0yLjE1LS41MS0uOTgtLjcyLTIuMDktLjU5LTMuMjEsLjE0LTEuMjcsLjczLTIuNDcsMS42Ny0zLjM4LC45My0uOSwyLjE1LTEuNDYsMy40My0xLjU2LC4xNS0uMDEsLjMxLS4wMiwuNDUtLjAyLDEuMjksMCwyLjU0LC40NSwzLjU0LDEuMjdsLjMxLC4yOXMuMDIsLjAxLC4wMiwuMDJjLjA3LC4wNSwuMTcsLjA0LC4yMy0uMDIsMCwwLC4wNS0uMDUsLjM4LS40MWwuNDYtLjVoLjAyYzEuMzcsMS4zNywyLjEyLDMuMjcsMi4wNSw1LjItLjA2LDEuNzYtLjc5LDMuNDMtMi4wNCw0LjY5LTEuMjUsMS4yNi0yLjkxLDEuOTktNC42NywyLjA3LS4xLDAtLjIsMC0uMywwLTEuNDcsMC0yLjg3LS40NS00LjA3LTEuMy0xLjE3LS44My0yLjA0LTEuOTktMi41My0zLjMzbC0uMDktLjI2Yy0uMDItLjA4LS4wOS0uMTMtLjE4LS4xMy0uMDIsMC0uMDQsMC0uMDYsMGwtMS4yMSwuNDJoLS4wMWMtLjMxLTEuMDYtLjQyLTIuMTUtLjMxLTMuMjQsLjQyLTQuMjQsMy45NC03LjU0LDguMi03LjY3LC4wOSwwLC4xOCwwLC4yNiwwLDQuNjksMCw4LjUsMy44MSw4LjUsOC41LDAsMS41MS0uNCwyLjk5LTEuMTYsNC4yOC0uNywxLjItMS43LDIuMjEtMi44OCwyLjk1aC0uMDFzLS4zMi0uNTktLjMyLS41OWMwLDAsMC0uMDEsMC0uMDJaIi8+PC9zdmc+',
      100
    );

    add_submenu_page(
      'zonos_menu',
      __('Zonos Base Configurations', 'zonos_for_woocommerce'),
      __('Zonos Settings', 'zonos_for_woocommerce'),
      'manage_options',
      'zonos_submenu_zonos_main_settings',
      array($this, 'generate_zonos_main_settings_page')
    );

    add_submenu_page(
      'zonos_menu',
      __('Zonos Hello Configurations', 'zonos_for_woocommerce'),
      __('Zonos Hello', 'zonos_for_woocommerce'),
      'manage_options',
      'zonos_submenu_zonos_hello_settings',
      array($this, 'generate_zonos_hello_settings_page')
    );
  }

  /**
   * Register the main Plugin Settings
   * @method plugin_register_settings
   */
  function plugin_register_settings()
  {
    $this->register_zonos_main_config();
    $this->register_zonos_hello_config();
    $this->register_zonos_product_config();
    $this->register_zonos_coupon_config();
  }

  /**
   * Plugin settings page for general purposes
   * @method generate_zonos_main_settings_page
   */
  function generate_zonos_main_settings_page()
  {
    if (!current_user_can('manage_options')) return;


    wp_enqueue_script(
      'zfw_admin_selector_listener',
      ZFW_DIRECTORY_URL . 'admin/js/zfw_selector_listener.js',
      null,
      ZFW_VERSION,
    );

    $arguments = array(
      'settings_fields' => 'zonos_main_settings',
      'settings_sections' => 'zonos_main_settings_page',
      'submit_button' => __('Save Settings', 'zonos_for_woocommerce'),
      'description' => __('Some filler text', 'zonos_for_woocommerce'),
    );
    include_once ZFW_DIRECTORY_PATH . 'admin/templates/zfw-plugin-settings-page.php';
  }

  /**
   * Plugin settings page for Zonos Hello
   * @method generate_zonos_hello_settings_page
   */
  function generate_zonos_hello_settings_page()
  {
    if (!current_user_can('manage_options')) return;

    $arguments = array(
      'settings_fields' => 'zonos_hello_settings',
      'settings_sections' => 'zonos_hello_settings_page',
      'submit_button' => __('Save Settings', 'zonos_for_woocommerce'),
      'description' => __('Some filler text', 'zonos_for_woocommerce'),
    );
    include_once ZFW_DIRECTORY_PATH . 'admin/templates/zfw-plugin-settings-page.php';
  }

  /**
   * Generic input for the settings page
   * @method text_input_setting
   */
  function text_input_setting($arguments)
  {
    include ZFW_DIRECTORY_PATH . 'admin/partials/zfw-text-input.php';
  }

  /**
   * Generic selector for the settings page
   * @method selector_input_setting
   */
  function selector_input_setting($arguments)
  {
    include ZFW_DIRECTORY_PATH . 'admin/partials/zfw-selector-input.php';
  }

  /**
   * Adds form feedback errors to admin pages
   * @method add_settings_errors
   */
  function add_settings_errors()
  {
    settings_errors();
  }

  /**
   * Registers Zonos Main Configurations
   * @method register_zonos_main_config
   */
  function register_zonos_main_config()
  {
    add_settings_section(
      'zonos_main_settings_section',
      __('Configurations for Zonos Integration', 'zonos_for_woocommerce'),
      null,
      'zonos_main_settings_page'
    );

    // API Key
    add_settings_field(
      'zonos_api_key',
      __('API Key', 'zonos_for_woocommerce'),
      array($this, 'text_input_setting'),
      'zonos_main_settings_page',
      'zonos_main_settings_section',
      array(
        'id' => 'input_zonos_api_key',
        'name' => 'zonos_api_key',
        'value' => get_option('zonos_api_key', ''),
        'type' => 'text',
        'placeholder' => __('Enter your API Key', 'zonos_for_woocommerce'),
        'description' => __('Some filler text', 'zonos_for_woocommerce'),
      )
    );
    register_setting(
      'zonos_main_settings',
      'zonos_api_key',
      array(
        'type' => 'string',
        'sanitize_callback' => 'sanitize_text_field',
      )
    );

    // Store Id
    add_settings_field(
      'zonos_store_id',
      __('Store ID', 'zonos_for_woocommerce'),
      array($this, 'text_input_setting'),
      'zonos_main_settings_page',
      'zonos_main_settings_section',
      array(
        'id' => 'input_zonos_store_id',
        'name' => 'zonos_store_id',
        'value' => get_option('zonos_store_id', ''),
        'type' => 'text',
        'placeholder' => __('Enter your Store ID', 'zonos_for_woocommerce'),
        'description' => __('Some filler text', 'zonos_for_woocommerce'),
      )
    );
    register_setting(
      'zonos_main_settings',
      'zonos_store_id',
      array(
        'type' => 'string',
        'sanitize_callback' => 'sanitize_text_field',
      )
    );
  }

  /**
   * Registers Zonos Hello Configurations
   * @method register_zonos_hello_config
   */
  function register_zonos_hello_config()
  {
    add_settings_section(
      'zonos_hello_settings_section',
      __('Configurations for Zonos Hello', 'zonos_for_woocommerce'),
      null,
      'zonos_hello_settings_page'
    );

    // Placement Element Selector
    add_settings_field(
      'zonos_placement_element_selector',
      __('Placement Element Selector', 'zonos_for_woocommerce'),
      array($this, 'text_input_setting'),
      'zonos_hello_settings_page',
      'zonos_hello_settings_section',
      array(
        'id' => 'input_zonos_placement_element_selector',
        'name' => 'zonos_placement_element_selector',
        'value' => get_option('zonos_placement_element_selector', ''),
        'type' => 'text',
        'placeholder' => __('Example: #zonos-pop-up', 'zonos_for_woocommerce'),
        'description' => __('Some filler text', 'zonos_for_woocommerce'),
      )
    );
    register_setting(
      'zonos_hello_settings',
      'zonos_placement_element_selector',
      array(
        'type' => 'string',
        'sanitize_callback' => 'sanitize_text_field',
      )
    );

    // Cart URL Pattern
    add_settings_field(
      'zonos_cart_url_pattern',
      __('Cart URL Pattern', 'zonos_for_woocommerce'),
      array($this, 'text_input_setting'),
      'zonos_hello_settings_page',
      'zonos_hello_settings_section',
      array(
        'id' => 'input_zonos_cart_url_pattern',
        'name' => 'zonos_cart_url_pattern',
        'value' => get_option('zonos_cart_url_pattern', ''),
        'type' => 'text',
        'placeholder' => __('Example: https://www.zonos.com/cart', 'zonos_for_woocommerce'),
        'description' => __('Some filler text', 'zonos_for_woocommerce'),
      )
    );
    register_setting(
      'zonos_hello_settings',
      'zonos_cart_url_pattern',
      array(
        'type' => 'string',
        'sanitize_callback' => 'sanitize_text_field',
      )
    );

    // Country Override Behavior
    add_settings_field(
      'zonos_country_override_behavior',
      __('Country Override Behavior', 'zonos_for_woocommerce'),
      array($this, 'selector_input_setting'),
      'zonos_hello_settings_page',
      'zonos_hello_settings_section',
      array(
        'id' => 'input_zonos_country_override_behavior',
        'name' => 'zonos_country_override_behavior',
        'value' => get_option('zonos_country_override_behavior', 'URL_PARAM'),
        'description' => __('Some filler text', 'zonos_for_woocommerce'),
        'options' => array(
          'URL_PARAM' => __('URL_PARAM', 'zonos_for_woocommerce'),
          'SESSION' => __('SESSION', 'zonos_for_woocommerce'),
        )
      )
    );
    register_setting(
      'zonos_hello_settings',
      'zonos_country_override_behavior',
      array(
        'type' => 'string',
        'sanitize_callback' => 'sanitize_text_field',
      )
    );

    // Currency Behavior
    add_settings_field(
      'zonos_currency_behavior',
      __('Currency Behavior', 'zonos_for_woocommerce'),
      array($this, 'selector_input_setting'),
      'zonos_hello_settings_page',
      'zonos_hello_settings_section',
      array(
        'id' => 'input_zonos_currency_behavior',
        'name' => 'zonos_currency_behavior',
        'value' => get_option('zonos_currency_behavior', 'DISABLED'),
        'description' => __('Some filler text', 'zonos_for_woocommerce'),
        'options' => array(
          'DISABLED' => __('DISABLED', 'zonos_for_woocommerce'),
          'ENABLED' => __('ENABLED', 'zonos_for_woocommerce'),
        )
      )
    );
    register_setting(
      'zonos_hello_settings',
      'zonos_currency_behavior',
      array(
        'type' => 'string',
        'sanitize_callback' => 'sanitize_text_field',
      )
    );

    // Product Title Element Selector
    add_settings_field(
      'zonos_product_title_element_selector',
      __('Product Title Element Selector', 'zonos_for_woocommerce'),
      array($this, 'text_input_setting'),
      'zonos_hello_settings_page',
      'zonos_hello_settings_section',
      array(
        'id' => 'input_zonos_product_title_element_selector',
        'name' => 'zonos_product_title_element_selector',
        'value' => get_option('zonos_product_title_element_selector', ''),
        'type' => 'text',
        'placeholder' => __('Example: .title', 'zonos_for_woocommerce'),
        'description' => __('Some filler text', 'zonos_for_woocommerce'),
      )
    );
    register_setting(
      'zonos_hello_settings',
      'zonos_product_title_element_selector',
      array(
        'type' => 'string',
        'sanitize_callback' => 'sanitize_text_field',
      )
    );

    // Currency Behavior
    add_settings_field(
      'zonos_currency_behavior',
      __('Currency Behavior', 'zonos_for_woocommerce'),
      array($this, 'selector_input_setting'),
      'zonos_hello_settings_page',
      'zonos_hello_settings_section',
      array(
        'id' => 'input_zonos_currency_behavior',
        'name' => 'zonos_currency_behavior',
        'value' => get_option('zonos_currency_behavior', 'DISABLED'),
        'description' => __('Some filler text', 'zonos_for_woocommerce'),
        'options' => array(
          'DISABLED' => __('DISABLED', 'zonos_for_woocommerce'),
          'ENABLED' => __('ENABLED', 'zonos_for_woocommerce'),
        )
      )
    );
    register_setting(
      'zonos_hello_settings',
      'zonos_currency_behavior',
      array(
        'type' => 'string',
        'sanitize_callback' => 'sanitize_text_field',
      )
    );

    // Product Description Element Selector
    add_settings_field(
      'zonos_product_description_element_selector',
      __('Product Descriptor Element Selector', 'zonos_for_woocommerce'),
      array($this, 'text_input_setting'),
      'zonos_hello_settings_page',
      'zonos_hello_settings_section',
      array(
        'id' => 'input_zonos_product_description_element_selector',
        'name' => 'zonos_product_description_element_selector',
        'value' => get_option('zonos_product_description_element_selector', ''),
        'type' => 'text',
        'placeholder' => __('Example: .description', 'zonos_for_woocommerce'),
        'description' => __('Some filler text', 'zonos_for_woocommerce'),
      )
    );
    register_setting(
      'zonos_hello_settings',
      'zonos_product_description_element_selector',
      array(
        'type' => 'string',
        'sanitize_callback' => 'sanitize_text_field',
      )
    );

    // Currency Element Selector
    add_settings_field(
      'zonos_currency_element_selector',
      __('Currency Element Selector', 'zonos_for_woocommerce'),
      array($this, 'text_input_setting'),
      'zonos_hello_settings_page',
      'zonos_hello_settings_section',
      array(
        'id' => 'input_zonos_currency_element_selector',
        'name' => 'zonos_currency_element_selector',
        'value' => get_option('zonos_currency_element_selector', ''),
        'type' => 'text',
        'placeholder' => __('Example: #price, .price', 'zonos_for_woocommerce'),
        'description' => __('Some filler text', 'zonos_for_woocommerce'),
      )
    );
    register_setting(
      'zonos_hello_settings',
      'zonos_currency_element_selector',
      array(
        'type' => 'string',
        'sanitize_callback' => 'sanitize_text_field',
      )
    );

    // Product Price Element Selector
    add_settings_field(
      'zonos_product_price_element_selector',
      __('Product Price Element Selector', 'zonos_for_woocommerce'),
      array($this, 'text_input_setting'),
      'zonos_hello_settings_page',
      'zonos_hello_settings_section',
      array(
        'id' => 'input_zonos_product_price_element_selector',
        'name' => 'zonos_product_price_element_selector',
        'value' => get_option('zonos_product_price_element_selector', ''),
        'type' => 'text',
        'placeholder' => __('Example: .price', 'zonos_for_woocommerce'),
        'description' => __('Some filler text', 'zonos_for_woocommerce'),
      )
    );
    register_setting(
      'zonos_hello_settings',
      'zonos_product_price_element_selector',
      array(
        'type' => 'string',
        'sanitize_callback' => 'sanitize_text_field',
      )
    );

    // Product Add To Cart Element Selector
    add_settings_field(
      'zonos_product_add_to_cart_element_selector',
      __('Add To Cart Element Selector', 'zonos_for_woocommerce'),
      array($this, 'text_input_setting'),
      'zonos_hello_settings_page',
      'zonos_hello_settings_section',
      array(
        'id' => 'input_zonos_product_add_to_cart_element_selector',
        'name' => 'zonos_product_add_to_cart_element_selector',
        'value' => get_option('zonos_product_add_to_cart_element_selector', ''),
        'type' => 'text',
        'placeholder' => __('Example: .add-to-cart', 'zonos_for_woocommerce'),
        'description' => __('Some filler text', 'zonos_for_woocommerce'),
      )
    );
    register_setting(
      'zonos_hello_settings',
      'zonos_product_add_to_cart_element_selector',
      array(
        'type' => 'string',
        'sanitize_callback' => 'sanitize_text_field',
      )
    );

    // Automatically open Zonos Hello
    add_settings_field(
      'zonos_automatically_open',
      __('Automatic Pop-Up', 'zonos_for_woocommerce'),
      array($this, 'text_input_setting'),
      'zonos_hello_settings_page',
      'zonos_hello_settings_section',
      array(
        'id' => 'input_zonos_automatically_open',
        'name' => 'zonos_automatically_open',
        'value' => '1',
        'checked' => get_option('zonos_automatically_open', false),
        'type' => 'checkbox',
        'label' => __('Open Zonos Hello (Optional).', 'your-text-domain'),
        'description' => __('Some filler text', 'zonos_for_woocommerce'),
      )
    );
    register_setting(
      'zonos_hello_settings',
      'zonos_automatically_open',
      array(
        'type' => 'string',
        'sanitize_callback' => 'sanitize_text_field',
      )
    );
  }

  /**
   * Registers Zonos Product Mapping Configurations
   * @method register_zonos_product_config
   */
  function register_zonos_product_config()
  {
    add_settings_section(
      'zonos_product_settings_section',
      __('Product Mapping Configurations for Zonos Integration', 'zonos_for_woocommerce'),
      null,
      'zonos_main_settings_page'
    );

    $WCProductValues = [
      'Select WooCommerce Variable' => '',
      'name' => 'name',
      'description' => 'description',
      'short_description' => 'short_description',
      'variation_id' => 'variation_id',
      'product_id' => 'product_id',
      'price' => 'price',
      'sku' => 'sku',
      'length' => 'length',
      'width' => 'width',
      'height' => 'height',
      'weight' => 'weight',
      'image_id' => 'image_id',
      'product_tag' => 'product_tag',
      'product_brand' => 'product_brand',
      'product_cat' => 'product_cat',
      'custom' => 'custom',
    ];
    $productVariables = [
      'zonos_item_description' => [
        'name' => 'Description',
        'type' => 'selector_input_setting',
        'required' => false,
        'options' => $WCProductValues,
      ],
      'zonos_name' => [
        'name' => 'Name',
        'type' => 'selector_input_setting',
        'required' => true,
        'options' => $WCProductValues,
      ],
      'zonos_item_description_long' => [
        'name' => 'Long Description',
        'type' => 'selector_input_setting',
        'required' => false,
        'options' => $WCProductValues,
      ],
      'zonos_product_id' => [
        'name' => 'Product ID',
        'type' => 'selector_input_setting',
        'required' => true,
        'options' => $WCProductValues,
      ],
      'zonos_price' => [
        'name' => 'Price',
        'type' => 'selector_input_setting',
        'required' => true,
        'options' => $WCProductValues,
      ],
      'zonos_sku' => [
        'name' => 'Sku',
        'type' => 'selector_input_setting',
        'required' => true,
        'options' => $WCProductValues,
      ],
      'zonos_length' => [
        'name' => 'Length',
        'type' => 'selector_input_setting',
        'required' => true,
        'options' => $WCProductValues,
      ],
      'zonos_width' => [
        'name' => 'Width',
        'type' => 'selector_input_setting',
        'required' => true,
        'options' => $WCProductValues,
      ],
      'zonos_height' => [
        'name' => 'Height',
        'type' => 'selector_input_setting',
        'required' => true,
        'options' => $WCProductValues,
      ],
      'zonos_weight' => [
        'name' => 'Weight',
        'type' => 'selector_input_setting',
        'required' => true,
        'options' => $WCProductValues,
      ],
      'zonos_image' => [
        'name' => 'Image',
        'type' => 'selector_input_setting',
        'required' => false,
        'options' => $WCProductValues,
      ],
      'zonos_hs_code' => [
        'name' => 'HS Code',
        'type' => 'selector_input_setting',
        'required' => false,
        'options' => $WCProductValues,
      ],
      'zonos_brand' => [
        'name' => 'Brand',
        'type' => 'selector_input_setting',
        'required' => false,
        'options' => $WCProductValues,
      ],
      'zonos_category' => [
        'name' => 'Category',
        'type' => 'selector_input_setting',
        'required' => true,
        'options' => $WCProductValues,
      ],
      'zonos_country' => [
        'name' => 'Country',
        'type' => 'selector_input_setting',
        'required' => false,
        'options' => $WCProductValues,
      ],
      'zonos_url' => [
        'name' => 'Url',
        'type' => 'selector_input_setting',
        'required' => false,
        'options' => $WCProductValues,
      ],
    ];

    foreach ($productVariables as $id => $args) {
      add_settings_field(
        $id,
        __($args['name'] . ($args['required'] ? ' *' : ''), 'zonos_for_woocommerce'),
        array($this, $args['type']),
        'zonos_main_settings_page',
        'zonos_product_settings_section',
        array(
          'id' => 'input_' . $id,
          'name' => $id,
          'value' => get_option($id, ''),
          'placeholder' => __('Enter your ' . $args['name'], 'zonos_for_woocommerce'),
          'description' => __('Some filler text', 'zonos_for_woocommerce'),
          'options' => $args['options'],
          'required' => $args['required']
        )
      );
      register_setting(
        'zonos_main_settings',
        $id,
        array(
          'type' => 'string',
          'sanitize_callback' => 'sanitize_text_field',
        )
      );
    }
  }

  /**
   * Registers Zonos Coupon Mapping Configurations
   * @method register_zonos_coupon_config
   */
  function register_zonos_coupon_config()
  {
    add_settings_section(
      'zonos_coupon_settings_section',
      __('Coupon Mapping Configurations for Zonos Integration', 'zonos_for_woocommerce'),
      null,
      'zonos_main_settings_page'
    );

    $WCCouponValues = [
      'Select WooCommerce Variable' => '',
      'code' => 'code',
      'amount' => 'amount',
      'custom' => 'custom',
    ];
    $couponVariables = [
      'zonos_coupon_name' => [
        'name' => 'Name',
        'type' => 'selector_input_setting',
        'required' => true,
        'options' => $WCCouponValues,
      ],
      'zonos_coupon_price' => [
        'name' => 'Amount',
        'type' => 'selector_input_setting',
        'required' => true,
        'options' => $WCCouponValues,
      ]
    ];

    foreach ($couponVariables as $id => $args) {
      add_settings_field(
        $id,
        __($args['name'], 'zonos_for_woocommerce'),
        array($this, $args['type']),
        'zonos_main_settings_page',
        'zonos_coupon_settings_section',
        array(
          'id' => 'input_' . $id,
          'name' => $id . ($args['required'] ? ' *' : ''),
          'value' => get_option($id, ''),
          'placeholder' => __('Enter your ' . $args['name'], 'zonos_for_woocommerce'),
          'description' => __('Some filler text', 'zonos_for_woocommerce'),
          'options' => $args['options'],
          'required' => $args['required']
        )
      );
      register_setting(
        'zonos_main_settings',
        $id,
        array(
          'type' => 'string',
          'sanitize_callback' => 'sanitize_text_field',
        )
      );
    }
  }
}