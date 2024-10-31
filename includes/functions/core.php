<?php
/**
 * Defines all core plugin functions.
 *
 * @package \Product Notices for WooCommerce
 * @author Aniket Desale
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Default supported color options for notice appearance
 *
 * @since 1.0.0
 *
 * @return array Default color values and labels
 */
function crwcpn_get_notice_colors() {
	return apply_filters(
		'crwcpn_notice_colors',
		array(
			'default'      => __( 'Default', 'product-notices-woocommerce' ),
			'blue'         => __( 'Blue', 'product-notices-woocommerce' ),
			'yellow'       => __( 'Yellow', 'product-notices-woocommerce' ),
			'red'          => __( 'Red', 'product-notices-woocommerce' ),
			'green'        => __( 'Green', 'product-notices-woocommerce' ),
			'custom_color' => __( '&mdash; Custom Style &mdash;', 'product-notices-woocommerce' ),
		)
	);
}

/**
 * Default custom color styles used in notice appearance.
 *
 * @since 1.1.0
 *
 * @return array Default custom color style values
 */
function crwcpn_custom_style_defaults() {
	return apply_filters(
		'crwcpn_custom_style_defaults',
		array(
			'background_color' => '#e5effa',
			'text_color'       => '#21242f',
			'border_color'     => '#aaccf0',
		)
	);
}

/**
 * Show Global Notice on `woocommerce_single_product_summary` hook above the Product Short Description
 *
 * @since 1.0.0
 */
add_action( 'woocommerce_single_product_summary', 'crwcpn_global_product_notice_top', 12 );

/**
 * Display Global product notice.
 */
function crwcpn_global_product_notice_top() {
	/** Global Notices.
	 *
	* @since   1.1.1
	* @uses`   crwcpn_get_global_product_notice` function to render notice.
	*          previously the output markup was directly echoed at this hook location.
	*/

	// Get multi select field ID.
	$global_notice_checkbox = get_option( 'crwpcn_global_product_notice_use_display_rules' );

	if ( 'yes' === $global_notice_checkbox ) {

		global $post;

		$product = wc_get_product( get_the_ID() );

		$product_categories = $product->get_category_ids();
		$product_tags       = $product->get_tag_ids();

		$selected_product_categories = get_option( 'crwpcn_product_notice_display_by_categories' );
		$selected_product_tags       = get_option( 'crwpcn_product_notice_display_by_tags' );

		$in_selected_categories = array_intersect( $selected_product_categories, $product_categories );
		$in_selected_tags       = array_intersect( $selected_product_tags, $product_tags );

		if ( ! empty( $in_selected_categories ) || ! empty( $in_selected_tags ) ) {
			echo crwcpn_get_global_product_notice(); // phpcs:ignore WordPress.Security.EscapeOutput
		}

		return;
	}

	echo crwcpn_get_global_product_notice(); // phpcs:ignore WordPress.Security.EscapeOutput
}

/**
 * Get Global product notice based on the plugin setting.
 *
 * @since 1.1.1
 *
 * @param bool $echo If boolean value is false fuction echo.
 * @return string Return or echo the product notice markup.
 */
function crwcpn_get_global_product_notice( $echo = false ) {
	$crwcpn_hide_global_notice = false;

	// Check if disabled on a product page.
	if ( function_exists( 'is_product' ) && is_product() ) {

		$crwcpn_hide_global_notice = get_post_meta( get_the_ID(), 'crwcpn_hide_global_notice', 1 );
	}

	$crwcpn_global_product_notice_text  = get_option( 'crwpcn_global_product_notice' );
	$crwcpn_global_product_notice_style = get_option( 'crwpcn_product_notice_background_color' );

	// Bail, if notice not set.
	if ( empty( $crwcpn_global_product_notice_text ) ) {

		return;
	}

	/**
	 * Color picker options.
	 *
	 * @since 1.1.0
	 */
	$custom_background_color = get_option( 'crwpcn_product_notice_custom_background_color' );
	$custom_text_color       = get_option( 'crwpcn_product_notice_custom_text_color' );
	$custom_border_color     = get_option( 'crwpcn_product_notice_custom_border_color' );

	$custom_border_radius = get_option( 'crwcpn_product_notice_custom_border_radius' );

	if ( ! empty( $custom_border_radius ) ) {
		$px                   = 'px';    // Concatenation Of String.
		$custom_border_radius = $custom_border_radius . $px;
	}

	$styles      = '';
	$content     = $styles;
	$color_class = esc_attr( $crwcpn_global_product_notice_style );

	if ( 'custom_color' === $crwcpn_global_product_notice_style ) {

		$styles = ' style="background-color: ' . esc_attr( $custom_background_color ) . '; color: ' . esc_attr( $custom_text_color ) . '; border-color: ' . esc_attr( $custom_border_color ) . '; border-radius : ' . esc_attr( $custom_border_radius ) . '"';

		$color_class = 'crwcpn-custom-style';
	}

	if ( 'custom_color' !== $crwcpn_global_product_notice_style ) {
		$styles = 'style="border-radius : ' . esc_attr( $custom_border_radius ) . '"';
	}

	if ( ! $crwcpn_hide_global_notice ) {

		$content  = '<div class="crwcpn-notice crwcpn-global-notice ' . esc_attr( $color_class ) . '"' . $styles . '>';
		$content .= do_shortcode( wp_kses_post( $crwcpn_global_product_notice_text ) );
		$content .= '</div>';
	}

	// Return or echo content.
	if ( $echo ) {

		echo $content; // phpcs:ignore WordPress.Security.EscapeOutput 
		return;
	} else {

		return $content;
	}
}

/**
 * Show notice on `woocommerce_single_product_summary` hook above the Product Short Description for a product
 * Product notice content and appearance style are set as post meta for the product.
 * Shows nothing if per-product notice is not configured
 *
 * @since 1.0.0
 */

add_action( 'woocommerce_single_product_summary', 'crwcpn_product_notice_top', 12 );

/**
 * Display per-product notice
 */
function crwcpn_product_notice_top() {
	/** Per-product notice.
	 *
	 * @since   1.1.1
	 * @uses`   crwcpn_get_product_notice` function to render notice.
	 *          previously the output markup was directly echoed at this hook location.
	 */
	echo crwcpn_get_product_notice( get_the_ID() ); // phpcs:ignore WordPress.Security.EscapeOutput 
}

/**
 * Get product notice for specific product.
 *
 * @since 1.1.1
 *
 * @param int  $id Product ID. Defaults to current product ID (global $post).
 * @param bool $echo Return or echo the product notice markup.
 */
function crwcpn_get_product_notice( $id = null, $echo = false ) {
	if ( null !== $id ) { // Use the ID if supplied.

		$product_id = intval( $id );
	} else { // Default to current product ID.

		$product_id = ( function_exists( 'is_product' ) && is_product() ) ? get_the_ID() : 0;
	}

	// Do nothing if invalid product ID.
	if ( 0 === $product_id ) {
		return;
	}

	$crwcpn_single_product_notice_text = get_post_meta( $product_id, 'crwcpn_product_notice', true );

	$crwpcn_single_product_notice_style = get_post_meta( $product_id, 'crwpcn_single_product_notice_background_color', true );

	/**
	 * Color picker options.
	 *
	 * @since 1.1.0
	 */
	$single_custom_background_color = get_post_meta( $product_id, 'crwpcn_single_product_notice_custom_background_color', true );
	$single_custom_text_color       = get_post_meta( $product_id, 'crwpcn_single_product_notice_custom_text_color', true );
	$single_custom_border_color     = get_post_meta( $product_id, 'crwpcn_single_product_notice_custom_border_color', true );

	$single_custom_border_radius = get_post_meta( $product_id, 'crwcpn_product_notice_border_radius', true );

	if ( ! empty( $single_custom_border_radius ) ) {
		$px                          = 'px';    // Concatenation Of String.
		$single_custom_border_radius = $single_custom_border_radius . $px;
	}

	$styles      = '';
	$content     = $styles;
	$color_class = esc_attr( $crwpcn_single_product_notice_style );

	if ( 'custom_color' === $crwpcn_single_product_notice_style ) {

		$styles = ' style="background-color: ' . esc_attr( $single_custom_background_color ) . '; color: ' . esc_attr( $single_custom_text_color ) . '; border-color: ' . esc_attr( $single_custom_border_color ) . '; border-radius: ' . esc_attr( $single_custom_border_radius ) . '"';

		$color_class = 'crwcpn-custom-style';
	}

	if ( 'custom_color' !== $crwpcn_single_product_notice_style ) {
		$styles = 'style="border-radius : ' . esc_attr( $single_custom_border_radius ) . '"';
	}

	if ( ! empty( $crwcpn_single_product_notice_text ) ) {

		$content  = '<div class="crwcpn-notice crwcpn-product-notice ' . esc_attr( $color_class ) . '"' . $styles . '>';
		$content .= do_shortcode( wp_kses_post( $crwcpn_single_product_notice_text ) );
		$content .= '</div>';
	}

	// Return or echo content.
	if ( $echo ) {

		echo $content; // phpcs:ignore WordPress.Security.EscapeOutput 
		return;
	} else {

		return $content;
	}
}

/**
 * Show Product Notices post type on `woocommerce_single_product_summary` hook above the Product Short Description
 *
 * @since 1.3.0
 */
add_action( 'woocommerce_single_product_summary', 'crwcpn_product_notice_display', 12 );

/**
 * Display post type product notice
 */
function crwcpn_product_notice_display() {
	/** Multiple Global and Per-Product Notices.
	 *
	* @since   1.3.0
	* @uses    `crwcpn_cp_product_notice` function to render notice.
	*          previously the output markup was directly echoed at this hook location.
	*/

	echo crwcpn_get_pt_product_notice(); // phpcs:ignore WordPress.Security.EscapeOutput 
}

/**
 * Get product notice for specific product.
 *
 * @since 1.3.0
 *
 * @param int  $id Product ID. Defaults to current product ID (global $post).
 * @param bool $ignore_rules Return return true or false.
 */
function crwcpn_get_pt_product_notice( $id = array(), $ignore_rules = false ) {

	/**
	 * Setup query to show the ‘services’ post type with ‘8’ posts.
	 * Output the title with an excerpt.
	 */
	if ( ! is_array( $id ) ) {

		$id = (array) $id;
	}

	$args = array(
		'post_type'      => CRWCPN_PT_SLUG,
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'fields'         => 'ids',
	);

	// If Post IDs.
	if ( ! empty( $id ) ) {
		$args['post__in'] = $id;
	}

	// get your custom posts ids as an array.
	$posts = get_posts( $args );

	$output = '';
	// loop over each post.
	foreach ( $posts as $post_id ) {

		// get the meta you need form each post.
		$crwcpn_multiple_product_notice_text = get_post_meta( $post_id, 'crwcpn_product_notice_pt', true );

		$crwcpn_multiple_product_notice_style = get_post_meta( $post_id, 'crwcpn_single_product_notice_background_color_pt', true );

		/**
		 * Color picker options.
		 *
		 * @since 1.3.0
		 */
		$single_custom_background_color = get_post_meta( $post_id, 'crwcpn_single_product_notice_custom_background_color_pt', true );
		$single_custom_text_color       = get_post_meta( $post_id, 'crwcpn_single_product_notice_custom_text_color_pt', true );
		$single_custom_border_color     = get_post_meta( $post_id, 'crwcpn_single_product_notice_custom_border_color_pt', true );

		$single_custom_border_radius = get_post_meta( $post_id, 'crwcpn_product_notice_border_radius_pt', true );

		if ( ! empty( $single_custom_border_radius ) ) {
			$px                          = 'px';    // Concatenation Of String.
			$single_custom_border_radius = $single_custom_border_radius . $px;
		}

		$styles      = '';
		$content     = $styles;
		$color_class = esc_attr( $crwcpn_multiple_product_notice_style );

		if ( 'custom_color' === $crwcpn_multiple_product_notice_style ) {

			$styles = ' style="background-color: ' . esc_attr( $single_custom_background_color ) . '; color: ' . esc_attr( $single_custom_text_color ) . '; border-color: ' . esc_attr( $single_custom_border_color ) . '; border-radius : ' . esc_attr( $single_custom_border_radius ) . '"';

			$color_class = 'crwcpn-custom-style';
		}

		if ( 'custom_color' !== $crwcpn_multiple_product_notice_style ) {
			$styles = 'style="border-radius : ' . esc_attr( $single_custom_border_radius ) . '"';
		}

		if ( ! empty( $crwcpn_multiple_product_notice_text ) ) {

			$content  = '<div class="crwcpn-notice crwcpn-product-notice ' . esc_attr( $color_class ) . '"' . $styles . '>';
			$content .= do_shortcode( wp_kses_post( $crwcpn_multiple_product_notice_text ) );
			$content .= '</div>';
		}

		$global_notice_checkbox_pt           = get_post_meta( $post_id, 'crwcpn_enable_global_product_notice_pt' );
		$display_global_notice_rule_chechbox = implode( ',', $global_notice_checkbox_pt );

		$display_rule_notice_checkbox_pt = get_post_meta( $post_id, 'crwcpn_global_product_notice_use_display_rules_pt' );
		$display_rule_chechbox           = implode( ',', $display_rule_notice_checkbox_pt );

		/* If display global notice is enabel. */
		if ( ! $ignore_rules && ( ! empty( $display_global_notice_rule_chechbox ) && '1' === $display_global_notice_rule_chechbox ) ) {

			$output .= $content;
		}

		/* If display notice on categories and/or tags is enabel. */
		if ( ! $ignore_rules && ( ! empty( $display_rule_chechbox ) && '1' === $display_rule_chechbox ) ) {

			$selected_product_categories = get_post_meta( $post_id, 'crwcpn_product_notice_display_by_categories_pt' );
			$selected_product_tags       = get_post_meta( $post_id, 'crwcpn_product_notice_display_by_tags_pt' );
			$selected_product_ids        = get_post_meta( $post_id, 'crwcpn_product_notice_display_by_products_pt' );

			/* If shortcode is not on product page */
			if ( get_post_type( get_the_ID() ) === 'product' ) {
				$product            = wc_get_product( get_the_ID() );
				$product_categories = $product->get_category_ids();
				$product_tags       = $product->get_tag_ids();

				if ( ! empty( $selected_product_categories ) && '' !== $selected_product_categories[0] ) {
					foreach ( $selected_product_categories as $value ) {

						$all_categories_values  = (array) $value;
						$in_selected_categories = array_intersect( $all_categories_values, $product_categories );

						if ( ! empty( $in_selected_categories ) ) {
							$output .= $content;
						}
					}
				}

				if ( ! empty( $selected_product_tags ) && '' !== $selected_product_tags[0] ) {

					foreach ( $selected_product_tags as $value ) {

						$all_tags_values  = (array) $value;
						$in_selected_tags = array_intersect( $all_tags_values, $product_tags );

						if ( ! empty( $in_selected_tags ) ) {
							$output .= $content;
						}
					}
				}

				if ( ! empty( $selected_product_ids ) && '' !== $selected_product_ids[0] ) {

					$ids         = get_the_ID();
					$product_ids = (array) $ids;

					foreach ( $selected_product_ids as $value ) {

						$all_products_values  = (array) $value;
						$in_selected_products = array_intersect( $all_products_values, $product_ids );

						if ( ! empty( $in_selected_products ) ) {
							$output .= $content;
						}
					}
				}
			}
		}
	}

	return $output;
}

/**
 * Shortcodes to display notice text.
 */
add_shortcode( 'crwcpn-notice', 'crwcpn_sc_notice' );

/**
 * Shortcode support to display product notices.
 *
 * # use attribute [type] to
 *
 * @since 1.1.1
 *
 * @param array $atts Shortcode attributes.
 * @return Product notice markup.
 */
function crwcpn_sc_notice( $atts ) {
	$crwcpn_attrs = shortcode_atts(
		array(
			'type'      => 'default',
			'id'        => '',
			'notice_id' => '',
		),
		$atts,
		'crwcpn-notice'
	);

	$type = sanitize_text_field( $crwcpn_attrs['type'] );

	// Show global or specific product notice, as requested.
	switch ( $type ) {
		case 'product':
			if ( ! empty( $crwcpn_attrs['id'] ) ) {

				$id     = (int) $crwcpn_attrs['id'];
				$output = crwcpn_get_product_notice( $id, false );
			} else {

				$output = crwcpn_get_product_notice( null, false );
			}

			break;

		case 'notice':
			$ids    = explode( ',', $crwcpn_attrs['notice_id'] );
			$output = crwcpn_get_pt_product_notice( $ids, true );

			break;

		case 'global':
			$output = crwcpn_get_global_product_notice();

			break;
		default:        // Sbow global notice for any other type specified as shortcode attribute.
			if ( 'default' === $crwcpn_attrs['type'] ) {
				if ( ! empty( $crwcpn_attrs['id'] ) ) {
					$ids    = explode( ',', $crwcpn_attrs['id'] );
					$output = crwcpn_get_pt_product_notice( $ids );
				} else {
					$output = crwcpn_get_global_product_notice();
				}
			} else {
				$output = crwcpn_get_global_product_notice();
			}
			break;
	}

	return $output;
}
