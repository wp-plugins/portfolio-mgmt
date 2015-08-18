<?php
/*----------------------------------------------------------------------------*/
/* Custom Portfolio Columns
/*----------------------------------------------------------------------------*/

add_filter( 'manage_edit-wap8-portfolio_columns', 'wap8_custom_portfolio_columns', 10, 1 );

/**
 * Custom Portfolio Columns
 *
 * Customizing the columns for the wap8-portfolio custom post type edit screen.
 *
 * @param $columns Post columns
 * @return $columns Custom post columns
 *
 * @package Portfolio Mgmt.
 * @version 1.0.0
 * @since 1.1.7 Added filter for custom column arguments
 * @author Heavy Heavy <@heavyheavyco>
 *
 */

function wap8_custom_portfolio_columns( $columns ) {

	$portfolio          = get_post_type_object( 'wap8-portfolio' );
	$portfolio_services = get_taxonomy( 'wap8-services' );
	$portfolio_tags     = get_taxonomy( 'wap8-portfolio-tags' );

	$portfolio_label = $portfolio->labels->name;
	$services_label  = $portfolio_services->labels->name;
	$tags_label      = $portfolio_tags->labels->name;

	$columns = array(
		'cb'                         => '<input type="checkbox" />',
		'wap8-featured-image'        => __( 'Thumbnail', 'portfolio-mgmt' ),
		'wap8-featured-column'       => __( 'Featured', 'portfolio-mgmt' ),
		'title'                      => esc_html( $portfolio_label ),
		'wap8-client-column'         => __( 'Client', 'portfolio-mgmt' ),
		'wap8-services-column'       => esc_html( $services_label ),
		'wap8-portfolio-tags-column' => esc_html( $tags_label ),
		'author'                     => __( 'Author', 'portfolio-mgmt' ),
		'date'                       => _x( __( 'Date', 'portfolio-mgmt' ), 'column name' ),
	);

    $columns = apply_filters( 'portfolio_mgmt_custom_columns_args', $columns);
	return $columns;

}

/*----------------------------------------------------------------------------*/
/* Portfolio Columns Content
/*----------------------------------------------------------------------------*/

add_action( 'manage_wap8-portfolio_posts_custom_column', 'wap8_portfolio_columns_content', 10, 2 );

/**
 * Portfolio Columns Content
 *
 * Adding the custom taxonomies and client names to their respective custom
 * columns. The taxonomies should be comma separated anchors similar to post
 * categories and tags behavior.
 *
 * @param $column Custom columns
 * @param $post_id Post ID
 *
 * @package Portfolio Mgmt.
 * @version 1.0.0
 * @since 1.1.5 Added a check for filtered post type labels
 * @author Heavy Heavy <@heavyheavyco>
 *
 */

function wap8_portfolio_columns_content( $column, $post_id ) {

	global $post;

	switch ( $column ) {

		case 'wap8-featured-image' : // featured image

			$image = get_the_post_thumbnail( $post->ID, array( 60, 60 ) ); // get the thumb version of the featured image

			if ( $image ) { // if an image has been set

				echo $image;

			} else { // no image has been set

				echo __( '<i>No thumbnail.</i>', 'portfolio-mgmt' );

			}

			break;

		case 'wap8-featured-column' : // featured case column

			$featured = get_post_meta( $post->ID, '_wap8_portfolio_feature', true ); // get the featured status of the current post

			if ( $featured == 1 ) { // the current post has been marked as featured

				echo '<img src="' . plugin_dir_url( dirname( __FILE__ ) ) . 'images/star.png" style="width: 16px; height: 16px;">';

			}

			break;

		case 'wap8-client-column' : // client column

			$client = get_post_meta( $post->ID, '_wap8_client_name', true ); // get the client name from custom meta box

			if ( !empty( $client ) ) { // if a client name has been set

				echo esc_html( $client );

			} else { // no client name has been set

				echo __( '<i>No Client.</i>', 'portfolio-mgmt' );

			}

			break;

		case 'wap8-services-column' : // services column

			$terms = get_the_terms( $post_id, 'wap8-services' ); // get the services for the post

			if ( !empty( $terms ) ) { // if terms were found

				$out = array();

				foreach ( $terms as $term ) { // loop through each term, linking to the 'edit posts' page for the specific term
					$out[] = sprintf(
						'<a href="%s">%s</a>',
						esc_url(
							add_query_arg(
								array(
									'post_type'      => $post->post_type,
									'wap8-services'  => $term->slug,
								), 'edit.php' ) ),
							esc_html( sanitize_term_field( 'name', $term->name, $term->term_id, 'wap8-services', 'display' ) )
						);
				}

				echo join( ', ', $out ); // join the terms and separate with a coma

			}

			else { // if no terms were found, output a default message

				$portfolio_services = get_taxonomy( 'wap8-services' );
				$services_label     = $portfolio_services->labels->name;

				printf(
					__( '<i>No %s.</i>', 'portfolio-mgmt' ),
					esc_html( $services_label )
				);

			}

			break;

		case 'wap8-portfolio-tags-column' : // portfolio tags column

			$terms = get_the_terms( $post_id, 'wap8-portfolio-tags' ); // get the portfolio tags for the post

			if ( !empty( $terms ) ) { // if terms were found

				$out = array();

				foreach ( $terms as $term ) { // loop through each term, linking to the 'edit posts' page for the specific term
					$out[] = sprintf(
						'<a href="%s">%s</a>',
						esc_url(
							add_query_arg(
								array(
									'post_type'           => $post->post_type,
									'wap8-portfolio-tags' => $term->slug,
								), 'edit.php' ) ),
							esc_html( sanitize_term_field( 'name', $term->name, $term->term_id, 'wap8-portfolio-tags', 'display' ) )
						);
				}

				echo join( ', ', $out ); // join the terms and separate with a coma

			}

			else { // if no terms were found, output a default message

				$portfolio_tags = get_taxonomy( 'wap8-portfolio-tags' );
				$tags_label     = $portfolio_tags->labels->name;

				printf(
					__( '<i>No %s.</i>', 'portfolio-mgmt' ),
					esc_html( $tags_label )
				);

			}

			break;

		default : // break out of the switch statement for everything else

			break;

	}

}

/*----------------------------------------------------------------------------*/
/* Portfolio Sortable Columns
/*----------------------------------------------------------------------------*/

add_filter( 'manage_edit-wap8-portfolio_sortable_columns', 'wap8_portfolio_sortable_columns', 10, 1 );

/**
 * Portfolio Sortable Columns
 *
 * Let WordPress know the client column should be sortable.
 *
 * @param $columns Post columns
 *
 * @package Portfolio Mgmt.
 * @version 1.0.0
 * @since 1.0.0
 * @author Erik Ford for We Are Pixel8 <@notdivisible>
 *
 */

function wap8_portfolio_sortable_columns( $columns ) {

	$columns['wap8-client-column'] = 'wap8-client-column';

	return $columns;

}

/*----------------------------------------------------------------------------*/
/* Portfolio Sortable Columns
/*----------------------------------------------------------------------------*/

add_action( 'pre_get_posts', 'wap8_portfolio_manage_sortable_columns', 10, 1 );

/**
 * Manage Sortable Columns
 *
 * Sort our custom column.
 *
 * @param $query
 *
 * @package Portfolio Mgmt.
 * @version 1.0.0
 * @since 1.1.6
 * @author Heavy Heavy <@heavyheavyco>
 *
 */

function wap8_portfolio_manage_sortable_columns( $query ) {

	if ( !is_admin() )
		return;

	if ( $query->is_main_query() && ( $orderby = $query->get( 'orderby' ) ) ) {
		switch( $orderby ) {
			case 'wap8-client-column' :
				$query->set( 'meta_key', '_wap8_client_name' );
				$query->set( 'orderby', 'meta_value' );
			break;
		}
	}

}