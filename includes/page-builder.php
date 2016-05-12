<?php
/**
 * Page Builder
 * - Register Page Template
 * - Add Page Builder Control
 * - Save Page Builder Data
 * - Admin Scripts
 * 
 * @since 1.0.0
 * @author David Chandra Purnama <david@genbumedia.com>
 * @copyright Copyright (c) 2016, Genbu Media
**/

/* === REGISTER PAGE TEMPLATE === */

/* Add page templates */
add_filter( 'theme_page_templates', 'fx_pbbase_register_page_template' );

/**
 * Register Page Template: Page Builder
 * @since 1.0.0
 */
function fx_pbbase_register_page_template( $templates ){
	$templates['templates/page-builder.php'] = 'Page Builder';
	return $templates;
}


/* === ADD PAGE BUILDER CONTROL === */

/* Add page builder form after editor */
add_action( 'edit_form_after_editor', 'fx_pbbase_editor_callback', 10, 2 );

/**
 * Page Builder Control
 * Added after Content Editor in Page Edit Screen.
 * @since 1.0.0
 */
function fx_pbbase_editor_callback( $post ){
	if( 'page' !== $post->post_type ){
		return;
	}
?>
	<div id="fx-page-builder">

		<div class="fxpb-rows">
			<?php fxpb_render_rows( $post ); // display saved rows ?>
		</div><!-- .fxpb-rows -->

		<div class="fxpb-actions">
			<a href="#" class="fxpb-add-row button-primary button-large" data-template="col-1">Add 1 Column</a>
			<a href="#" class="fxpb-add-row button-primary button-large" data-template="col-2">Add 2 Columns</a>
		</div><!-- .fxpb-actions -->

		<div class="fxpb-templates" style="display:none;">

			<?php /* == This is the 1 column row template == */ ?>
			<div class="fxpb-row fxpb-col-1">

				<div class="fxpb-row-title">
					<span class="fxpb-handle dashicons dashicons-sort"></span>
					<span class="fxpb-order">0</span>
					<span class="fxpb-row-title-text">1 Column</span>
					<span class="fxpb-remove dashicons dashicons-trash"></span>
				</div><!-- .fxpb-row-title -->

				<div class="fxpb-row-fields">
					<textarea class="fxpb-row-input" name="" data-field="content" placeholder="Add HTML here..."></textarea>
					<input class="fxpb-row-input" type="hidden" name="" data-field="type" value="col-1">
				</div><!-- .fxpb-row-fields -->

			</div><!-- .fxpb-row.fxpb-col-1 -->

			<?php /* == This is the 2 columns row template == */ ?>
			<div class="fxpb-row fxpb-col-2">

				<div class="fxpb-row-title">
					<span class="fxpb-handle dashicons dashicons-sort"></span>
					<span class="fxpb-order">0</span>
					<span class="fxpb-row-title-text">2 Columns</span>
					<span class="fxpb-remove dashicons dashicons-trash"></span>
				</div><!-- .fxpb-row-title -->

				<div class="fxpb-row-fields">
					<div class="fxpb-col-2-left">
						<textarea class="fxpb-row-input" name="" data-field="content-1" placeholder="1st column content here..."></textarea>
					</div><!-- .fxpb-col-2-left -->
					<div class="fxpb-col-2-right">
						<textarea class="fxpb-row-input" name="" data-field="content-2" placeholder="2nd column content here..."></textarea>
					</div><!-- .fxpb-col-2-right -->
					<input class="fxpb-row-input" type="hidden" name="" data-field="type" value="col-2">
				</div><!-- .fxpb-row-fields -->

			</div><!-- .fxpb-row.fxpb-col-2 -->

		</div><!-- .fxpb-templates -->

		<?php wp_nonce_field( "fxpb_nonce_action", "fxpb_nonce" ) ?>

	</div><!-- .fx-page-builder -->
<?php
}


/* === SAVE PAGE BUILDER DATA === */

/* Save post meta on the 'save_post' hook. */
add_action( 'save_post', 'fx_pbbase_save_post', 10, 2 );

/**
 * Save Page Builder Data When Saving Page
 * @since 1.0.0
 */
function fx_pbbase_save_post( $post_id, $post ){

	/* Stripslashes Submitted Data */
	$request = stripslashes_deep( $_POST );

	/* Verify/validate */
	if ( ! isset( $request['fxpb_nonce'] ) || ! wp_verify_nonce( $request['fxpb_nonce'], 'fxpb_nonce_action' ) ){
		return $post_id;
	}
	/* Do not save on autosave */
	if ( defined('DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return $post_id;
	}
	/* Check post type and user caps. */
	$post_type = get_post_type_object( $post->post_type );
	if ( 'page' != $post->post_type || !current_user_can( $post_type->cap->edit_post, $post_id ) ){
		return $post_id;
	}

	/* == Save, Delete, or Update Page Builder Data == */

	/* Get (old) saved page builder data */
	$saved_data = get_post_meta( $post_id, 'fxpb', true );

	/* Get new submitted data and sanitize it. */
	$submitted_data = isset( $request['fxpb'] ) ? fxpb_sanitize( $request['fxpb'] ) : null;

	/* New data submitted, No previous data, create it  */
	if ( $submitted_data && '' == $saved_data ){
		add_post_meta( $post_id, 'fxpb', $submitted_data, true );
	}
	/* New data submitted, but it's different data than previously stored data, update it */
	elseif( $submitted_data && ( $submitted_data != $saved_data ) ){
		update_post_meta( $post_id, 'fxpb', $submitted_data );
	}
	/* New data submitted is empty, but there's old data available, delete it. */
	elseif ( empty( $submitted_data ) && $saved_data ){
		delete_post_meta( $post_id, 'fxpb' );
	}

	/* == Get Selected Page Template == */
	$page_template = isset( $request['page_template'] ) ? esc_attr( $request['page_template'] ) : null;

	/* == Page Builder Template Selected, Save to Post Content == */
	if( 'templates/page-builder.php' == $page_template ){

		/* Page builder content without row/column wrapper */
		$pb_content = fxpb_format_post_content_data( $submitted_data );

		/* Post Data To Save */
		$this_post = array(
			'ID'           => $post_id,
			'post_content' => sanitize_post_field( 'post_content', $pb_content, $post_id, 'db' ),
		);

		/**
		 * Prevent infinite loop.
		 * @link https://developer.wordpress.org/reference/functions/wp_update_post/
		 */
		remove_action( 'save_post', 'fx_pbbase_save_post' );
		wp_update_post( $this_post );
		add_action( 'save_post', 'fx_pbbase_save_post' );
	}

	/* == Always delete page builder data if page template not selected == */
	else{
		delete_post_meta( $post_id, 'fxpb' );
	}
}


/**
 * Format Page Builder Content Without Wrapper Div.
 * This is added to post content.
 * @since 1.0.0
**/
function fxpb_format_post_content_data( $row_datas ){

	/* return if no rows data */
	if( !$row_datas ){
		return '';
	}

	/* Output */
	$content = '';

	/* Loop for each rows */
	foreach( $row_datas as $order => $row_data ){
		$order = intval( $order );

		/* === Row with 1 column === */
		if( 'col-1' == $row_data['type'] ){
			$content .= $row_data['content'] . "\r\n\r\n";
		}
		/* === Row with 2 columns === */
		elseif( 'col-2' == $row_data['type'] ){
			$content .= $row_data['content-1'] . "\r\n\r\n";
			$content .= $row_data['content-2'] . "\r\n\r\n";
		}
	}
	return $content;
}


/**
 * Render Saved Rows
 * @since 1.0.0
 */
function fxpb_render_rows( $post ){

	/* Get saved rows data and sanitize it */
	$row_datas = fxpb_sanitize( get_post_meta( $post->ID, 'fxpb', true ) );

	/* Default Message */
	$default_message = 'Please add row to start!';

	/* return if no rows data */
	if( !$row_datas ){
		echo '<p class="fxpb-rows-message">' . $default_message . '</p>';
		return;
	}
	/* Data available, hide default notice */
	else{
		echo '<p class="fxpb-rows-message" style="display:none;">' . $default_message . '</p>';
	}

	/* Loop for each rows */
	foreach( $row_datas as $order => $row_data ){
		$order = intval( $order );

		/* === Row with 1 column === */
		if( 'col-1' == $row_data['type'] ){
			?>
			<div class="fxpb-row fxpb-col-1">

				<div class="fxpb-row-title">
					<span class="fxpb-handle dashicons dashicons-sort"></span>
					<span class="fxpb-order"><?php echo $order; ?></span>
					<span class="fxpb-row-title-text">1 Column</span>
					<span class="fxpb-remove dashicons dashicons-trash"></span>
				</div><!-- .fxpb-row-title -->

				<div class="fxpb-row-fields">
					<textarea class="fxpb-row-input" name="fxpb[<?php echo $order; ?>][content]" data-field="content" placeholder="Add HTML here..."><?php echo esc_textarea( $row_data['content'] ); ?></textarea>
					<input class="fxpb-row-input" type="hidden" name="fxpb[<?php echo $order; ?>][type]" data-field="type" value="col-1">
				</div><!-- .fxpb-row-fields -->

			</div><!-- .fxpb-row.fxpb-col-1 -->
			<?php
		}
		/* === Row with 2 columns === */
		elseif( 'col-2' == $row_data['type'] ){
			?>
			<div class="fxpb-row fxpb-col-2">

				<div class="fxpb-row-title">
					<span class="fxpb-handle dashicons dashicons-sort"></span>
					<span class="fxpb-order"><?php echo $order; ?></span>
					<span class="fxpb-row-title-text">2 Columns</span>
					<span class="fxpb-remove dashicons dashicons-trash"></span>
				</div><!-- .fxpb-row-title -->

				<div class="fxpb-row-fields">
					<div class="fxpb-col-2-left">
						<textarea class="fxpb-row-input" name="fxpb[<?php echo $order; ?>][content-1]" data-field="content-1" placeholder="1st column content here..."><?php echo esc_textarea( $row_data['content-1'] ); ?></textarea>
					</div><!-- .fxpb-col-2-left -->
					<div class="fxpb-col-2-right">
						<textarea class="fxpb-row-input" name="fxpb[<?php echo $order; ?>][content-2]" data-field="content-2" placeholder="2nd column content here..."><?php echo esc_textarea( $row_data['content-2'] ); ?></textarea>
					</div><!-- .fxpb-col-2-right -->
					<input class="fxpb-row-input" type="hidden" name="fxpb[<?php echo $order; ?>][type]" data-field="type" value="col-2">
				</div><!-- .fxpb-row-fields -->

			</div><!-- .fxpb-row.fxpb-col-2 -->
			<?php
		}
	}
}


/* === ADMIN SCRIPTS === */

/* Admin Script */
add_action( 'admin_enqueue_scripts', 'fx_pbbase_admin_scripts' );

/**
 * Admin Scripts
 * @since 1.0.0
 */
function fx_pbbase_admin_scripts( $hook_suffix ){
	global $post_type;

	/* In Page Edit Screen */
	if( 'page' == $post_type && in_array( $hook_suffix, array( 'post.php', 'post-new.php' ) ) ){

		/* Load Editor/Page Builder Toggle Script */
		wp_enqueue_script( 'fx-pbbase-admin-editor-toggle', FX_PBBASE_URI . 'assets/admin-editor-toggle.js', array( 'jquery' ), FX_PBBASE_VERSION );

		/* Enqueue CSS & JS For Page Builder */
		wp_enqueue_style( 'fx-pbbase-admin', FX_PBBASE_URI. 'assets/admin-page-builder.css', array(), FX_PBBASE_VERSION );
		wp_enqueue_script( 'fx-pbbase-admin', FX_PBBASE_URI. 'assets/admin-page-builder.js', array( 'jquery', 'jquery-ui-sortable' ), FX_PBBASE_VERSION, true );
	}
}


