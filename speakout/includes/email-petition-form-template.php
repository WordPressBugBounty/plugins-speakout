<?php if ( ! $expired ) : ?>

	<!-- SpeakOut! Email Petitions <?php echo esc_html( $dk_speakout_version ) . esc_html( $showPro ) . ' : ' . esc_html( ucfirst( $lang ) ); ?> -->
	<?php
	// Hidden fields for required custom fields (for JS validation)
	for ( $i = 1; $i <= 9; $i++ ) {
		$required_key = ( $i === 1 ) ? 'custom_field_required' : "custom_field{$i}_required";
		if ( ! empty( $petition->{$required_key} ) ) {
			echo '<p id="dk-speakout-custom' . absint( $i ) . '-required" style="display:none;" />';
		}
	}
	?>
	<div id="dk-speakout-windowshade"></div>
	<div class="dk-speakout-petition-wrap <?php echo esc_attr( $css_classes ); ?>" id="dk-speakout-petition-<?php echo absint( $petition->id ); ?>" <?php echo $width; ?>>
		<h3><?php echo esc_html( $petition->title ); ?></h3>

		<?php if ( $petition->display_petition_message == 1 ) : ?>
			<a id="dk-speakout-readme-<?php echo absint( $petition->id ); ?>" class="dk-speakout-readme" rel="<?php echo absint( $petition->id ); ?>" style="display: none;"><span><?php echo esc_html( $petitionReadTitle ); ?></span></a>
		<?php endif; ?>

		<div id="dk-speakout-form-wrap">
			<form class="dk-speakout-petition">
				<?php wp_nonce_field( 'dk_speakout_sendmail_nonce', 'security', true, true ); ?>
				<input type="hidden" id="dk-speakout-posttitle-<?php echo absint( $petition->id ); ?>" value="<?php echo esc_attr( $petition->title ); ?>" />
				<input type="hidden" id="dk-speakout-tweet-<?php echo absint( $petition->id ); ?>" value="<?php echo esc_attr( dk_speakout_SpeakOut::x_encode( $petition->x_message ) ); ?>" />
				<input type="hidden" id="dk-speakout-lang-<?php echo absint( $petition->id ); ?>" value="<?php echo esc_attr( $wpml_lang ); ?>" />
				<input type="hidden" id="dk-speakout-textval-<?php echo absint( $petition->id ); ?>" value="val" />
				<input type="hidden" id="dk-speakout-petition-fade-<?php echo absint( $petition->id ); ?>" value="<?php echo esc_attr( $options["petition_fade"] ); ?>" />
				<input type="hidden" id="dk-speakout-requires_confirmation-<?php echo absint( $petition->id ); ?>" value="<?php echo esc_attr( $petition->requires_confirmation ); ?>" />
				<input type="hidden" id="dk-speakout-hide-email-field-<?php echo absint( $petition->id ); ?>" value="<?php echo esc_attr( $petition->hide_email_field ); ?>" />

				<?php if ( $petition->redirect_url_option == 1 && $petition->redirect_url > "" ) : ?>
					<input type="hidden" id="dk-speakout-url-target-<?php echo absint( $petition->id ); ?>" value="<?php echo esc_attr( $petition->url_target ); ?>" />
					<input type="hidden" id="dk-speakout-redirect-url-<?php echo absint( $petition->id ); ?>" value="<?php echo esc_url( $petition->redirect_url ); ?>" />
					<input type="hidden" id="dk-speakout-redirect-delay-<?php echo absint( $petition->id ); ?>" value="<?php echo esc_attr( $petition->redirect_delay ); ?>" />
				<?php endif; ?>

				<?php echo dk_speakout_render_custom_fields( $petition, 0 ); ?>
				<?php echo dk_speakout_render_custom_fields( $petition, 1 ); ?>

				<?php if ( $options['display_honorific'] == 'enabled' ) : ?>
					<div class="dk-speakout-full">
						<select name="dk-speakout-honorific" id="dk-speakout-honorific-<?php echo absint( $petition->id ); ?>">
							<?php echo $honorifics; ?>
						</select>
					</div>
				<?php endif; ?>

				<div class="dk-speakout-full">
					<input autocomplete="given-name" name="dk-speakout-first-name" id="dk-speakout-first-name-<?php echo absint( $petition->id ); ?>" type="text" placeholder="<?php echo esc_attr__( 'First Name', 'speakout' ); ?>" required="required"  />
				</div>
				
				<div class="dk-speakout-full">
					<input autocomplete="family-name" name="dk-speakout-last-name" id="dk-speakout-last-name-<?php echo absint( $petition->id ); ?>" type="text" placeholder="<?php echo esc_attr__( 'Last Name', 'speakout' ); ?>" required="required"  />
				</div>

				<?php echo dk_speakout_render_custom_fields( $petition, 2 ); ?>

				<?php if ( $petition->hide_email_field != 1 ) : ?>
					<div class="dk-speakout-full">
						<input autocomplete="email" name="dk-speakout-email" id="dk-speakout-email-<?php echo absint( $petition->id ); ?>" type="email"  placeholder="<?php echo esc_attr__( 'Email', 'speakout' ); ?>" required="required"  />
					</div>
				<?php endif; ?>

				<?php if ( in_array( 'street', $petition->address_fields ) ) :
					$required = $petition->street_required == 1 ? " required='required' " : ""; ?>
					<div class="dk-speakout-full">
						<input  autocomplete="address-line1" name="dk-speakout-street" id="dk-speakout-street-<?php echo absint( $petition->id ); ?>" maxlength="200" type="text"  placeholder="<?php echo esc_attr__( 'Street', 'speakout' ); ?>" <?php echo $required; ?> />
					</div>
				<?php endif; ?>
				<div>

				<?php if ( in_array( 'postcode', $petition->address_fields ) && $options['eu_postalcode'] == 'enabled' ) :
					$required = $petition->postcode_required == 1 ? ' required="required" ' : ''; ?>
					<div class="dk-speakout-half">
						<input  autocomplete="postal-code" name="dk-speakout-postcode" id="dk-speakout-postcode-<?php echo absint( $petition->id ); ?>" maxlength="200" type="text"  placeholder="<?php echo esc_attr__( 'Postal Code', 'speakout' ); ?>" <?php echo $required; ?>/>
					</div>
				<?php endif; ?>

				<?php if ( in_array( 'city', $petition->address_fields ) ) :
					$required = $petition->city_required == 1 ? ' required="required" ' : ''; ?>
					<div class="dk-speakout-half">
						<input  autocomplete="address-level2" name="dk-speakout-city" id="dk-speakout-city-<?php echo absint( $petition->id ); ?>" maxlength="200" type="text" placeholder="<?php echo esc_attr__( 'City', 'speakout' ); ?>" <?php echo $required; ?> />
					</div>
				<?php endif; ?>

				<?php if ( in_array( 'state', $petition->address_fields ) ) :
					$required = $petition->state_required == 1 ? ' required="required" ' : ''; ?>
					<div class="dk-speakout-half">
						<input  autocomplete="address-level1" name="dk-speakout-state" id="dk-speakout-state-<?php echo absint( $petition->id ); ?>" maxlength="200" type="text" list="dk-speakout-states"  placeholder="<?php echo esc_attr__( 'State / Province', 'speakout' ); ?>" <?php echo $required; ?> />
					</div>
				<?php endif; ?>

				<?php if ( in_array( 'postcode', $petition->address_fields ) && $options['eu_postalcode'] != 'enabled' ) :
					$required = $petition->postcode_required == 1 ? ' required="required" ' : ''; ?>
					<div class="dk-speakout-half">
						<input  autocomplete="postal-code" name="dk-speakout-postcode" id="dk-speakout-postcode-<?php echo absint( $petition->id ); ?>" maxlength="200" type="text"  placeholder="<?php echo esc_attr__( 'Postal Code', 'speakout' ); ?>" <?php echo $required; ?>/>
					</div>
				<?php endif; ?>

				<?php if ( in_array( 'country', $petition->address_fields ) ) :
					$required  = $petition->country_required == 1 ? ' required="required" ' : ''; ?>
					<div class="dk-speakout-half">
						<select name="dk-speakout-country"   id="dk-speakout-country-<?php echo absint( $petition->id ); ?>" <?php echo $required; ?> />
							<option value=""><?php echo esc_html__( 'Country', 'speakout' ); ?></option>
							<?php echo $countries; ?>
						</select>
					</div>
				<?php endif; ?>

				<?php echo dk_speakout_render_custom_fields( $petition, 3 ); ?>

				</div>

				<?php if ( $petition->is_editable == 1 && $petition->display_petition_message == 1 ) : ?>
					<div class="dk-speakout-full dk-speakout-message-editable" id="dk-speakout-message-editable-<?php echo absint( $petition->id ); ?>">
						<p class="dk-speakout-greeting"><?php echo esc_html( $petition->greeting ); ?></p>
						<textarea name="dk-speakout-message" class="dk-speakout-message-<?php echo absint( $petition->id ); ?>" <?php echo $height; ?> rows="8"><?php echo wp_kses( $petition->petition_message, $kses_array ); ?></textarea>
						<div id='dk_speakout_markdown'><?php echo esc_html__( 'You can add formatting using markdown syntax' ); ?> - <a href='https://www.markdownguide.org/basic-syntax/' target='_blank'><?php echo esc_html__( "read more" ); ?></a></div>
						<?php if ( $petition->petition_footer != '' ) : ?>
							<br><br><?php echo wp_kses_post( $petition->petition_footer ); ?>
						<?php endif; ?>
					</div>
				<?php elseif ( $petition->display_petition_message == 1 ) : ?>
					<div class="dk-speakout-full dk-speakout-message" <?php echo $height; ?> id="dk-speakout-message-<?php echo absint( $petition->id ); ?>">
						<p class="dk-speakout-greeting"><?php echo esc_html( $petition->greeting ); ?></p>
						<?php echo wp_kses_post( $Parsedown->text( wp_kses( $petition->petition_message, $kses_array ) ) ); ?>
						
						<?php if ( $petition->petition_footer != '' ) : ?>
							<br><br><?php echo wp_kses_post( $petition->petition_footer ); ?>
						<?php endif; ?>
					</div>
				<?php endif; ?>

				<?php if ( $petition->displays_optin == 1 && $petition->hide_email_field != 1 ) : 
					$optin_default = ( $options['optin_default'] == 'checked' ) ? ' checked="checked"' : ''; ?>
					<div class="dk-speakout-optin-wrap" >
						<div class="dk-speakout-optin-checkbox">
							<input type="checkbox" name="dk-speakout-optin"  id="dk-speakout-optin-<?php echo absint( $petition->id ); ?>"<?php echo $optin_default; ?> />
							<label for="dk-speakout-optin-<?php echo absint( $petition->id ); ?>" class="dk-speakout-options"><?php echo esc_html( $petition->optin_label ); ?></label>
						</div>
					</div>
				<?php endif; ?>

				<?php if ( $options['display_bcc'] == 'enabled' && $petition->hide_email_field != 1 ) : ?>
					<div class="dk-speakout-bcc-wrap">
						<div class="dk-speakout-options-checkbox">
							<input type="checkbox" name="dk-speakout-bcc" id="dk-speakout-bcc-<?php echo absint( $petition->id ); ?>" checked="checked" />
							<label for="dk-speakout-bcc-<?php echo absint( $petition->id ); ?>" class="dk-speakout-options"><?php echo esc_html__( 'BCC yourself', 'speakout' ); ?> </label>
						</div>
					</div>
				<?php endif; ?>

				<?php if ( $petition->allow_anonymous == 1 ) : ?>
					<div class="dk-speakout-anonymise-wrap">
						<div class="dk-speakout-options-checkbox">
							<input type="checkbox" name="dk-speakout-anonymise" id="dk-speakout-anonymise-<?php echo absint( $petition->id ); ?>" value="1" />
							<label for="dk-speakout-anonymise-<?php echo absint( $petition->id ); ?>" class="dk-speakout-options"><?php echo esc_html__( 'Hide name from public', 'speakout' ); ?> </label>
						</div>
					</div>
				<?php endif; ?>

				<?php if ( $options['display_privacypolicy'] == 'enabled' ) : ?>
					<div class="dk-speakout-privacypolicy-wrap">
						<div class="dk-speakout-options-checkbox">
							<input type="checkbox" name="dk-speakout-privacypolicy" id="dk-speakout-privacypolicy-<?php echo absint( $petition->id ); ?>" class="required" required="required" />
							<label for="dk-speakout-privacypolicy-<?php echo absint( $petition->id ); ?>" class="required dk-speakout-options"><?php echo esc_html__( 'Yes, I accept your ', 'speakout' ); ?><a href="<?php echo esc_url( $options['privacypolicy_url'] ); ?>" target="_blank"><?php echo esc_html__( 'privacy policy', 'speakout' ); ?></a></label>
						</div>
					</div>
				<?php endif; ?>

				<?php if ( isset( $options['g_recaptcha_status'] ) && $options['g_recaptcha_status'] == 'on' && $options['g_recaptcha_version'] != 3 ) : ?>
					<div class="dk-speakout-recaptcha">
						<div class="g-recaptcha" data-sitekey="<?php echo esc_attr( $options["g_recaptcha_site_key"] ); ?>"></div>
						<br/>
					</div>
				<?php endif; ?>

				<?php if ( isset( $options['hcaptcha_status'] ) && $options['hcaptcha_status'] == 'on' ) : ?>
					<div class="dk-speakout-hcaptcha">
						<div class="h-captcha" data-sitekey="<?php echo esc_attr( $options["hcaptcha_site_key"] ); ?>"></div>
						<br/>
					</div>
				<?php endif; ?>

				<div class="dk-speakout-submit-wrap">
					<div id="dk-speakout-ajaxloader-<?php echo absint( $petition->id ); ?>" class="dk-speakout-ajaxloader" style="visibility: hidden;">&nbsp;</div>
					<button name="<?php echo absint( $petition->id ); ?>" class="dk-speakout-submit"><?php echo esc_html( $options['button_text'] ); ?></button>
				</div>
			</form>
		</div>
		<div class="dk-speakout-response"></div>

		<?php if ( $options['display_count'] == 1 ) : ?>
			<div class="dk-speakout-progress-wrap">
				<div class="dk-speakout-signature-count">
					<span><?php echo esc_html( number_format( $petition->signatures, 0, $options['decimal_separator'], $options['thousands_separator'] ) ); ?></span> <?php echo esc_html__( 'signatures', 'speakout' ); ?> <?php echo esc_html( $goal_text ); ?>
				</div>
				<?php if ( $petition->goal != 0 ) : ?>
					<div class="dk-speakout-count">0<?php echo dk_speakout_SpeakOut::progress_bar( $petition->goal, $petition->signatures, $progress_width ); ?> <?php echo esc_html( number_format( $petition->goal ) ); ?></div>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<?php if ( $options['display_sharing'] == "enabled" || $options['display_sharing'] == "on" ) : ?>
			<div class="dk-speakout-share">
				<div>
					<p><?php echo esc_html( $options['share_message'] ); ?></p>
					<p>
						<a class="dk-speakout-facebook" href="#" title="Facebook" rel="<?php echo absint( $petition->id ); ?>"></a>
						<a class="dk-speakout-email"  target="_blank" href="<?php echo esc_attr( $mailto_href ); ?>" title="Share by Email"></a>
						<a class="dk-speakout-x" href="#" title="X" rel="<?php echo absint( $petition->id ); ?>"></a>
					</p>
				</div>
				<div class="dk-speakout-clear"></div>
			</div>
		<?php endif; ?>
	</div>

<?php else : // if expired ?>

	<div class="dk-speakout-petition-wrap dk-speakout-expired" id="dk-speakout-petition-<?php echo absint( $petition->id ); ?>">
		<h3><?php echo esc_html( $petition->title ); ?></h3>
		<p><?php echo esc_html( $options['expiration_message'] ); ?></p>
		<p><strong><?php echo esc_html__( 'End date', 'speakout' ); ?>:</strong> <?php echo esc_html( date( 'M d, Y', strtotime( $petition->expiration_date ) ) ); ?></p>
		<p><strong><?php echo esc_html__( 'Signatures collected', 'speakout' ); ?>:</strong> <?php echo esc_html( number_format( $petition->signatures, 0, $options['decimal_separator'], $options['thousands_separator'] ) ); ?></p>
		<?php echo $goal_text; ?>
		<div class="dk-speakout-progress-wrap">
			<div class="dk-speakout-signature-count">
				<span><?php echo esc_html( number_format( $petition->signatures, 0, $options['decimal_separator'], $options['thousands_separator'] ) ); ?></span> 
				<?php echo _n( 'signature', 'signatures', esc_html( number_format( $petition->signatures, 0, $options['decimal_separator'], $options['thousands_separator'] ) ), 'speakout' ) . esc_html( $goal_text ); ?>
			</div>
			<?php echo dk_speakout_SpeakOut::progress_bar( $petition->goal, $petition->signatures, $progress_width ); ?>
		</div>
	</div>

<?php endif; ?>
