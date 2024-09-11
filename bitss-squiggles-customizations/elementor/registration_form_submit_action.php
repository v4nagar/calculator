<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor form ping action.
 *
 * Custom Elementor form action which will ping an external server.
 *
 * @since 1.0.0
 */
class Registration_Form_Action_After_Submit extends \ElementorPro\Modules\Forms\Classes\Action_Base {

	/**
	 * Get action name.
	 *
	 * Retrieve ping action name.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string
	 */
	public function get_name() {
		return 'User Registration';
	}

	/**
	 * Get action label.
	 *
	 * Retrieve ping action label.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string
	 */
	public function get_label() {
		return esc_html__( 'User Registration', 'elementor-forms-ping-action' );
	}

	/**
	 * Run action.
	 *
	 * Ping an external server after form submission.
	 *
	 * @since 1.0.0
	 * @access public
	 * @param \ElementorPro\Modules\Forms\Classes\Form_Record  $record
	 * @param \ElementorPro\Modules\Forms\Classes\Ajax_Handler $ajax_handler
	 */
	public function run( $record, $ajax_handler ) {

	//code for user registration...

		// Get submitted form data.
		$raw_fields = $record->get( 'fields' );

		// Normalize form data.
		$fields = [];
		foreach ( $raw_fields as $id => $field ) {
			$fields[ $id ] = $field['value'];
		}
		$step = $fields["register_step"];

		if($step=="step1"){
			$user_id = get_current_user_id();
			if($user_id!=0){
				$ajax_handler->add_error("field_f5d37f5","You are already logged-in, please logout and then try again.");
				return;
			}
			$user_email = $fields["email"];
			$password = $fields["6ed25c0"];
			$city = $fields["field_3840227"];
			

			if(strlen($password)<6){
				$ajax_handler->add_error("6ed25c0","Password should of at least 6 characters.");
				return;
			}

			$registration_code = $fields["field_f5d37f5"];
			$registration_code_post = null;
			if(!empty($registration_code)){
				//validate registration code
				$filters_codes = array(
					'post_status' => 'publish',
					'post_type' => 'registration-code',
					'name' =>   $registration_code,
					'meta_key' => 'wpcf-'.'used-by',
					'meta_query' => array(
						array(
							'key' => 'wpcf-'.'used-by',
							'value' => '',
							'compare' => '='
						)
					)
				);		
				$registration_codes = new WP_Query($filters_codes);
				if($registration_codes->have_posts()){
					$registration_codes->the_post();
					$registration_code_post = $registration_codes->post;

				}else{
					$ajax_handler->add_error("field_f5d37f5","Invalid Registration Code!");
					return;
				}
			}
			$user_name = $user_email;
			$user_id = username_exists( $user_name ); 

			if ( ! $user_id && false == email_exists( $user_email )) {
			
				$userdata = array(
					'user_login' =>  $user_name,
					'user_pass'  =>  $password,
					'user_nicename' => $user_email,
					'display_name' => $user_email,
					'nickname' => $user_email,
					'user_email' => $user_email,
					'first_name' => "",
					'last_name' => "",
					'role' => 'customer'
				);             
				$user_id = wp_insert_user( $userdata ) ;
				if($user_id){            
					$user = get_user_by( 'login', $user_id );      
					update_user_meta($user_id, "billing_city", $city);
					update_user_meta($user_id, "billing_country", 'IN');
					update_user_meta($user_id, "shipping_country", 'IN');

					if($registration_code_post!=null){
						update_post_meta($registration_code_post->ID, 'wpcf-'.'used-by', $user_name  );
						update_post_meta($registration_code_post->ID, 'wpcf-'.'used-on', time() );
					}
					wp_set_current_user( $user_id, $user_name );
					wp_set_auth_cookie( $user_id );
					do_action( 'wp_login', $user_name,	$user );
					$url = "/user-registration-step-2/";
					$ajax_handler->add_response_data( 'redirect_url', $url );
					
					// if ( wp_safe_redirect( $url ) ) {
					// 	exit;
					// }
				}
			}else{
				$ajax_handler->add_error("email","User already exists.");
			}
		}else if($step=="step2"){
			$user_id = get_current_user_id();
			if($user_id==0){
				//not logged in, return error
				$ajax_handler->add_error("pincode","Something is wrong, please go back and try again.");
				$ajax_handler->add_response_data( 'redirect_url', "/" );
				return;
			}
			$first_name = $fields["first_name"];
			$last_name = $fields["last_name"];
			$phone = $fields["field_2c1fa37"];
			$address_1 = $fields["field_a9402bb"];
			$address_2 = $fields["field_a687753"];
			$pincode = $fields["field_4c751d4"];

			$phone = $this->clean_phone_number($phone);

			
            
            update_user_meta($user_id, "first_name", $first_name);
            update_user_meta($user_id, "last_name", $last_name);


			update_user_meta($user_id, "billing_first_name", $first_name);
            update_user_meta($user_id, "billing_last_name", $last_name);
			update_user_meta($user_id, "billing_address_1", $address_1);
            update_user_meta($user_id, "billing_address_2", $address_2);
            update_user_meta($user_id, "billing_state", "MH");
            update_user_meta($user_id, "billing_postcode", $pincode);
			update_user_meta($user_id, "billing_phone", $phone);
            update_user_meta($user_id, "billing_city", "Pune");
            update_user_meta($user_id, "billing_country", 'IN');

			update_user_meta($user_id, "shipping_first_name", $first_name);
            update_user_meta($user_id, "shipping_last_name", $last_name);
			update_user_meta($user_id, "shipping_address_1", $address_1);
            update_user_meta($user_id, "shipping_address_2", $address_2);
            update_user_meta($user_id, "shipping_state", "MH");
            update_user_meta($user_id, "shipping_postcode", $pincode);
			//update_user_meta($user_id, "shipping_phone", $phone);
            update_user_meta($user_id, "shipping_city", "Pune");
            update_user_meta($user_id, "shipping_country", 'IN');
            
			if(class_exists('Otpfy_For_Wordpress')){
				Otpfy_For_Wordpress::send_otp($phone);

				$url = "/verify-otp/?phone_number=" . $phone;
				$ajax_handler->add_response_data( 'redirect_url', $url );
			}else{
				$ajax_handler->add_error("otp","Something is wrong, contact admin.");
				return;
			}
			

		}else if($step=="verify_otp"){
			$user_id = get_current_user_id();
			$phone = $fields["field_e2f9acb"];
			$phone = $this->clean_phone_number($phone);
			$user_otp = $fields["otp"];
		
			// if ( false === ( $server_otp = get_transient( 'otp_' . $phone ) ) ){
			// 	$ajax_handler->add_error("otp","Something is wrong, please go back and try again.");
			// 	return;
			// }
	
			// if($server_otp != $user_otp){
			// 	$ajax_handler->add_error("otp","Invalid OTP, please try again.");
			// 	return;
			// }

			if(class_exists('Otpfy_For_Wordpress')){

				$res= Otpfy_For_Wordpress::verify_otp($phone,$user_otp );

				if($res){
					update_user_meta($user_id, "bt_is_otp_verified_". $phone, true);
					$url = "/subscriberhome/";
					$ajax_handler->add_response_data( 'redirect_url', $url );
					return;
				}else{
					$ajax_handler->add_error("otp","Invalid OTP, please try again.");
					return;
				}

			}

			$ajax_handler->add_error("otp","Something is wrong, contact admin.");
		
		
		}
	}

	function clean_phone_number($phone_no){
		return substr( preg_replace('/^\+?1|\|1|\D/', '', $phone_no), -10);
	}

	/**
	 * Register action controls.
	 *
	 * Ping action has no input fields to the form widget.
	 *
	 * @since 1.0.0
	 * @access public
	 * @param \Elementor\Widget_Base $widget
	 */
	public function register_settings_section( $widget ) {}

	/**
	 * On export.
	 *
	 * Ping action has no fields to clear when exporting.
	 *
	 * @since 1.0.0
	 * @access public
	 * @param array $element
	 */
	public function on_export( $element ) {}

}
