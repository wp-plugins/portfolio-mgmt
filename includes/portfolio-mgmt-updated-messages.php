<?php
/*----------------------------------------------------------------------------*/
/* Updated Portfolio Messages
/*----------------------------------------------------------------------------*/

add_filter( 'post_updated_messages', 'wap8_updated_portfolio_messages', 10, 1 );

/**
 * Updated Portfolio Messages
 *
 * Customizing post updated messages for the wap8-portfolio custom post type.
 *
 * @param $messages Post updated messages
 * @return $messages Custom post updated messages
 *
 * @package Portfolio Portfolio Mgmt.
 * @version 1.0.0
 * @since 1.1.5 Accounting for filtered post type arguments
 * @author Heavy Heavy <@heavyheavyco>
 *
 */

function wap8_updated_portfolio_messages( $messages ) {

	global $post, $post_ID;

	$portfolio       = get_post_type_object( 'wap8-portfolio' );
	$portfolio_label = $portfolio->labels->singular_name;

	$messages['wap8-portfolio'] = array(
		0  => '', // Unused. Messages start at index 1.
		1  => sprintf( __( '%1$s updated. <a href="%2$s">View %3$s</a>', 'portfolio-mgmt' ), esc_html( $portfolio_label ), esc_url( get_permalink( $post_ID ) ), esc_html( $portfolio_label ) ),
		2  => __( 'Custom field updated.', 'portfolio-mgmt' ),
		3  => __( 'Custom field deleted.', 'portfolio-mgmt' ),
		4  => sprintf( __( '%s updated.', 'portfolio-mgmt' ), esc_html( $portfolio_label ) ),
		/* translators: %s: date and time of the revision */
		5  => isset( $_GET['revision'] ) ? sprintf( __( '%1$s restored to revision from %2$s', 'portfolio-mgmt' ), esc_html( $portfolio_label ), wp_post_revision_title( ( int ) $_GET['revision'], false ) ) : false,
		6  => sprintf( __( '%1$s published. <a href="%2$s">View %3$s</a>', 'portfolio-mgmt' ), esc_html( $portfolio_label ), esc_url( get_permalink( $post_ID ) ), esc_html( $portfolio_label ) ),
		7  => sprintf( __( '%s saved.', 'portfolio-mgmt' ), esc_html( $portfolio_label ) ),
		8  => sprintf( __( '%1$s submitted. <a target="_blank" href="%2$s">Preview %3$s</a>', 'portfolio-mgmt' ), esc_html( $portfolio_label ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ), esc_html( $portfolio_label ) ),
		9  => sprintf( __( '%1$s scheduled for: <strong>%2$s</strong>. <a target="_blank" href="%3$s">Preview %4$s</a>', 'portfolio-mgmt' ),
		// translators: Publish box date format, see http://php.net/date
		esc_html( $portfolio_label ), date_i18n( __( 'M j, Y @ G:i', 'portfolio-mgmt' ), strtotime( $post->post_date ) ), esc_url( get_permalink( $post_ID ) ), esc_html( $portfolio_label ) ),
		10 => sprintf( __( '%1$s draft updated. <a target="_blank" href="%2$s">Preview %3$s</a>', 'portfolio-mgmt' ), esc_html( $portfolio_label ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ), esc_html( $portfolio_label ) ),
	);

	return $messages;

}