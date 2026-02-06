<?php

// create admin menus
add_action( 'admin_menu', 'dk_speakout_create_menus' );

function dk_speakout_create_menus() {

	// load sidebar menus
	$petitions = array(
		'page_title' => __( 'SpeakOut! Email Petitions', 'speakout' ),
		'menu_title' => 'SpeakOut!' ,
		'capability' => 'manage_options',
		'menu_slug'  => 'dk_speakout_top',
		'function'   => 'dk_speakout_petitions_page',
		'icon_url'   => 'dashicons-megaphone'
	);
	$petitions_page = add_menu_page( $petitions['page_title'], $petitions['menu_title'], $petitions['capability'], $petitions['menu_slug'], $petitions['function'], $petitions['icon_url'] );
	
$petition = array(
	    'parent_slug' => 'dk_speakout_top',
		'page_title' => __( 'SpeakOut! Email Petitions', 'speakout' ),
		'menu_title' => __( 'Shortcodes', 'speakout' ),
		'capability' => 'manage_options',
		'menu_slug'  => 'dk_speakout_top',
		'function'   => 'dk_speakout_petitions_page',
		'position'  => 1
	);
	$petition_page = add_submenu_page( $petition['parent_slug'], $petition['page_title'], $petition['menu_title'], $petition['capability'], $petition['menu_slug'], $petition['function'] );	
		
	$addnew = array(
		'parent_slug' => 'dk_speakout_top',
		'page_title'  => __( 'Edit petition', 'speakout' ),
		'menu_title'  => __( 'Edit petition', 'speakout' ),
		'capability'  => 'publish_posts',
		'menu_slug'   => 'dk_speakout_addnew_page',
		'function'    => 'dk_speakout_addnew_page',
		'position'  => 2
	);
	$addnew_page = add_submenu_page( $addnew['parent_slug'], $addnew['page_title'], $addnew['menu_title'], $addnew['capability'], $addnew['menu_slug'], $addnew['function'] );
	

	$signatures = array(
		'parent_slug' => 'dk_speakout_top',
		'page_title'  => __( 'Signatures', 'speakout' ),
		'menu_title'  => __( 'Signatures', 'speakout' ),
		'capability'  => 'manage_options',
		'menu_slug'   => 'dk_speakout_signatures',
		'function'    => 'dk_speakout_signatures_page',
		'position'  => 3
	);
	$signatures_page = add_submenu_page( $signatures['parent_slug'], $signatures['page_title'], $signatures['menu_title'], $signatures['capability'], $signatures['menu_slug'], $signatures['function'] );

	$settings = array(
		'parent_slug' => 'dk_speakout_top',
		'page_title'  => __( 'SpeakOut! Petitions Settings', 'speakout' ),
		'menu_title'  => __( 'Settings', 'speakout' ),
		'capability'  => 'manage_options',
		'menu_slug'   => 'dk_speakout_settings',
		'function'    => 'dk_speakout_settings_page',
		'position'  => 4
	);
    $settings_page = add_submenu_page( $settings['parent_slug'], $settings['page_title'], $settings['menu_title'], $settings['capability'], $settings['menu_slug'], $settings['function']);
    

	$upgrade = array(
		'parent_slug' => 'dk_speakout_top',
		'page_title'  => "<span style='color:red;'>" . __( 'SpeakOut! Upgrade', 'speakout' ) . "</span>",
		'menu_title'  => "<span style='color:red;'>" . __( 'Upgrade to Pro', 'speakout' ) . "</span>",
		'capability'  => 'manage_options',
		'menu_slug'   => 'dk_speakout_upgrade',
		'function'    => "dk_speakout_upgrade_page",
		'position'  => 6
	);

	$upgrade_page = add_submenu_page( $upgrade['parent_slug'], $upgrade['page_title'], $upgrade['menu_title'], $upgrade['capability'], $upgrade['menu_slug'], $upgrade['function'] );


// load contextual help tabs for newer WordPress installs (requires 3.3.1)
	if ( version_compare( get_bloginfo( 'version' ), '3.3', '>' ) == 1 ) {
		add_action( 'load-' . $addnew_page, 'dk_speakout_help_addnew' );
		add_action( 'load-' . $settings_page, 'dk_speakout_help_settings' );
	}
}

// display custom menu icon

add_action( 'admin_head', 'dk_speakout_menu_icon' );

function dk_speakout_menu_icon() {
	echo '
		<style type="text/css">
			#toplevel_page_dk_speakout .wp-menu-image {
				background: url(' . plugins_url( "speakout/images/icon-emailpetitions-16.png" ) . ') no-repeat 6px 7px !important;
			}
			body.admin-color-classic #toplevel_page_dk_speakout .wp-menu-image {
				background: url(' . plugins_url( "speakout/images/icon-emailpetitions-16.png" ) . ') no-repeat 6px -41px !important;
			}
			#toplevel_page_dk_speakout:hover .wp-menu-image, #toplevel_page_dk_speakout.wp-has-current-submenu .wp-menu-image {
				background-position: 6px -17px !important;
			}
			body.admin-color-classic #toplevel_page_dk_speakout:hover .wp-menu-image, body.admin-color-classic #toplevel_page_dk_speakout.wp-has-current-submenu .wp-menu-image {
				background-position: 6px -17px !important;
			}
		</style>
	';
}

// load JavaScript and CSS for use on admin pages
add_action( 'admin_enqueue_scripts', 'dk_speakout_admin_enqueue_scripts' );

function dk_speakout_admin_enqueue_scripts( $hook ) {
	global $dk_speakout_version;

	// Only load on our admin pages
	if ( strpos( $hook, 'dk_speakout' ) === false ) {
		return;
	}

	wp_enqueue_style( 'dk_speakout_admin_css', plugins_url( 'speakout/css/admin.css' ) ,"", $dk_speakout_version );
	wp_enqueue_script( 'dk_speakout_admin_js', plugins_url( 'speakout/js/admin.js' ), array( 'jquery' ) , $dk_speakout_version, true );

	// Localize the script with new data
	$translation_array = array(
		'show_tips' => __( 'Show SpeakOut! Tips', 'speakout' ),
		'hide_tips' => __( 'Hide SpeakOut! Tips', 'speakout' ),
		'ajaxurl'   => admin_url( 'admin-ajax.php' ),
		'nonce'     => wp_create_nonce( 'dk_speakout_ajax_nonce' )
	);
	wp_localize_script( 'dk_speakout_admin_js', 'dk_speakout_js', $translation_array );
}
?>