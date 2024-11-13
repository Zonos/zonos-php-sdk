<?php

/**
 * Contains the Admin functionality
 *
 * @Class ZFW_Admin
 */
class ZFW_Admin
{

  /**
   * Create the admin Menu
   * @method create_admin_menu
   */
  function create_admin_menu()
  {
    add_menu_page(
      'Zonos Settings',
      'Zonos',
      'manage_options',
      'zonos_menu',
      null,
      'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiPz48c3ZnIGlkPSJMYXllcl8xIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAyMCAyMCI+PGRlZnM+PHN0eWxlPi5jbHMtMXtmaWxsOiNmZmY7fTwvc3R5bGU+PC9kZWZzPjxwYXRoIGNsYXNzPSJjbHMtMSIgZD0iTTE2LjU3LDMuODVjLTEuMDUtMS4wMy0yLjM1LTEuOC0zLjc2LTIuMjEtLjkzLS4yNy0xLjg4LS40MS0yLjgxLS40MS0yLjQ4LDAtNC44MiwuOTctNi41NywyLjcyQzEuNjcsNS43LC43MSw4LjAzLC43MSwxMC41MmMwLC45NiwuMTUsMS45MiwuNDUsMi44NiwwLC4wMywuMDQsLjEyLC4wNiwuMTksLjAzLC4wOSwuMDcsLjIsLjA3LC4yMSwuMDMsLjA3LC4xLC4xMiwuMTcsLjEyLC4wMiwwLC4wNCwwLC4wNi0uMDFsMS4yMy0uNDJzMCwwLC4wMSwwYy41NywxLjM4LDEuNTIsMi41NywyLjc1LDMuNDQsMS4zMiwuOTIsMi44NiwxLjQxLDQuNDcsMS40MSwxLjE5LDAsMi4zNC0uMjYsMy40MS0uNzksMCwwLDAsMCwuMDEsMGwuNjMsMS4xNGMuMDMsLjA2LC4wOSwuMSwuMTYsLjEsLjAzLDAsLjA2LDAsLjA5LS4wMmwuMzctLjIxYzEuNzctMS4wMywzLjE3LTIuNjUsMy45My00LjU0LC44Mi0yLjAyLC45LTQuMjUsLjIyLTYuNDMtLjQzLTEuNC0xLjIyLTIuNjktMi4yNy0zLjcyWm0tNi4zLDQuMDdjMS4yMywuMTIsMi4yLDEuMSwyLjMzLDIuMzMsLjA4LC43NS0uMTYsMS40Ny0uNjYsMi4wMi0uNSwuNTUtMS4yLC44Ni0xLjk0LC44Ni0uMDksMC0uMTgsMC0uMjctLjAxLTEuMjMtLjEyLTIuMi0xLjEtMi4zMy0yLjMzLS4wOC0uNzUsLjE2LTEuNDcsLjY2LTIuMDIsLjUtLjU1LDEuMi0uODYsMS45NC0uODYsLjA5LDAsLjE4LDAsLjI3LC4wMVptMy44NSw5LjIyYzEuMDgtLjY3LDEuOTgtMS42MSwyLjYyLTIuNywuNjktMS4xOSwxLjA2LTIuNTQsMS4wNi0zLjkyLDAtMS4wOS0uMjItMi4xNS0uNjYtMy4xNC0uNDItLjk2LTEuMDMtMS44MS0xLjc5LTIuNTNsLS4zMS0uMjlzLS4wOC0uMDUtLjEzLS4wNS0uMSwuMDItLjEzLC4wNWMwLDAtLjI5LC4zMS0uODUsLjkzaC0uMDJjLTEuMjItLjk1LTIuNzctMS40My00LjMtMS4zNC0zLjE2LC4xOS01LjczLDIuNzEtNS45OCw1Ljg3LS4xLDEuMjQsLjE2LDIuNDYsLjc1LDMuNTQsLjU3LDEuMDQsMS40MSwxLjksMi40NCwyLjVsLjIsLjExcy4wOSwuMDUsLjEzLC4wN2wuMDQsLjAyYy4wOSwuMDUsLjIsMCwuMjUtLjA4bC42Mi0xLjE3czAsMCwuMDIsMGMuNjcsLjI5LDEuMzksLjQyLDIuMTEsLjM5LDEuMjUtLjA1LDIuNDItLjU3LDMuMy0xLjQ4LC44OC0uOSwxLjM3LTIuMDgsMS4zOS0zLjMzLC4wMi0xLjI4LS40Ni0yLjQ5LTEuMzQtMy40Mi0uODgtLjkyLTIuMDYtMS40Ni0zLjMzLTEuNTItLjAzLDAtLjExLDAtLjE5LDBoLS4wOGMtLjA3LDAtLjEzLDAtLjE2LDAtLjA1LDAtLjExLC4wMy0uMTQsLjA3LS4wMywuMDMtLjA0LC4wOC0uMDQsLjEydjEuM3MwLC4wMS0uMDEsLjAxYy0uODUsLjEtMS42NSwuNTQtMi4yLDEuMTktLjYsLjcxLS44OCwxLjYyLS43OCwyLjU1LC4xNywxLjYxLDEuNDYsMi44OCwzLjA3LDMuMDMsLjEsMCwuMjEsLjAxLC4zMSwuMDEsMS44OCwwLDMuNC0xLjUzLDMuNC0zLjQsMC0xLjY5LTEuMzItMy4xNy0zLTMuMzgsMCwwLS4wMSwwLS4wMS0uMDF2LS42NmguMDFjMS4wNCwuMDksMi4wMywuNjEsMi43MSwxLjQxLC43NCwuODcsMS4wNywxLjk4LC45NCwzLjEyLS4yMSwxLjgyLTEuNjMsMy4yOC0zLjQ1LDMuNTUtLjIsLjAzLS40MSwuMDUtLjYxLC4wNS0uNjEsMC0xLjE5LS4xMy0xLjc0LS4zOWwtLjM2LS4xOWMtLjA4LS4wNC0uMTktLjAyLS4yNSwuMDhsLS42LDEuMTVoLS4wMmMtLjg0LS41My0xLjUzLTEuMjctMS45OS0yLjE1LS41MS0uOTgtLjcyLTIuMDktLjU5LTMuMjEsLjE0LTEuMjcsLjczLTIuNDcsMS42Ny0zLjM4LC45My0uOSwyLjE1LTEuNDYsMy40My0xLjU2LC4xNS0uMDEsLjMxLS4wMiwuNDUtLjAyLDEuMjksMCwyLjU0LC40NSwzLjU0LDEuMjdsLjMxLC4yOXMuMDIsLjAxLC4wMiwuMDJjLjA3LC4wNSwuMTcsLjA0LC4yMy0uMDIsMCwwLC4wNS0uMDUsLjM4LS40MWwuNDYtLjVoLjAyYzEuMzcsMS4zNywyLjEyLDMuMjcsMi4wNSw1LjItLjA2LDEuNzYtLjc5LDMuNDMtMi4wNCw0LjY5LTEuMjUsMS4yNi0yLjkxLDEuOTktNC42NywyLjA3LS4xLDAtLjIsMC0uMywwLTEuNDcsMC0yLjg3LS40NS00LjA3LTEuMy0xLjE3LS44My0yLjA0LTEuOTktMi41My0zLjMzbC0uMDktLjI2Yy0uMDItLjA4LS4wOS0uMTMtLjE4LS4xMy0uMDIsMC0uMDQsMC0uMDYsMGwtMS4yMSwuNDJoLS4wMWMtLjMxLTEuMDYtLjQyLTIuMTUtLjMxLTMuMjQsLjQyLTQuMjQsMy45NC03LjU0LDguMi03LjY3LC4wOSwwLC4xOCwwLC4yNiwwLDQuNjksMCw4LjUsMy44MSw4LjUsOC41LDAsMS41MS0uNCwyLjk5LTEuMTYsNC4yOC0uNywxLjItMS43LDIuMjEtMi44OCwyLjk1aC0uMDFzLS4zMi0uNTktLjMyLS41OWMwLDAsMC0uMDEsMC0uMDJaIi8+PC9zdmc+',
      100
    );

    add_submenu_page(
      'zonos_menu',
      'Settings',
      'Zonos Settings',
      'manage_options',
      'zonos_submenu_settings',
      array($this, 'plugin_settings_page')
    );
  }

  /**
   * Register the main Plugin Settings
   * @method plugin_register_settings
   */
  function plugin_register_settings()
  {
    add_settings_section(
      'zonos_settings_section_integrations',
      null,
      null,
      'page_zonos_setting'
    );

    add_settings_field(
      'zonos_store_id',
      'Store ID',
      array($this, 'input_store_id'),
      'page_zonos_setting',
      'zonos_settings_section_integrations'
    );

    add_settings_field(
      'zonos_api_key',
      'API Key',
      array($this, 'input_api_key'),
      'page_zonos_setting',
      'zonos_settings_section_integrations'
    );

    register_setting(
      'zonos_settings',
      'zonos_store_id',
      array(
        'type' => 'string',
        'sanitize_callback' => 'sanitize_text_field',
      )
    );
    register_setting(
      'zonos_settings',
      'zonos_api_key',
      array(
        'type' => 'string',
        'sanitize_callback' => 'sanitize_text_field',
      )
    );
  }

  /**
   * Plugin main settings page
   * @method plugin_settings_page
   */
  function plugin_settings_page()
  {
    if (!current_user_can('manage_options')) return;
    ?>
      <div class="wrap">
          <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
          <form action="options.php" method="post">
            <?php
            settings_fields('zonos_settings');
            do_settings_sections('page_zonos_setting');
            submit_button('Save Settings');
            ?>
          </form>
      </div>
    <?php

  }

  /**
   * Input for the store id in the main settings page
   * @method input_store_id
   */
  function input_store_id()
  {
    $setting = get_option('zonos_store_id');
    ?>
      <input type="text" name="zonos_store_id"
             value="<?php echo isset($setting) ? esc_attr($setting) : ''; ?>">
    <?php
  }


  /**
   * Input for the store id in the main settings page
   * @method input_api_key
   */
  function input_api_key()
  {
    $setting = get_option('zonos_api_key');
    ?>
      <input type="text" name="zonos_api_key"
             value="<?php echo isset($setting) ? esc_attr($setting) : ''; ?>">
    <?php
  }
}