<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://amitmittal.tech
 * @since      1.0.0
 *
 * @package    Bitss_Squiggles_Customizations
 * @subpackage Bitss_Squiggles_Customizations
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Bitss_Squiggles_Customizations
 * @subpackage Bitss_Squiggles_Customizations
 * @author     Amit Mittal <amitmittal@bitsstech.com>
 */
class Bitss_Squiggles_Customizations_Book_Shelf {

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

    private $books_issue_history = null;

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

	}


    public function Get_User_Book_Shelf($user_id=null){
        if(empty($user_id) && is_user_logged_in()){
            $user_id = get_current_user_id() ;
        }
        //1. Get list of all “processing” or “completed” orders of past 90 days.
        //2. Get orders having shipping status not equal to “rto delivered” or “rto in transit” or “canceled” from above list.
        $order_ids = $this->get_orders($user_id,365);
        $shelf_order_ids=array();
        foreach ($order_ids as $order_id) {
            if(!empty($order_id)){
                if(function_exists('bt_get_shipping_tracking')) {
                    $shipment_obj = bt_get_shipping_tracking($order_id);
                  
                    if($shipment_obj != null){
                        $courier_name = $shipment_obj["tracking_data"]["courier_name"];
                        $current_status = $shipment_obj["tracking_data"]["current_status"];
                        $awb = $shipment_obj["tracking_data"]["awb"];
                        $tracking_url = $shipment_obj["tracking_data"]["tracking_url"];
                      
                        //if( $current_status != "canceled" && $current_status != "rto-in-transit" && $current_status != "rto-delivered"){
                            $shelf_order_ids[] = $order_id;
                       // }
                    }else{
                        $shelf_order_ids[] = $order_id;
                    }            
                }
            }
        }
        $issued_books=array();
        $total_credits = 0;
        $issued_book_copies = array();
        //3. Get Book products with “Issue” status in all orders from step 2.
        foreach ($shelf_order_ids as $order_id) {
            $order = wc_get_order( $order_id);
            $items = $order->get_items();
           // print_r($items);exit;
           
            foreach ( $items as $item ) {
                
                $product_id = $item->get_product_id();
                if ( has_term( 'book', 'product_cat', $product_id ) ) {
                    $issue_or_return = $item->get_meta("issue_or_return");
                    if($issue_or_return=="issue"){
                        $booked_copy_id = $item->get_meta("booked_copy_post_id");
                        $last_booked_order_id = get_post_meta( $booked_copy_id, 'last_booked_order_id',true);
                        if($last_booked_order_id != $order_id) continue;
                        if(isset($issued_book_copies[$product_id.'_'.$booked_copy_id])) continue;
                        $copy_status = get_post_meta( $booked_copy_id, 'wpcf-'.'copy-status',true);
                        if($issue_or_return=="issue" && ($copy_status=="booked"||$copy_status=="issued_in_transit"||$copy_status=="issued_delivered"||$copy_status=="return_scheduled"||$copy_status=="return_in_transit")){
                            $credits=  $item->get_meta("credits");//$order->get_item_subtotal( $item );
                            $total_credits = $total_credits + $credits;
                            $issued_book_copies[$product_id.'_'.$booked_copy_id]=true;
                            $issued_books[] = array(
                                "product_id" => $product_id,
                                "copy_post_id" => $booked_copy_id,
                                "credits" => $credits
                            ); 
                        }
                    }
                }
            }
        }
       

        return array( 
            "books"=>$issued_books,
            "credits"=>$total_credits
        );
    }

    private function get_orders($user_id,$days=90){
        $order_statuses=array('wc-processing','wc-completed');
        $orders_date= $days;
        $fromTime = date("Y-m-d",strtotime("-$orders_date day"));
        $filters_orders = array(
            'post_status' =>  $order_statuses,
            'posts_per_page'   => -1,
            'post_type'   => 'shop_order',
            'fields' => 'ids',
             'date_query' => array(
                 'after' => $fromTime
             ),
            'orderby' => 'date',
            'order' => 'DESC',
            'meta_query'  => array(
              'relation' => 'AND',
               array(
                    'key'     => '_customer_user',
                    'value'   => $user_id,
                    'compare' => '=',
                ),
                array(
                    'key'     => '_bt_shipping_provider',
                    'value'   => '',
                    'compare' => '!=',
                ),
            ),
        );

        $orders = new WP_Query($filters_orders);        
        return $orders->posts;
    }

    public function update_book_product_stock_status( $product_id ){
        if ( !has_term( 'book', 'product_cat', $product_id ) ) {
			return;
		}
        $available_post_ids=$this->find_available_copies_of_book($product_id);
		$is_in_stock = sizeof($available_post_ids)>0;
		$stock_s = $is_in_stock?"instock":"outofstock";
	 	update_post_meta( $product_id, '_stock_status', wc_clean( $stock_s ) );
    }

    public function find_available_copies_of_book($product_id){
        $query = new WP_Query( 
			array(
				'post_type' => 'copy', //Child post type slug
				'numberposts' => -1,
				'toolset_relationships' => array(
					'role' => 'child',
					'related_to' => $product_id, // ID of starting post
					'relationship' =>'copy',
				),
				'meta_query' => array(
					'relation' => 'AND',
						array(
							'key' => 'wpcf-on-hold',
							'value' => 'No',
							'compare' => '='
						),
					),
			)
		);
		$copy_posts = $query->posts;
        $available_post_ids=[];
        foreach ($copy_posts as  $post) {
            $post_id = $post->ID;
            $available = get_post_meta($post_id,'wpcf-copy-status',true);
            if($available==false || $available == "available" || $available ==""){
                $available_post_ids[] = $post_id;
            }
        }
        // echo json_encode( $available_post_ids);
        // echo "done"; 

        // exit;
		
		return $available_post_ids;
	}

    public function find_copy_post_id_by_copy_id($copy_id){
        $product_id=false;
        $copy_post_id=false;

        if (strlen($copy_id) == 9) {
            $book_smart_id = mb_substr($copy_id, 0, -2);
            $filters_codes = array(
                'post_status' => 'publish',
                'post_type' => 'product',
                'fields' => 'ids',
                'meta_key' => 'wpcf-'.'smart_product_id',
                'meta_query' => array(
                    array(
                        'key' => 'wpcf-'.'smart_product_id',
                        'value' => $book_smart_id,
                        'compare' => '='
                    )
                )
            );		
            $products = new WP_Query($filters_codes);
            $arr = $products->posts;
            //echo json_encode($arr);  exit;
            if (sizeof($arr) > 0) {
                $product_id = $arr[0];
            } 

            if($product_id){
                //find copies of the product and try to find the said copy-id
                $query = new WP_Query( 
                    array(
                        'post_type' => 'copy', //Child post type slug
                        'numberposts' => -1,
                        'toolset_relationships' => array(
                            'role' => 'child',
                            'related_to' => $product_id, // ID of starting post
                            'relationship' =>'copy',
                        ),
                        'meta_query' => array(
                            'relation' => 'AND',
                                array(
                                    'key' => 'wpcf-on-hold',
                                    'value' => 'No',
                                    'compare' => '='
                                ),
                            ),
                    )
                );
                $copy_posts = $query->posts;
                foreach ($copy_posts as  $post) {
                    $post_id = $post->ID;
                    $current_copy_id = get_post_meta($post_id,'wpcf-copy-id',true);
                    if($current_copy_id==$copy_id){
                        $copy_post_id=$post_id;
                        break;
                    }
                }
            }

        }

        if($product_id && $copy_post_id){
            return array(
                "product_id"=>$product_id,
                "copy_post_id"=>$copy_post_id
            );
        }
		
		return false;
	}

    public function get_active_subscription($user_id=null){
        $active_subscription = false;
        if(empty($user_id)){
            $user_id = get_current_user_id();
        }

        if(function_exists('wcs_get_users_subscriptions')) {	
			$users_subscriptions = wcs_get_users_subscriptions($user_id);
			foreach ($users_subscriptions as $subscription){
			if ($subscription->has_status(array('active'))) {
					$active_subscription=$subscription;
					break;
			    }
			}
		}
        return  $active_subscription;
    }

    public function get_user_books_issue_history($user_id=null){
        if($this->books_issue_history!=null){
            //to improve perofrmance
            return $this->books_issue_history;
        }
        if(empty($user_id) && is_user_logged_in()){
            $user_id = get_current_user_id() ;
        }
        $order_ids = $this->get_orders($user_id,365);
        $books_issue_history = [];

        foreach ($order_ids as $order_id) {
            $order = wc_get_order( $order_id);
            $items = $order->get_items();
           // print_r($items);exit;
           
            foreach ( $items as $item ) {
                
                $product_id = $item->get_product_id();
                if ( has_term( 'book', 'product_cat', $product_id ) ) {
                    $issue_or_return = $item->get_meta("issue_or_return");
                    if($issue_or_return=="issue"){
                        $booked_copy_id = $item->get_meta("booked_copy_post_id");
                        $books_issue_history[] = array(
                            "product_id"    =>  $product_id, 
                            "copy_id"       =>  $booked_copy_id, 
                            "order_id"      =>  $order_id, 
                            "order_date"    =>  $order->get_date_created()
                        );
                    }
                }
            }
        }
        $this->books_issue_history = $books_issue_history;
        return $books_issue_history;
    }

    public function is_in_books_issue_history($product_id,$user_id=null){
        
        $reading_history = $this->get_user_books_issue_history($user_id);
        $is_in_history = false;
        foreach ( $reading_history as $h ) {
            if ( $product_id == $h['product_id'] ) {
                $is_in_history = true;
                break;
            }
        }
        return $is_in_history;
    }


}
