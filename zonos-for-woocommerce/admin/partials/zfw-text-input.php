<?php
/**
 * Partial template for rendering a generic input field.
 *
 * Expected Variables:
 * - $args['name'] (string): Name attribute for the input field.
 * - $args['type'] (string): Type attribute for the input field (default: text).
 * - $args['value'] (string): Current value of the input field.
 * - $args['placeholder'] (string): Placeholder text (optional).
 * - $args['class'] (string): Additional classes for the input field (optional).
 */
?>

<input
        type="<?php echo esc_attr($args['type'] ?? 'text'); ?>"
        name="<?php echo esc_attr($args['name']); ?>"
        value="<?php echo esc_attr($args['value'] ?? ''); ?>"
        class="<?php echo esc_attr($args['class'] ?? 'regular-text'); ?>"
        placeholder="<?php echo esc_attr($args['placeholder'] ?? ''); ?>"
/>