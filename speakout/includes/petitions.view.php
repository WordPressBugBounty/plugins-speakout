<?php // Pro ?>
<div class="wrap" id="dk-speakout">	
	<div id="icon-dk-speakout" class="icon32"><br /></div>	
	<h2><?php echo esc_html( $page_title ); ?> <a href="<?php echo esc_url( $addnew_url ); ?>" class="add-new-h2"><?php _e( 'Add New', 'speakout' ); ?></a></h2>
	<?php if ( $message_update ) echo '<div id="message" class="updated"><p>' . esc_html( $message_update ) . '</p></div>' ?>
	<div class="tablenav">		
		<ul class='subsubsub'>			
			<li class='table-label'><?php _e( 'All Petitions', 'speakout' ); ?> <span class="count">(<?php echo absint( $count ); ?>)</span></li>		
		</ul>		
		<?php echo dk_speakout_SpeakOut::pagination( $query_limit, $count, 'speakout', $current_page, $base_url, true ); ?>	
	</div>
	<table class="widefat">		
		<thead>			
			<tr>
				<th><?php _e( 'ID', 'speakout' ); ?></th>
				<th><?php _e( 'Petition name', 'speakout' ); ?></th>
				<th><?php _e( 'Shortcodes', 'speakout' ); ?></th>
				<th class="dk-speakout-right"><?php _e( 'Signatures', 'speakout' ); ?></th>
				<th class="dk-speakout-right"><?php _e( 'Goal', 'speakout' ); ?></th>
				<th></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th><?php _e( 'ID', 'speakout' ); ?></th>
				<th><?php _e( 'Petition name', 'speakout' ); ?></th>
				<th><?php _e( 'Shortcodes', 'speakout' ); ?></th>
				<th class="dk-speakout-right"><?php _e( 'Signatures', 'speakout' ); ?></th>
				<th class="dk-speakout-right"><?php _e( 'Goal', 'speakout' ); ?></th>
				<th></th>
			</tr>
		</tfoot>
		<tbody>		
	<?php if ( $count == 0 ) echo '<tr><td colspan="5">' . __( "No petitions found.", "speakout" ) . ' </td></tr>'; ?>		
			<?php foreach ( $petitions as $petition ) : ?>			
			<?php
			$edit_url       = esc_url( wp_nonce_url( admin_url( 'admin.php?page=dk_speakout_addnew_page&action=edit&id=' . $petition->id ), 'dk_speakout-edit_petition' . $petition->id ) );
			$delete_url     = esc_url( wp_nonce_url( admin_url( 'admin.php?page=dk_speakout_top&action=delete&id=' . $petition->id ), 'dk_speakout-delete_petition' . $petition->id ) );
			$duplicate_url  = esc_url( wp_nonce_url( admin_url( 'admin.php?page=dk_speakout_addnew_page&action=duplicate&id=' . $petition->id ), 'dk_speakout-edit_petition' . $petition->id ) );
			$signatures_url = esc_url( admin_url( 'admin.php?page=dk_speakout_signatures&action=petition&pid=' . $petition->id ) );
			?>
			<tr class="dk-speakout-tablerow">				
				<td><a class="row-title" href="<?php echo $edit_url; ?>"><?php echo esc_html( $petition->id ); ?></a></td>				
				<td><a class="row-title" href="<?php echo $edit_url; ?>"><?php echo esc_html( $petition->title ); ?></a>
					<div class="row-actions">
						<span class="edit"><a href="<?php echo $edit_url; ?>"><?php _e( 'Edit', 'speakout' ); ?></a> | </span>
						<span><a href="<?php echo $delete_url; ?>" class="dk-speakout-delete-petition"><?php _e( 'Delete', 'speakout' ); ?></a> | </span>
						<span class="edit"><a href="<?php echo $duplicate_url; ?>"><?php _e( 'Duplicate', 'speakout' ); ?></a></span>
					</div>
				</td>
				<td>
					[emailpetition id="<?php echo esc_html( $petition->id ); ?>"] - <?php _e( 'display petition', 'speakout' ); ?><br />
					[signaturelist id="<?php echo esc_html( $petition->id ); ?>"] - <?php _e( 'display signature list', 'speakout' ); ?><br />
					[signaturecount id="<?php echo esc_html( $petition->id ); ?>"] - <?php _e( 'display signature count', 'speakout' ); ?><br />
					[signaturegoal id="<?php echo esc_html( $petition->id ); ?>"] - <?php _e( 'display signature goal', 'speakout' ); ?><br />
					[petitiontitle id="<?php echo esc_html( $petition->id ); ?>"] - <?php _e( 'display petition title', 'speakout' ); ?><br />
					[petitionmessage id="<?php echo esc_html( $petition->id ); ?>"] - <?php _e( 'display petition message', 'speakout' ); ?>
				</td>
				<td class="dk-speakout-right"><?php echo number_format( $petition->signatures ); ?></td>
				<td class="dk-speakout-right">
					<?php echo number_format( $petition->goal ); ?>
					<div class="dk_speakout_clear"></div>
					<?php echo dk_speakout_SpeakOut::progress_bar( $petition->goal, $petition->signatures, 65 ); ?>
				</td>
				<td class="dk-speakout-right" style="vertical-align: middle"><a class="button" href="<?php echo esc_url( $signatures_url ); ?>"><?php _e( 'View Signatures', 'speakout' ); ?></a></td>
			</tr>
			<?php endforeach; ?>		
		</tbody>	
	</table>
	<div class="tablenav">		<?php echo dk_speakout_SpeakOut::pagination( $query_limit, $count, 'speakout', $current_page, $base_url, false ); ?>	</div>
	<div id="dk-speakout-delete-confirmation" class="dk-speakout-hidden"><?php _e( 'Delete this petition permanently? All of the petition\'s signatures will be deleted as well.', 'speakout' ); ?></div></div>
