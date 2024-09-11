<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://amitmittal.tech
 * @since      1.0.0
 *
 * @package    Bitss_Squiggles_Customizations
 * @subpackage Bitss_Squiggles_Customizations/public/partials
 */
//echo json_encode($active_subscription);

$plan_credits = get_post_meta($active_subscription->get_id(),'wpcf-'.'subscription-credits',true);
$plan_free_delivery = get_post_meta($active_subscription->get_id(),'wpcf-'.'subscription-free-delivery-per-month',true);
$plan_delivery_fee = get_post_meta($active_subscription->get_id(),'wpcf-'.'subscription-delivery-fee',true);
$plan_plus_member = get_post_meta($active_subscription->get_id(),'wpcf-'.'subscription-squiggles-plus-membership',true);
$plan_deposit_amt = get_post_meta($active_subscription->get_id(),'wpcf-'.'subscription-deposit-amount',true);
$subscription_length = $active_subscription->get_time( 'end' );
// Iterating through subscription items

?>
<div class="user_plan_details">
    <h2>Curent Plan</h2>
    <table class="table" style="text-align:center">
        <tr>
            <td>
                <em><?php echo $plan_name ?></em><br>
                <small>Plan</small>
            </td>
            <td>
                <em><?php echo $plan_free_delivery ?></em><br>
                <small>Free Delivery Per Month</small>
            </td>
            <td>
                <em><?php echo $plan_credits ?></em><br>
                <small>Credits per Delivery</small>
            </td>
            <td>
                <em><?php echo $subscription_length ?></em><br>
                <small>Valid Till</small>
            </td>
        </tr>
    </table>
</div>