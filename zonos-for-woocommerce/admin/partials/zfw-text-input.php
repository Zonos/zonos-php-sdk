<?php
/**
 * Partial template for rendering a generic input field.
 *
 * Expected Variables:
 * - $arguments['id'] (string): ID attribute for the input field.
 * - $arguments['name'] (string): Name attribute for the input field.
 * - $arguments['type'] (string): Type attribute for the input field (default: text).
 * - $arguments['value'] (string): Current value of the input field.
 * - $arguments['placeholder'] (string): Placeholder text (optional).
 * - $arguments['class'] (string): Additional classes for the input field (optional).
 * - $arguments['checked'] (boolean): Checked state for the input (optional).
 * - $arguments['label'] (string): Determines a label to be displayed for the input.
 * - $arguments['description'] (string): Determines a description to be displayed under the input.
 */
?>

<input
        id="<?php echo esc_attr($arguments['id']); ?>"
        type="<?php echo esc_attr($arguments['type'] ?? 'text'); ?>"
        name="<?php echo esc_attr($arguments['name']); ?>"
        value="<?php echo esc_attr($arguments['value'] ?? ''); ?>"
        class="<?php echo esc_attr($arguments['class'] ?? 'regular-text'); ?>"
        placeholder="<?php echo esc_attr($arguments['placeholder'] ?? ''); ?>"
  <?php
  if (!empty($arguments['checked']) && $arguments['type'] === 'checkbox') {
    checked($arguments['checked'], true);
  }
  ?>
/>
<?php if (!empty($arguments['label'])) : ?>
    <label for="<?php echo esc_attr($arguments['id']); ?>">
      <?php echo esc_html($arguments['label']); ?>
    </label>
<?php endif; ?>

<?php if (!empty($arguments['description'])) : ?>
    <p class="description">
      <?php echo esc_html($arguments['description']); ?>
    </p>
<?php endif; ?>
