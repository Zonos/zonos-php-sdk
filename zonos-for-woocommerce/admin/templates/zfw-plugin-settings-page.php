<?php
/**
 * Renders the admin settings page
 */
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