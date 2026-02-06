<?php
function dk_speakout_upgrade_button() {
    $upgrade_url = site_url( '/wp-admin/admin.php?page=dk_speakout_upgrade' );
    $button_text = __( 'Upgrade to Pro', 'speakout' );
    echo '<a href="' . esc_url( $upgrade_url ) . '" class="button-secondary upgrade-button">' . esc_html( $button_text ) . '</a>';
}
?>