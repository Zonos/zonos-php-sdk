<?php
/**
 * Partial template for rendering a generic select field.
 *
 * Expected Variables:
 * - $args['name'] (string): Name attribute for the input field.
 * - $args['type'] (string): Type attribute for the input field (default: text).
 * - $args['value'] (string): Current value of the input field.
 * - $args['options'] (string[]): Array of options available to select.
 * - $args['placeholder'] (string): Placeholder text (optional).
 * - $args['class'] (string): Additional classes for the input field (optional).
 * - $args['required'] (boolean): Flag to denote if the input is required (optional).
 */
?>
<select
	<?php echo $args['required'] ? 'required' : ''; ?>
	name="<?php echo esc_attr($args['name']); ?>"
	id="<?php echo esc_attr($args['name'] ?? ''); ?>"
>
	<option value="">Select WooCommerce Variable</option>
	<?php echo isset($args['value']) && !in_array($args['value'], $args['options']) ?
	  '<option value="'.$args['value'].'" selected>'.$args['value'].'</option>': '';
  ?>
	<?php foreach($args['options'] as $option) {
		$selected = ($args['value'] && $args['value'] == $option) ? 'selected' : '';
		echo '<option value="'.$option.'" '.$selected.'>'.$option.'</option>';
	}?>
</select>
<input
	class="hidden"
	type="text"
	placeholder="<?php echo 'Enter value for '.$args['name']; ?>"
	name="<?php echo esc_attr($args['name']); ?>"
	id="<?php echo esc_attr($args['name'] ?? ''); ?>"
	required
	disabled
>