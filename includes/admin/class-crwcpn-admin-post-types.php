<?php
/**
 * Create a custom post type Product Notices for WooCommerce notices.
 *
 * @package \Product Notices for WooCommerce notices
 * @author Rohit Dokhe
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Displays Product Notices post type.
 *
 * @since 1.3.0
 */
class CRWCPN_Admin_Post_Types {


	/**
	 *   Constructor
	 */
	public function __construct() {
		include_once __DIR__ . '/class-crwcpn-notice-meta-boxes.php';

		add_action( 'init', array( $this, 'register' ) );

		add_filter( 'post_updated_messages', array( $this, 'post_updated_messages' ) );
		add_filter( 'bulk_post_updated_messages', array( $this, 'bulk_post_updated_messages' ), 10, 2 );

		add_filter( 'manage_product-notices-cpt_posts_columns', array( $this, 'set_custom_edit_product_notices_columns' ) );
		add_action( 'manage_product-notices-cpt_posts_custom_column', array( $this, 'crwcpn_custom_notice_column' ), 10, 2 );

		add_action( 'admin_enqueue_scripts', array( $this, 'load_assets' ) );
		add_action( 'wp_ajax_update_notice_status', array( $this, 'update_notice_status' ) );
	}

	/**
	 *   Register 'Product Notices' post type.
	 */
	public function register() {
		$product_notices_pt_labels = array(
			'name'                  => _x( 'Product Notices', 'Post Type General Name', 'product-notices-woocommerce' ),
			'singular_name'         => _x( 'Product Notice', 'Post Type Singular Name', 'product-notices-woocommerce' ),
			'menu_name'             => __( 'Product Notices', 'product-notices-woocommerce' ),
			'name_admin_bar'        => __( 'Product Notice', 'product-notices-woocommerce' ),
			'archives'              => __( 'Product Notice Archives', 'product-notices-woocommerce' ),
			'attributes'            => __( 'Product Notice Attributes', 'product-notices-woocommerce' ),
			'parent_item_colon'     => __( 'Parent Product Notice:', 'product-notices-woocommerce' ),
			'all_items'             => __( 'All Product Notices', 'product-notices-woocommerce' ),
			'add_new_item'          => __( 'Add Product Notice', 'product-notices-woocommerce' ),
			'add_new'               => __( 'Add New', 'product-notices-woocommerce' ),
			'new_item'              => __( 'New Product Notice', 'product-notices-woocommerce' ),
			'edit_item'             => __( 'Edit Product Notice', 'product-notices-woocommerce' ),
			'update_item'           => __( 'Update Product Notice', 'product-notices-woocommerce' ),
			'view_item'             => __( 'View Product Notice', 'product-notices-woocommerce' ),
			'view_items'            => __( 'View Product Notices', 'product-notices-woocommerce' ),
			'search_items'          => __( 'Search Product Notice', 'product-notices-woocommerce' ),
			'not_found'             => __( 'Not found', 'product-notices-woocommerce' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'product-notices-woocommerce' ),
			'insert_into_item'      => __( 'Insert into Product Notice', 'product-notices-woocommerce' ),
			'uploaded_to_this_item' => __( 'Uploaded to this Product Notice', 'product-notices-woocommerce' ),
			'items_list'            => __( 'Product Notice list', 'product-notices-woocommerce' ),
			'items_list_navigation' => __( 'Product Notices list navigation', 'product-notices-woocommerce' ),
			'filter_items_list'     => __( 'Filter Product Notices list', 'product-notices-woocommerce' ),

		);

		$product_notice_pt_args = array(
			'label'               => __( 'Product Notice', 'product-notices-woocommerce' ),
			'description'         => __( 'Add, edit, delete product notices and configure them to show on the website.', 'product-notices-woocommerce' ),
			'labels'              => $product_notices_pt_labels,
			'supports'            => array( 'title' ),
			'hierarchical'        => false,
			'public'              => false, // it's not public, it shouldn't have it's own permalink, and so on.
			'show_ui'             => true,
			'show_in_menu'        => true,
			'menu_position'       => 57,
			'menu_icon'           => 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMTUiIHZpZXdCb3g9IjAgMCAxOTAgMTQ0IiBmaWxsPSJub25lIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPgo8cGF0aCBkPSJNMTg5LjI4OCAxMDQuNDgxQzE4OS4yODggMTIyLjM0NyAxNzQuODA1IDEzNi44MyAxNTYuOTM5IDEzNi44M0MxMzkuMDczIDEzNi44MyAxMjQuNTg5IDEyMi4zNDcgMTI0LjU4OSAxMDQuNDgxQzEyNC41ODkgODYuNjE0NyAxMzkuMDczIDcyLjEzMTMgMTU2LjkzOSA3Mi4xMzEzQzE3NC44MDUgNzIuMTMxMyAxODkuMjg4IDg2LjYxNDcgMTg5LjI4OCAxMDQuNDgxWiIgZmlsbD0iI0E3QUFBRCIvPgo8cGF0aCBkPSJNMTUyLjQyIDExOC44NDFIMTYxLjg5M0MxNjEuODkzIDEyMC41MzMgMTYwLjk5IDEyMi4wOTYgMTU5LjUyNSAxMjIuOTQzQzE1OC4wNTkgMTIzLjc4OCAxNTYuMjU0IDEyMy43ODggMTU0Ljc4OSAxMjIuOTQzQzE1My4zMjMgMTIyLjA5NiAxNTIuNDIgMTIwLjUzMyAxNTIuNDIgMTE4Ljg0MVpNMTU3LjM0NiA4NS42ODc5QzE1Ni4zNzIgODUuNjM1OSAxNTUuNDE5IDg1Ljk4NzMgMTU0LjcxMSA4Ni42NTkyQzE1NC4wMDMgODcuMzMxMSAxNTMuNjAzIDg4LjI2NDQgMTUzLjYwNCA4OS4yNDAyVjkxLjAxNjNDMTUxLjUyNCA5MS43NTE4IDE0OS43MjMgOTMuMTE1MyAxNDguNDUxIDk0LjkxNzlDMTQ3LjE3OCA5Ni43MjA1IDE0Ni40OTcgOTguODc0IDE0Ni41IDEwMS4wOFYxMDQuMDQxQzE0Ni41IDEwNS45MjMgMTQ1Ljc1MyAxMDcuNzI5IDE0NC40MjIgMTA5LjA2MUwxNDMuOTMxIDEwOS41NTJDMTQzLjQ0NCAxMTAuMDQxIDE0My4xMTkgMTEwLjY2OCAxNDMgMTExLjM0OEMxNDIuODgyIDExMi4wMjggMTQyLjk3NSAxMTIuNzI4IDE0My4yNjggMTEzLjM1M0MxNDMuNTU2IDExMy45NDMgMTQ0LjAwNyAxMTQuNDM5IDE0NC41NjcgMTE0Ljc4MUMxNDUuMTI3IDExNS4xMjQgMTQ1Ljc3MyAxMTUuMzAxIDE0Ni40MjkgMTE1LjI4OUgxNjguMDE0QzE2OC45MDMgMTE1LjI4OSAxNjkuNzU2IDExNC45MzYgMTcwLjM4NCAxMTQuMzA3QzE3MS4wMTMgMTEzLjY3OSAxNzEuMzY1IDExMi44MjcgMTcxLjM2NSAxMTEuOTM4VjExMS43OUMxNzEuMzY1IDExMC45OTEgMTcxLjA4IDExMC4yMTggMTcwLjU2IDEwOS42MTJMMTY5LjUyNCAxMDguNDI4QzE2OC40MTUgMTA3LjEzNCAxNjcuODA4IDEwNS40ODQgMTY3LjgxMyAxMDMuNzhWMTAxLjA4MUMxNjcuODE3IDk4Ljg3NDEgMTY3LjEzNSA5Ni43MjA2IDE2NS44NjMgOTQuOTE4QzE2NC41OSA5My4xMTU0IDE2Mi43OSA5MS43NTE5IDE2MC43MDkgOTEuMDE2NFY4OS40NDE4QzE2MC43MjkgODguNTA2NCAxNjAuMzkzIDg3LjU5OCAxNTkuNzY5IDg2LjkwMTNDMTU5LjE0NSA4Ni4yMDQyIDE1OC4yNzggODUuNzcwMiAxNTcuMzQ2IDg1LjY4NzlaIiBmaWxsPSJ3aGl0ZSIvPgo8cGF0aCBkPSJNMTE3LjMzNSAxMTAuNzMzQzExNy41MjMgMTExLjgzNiAxMTcuMjEzIDExMi45NjcgMTE2LjQ5MSAxMTMuODI0QzExNS43NjkgMTE0LjY3OSAxMTQuNzA3IDExNS4xNzMgMTEzLjU4NiAxMTUuMTczSDc4Ljk0ODlMNTEuODI4MiAxNDIuMzMxSDUxLjgyOTlDNTAuMTY1IDE0My45MzYgNDcuNzA3NyAxNDQuNDAxIDQ1LjU3MzQgMTQzLjUxM0M0My40Mzg4IDE0Mi42MjcgNDIuMDM1MSAxNDAuNTU3IDQxLjk5OTMgMTM4LjI0NVYxMTUuMTczSDE1LjMwMDRDMTEuMjQxNCAxMTUuMTczIDcuMzQ5NDYgMTEzLjU2MSA0LjQ4MDg0IDExMC42OTJDMS42MTIyMyAxMDcuODIzIDAgMTAzLjkzMSAwIDk5Ljg3MjVWMTUuMzAwNEMwLjAwNTEyMzAyIDExLjI0NDggMS42MTg4MyA3LjM1NDcgNC40ODYwOSA0LjQ4Nzg0QzcuMzU0OTIgMS42MTkwMSAxMS4yNDMyIDAuMDA1MjQ1ODYgMTUuMzAwNCAwSDE2MC42NTVDMTY0LjcxIDAuMDA1MTIyNTkgMTY4LjU5OSAxLjYxODgzIDE3MS40NjcgNC40ODc4NEMxNzQuMzM2IDcuMzU0OTcgMTc1Ljk1IDExLjI0NDkgMTc1Ljk1NSAxNS4zMDA0VjYyLjk1OTFDMTc1Ljk0MyA2NC4yMjI3IDE3NS4zMTEgNjUuNDAxIDE3NC4yNjMgNjYuMTA5N0MxNzMuMjE2IDY2LjgxNjcgMTcxLjg4NyA2Ni45NjUyIDE3MC43MDkgNjYuNTA0MUMxNDIuOTAyIDU1LjUwNzEgMTExLjkxMiA4MS42NTk0IDExNy4zMzcgMTEwLjczNkwxMTcuMzM1IDExMC43MzNaIiBmaWxsPSIjQTdBQUFEIi8+Cjwvc3ZnPgo=',
			'show_in_admin_bar'   => true,
			'show_in_nav_menus'   => true,
			'can_export'          => true,
			'has_archive'         => true,
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'query_var'           => true,
			'rewrite'             => true,
			'show_in_rest'        => true,
		);

		register_post_type( CRWCPN_PT_SLUG, $product_notice_pt_args );
	}

	/**
	 *   Filters the post updated messages.
	 *
	 * @param array $messages Array of Post updated messages.
	 */
	public function post_updated_messages( $messages ) {

		$post = get_post();

		// Product notices post.
		$messages[ CRWCPN_PT_SLUG ] = array(
			0  => '',   // Unused. Messages start at index 1.
			1  => __( 'Product notice updated.', 'product-notices-woocommerce' ),
			4  => __( 'Product notice updated.', 'product-notices-woocommerce' ),
			5  => isset( $_GET['revision'] ) ? sprintf( __( 'Product notice restored to revision from %s', 'product-notices-woocommerce' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,	// @codingStandardsIgnoreLine
			6  => __( 'Product notice published.', 'product-notices-woocommerce' ),	// @codingStandardsIgnoreLine
			7  => __( 'Product notice saved.', 'product-notices-woocommerce' ),
			8  => __( 'Product notice submitted.', 'product-notices-woocommerce' ),
			9  => sprintf(
				/* translators: %s: date */
				__( 'Product notice scheduled for: %s.', 'product-notices-woocommerce' ),
				'<strong>' . date_i18n( __( 'M j, Y @ G:i', 'product-notices-woocommerce' ), strtotime( $post->post_date ) ) . '</strong>'
			),
			10 => __( 'Product notice draft updated.', 'product-notices-woocommerce' ),
		);

		return $messages;
	}

	/**
	 * Specify custom bulk actions messages for different post types.
	 *
	 * @param  array $bulk_messages Array of messages.
	 * @param  array $bulk_counts Array of how many objects were updated.
	 * @return array
	 */
	public function bulk_post_updated_messages( $bulk_messages, $bulk_counts ) {
		$bulk_messages[ CRWCPN_PT_SLUG ] = array(
			/* translators: %s: product-notices-cpt count */
			'updated'   => _n( '%s product notice updated.', '%s product notices updated.', $bulk_counts['updated'], 'product-notices-woocommerce' ),
			/* translators: %s: product-notices-cpt count */
			'locked'    => _n( '%s product notice not updated, somebody is editing it.', '%s product notices not updated, somebody is editing them.', $bulk_counts['locked'], 'product-notices-woocommerce' ),
			/* translators: %s: product-notices-cpt count */
			'deleted'   => _n( '%s product notice permanently deleted.', '%s product notices permanently deleted.', $bulk_counts['deleted'], 'product-notices-woocommerce' ),
			/* translators: %s: product-notices-cpt count */
			'trashed'   => _n( '%s product notice moved to the Trash.', '%s product notices moved to the Trash.', $bulk_counts['trashed'], 'product-notices-woocommerce' ),
			/* translators: %s: product-notices-cpt count */
			'untrashed' => _n( '%s product notice restored from the Trash.', '%s product notices restored from the Trash.', $bulk_counts['untrashed'], 'product-notices-woocommerce' ),
		);

		return $bulk_messages;
	}

	/**
	 *   Add the custom columns to the product notices post type.
	 *
	 * @param array $columns An array of sortable columns.
	 */
	public function set_custom_edit_product_notices_columns( $columns ) {
		unset( $columns['author'] );

		$columns['switch_toggle']            = __( 'Status', 'product-notices-woocommerce' );
		$columns['product_categories']       = __( 'Categories', 'product-notices-woocommerce' );
		$columns['product_tags']             = __( 'Tags', 'product-notices-woocommerce' );
		$columns['crwcpn_notice_shortcodes'] = __( 'Shortcode', 'product-notices-woocommerce' );

		return $columns;
	}

	/**
	 *   Add the data to the custom columns for the book post type.
	 *
	 * @param array $column An array of sortable columns.
	 * @param int   $post_id is a post id.
	 */
	public function crwcpn_custom_notice_column( $column, $post_id ) {

		switch ( $column ) {

			case 'switch_toggle':
				$post_id      = get_the_ID();
				$option_val   = get_post_status( $post_id ) === 'publish' ? '1' : '';
				$option_check = get_post_status( $post_id ) === 'publish' ? 'checked ="checked"' : '';

				?>

				<table>
					<tr>
						<th>
							<div class="crwcpn-switch-toggle">
								<label><input id="<?php echo esc_attr( $post_id ); ?>" class="crwcpn-toggle-link" value="<?php echo esc_attr( $option_val ); ?>" type="checkbox" <?php echo esc_attr( $option_check ); ?>><span></span></label>
							</div>
						</th>
						<th>
							<div class="toggle-loader-<?php echo sanitize_html_class( $post_id ); ?>"></div>
						</th>
					</tr>
				</table>

				<?php

				break;

			case 'product_categories':
				$product_categories = get_post_meta( $post_id, 'crwcpn_product_notice_display_by_categories_pt' );

				foreach ( $product_categories as $value ) {
					if ( ! empty( $value ) ) {
						$cat_name = array();
						foreach ( $value as $data ) {
							$cat_name[] = get_the_category_by_ID( $data );
						}
						$enable_categories_list = implode( ', ', $cat_name );
						echo $enable_categories_list; // phpcs:ignore WordPress.Security.EscapeOutput 
					}
				}

				break;

			case 'product_tags':
				$product_tags = get_post_meta( $post_id, 'crwcpn_product_notice_display_by_tags_pt' );

				foreach ( $product_tags as $value ) {
					if ( ! empty( $value ) ) {
						$tags_name = array();
						foreach ( $value as $data ) {
							$tags_name[] = get_the_category_by_ID( $data );
						}
						$enable_tags_list = implode( ', ', $tags_name );
						echo $enable_tags_list; // phpcs:ignore WordPress.Security.EscapeOutput 
					}
				}

				break;

			case 'crwcpn_notice_shortcodes':
				$id = get_the_ID();

				?>
				<div class="validate">
					<?php

					echo "<a id='code-' class='crwcpn-clipboard-code c2c' data-clipboard-text='[crwcpn-notice id=" . '"' . intval( $id ) . '"' . "]'>[crwcpn-notice id=" . '"' . intval( $id ) . '"]</a>';

					?>
				</div>

				<div id="crwcpn_snackbar"><?php esc_html_e( 'Copied to Clipboard', 'product-notices-woocommerce' ); ?></div>

				<?php

				break;
		}
	}

	/**
	 * Loads JS and CSS for post type admin page.
	 *
	 * @since 1.3.0
	 */
	public function load_assets() {
		$screen    = get_current_screen();
		$screen_id = $screen ? $screen->id : '';

		if ( 'edit-product-notices-cpt' === $screen_id ) {

			$script_file_path = crwcpn()->plugin_url() . '/assets/js/admin/';
			$script_file_name = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? 'edit-screen.js' : 'edit-screen.min.js';

			wp_enqueue_style( 'crwcpn-admin-screen', crwcpn()->plugin_url() . '/assets/css/admin/admin-edit-screen.css', array(), CRWCPN_VER );
			wp_enqueue_style( 'crwcpn-admin', crwcpn()->plugin_url() . '/assets/css/admin/admin.css', array(), CRWCPN_VER );
			wp_enqueue_script( 'crwcpn-admin-c2c', crwcpn()->plugin_url() . '/assets/js/admin/c2c.min.js', array(), CRWCPN_VER, true );
			wp_enqueue_script( 'crwcpn-edit-screen', $script_file_path . $script_file_name, array(), CRWCPN_VER, true );

			$edit_screen_script_file_name = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? 'edit-screen.js' : 'edit-screen.min.js';
			wp_register_script( 'crwcpn-edit-screen', $script_file_path . $edit_screen_script_file_name, array( 'jquery' ), CRWCPN_VER, true );

			wp_localize_script(
				'crwcpn-edit-screen',
				'crwcpn_notice_status_update_Ajax', // notice status updated.
				array(
					'ajaxurl'   => admin_url( 'admin-ajax.php' ),
					'ajaxnonce' => wp_create_nonce( 'ajax_post_validation' ), // notice status validation.
				)
			);

			// enqueue jQuery library and the script you registered above.
			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'crwcpn-edit-screen' );
		}
	}

	/**
	 * Update a post with new post data.
	 *
	 * @since 1.3.0
	 */
	public function update_notice_status() {

		$post_id = sanitize_text_field( wp_unslash( $_POST['post_id'] ) );	// @codingStandardsIgnoreLine

		if ( ! empty( $post_id ) ) {
			$post_status = get_post_status( $post_id );

			$post = array(
				'ID'          => $post_id,
				'post_status' => $post_status !== 'publish' ? 'publish' : 'draft',	// @codingStandardsIgnoreLine
			);

			wp_update_post( $post );
		}
		wp_die();
	}

}



