<?php
/**
 * PAGE BUILDER
 * - Register Page Template
 * - Add Page Builder Control
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
			<p class="fxpb-rows-message">This is where we manage rows.</p>
		</div><!-- .fxpb-rows -->

		<div class="fxpb-actions">
			<a href="#" class="fxpb-add-row button-primary button-large" data-template="col-1">Add 1 Column</a>
			<a href="#" class="fxpb-add-row button-primary button-large" data-template="col-2">Add 2 Columns</a>
		</div><!-- .fxpb-actions -->

		<div class="fxpb-templates">

			<?php /* == This is the 1 column row template == */ ?>
			<div class="fxpb-row fxpb-col-1">

				<div class="fxpb-row-title">
					<span class="fxpb-handle dashicons dashicons-sort"></span>
					<span class="fxpb-row-title-text">1 Column</span>
					<span class="fxpb-remove dashicons dashicons-trash"></span>
				</div><!-- .fxpb-row-title -->

				<div class="fxpb-row-fields">
					<textarea class="fxpb-row-input" name="" data-field="content" placeholder="Add HTML here..."></textarea>
					<input class="fxpb-row-input" type="hidden" name="" data-field="type" value="text">
				</div><!-- .fxpb-row-fields -->

			</div><!-- .fxpb-row.fxpb-col-1 -->

			<?php /* == This is the 2 columns row template == */ ?>
			<div class="fxpb-row fxpb-col-2">

				<div class="fxpb-row-title">
					<span class="fxpb-handle dashicons dashicons-sort"></span>
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
					<input class="fxpb-row-input" type="hidden" name="" data-field="type" value="text">
				</div><!-- .fxpb-row-fields -->

			</div><!-- .fxpb-row.fxpb-col-2 -->

		</div><!-- .fxpb-templates -->

	</div><!-- #fx-page-builder -->
<?php
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


