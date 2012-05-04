<?php
function fb_admin_dialog($message, $error = false) {
		global $wp_version;

		echo '<div ' . ( $error ? 'id="disqus_warning" ' : '') . 'class="updated fade' . '"><p><strong>'. $message . '</strong></p></div>';
}

function fb_construct_fields($placement, $children, $parent = null) {
	$options = get_option('fb_options');
	
	if ($placement == 'widget') {
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = __( 'Like ' . esc_attr(get_bloginfo('name')) . ' on Facebook', 'text_domain' );
		}
		
		$children_fields = fb_construct_fields_children($placement, $children, $parent);
		
		/*echo '<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
			</p>';*/
		}
	else if ($placement == 'settings') {
		$children_fields = fb_construct_fields_children($placement, $children, $parent);
		
		echo '<table class="form-table">
						<tbody>';
		
		if ($parent) {
			echo '	<tr valign="top">
								<th scope="row"><strong>Enable</strong></th>
								<td><a href="' . $parent['help_link'] . '" target="_new" title="' . $parent['help_text'] . '" style=" text-decoration: none;">[?]</a>&nbsp; <input type="checkbox" name="fb_options[' . $parent['name'] . '][enabled]" value="true" id="' . $parent['name'] . '" ' . checked(isset($options[$parent['name']]['enabled']), 1, false) . ' onclick="toggleOptions(\'' . $parent['name'] . '\', [\'' . implode("','", $children_fields['names']) . '\'])"></td>
								</tr>';
		}
			echo $children_fields['output'];
			
			echo '</tbody>
						</table>';
	}
}

function fb_construct_fields_children($placement, $children, $parent = null) {
	$options = get_option('fb_options');
	
	print '<!--';
	print_r($options);
	print '-->';
	
	$display = ' style="display: none" ';
	
	if ($parent) {
		if (isset($options[$parent['name']]['enabled']) && $options[$parent['name']]['enabled'] == 'true') {
			$display = '';
		}
	}
	else {
		$display = '';
	}
	
	$children_output = '';
	
	foreach ($children as $child) {
		$help_link = '';
		
		if (!isset($child['help_link'])) {
			$help_link = '<a href="#" target="_new" title="' . $child['help_text'] . '" onclick="return false;" style="color: #aaa; text-decoration: none;">[?]</a>';
		}
		else {
			$help_link = '<a href="' . $child['help_link'] . '" target="_new" title="' . $child['help_text'] . '" style=" text-decoration: none;">[?]</a>';
		}
		
		$parent_js_array = '';
		
		if ($parent) {
			$parent_js_array = '[' . $parent['name'] . ']';
			
			if (isset($options[$parent['name']][$child['name']])) {
				$child_value = $options[$parent['name']][$child['name']];
			}
		}
		else {
			if (isset($options[$child['name']])) {
				$child_value = $options[$child['name']];
			}
		}
		
		switch ($child['field_type']) {
			case 'dropdown':
				$children_output .= '	<tr valign="top"' . $display . ' id="' . $parent['name'] . '_' . $child['name'] . '">
						<th scope="row">' . ucwords(str_replace("_", " ", $child['name'])) . '</th>
						<td>' . $help_link . '&nbsp;';
				
				$children_output .= '<select name="fb_options' . $parent_js_array . '[' . $child['name'] . ']">';
				
				if (isset($child_value)) {
					foreach ($child['options'] as $option) {
						$children_output .= '<option value="' . $option . '" ' . selected( $child_value, $option, false ) . '>' . $option . '</option>';
					}
				}
				else {
					foreach ($child['options'] as $option) {
						$children_output .= '<option value="' . $option . '">' . $option . '</option>';
					}
				}
				
				$children_output .= '</select></td></tr>';
				
				break;
			case 'checkbox':
				$children_output .= '	<tr valign="top"' . $display . ' id="' . $parent['name'] . '_' . $child['name'] . '">
						<th scope="row">' . ucwords(str_replace("_", " ", $child['name'])) . '</th>
						<td>' . $help_link . '&nbsp; <input type="checkbox" name="fb_options' . $parent_js_array . '[' . $child['name'] . ']" value="true"' . checked(isset($child_value), 1, false) . '></td>
						</tr>';
				break;
			case 'text':
				$text_field_value = '';
				
				if (isset($child_value)) {
					$text_field_value = $child_value;
				}
				
				$children_output .= '	<tr valign="top"' . $display . ' id="' . $parent['name'] . '_' . $child['name'] . '">
						<th scope="row">' . ucwords(str_replace("_", " ", $child['name'])) . '</th>
						<td>' . $help_link . '&nbsp; <input type="text" name="fb_options' . $parent_js_array . '[' . $child['name'] . ']" value="' . $text_field_value . '"></td>
						</tr>';
				break;
		}
		
		
		if ($parent['name']) {
			$children_names[] = $parent['name'] . '_' . $child['name'];
		}
		else {
			$children_names[] = $child['name'];
		}
		
		
	}
	
	$return['output'] = $children_output;
	$return['names'] = $children_names;
	
	return $return;
}
?>