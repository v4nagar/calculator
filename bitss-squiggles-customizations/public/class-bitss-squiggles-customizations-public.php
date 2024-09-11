<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://amitmittal.tech
 * @since      1.0.0
 *
 * @package    Bitss_Squiggles_Customizations
 * @subpackage Bitss_Squiggles_Customizations/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Bitss_Squiggles_Customizations
 * @subpackage Bitss_Squiggles_Customizations/public
 * @author     Amit Mittal <amitmittal@bitsstech.com>
 */
class Bitss_Squiggles_Customizations_Public {

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

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-bitss-squiggles-customizations-book-shelf.php';
		
		$this->book_shelf_obj = new Bitss_Squiggles_Customizations_Book_Shelf( $this->plugin_name, $this->version );
		
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/bitss-squiggles-customizations-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/bitss-squiggles-customizations-public.js', array( 'jquery' ), $this->version, false );
		wp_localize_script($this->plugin_name, 'bitss_squiggles_vars', array(
			'ajax_url' => admin_url('admin-ajax.php'),
			"search_user_nonce" => wp_create_nonce('api_call_search_for_user'),
			)
		);
		
	}

	public function do_shortcode_bt_login_form()
	{
		$args = array(
		  'echo'           => true,
		  'remember'       => true,
		  'redirect'       => ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'],
		  'form_id'        => 'loginform',
		  'id_username'    => 'user_login',
		  'id_password'    => 'user_pass',
		  'id_remember'    => 'rememberme',
		  'id_submit'      => 'wp-submit',
		  'label_username' => __( 'Username or Email Address' ),
		  'label_password' => __( 'Password' ),
		  'label_remember' => __( 'Remember Me' ),
		  'label_log_in'   => __( 'Log In' ),
		  'value_username' => '',
		  'value_remember' => false
	  );
	  wp_login_form($args);
	
	}

	public function add_elementor_register_form_action($form_actions_registrar)
	{
		//include_once( __DIR__ .  '/form-actions/ping.php' );

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'elementor/registration_form_submit_action.php';

		$form_actions_registrar->register( new \Registration_Form_Action_After_Submit() );

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'elementor/login_form_action.php';

		$form_actions_registrar->register( new \Login_Form_Action() );
	
	}

	public function woocommerce_currencies( $cw_currency ) {
		$cw_currency['squiggles_credits'] = __( 'Squiggles Credits', 'woocommerce' );
		return $cw_currency;
    }

    public function woocommerce_currency_symbol( $custom_currency_symbol, $custom_currency ) {
		switch( $custom_currency ) {
			case 'squiggles_credits': 'Credits'; break;
		}

		if ( has_term( 'book', 'product_cat' ) ) {
			$custom_currency_symbol = 'Credits';
		}
	
		return $custom_currency_symbol;
    }

	public function init_book_shelf_data(){
		$this->books_shelf = $this->book_shelf_obj->Get_User_Book_Shelf();
	}

	public function woocommerce_product_is_in_stock($is_in_stock, $product  ) {

		$books = array_column($this->books_shelf["books"], 'product_id'); ;
		if (in_array($product->get_id(), $books)){
			$is_in_stock = true;
		}
	
		return $is_in_stock;
    }

	public function woocommerce_product_add_to_cart_text( $add_to_cart_text, $product ) {
		if($product->is_in_stock()){

			if ( has_term( 'book', 'product_cat',$product->get_id() ) ) {

				//$this->books_shelf = $this->book_shelf_obj->Get_User_Book_Shelf();
				$books = array_column($this->books_shelf["books"], 'product_id'); ;
				if (in_array($product->get_id(), $books)){
					$add_to_cart_text = "Return";
				}else{
					$add_to_cart_text = "Issue";
				}
			}else{
				$add_to_cart_text = "Add to Bag";
			}
		}else{

			//$this->books_shelf = $this->book_shelf_obj->Get_User_Book_Shelf();
			$books = array_column($this->books_shelf["books"], 'product_id'); ;
			if (in_array($product->get_id(), $books)){
				$add_to_cart_text = "Return";
			}else{
				$add_to_cart_text = "Out of Stock";
			}
		}
		return $add_to_cart_text;
	}

	public function redirect_product_smart_id(){
		if(isset($_GET["qrbook"])){
			$qrbook = $_GET["qrbook"];
			$ids = explode("_",$qrbook);
			$smart_id = "";
			$copy_id = "";
			if(sizeof($ids)>0){
				$smart_id = $ids[0];
			}
			if(sizeof($ids)>1){
				$copy_id = $ids[1];
			}
			if(empty($smart_id)){
				wp_safe_redirect("/");
				exit;
			}

			$filters_codes = array(
					'post_status' => 'publish',
					'post_type' => 'product',
					'fields' => 'ids',
					'meta_key' => 'wpcf-'.'smart_product_id',
					'meta_query' => array(
						array(
							'key' => 'wpcf-'.'smart_product_id',
							'value' => $smart_id,
							'compare' => '='
						)
					)
			);		
			$products = new WP_Query($filters_codes);
			foreach ($products->posts as $postid){
				$product_link = get_permalink($postid);
				if(!empty($copy_id)){
					//to do. planned for next phase
					//setcookie('qr_book_copy_'.$postid, $copy_id, strtotime('+1 day'));
				}
				wp_safe_redirect($product_link);
				exit;
			}
			
		}

		

		if (isset( $_GET['empty-cart'] ) ) { 
			WC()->cart->empty_cart();
			wp_safe_redirect('/cart');
			die();
		}

		if (isset( $_GET['bt_return_all_books'] ) ) { 
			if($this->books_shelf==null)
				$this->books_shelf = $this->book_shelf_obj->Get_User_Book_Shelf();
			$books = array_column($this->books_shelf["books"], 'product_id');
			foreach ($books as  $product_id) {
				$is_prod = false;

				foreach ( WC()->cart->get_cart() as $cart_item ) {
					$product = $cart_item['data'];
					if( ! empty($product) ){
						$cart_product_id = method_exists( $product, 'get_id' ) ? $product->get_id() : $product->id;
						if($cart_product_id==$product_id){
							$is_prod = true;
							break;
						}
					}
				}
				
				if(!$is_prod) {      //make sure product is not already in cart
					WC()->cart->add_to_cart( $product_id);
				}
			}

			wp_safe_redirect('/cart');
			die();
		}
	}

	public function get_search_form($search_form) {
		$search_form = str_replace( '</form>', '<input type="hidden" name="post_type" value="product"/></form>', $search_form );
		return  $search_form;
	}

	public function wp_head() {
		if(!is_user_logged_in()){
			echo '<style type="text/css">#primary-site-navigation2,.ast-header-search2,#yith-wcwl-items-5,.ast-header-woo-cart2,.ast-builder-menu-mobilee,.ast-header-button-22,.main-header-menu-togglee {displayy:none !important;}</style>';
	  }
	  
	  if(is_checkout()&&is_user_logged_in()){
			  //hide billing fields if data is already present
			  $user_id = get_current_user_id();
			  if(!empty(get_user_meta($user_id,'billing_first_name',true))){
				  echo '<style>
					form[name=checkout] .woocommerce-billing-fields{
					  display:none !important;
					}
				  </style>
				  ';
			  }
	  }
	}

	public function woocommerce_product_tabs($tabs ) {
		unset( $tabs['description'] );      	// Remove the description tab
		// unset( $tabs['reviews'] ); 			// Remove the reviews tab
	    unset( $tabs['additional_information'] );  	// Remove the additional information tab
		 
		$tabs['additional_book_information'] = array(
			'title' 	=> __( 'Additional Information', 'woocommerce' ),
			'priority' 	=> 10,
			'callback' 	=> array($this,'product_additional_information_tab_content')
		);
		$tabs['author'] = array(
			 'title' 	=> __( 'About the Author', 'woocommerce' ),
			 'priority' 	=> -10,
			 'callback' 	=> array($this,'product_author_tab_content')
		);

		$tabs['reviews']['priority'] = 50;			// Reviews first

		$tabs['goodreadreviews'] = array(
			'title' 	=> __( 'Goodreads Reviews', 'woocommerce' ),
			'priority' 	=> 55,
			'callback' 	=> array($this,'product_goodreadreviews_tab_content')
	   );
		$tabs['additional_book_information']['priority'] = 40;			// Description second
		$tabs['author']['priority'] = 0;	// Additional information third

		 return $tabs;
	}

	public function product_goodreadreviews_tab_content() {
		$this->bt_book_goodread_reviews();
	}

	public function product_author_tab_content() {
		$content= "";
		$authors = get_the_terms( get_the_ID(), 'book-author' );	
		if($authors !== false){
			foreach ($authors as $key => $term) {
				$template = do_shortcode('[elementor-template id="6407"]');
				$template = str_replace('#author_name#',$term->name,$template);
				$template = str_replace('#author_desc#',$term->description,$template);
				$image_url = get_term_meta($term->term_id,'wpcf-'.'author-image',true);
				if(empty($image_url)){
					$image_url = "/wp-content/uploads/2021/08/woocommerce-placeholder.png";
				}
				$template = str_replace('#author_img#',$image_url,$template);
				$content= $content . $template;
			}
		}
		echo $content;
	}

	public function product_additional_information_tab_content() {
		global $product;
		$content = do_shortcode('[elementor-template id="6395"]');
		$d = $product->get_length() . ' x ' . $product->get_width() . ' x ' . $product->get_height();
		$content  = str_replace('#dimensions#',$d,$content );
		$min_age = get_post_meta($product->get_id(),'wpcf-'.'min-age',true);
		$max_age = get_post_meta($product->get_id(),'wpcf-'.'max-age',true);
		if(!empty($min_age) && !empty($max_age)){
			$d = $min_age . " - " . $max_age . ' years';
			$content  = str_replace('#reading_age#',$d,$content );
		}else{
			$content  = str_replace('#reading_age#',"",$content );
		}
		echo $content;
	}

	public function bt_book_goodread_reviews() {
		global $product;
		if ( is_a( $product, 'WC_Product' ) ) {
		
			$title = $product->get_title();
			$isbn =  get_post_meta( $product->get_id(), 'wpcf-'.'isbn13',true);
			if(empty($isbn)) return;
			$string = '
			
					<style>
					#goodreads-widget {
					font-family: georgia, serif;
					padding: 18px 0;
					width:98%;
					}
					#goodreads-widget h1 {
					font-weight:normal;
					font-size: 16px;
					border-bottom: 1px solid #BBB596;
					margin-bottom: 0;
					}
					#goodreads-widget a {
					text-decoration: none;
					color:#660;
					}
					iframe{
					background-color: #fff;
					}
					#goodreads-widget a:hover { text-decoration: underline; }
					#goodreads-widget a:active {
					color:#660;
					}
					#gr_footer {
					width: 100%;
					border-top: 1px solid #BBB596;
					text-align: right;
					}
					#goodreads-widget .gr_branding{
					color: #382110;
					font-size: 11px;
					text-decoration: none;
					font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
					}
				</style>
				<div id="goodreads-widget">
					<div id="gr_header"><h1><a rel="nofollow" href="#">Goodreads reviews for '.$title.'</a></h1></div>
					<iframe sandbox id="the_iframe" src="https://www.goodreads.com/api/reviews_widget_iframe?did=DEVELOPER_ID&format=html&header_text=Goodreads+reviews+for+'.$title.'&isbn='.$isbn.'&links=660&review_back=fff&stars=000&text=000" style="width:100%" height="400" frameborder="0"></iframe>
					<div id="gr_footer">
					<a class="gr_branding" target="_blank" rel="nofollow noopener noreferrer" href="#">Reviews from Goodreads.com</a>
					</div>
				</div>
				
			  
			
			';
			
			echo  $string;
		}
	
	}

	public function pre_get_posts($query ) {
		if ( $query->is_main_query() && is_user_logged_in() && !current_user_can( 'administrator' )) {
			//work-around for using is_front_page() in pre_get_posts
			//known bug in WP tracked by https://core.trac.wordpress.org/ticket/21790
			$front_page_id = get_option('page_on_front');
			$current_page_id = $query->get('page_id');
			$is_static_front_page = 'page' == get_option('show_on_front');
		
			if ($is_static_front_page && $front_page_id == $current_page_id) {
			  $query->set('page_id', 142);
			}
		  }
	}

	public function woocommerce_after_shop_loop_item() {
		$wishlist = do_shortcode("[bt_add_to_wishlist]");
		echo $wishlist;
	}

	public function bt_add_to_wishlist() {
		global $product;
		$str="";
		$avl="";
		if ( is_a( $product, 'WC_Product' ) ) {
			$product_id = $product->get_id();
			
			//$link = $product->get_permalink();
			if ( has_term( 'book', 'product_cat',$product_id ) ) {

				//$this->books_shelf = $this->book_shelf_obj->Get_User_Book_Shelf();
				$books = array_column($this->books_shelf["books"], 'product_id'); ;
				if (in_array($product_id , $books)){
					//book is in shelf, check if copy status is not delivered
					$return_copy_post_ids = array_filter($this->books_shelf["books"], function ($obj) use ($product_id) {
						return $obj["product_id"] == $product_id;
					});
					$return_copy_post_id =  reset($return_copy_post_ids)["copy_post_id"];
					$copy_status = get_post_meta( $return_copy_post_id, 'wpcf-'.'copy-status',true);
					if($copy_status !="issued_delivered"){
						$avl = "bt_copy_status";
						$str = $copy_status;
					}
					else if($copy_status =="issued_delivered"){
						$avl = "";
						$str = do_shortcode("[bt_add_to_cart class='bt_add_to_cart' id='".$product->get_id()."' show_price='false'] ");
					}
				}
				if(empty($str)){
					$s= $product->is_in_stock()?"Available":"In Circulation";
					$wishlist = do_shortcode("[yith_wcwl_add_to_wishlist]");
					$str = str_replace("Remove from list","","$wishlist");
					if ($s == "Available") {
						$str = str_replace("Add to wishlist","","$str") . " <span>Available<span>";			
						$avl = "available";
					} else {
						$str = str_replace("Add to wishlist","","$str"). " <span>In Circulation<span>";;
						$avl = "incirculation";
					}
				}
			}
			echo   "<div class='$avl'>$str</div>";

			$is_in_history = $this->book_shelf_obj->is_in_books_issue_history($product_id );

			if($is_in_history){
				echo "<div class='bt_has_read_before shine'>Previously Read</div>";
			}


		}
	
	}

	public function bt_book_shelf(){
		//$this->books_shelf = $this->book_shelf_obj->Get_User_Book_Shelf();
		$books = array_column($this->books_shelf["books"], 'product_id');

		if(sizeof($books)>0){
			$books_string = implode(',',$books);
			$sc = do_shortcode("[wccarousel itemswc=5 autoplay=true] [products columns=1 limit=10 ids='".$books_string."'][/wccarousel] ");
		}else{
			$sc = "Your book shelf is currently empty."; 
		}
	
		echo '<div style="margin:10px;"><h2>My Book Shelf ('.sizeof($books).')
		<br><small style="font-size: 14px;"><a href="/my-account/reading-history/">See your reading history</a></small>';

		if(sizeof($books)){
			echo ' <small style="font-size: 14px;"><a href="?bt_return_all_books=1">Return all books</a></small>';
		}
		
		echo '</h2>'. $sc . '</div>';
	}

	public function bt_user_plan(){
		return $this->display_subscription_details();
	}

	public function bt_add_to_cart(){
		global $product;
		$btn = "";
		
		if ( is_a( $product, 'WC_Product' ) ) {
			$additional_classes = "";
			if ( WC()->cart ) {
				$cart = WC()->cart; // Get cart
				if ( ! $cart->is_empty() ) {
					foreach ( $cart->get_cart() as $cart_item_key => $cart_item ) {
						$_product_id = $cart_item['product_id'];
						if ( $product->get_id() == $_product_id ) {
							$additional_classes = "added disabled";
							break;
						}
					}
				}
			}

			$btn = do_shortcode("[add_to_cart class='bt_add_to_cart ".$additional_classes." ' id='".$product->get_id()."' show_price='false'] ");
			$product_id = $product->get_id();
			if ( has_term( 'book', 'product_cat',$product_id ) ) {

				//$this->books_shelf = $this->book_shelf_obj->Get_User_Book_Shelf();
				$books = array_column($this->books_shelf["books"], 'product_id'); ;
				if (in_array($product_id , $books)){
					//book is in shelf, check if copy status is not delivered
					$return_copy_post_ids = array_filter($this->books_shelf["books"], function ($obj) use ($product_id) {
						return $obj["product_id"] == $product_id;
					});
					$return_copy_post_id =  reset($return_copy_post_ids)["copy_post_id"];
					$copy_status = get_post_meta( $return_copy_post_id, 'wpcf-'.'copy-status',true);
					if($copy_status !="issued_delivered"){
						$btn = "<div class='bt_copy_status'>" . $copy_status ."</div>";
					}
				}
			}
		}
		echo $btn;
	}

	public function woocommerce_loop_add_to_cart_link($cart_btn_html,$product,$args){
		if ( is_a( $product, 'WC_Product' ) ) {
		
			$product_id = $product->get_id();
			if ( has_term( 'book', 'product_cat',$product_id ) ) {

				//$this->books_shelf = $this->book_shelf_obj->Get_User_Book_Shelf();
				//echo json_encode($this->books_shelf);exit;
				$books = array_column($this->books_shelf["books"], 'product_id'); ;
				if (in_array($product_id , $books)){
					//book is in shelf, check if copy status is not delivered
					$return_copy_post_ids = array_filter($this->books_shelf["books"], function ($obj) use ($product_id) {
						return $obj["product_id"] == $product_id;
					});
					$return_copy_post_id =  reset($return_copy_post_ids)["copy_post_id"];
					$copy_status = get_post_meta( $return_copy_post_id, 'wpcf-'.'copy-status',true);
					if($copy_status !="issued_delivered"){
						$cart_btn_html = "<div class='bt_copy_status'>" . $copy_status ."</div>";
					}
				}
			}
		}

		return $cart_btn_html;
	}

	public function woocommerce_is_purchasable( $is_purchasable, $product){

		// if ( is_a( $product, 'WC_Product' ) ) {
			
		// 	$product_id = $product->get_id();
		// 	if ( has_term( 'book', 'product_cat',$product_id ) ) {

		// 		$this->books_shelf = $this->book_shelf_obj->Get_User_Book_Shelf();
		// 		$books = array_column($this->books_shelf["books"], 'product_id'); ;
		// 		if (in_array($product_id , $books)){
		// 			//book is in shelf, check if copy status is not delivered
		// 			$return_copy_post_ids = array_filter($this->books_shelf["books"], function ($obj) use ($product_id) {
		// 				return $obj["product_id"] == $product_id;
		// 			});
		// 			$return_copy_post_id =  reset($return_copy_post_ids)["copy_post_id"];
		// 			$copy_status = get_post_meta( $return_copy_post_id, 'wpcf-'.'copy-status',true);
		// 			if($copy_status !="issued_delivered"){
		// 				$is_purchasable = false;
		// 			}

		// 		}
		// 	}
		// }
		return $is_purchasable;
	}

	public function woocommerce_catalog_orderby( $options ){
	
		//unset( $options[ 'price-desc' ] ); // remove
		$options[ 'price-desc' ] = 'Sort by credits: High to Low'; // rename
		$options[ 'price' ] = 'Sort by credits: Low to High'; 
		return $options;
		
	}

	public function otp_resend(){
	
		$resp = array(
			"msg"=>"An error happened."
		);

		if(isset($_POST["mobno"])){
		
			$phone = $_POST["mobno"];
			// set_transient( 'otp_' . $phone, $otp, 10 * MINUTE_IN_SECONDS   );
			// $this->send_otp_for_verify($phone,$otp);

			if(class_exists('Otpfy_For_Wordpress')){
				Otpfy_For_Wordpress::send_otp($phone);
				$resp = array(
					"msg"=>"OTP sent successfully."
				);
			}else{
				$resp = array(
					"msg"=>"An error happened, please try again."
				);
			}
			
		
		}
		
		wp_send_json($resp);
	}

	public function wp_footer_bitss_backlink(){
		
		?>
		<div class="bitss_footer_baklink" style="text-align:center;background-color:white;font-size:12px;">
			<span style="color:#000">Copyright &copy; <?php echo gmdate( 'Y' ); ?> <?php bloginfo( 'name' ); ?></span>
			<span style="color:#000"> | </span>
			<span id="fl-site-credits" style="color:#000">Powered by <a  style="color:#000;font-weight:600" href="http://bitss.tech" target="_blank" title="Bitss Techniques" rel="dofollow noopener"><span style="color:#f02121">Bitss</span>Tech</a></span>
		</div>
		<?php
	}

	public function woocommerce_cart_calculate_fees($cart){
		if ( empty( $cart->recurring_cart_key ) ) {
			$items = $cart->get_cart();
			foreach($items as $item => $values) {
				$product = wc_get_product( $values["product_id"] );
				if ( class_exists( 'WC_Subscriptions_Product' ) && WC_Subscriptions_Product::is_subscription( $product ) ) {
					$plan_deposit_amt = get_post_meta($values["product_id"],'wpcf-'.'deposit-amount',true);
					if($plan_deposit_amt >0){
						$cart->add_fee( 'Security Deposit',$plan_deposit_amt);
					}
					break;
				}
			}
		
		}

		//damage and other charges from subscription
		$active_subscription = $this->book_shelf_obj->get_active_subscription();
		if($active_subscription){
			$sub_damages = get_post_meta( $active_subscription->get_id(), 'sub_damages', true);
			foreach ($sub_damages as $charge) {
				$charge = (array)$charge;
				if(!isset($charge["paid_order_id"]) && $charge["amt"]>0){
					$cart->add_fee( $charge["type"] . "(".$charge["remark"].")" ,$charge["amt"]);
				}
			}
		}
	}

	public function woocommerce_cart_item_price( $price, $cart_item, $cart_item_key){

		//return json_encode($cart_item);
		$product_id =  $cart_item["product_id"];
		if ( has_term( 'book', 'product_cat', $product_id ) ) {
			$price =  $cart_item[ 'data' ]->get_price() . ' Credits';
		}
	
		return $price;
	}
	public function woocommerce_get_price_html( $price, $product){

		$product_id =  $product->get_id();
		if ( has_term( 'book', 'product_cat', $product_id ) ) {
			$price =  $product->get_price() . ' Credits';
		}
	
		return $price;
	}

	public function woocommerce_cart_item_subtotal( $subtotal, $cart_item, $cart_item_key){

		$product_id =  $cart_item["product_id"];
		if ( has_term( 'book', 'product_cat', $product_id ) ) {
			$subtotal  =  $cart_item[ 'data' ]->get_price() . ' Credits';
		}
	
		return $subtotal ;
	}

	function get_cart_totals($cart=null){
		if(empty($cart)){
			$cart =  WC()->cart;
		}
		$plan_credits = 0;
		$active_subscription = $this->book_shelf_obj->get_active_subscription();
		if($active_subscription){
			$plan_credits = get_post_meta($active_subscription->get_id(),'wpcf-'.'subscription-credits',true);
		}
		
		$items = $cart->get_cart();
		$cart_subtotal = 0;
		$issue_credits = 0;
		$return_credits = 0;
		
		//$this->books_shelf = $this->book_shelf_obj->Get_User_Book_Shelf();
		//echo json_encode($this->books_shelf );exit;
		$book_shelf_credits = $this->books_shelf["credits"];
		foreach($items as $item => $values) {
			//print_r($values);exit;
			if ( !has_term( 'book', 'product_cat', $values["product_id"]) ) {
				$line_subtotal      = $values['line_subtotal']; 
				// $line_subtotal_tax  = $cart_item['line_subtotal_tax'];
				// // gets the cart item total
				// $line_total         = $cart_item['line_total'];
				// $line_tax           = $cart_item['line_tax'];
				$cart_subtotal = $cart_subtotal + $line_subtotal ;
			}else{
				$line_subtotal      = $values['line_subtotal']; 
				if($values["issue_or_return"]=="issue"){ //book issued - todo
					$issue_credits = $issue_credits + $line_subtotal ;
				}else{
					//book for return
					$return_credits = $return_credits + $line_subtotal ;
				}
				
			}
		}
		$available_credits = $plan_credits - $issue_credits - ($book_shelf_credits-$return_credits);
		$resp =  array(
			"cart_subtotal" => $cart_subtotal,
			"issue_credits" => $issue_credits,
			"return_credits" => $return_credits,
			"plan_credits" => $plan_credits,
			"book_shelf_credits" => $book_shelf_credits,
			"available_credits" => $available_credits
		);
		//print_r($resp);
		return $resp;
	}

	
	function validate_cart_item_copies($errors=null,$cart=null){
		if(empty($cart)){
			$cart =  WC()->cart;
		}
		$copy_available = array();
		$copy_not_available = array();
		$items = $cart->get_cart();
		foreach($items as $item => $values) {
			//print_r($values);exit;
			$product_id = $values["product_id"];
			if ( has_term( 'book', 'product_cat', $product_id ) ) {
				$issue_or_return = $values["issue_or_return"];
				if($issue_or_return=="issue"){
					$available_copies = $this->book_shelf_obj->find_available_copies_of_book($product_id);
					if(sizeof($available_copies)<1){
						//no copy available, raise error message
						$copy_not_available[] = $product_id;
						if($errors!=null){
							$product = wc_get_product(  $product_id );
							$errors->add( 'validation', 'Error processing order, unable to find an available copy of "'.  $product->get_title() .'" for issue.' );
						}
					}else{
						$copy_available[] = $product_id;
					}
				}
			}
		}

		$resp = ["copy_not_available"=>$copy_not_available,"copy_available"=>$copy_available];

		//echo json_encode($resp);exit;
	
		return sizeof($copy_not_available)>0;
	}

	public function woocommerce_cart_subtotal( $cart_subtotal, $compound, $cart ) { 
		$credits_totals = $this->get_cart_totals($cart);
		$cart_subtotal = $credits_totals["cart_subtotal"];
		$issue_credits =  $credits_totals["issue_credits"];
		$return_credits =  $credits_totals["return_credits"];
		$plan_credits =  $credits_totals["plan_credits"];
		$book_shelf_credits =  $credits_totals["book_shelf_credits"];
		$available_credits =  $credits_totals["available_credits"];

		if($issue_credits > 0 || $return_credits > 0  || $available_credits > 0){
		// $html = '<table>
		// 	<tr>
		// 		<td>Issue Credits</td><td>:</td><td>'.$issue_credits .'</td>
		// 	</tr>
		// 	<tr>
		// 		<td>Return Credits</td><td>:</td><td>'.$return_credits .'</td>
		// 	</tr>
		// 	<tr>
		// 		<td>Available Credits</td><td>:</td><td>'.$available_credits .'</td>
		// 	</tr>
		// 	<tr>
		// 		<td>Payable</td><td>:</td><td>'. get_woocommerce_currency_symbol().$cart_subtotal .'</td>
		// 	</tr>
		// </table>' ;

		$available_css_class = "available";
		if($available_credits<0){
			$available_css_class = "not_available";
		}

		$html = '
			<div class="credits_totals"><br>
				<span>Issue Credits: '.$issue_credits .'</span><br>
				<span>Return Credits: '.$return_credits .'</span><br>
				<span class="'.$available_css_class.'">Available Credits: '.$available_credits .'</span><br>
			</div>
		
		';
		if($available_credits<0){
			$html = $html . '
				<style>
					a.checkout-button 
					{
						pointer-events:none;
						cursor: not-allowed;
						background-color: rgb(229, 229, 229) !important;
					}
				
					a.checkout-button > * 
					{
						pointer-events:none;
					}
				</style>
			';
		}
		}else{
			$html = get_woocommerce_currency_symbol().$cart_subtotal ;
		}
		return $html; 
	}

	public function woocommerce_cart_totals_order_total_html( $order_total ) { 
		if(WC()->cart->needs_shipping()){
			$currency = get_woocommerce_currency_symbol();
			$items = WC()->cart->get_cart();
			$order_total = (float)preg_replace('/[^0-9\.]/', "", html_entity_decode($order_total) ); 
			foreach($items as $item => $values) {
				if ( has_term( 'book', 'product_cat', $values["product_id"]) ) {
					$line_total         = floatval($values['line_total']);
					$order_total = $order_total - $line_total  ;
				}
			}
			$order_total = $currency . $order_total;
		}
		return $order_total; 

	}

	function woocommerce_cart_needs_payment( $needs_payment, $cart  ) {
		// $items = $cart->get_cart();
		// $order_total = 0;
		// foreach($items as $item => $values) {
		// 	if ( !has_term( 'book', 'product_cat', $values["product_id"]) ) {
		// 		//$line_subtotal      = $values['line_subtotal']; 
		// 		// $line_subtotal_tax  = $cart_item['line_subtotal_tax'];
		// 		// // gets the cart item total
		// 		 $line_total         = $values['line_total'];
		// 		// $line_tax           = $cart_item['line_tax'];
		// 		$order_total = $order_total + $line_total ;
		// 	}
		// }

		// return $order_total>0?$needs_payment:false;
		return $needs_payment;
	}

	public function woocommerce_order_button_html( $button ) {

		//$credits_totals = $this->get_cart_totals($cart);
		//$available_credits =  $credits_totals["available_credits"];

		//if($available_credits<0){
			//block order
		//	$button = "<button style='width:100%;cursor: not-allowed;' disabled>Place Order</button>";
		//}
		

		return $button;
	}

	public function woocommerce_after_checkout_validation( $fields, $errors ){
		$active_subscription = $this->book_shelf_obj->get_active_subscription();
		if($active_subscription){
			$credits_totals = $this->get_cart_totals();
			$issue_credits = $credits_totals["issue_credits"];
			$available_credits =  $credits_totals["available_credits"];
			if(($issue_credits > 0)  && $available_credits<0){ //validate only if any book is added to cart for issue.
				$errors->add( 'validation', 'You cannot order more books than your plan allows. Please review your cart and try again.' );
			}

			$validate_copies = $this->validate_cart_item_copies($errors);
			if($validate_copies){
				//$errors->add( 'validation', 'Error processing order, unable to find an available copy of one of the selected books for issue.' );
			}
		}
	}

	function woocommerce_add_cart_item_data( $cart_item_data, $product_id ) {
		if ( has_term( 'book', 'product_cat',$product_id ) ) {

			//$this->books_shelf = $this->book_shelf_obj->Get_User_Book_Shelf();
			$books = array_column($this->books_shelf["books"], 'product_id'); ;
			if (in_array($product_id , $books)){

				$return_copy_post_ids = array_filter($this->books_shelf["books"], function ($obj) use ($product_id) {
					return $obj["product_id"] == $product_id;
				});
				//echo json_encode($return_copy_post_ids );exit;
				//[{"product_id":6745,"copy_post_id":"11411","credits":"2"}]
				$cart_item_data['issue_or_return'] = 'return';
				$cart_item_data['return_copy_post_id'] = reset($return_copy_post_ids)["copy_post_id"];
				$cart_item_data['credits'] =  reset($return_copy_post_ids)["credits"];
			}else{
				$cart_item_data['issue_or_return'] = 'issue';
				
				$product = wc_get_product(  $product_id );
				$cart_item_data['credits'] = $product->get_price();
			}
		}
		return $cart_item_data;
	}

	

	public function woocommerce_before_cart(){
		
		if ( method_exists( 'user_switching', 'get_old_user' ) ) {
			$old_user = user_switching::get_old_user();
			if ( $old_user && user_can( $old_user->ID, 'list_users' )) {
				do_shortcode('[bt_library_poc]');
			}
		}
	}

	public function woocommerce_cart_is_empty(){
		if ( method_exists( 'user_switching', 'get_old_user' ) ) {
			$old_user = user_switching::get_old_user();
			if ( $old_user && user_can( $old_user->ID, 'list_users' )) {
				do_shortcode('[bt_library_poc]');
			}
		}

	}


	public function display_subscription_details(){
		$string ="";

		$active_subscription = $this->book_shelf_obj->get_active_subscription();
		if($active_subscription){
			$plan_name = "NA";
			foreach( $active_subscription->get_items() as $item_id => $product ){
				// Get the name
				$plan_name = $product->get_name();
				break;
			}
			$plan_credits = get_post_meta($active_subscription->get_id(),'wpcf-'.'subscription-credits',true);
			$plan_free_delivery = get_post_meta($active_subscription->get_id(),'wpcf-'.'subscription-free-delivery-per-month',true);
			$plan_delivery_fee = get_post_meta($active_subscription->get_id(),'wpcf-'.'subscription-delivery-fee',true);
			$plan_plus_member = get_post_meta($active_subscription->get_id(),'wpcf-'.'subscription-squiggles-plus-membership',true);
			$plan_deposit_amt = get_post_meta($active_subscription->get_id(),'wpcf-'.'subscription-deposit-amount',true);
			$delivery_order_ids = $this->get_user_delivery_orders_current_billing_cycle();
			$subscription_length = $active_subscription->get_time( 'next_payment' );


			$string = $string . "<div class='user-plan-text'>You are currently on <a class='user-plan' href='#'>" . $plan_name . '</a></div>';
			$content = do_shortcode('[elementor-template id="13526"]');
			$content  = str_replace('#plan#',$plan_name,$content );
			$content  = str_replace('#free_delivery#',$plan_free_delivery,$content );
			$content  = str_replace('#credits#',$plan_credits,$content );
			$content  = str_replace('#valid_till#',date('d/m/Y', $subscription_length),$content );
			$content  = str_replace('#delivery_fee#',wc_price($plan_delivery_fee),$content );
			$content  = str_replace('#delivery_remaining#',$plan_free_delivery - sizeof($delivery_order_ids),$content );
			$string = $string . $content;
			//include_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/bitss-squiggles-customizations-public-plan-details.php';		
		}else{
			echo "No active subscription. <a href='/user-registration-select-plan/'>See Plans</a>";
		}

		return $string;
	}

	function get_user_delivery_orders_current_billing_cycle(){
		$active_subscription = $this->book_shelf_obj->get_active_subscription();
		$delivery_order_ids = [];
		if($active_subscription){

			$subscription_order_ids = $active_subscription->get_related_orders('ids');
			$outlet_timezone=new DateTimeZone( get_option('timezone_string'));
			$last_payment_date = new DateTime($active_subscription->get_date( 'start', 'site' ),$outlet_timezone);
			
			$currentMonth = date('m'); // Get the current month as a two-digit number
			$currentYear = date('Y'); // Get the current year

			$specificDate = $last_payment_date->format('d'); // Replace with the desired day of the month

			// Create a DateTime object for the specific date in the current month
			$current_month_reset_date = new DateTime("$currentYear-$currentMonth-$specificDate",$outlet_timezone);
			$start_date=null;
			if($current_month_reset_date < new DateTime("now",$outlet_timezone)){
				$start_date = $current_month_reset_date;
			}else{
				$start_date = $current_month_reset_date->modify('-1 month');
			}

			//echo json_encode($start_date );exit;

			
			$order_statuses=array('wc-processing','wc-completed');
			
			$filters_orders = array(
				'post_status' =>  $order_statuses,
				'posts_per_page'   => -1,
				'post_type'   => 'shop_order',
				'fields' => 'ids',
				'date_query' => array(
					'after' => $start_date->format('Y-m-d')
				),
				'post__not_in' => $subscription_order_ids ,
				'meta_query'  => array(
				'relation' => 'AND',
				array(
						'key'     => '_customer_user',
						'value'   => $active_subscription->get_user_id(),
						'compare' => '=',
					),
				),
			);
			$orders = new WP_Query($filters_orders);        
			
			foreach ($orders->posts as $order_id) {
				$order = wc_get_order( $order_id );
				$shipping_method_name = $order->get_shipping_method();
				//echo $shipping_method_name . " ";
				if (strpos(strtolower ($shipping_method_name), 'delivery') !== false) {
					$delivery_order_ids[] = $order_id;
				}
			
			}
		}
		//echo json_encode( $delivery_order_ids);exit;
		return $delivery_order_ids;
	}

	public function woocommerce_package_rates( $rates ) {
	
		$active_subscription = $this->book_shelf_obj->get_active_subscription();

		if($active_subscription){

			 //1. find lowest price shipping method
			 $lowest_rate_id = null;
			 foreach( $rates as $rate_id => $rate ) {
				   if( $rate->method_id  == 'flat_rate' || $rate->method_id  == 'szbd-shipping-method' || $rate->method_id  == 'free_shipping' ) {
					   if($lowest_rate_id==null){
						   $lowest_rate_id = $rate_id;
					   }else{
						   if($rate->cost < $rates[$lowest_rate_id]->cost){
							   $lowest_rate_id = $rate_id;
						   }
					   }
				   }
			 }

			$plan_free_delivery = get_post_meta($active_subscription->get_id(),'wpcf-'.'subscription-free-delivery-per-month',true);
			$plan_delivery_fee = get_post_meta($active_subscription->get_id(),'wpcf-'.'subscription-delivery-fee',true);

			$delivery_order_ids = $this->get_user_delivery_orders_current_billing_cycle();
			// echo json_encode($delivery_order_ids);
			// echo "<br>";
			// echo $plan_free_delivery;
			// exit;
			
			if(sizeof($delivery_order_ids)>=$plan_free_delivery){
				//chaargeable delivery
				 //2. set lowest rate id as free
				 if($lowest_rate_id != null){
					$rates[$lowest_rate_id]->cost = $plan_delivery_fee;
					$rates[$lowest_rate_id]->label = $rates[$lowest_rate_id]->label;
					$rates[$lowest_rate_id]->taxes = array();
				  }

			}else{
				//free delivery
				//2. set lowest rate id as free
				if($lowest_rate_id != null){
					$rates[$lowest_rate_id]->cost = 0;
					$rates[$lowest_rate_id]->label = $rates[$lowest_rate_id]->label . ' (Free)';
					$rates[$lowest_rate_id]->taxes = array();
				}
			}
		}
		return $rates;
	}

	function display_checkout_fields( $checkout ) {
		
		if(WC()->cart->needs_shipping()){

			$current_shipping_method_string = WC()->session->get('chosen_shipping_methods');
			$selected_shipping = 'flat_rate';
			if (!empty($current_shipping_method_string)) {
				if (is_array($current_shipping_method_string)) {
					$current_shipping_method_string = $current_shipping_method_string[0];
				}
				if (strpos($current_shipping_method_string, 'pickup') !== false) {
					$selected_shipping = 'local_pickup';
				}else if (strpos($current_shipping_method_string, 'flat_rate') !== false) {
					$selected_shipping = 'flat_rate';
				}else{
					//do not show checkout fields
					return;
				}

			}
			
			if($selected_shipping != "local_pickup"){
				?>
					<tr class="cart-timeslots">
						<td colspan="2">
						<?php 
							include_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/bitss-squiggles-customizations-public-checkout-fields.php';
						?>
						</td>
					</tr>
				<?php
			}
		}
	}

	function kaarot_get_schedule_date(){
		$pincode = $this->get_user_pincode();
		$sq_user_zone = $this->get_user_shipping_zone($pincode); 

		// echo $pincode ;
		// echo " ";
		// echo $sq_user_zone;
		// exit;
		
		$slots = $this->get_slot_by_pincode($pincode, $sq_user_zone, null);
		$dates =array_unique(array_column($slots, 'date'));
		$schedule_dates = array();
		$outlet_timezone=new DateTimeZone( get_option('timezone_string'));
		foreach($dates as $date){
			$checkday = new DateTime($date,$outlet_timezone);
			$startday = (new DateTime("+1 day",$outlet_timezone))->settime(0,0);
			$endday = (new DateTime("+5 day",$outlet_timezone))->settime(0,0); //next 4 days
			if($checkday >= $startday && $checkday <= $endday ){
				$schedule_dates[$date] = $date;
			}
			
		}
		uasort($schedule_dates,function($a,$b)
		{
			// Subtracting the UNIX timestamps from each other.
			// Returns a negative number if $b is a date before $a,
			// otherwise positive.
			return strtotime($a)-strtotime($b);
		});
		$results=array(
			"schedule_dates" => $schedule_dates
		);
	
		echo json_encode($results);
	
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

	function get_user_shipping_zone($pincode){
		$sq_user_zone = get_user_meta( get_current_user_id(), 'sq_user_zone', true ); 
		
		if($sq_user_zone === false || empty($sq_user_zone)){
			$slots = $this->get_slot_by_pincode($pincode, null, null);
			if(sizeof($slots) > 0){
				$zones =array_unique(array_column($slots, 'zone'));
				$sq_user_zone = $zones[0];
			}else{
				$slots = $this->get_slot_by_pincode("*", null, null);
				if(sizeof($slots) > 0){
					$zones =array_unique(array_column($slots, 'zone'));
					$sq_user_zone = $zones[0];
				}
			}
			
		}
		return $sq_user_zone;
	}

	function get_user_pincode(){
		$pincode = get_user_meta( get_current_user_id(), 'billing_postcode', true ); 
		
		$slots = $this->get_slot_by_pincode($pincode, null, null);
		if(sizeof($slots) < 1){
			$pincode ="*";
		}
		return $pincode;
	}



	
	function kaarot_get_schedule_time(){

		$selected_date = $_POST["selected_date"];
		$pincode = $this->get_user_pincode();
		$sq_user_zone = $this->get_user_shipping_zone($pincode); 
		$schedule_time = array();
		$slots =$this->get_slot_by_pincode($pincode ,$sq_user_zone ,$selected_date);
		foreach($slots as $slot){
			$time = $slot["start"] . ' - ' . $slot["end"];
			$schedule_time[$time] = $time;
		}
		
		
	

		$results=array(
			"schedule_time" => $schedule_time
		);
	
		$next_schedule_time = "";// get_next_schedule_datetime($shipping_method_id);
		$results["next_schedule_time"] = $next_schedule_time;
		$results["enable_store_close_popup"] = (get_option('kaarot_printer_settings_slot_unavailable_warning_enable', "on") == 'on') ? true : false;
		echo json_encode($results);
	
		die();
	}

	function kaarot_validate_checkout_fields() {

	
		if(WC()->cart->needs_shipping()){
	
			global $woocommerce;
			$current_shipping_method_string = WC()->session->get('chosen_shipping_methods');
			$selected_shipping = 'flat_rate';
			if (!empty($current_shipping_method_string)) {
				if (is_array($current_shipping_method_string)) {
					$current_shipping_method_string = $current_shipping_method_string[0];
				}
				if (strpos($current_shipping_method_string, 'pickup') !== false) {
					$selected_shipping = 'local_pickup';
				}else if (strpos($current_shipping_method_string, 'flat_rate') !== false) {
					$selected_shipping = 'flat_rate';
				}else{
					//do not show checkout fields
					return;
				}

			}

			if($selected_shipping != "local_pickup"){
	
				// Check if set, if its not set add an error.
	
				if ( ( ! $_POST['later_dates'] || $_POST['later_dates'] == '' ) && (WC()->session->get('fdoe_shipping') != 'eathere')) {
					if ( function_exists( 'wc_add_notice' ) ) {
						wc_add_notice( 'Please select a date', 'error' );
					} else {
						$woocommerce->add_error('Please select a date.' );
					}
				}
	
	
				// Check if set, if its not set add an error.
	
				if ( ( ! $_POST['later_time'] || $_POST['later_time'] == '' ) && (WC()->session->get('fdoe_shipping') != 'eathere')) {
					if ( function_exists( 'wc_add_notice' ) ) {
						wc_add_notice('Please select a time.', 'error' );
					} else {
						$woocommerce->add_error( 'Please select a time.');
					}
				}
	
			}
		}
	
	}

	function kaarot_checkout_update_order_meta( $order_id ) {

		if(isset($_POST["later_dates"]) && isset($_POST["later_time"])){
			$later_dates = $_POST["later_dates"];
			$later_time = $_POST["later_time"];
			
			update_post_meta( $order_id, 'jckwds_date' , esc_attr( htmlspecialchars( $later_dates) ) );
			update_post_meta( $order_id, 'jckwds_timeslot' , esc_attr( htmlspecialchars($later_time) ) );
		}
		$active_subscription = $this->book_shelf_obj->get_active_subscription();
		if($active_subscription){
			update_post_meta( $order_id, 'active_subscription_id' , $active_subscription->get_id() );
		}
	}

	function custom_shop_order_column($columns)
	{
		$reordered_columns = array();

		// Inserting columns to a specific location
		foreach( $columns as $key => $column){
			$reordered_columns[$key] = $column;
			if( $key ==  'order_status' ){
				$reordered_columns['slot_details'] = 'Slot Details';
			}
		}
		return $reordered_columns;
	}

	function custom_orders_list_column_content( $column, $post_id )
	{
		switch ( $column )
		{
			case 'slot_details' :
				// Get custom post meta data
				$order = wc_get_order( $post_id );
				$shipping_method_name = $order->get_shipping_method();
				echo $shipping_method_name . "<br>"; 
			
				$date = get_post_meta( $post_id, 'jckwds_date', true );
				$time = get_post_meta( $post_id, 'jckwds_timeslot', true );
				echo  ($date==="asap"?"ASAP":$date) . "<br>" . ($time==="asap"?"ASAP":$time);

				break;

		}
	}

	public function woocommerce_get_formatted_order_total($formatted_total, $order){
		// $order_total = 0;
		// $items = $order->get_items();
		
		// foreach($items as $item => $values) {
		// 	//return  json_encode($values);
		// 	$product_id = $values->get_product_id();
		// 	if ( !has_term( 'book', 'product_cat', $product_id ) ) {
		// 		//$line_subtotal      = $values['line_subtotal']; 
		// 		// $line_subtotal_tax  = $cart_item['line_subtotal_tax'];
		// 		// // gets the cart item total
		// 		$line_total         = $values->get_subtotal();
		// 		// $line_tax           = $cart_item['line_tax'];
		// 		$order_total = $order_total + $line_total ;
		// 	}
		// }
		// $order_total =get_woocommerce_currency_symbol(). $order_total;
		return $formatted_total; 
	}

	public function woocommerce_order_formatted_line_subtotal( $subtotal, $item, $order ){
		 $product_id = $item->get_product_id();
		 if ( has_term( 'book', 'product_cat', $product_id ) ) {
			//$subtotal = str_replace(get_woocommerce_currency_symbol(), "", $subtotal);
		 	$subtotal = $item->get_meta("credits"). ' Credits';
		 }
		return $subtotal;
	}

	public function woocommerce_checkout_create_order_line_item( $item, $cart_item_key, $values, $order){
		foreach( $item as $cart_item_key=>$values ) {
			if( isset( $values['issue_or_return'] ) ) {
				$item->add_meta_data( 'issue_or_return', $values['issue_or_return'], true );
			}
			 if( isset( $values['return_copy_post_id'] ) ) {
				$item->add_meta_data( 'return_copy_post_id', $values['return_copy_post_id'], true );
			}
			if( isset( $values['booked_copy_post_id'] ) ) {
				$item->add_meta_data( 'booked_copy_post_id', $values['booked_copy_post_id'], true );
			}
			  if( isset( $values['credits'] ) ) {
				$item->add_meta_data( 'credits', $values['credits'], true );
			}
		}

		$product_id = $item->get_product_id();
		if ( has_term( 'book', 'product_cat', $product_id ) ) {
			
			$item->set_subtotal(0);
			$item->set_subtotal_tax(0);

			$item->set_total(0);
			$item->set_total_tax(0);
			
		}
		return $item;
	}

	public function woocommerce_checkout_fields($fields){
		//	echo json_encode($fields );exit;
		//	
		//$fields=array();
		return $fields;
	}

	function woocommerce_cart_item_name( $name, $cart_item, $cart_item_key ) {
		
		if( isset( $cart_item['issue_or_return'] ) ) {
			if($cart_item['issue_or_return'] =="issue"){
				$name .= "<br>". "Issuing" ;
			}else if($cart_item['issue_or_return'] =="return"){
				$name .="<br>". "Returning";
			}
		}
		if( isset( $cart_item['return_copy_post_id'] ) ) {
			$copy_post_id = $cart_item['return_copy_post_id'];
			$copy_smart_id = get_post_meta($copy_post_id,'wpcf-'.'copy-id',true);
			if(empty($copy_smart_id)){
				$copy_smart_id = 'NA';
			}
			$name .= "<br>". "Copy# " . $copy_smart_id;// . $copy_post_id;
		}
		if( isset( $cart_item['booked_copy_post_id'] ) ) {
			$copy_post_id = $cart_item['booked_copy_post_id'];
			$copy_smart_id = get_post_meta($copy_post_id,'wpcf-'.'copy-id',true);
			if(empty($copy_smart_id)){
				$copy_smart_id = 'NA';
			}
			$name .= "<br>". "Copy# " . $copy_smart_id;// . $copy_post_id;
		}
		 //  if( isset( $cart_item['credits'] ) ) {
		// 	$name .= "<div>". $cart_item['credits'] . ' credits'."</div>";
		//    }
		   
		return $name;
	}

	function woocommerce_is_sold_individually( $return, $product ) 
	{
		return( true );
	}

	public function woocommerce_get_discounted_price($price, $values, $cart ){

		$product_id = $values["product_id"];
		if ( has_term( 'book', 'product_cat', $product_id ) ) {
			$price =  0;
		}
		return $price;
	}

	function woocommerce_thankyou($order_id){
		echo '<a class="button" href="/">Continue browsing books</a>';
	}

	function woocommerce_proceed_to_checkout(){
		$credits_totals = $this->get_cart_totals(null);
		$available_credits =  $credits_totals["available_credits"];
		if($available_credits<0){
			echo "<div class='bt_checkout_credits_warning'>You have exceeded the allowed credits for issue. Either return more books or remove books for issue from cart and try again</div>";
		}
	}

	function bt_user_issue_history(){
		$books_arr = $this->book_shelf_obj->get_user_books_issue_history();

		
		$final_html = "";
		$row_tempalte = do_shortcode('[elementor-template id="12654"]');//dev
		if(empty($row_tempalte)){
			$row_tempalte = do_shortcode('[elementor-template id="18900"]');//prod
		}

		foreach($books_arr as $b){
			$product = wc_get_product( $b['product_id'] );
			$product_id = $product->get_permalink();
			$image = wp_get_attachment_image_src( get_post_thumbnail_id( $b['product_id'] ));
			$user_id = get_current_user_id();
			$recent_comments = get_comments( array(
			'post_id' => $b['product_id'],   // Use post_id, not post_ID
			"comment_content" => true,
				//         	'count'   => true, // Return only the count
			'user_id' => $user_id
			) );
			$link = $product_id;
			$review = "Please review";
			if (sizeof($recent_comments) != 0) {
				$comment_id = $recent_comments[0]->comment_ID;
				$review_comment = $recent_comments[0]->comment_content;
				$review_comment = strlen($review_comment) > 25 ? substr($review_comment,0,25)."..." : $review_comment;
				$review_rating = get_comment_meta( $recent_comments[0]->comment_ID, 'rating', true );
				$review = $review_rating . " out of 5 stars. <br> &#34<i>" . $review_comment . "</i>&#34;";
			}
			
			$image_url = $image[0];
			$product_image = str_replace('\/', '/', $image_url);
			$product_name = $product->get_name();
			$product_issued = date_format($b['order_date'],"d M, Y");			
			$product_review = $review;
			$product_review_link = $link;
			$order_id = $b['order_id'];

			$book_row = str_replace('#product_image#', $product_image, $row_tempalte );
			$book_row = str_replace('#product_name#', $product_name, $book_row );
			$book_row = str_replace('#product_issued#', $product_issued, $book_row );
			$book_row = str_replace('#product_review#', $product_review, $book_row );
			$book_row = str_replace('#product_review_link#', $product_review_link, $book_row );
			$book_row = str_replace('#order_id#', $order_id, $book_row );
			$book_row = str_replace('#product_id#', $product_id, $book_row );

			$final_html .= $book_row;
		}		

		if(empty($final_html)){
			$final_html="Nothing here as of now. The books you issue will appear here.";
		}
		return $final_html;
	}

	function bt_add_reading_history_endpoint(){
		add_rewrite_endpoint( 'reading-history', EP_ROOT | EP_PAGES );
	}
	function bt_reading_history_query_vars($vars){
		$vars[] = 'reading-history';
		return $vars;
	}
	function woocommerce_account_menu_items($items){
		$items['reading-history'] = 'Reading History';
    	return $items;
	}
	function woocommerce_account_reading_history_endpoint(){
		echo do_shortcode( '[bt_user_issue_history]' );
	}

	public function bt_library_poc()
	{
		//1. validate copy status. should be: available or issued_delivered

		if( isset( $_POST["bt_squggiles_copy_id"] ) ) {

			$copy_id =  $_POST["bt_squggiles_copy_id"];

			$copy = $this->book_shelf_obj->find_copy_post_id_by_copy_id($copy_id);
		//echo json_encode($copy);exit;

			if ($copy) {
				$copy_status = get_post_meta( $copy["copy_post_id"], 'wpcf-'.'copy-status',true);
				$copy_on_hold = get_post_meta( $copy["copy_post_id"], 'wpcf-'.'on-hold',true);
				//echo json_encode($copy_status);exit;
				if(($copy_status!="available" && $copy_status!="issued_delivered") && $copy_status!=""){
					wc_add_notice( 'This copy is not available for issue/return.', 'error' );
				}
				else if(strcasecmp($copy_on_hold,"no")!=0 && $copy_on_hold!=""){
					//	echo json_encode($copy_on_hold);exit;
					wc_add_notice( 'This copy is on hold.', 'error' );
				}
				else{
					
					$product_id = $copy["product_id"];
					// echo $product_id; exit;
					foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
						$tp = $cart_item['data'];
						if( ! empty($tp) ){
							$cart_product_id = method_exists( $tp, 'get_id' ) ? $tp->get_id() : $tp->id;
							if($cart_product_id==$product_id){
								WC()->cart->remove_cart_item($cart_item_key);
							}
						}
					}
					if($copy_status=="available"||$copy_status==""){
						$cart_item_key = WC()->cart->add_to_cart( $product_id,1,null,null,array('booked_copy_post_id'=> $copy["copy_post_id"]) );
					}else{
						$cart_item_key = WC()->cart->add_to_cart( $product_id);
					}
					if($cart_item_key){

					}
					wp_safe_redirect('/cart');
					die();
				}
				
			}else{
				wc_add_notice( 'Invalid Copy ID, please try again.', 'error' );
			}
		}
		include_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/bitss-squiggles-setting-admin-pos.php';		
		
	}

	public function woocommerce_cart_loaded_from_session() {

		//if the cart is empty do nothing
		if (WC()->cart->get_cart_contents_count() == 0) {
			return;
		}
	
		//array to collect cart items
		$cart_sort = [];
	
		//add cart item inside the array
		foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
			$cart_sort[$cart_item_key] = WC()->cart->cart_contents[$cart_item_key];
		}
	
		//replace the cart contents with in the reverse order
		WC()->cart->cart_contents = array_reverse($cart_sort);
	}

	function yith_wcwl_after_wishlist_form($wishlist){
	
		echo '
		<script type="text/javascript">
			// Ready state
			(function($){ 

				$( document.body ).on( "added_to_cart", function(){
					location.reload();
				});

			})(jQuery); // "jQuery" Working with WP (added the $ alias as argument)
		</script>
		
		';
	}

	function intercept_wc_get_template($template, $template_name, $args, $template_path, $default_path){
		$plugin_path  = untrailingslashit( plugin_dir_path( __FILE__ ) )  . '/template/woocommerce/';

		if ($template_name == 'myaccount/form-login.php') {
			$template = $plugin_path . 'my-account-login.php';
				
		}
		return $template;
	}

	// function api_call_search_for_user()
	// {
	// 	$nonce = $_GET["nonce"];
	// 	// $otp_login_redirect = isset($_GET["otp_login_redirect"])?$_GET["otp_login_redirect"]:"";

	// 	if (!wp_verify_nonce($nonce, 'api_call_search_for_user')) {
	// 		exit;
	// 	}
	// 	$users = [];
	// 	$response = array(
	// 		"status" => false,
	// 		"data" => null,
	// 		// "message" => $this->settings->get_em_user_not_exist() 
	// 		"message" => "User not found!"
	// 	);

	// 	$value = $_GET["input"];

	// 	if(empty($value)){
	// 		$response = array(
	// 			"status" => false,
	// 			"data" => null,
	// 			"message" =>  "please enter the required details"
	// 		);
	// 		wp_send_json($response);
	// 		die();
	// 	}

	// 	$user = get_user_by('login', $value);
	// 	if ($user == false || $user == null) {
	// 		$user = get_user_by('email', $value);
	// 	}
	// 	if ($user == false || $user == null) {
	// 		$args = array (
	// 			'meta_query' => array(
	// 				'relation' => 'or',
	// 				array(
	// 					'key'     => 'billing_phone',
	// 					'value'   => $value,
	// 					'compare' => 'like'
	// 			   )	
	// 			)
	// 		);
	// 		// Create the WP_User_Query object
	// 		$wp_user_query = new WP_User_Query( $args );

	// 		$users = $wp_user_query->get_results();

	// 	}
	// 	if($user){
	// 		$users[] = $user;
	// 	}

	// 	$resp_users = [];
	// 	foreach($users as $u){
	// 		if ( method_exists( 'user_switching', 'maybe_switch_url' ) ) {
	// 			$url = user_switching::maybe_switch_url( $u );
	// 		}

	// 		$phone = get_user_meta($u->ID, 'billing_phone', true);

	// 		$resp_users[] = array(
	// 			"user_id" => $u->ID,
	// 			"switch_url" => $url,
	// 			"user_name" => $u->user_login,
	// 			"user_email" =>  $u->user_email,
	// 			"user_phone" => $phone
	// 		);
	// 	}

	// 	$response = array(
	// 		"status" => true,
	// 		"data" => $resp_users,
	// 		"message" =>  "User found..."
	// 	);		

	// 	wp_send_json($response);
	// 	die();

	// }

}
