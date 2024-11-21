<?php
/**
 * Partial template for rendering a generic selector input.
 *
 * Expected Variables:
 * - $arguments['id'] (string): ID attribute for the selector field.
 * - $arguments['name'] (string): Name attribute for the selector field.
 * - $arguments['options'] (array): Array of options for the selector.
 * - $arguments['value'] (string): Current value of the selector.
 * - $arguments['class'] (string): Additional classes for the selector (optional).
 * - $arguments['label'] (string): Determines a label to be displayed for the selector.
 * - $arguments['description'] (string): Determines a description to be displayed under the selector.
 */
?>
<select
        id="<?php echo esc_attr($arguments['id']); ?>"
        name="<?php echo esc_attr($arguments['name']); ?>"
        class="<?php echo esc_attr($arguments['class'] ?? 'regular-text'); ?>"
        <?php echo $arguments['required'] ? 'required' : ''; ?>>
  <?php if ($arguments['value'] && !in_array($arguments['value'], $arguments['options'])) : ?>
    <option value="<?php echo esc_attr($arguments['value']); ?>" selected><?php echo esc_attr($arguments['value']); ?></option>
  <?php endif; ?>
  <?php foreach ($arguments['options'] as $label => $value) : ?>
      <option value="<?php echo esc_attr($value); ?>" <?php selected($arguments['value'], $value); ?>>
        <?php echo esc_html($label); ?>
      </option>
  <?php endforeach; ?>
</select>
<input
        class="hidden selector-custom"
        type="text"
        placeholder="<?php echo esc_attr($arguments['placeholder']); ?>"
        name="<?php echo esc_attr($arguments['name']); ?>"
        id="<?php echo esc_attr($arguments['id'] ?? ''); ?>"
        required
        disabled
>
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
