<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://amitmittal.tech
 * @since      1.0.0
 *
 * @package    Bitss_Squiggles_Customizations
 * @subpackage Bitss_Squiggles_Customizations/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Bitss_Squiggles_Customizations
 * @subpackage Bitss_Squiggles_Customizations/includes
 * @author     Amit Mittal <amitmittal@bitsstech.com>
 */
class Bitss_Squiggles_Customizations {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Bitss_Squiggles_Customizations_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'BITSS_SQUIGGLES_CUSTOMIZATIONS_VERSION' ) ) {
			$this->version = BITSS_SQUIGGLES_CUSTOMIZATIONS_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'bitss-squiggles-customizations';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Bitss_Squiggles_Customizations_Loader. Orchestrates the hooks of the plugin.
	 * - Bitss_Squiggles_Customizations_i18n. Defines internationalization functionality.
	 * - Bitss_Squiggles_Customizations_Admin. Defines all hooks for the admin area.
	 * - Bitss_Squiggles_Customizations_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-bitss-squiggles-customizations-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-bitss-squiggles-customizations-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-bitss-squiggles-customizations-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-bitss-squiggles-customizations-public.php';

		$this->loader = new Bitss_Squiggles_Customizations_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Bitss_Squiggles_Customizations_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Bitss_Squiggles_Customizations_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Bitss_Squiggles_Customizations_Admin( $this->get_plugin_name(), $this->get_version() );

			$this->loader->add_action( 'woocommerce_order_status_processing', $plugin_admin, 'woocommerce_order_status_processing' );
			$this->loader->add_action( 'woocommerce_order_status_cancelled', $plugin_admin, 'woocommerce_order_status_cancelled' );
			$this->loader->add_action( 'woocommerce_new_product', $plugin_admin, 'woocommerce_new_product' );
			$this->loader->add_action( 'woocommerce_update_product', $plugin_admin, 'woocommerce_update_product' );
			$this->loader->add_action( 'subscriptions_created_for_order', $plugin_admin, 'subscriptions_created_for_order' );
			$this->loader->add_action( 'woocommerce_checkout_create_order', $plugin_admin, 'woocommerce_checkout_create_order',10,2 );
			$this->loader->add_action( 'woocommerce_email_after_order_table', $plugin_admin, 'woocommerce_email_after_order_table' );
			$this->loader->add_action( 'woocommerce_email', $plugin_admin, 'unhook_woocommerce_email' );
			$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
			$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
			$this->loader->add_action( 'bt_shipment_status_changed', $plugin_admin, 'bt_shipment_status_changed',10,3 );
			$this->loader->add_action( 'admin_init', $plugin_admin, 'admin_init_export_copy' );
			$this->loader->add_filter( 'woocommerce_csv_product_import_mapping_options', $plugin_admin, 'woocommerce_csv_product_import_books_mapping_options' );
			$this->loader->add_filter( 'woocommerce_csv_product_import_mapping_default_columns', $plugin_admin, 'woocommerce_csv_product_import_books_mapping_default_columns' );
			$this->loader->add_filter( 'woocommerce_product_import_pre_insert_product_object', $plugin_admin, 'woocommerce_product_import_pre_insert_product_object_books',20,2 );
			$this->loader->add_filter( 'woocommerce_product_import_inserted_product_object', $plugin_admin, 'woocommerce_product_import_pre_insert_product_object_books_taxonomy_fields',20,2 );
			$this->loader->add_filter( 'woocommerce_order_item_display_meta_key', $plugin_admin, 'woocommerce_order_item_display_meta_key',10,3 );
			$this->loader->add_filter( 'woocommerce_order_item_display_meta_value', $plugin_admin, 'woocommerce_order_item_display_meta_value',10,3 );
			$this->loader->add_action( 'admin_menu', $plugin_admin, 'addPluginAdminMenu', 12 );
			$this->loader->add_action( 'add_meta_boxes', $plugin_admin, 'add_meta_boxes_edit_order_page' );
			$this->loader->add_action( 'edit_user_profile', $plugin_admin, 'wk_custom_user_profile_fields' );
			$this->loader->add_action( 'edit_user_profile_update', $plugin_admin, 'wk_save_custom_user_profile_fields' );
			$this->loader->add_action( 'woocommerce_admin_order_data_after_order_details', $plugin_admin, 'woocommerce_admin_order_data_after_order_details' );
			$this->loader->add_action( 'admin_init', $plugin_admin, 'init_set_capabilities',999 );
			$this->loader->add_action( 'woocommerce_subscription_totals_table',$plugin_admin,'add_deposit_damage_history_tab' );

			if(is_admin()){
				$this->loader->add_action(' wp_ajax_process_refund',$plugin_admin, 'process_refund');
				$this->loader->add_action(' wp_ajax_nopriv_process_refund',$plugin_admin, 'process_refund');
				$this->loader->add_action( 'wp_ajax_save_subscription_damage', $plugin_admin, 'send_save_subscription_damage' );
				$this->loader->add_action( 'wp_ajax_save_slots_data', $plugin_admin, 'save_slots_data' );
				$this->loader->add_action( 'wp_ajax_save_issue_copy_id', $plugin_admin, 'save_issue_copy_id' );
				$this->loader->add_action( 'wp_ajax_get_all_perticular_book_id_arr', $plugin_admin, 'get_all_perticular_book_id_arr' );
				$this->loader->add_action( 'wp_ajax_nopriv_api_call_search_for_user', $plugin_admin, 'api_call_search_for_user' );
				$this->loader->add_action( 'wp_ajax_api_call_search_for_user', $plugin_admin, 'api_call_search_for_user' );
			
				
			}

			//phase 3 books import ajax functions
			//if(is_admin()){
				
				$this->loader->add_action( 'wp_ajax_phase_3_search_isbn', $plugin_admin, 'phase_3_search_isbn' );
				$this->loader->add_action( 'wp_ajax_nopriv_phase_3_search_isbn', $plugin_admin, 'phase_3_search_isbn' );

				$this->loader->add_action( 'wp_ajax_phase_3_search_title_author', $plugin_admin, 'phase_3_search_title_author' );
				$this->loader->add_action( 'wp_ajax_nopriv_phase_3_search_title_author', $plugin_admin, 'phase_3_search_title_author' );

				$this->loader->add_action( 'wp_ajax_phase_3_create_product', $plugin_admin, 'phase_3_create_product' );
				$this->loader->add_action( 'wp_ajax_nopriv_phase_3_create_product', $plugin_admin, 'phase_3_create_product' );

				$this->loader->add_action( 'wp_ajax_phase_3_add_book_copy', $plugin_admin, 'phase_3_add_book_copy' );
				$this->loader->add_action( 'wp_ajax_nopriv_phase_3_add_book_copy', $plugin_admin, 'phase_3_add_book_copy' );


				$this->loader->add_action( 'wp_ajax_phase_3_get_authors', $plugin_admin, 'phase_3_get_authors' );
				
			//}
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Bitss_Squiggles_Customizations_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_shortcode( 'bt_login_form', $plugin_public,  'do_shortcode_bt_login_form' );
		$this->loader->add_shortcode( 'bt_add_to_wishlist', $plugin_public,  'bt_add_to_wishlist' );
		$this->loader->add_shortcode( 'bt_add_to_cart', $plugin_public,  'bt_add_to_cart' );
		$this->loader->add_shortcode( 'bt_book_shelf', $plugin_public,  'bt_book_shelf' );
		$this->loader->add_shortcode( 'bt_user_plan', $plugin_public,  'bt_user_plan' );
		$this->loader->add_shortcode( 'bt_user_issue_history', $plugin_public,  'bt_user_issue_history' );
		$this->loader->add_shortcode( 'bt_library_poc', $plugin_public,  'bt_library_poc' );

		$this->loader->add_action( 'elementor_pro/forms/actions/register', $plugin_public, 'add_elementor_register_form_action' );
		$this->loader->add_action( 'init', $plugin_public, 'redirect_product_smart_id' );
		
		$this->loader->add_action( 'wp_head', $plugin_public, 'wp_head' );
		$this->loader->add_action( 'wp_footer', $plugin_public, 'wp_footer_bitss_backlink',999 );
		$this->loader->add_action( 'pre_get_posts', $plugin_public, 'pre_get_posts' );
		$this->loader->add_action( 'woocommerce_after_shop_loop_item', $plugin_public, 'woocommerce_after_shop_loop_item' );
		$this->loader->add_action( 'woocommerce_after_checkout_validation', $plugin_public, 'woocommerce_after_checkout_validation',10,2 );
		$this->loader->add_action( 'woocommerce_before_cart', $plugin_public, 'woocommerce_before_cart');
		$this->loader->add_action( 'woocommerce_cart_is_empty', $plugin_public, 'woocommerce_cart_is_empty');
		$this->loader->add_action( 'woocommerce_is_purchasable', $plugin_public, 'woocommerce_is_purchasable',10,2);
		$this->loader->add_action( 'woocommerce_checkout_before_order_review_heading', $plugin_public, 'display_checkout_fields',10);
		$this->loader->add_action( 'woocommerce_checkout_process', $plugin_public, 'kaarot_validate_checkout_fields',10 );
		$this->loader->add_action( 'woocommerce_checkout_update_order_meta', $plugin_public, 'kaarot_checkout_update_order_meta',10 );
		$this->loader->add_action( 'manage_shop_order_posts_custom_column', $plugin_public, 'custom_orders_list_column_content',10,2 );
		$this->loader->add_action( 'woocommerce_checkout_create_order_line_item', $plugin_public, 'woocommerce_checkout_create_order_line_item',10,4);
		$this->loader->add_action( 'woocommerce_thankyou', $plugin_public, 'woocommerce_thankyou',99,1);
		$this->loader->add_action( 'woocommerce_proceed_to_checkout', $plugin_public, 'woocommerce_proceed_to_checkout',99);
		$this->loader->add_action( 'init', $plugin_public, 'bt_add_reading_history_endpoint');
		$this->loader->add_action( 'init', $plugin_public, 'init_book_shelf_data');
		$this->loader->add_action( 'woocommerce_account_reading-history_endpoint', $plugin_public, 'woocommerce_account_reading_history_endpoint');
		$this->loader->add_action( 'yith_wcwl_after_wishlist_form', $plugin_public, 'yith_wcwl_after_wishlist_form',99,1);
		$this->loader->add_action( 'woocommerce_cart_loaded_from_session', $plugin_public, 'woocommerce_cart_loaded_from_session',99);
		if ( is_admin() ) {

			$this->loader->add_action( 'wp_ajax_otp_resend', $plugin_public, 'otp_resend' );
			$this->loader->add_action( 'wp_ajax_nopriv_otp_resend', $plugin_public, 'otp_resend' );

			$this->loader->add_action( 'wp_ajax_kaarot_get_schedule_date', $plugin_public, 'kaarot_get_schedule_date' );
			$this->loader->add_action( 'wp_ajax_nopriv_kaarot_get_schedule_date', $plugin_public, 'kaarot_get_schedule_date' );

			$this->loader->add_action( 'wp_ajax_kaarot_get_schedule_time', $plugin_public, 'kaarot_get_schedule_time' );
			$this->loader->add_action( 'wp_ajax_nopriv_kaarot_get_schedule_time', $plugin_public, 'kaarot_get_schedule_time' );

		}
		
		$this->loader->add_filter( 'woocommerce_currencies', $plugin_public, 'woocommerce_currencies' );
		$this->loader->add_filter( 'woocommerce_currency_symbol', $plugin_public, 'woocommerce_currency_symbol', 20, 2 );
		$this->loader->add_filter( 'woocommerce_product_is_in_stock', $plugin_public, 'woocommerce_product_is_in_stock', 20, 2 );
		$this->loader->add_filter( 'woocommerce_product_add_to_cart_text', $plugin_public, 'woocommerce_product_add_to_cart_text', 20, 2 );
		$this->loader->add_filter( 'astra_get_search_form', $plugin_public, 'get_search_form' );
		$this->loader->add_filter( 'get_search_form', $plugin_public, 'get_search_form' );
		$this->loader->add_filter( 'woocommerce_product_tabs', $plugin_public, 'woocommerce_product_tabs',98 );
		$this->loader->add_filter( 'woocommerce_catalog_orderby', $plugin_public, 'woocommerce_catalog_orderby',10 );
		$this->loader->add_filter( 'woocommerce_cart_calculate_fees', $plugin_public, 'woocommerce_cart_calculate_fees',10 );
		$this->loader->add_filter( 'woocommerce_cart_item_price', $plugin_public, 'woocommerce_cart_item_price',10 ,3);
		$this->loader->add_filter( 'woocommerce_get_price_html', $plugin_public, 'woocommerce_get_price_html',10 ,2);
	
		$this->loader->add_filter( 'woocommerce_cart_item_subtotal', $plugin_public, 'woocommerce_cart_item_subtotal',10 ,3);
		$this->loader->add_filter( 'woocommerce_cart_subtotal', $plugin_public, 'woocommerce_cart_subtotal',10 ,3);
		$this->loader->add_filter( 'woocommerce_cart_totals_order_total_html', $plugin_public, 'woocommerce_cart_totals_order_total_html',10);
		$this->loader->add_filter( 'woocommerce_cart_needs_payment', $plugin_public, 'woocommerce_cart_needs_payment',10,2);
		$this->loader->add_filter( 'woocommerce_order_button_html', $plugin_public, 'woocommerce_order_button_html',10);

		$this->loader->add_filter( 'woocommerce_add_cart_item_data', $plugin_public, 'woocommerce_add_cart_item_data',10,2);
		$this->loader->add_filter( 'woocommerce_loop_add_to_cart_link', $plugin_public, 'woocommerce_loop_add_to_cart_link',10,3);
		$this->loader->add_filter( 'woocommerce_package_rates', $plugin_public, 'woocommerce_package_rates',99);

		$this->loader->add_filter( 'manage_edit-shop_order_columns', $plugin_public, 'custom_shop_order_column',99);
		$this->loader->add_filter( 'woocommerce_get_formatted_order_total', $plugin_public, 'woocommerce_get_formatted_order_total',99,2);
		$this->loader->add_filter( 'woocommerce_order_formatted_line_subtotal', $plugin_public, 'woocommerce_order_formatted_line_subtotal',99,3);
		$this->loader->add_filter( 'woocommerce_checkout_fields', $plugin_public, 'woocommerce_checkout_fields',99);
		$this->loader->add_filter( 'woocommerce_cart_item_name', $plugin_public, 'woocommerce_cart_item_name',99,3);
		$this->loader->add_filter( 'woocommerce_is_sold_individually', $plugin_public, 'woocommerce_is_sold_individually',99,2);

		$this->loader->add_filter( 'woocommerce_get_discounted_price', $plugin_public, 'woocommerce_get_discounted_price',99,3);
		$this->loader->add_filter( 'query_vars', $plugin_public, 'bt_reading_history_query_vars');
		$this->loader->add_filter( 'woocommerce_account_menu_items', $plugin_public, 'woocommerce_account_menu_items',9,1);

		$this->loader->add_action( 'wc_get_template', $plugin_public, 'intercept_wc_get_template',99, 5 );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Bitss_Squiggles_Customizations_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
