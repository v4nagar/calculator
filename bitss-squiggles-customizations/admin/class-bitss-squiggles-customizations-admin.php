<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://amitmittal.tech
 * @since      1.0.0
 *
 * @package    Bitss_Squiggles_Customizations
 * @subpackage Bitss_Squiggles_Customizations/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Bitss_Squiggles_Customizations
 * @subpackage Bitss_Squiggles_Customizations/admin
 * @author     Amit Mittal <amitmittal@bitsstech.com>
 */
class Bitss_Squiggles_Customizations_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;
	private $book_shelf_obj;
	private $books_shelf;
	private $isbndb_obj;
	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/services/isbndb.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-bitss-squiggles-customizations-book-shelf.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/models/bt_sq_book.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/models/bt_sq_book_copy.php';
		$this->book_shelf_obj = new Bitss_Squiggles_Customizations_Book_Shelf( $this->plugin_name, $this->version );
		$this->isbndb_obj = new Bt_Squiggles_Isbndb();
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Bitss_Squiggles_Customizations_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Bitss_Squiggles_Customizations_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		$current_screen = get_current_screen();
		wp_register_style('bulma', plugin_dir_url(__FILE__) . 'css/bulma.min.css');
		wp_register_style('isteven-multi-select', plugin_dir_url(__FILE__) . 'css/isteven-multi-select.css');
		//echo json_encode($current_screen);exit;
		if($current_screen!=null && ($current_screen->id=="woocommerce_page_wc-admin-squiggles-settings" || $current_screen->id=="shop_order"|| $current_screen->id=="product")){

		
			wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/bitss-squiggles-customizations-admin.css', array('bulma'), $this->version, 'all');
		}
	
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Bitss_Squiggles_Customizations_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Bitss_Squiggles_Customizations_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		$current_screen = get_current_screen();
		//echo json_encode($current_screen);exit;
		if($current_screen!=null && ($current_screen->id=="woocommerce_page_wc-admin-squiggles-settings" || $current_screen->id=="shop_order"|| $current_screen->id=="product")){

			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/bitss-squiggles-customizations-admin.js', array( 'jquery' ), $this->version, false );
		}
		wp_localize_script(	$this->plugin_name, 'bitss_squiggles_vars',
			array(
				'ajax_url' => admin_url('admin-ajax.php'),
				"save_issue_copy_id_nonce" => wp_create_nonce('save_issue_copy_id'),
				"show_all_copy_id_dropdown_nonce" => wp_create_nonce('get_all_perticular_book_id_arr'),
			)
		);
		wp_register_script($this->plugin_name . '-isteven-multi-select', plugin_dir_url(__FILE__) . 'js/isteven-multi-select.js', array('jquery'), $this->version, false);	
		wp_register_script($this->plugin_name . '-pos', plugin_dir_url(__FILE__) . 'js/pos.js', array('jquery'), $this->version, false);	
		wp_localize_script(	$this->plugin_name. '-pos', 'bitss_squiggles_pos_vars',
		array(
			'ajax_url' => admin_url('admin-ajax.php'),
			"search_user_nonce" => wp_create_nonce('api_call_search_for_user'),
		)
		);

		wp_register_script($this->plugin_name . '-books-import', plugin_dir_url(__FILE__) . 'js/books_import.js', array('jquery',$this->plugin_name . '-isteven-multi-select'), $this->version, false);	
	}

	function api_call_search_for_user()
	{
		$nonce = $_GET["nonce"];
		// $otp_login_redirect = isset($_GET["otp_login_redirect"])?$_GET["otp_login_redirect"]:"";

		if (!wp_verify_nonce($nonce, 'api_call_search_for_user')) {
			exit;
		}
		$users = [];
		$response = array(
			"status" => false,
			"data" => null,
			"message" => "User not found!"
		);

		$value = $_GET["input"];

		if(empty($value)){
			$response = array(
				"status" => false,
				"data" => null,
				"message" =>  "please enter the required details"
			);
			wp_send_json($response);
			die();
		}

		$search_string = $value;

		$users = new WP_User_Query( array(
			'role__in' => array('customer','subscriber'),
			// 'search'         => "*$search_string*",
			// 'search_columns' => array(
			// 	'user_login',
			// 	'user_nicename',
			// 	'user_email',
			// 	'user_url',
			// ),
			'number' => 15,
			'meta_query' => array(
				'relation' => 'OR',
				array(
					'key'     => 'billing_phone',
					'value'   => $search_string,
					'compare' => 'LIKE'
				),
				array(
					'key'     => 'nickname',
					'value'   => $search_string,
					'compare' => 'LIKE'
				),
				array(
					'key'     => 'first_name',
					'value'   => $search_string,
					'compare' => 'LIKE'
				),
				array(
					'key'     => 'last_name',
					'value'   => $search_string,
					'compare' => 'LIKE'
				),
				array(
					'key'     => 'billing_first_name',
					'value'   => $search_string,
					'compare' => 'LIKE'
				),
				array(
					'key'     => 'billing_last_name',
					'value'   => $search_string,
					'compare' => 'LIKE'
				),
				array(
					'key'     => 'billing_email',
					'value'   => $search_string,
					'compare' => 'LIKE'
				),
				array(
					'key'     => 'billing_first_name',
					'value'   => $search_string,
					'compare' => 'LIKE'
				),
				
			)
		) );

		$users_found = $users->get_results();
		// echo json_encode($users_found);

		$resp_users = [];
		foreach($users_found as $u){
			if ( method_exists( 'user_switching', 'maybe_switch_url' ) ) {
				$url = user_switching::maybe_switch_url( $u ) . "&redirect_to=/cart";
			}
			$plan_name = "NA";
			$active_subscription = $this->book_shelf_obj->get_active_subscription($u->ID);
			if($active_subscription){
				foreach( $active_subscription->get_items() as $item_id => $product ){
					// Get the name
					$plan_name = $product->get_name();
					break;
				}
			}

			$phone = get_user_meta($u->ID, 'billing_phone', true);

			$resp_users[] = array(
				"user_id" => $u->ID,
				"switch_url" => $url,
				"user_name" => $u->first_name . ' ' . $u->last_name ,
				"user_email" =>  $u->user_email,
				"user_phone" => $phone,
				"plan_name" => $plan_name
			);
		}
		// if (sizeof($resp_users) > 0) {
			$response = array(
				"status" => true,
				"data" => $resp_users,
				"message" =>  "User found..."
			);		
		// }

		wp_send_json($response);
		die();

	}

	public function addPluginAdminMenu()
	{
		//add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position );
		//add_menu_page(  $this->plugin_name, 'Coupons Settings', 'administrator', $this->plugin_name, array( $this, 'displayPluginAdminDashboard' ), 'dashicons-chart-area', 26 );

		//add_submenu_page( '$parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function );
		//add_submenu_page( $this->plugin_name, 'Page Settings', 'Settings', 'administrator', $this->plugin_name.'-settings', array( $this, 'displayPluginAdminSettings' ));
		// add_menu_page( "Squiggles", "Squiggles", "adminstrator", "Squiggles", array( $this, 'displayPluginAdminDashboard' ), 'dashicons-chart-area', 26 );

		add_submenu_page('woocommerce', 'Squiggles Settings', 'Squiggles Settings', 'manage_woocommerce', 'wc-admin-squiggles-settings', array($this, 'displayPluginAdminSettings'));
	//	add_submenu_page('woocommerce', 'Issue Books Offline', 'Issue Books', 'manage_woocommerce', 'wc-admin-squiggles-issue-books', array($this, 'displayAdminIssueBooks'));

		add_menu_page( "Issue Books", "Issue Books", "edit_shop_orders", "squiggles-place-order", array( $this, 'displayAdminIssueBooks' ), 'dashicons-chart-area', 10 );
		
		if ( current_user_can( 'shop_manager' ) ) {
			remove_submenu_page( 'woocommerce', 'wc-reports' );
			remove_menu_page( 'wc-admin&path=/analytics/overview' );
			remove_submenu_page( 'woocommerce', 'wc-admin' );
			remove_menu_page( 'pw-gift-cards' );
		}
	}

	public function displayPluginAdminSettings()
	{
		$tab = "delivery";
		if (!empty($_GET["tab"])) {
			$tab = $_GET["tab"];
		}
		wp_enqueue_style('isteven-multi-select');
		wp_enqueue_script( $this->plugin_name . '-books-import' );
		wp_enqueue_media();
		require_once 'partials/squiggles-setting-admin-settings-display.php';
	}

	public function displayAdminIssueBooks()
	{
		wp_enqueue_style('bulma');
		wp_enqueue_script( $this->plugin_name . '-pos' );
		require_once 'partials/squiggles-setting-admin-pos.php';
	}

	public function add_meta_boxes_edit_order_page()
	{
		add_meta_box(
			'custom_order_meta_box',
			__('Subscription Damage & Other Changes'),
			array($this, 'custom_metabox_content'),
			'shop_order',
			'normal',
			'default'
		);

		add_meta_box(
			'custom_order_meta_box',
			__('Order Timeline'),
			array($this, 'custom_metabox_content_of_product'),
			'product',
			'normal',
			'default'
		);
	}
	public function custom_metabox_content( $post)
	{
// 			$copy_post_id = WC_Order_Item_Product($post->ID
 			$order = new WC_Order($post->ID );
			$items = $order->get_items();

		$active_subscription_id = get_post_meta($post->ID, 'active_subscription_id',true);
		
		if($active_subscription_id){
			require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/squiggles-setting-admin-meta-box-order-edit-page.php';
		}else{
			echo "This order is not assiciated with any subscription.";
		}
	}

	public function custom_metabox_content_of_product()
	{
		// echo "product content table!";
		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/squiggles-setting-admin-meta-box-product-page.php';
	}

	public function send_save_subscription_damage()
	{
		
		$resp = array(
			"status" => false,
			"message" => "",
			"data" => []
		);

		if (isset($_POST['data'])) {

			$sub_damage = $_POST['data'];
			$added_order_id = $sub_damage["added_order_id"];
			$active_subscription_id = get_post_meta($added_order_id, 'active_subscription_id',true);
			// $added_order_id = "172";
			//loop through all fields of sub_damage and sanitize every field.
			foreach ($sub_damage as $key => $val) {
				if (is_array($val)) {
 				   $sub_damage[$key] = $val;
				} else {
					$sub_damage[$key] = sanitize_text_field($val);
				}

			}

			$sub_damages = get_post_meta( $active_subscription_id, 'sub_damages', true);
			if(empty($sub_damages)){
				$sub_damages =[];
			}
			$sub_damages[] = $sub_damage;
// 			echo "<pre>"; print_r($sub_damages); die;
			update_post_meta( $active_subscription_id, 'sub_damages', $sub_damages);
			foreach ($sub_damages as $k => $item) {
        
				if (!empty($item['sq_books_ids'])) {
					$image_urls = [];
					foreach ($item['sq_books_ids'] as $image_id) {
						   $image_url = wp_get_attachment_image_src($image_id, 'full'); 
		// 				var_dump($image_url[0]);
						if ($image_url) {
							$image_urls[] = $image_url[0];
						}
					}
					$sub_damages[$k]['sq_books_ids'] = $image_urls;
				}
			}

			if($_POST['email']){
				$notification_data = $_POST['data'];
				$this->send_email_notification($notification_data);
				$res_sms = " and Notification has sent";
			}

			$resp = array(
				"status" => true,
				"data" => $sub_damages,
				"message" => "Data Saved successfully".$res_sms
			);

		}
		wp_send_json($resp);
		die();
	}

	public function save_slots_data()
	{
		$response = array(
			"status" => false,
			"data" => null,
			"message" => "An error occured."
		);

		if ($_SERVER["REQUEST_METHOD"] == "POST") {
			$slots = $_POST["slots"];
			update_option("squggles_delivery_slots", $slots);
			$response = array(
				"status" => true,
				"data" => $slots,
				"message" => "Saved successfully"
			);
		}

		wp_send_json($response);
		die();
	}

	function get_slot_by_pincode($pincode,$zone=null,$date=null){
		$data = get_option("squggles_delivery_slots",true);
		$pincode_slots = array_filter(
						   $data,
						   function($slot)use($pincode,$zone,$date){ 
							   if(empty($zone)&&empty($date))
									  return $slot["pincode"] === $pincode;
							   else if(empty($zone))
									  return $slot["pincode"] === $pincode && $slot["date"]==$date;
							   else if(empty($date))
									return $slot["pincode"] === $pincode && strcasecmp($slot["zone"],$zone)==0 ;
							   else
									return $slot["pincode"] === $pincode && strcasecmp($slot["zone"],$zone)==0  && $slot["date"]==$date;
							   
						   });
		
		return $pincode_slots;
		
	}

	
	public function wk_custom_user_profile_fields($user)
	{
		echo '<h1 class="heading">Custom Fields</h1>';

		$user_zone = get_user_meta($user->ID, 'sq_user_zone', true);

		$pincode = get_user_meta($user->ID, 'billing_postcode', true);
		$slots = $this->get_slot_by_pincode($pincode, null, null);
		$zones = array_unique(array_column($slots, 'zone'));
		// print_r($slots);
		// print_r($zones);
		// print_r($user_zone);

		?>

		<table class="form-table">
			<tr>
				<th>
					<label for="user_zone">Zones Available</label>
				</th>
				<td>
					<select name="user_zone" id="user_zone">
						<option value="">Select your zone</option>
						<?php
						foreach ($zones as $val) {
							if ($val == $user_zone) {
								echo '<option value="' . $val . '" Selected >' . $val . '</option>';
							}else {

								echo '<option value="' . $val . '" >' . $val . '</option>';
							}
						}
						?>
					</select>
				</td>
			</tr>
		</table>

		<?php

	}

	public function wk_save_custom_user_profile_fields($user_id)
	{
		$user_zone = $_POST['user_zone'];
		update_user_meta($user_id, 'sq_user_zone', $user_zone);
	}

	/**
	 * Register the 'Custom Column' column in the importer.
	 * Ref: https://gist.github.com/SashkoWooTeam/3acb254d044be23d35af3242c9bfb2d5
	 * @param array $options
	 * @return array $options
	 */
	public function woocommerce_csv_product_import_books_mapping_options( $options ) {

		//custom fields
		$options['smart_product_id'] = 'Product_ID';
		$options['isbn10'] = 'ISBN 10';
		$options['isbn13'] = 'ISBN 13';
		$options['pages'] = 'Pages';
		$options['mrp'] = 'MRP';
		$options['min-age'] = 'Min_Age';
		$options['max-age'] = 'Max Age';
		$options['publication-date'] = 'publication_date';
		$options['number-of-copies'] = 'number_of_copies';
		$options['product-classification'] = 'Product_Classification';
		$options['product-type'] = 'Product_Type';
		$options['product-sub-type'] = 'Product_Sub_Type';
		$options['edition'] = 'Edition';
		$options['lexile-code'] = 'Lexile Code';
		$options['dewey-decimal'] = 'Dewey_Decimal';
		$options['illustrator'] = 'Illustrator';
		$options['country-of-print'] = 'Print_Country';
		
		//taxonomy fields
		$options['product-series'] = 'Product Series';
		$options['book-author'] = 'Author_Name';
		$options['book-author-description'] = 'Author_Description';
		$options['book-publisher'] = 'Publisher';
		$options['book-language'] = 'Language';
		$options['book-subject'] = 'Subject';
		$options['book-genre'] = 'Genre';
		$options['character'] = 'Product_characters ';

		//copy data
		$options['copy_invoice_number'] = 'Copy Invoice Number';
		$options['copy_invoice_line'] = 'Copy Invoice Line';
		$options['copy_invoice_date'] = 'Copy Invoice Date';
		$options['copy_binding'] = 'Copy Binding';
		$options['copy_shelf_id'] = 'Copy Shelf ID';
		$options['copy_purchase_price'] = 'Copy Purchase Price';
		$options['copy_mrp'] = 'Copy MRP';
		$options['copy_purchase_condition'] = 'Copy Purchase Condition';
		$options['copy_condition'] = 'Copy Condition';
		$options['copy_comments'] = 'Copy Comments';
		$options['copy_id'] = 'Copy ID';

		return $options;

	}

	public function woocommerce_csv_product_import_books_mapping_default_columns( $columns ) {

		// custom fields
		$columns['Product_ID'] = 'smart_product_id';
		$columns['ISBN 10'] = 'isbn10';
		$columns['ISBN 13'] = 'isbn13';
		$columns['Pages'] = 'pages';
		$columns['MRP'] = 'mrp';
		$columns['Min_Age'] = 'min-age';
		$columns['Max Age'] = 'max-age';
		$columns['publication_date'] = 'publication-date';
		$columns['number_of_copies'] = 'number-of-copies';
		$columns['Product_Classification'] = 'product-classification';
		$columns['Product_Type'] = 'product-type';
		$columns['Product_Sub_Type'] = 'product-sub-type';
		$columns['Edition'] = 'edition';
		$columns['Lexile Code'] = 'lexile-code';
		$columns['Dewey_Decimal'] = 'dewey-decimal';
		$columns['Illustrator'] = 'illustrator';
		$columns['Print_Country'] = 'country-of-print';
		
		//taxonomy fields
		$columns['Product Series'] = 'product-series';
		$columns['Author_Name'] = 'book-author';
		$columns['Author_Description'] = 'book-author-description';
		$columns['Publisher'] = 'book-publisher';
		$columns['Language'] = 'book-language';
		$columns['Subject'] = 'book-subject';
		$columns['Genre'] = 'book-genre';
		$columns['Product_characters'] = 'character';
		//$columns['product custom text'] = 'product_custom_text';


		//copy fields
		$columns['Copy Invoice Number'] = 'copy_invoice_number';
		$columns['Copy Invoice Line'] = 'copy_invoice_line';
		$columns['Copy Invoice Date'] = 'copy_invoice_date';
		$columns['Copy Binding'] = 'copy_binding';
		$columns['Copy Shelf ID'] = 'copy_shelf_id';
		$columns['Copy Purchase Price'] = 'copy_purchase_price';
		$columns['Copy MRP'] = 'copy_mrp';
		$columns['Copy Purchase Condition'] = 'copy_purchase_condition';
		$columns['Copy Condition'] = 'copy_condition';
		$columns['Copy Comments'] = 'copy_comments';
		$columns['Copy ID'] = 'copy_id';

	
		return $columns;
		
	}

	public function woocommerce_product_import_pre_insert_product_object_books( $object, $data ) {

		if ( ! empty( $data['smart_product_id'] ) ) {
			$object->update_meta_data( 'wpcf-'.'smart_product_id', $data['smart_product_id'] );
		}
		if ( ! empty( $data['isbn10'] ) ) {
			$object->update_meta_data( 'wpcf-'.'isbn10', $data['isbn10'] );
		}
		if ( ! empty( $data['isbn13'] ) ) {
			$object->update_meta_data( 'wpcf-'.'isbn13', $data['isbn13'] );
		}
		if ( ! empty( $data['pages'] ) ) {
			$object->update_meta_data( 'wpcf-'.'pages', $data['pages'] );
		}
		if ( ! empty( $data['mrp'] ) ) {
			$object->update_meta_data( 'wpcf-'.'mrp', $data['mrp'] );
		}
		if ( ! empty( $data['min-age'] ) ) {
			$object->update_meta_data( 'wpcf-'.'min-age', $data['min-age'] );
		}
		if ( ! empty( $data['max-age'] ) ) {
			$object->update_meta_data( 'wpcf-'.'max-age', $data['max-age'] );
		}
		if ( ! empty( $data['publication-date'] ) ) {
			$object->update_meta_data( 'wpcf-'.'publication-date', strtotime($data['publication-date']) );
		}
		if ( ! empty( $data['number-of-copies'] ) ) {
			$object->update_meta_data( 'wpcf-'.'number-of-copies', $data['number-of-copies'] );
		}
		if ( ! empty( $data['product-classification'] ) ) {
			$object->update_meta_data( 'wpcf-'.'product-classification', $data['product-classification'] );
		}
		if ( ! empty( $data['product-type'] ) ) {
			$object->update_meta_data( 'wpcf-'.'product-type', $data['product-type'] );
		}
		if ( ! empty( $data['product-sub-type'] ) ) {
			$object->update_meta_data( 'wpcf-'.'product-sub-type', $data['product-sub-type'] );
		}
		if ( ! empty( $data['edition'] ) ) {
			$object->update_meta_data( 'wpcf-'.'edition', $data['edition'] );
		}
		if ( ! empty( $data['lexile-code'] ) ) {
			$object->update_meta_data( 'wpcf-'.'lexile-code', $data['lexile-code'] );
		}
		if ( ! empty( $data['dewey-decimal'] ) ) {
			$object->update_meta_data( 'wpcf-'.'dewey-decimal', $data['dewey-decimal'] );
		}
		if ( ! empty( $data['illustrator'] ) ) {
			$object->update_meta_data( 'wpcf-'.'illustrator', $data['illustrator'] );
		}
		if ( ! empty( $data['country-of-print'] ) ) {
			$object->update_meta_data( 'wpcf-'.'country-of-print', $data['country-of-print'] );
		}
		
		return $object;
	
	}

	public function woocommerce_product_import_pre_insert_product_object_books_taxonomy_fields( $product, $data ) {
  
		$taxonomies = array('product-series','book-author','book-publisher','book-language','book-subject','book-genre','character');
		
		foreach ($taxonomies as $custom_taxonomy) {
			//$custom_taxonomy = 'product-series';
			if ( is_a( $product, 'WC_Product' ) ) {
				if( ! empty( $data[ $custom_taxonomy ] ) ) {
							$product->save();
							$custom_taxonomy_values = $data[ $custom_taxonomy ];
							$custom_taxonomy_values = explode(",", $custom_taxonomy_values);
							$terms = array();
							foreach($custom_taxonomy_values as $custom_taxonomy_value){
								 $custom_taxonomy_value = str_replace('"','', $custom_taxonomy_value);
								if(!get_term_by('name', $custom_taxonomy_value, $custom_taxonomy)){
										$custom_taxonomy_args= array(
											'cat_name' => $custom_taxonomy_value,
											'taxonomy' => $custom_taxonomy,
											'category_description' => ''
										);
										if($custom_taxonomy=='book-author' && !empty($data[ 'book-author-description' ] )){
											//set description
											$custom_taxonomy_args['category_description'] = $data[ 'book-author-description' ];
										}
										$custom_taxonomy_value_cat = wp_insert_category($custom_taxonomy_args);
										array_push($terms, $custom_taxonomy_value_cat);
								}else{
										$custom_taxonomy_value_cat = get_term_by('name', $custom_taxonomy_value, $custom_taxonomy)->term_id;
										array_push($terms, $custom_taxonomy_value_cat);
								}
							}
					wp_set_object_terms( $product->get_id(),  $terms, $custom_taxonomy );
				}
			}
		}
		$this->save_copy_data($product->get_id(),$data);
		return $product;
	}

	function save_copy_data($product_id, $data){
		$query = new WP_Query( 
			array(
				'post_type' => 'copy', //Child post type slug
				'numberposts' => -1,
				'toolset_relationships' => array(
					'role' => 'child',
					'related_to' => $product_id, // ID of starting post
					'relationship' =>'copy',
				),
			)
		);
		$copy_posts = $query->posts;

		if(sizeof($copy_posts)==0 && !empty($data['copy_id'])){
			$bono = array(
				'post_type'    => 'copy',
				'post_title'    => "Copy 1",
				'post_status'   => 'publish',
			);
			
			$postid=wp_insert_post( $bono );
			add_post_meta( $postid, 'wpcf-'.'copy-id', $data['copy_id'], true);
			add_post_meta( $postid, 'wpcf-'.'copy-comments', $data['copy_comments'], true);
			add_post_meta( $postid, 'wpcf-'.'copy-condition', $data['copy_condition'], true);
			add_post_meta( $postid, 'wpcf-'.'purchase-condition', $data['copy_purchase_condition'], true);
			add_post_meta( $postid, 'wpcf-'.'copy-mrp', $data['copy_mrp'], true);
			add_post_meta( $postid, 'wpcf-'.'purchase-price', $data['copy_purchase_price'], true);
			add_post_meta( $postid, 'wpcf-'.'shelf-id', $data['copy_shelf_id'], true);
			add_post_meta( $postid, 'wpcf-'.'binding', $data['copy_binding'], true);
			add_post_meta( $postid, 'wpcf-'.'invoice-date', strtotime($data['copy_invoice_date']), true);
			add_post_meta( $postid, 'wpcf-'.'invoice-line-item-number', $data['copy_invoice_line'], true);
			add_post_meta( $postid, 'wpcf-'.'invoice-number', $data['copy_invoice_number'], true);
			//entry-date
			//on-hold
			add_post_meta( $postid, 'wpcf-'.'entry-date', time(), true);
			add_post_meta( $postid, 'wpcf-'.'on-hold',"No", true);


			toolset_connect_posts( 'copy', $product_id,$postid);
		}


	}

	public function subscriptions_created_for_order($order){
		if ( is_a( $order, 'WC_Order' ) ) {
			$items = $order->get_items();
			foreach ( $items as $item ) {         
				$product =    $item->get_product();
				if ( is_a( $product, 'WC_Product_Subscription' ) || is_a( $product, 'WC_Product_Variable_Subscription' ) || is_a( $product, 'WC_Product_Subscription_Variation' ) ) {
					//this is a plan product. copy meta data from product to subscription
					$subscription = wcs_get_subscriptions_for_order( $order->get_id());
					if(sizeof($subscription)>0){
						$subscription = reset($subscription);
						$product_id = "";
						if(is_a( $product, 'WC_Product_Subscription_Variation' ) ){
							$variation_id = $product->get_id();
							$product_id = wp_get_post_parent_id($variation_id);
						}else{
							$product_id = $product->get_id();
						}
						
						$plan_credits = get_post_meta($product_id,'wpcf-'.'credits',true);
						$plan_free_delivery = get_post_meta($product_id,'wpcf-'.'free-delivery-per-month',true);
						$plan_delivery_fee = get_post_meta($product_id,'wpcf-'.'delivery-fee',true);
						$plan_plus_member = get_post_meta($product_id,'wpcf-'.'squiggles-plus-membership',true);
						$plan_deposit_amt = get_post_meta($product_id,'wpcf-'.'deposit-amount',true);

						update_post_meta($subscription->get_id(),'wpcf-'.'subscription-credits',$plan_credits);
						update_post_meta($subscription->get_id(),'wpcf-'.'subscription-free-delivery-per-month',$plan_free_delivery);
						update_post_meta($subscription->get_id(),'wpcf-'.'subscription-delivery-fee',$plan_delivery_fee);
						update_post_meta($subscription->get_id(),'wpcf-'.'subscription-squiggles-plus-membership',$plan_plus_member);
						update_post_meta($subscription->get_id(),'wpcf-'.'subscription-deposit-amount',$plan_deposit_amt);
					}
					break;
				}
			
			}
		}
	
	}

	function woocommerce_order_status_processing( $order_id) {
		//loop through all book products in the said order
		//if issue, find an available copy of book, mark it as 'Booked'. set copyid in order meta, add order notes.
		//if no copy is  available, mRK ORDER AS ON-HOLD and add this order notes.
		//if return, get copyid from order meta, mark it as 'Return Scheduled', add to order notes.

		$order = wc_get_order( $order_id);
		$items = $order->get_items();
	   // print_r($items);exit;
	   
		foreach ( $items as $item ) {
			
			$product_id = $item->get_product_id();
			if ( has_term( 'book', 'product_cat', $product_id ) ) {
				$issue_or_return = $item->get_meta("issue_or_return");
				if($issue_or_return=="issue"){
					$available_copy_id=false;
					$booked_copy_post_id = $item->get_meta("booked_copy_post_id");
					if($booked_copy_post_id){
						//copy post id already set, check if its still available
						$copy_status = get_post_meta( $booked_copy_post_id, 'wpcf-'.'copy-status',true);
						if($copy_status=="available" || $copy_status==""){
							$available_copy_id = $booked_copy_post_id;
						}
					} else if(!$available_copy_id && !$booked_copy_post_id){
						//issued book, find an available copyid and set it as "booked" and set issue orderid in copy meta
						$available_copies = $this->book_shelf_obj->find_available_copies_of_book($product_id);
						if(sizeof($available_copies)>0){
							$available_copy_id = $available_copies[0];
						}
					}
					
					
					if($available_copy_id ){
						
						update_post_meta( $available_copy_id, 'wpcf-'.'copy-status',"booked");
						update_post_meta( $available_copy_id, 'last_booked_order_id',$order_id);
						//$item->update_meta_data("booked_copy_post_id",$available_copy_id);
						wc_add_order_item_meta($item->get_id(),"booked_copy_post_id",$available_copy_id,true);
					}else{
						//no available copy, mark order as on-hold and set order note.
					}
			   	}else if($issue_or_return=="return"){
					//returned book, 
					//set copy-status as "return scheduled" and set last_return-order-id in copy meta
					$return_copy_post_id = $item->get_meta("return_copy_post_id");

					update_post_meta( $return_copy_post_id, 'wpcf-'.'copy-status',"return_scheduled");
					update_post_meta( $return_copy_post_id, 'last_return_order_id',$order_id);
					$item->update_meta_data("return_copy_post_id",$return_copy_post_id);
			   	}
				$this->book_shelf_obj->update_book_product_stock_status($product_id);
			}
		}

		//mark damages and other fees as paid
		$active_subscription_id = get_post_meta($order_id, 'active_subscription_id',true);
		
		if($active_subscription_id){
			$sub_damages = get_post_meta( $active_subscription_id, 'sub_damages', true);
			foreach ($sub_damages as $key => $charge) {
				$charge = (array)$charge;
				if(!isset($charge["paid_order_id"]) && $charge["amt"]>0){
					$sub_damages[$key]["paid_order_id"] = $order_id;
				}
			}
			update_post_meta( $active_subscription_id, 'sub_damages', $sub_damages);
			
		}
		WC()->mailer()->get_emails()['WC_Email_New_Order']->trigger( $order_id );
	}



	public function woocommerce_new_product($product_id){
		$this->book_shelf_obj->update_book_product_stock_status($product_id);
	}

	public function woocommerce_update_product($product_id){
		$this->book_shelf_obj->update_book_product_stock_status($product_id);
	}

	public function bt_shipment_status_changed($order_id,$shipment_obj,$shipment_obj_old){
		$current_status = $shipment_obj->current_status;

		if(!empty($current_status)){
			switch($current_status){
				case "pending-pickup":
				case "out-for-pickup":
				case "in-transit":
				case "out-for-delivery":
					//set copy status to in-transit
					$this->update_copy_on_shipment_status_changed($order_id,'issued_in_transit');
					break;
				case "delivered":
					//set copy status to delivered
					$this->update_copy_on_shipment_status_changed($order_id,'issued_delivered');
					break;
				case "rto-in-transit":
					//set copy status to return in transit
					$this->update_copy_on_shipment_status_changed($order_id,'return_in_transit');
					break;
				case "rto-delivered":
					//set copy status to available
					$this->update_copy_on_shipment_status_changed($order_id,'available');
					break;
			}
		}

	}

	private function update_copy_on_shipment_status_changed($order_id,$new_copy_status){
		$order = wc_get_order( $order_id);
		$items = $order->get_items();
	   	$issued_copy_post_ids=array();
		$return_copy_post_ids=array();
	   
		foreach ( $items as $item ) {
			
			$product_id = $item->get_product_id();
			if ( has_term( 'book', 'product_cat', $product_id ) ) {
				$issue_or_return = $item->get_meta("issue_or_return");

				if($issue_or_return=="issue" ){
					$booked_copy_post_id = $item->get_meta("booked_copy_post_id");
					if($new_copy_status=="issued_in_transit" || $new_copy_status=="issued_delivered"){
						update_post_meta( $booked_copy_post_id, 'wpcf-'.'copy-status',$new_copy_status);
					}
			   	}else if($issue_or_return=="return"){
					$return_copy_post_id = $item->get_meta("return_copy_post_id");
					if($new_copy_status=="return_in_transit" || $new_copy_status=="available"){
						update_post_meta( $return_copy_post_id, 'wpcf-'.'copy-status',$new_copy_status);
					}
			   	}
				$this->book_shelf_obj->update_book_product_stock_status($product_id);
			}
		}
	}

	public function woocommerce_checkout_create_order( $order, $data ){
		$order->calculate_totals();
	}

	function woocommerce_order_item_display_meta_key( $display_key, $meta, $item){
		if($display_key=="issue_or_return"){
			$display_key="For";
		} else if($display_key=="credits"){
			$display_key="Credits";
		} else if($display_key=="booked_copy_post_id"){
			$display_key="Copy#";
		} else if($display_key=="return_copy_post_id"){
			$display_key="Copy#";
		}	

		return $display_key;
	}

	function woocommerce_order_item_display_meta_value( $display_value, $meta, $item){

		if($meta->key =="issue_or_return"){
			$display_value=$display_value;
		} else if($meta->key =="booked_copy_post_id"){

			$copy_post_id = $meta->value;
			$copy_smart_id = get_post_meta($copy_post_id,'wpcf-'.'copy-id',true);
			if(empty($copy_smart_id)){
				$copy_smart_id = 'NA' . $copy_post_id ;
			} 
			
			$item_id = $item->get_id();
			$product_id = $item->get_product_id();
			$order_id =  $item->get_order_id();
			$order = wc_get_order($order_id);
			$order_status  = $order->get_status();
			$edit_icon = '';
			if ($order_status == "processing") {
				$shipment_obj = bt_get_shipping_tracking($order_id);
				$current_status = $shipment_obj["tracking_data"]["current_status"];
				if (empty($current_status) || $current_status == "pending-pickup") {
					$edit_icon = "<span id='edit_$item_id' class='dashicons dashicons-edit edit_issue_copy_id' data-item-id='$item_id' data-product-id='$product_id'>";					
				}
			}

			// echo $order_id; exit;
			
			$display_value = "<span class='item-$item_id' style='display: none;'>". $copy_smart_id . "</span>" . $edit_icon;
			
		} else if($meta->key =="return_copy_post_id"){

			$copy_post_id = $meta->value;
			$copy_smart_id = get_post_meta($copy_post_id,'wpcf-'.'copy-id',true);
			if(empty($copy_smart_id)){
				$copy_smart_id = 'NA';
			}
			$display_value =  $copy_smart_id ;// . $copy_post_id;
		}	

		return $display_value;
	}
	
	function save_issue_copy_id() {
		$nonce = $_POST["nonce"];
		if (!wp_verify_nonce($nonce, 'save_issue_copy_id')) {
			exit; // Get out of here, the nonce is rotten!
		}
		$response = array(
			"status" => false,
			"data" => null,
			"message" => "An error happened."
		);


		$new_copy_post_id = $_POST["copy_post_id"];
		$item_id = $_POST["item_id"];
		$order_id = $_POST["order_id"];

		if(empty($new_copy_post_id) || empty($item_id) || empty($order_id)){
			$response = array(
				"status" => false,
				"data" => null,
				"message" => "An error happened. Please try again."
			);
			wp_send_json($response);
			die();
		}
		
		$order = wc_get_order( $order_id);
		$items = $order->get_items();
	   // print_r($items);exit;
	   
		foreach ( $items as $item ) {

			if($item_id ==  $item->get_id()){
				$product_id = $item->get_product_id();
				if ( has_term( 'book', 'product_cat', $product_id ) ) {
					// validate 4.b to do
					$copy_post_ids = $this->book_shelf_obj->find_available_copies_of_book($product_id);
					if ( !in_array($new_copy_post_id, $copy_post_ids)) {
						$response = array(
							"status" => false,
							"data" => null,
							"message" => "This copy is no longer available, please choose another copy and try again."
						);
						wp_send_json($response);
						die();
					}

					$issue_or_return = $item->get_meta("issue_or_return");
					if($issue_or_return=="issue"){
						//issued book, find an available copyid and set it as "booked" and set issue orderid in copy meta
						$available_copies = $this->book_shelf_obj->find_available_copies_of_book($product_id);
						if(sizeof($available_copies)>0 && in_array($new_copy_post_id, $available_copies)){
							//release old copy
							$old_booked_copy_post_id = $item->get_meta("booked_copy_post_id");
							update_post_meta( $old_booked_copy_post_id, 'wpcf-'.'copy-status',"available");

							//book new copy
							$available_copy_id = $new_copy_post_id;
							update_post_meta( $available_copy_id, 'wpcf-'.'copy-status',"booked");
							update_post_meta( $available_copy_id, 'last_booked_order_id',$order_id);
							wc_update_order_item_meta($item_id,"booked_copy_post_id",$available_copy_id);

							$response = array(
								"status" => true,
								"data" => null,
								"message" => "Copy Updated Sucessfully."
							);
							wp_send_json($response);
							die();

						}else{
							//no available copy, mark order as on-hold and set order note.
							$response = array(
								"status" => false,
								"data" => null,
								"message" => "Selected copy is no longer available for issue."
							);
							wp_send_json($response);
							die();
						}
						$this->book_shelf_obj->update_book_product_stock_status($product_id);

					}
					
				}
				break;
			}
		}

		wp_send_json($response);
		die();
	}
	
	function get_all_perticular_book_id_arr() {
		$nonce = $_POST["nonce"];
		if (!wp_verify_nonce($nonce, 'get_all_perticular_book_id_arr')) {
			exit; // Get out of here, the nonce is rotten!
		}
		$response = array(
			"status" => false,
			"data" => null,
			"message" => "An error happened."
		);
		$product_id = $_POST["product_id"];

		if(!empty(($product_id))){
			$copy_post_ids = $this->book_shelf_obj->find_available_copies_of_book($product_id);
			$copy_ids = [];
			foreach($copy_post_ids as $pid){
				$copy_id = get_post_meta($pid,'wpcf-copy-id',true);
				if(!empty($copy_id)){
					$copy_ids[$pid] = $copy_id;
				}
			}

			$response = array(
				"status" => true,
				"data" => $copy_ids,
				"message" => "Success."
			);
			
		}

		wp_send_json($response);
		die();
	}

	function woocommerce_admin_order_data_after_order_details($order ){

		echo "<br><br>";
		echo "<b>Slot Details:</b><br>";
		$shipping_method_name = $order->get_shipping_method();
		echo $shipping_method_name . "<br>"; 
	
		$date = get_post_meta( $order->get_id(), 'jckwds_date', true );
		$time = get_post_meta( $order->get_id(), 'jckwds_timeslot', true );
		echo  ($date==="asap"?"ASAP":$date) . "<br>" . ($time==="asap"?"ASAP":$time);


	}

	function admin_init_export_copy(){
		if(!current_user_can('administrator')) return;
		if(!isset($_GET["bt_export_copy"])) return;
		$products = wc_get_products(array(
			'limit'  => -1, // All products
			'status' => 'publish', // Only published products
			'category' => array( 'book' )
		) );
	
		//echo sizeof($products);
		$copies = array();
		foreach( $products as $product ) {
			$product_id   = $product->get_id();
			
			$query = new WP_Query( 
				array(
					'post_type' => 'copy', //Child post type slug
					'numberposts' => -1,
					'toolset_relationships' => array(
						'role' => 'child',
						'related_to' => $product_id, // ID of starting post
						'relationship' =>'copy',
					),
				)
			);
			$copy_posts = $query->posts;
		
			
			foreach ($copy_posts as  $post) {
				$post_id = $post->ID;
				$entry_date = get_post_meta($post_id,'wpcf-entry-date',true);
				if(!empty($entry_date)){
					$entry_date = date('d/m/y',$entry_date);
				}
				$invoice_date = get_post_meta($post_id,'wpcf-invoice-date',true);
				if(!empty(trim($invoice_date)) && is_numeric($invoice_date)){
					try{
					$invoice_date = date('d/m/y',$invoice_date);
					}catch(Exception $e){
					//	echo $invoice_date;
					//	exit;
					}
				}
				$copies[] = array(
				"post_id" => $post->ID,
				"book" => $product->get_title(),
				"product_id" => $product_id,
				"copy_id" => get_post_meta($post_id,'wpcf-copy-id',true),
				"copy_mrp" => get_post_meta($post_id,'wpcf-copy-mrp',true),
				"copy_status" => get_post_meta($post_id,'wpcf-copy-status',true),
				"copy_comments" => get_post_meta($post_id,'wpcf-copy-comments',true),
				"copy_condition" => get_post_meta($post_id,'wpcf-copy-condition',true),
				"purchase_condition" => get_post_meta($post_id,'wpcf-purchase-condition',true),
				"purchase_price" => get_post_meta($post_id,'wpcf-purchase-price',true),
				"shelf_id" => get_post_meta($post_id,'wpcf-shelf-id',true),
				"binding" => get_post_meta($post_id,'wpcf-binding',true),
				"invoice_date" => $invoice_date,
				"invoice_line" => get_post_meta($post_id,'wpcf-invoice-line-item-number',true),
				"invoice_number" => get_post_meta($post_id,'wpcf-invoice-number',true),
				"entry_date" => $entry_date ,
				"on_hold" => get_post_meta($post_id,'wpcf-on-hold',true)
				);
			}
			
		}
		
		$csv = "Book,copy_post_id,woocommerce_product_id,copy_id,copy_mrp,copy_status,copy_comments,copy_condition,purchase_condition,purchase_price,shelf_id,binding,invoice_date,invoice_line,invoice_number,entry_date,on_hold\n";
		foreach( $copies as $row ) {
			$csv .=  '"'.$row['book']. '",' .$row['post_id'] . ',' . $row['product_id'];
			$csv .=  ',' .$row['copy_id'] . ',' . $row['copy_mrp'];
        	$csv .=  ',' .$row['copy_status'] . ',' . $row['copy_comments'];
			$csv .=  ',' .$row['copy_condition']. ',' .$row['purchase_condition'] . ',' . $row['purchase_price'];
			$csv .=  ',' .$row['shelf_id']. ',' .$row['binding'] . ',' . $row['invoice_date'];
			$csv .=  ',' .$row['invoice_line']. ',' .$row['invoice_number'] . ',' . $row['entry_date']. ',' . $row['on_hold'];
        	$csv .= "\n";
    	}
		
		$filename = 'copies.csv';
		header( 'Content-Type: text/csv' ); // tells browser to download
		header( 'Content-Disposition: attachment; filename="' . $filename .'"' );
		header( 'Pragma: no-cache' ); // no cache
		header( "Expires: Sat, 26 Jul 1997 05:00:00 GMT" ); // expire date

		echo $csv;
		exit;
	}

	function woocommerce_email_after_order_table($order){
		$date = get_post_meta( $order->get_id(), 'jckwds_date', true );
		if(!empty($date)){
			echo "<b>Slot Details:</b><br>";
			$shipping_method_name = $order->get_shipping_method();
			echo $shipping_method_name . "<br>"; 
		
			$date = get_post_meta( $order->get_id(), 'jckwds_date', true );
			$time = get_post_meta( $order->get_id(), 'jckwds_timeslot', true );
			echo  ($date==="asap"?"ASAP":$date) . "<br>" . ($time==="asap"?"ASAP":$time) . "<br>" . "<br>";
		}
	}

	function unhook_woocommerce_email($email_class ){
 		 // New order emails
		 //we will trigger new order email again in processing hook
		remove_action( 'woocommerce_order_status_on-hold_to_processing_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );
		remove_action( 'woocommerce_order_status_pending_to_on-hold_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );
		remove_action( 'woocommerce_order_status_pending_to_processing_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );
	}

	function woocommerce_order_status_cancelled($order_id){
		$order = wc_get_order( $order_id);
		$items = $order->get_items();
		$order_note = "";
		foreach ( $items as $item ) {
			
			$product_id = $item->get_product_id();
			if ( has_term( 'book', 'product_cat', $product_id ) ) {
				$issue_or_return = $item->get_meta("issue_or_return");
				if($issue_or_return=="issue"){
					$booked_copy_post_id = $item->get_meta("booked_copy_post_id");
					$copy_status = get_post_meta( $booked_copy_post_id, 'wpcf-'.'copy-status',true);
					$last_booked_order_id = get_post_meta( $booked_copy_post_id, 'last_booked_order_id',true);
					if($copy_status =='booked' && $last_booked_order_id == $order_id){
						update_post_meta( $booked_copy_post_id, 'wpcf-'.'copy-status','available');
						$copy_smart_id = get_post_meta($booked_copy_post_id,'wpcf-'.'copy-id',true);
						$order_note .= "Changed status of copy# " . $copy_smart_id ." to Available.\n";
					}else{
						$order_note .= "Unable to change status of copy# " . $copy_smart_id .".\n";
					}
			   	}else if($issue_or_return=="return"){
					$return_copy_post_id = $item->get_meta("return_copy_post_id");
					$copy_status = get_post_meta( $return_copy_post_id, 'wpcf-'.'copy-status',true);
					$last_return_order_id = get_post_meta( $return_copy_post_id, 'last_return_order_id',true);
					if($copy_status =='return_scheduled' && $last_return_order_id == $order_id){
						update_post_meta( $return_copy_post_id, 'wpcf-'.'copy-status','issued_delivered');
						$copy_smart_id = get_post_meta($return_copy_post_id,'wpcf-'.'copy-id',true);
						$order_note .= "Changed status of copy# " . $copy_smart_id ." to Issued.\n";
					}else{
						$order_note .= "Unable to change status of copy# " . $copy_smart_id .".\n";
					}
			   	}
				$this->book_shelf_obj->update_book_product_stock_status($product_id);
			}
		}
		if(!empty($order_note )){
			$order->add_order_note( $order_note . "\n\n- Squiggles");
		}
	}

	function init_set_capabilities(){
		  // Get the role object.
		  $editor = get_role( 'shop_manager' );

		  // A list of capabilities to remove from editors.
		  $caps = array(
			  'edit_theme_options',
			  'import',
			  'export',
			  'upload_files',
			  'moderate_comments',
			  'manage_links',
			   'manage_categories',
			   'delete_others_pages',
			   'delete_others_posts',
			   'delete_published_posts',
			   'delete_published_pages',
			   'delete_private_posts',
			   'delete_private_pages',
			   'delete_pages',
			   'delete_posts',
			   'read_private_pages', 
			  'edit_posts',
			  'edit_pages',
			  'edit_published_posts',
			  'edit_published_pages',
			  'edit_private_pages',
			  'edit_private_posts',
			  'edit_others_posts',
			  'edit_others_pages',
			  'publish_posts',
			  'publish_pages',
			  'edit_others_posts',
			  'view_woocommerce_reports'
			  
		  );
	  
		  foreach ( $caps as $cap ) {
		  
			  // Remove the capability.
			  $editor->remove_cap( $cap );
		  }
		//  $editor->remove_role( 'view_woocommerce_reports');
	}

	function phase_3_get_authors(){
		$resp = array(
			"status" => false,
			"message" => "",
			"data" => []
		);

	
		$book_authors = get_terms( 'book-author' );

		$resp = array(
			"status" => true,
			"message" => "Success",
			"data" => array_values($book_authors)
		);

		wp_send_json($resp);
		die();
	}


	function phase_3_search_isbn(){
		$resp = array(
			"status" => false,
			"message" => "",
			"data" => []
		);

		$received_data = json_decode(file_get_contents('php://input'), true);

		$isbn = "";
		if (isset($received_data['isbn'])) {
			$isbn = $received_data['isbn'];
		}

		$found_products = [];

		try{
			$book = $this->isbndb_obj->get_book_by_isbn( $isbn);
			//echo json_encode($book);
			//exit;
			if(!empty($book)){
				$found_products[] = $book;
			}
		}catch(Exception $e){

		}

		$filters_codes = array(
			'post_status' => 'publish',
			'post_type' => 'product',
			'category' => array( 'book' ),
			'fields' => 'ids',
			'meta_query' => array(
				array(
					'key' => 'wpcf-'.'isbn13',
					'value' => $isbn,
					'compare' => '='
				)
			)
		);		
		$products = new WP_Query($filters_codes);
		foreach ($products->posts as $postid){
			$found_products[] = Bitss_Squiggles_Book::GetBookByProductId( $postid);
		}
		

		//$b1 = Bitss_Squiggles_Book::GetBookByProductId( 11974);;
		//$b2 =  Bitss_Squiggles_Book::GetBookByProductId( 11972);;


		//$found_products = [$b1,$b2];

		$resp = array(
			"status" => true,
			"message" => "Success",
			"data" => $found_products
		);

		wp_send_json($resp);
		die();
	}

	function phase_3_search_title_author(){
		$resp = array(
			"status" => false,
			"message" => "",
			"data" => []
		);

		$received_data = json_decode(file_get_contents('php://input'), true);

		$title = "";
		if (isset($received_data['title'])) {
			$title = $received_data['title'];
		}

		$found_products = [];

		try{
			$books = $this->isbndb_obj->get_book_by_title( $title);
			//echo json_encode($book);
			//exit;
			if(!empty($books)){
				$found_products = $books;
			}
		}catch(Exception $e){
			
		}

		$filters_codes = array(
			'post_status' => 'publish',
			'post_type' => 'product',
			'category' => array( 'book' ),
			'fields' => 'ids',
			's' => $title
		);		
		$products = new WP_Query($filters_codes);
		foreach ($products->posts as $postid){
			$book = Bitss_Squiggles_Book::GetBookByProductId( $postid);

			if(!empty($book)){
				$found_products[] = $book;
			}
		}
		

		//$b1 = Bitss_Squiggles_Book::GetBookByProductId( 11974);;
		//$b2 =  Bitss_Squiggles_Book::GetBookByProductId( 11972);;


		//$found_products = [$b1,$b2];

		$resp = array(
			"status" => true,
			"message" => "Success",
			"data" => $found_products
		);

		wp_send_json($resp);
		die();
	}

	function phase_3_create_product(){
		$resp = array(
			"status" => false,
			"message" => "",
			"data" => []
		);

		$received_data = json_decode(file_get_contents('php://input'), true);
		$product =  Bitss_Squiggles_Book::CreateBook( $received_data);

		$p =Bitss_Squiggles_Book::GetBookFromProduct( $product);
	
		$resp = array(
			"status" => true,
			"message" => "Success",
			"data" => $p
		);

		wp_send_json($resp);
		die();
	}

	function phase_3_add_book_copy(){
		$resp = array(
			"status" => false,
			"message" => "An error occured.",
			"data" => []
		);

		$received_data = json_decode(file_get_contents('php://input'), true);

		$product_id = "";
		if (isset($received_data['woocommerce_product_id'])) {
			$product_id = $received_data['woocommerce_product_id'];
		}
		if(!empty($product_id)){
			$product_copy =  Bitss_Squiggles_Book::AddCopy($product_id, $received_data);
			$resp = array(
				"status" => true,
				"message" => "Success",
				"data" => $product_copy
			);
	
		}
		
		wp_send_json($resp);
		die();
	}

	function add_deposit_damage_history_tab( $s ) {
		$active_subscription_id = $s->get_id();
		$sub_damages = get_post_meta( $active_subscription_id, 'sub_damages', true );
		$subscription_amt = get_post_meta( $active_subscription_id, 'wpcf-subscription-deposit-amount', true );
		$parent_id = $s->get_parent_id();
		$parent_order = wc_get_order($parent_id);
		$this->books_shelf = $this->book_shelf_obj->Get_User_Book_Shelf();
		$return_book = $this->books_shelf["books"];
	
		$refunded_amt = 0;
	
		if ($parent_order) {
			$refunds = $parent_order->get_refunds();
			if (!empty($refunds)) {
				foreach ($refunds as $refund) {
					$refunded_amt += floatval($refund->get_amount());
				}
			}
		}
	
		echo '<h2>Deposit And Damage History</h2>';
	
		$html = '<div class="sq_sd_table deposit_and_dmg_table table-container">
			<table class="table is-bordered" id="sub_damage_table">
				<thead>
					<tr>
						<th>S.No.</th>
						<th>Date</th>
						<th>Reason</th>
						<th>Severity</th>
						<th>Copy</th>
						<th>Amount</th>
						<th>Remark</th>
						<th>Added Order Id</th>
						<th>Paid Order Id</th>
					</tr>
				</thead>
				<tbody>';
	
		$i = 0;
		$damage_amt = 0;
		foreach ($sub_damages as $k => $item) {
        
			if (!empty($item['sq_books_ids'])) {
				$image_urls = [];
				foreach ($item['sq_books_ids'] as $image_id) {
					   $image_url = wp_get_attachment_image_src($image_id, 'full'); 
					if ($image_url) {
						$image_urls[] = $image_url[0];
					}
				}
				$sub_damages[$k]['sq_books_ids'] = $image_urls;
			}
		}
		// echo "<pre>"; print_r($sub_damages); die;
		if (!empty($sub_damages)) {
			foreach ($sub_damages as $value) {
				$imagesHtml = "<div>";
				foreach($value['sq_books_ids'] as $imageUrl){
					$imagesHtml .= "<a href ='". $imageUrl ."' target='_blank'><img src='" . $imageUrl . "' alt='Damage Image' style='width:20px'></a>";
				}
				$imagesHtml .= "</div>";
				// print_r($imagesHtml); die;
				$i++;
				$paid_order_id = isset($value['paid_order_id']) ? $value['paid_order_id'] : 'N/A';
				if (!isset($value['paid_order_id'])) {
					$damage_amt += floatval($value['amt']);
				}
				$html .= '
					<tr>
						<td>' . esc_html($i) . '</td>
						<td>' . esc_html(date_i18n(get_option('date_format'), strtotime($value['date']))) . '</td>
						<td>' . esc_html($value['type']) . '<div class="sq-damage-table-img">'.$imagesHtml.'</div></td>
						<td>' . esc_html($value['severity']) . '</td>
						<td>' . esc_html($value['copys']) . '</td>
						<td>' . wp_kses_post(wc_price($value['amt'])) . '</td>
						<td>' . esc_html($value['remark']) . '</td>
						<td>' . esc_html($value['added_order_id']) . '</td>
						<td>' . $paid_order_id . '</td>
					</tr>';
			}
		} else {
			$html .= '<tr><td colspan="7">No damages exist</td></tr>';
		}
	
		$remaining_amt = floatval($subscription_amt) - $damage_amt;
		$formatted_subscription_amt = wc_price($subscription_amt);
		if($damage_amt >0){
					$formatted_damage_amt = wc_price($damage_amt);
		}else{
					$formatted_damage_amt = 'No damage charges';
		}
		$formatted_remaining_amt = wc_price($remaining_amt);
	
		$html .= '</tbody></table></div>';
	
		echo $html;
	
		echo '<div class="remaining_amt">
			<table>
				<tr>
					<td>Deposit Amount</td>
					<td>' . wp_kses_post($formatted_subscription_amt) . '</td>
				</tr>
				<tr>
					<td>Damage And Other Charges</td>
					<td>' . wp_kses_post($formatted_damage_amt) . '</td>
				</tr>
				<tr>
					<td>Remaining Amount</td>
					<td>' . wp_kses_post($formatted_remaining_amt) . '</td>
				</tr>';
	
		// Display the refund button if no damages and no refund has been requested yet
		if ($remaining_amt > 0 && $refunded_amt < $remaining_amt && !$s->has_status(array('active')) && sizeof($return_book) < 1) {
			echo '
				<tr>
					<td></td>
					<td><a href="#" class="button" id="request-refund" data-subscription="' . esc_attr($active_subscription_id) . '" data-amount="' . esc_attr($remaining_amt) . '">Request Refund</a></td>
				</tr>';
		}
	
		if (!empty($refunds)) {
			foreach ($refunds as $refund) {
				$refund_status = $refund->get_refunded_payment();
				$refund_date = $refund->get_meta( '_refund_day_date' );
				$refund_remark = $refund->get_meta( '_refund_remark' );
				$refund_created_date = $refund->get_date_created();
	
				if ($refund_status) {
					$formatted_refund_date = date_i18n(get_option('date_format'), strtotime($refund_date));
					echo '<tr>
						<td><strong>Refunded on </strong>
						<div><strong>' . esc_html($formatted_refund_date) . '</strong></div>
						<div><strong>' . esc_html($refund_remark) . '</strong></div>
						</td>
						<td><strong>' . wc_price($refund->get_total()) . '</strong></td>
					</tr>';
				} else {
					echo '<tr>
						<td><strong>Refund on ' . date_i18n(get_option('date_format'), strtotime($refund_created_date)) . ' is Pending </strong></td>
						<td><a href="#" class="button cancel-refund" data-refund-id="' . esc_attr($refund->get_id()) . '">Cancel Refund</a></td>
					</tr>';
				}
			}
		}
	
		echo '</table></div>';
	
	
	
		// Adding JavaScript for AJAX
?>
	<script type="text/javascript">
			jQuery(document).ready(function($) {
				$('#request-refund').on('click', function(e) {
					e.preventDefault();
					var $this = $(this);
					var subscriptionId = $(this).data('subscription');
					var refundAmount = $(this).data('amount');
					        // Check if button is already disabled
							if ($this.hasClass('disabled')) {
								return; // Exit if the button is disabled
							}
							if (!confirm('Are you sure you want to request a refund of ' + refundAmount + '?')) {
								return; // Exit if user does not confirm
							}

					// Add 'disabled' class to visually indicate the button is disabled
					$this.addClass('disabled').text('Processing...');
					$.ajax({
						url: '<?php echo admin_url('admin-ajax.php'); ?>',
						type: 'POST',
						data: {
							action: 'process_refund',
							subscription_id: subscriptionId,
							refund_amount: refundAmount,
						},
						success: function(response) {
							// $this.prop('disabled', false).text('Request Refund');
							if (response.success) {
								alert('Refund Successful!.');
								location.reload(); // Reload the same page after refund
								$this.removeClass('disabled').text('Request Refund'); // Re-enable button

							} else {
								alert('Refund failed: ' + response.data);
								$this.removeClass('disabled').text('Request Refund'); // Re-enable button
							}
						}
					});
				});
				jQuery(document).on('click', '.cancel-refund', function(e) {
				e.preventDefault();

				var refundId = jQuery(this).data('refund-id');

				if (confirm('Are you sure you want to cancel this refund?')) {
					jQuery.ajax({
						url: '/wp-admin/admin-ajax.php',
						type: 'POST',
						data: {
							action: 'cancel_refund',
							refund_id: refundId,
						},
						success: function(response) {
							if (response.success) {
								alert('Refund cancelled successfully.');
								location.reload();
							} else {
								alert('Failed to cancel refund. Please try again.');
							}
						},
						error: function() {
							alert('Error cancelling the refund.');
						}
					});
				}
			});

			});
		</script>

		<?php
	}
	function process_refund() {
		if (isset($_POST['subscription_id']) && isset($_POST['refund_amount'])) {
			$subscription_id = absint($_POST['subscription_id']);
			$refund_amount = floatval($_POST['refund_amount']);
			$order = wc_get_order($subscription_id);
			if ($order) {
				$parent_id = $order->get_parent_id();
				$refund_id = wc_create_refund(array(
					'amount'     =>  $refund_amount,
					'reason'     => 'Refund requested by user',
					'order_id'   => $parent_id,
					'line_items' => array(),
					'refund_payment' => false
				));
	
				if (!is_wp_error($refund_id)) {
					wp_send_json_success();
				} else {
					wp_send_json_error($refund_id->get_error_message());
				}
			} else {
				wp_send_json_error('Order not found.');
			}
		} else {
			wp_send_json_error('Invalid request.');
		}
	}

	

function handle_cancel_refund() {
    if (!isset($_POST['refund_id'])) {
        wp_send_json_error('Invalid request');
    }

    $refund_id = intval($_POST['refund_id']);
    
    // Get the refund object
    $refund = wc_get_order($refund_id);

    // Ensure it's a refund order and is in the 'pending' state
//     if (!$refund || 'refund' !== $refund->get_type() || 'pending' !== $refund->get_status()) {
//         wp_send_json_error('Refund cannot be canceled');
//     }

    // Permanently delete the refund order
    wp_delete_post($refund_id, true); // 'true' ensures permanent deletion, not just moving to trash

    wp_send_json_success('Refund deleted successfully');
}
	
		function edit_my_account_order_refund_status($order_id){
		$order = wc_get_order( $order_id );
			$refunds = $order->get_refunds();

	// 			foreach ( $refunds as $refund ) {
	// 				// Get the current refund reason
	// 				$reason = $refund->get_reason();

	// 				// Check if the payment has been refunded
	// 				if ( ! $refund->get_refunded_payment() ) {
	// 					$pending_text = 'Refund Pending';
	// 					$pending_text = '(' . $pending_text . ')';
	// 				} else {
	// 					$refund_remark = $refund->get_meta( '_refund_remark' );
	// 					$refund_date = $refund->get_meta( '_refund_day_date' );
						
	// 				}

	// 				// Append the pending text to the reason
	// 				$reason = $reason . ' ' . $pending_text;

	// 				// Set the updated reason
	// 				$refund->set_reason( $reason );

	// 				// Save the updated refund object
	// 				$refund->save();
	// 			}
		foreach ( $refunds as $refund ) {
			// Get the current refund reason
			$reason = $refund->get_reason();
			
			// Check if the reason already contains the pending text
			if ( strpos( $reason, 'Refund Pending' ) === false && ! $refund->get_refunded_payment() ) {
				// If not refunded, append 'Refund Pending'
				$pending_text = 'Refund Pending';
				$pending_text = '(' . $pending_text . ')';
			} elseif ( strpos( $reason, 'Refunded on' ) === false && $refund->get_refunded_payment() ) {
				// If refunded, append 'Refunded on' and the formatted date
				$refund_remark = $refund->get_meta( '_refund_remark' );
				$refund_date = $refund->get_meta( '_refund_day_date' );
				$formatted_date = date_i18n( get_option('date_format') . ' ' . get_option('time_format'), strtotime($refund_date) );
				$pending_text = 'Refunded on ' . $formatted_date;
				
				// Append refund remark if needed
				if ( $refund_remark ) {
					$pending_text .= ' (' . $refund_remark . ')';
				}
			} else {
				// Skip if pending text is already in the reason
				continue;
			}

			// Append the pending text to the reason
			$reason = $reason . ' ' . $pending_text;

			// Set the updated reason
			$refund->set_reason( $reason );

			// Save the updated refund object
			$refund->save();
		}



	}

	public function send_email_notification($notification_data) {
		$subtotal = 0;
		$order_id = $notification_data['added_order_id'];
		$active_subscription_id = get_post_meta($order_id, 'active_subscription_id', true);
		$sub_damages = get_post_meta($active_subscription_id, 'sub_damages', true);
		$sub_damages[] = $notification_data;
		// $credits = 4; 
		$subscription = wcs_get_subscription($active_subscription_id);
		$user_id = $subscription->get_user_id();
		$user_info = get_userdata($user_id);
		$user_email = $user_info->user_email;
		
		$image_urls = [];
		foreach ($sub_damages as $k => $item) {
			if (!empty($item['sq_books_ids'])) {
				foreach ($item['sq_books_ids'] as $image_id) {
					$image_url = wp_get_attachment_image_src($image_id, 'full'); 
					if ($image_url) {
						$image_urls[] = $image_url[0];
					}
				}
				$sub_damages[$k]['sq_books_ids'] = $image_urls;
			}
		}
	
		$subject = "Important information related to your recent Order #" . esc_html($order_id);
		ob_start();
		include plugin_dir_path(dirname(__FILE__)) . 'admin/partials/bitss-squiggles-email-notification-display.php';
		$message = ob_get_contents();
		ob_end_clean();
		
		$headers = array('Content-Type: text/html; charset=UTF-8');
		$attachments = [];
		if (!empty($image_urls)) {
			foreach ($image_urls as $url) {
				$temp_file = $this->download_image($url);
				if ($temp_file) {
					$attachments[] = $temp_file;
				}
			}
		}

		if (!empty($user_email) && !empty($subject) && !empty($message)) {
			wp_mail($user_email, $subject, $message, $headers, $attachments);
		}
	
		if (!empty($attachments)) {
			foreach ($attachments as $file) {
				if (file_exists($file)) {
					unlink($file);
				}
			}
		}
	}
	
	function download_image($url) {
		$upload_dir = wp_upload_dir();
		$temp_file = $upload_dir['path'] . '/' . basename($url);
		$image_content = file_get_contents($url);
		if ($image_content) {
			file_put_contents($temp_file, $image_content);
			return $temp_file;
		}
	
		return false;
	}
	
	
	
}	
