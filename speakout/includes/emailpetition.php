<?php
include_once( 'class.petition.php' );
include_once( 'class.speakout.php' );
include_once( 'class.wpml.php' );
include_once( 'parsedown.php' );

// Define the maximum number of custom fields supported.
define( 'SPEAKOUT_MAX_CUSTOM_FIELDS', 9 );

// register shortcode to display signatures count 
add_shortcode( 'signaturecount', 'dk_speakout_signaturescount_shortcode' );

function dk_speakout_signaturescount_shortcode( $attr ) {
	$petition = new dk_speakout_Petition();
	$id       = 1; // default
	if ( isset( $attr['id'] ) ) {
		$id = absint( $attr['id'] );
	}

	$petition_exists = $petition->retrieve( $id );
	if ( $petition_exists ) {
		return "<span class='signatureCount'>" . esc_html( $petition->signatures ) . "</span>";
	} else {
		return '';
	}
}


// register shortcode to display signatures goal
add_shortcode( 'signaturegoal', 'dk_speakout_signaturesgoal_shortcode' );

function dk_speakout_signaturesgoal_shortcode( $attr ) {
	$petition = new dk_speakout_Petition();

	$id = 1; // default
	if ( isset( $attr['id'] ) ) {
		$id = absint( $attr['id'] );
	}

	$petition_exists = $petition->retrieve( $id );
	if ( $petition_exists ) {
		return "<span class='signatureGoal'>" . esc_html( $petition->goal ) . "</span>";
	} else {
		return '';
	}
}

// register shortcode to display petition title
add_shortcode( 'petitiontitle', 'dk_speakout_petitiontitle_shortcode' );

function dk_speakout_petitiontitle_shortcode( $attr ) {
	$petition = new dk_speakout_Petition();

	$id = 1; // default
	if ( isset( $attr['id'] ) ) {
		$id = absint( $attr['id'] );
	}

	$petition_exists = $petition->retrieve( $id );
	if ( $petition_exists ) {
		return "<span class='petitionTitle'>" . esc_html( $petition->title ) . "</span>";
	} else {
		return '';
	}
}

// register shortcode to display petition message
add_shortcode( 'petitionmessage', 'dk_speakout_petitionmessage_shortcode' );

function dk_speakout_petitionmessage_shortcode( $attr ) {
	$petition = new dk_speakout_Petition();

	$id = 1; // default
	if ( isset( $attr['id'] ) ) {
		$id = absint( $attr['id'] );
	}

	$petition_exists = $petition->retrieve( $id );
	if ( $petition_exists ) {
		$Parsedown = new Parsedown();
		return "<span class='petitionMessage'>" . wp_kses_post( $Parsedown->text( $petition->petition_message ) ) . "</span>";
	} else {
		return '';
	}
}

/**
 * Renders custom fields for the petition form.
 * Moved outside of the shortcode to prevent fatal 'redeclare function' errors.
 *
 * @param object $petition The petition object.
 * @param int    $location The location to display fields for.
 * @return string The HTML for the custom fields.
 */
function dk_speakout_render_custom_fields( $petition, $location ) {
	$form_html = '';

	// Configuration for custom field types.
	$field_types = array(
		5 => 'select',
		6 => 'checkbox',
		7 => 'checkbox',
		8 => 'checkbox',
		9 => 'checkbox',
	);

	for ( $i = 1; $i <= SPEAKOUT_MAX_CUSTOM_FIELDS; $i++ ) {
		$base_name    = ( $i === 1 ) ? 'custom_field' : "custom_field{$i}";
		$displays_key = 'displays_' . $base_name;
		$location_key = $base_name . '_location';
		$required_key = $base_name . '_required';
		$label_key    = $base_name . '_label';
		$values_key   = $base_name . '_values';
		$type         = isset( $field_types[ $i ] ) ? $field_types[ $i ] : 'text';

		if ( ! empty( $petition->{$displays_key} ) && (int) $petition->{$location_key} === $location ) {
			$required_attr = ! empty( $petition->{$required_key} ) ? ' required="required"' : '';
			$field_id      = 'dk-speakout-' . $base_name . '-' . absint( $petition->id );
			$placeholder   = esc_attr( $petition->{$label_key} );

			$form_html .= '<div class="dk-speakout-full">';
			switch ( $type ) {
				case 'select':
					$form_html .= '<select name="dk-speakout-' . $base_name . '" id="' . $field_id . '"' . $required_attr . ' >';
					$form_html .= '<option value="">' . esc_html( $petition->{$label_key} ) . '</option>';
					$arrFieldValues = explode( "|", $petition->{$values_key} );
					foreach ( $arrFieldValues as $fieldValue ) {
						$form_html .= '<option value="' . esc_attr( $fieldValue ) . '">' . esc_html( $fieldValue ) . '</option>';
					}
					$form_html .= '</select>';
					break;
				case 'checkbox':
					$form_html .= '<input name="dk-speakout-' . $base_name . '" id="' . $field_id . '" type="checkbox" ' . $required_attr . ' value="1" /> <label for="' . $field_id . '">' . esc_html( $petition->{$label_key} ) . '</label>';
					break;
				case 'text':
				default:
					$form_html .= '<input name="dk-speakout-' . $base_name . '" id="' . $field_id . '" maxlength="400" type="text" placeholder="' . $placeholder . '"' . $required_attr . ' />';
					break;
			}
			$form_html .= '</div>';
		}
	}
	return $form_html;
}

// register shortcode to display petition form
add_shortcode( 'emailpetition', 'dk_speakout_emailpetition_shortcode' );

function dk_speakout_emailpetition_shortcode( $attr ) {

	// only query a petition if the "id" attribute has been set
	if ( isset( $attr['id'] ) && is_numeric( $attr['id'] ) ) {

		global $dk_speakout_version;
		$petition = new dk_speakout_Petition();
		$wpml     = new dk_speakout_WPML();
		$options  = get_option( 'dk_speakout_options' );
		$Parsedown = new Parsedown();

		// get petition data from database
		$id              = absint( $attr['id'] );
		$petition_exists = $petition->retrieve( $id );

		// attempt to translate with WPML
		$wpml->translate_petition( $petition );
		$options   = $wpml->translate_options( $options );
		$wpml_lang = defined( 'ICL_LANGUAGE_CODE' ) ? ICL_LANGUAGE_CODE : '';

		// Set up variables for the template
		$showPro = false;
		$lang = $wpml_lang;
		$css_classes = 'dk-speakout-petition-id-' . $petition->id;
		$width = ( isset( $attr['width'] ) ) ? 'style="width: ' . absint( $attr['width'] ) . 'px;"' : '';
		$petitionReadTitle = __( 'Read the petition', 'speakout' );
		
		// Get honorifics
		$honorifics = "";
		$custom_file_name = file_exists( plugin_dir_path( __DIR__ ) . "custom/honorifics.txt") ? plugin_dir_path( __DIR__ ) . "custom/honorifics.txt" : plugin_dir_path( __DIR__ ) . "includes/honorifics.txt";
		$file = file( $custom_file_name );
		foreach ( $file as $line ) {
			if ( trim( $line ) != '' ) {
				$honorifics .= '<option value="' .  esc_html( trim( $line ) ) . '">' .  esc_html( trim( $line ) ) . '</option>' . PHP_EOL;
			}
		}

		// Get countries
		$countries = "";
		$custom_file_name = file_exists( plugin_dir_path( __DIR__ ) . "custom/countries.txt") ? plugin_dir_path( __DIR__ ) . "custom/countries.txt" : plugin_dir_path( __DIR__ ) . "includes/countries.txt";
		$file = file( $custom_file_name );
		foreach ( $file as $line ) {
			if ( trim( $line ) != '' ) {
				$countries .= '<option value="' .  esc_html( trim( $line ) ) . '">' .  esc_html( trim( $line ) ) . '</option>' . PHP_EOL;
			}
		}

		$height = ( $petition->is_editable == 1 ) ? 'style="height: 200px;"' : '';
		$kses_array = array( 'a' => array( 'href' => array(), 'title' => array() ), 'br' => array(), 'em' => array(), 'strong' => array() );
		$goal_text = ( $petition->goal > 0 ) ? __( 'Goal:', 'speakout' ) . ' ' . number_format( $petition->goal, 0, $options['decimal_separator'], $options['thousands_separator'] ) : '';
		$progress_width = ( $petition->goal > 0 ) ? ( $petition->signatures / $petition->goal ) * 100 : 0;
		$mailto_href = 'mailto:?subject=' . rawurlencode( $petition->title ) . '&body=' . rawurlencode( get_permalink() );

		if ( $petition_exists ) {

			$expired = ( $petition->expires == 1 && current_time( 'timestamp' ) >= strtotime( $petition->expiration_date ) ) ? 1 : 0;

			// Start output buffering
			ob_start();

			// Include the template file
			include( 'email-petition-form-template.php' );

			// Get the buffered content
			$petition_form = ob_get_clean();

		}
		// petition doesn't exist
		else {
			$petition_form = '';
		}
	}

	// id attribute was left out, as in [emailpetition]
	else {
		$petition_form = '
			<div class="dk-speakout-petition-wrap dk-speakout-expired">
				<h3>' . esc_html__( 'Petition', 'speakout' ) . '</h3>
				<div class="dk-speakout-notice">
					<p>' . esc_html__( 'Error: The site administrator must include a valid petition id  in the shortcode.', 'speakout' ) . '</p>
				</div>
			</div>';
	}

	return $petition_form;
}

// load public CSS on pages/posts that contain the [emailpetition] shortcode
add_filter( 'the_posts', 'dk_speakout_public_css_js' );

function dk_speakout_public_css_js( $posts ) {
	global $dk_speakout_version;
	if ( empty( $posts ) ) return $posts;

	$options         = get_option( 'dk_speakout_options' );
	$shortcode_found = false;

	foreach ( $posts as $post ) {
		if ( has_shortcode( $post->post_content, 'emailpetition' ) ) {
			$shortcode_found = true;
			break;
		}
	}

	// load the CSS and JavaScript
	if ( $shortcode_found ) {
		$theme = $options['petition_theme'];

		switch ( $theme ) {
			case 'default':
				wp_enqueue_style( 'dk_speakout_css', esc_url( plugins_url( 'speakout/css/theme-default.css' ) ), array(), $dk_speakout_version );
				break;
			case 'basic':
				wp_enqueue_style( 'dk_speakout_css', esc_url( plugins_url( 'speakout/css/theme-basic.css' ) ), array(), $dk_speakout_version );
				break;
			case 'none':
				$parent_dir                = get_template_directory_uri();
				$parent_petition_theme_url = $parent_dir . '/petition.css';

				// if a child theme is in use
				// attempt to load petition.css from child theme folder
				if ( is_child_theme() ) {
					$child_dir                 = get_stylesheet_directory_uri();
					$child_petition_theme_url  = $child_dir . '/petition.css';
					$child_petition_theme_path = STYLESHEETPATH . '/petition.css';

					// use child theme if it exists
					if ( file_exists( $child_petition_theme_path ) ) {
						wp_enqueue_style( 'dk_speakout_css', esc_url( $child_petition_theme_url ), array(), $dk_speakout_version );
					}
					// else try to load style from parent theme folder
					else {
						wp_enqueue_style( 'dk_speakout_css', esc_url( $parent_petition_theme_url ), array(), $dk_speakout_version );
					}
				}
				// try to load style from active theme folder
				else {
					wp_enqueue_style( 'dk_speakout_css', esc_url( $parent_petition_theme_url ), array(), $dk_speakout_version );
				}
				break;
		}

		// ensure ajax callback url works on both https and http
		$protocol = isset( $_SERVER['HTTPS'] ) ? 'https://' : 'http://';
		$params   = array( 'ajaxurl' => esc_url( admin_url( 'admin-ajax.php', $protocol ) ) );
		if ( isset( $options['g_recaptcha_status'] ) && $options['g_recaptcha_status'] == "on" ) {
			wp_enqueue_script( 'dk_speakout_js', esc_url( plugins_url( 'speakout/js/public-gr.js' ) ), array( 'jquery' ), $dk_speakout_version );
		} elseif ( isset( $options['hcaptcha_status'] ) && $options['hcaptcha_status'] == "on" ) {
			wp_enqueue_script( 'dk_speakout_js', esc_url( plugins_url( 'speakout/js/public-h.js' ) ), array( 'jquery' ), $dk_speakout_version );
		} else {
			wp_enqueue_script( 'dk_speakout_js', esc_url( plugins_url( 'speakout/js/public.js' ) ), array( 'jquery' ), $dk_speakout_version );
		}
		wp_enqueue_script( 'jquery-effects-highlight' );
		wp_localize_script( 'dk_speakout_js', 'dk_speakout_js', $params );
	}

	return $posts;
}

?>