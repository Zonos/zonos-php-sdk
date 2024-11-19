<?php
/**
 * Renders the admin settings page
 */
?>
<style>
    .wrap.settings th {
        padding: 10px;
    }

    .wrap.settings td {
        padding: 0;
    }

    .wrap.settings select,
    .wrap.settings input {
        width: 220px;
    }
    .hidden {
        display: none;
    }
</style>
<div class="wrap settings">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    <form action="options.php" method="post">
      <?php
      settings_fields('zonos_settings');
      do_settings_sections('page_zonos_setting');
      submit_button('Save Settings');
      ?>
    </form>
</div>