<?php
/**
 * Registers custom post type for the Product Notices.
 *
 * @package \Product Notices for WooCOmmerce
 * @author Rohit Dokhe
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Add settings and notice fields for product notice using custom fields
 *
 * @since 1.3.0
 */
class CRWCPN_Notice_Meta_Boxes {

	/**
	 * Init Constructor.
	 */
	public function __construct() {

		add_action( 'edit_form_after_editor', array( $this, 'crwcpn_product_notice_post_type' ) );

		add_action( 'save_post', array( $this, 'save' ) );

		// Static assets for admin area.
		add_action( 'admin_enqueue_scripts', array( $this, 'load_assets' ) );
	}

	/**
	 * Loads scripts and styles for edit product notices post type screen.
	 *
	 * @since 1.3.0
	 */
	public function load_assets() {
		$screen    = get_current_screen();
		$screen_id = $screen ? $screen->id : '';

		if ( 'product-notices-cpt' === $screen_id ) {

			$script_file_path = crwcpn()->plugin_url() . '/assets/js/admin/';
			$script_file_name = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? 'admin-settings.js' : 'admin-settings.min.js';

			$edit_screen_script_file_name = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? 'edit-screen.js' : 'edit-screen.min.js';
			wp_enqueue_script( 'crwcpn-edit-screen', $script_file_path . $edit_screen_script_file_name, array(), CRWCPN_VER, true );

			wp_enqueue_style( 'crwcpn-admin-screen', crwcpn()->plugin_url() . '/assets/css/admin/admin-edit-screen.css', array(), CRWCPN_VER );
			wp_enqueue_style( 'crwcpn-admin', crwcpn()->plugin_url() . '/assets/css/admin/admin.css', array(), CRWCPN_VER );

			if ( ! wp_style_is( 'wp-color-picker' ) ) {
				wp_enqueue_style( 'wp-color-picker' );
			}

			if ( ! wp_script_is( 'selectWoo' ) ) {
				wp_enqueue_script( 'selectWoo' );
			}

			wp_enqueue_style( 'crwcpn-select2', plugins_url() . '/woocommerce/assets/css/select2.css', array(), CRWCPN_VER );

			if ( ! wp_script_is( 'crwcpn-admin', 'registered' ) ) {
				wp_register_script( 'crwcpn-admin', $script_file_path . $script_file_name, array( 'wp-color-picker', 'selectWoo' ), CRWCPN_VER, true );
			}

			$i18n_args = array(
				'category_placeholder_text'     => __( 'Select one or more categories', 'product-notices-for-woocommerce' ),
				'tag_placeholder_text'          => __( 'Select one or more tag', 'product-notices-for-woocommerce' ),
				'product_placeholder_text'      => __( 'Select one or more product', 'product-notices-for-woocommerce' ),
				'display_rules_validation_text' => __( 'You will need to select at least one category, tag, or product for the Product Notice to be displayed when Display Rules are enabled.', 'product-notices-woocommerce' ),
			);

			wp_localize_script( 'crwcpn-admin', 'crwcpn_admin', $i18n_args );
			wp_enqueue_script( 'crwcpn-admin' );
		}
	}

	/**
	 * Add Custom fields to Product notices edit post type page.
	 *
	 * @since 1.3.0
	 *
	 * @param WP_Post $post post objects.
	 */
	public function crwcpn_product_notice_post_type( $post ) {

		$screen    = get_current_screen();
		$screen_id = $screen ? $screen->id : '';

		if ( 'product-notices-cpt' === $screen_id ) {

			$custom_style_defaults = crwcpn_custom_style_defaults();

			$crwcpn_product_notice_text_pt = get_post_meta( $post->ID, 'crwcpn_product_notice_pt', 1 );

			$display_rule_global_notice_checkbox_pt = get_post_meta( $post->ID, 'crwcpn_enable_global_product_notice_pt' );
			$pt_global_notice_checkbox              = isset( $display_rule_global_notice_checkbox_pt ) ? $display_rule_global_notice_checkbox_pt : false;
			$global_notice_rule_value               = implode( ',', $pt_global_notice_checkbox );

			$display_rule_notice_checkbox_pt = get_post_meta( $post->ID, 'crwcpn_global_product_notice_use_display_rules_pt' );
			$pt_notice_checkbox              = isset( $display_rule_notice_checkbox_pt ) ? $display_rule_notice_checkbox_pt : false;
			$display_notice_rule_value       = implode( ',', $pt_notice_checkbox );

			$crwcpn_product_categories_pt = get_post_meta( $post->ID, 'crwcpn_product_notice_display_by_categories_pt', true );
			$crwcpn_product_tags_pt       = get_post_meta( $post->ID, 'crwcpn_product_notice_display_by_tags_pt', true );

			$crwcpn_product_name_pt = get_post_meta( $post->ID, 'crwcpn_product_notice_display_by_products_pt', true );

			$crwcpn_product_notice_color_pt = get_post_meta( $post->ID, 'crwcpn_single_product_notice_background_color_pt', true );

			$background_color_pt = get_post_meta( $post->ID, 'crwcpn_single_product_notice_custom_background_color_pt', true );
			$text_color_pt       = get_post_meta( $post->ID, 'crwcpn_single_product_notice_custom_text_color_pt', true );
			$border_color_pt     = get_post_meta( $post->ID, 'crwcpn_single_product_notice_custom_border_color_pt', true );

			$border_radius_pt = get_post_meta( $post->ID, 'crwcpn_product_notice_border_radius_pt', true );

			$background_color_pt = empty( $background_color_pt ) ? $custom_style_defaults['background_color'] : $background_color_pt;
			$text_color_pt       = empty( $text_color_pt ) ? $custom_style_defaults['text_color'] : $text_color_pt;
			$border_color_pt     = empty( $border_color_pt ) ? $custom_style_defaults['border_color'] : $border_color_pt;

			if ( empty( $crwcpn_product_notice_text_pt ) ) {

				$crwcpn_product_notice_text_pt = '';
			}

			$color_options = crwcpn_get_notice_colors();

			$crwcpn_admin_class = new CRWCPN_Admin();

			$product_categories  = $crwcpn_admin_class->crwcpn_get_product_categories();
			$product_tags        = $crwcpn_admin_class->crwcpn_get_product_tags();
			$crwcpn_woo_products = $crwcpn_admin_class->crwcpn_get_all_products();

			wp_nonce_field( 'crwcpn_product_notice_field', 'crwcpn_product_notice_field_nonce' );

			?>
			<div class="crwcpn-product-notice-box">
				<div class="crwcpn-product-notice">
					<h3><?php esc_html_e( 'Notice Text', 'product-notices-woocommerce' ); ?></h3>
					<p>
						<em><?php esc_html_e( 'Enter the information that you wish to show up on your eCommerce website', 'product-notices-woocommerce' ); ?></em><br>
						<textarea id="crwcpn-product-notice-top-pt" class="crwcpn-input-textarea" name="crwcpn_product_notice_top_pt" rows="5" cols="50" placeholder="<?php esc_html__( 'Use of HTML is supported in this field', 'product-notices-woocommerce' ); ?>" style="width: 100%; margin-top: 8px;"><?php echo esc_textarea( $crwcpn_product_notice_text_pt ); ?></textarea>
					</p>
				</div>
			</div>
			<nav class="nav-tab-wrapper wp-clearfix crwcpn-tab-container" aria-label="Secondary menu">
				<ul>
					<li><a id="tab-display-rules" class="nav-tab crwcpn-display-rules-tabs"><span class="dashicons dashicons-admin-settings crwcpn-tab-dashicons"></span>Display Rules</a></li>
					<li><a id="tab-notice-appearance" class="nav-tab crwcpn-display-rules-tabs"><span class="dashicons dashicons-admin-appearance crwcpn-tab-dashicons"></span>Notice Appearance</a></li>
				</ul>
			</nav>

			<div class="tab-pane fade crwcpn-tab-panel tab-display-rules">
				<h3><?php esc_html_e( 'Display rules to show a notice', 'product-notices-woocommerce' ); ?></h3>

				<p><label for="crwcpn_product_notice_info"><em><?php esc_html_e( 'Use to displayed product notice on global or selected product categories, tags and products.', 'product-notices-woocommerce' ); ?></em></label></p>
				<p><label id="crwcpn-display-warning-notice-description"></label></p>
				<table>
					<tbody>
						<tr>
							<td class="crwcpn-cpt-td"><label for="crwcpn_enable_global_notice"><?php esc_html_e( 'Enable Global Notice', 'product-notices-woocommerce' ); ?></label></td>
							<td><span class="dashicons dashicons-editor-help crwcpn-tooltip"><span class="crwcpn-tooltip-text" ><?php esc_html_e( 'This is used to enabel global notice.', 'product-notices-woocommerce' ); ?></span></td>
							<td><input id="crwcpn-enable-global-product-notice-pt" class="crwcpn-input input-checkbox" name="crwcpn_enable_global_product_notice_pt" value="1" <?php checked( $global_notice_rule_value, true ); ?> type="checkbox" /></td>
						</tr>

						<tr>
							<td class="crwcpn-cpt-td"><label for="crwcpn_enable_rules"><?php esc_html_e( 'Enable Rules', 'product-notices-woocommerce' ); ?></label></td>
							<td><span class="dashicons dashicons-editor-help crwcpn-tooltip"><span class="crwcpn-tooltip-text" ><?php esc_html_e( 'This is used to enabel display rules.', 'product-notices-woocommerce' ); ?></span></span></td>
							<td><input id="crwcpn-global-product-notice-use-display-rules-pt" class="crwcpn-input input-checkbox" name="crwcpn_global_product_notice_use_display_rules_pt" value="1" <?php checked( $display_notice_rule_value, true ); ?> type="checkbox" /></td>
						</tr>

						<tr>
							<td class="crwcpn-cpt-td"><label for="crwcpn_show_on_categories"><?php esc_html_e( 'Show on Category', 'product-notices-woocommerce' ); ?></label></td>
							<td><span class="dashicons dashicons-editor-help crwcpn-tooltip"><span class="crwcpn-tooltip-text" ><?php esc_html_e( 'This is used to display product notice by categories.', 'product-notices-woocommerce' ); ?></span></span></td>
							<td>
								<p>
									<select class="wc-enhanced-select crwcpn-select-woo" id="crwcpn-product-notice-display-by-categories-pt" name="crwcpn_product_notice_display_by_categories_pt[]" multiple='multiple' >
										<?php foreach ( $product_categories as $value => $label ) : ?>
											<option value="<?php echo esc_attr( $value ); ?>"
																	<?php
																	if ( ! empty( $crwcpn_product_categories_pt ) ) {
																		selected( in_array( $value, $crwcpn_product_categories_pt ), true );	// phpcs:ignore
																	}
																	?>
											><?php echo esc_html( $label ); ?></option>
										<?php endforeach; ?>
									</select>
								</p>
							</td>
						</tr>

						<tr>
							<td class="crwcpn-cpt-td"><label for="crwcpn_show_on_tags"><?php esc_html_e( 'Show on Tags', 'product-notices-woocommerce' ); ?></label></td>
							<td><span class="dashicons dashicons-editor-help crwcpn-tooltip"><span class="crwcpn-tooltip-text" ><?php esc_html_e( 'This is used to display product notice by tags.', 'product-notices-woocommerce' ); ?></span></span></td>
							<td>
								<p>
									<select class="wc-enhanced-select crwcpn-select-woo" id="crwcpn-product-notice-display-by-tags-pt" name="crwcpn_product_notice_display_by_tags_pt[]" multiple='multiple' >
										<?php foreach ( $product_tags as $value => $label ) : ?>
											<option value="<?php echo esc_attr( $value ); ?>"
																	<?php
																	if ( ! empty( $crwcpn_product_tags_pt ) ) {
																		selected( in_array( $value, $crwcpn_product_tags_pt ), true );	// phpcs:ignore
																	}
																	?>
											><?php echo esc_html( $label ); ?></option>
										<?php endforeach; ?>
									</select>
								</p>
							</td>
						</tr>

						<tr>
							<td class="crwcpn-cpt-td"><label for="crwcpn_show_on_products"><?php esc_html_e( 'Show on Products', 'product-notices-woocommerce' ); ?></label></td>
							<td><span class="dashicons dashicons-editor-help crwcpn-tooltip"><span class="crwcpn-tooltip-text" ><?php esc_html_e( 'This is used to display product notice by products.', 'product-notices-woocommerce' ); ?></span></span></td>
							<td>
								<p>
								<select class="wc-enhanced-select crwcpn-select-woo" id="crwcpn-product-notice-display-by-products-pt" name="crwcpn_product_notice_display_by_products_pt[]" multiple='multiple' >
									<?php foreach ( $crwcpn_woo_products as $value => $label ) : ?>
										<option value="<?php echo esc_attr( $value ); ?>"
																	<?php
																	if ( ! empty( $crwcpn_product_name_pt ) ) {
																		selected( in_array( $value, $crwcpn_product_name_pt ), true );	// phpcs:ignore
																	}
																	?>
											><?php echo esc_html( $label ); ?></option>
									<?php endforeach; ?>
								</select>
								</p>
							</td>
						</tr>
					</tbody>
				</table>
			</div>

			<div class="tab-pane fade crwcpn-tab-panel tab-notice-appearance">
				<div class="crwcpn-product-notice-appearance">
					<h3><?php esc_html_e( 'Notice Appearance', 'product-notices-woocommerce' ); ?></h3>
					<p>
						<label for="crwcpn_product_notice_color_pt"><em><?php esc_html_e( 'Choose from preset styles or configure custom color styles for your notice below.', 'product-notices-woocommerce' ); ?></em></label><br>
						<select id="crwcpn-product-notice-color-pt" name="crwcpn_product_notice_color_pt" style="margin-top: 8px;">
							<?php foreach ( $color_options as $value => $label ) : ?>
								<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $crwcpn_product_notice_color_pt, $value ); ?>><?php echo esc_html( $label ); ?></option>
							<?php endforeach; ?>
						</select>
					</p>

					<?php
					/**
					 * Color picker options.
					 *
					 * @since 1.3.0
					 */
					?>
					<div id="crwcpn-product-notice-custom-style-post-type">
						<p>
							<label for="crwcpn_product_notice_background_color_pt"><?php esc_html_e( 'Background Color', 'product-notices-woocommerce' ); ?></label>
							<span class="dashicons dashicons-editor-help crwcpn-tooltip crwcpn-tooltip-color"><span class="crwcpn-tooltip-text" ><?php esc_html_e( 'This will be used as the border color of the notice box.', 'product-notices-woocommerce' ); ?></span></span>
							<input id="crwcpn-product-notice-background-color-pt" class="crwcpn-input-color" name="crwcpn_product_notice_background_color_pt" value="<?php echo esc_attr( $background_color_pt ); ?>" type="text" data-default-color="<?php echo esc_attr( $custom_style_defaults['background_color'] ); ?>">
						</p>
						<p>
							<label for="crwcpn_product_notice_text_color_pt"><?php esc_html_e( 'Text Color', 'product-notices-woocommerce' ); ?></label>
							<span class="dashicons dashicons-editor-help crwcpn-tooltip crwcpn-tooltip-color"><span class="crwcpn-tooltip-text" ><?php esc_html_e( 'This will be used as the text color for the text in the notice box.', 'product-notices-woocommerce' ); ?></span></span>
							<input type="text" id="crwcpn-product-notice-text-color-pt" class="crwcpn-input-color" name="crwcpn_product_notice_text_color_pt" value="<?php echo esc_attr( $text_color_pt ); ?>" data-default-color="<?php echo esc_attr( $custom_style_defaults['text_color'] ); ?>">
						</p>
						<p>
							<label for="crwcpn_product_notice_border_color_pt"><?php esc_html_e( 'Border Color', 'product-notices-woocommerce' ); ?></label>
							<span class="dashicons dashicons-editor-help crwcpn-tooltip crwcpn-tooltip-color"><span class="crwcpn-tooltip-text" ><?php esc_html_e( 'This will be used as the border color of the notice box.', 'product-notices-woocommerce' ); ?></span></span>
							<input type="text" id="crwcpn-product-notice-border-color-pt" class="crwcpn-input-color" name="crwcpn_product_notice_border_color_pt" value="<?php echo esc_attr( $border_color_pt ); ?>" data-default-color="<?php echo esc_attr( $custom_style_defaults['border_color'] ); ?>">
						</p>
					</div>
				</div>

				<div>
					<table>
						<tr>
							<td class="crwcpn-cpt-td" style="width: 100px !important;">
								<p><label for="crwcpn_border_radius"><?php esc_html_e( 'Border Radius', 'product-notices-woocommerce' ); ?></label></p>
							</td>
							<td><span class="dashicons dashicons-editor-help crwcpn-tooltip"><span class="crwcpn-tooltip-text" ><?php esc_html_e( 'This will be used as the border radius for the notice box.', 'product-notices-woocommerce' ); ?></span></span></td>
							<td class="">
								<p>
									<input type="number" style="width: 100px;" id="crwcpn-product-notice-border-radius-pt" class="" name="crwcpn_product_notice_border_radius_pt" value="<?php echo esc_attr( $border_radius_pt ); ?>" placeholder="<?php esc_html_e( 'px', 'product-notices-woocommerce' ); ?>" />
								</p>
							</td>
						</tr>
					</table>
				</div>
			</div>

			<?php
		}
	}

	/**
	 * Returns a 1 or 0, for all truthy / falsy values.
	 *
	 * Uses double casting. First, we cast to bool, then to integer.
	 *
	 * @param bool $new_value sanitized boolean value.
	 *
	 * @since 1.3.0
	 */
	public static function crwcpn_one_zero( $new_value ) {

		return (int) (bool) $new_value;

	}

	/**
	 * Save post meta data
	 *
	 * @param int $post_id Product ID.
	 *
	 * @since 1.3.0
	 */
	public function save( $post_id ) {
		// If this is an autosave, our form has not been submitted, so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// Check the user's permissions.
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		/* Save/Update product notice. */
		if ( isset( $_POST['crwcpn_product_notice_field_nonce'] ) && wp_verify_nonce( wp_unslash( $_POST['crwcpn_product_notice_field_nonce'] ), 'crwcpn_product_notice_field' ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

			$crwcpn_product_notice_pt = ! empty( $_POST['crwcpn_product_notice_top_pt'] ) ? wp_filter_post_kses( wp_unslash( $_POST['crwcpn_product_notice_top_pt'] ) ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

			update_post_meta( $post_id, 'crwcpn_product_notice_pt', $crwcpn_product_notice_pt );
		}

		/* If checkbox is Selected to show global notice save/update. */
		if ( isset( $_POST['crwcpn_product_notice_field_nonce'] ) && wp_verify_nonce( wp_unslash( $_POST['crwcpn_product_notice_field_nonce'] ), 'crwcpn_product_notice_field' ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

			$crwcpn_enable_global_product_notice_pt = ! empty( $_POST['crwcpn_enable_global_product_notice_pt'] ) ? $this->crwcpn_one_zero( wp_unslash( $_POST['crwcpn_enable_global_product_notice_pt'] ) ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

			update_post_meta( $post_id, 'crwcpn_enable_global_product_notice_pt', $crwcpn_enable_global_product_notice_pt );
		}

		/**
		 * Reset the Display Rules checkbox when no categories and/or tags are selected.
		 *
		 * @since 1.3.0
		 */
		if ( isset( $_POST['crwcpn_product_notice_field_nonce'] ) && wp_verify_nonce( wp_unslash( $_POST['crwcpn_product_notice_field_nonce'] ), 'crwcpn_product_notice_field' ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

			$crwcpn_global_product_notice_use_display_rules_pt = ! empty( $_POST['crwcpn_global_product_notice_use_display_rules_pt'] ) ? $this->crwcpn_one_zero( wp_unslash( $_POST['crwcpn_global_product_notice_use_display_rules_pt'] ) ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

			if ( '' !== $crwcpn_global_product_notice_use_display_rules_pt ) {

				if ( empty( $_POST['crwcpn_product_notice_display_by_categories_pt'] ) && empty( $_POST['crwcpn_product_notice_display_by_tags_pt'] ) && empty( $_POST['crwcpn_product_notice_display_by_products_pt'] ) ) {
					$_POST['crwcpn_global_product_notice_use_display_rules_pt'] = isset( $_POST['crwcpn_global_product_notice_use_display_rules_pt'] ) ? 0 : 1;

					update_post_meta( $post_id, 'crwcpn_global_product_notice_use_display_rules_pt', sanitize_text_field( wp_unslash( $_POST['crwcpn_global_product_notice_use_display_rules_pt'] ) ) );
				} else {

					update_post_meta( $post_id, 'crwcpn_global_product_notice_use_display_rules_pt', $crwcpn_global_product_notice_use_display_rules_pt );
				}
			} else {

				update_post_meta( $post_id, 'crwcpn_global_product_notice_use_display_rules_pt', $crwcpn_global_product_notice_use_display_rules_pt );
			}
		}

		if ( isset( $_POST['crwcpn_product_notice_field_nonce'] ) && wp_verify_nonce( wp_unslash( $_POST['crwcpn_product_notice_field_nonce'] ), 'crwcpn_product_notice_field' ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

			$crwcpn_product_notice_display_by_categories_pt = ! empty( $_POST['crwcpn_product_notice_display_by_categories_pt'] ) ? sanitize_html_class( wp_unslash( $_POST['crwcpn_product_notice_display_by_categories_pt'] ) ) : '';

			$crwcpn_product_notice_display_by_tags_pt = ! empty( $_POST['crwcpn_product_notice_display_by_tags_pt'] ) ? sanitize_html_class( wp_unslash( $_POST['crwcpn_product_notice_display_by_tags_pt'] ) ) : '';

			$crwcpn_product_notice_display_by_products_pt = ! empty( $_POST['crwcpn_product_notice_display_by_products_pt'] ) ? sanitize_html_class( wp_unslash( $_POST['crwcpn_product_notice_display_by_products_pt'] ) ) : '';

			update_post_meta( $post_id, 'crwcpn_product_notice_display_by_categories_pt', $crwcpn_product_notice_display_by_categories_pt );

			update_post_meta( $post_id, 'crwcpn_product_notice_display_by_tags_pt', $crwcpn_product_notice_display_by_tags_pt );

			update_post_meta( $post_id, 'crwcpn_product_notice_display_by_products_pt', $crwcpn_product_notice_display_by_products_pt );
		}

		if ( isset( $_POST['crwcpn_product_notice_field_nonce'] ) && wp_verify_nonce( wp_unslash( $_POST['crwcpn_product_notice_field_nonce'] ), 'crwcpn_product_notice_field' ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

			$crwcpn_product_notice_color_pt = ! empty( $_POST['crwcpn_product_notice_color_pt'] ) ? sanitize_html_class( wp_unslash( $_POST['crwcpn_product_notice_color_pt'] ) ) : '';

			// If custom color is selected for single product notice.
			if ( 'custom_color' === $crwcpn_product_notice_color_pt ) {

				$crwcpn_product_notice_background_color_pt = ! empty( $_POST['crwcpn_product_notice_background_color_pt'] ) ? sanitize_hex_color( wp_unslash( $_POST['crwcpn_product_notice_background_color_pt'] ) ) : '';
				$crwcpn_product_notice_text_color_pt       = ! empty( $_POST['crwcpn_product_notice_text_color_pt'] ) ? sanitize_hex_color( wp_unslash( $_POST['crwcpn_product_notice_text_color_pt'] ) ) : '';
				$crwcpn_product_notice_border_color_pt     = ! empty( $_POST['crwcpn_product_notice_border_color_pt'] ) ? sanitize_hex_color( wp_unslash( $_POST['crwcpn_product_notice_border_color_pt'] ) ) : '';

				/* Update values in meta */
				update_post_meta( $post_id, 'crwcpn_single_product_notice_custom_background_color_pt', $crwcpn_product_notice_background_color_pt );
				update_post_meta( $post_id, 'crwcpn_single_product_notice_custom_text_color_pt', $crwcpn_product_notice_text_color_pt );
				update_post_meta( $post_id, 'crwcpn_single_product_notice_custom_border_color_pt', $crwcpn_product_notice_border_color_pt );

				/* Indicate custom style */
				update_post_meta( $post_id, 'crwcpn_single_product_notice_background_color_pt', $crwcpn_product_notice_color_pt );
			} else {

				update_post_meta( $post_id, 'crwcpn_single_product_notice_background_color_pt', $crwcpn_product_notice_color_pt );
			}
		}

		if ( isset( $_POST['crwcpn_product_notice_field_nonce'] ) && wp_verify_nonce( wp_unslash( $_POST['crwcpn_product_notice_field_nonce'] ), 'crwcpn_product_notice_field' ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

			$crwcpn_product_notice_border_radius_pt = ! empty( $_POST['crwcpn_product_notice_border_radius_pt'] ) ? sanitize_html_class( wp_unslash( $_POST['crwcpn_product_notice_border_radius_pt'] ) ) : '';

			update_post_meta( $post_id, 'crwcpn_product_notice_border_radius_pt', $crwcpn_product_notice_border_radius_pt );
		}
	}
}
