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
class Login_Form_Action extends \ElementorPro\Modules\Forms\Classes\Action_Base {

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
		return 'Bitss Login Form';
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
		return esc_html__( 'Bitss Login Form', 'elementor-forms-ping-action' );
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
		$user_email = $fields["username"];
		$password = $fields["password"];

        $user = wp_authenticate($user_email, $password);
        if(!is_wp_error($user) && $user !== false) {
           
            wp_set_current_user($user->ID, $user->user_login);
            wp_set_auth_cookie($user->ID);
            do_action('wp_login', $user->user_login, $user);
            $url = "/subscriberhome";
            $ajax_handler->add_response_data( 'redirect_url', $url );
          
        } else {
            $ajax_handler->add_error("username","Invalid login credentials.");
			return;
        }
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
