/**
 * Scripts loading on the frontend.
 *
 * @package \Product Notices for WooCommerce
 * @author Rohit Dokhe
 * @since 1.1.0
 */

var adminScript = (function ($) {

	'use strict';

	var colorPicker = function () {

		/* Handles color picker on Product Edit screen. */
		if ($( 'body' ).find( '.crwcpn-input-color' ).length) {

			$( '.crwcpn-input-color' ).wpColorPicker();
		}

		if ($( '#crwpcn_product_notice_background_color' ).val() === 'custom_color' || $( '#crwcpn_product_notice_color' ).val() === 'custom_color' || $( '#crwcpn-product-notice-color-pt' ).val() === 'custom_color') {

			crwpcn_colorElementShow();
		} else {

			crwpcn_colorElementHide();
		}

		$( '#crwpcn_product_notice_background_color,#crwcpn_product_notice_color,#crwcpn-product-notice-color-pt' ).change(
			function () {

				if ($( this ).val() === 'custom_color') {

					crwpcn_colorElementShow();
				} else {

					crwpcn_colorElementHide();
				}
			}
		);

		/**
		 * Show HTML element on admin settings and product page.
		 *
		 * @since 1.1.0
		 */
		function crwpcn_colorElementShow() {

			// Custom color picker field Ids of the admin settings.
			$( '#crwpcn_product_notice_custom_background_color' ).parents( 'tr' ).show( 'slow' );
			$( '#crwpcn_product_notice_custom_text_color' ).parents( 'tr' ).show( 'slow' );
			$( '#crwpcn_product_notice_custom_border_color' ).parents( 'tr' ).show( 'slow' );

			// Custom color picker field Id of the edit product page.
			$( '#crwcpn-product-notice-custom-style,#crwcpn-product-notice-custom-style-post-type' ).show( 'slow' );
		}

		/**
		 * Hide HTML element on admin settings and product page.
		 *
		 * @since 1.1.0
		 */
		function crwpcn_colorElementHide() {

			// Custom color picker field Ids of the admin settings.
			$( '#crwpcn_product_notice_custom_background_color' ).parents( 'tr' ).hide( 'slow' );
			$( '#crwpcn_product_notice_custom_text_color' ).parents( 'tr' ).hide( 'slow' );
			$( '#crwpcn_product_notice_custom_border_color' ).parents( 'tr' ).hide( 'slow' );

			// Custom color picker field Id of the edit product page.
			$( '#crwcpn-product-notice-custom-style,#crwcpn-product-notice-custom-style-post-type' ).hide( 'slow' );
		}
	},

	/**
	 * Handles
	 */
	displayRulesAdmin = function () {

		if (typeof crwcpn_admin == 'undefined') {
			return;
		}

		/* Placeholder for categories and tags field */
		$( '#crwpcn_product_notice_display_by_categories, #crwcpn-product-notice-display-by-categories-pt' ).selectWoo(
			{
				placeholder: typeof undefined !== crwcpn_admin && crwcpn_admin.category_placeholder_text,
				allowClear: true
			}
		);

		$( '#crwpcn_product_notice_display_by_tags, #crwcpn-product-notice-display-by-tags-pt' ).selectWoo(
			{
				placeholder: typeof undefined !== crwcpn_admin && crwcpn_admin.tag_placeholder_text,
				allowClear: true
			}
		);

		$( '#crwcpn-product-notice-display-by-products-pt' ).selectWoo(
			{
				placeholder: typeof undefined !== crwcpn_admin && crwcpn_admin.product_placeholder_text,
				allowClear: true
			}
		);

		/* If checkbox is enabled, show fields */
		if ($( '#crwpcn_global_product_notice_use_display_rules' ).is( ':checked' )) {
			crwpcn_Display_Category_Elementshow();
		} else {
			crwpcn_Display_Category_ElementHide();
		}

		/* Desiable warning notice if fields is not empty. */
		if ($( '#crwpcn_global_product_notice_use_display_rules' ).is( ':checked' )) {
			$( '#crwpcn_product_notice_display_by_categories, #crwpcn_product_notice_display_by_tags' ).change(
				function () {
					var crwpcn_get_categories_val = $( '#crwpcn_product_notice_display_by_categories' ).val(),
						crwpcn_get_tags_val       = $( '#crwpcn_product_notice_display_by_tags' ).val(),
						validation_text           = typeof crwcpn_admin !== undefined && crwcpn_admin.display_rules_validation_text;

					if (crwpcn_get_categories_val.length == 0 && crwpcn_get_tags_val.length == 0) {

						$( "#crwcpn-display-warning-notice-description" ).removeClass( 'hide' );
						$( "#crwcpn-display-warning-notice-description" ).html( '<span id="remove" class="notice notice-warning" style="display: block; margin-top: 0"><p>' + validation_text + '</p></span>' );

					} else if ((crwpcn_get_categories_val.length == 0 && crwpcn_get_tags_val.length != 0) || (crwpcn_get_categories_val.length != 0 && crwpcn_get_tags_val.length == 0)) {
						$( "#crwcpn-display-warning-notice-description" ).html( '' );
						$( "#crwcpn-display-warning-notice-description" ).addClass( 'hide' );
					}
				}
			);
		} else if ($( '#crwpcn_global_product_notice_use_display_rules' ).on( 'change' )) {
			$( '#crwpcn_product_notice_display_by_categories, #crwpcn_product_notice_display_by_tags' ).change(
				function () {
					var crwpcn_get_categories_val = $( '#crwpcn_product_notice_display_by_categories' ).val(),
						crwpcn_get_tags_val       = $( '#crwpcn_product_notice_display_by_tags' ).val(),
						validation_text           = typeof crwcpn_admin !== undefined && crwcpn_admin.display_rules_validation_text;
					if (crwpcn_get_categories_val.length == 0 && crwpcn_get_tags_val.length == 0) {

						$( "#crwcpn-display-warning-notice-description" ).removeClass( 'hide' );
						$( "#crwcpn-display-warning-notice-description" ).html( '<span id="remove" class="notice notice-warning" style="display: block; margin-top: 0"><p>' + validation_text + '</p></span>' );

					} else if ((crwpcn_get_categories_val.length == 0 && crwpcn_get_tags_val.length != 0) || (crwpcn_get_categories_val.length != 0 && crwpcn_get_tags_val.length == 0)) {
						$( "#crwcpn-display-warning-notice-description" ).html( '' );
						$( "#crwcpn-display-warning-notice-description" ).addClass( 'hide' );
					}
				}
			);
		}

		$( '#crwpcn_global_product_notice_use_display_rules' ).change(
			function () {

				if ($( this ).is( ":checked" )) {
					crwpcn_Display_Category_Elementshow();
				} else {
					crwpcn_Display_Category_ElementHide();
					$( "#crwcpn-display-warning-notice-description" ).html( '' );
				}
			}
		);

		/* function to show fields */
		function crwpcn_Display_Category_Elementshow() {

			$( '#crwpcn_product_notice_display_by_categories, #crwcpn-product-notice-display-by-categories-pt' ).parents( 'tr' ).show( 'slow' );
			$( '#crwpcn_product_notice_display_by_tags, #crwcpn-product-notice-display-by-tags-pt' ).parents( 'tr' ).show( 'slow' );
			$( '#crwcpn-product-notice-display-by-products-pt' ).parents( 'tr' ).show( 'slow' );
		}

		/* function to hide fields */
		function crwpcn_Display_Category_ElementHide() {

			$( '#crwpcn_product_notice_display_by_categories, #crwcpn-product-notice-display-by-categories-pt' ).parents( 'tr' ).hide();
			$( '#crwpcn_product_notice_display_by_tags, #crwcpn-product-notice-display-by-tags-pt' ).parents( 'tr' ).hide( 'slow' );
			$( '#crwcpn-product-notice-display-by-products-pt' ).parents( 'tr' ).hide( 'slow' );
		}

		/**
		 * All condition for custom post type notice.
		 *
		 * @since 1.3.0
		 */

		/* If display global notice is checked. */
		if ($( '#crwcpn-enable-global-product-notice-pt' ).is( ':checked' )) {
			crwpcn_Display_Category_ElementHide();
			$( '#crwcpn-global-product-notice-use-display-rules-pt' ).prop( 'disabled', true );
		}

		/* If selcte catagorie/ tagd checkbox is enabled, show fields */
		if ($( '#crwcpn-global-product-notice-use-display-rules-pt' ).is( ':checked' )) {
			crwpcn_Display_Category_Elementshow();
			$( '#crwcpn-enable-global-product-notice-pt' ).prop( 'disabled', true );
		}

		$( '#crwcpn-enable-global-product-notice-pt' ).change(
			function () {

				if ($( this ).is( ":checked" )) {
					$( '#crwcpn-global-product-notice-use-display-rules-pt' ).prop( 'disabled', true );
					crwpcn_Display_Category_ElementHide();
				} else {
					$( '#crwcpn-global-product-notice-use-display-rules-pt' ).prop( 'disabled', false );
				}
			}
		);

		$( '#crwcpn-global-product-notice-use-display-rules-pt' ).change(
			function () {

				if ($( this ).is( ":checked" )) {
					crwpcn_Display_Category_Elementshow();
					$( '#crwcpn-enable-global-product-notice-pt' ).prop( 'disabled', true );
				} else {
					crwpcn_Display_Category_ElementHide();
					$( "#crwcpn-display-warning-notice-description" ).html( '' );
					$( '#crwcpn-enable-global-product-notice-pt' ).prop( 'disabled', false );
				}
			}
		);

		/* Desiable warning notice if fields is not empty on product notices post type. */
		if ($( '#crwcpn-global-product-notice-use-display-rules-pt' ).is( ':checked' )) {
			$( '#crwcpn-product-notice-display-by-categories-pt, #crwcpn-product-notice-display-by-tags-pt, #crwcpn-product-notice-display-by-products-pt' ).change(
				function () {
					var crwpcn_get_categories_val = $( '#crwcpn-product-notice-display-by-categories-pt' ).val(),
						crwpcn_get_tags_val       = $( '#crwcpn-product-notice-display-by-tags-pt' ).val(),
						crwpcn_get_products_val   = $( '#crwcpn-product-notice-display-by-products-pt' ).val(),
						validation_text           = typeof crwcpn_admin !== undefined && crwcpn_admin.display_rules_validation_text;

					if (crwpcn_get_categories_val.length == 0 && crwpcn_get_tags_val.length == 0 && crwpcn_get_products_val.length == 0) {

						$( "#crwcpn-display-warning-notice-description" ).removeClass( 'hide' );
						$( "#crwcpn-display-warning-notice-description" ).html( '<span id="remove" class="notice notice-warning" style="display: block; margin-top: 0"><p>' + validation_text + '</p></span>' );

					} else if ((crwpcn_get_categories_val.length == 0 && crwpcn_get_tags_val.length != 0) || (crwpcn_get_categories_val.length != 0 && crwpcn_get_tags_val.length == 0) || (crwpcn_get_categories_val.length != 0 && crwpcn_get_products_val.length == 0) || (crwpcn_get_tags_val.length == 0 && crwpcn_get_products_val.length != 0)) {
						$( "#crwcpn-display-warning-notice-description" ).html( '' );
						$( "#crwcpn-display-warning-notice-description" ).addClass( 'hide' );
					}
				}
			);
		} else if ($( '#crwcpn-global-product-notice-use-display-rules-pt' ).on( 'change' )) {
			$( '#crwcpn-product-notice-display-by-categories-pt, #crwcpn-product-notice-display-by-tags-pt,#crwcpn-product-notice-display-by-products-pt' ).change(
				function () {
					var crwpcn_get_categories_val = $( '#crwcpn-product-notice-display-by-categories-pt' ).val(),
						crwpcn_get_tags_val       = $( '#crwcpn-product-notice-display-by-tags-pt' ).val(),
						crwpcn_get_products_val   = $( '#crwcpn-product-notice-display-by-products-pt' ).val(),
						validation_text           = typeof crwcpn_admin !== undefined && crwcpn_admin.display_rules_validation_text;
					if (crwpcn_get_categories_val.length == 0 && crwpcn_get_tags_val.length == 0 && crwpcn_get_products_val.length == 0) {

						$( "#crwcpn-display-warning-notice-description" ).removeClass( 'hide' );
						$( "#crwcpn-display-warning-notice-description" ).html( '<span id="remove" class="notice notice-warning" style="display: block; margin-top: 0"><p>' + validation_text + '</p></span>' );

					} else if ((crwpcn_get_categories_val.length == 0 && crwpcn_get_tags_val.length != 0) || (crwpcn_get_categories_val.length != 0 && crwpcn_get_tags_val.length == 0) || (crwpcn_get_categories_val.length != 0 && crwpcn_get_products_val.length == 0) || (crwpcn_get_tags_val.length == 0 && crwpcn_get_products_val.length != 0)) {
						$( "#crwcpn-display-warning-notice-description" ).html( '' );
						$( "#crwcpn-display-warning-notice-description" ).addClass( 'hide' );
					}
				}
			);
		}

	},

	/**
	 * Bind behavior to events.
	 */
	ready = function () {
		// Run on document ready.
		colorPicker();
		displayRulesAdmin();
	};

	// Only expose the ready function to the world.
	return {
		ready: ready
	};

})( jQuery );
jQuery( adminScript.ready );
