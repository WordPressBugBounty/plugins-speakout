<?php
include_once( 'class.signature.php' );
include_once( 'class.petition.php' );
include_once( 'class.mail.php' );
include_once( 'class.wpml.php' );

// capture confirmation_code variable from links clicked in confirmation emails
if ( isset( $_REQUEST['dkspeakoutconfirm'] ) ) {
	add_action( 'template_redirect', 'dk_speakout_confirm_email' );
}

/**
 * Displays the confirmation page
 */
function dk_speakout_confirm_email() {

	// set WPML language
	global $sitepress;
	$lang = isset( $_REQUEST['lang'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['lang'] ) ) : '';

	if ( isset( $sitepress ) ) {
		$sitepress->switch_lang( $lang, true );
	}

	$the_signature = new dk_speakout_Signature();
	$the_petition  = new dk_speakout_Petition();
	$options = get_option( 'dk_speakout_options' );
	$wpml          = new dk_speakout_WPML();

	// get the confirmation code from url
	$confirmation_code = sanitize_text_field( wp_unslash( $_REQUEST['dkspeakoutconfirm'] ) );

	// try to confirm the signature
	$try_confirm = $the_signature->confirm( $confirmation_code );

	// retrieve the petition data
    $the_petition->retrieve( $the_signature->petitions_id );
    $wpml->translate_petition( $the_petition );

    //set our return URL depending on whether option has been filled
    $returnURL = ! empty( $the_petition->return_url ) ? esc_url( $the_petition->return_url ) : esc_url( site_url() );

	// if our attempt to confirm the signature was successful
	if ( $try_confirm ) {

		// send the petition email.  Querystring value is whether or not to BCC, called from confirmations.php
		if ( $the_petition->sends_email ) {
			$bcc_sender = isset( $_GET['b'] ) ? sanitize_text_field( $_GET['b'] ) : '';
			dk_speakout_Mail::send_petition( $the_petition, $the_signature, $bcc_sender );
		}

		// set up the status message
		$message = __( 'Thank you. Your signature has been added to the petition.', 'speakout' );
		
        if ( $options['webhooks'] == 'on' ) {
            $id = absint( $the_petition->id );
			$title = sanitize_text_field( $the_petition->title );
			$email = sanitize_email( $the_signature->email );
			$firstName = sanitize_text_field( $the_signature->first_name );
			$lastName = sanitize_text_field( $the_signature->last_name );
			$activecampaignList = sanitize_text_field( $the_petition->activecampaign_list_id );
			$mailchimpList = sanitize_text_field( $the_petition->mailchimp_list_id );
			$mailerliteGroup = sanitize_text_field( $the_petition->mailerlite_group_id );
			$sendyList = sanitize_text_field( $the_petition->sendy_list_id );
		    do_action( 'speakout_signature_confirmed', $id, $title, $email, $firstName, $lastName, $activecampaignList, $mailchimpList, $mailerliteGroup, $sendyList);
		}
	}
	else {

		// has the signature already been confirmed?
		if ( $the_signature->check_confirmation( $confirmation_code ) ) {
			$message = __( 'Your signature has already been confirmed.', 'speakout' );
		}
		else {
			// the confirmation code is fubar or an admin has already deleted the signature
			$message = __( 'The confirmation code you provided is invalid.', 'speakout' );
		}
	}

	// display the confirmation page
	$confirmation_page = '
		<!doctype html>
		<html>
		<head>
			<meta http-equiv="Content-Type" content="text/html; charset=' . esc_attr( get_bloginfo( "charset" ) ) . '" />
			';
	//if we don't have a confirmation URL don't attempt to redirect
	$confirmation_page .= 	'<meta http-equiv="refresh" content="2;' . esc_url( $returnURL ) . '">';
//	}
	
	$confirmation_page .= 	'<title>' . esc_html( get_bloginfo( "name" ) ) . '</title>
			<style type="text/css">
				body {
					background: #666;
					font-family: arial, sans-serif;
					font-size: 14px;
				}
				#confirmation {
					background: #fff url(' . esc_url( plugins_url( "speakout/images/mail-stripes.png" ) ) . ') repeat top left;
					border: 1px solid #fff;
					width: 515px;
					margin: 200px auto 0 auto;
					box-shadow: 0px 3px 5px #333;
				}
				#confirmation-content {
					background: #fff url(' . esc_url( plugins_url( "speakout/images/postmark.png" ) ) . ') no-repeat top right;
					margin: 10px;
					padding: 40px 0 20px 100px;
				}
			</style>
		</head>
		<body>
			<div id="confirmation">
				<div id="confirmation-content">
					<h2>' . esc_html__( "Email Confirmation", "speakout" ) . '</h2>
					<p>' . esc_html( $message ) . '</p>';
			$confirmation_page .= 	'<p>' . esc_html__( "If you aren't redirected", "speakout" ) . ' <a href="' . esc_url( $returnURL ) . '">' . esc_html__( "Click here", "speakout") . '</a> ' . esc_html__( "to return to site ", "speakout" )  . '</p>';

				$confirmation_page .= 	'</div>
			</div>
		</body>
		</html>
	';

	echo $confirmation_page;

	// stop page rendering here
	// without this, the home page will display below the confirmation message
	die();
}

?>