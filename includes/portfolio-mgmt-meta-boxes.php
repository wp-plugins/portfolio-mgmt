<?php
/*----------------------------------------------------------------------------*/
/* Add Portfolio Meta Boxes
/*----------------------------------------------------------------------------*/

add_action( 'add_meta_boxes', 'wap8_add_portfolio_meta_boxes', 10 );

/**
 * Add Portfolio Meta Boxes
 *
 * Add meta boxes to the portfolio post editor using the add_meta_box function.
 *
 * @package Portfolio Mgmt.
 * @version 1.0.0
 * @since 1.0.0
 * @author Heavy Heavy <@heavyheavycoe>
 *
 */

function wap8_add_portfolio_meta_boxes() {

	$portfolio       = get_post_type_object( 'wap8-portfolio' );
	$portfolio_label = $portfolio->labels->singular_name;

	add_meta_box( 'wap8-portfolio-case-info', $portfolio_label . __( ' Information', 'portfolio-mgmt' ), 'wap8_portfolio_case_info_cb', 'wap8-portfolio', 'side', 'high' );

}

/*----------------------------------------------------------------------------*/
/* Portfolio Case Info Callback
/*----------------------------------------------------------------------------*/

/**
 * Portfolio Case Info Callback
 *
 * Render a sidebar meta box to save client meta data and a project URL, if one
 * is relevant.
 *
 * @param $post
 *
 * @package Portfolio Mgmt.
 * @version 1.0.0
 * @since 1.1.3 Fixed form text inputs in custom meta box so they are now responsive
 * @author Heavy Heavy <@heavyheavyco>
 *
 */

function wap8_portfolio_case_info_cb( $post ) {

	// declare variables to store saved meta data
	$feature_case     = get_post_meta( $post->ID, '_wap8_portfolio_feature', true );
	$client           = get_post_meta( $post->ID, '_wap8_client_name', true ); // client name
	$project_url      = get_post_meta( $post->ID, '_wap8_project_url', true ); // project URL
	$project_url_text = get_post_meta( $post->ID, '_wap8_project_url_text', true ); // project URL text

	// nonce to verify intention later
	wp_nonce_field( plugin_basename( __FILE__ ), 'wap8_portfolio_nonce' );

	$portfolio       = get_post_type_object( 'wap8-portfolio' );
	$portfolio_label = $portfolio->labels->singular_name;

	printf(
		__( '<p>%s information is optional meta data that can be used by your theme.</p>', 'portfolio-mgmt' ),
		esc_html( $portfolio_label )
	); ?>

	<p>
		<input id="wap8-portfolio-feature" name="_wap8_portfolio_feature" type="checkbox" value="1" <?php checked( $feature_case ); ?> />
		<label for="wap8-portfolio-feature"><?php printf( __( 'Feature this %s', 'portfolio-mgmt' ), esc_html( $portfolio_label ) ); ?></label>
	</p>

	<p>
		<label for="wap8-client-name"><?php _e( 'Client Name', 'portfolio-mgmt' ); ?></label><br />
		<input type="text" id="wap8-client-name" name="_wap8_client_name" value="<?php echo esc_attr( $client ); ?>" />
	</p>

	<p>
		<label for="wap8-project-url"><?php _e( 'Project URL', 'portfolio-mgmt' ); ?></strong><br />
		<input type="text" id="wap8-project-url" name="_wap8_project_url" value="<?php echo esc_attr( $project_url ) ?>" /><br />
	</p>

	<p>
		<label for="wap8-project-url-text"><?php _e( 'Project URL Text', 'portfolio-mgmt' ); ?></label><br />
		<input type="text" id="wap8-project-url-text" name="_wap8_project_url_text" value="<?php echo esc_attr( $project_url_text ); ?>" /><br />
	</p>

	<p><?php _e( 'If your currently active theme does not already display this content, please click on the Help tab above for detailed instructions.', 'portfolio-mgmt' ); ?></p>

	<?php

}

/*----------------------------------------------------------------------------*/
/* Save Portfolio Meta
/*----------------------------------------------------------------------------*/

add_action( 'save_post', 'wap8_save_portfolio_meta', 10 );

/**
 * Save portfolio meta.
 *
 * Save and sanitize the portfolio meta boxes.
 *
 * @param $id
 *
 * @package Portfolio Mgmt.
 * @version 1.0.0
 * @since 1.0.8 Improve data sanitization
 * @author Heavy Heavy <@heavyheavyco>
 *
 */

function wap8_save_portfolio_meta( $id ) {

	// we do not want to auto save the data
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
		return; 

	// check our nonce
	if ( !isset( $_POST['wap8_portfolio_nonce'] ) )
		return;

	if ( !wp_verify_nonce( $_POST['wap8_portfolio_nonce'], plugin_basename( __FILE__ ) ) )
		return;

	// make sure the current user can edit the post
	if ( !current_user_can( 'edit_post', $id ) )
		return;

	// save featured case study checkbox
	if ( isset( $_POST['_wap8_portfolio_feature'] ) ) {
		update_post_meta( $id, '_wap8_portfolio_feature', $_POST['_wap8_portfolio_feature'] );
	} else {
		delete_post_meta( $id, '_wap8_portfolio_feature', '' );
	}

	// strip all tags and escape attributes before saving client name
	if ( isset( $_POST['_wap8_client_name'] ) )
		update_post_meta( $id, '_wap8_client_name', wp_strip_all_tags( $_POST['_wap8_client_name'] ) );

	// escape URL before saving project URL
	if ( isset( $_POST['_wap8_project_url'] ) )
		update_post_meta( $id, '_wap8_project_url', esc_url_raw( $_POST['_wap8_project_url'] ) );

	// strip all tags and escape attributes before saving project URL text
	if ( isset( $_POST['_wap8_project_url_text'] ) )
		update_post_meta( $id, '_wap8_project_url_text', wp_strip_all_tags( $_POST['_wap8_project_url_text'] ) );

}