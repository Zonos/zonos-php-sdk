<?php
/**
 * Renders the admin settings page
 *
 *  Expected Variables:
 *  - $arguments['settings_fields'] (string): The
 *  - $arguments['settings_sections'] (string): The
 *  - $arguments['submit_button'] (string): Translation text for the submit button.
 */
?>
<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

  <?php if (!empty($arguments['description'])) : ?>
      <p>
        <?php echo esc_html($arguments['description']); ?>
      </p>
  <?php endif; ?>
    <form action="options.php" method="post">
      <?php
      settings_fields($arguments['settings_fields']);
      do_settings_sections($arguments['settings_sections']);
      submit_button($arguments['submit_button']);
      ?>
    </form>
</div>