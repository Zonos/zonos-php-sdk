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
<style>
    .wrap.settings th {
        padding: 10px;
    }

    .wrap.settings td {
        padding: 0;
    }

    .wrap.settings select,
    .wrap.settings input {
        width: 240px;
    }
    .hidden {
        display: none;
    }
    .selector-custom {
        margin-top: -10px;
    }
</style>
<div class="wrap settings">
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