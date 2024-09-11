<?php
global $post;
$product_id = $post->ID;
function get_orders_ids_by_product_id($product_id)
{
    global $wpdb;

    // Define HERE the orders status to include in  <==  <==  <==  <==  <==  <==  <==
    $orders_statuses = "'wc-completed', 'wc-processing', 'wc-on-hold'";

    # Get All defined statuses Orders IDs for a defined product ID (or variation ID)
    return $wpdb->get_col(
        "
            SELECT DISTINCT woi.order_id
            FROM {$wpdb->prefix}woocommerce_order_itemmeta as woim, 
                 {$wpdb->prefix}woocommerce_order_items as woi, 
                 {$wpdb->prefix}posts as p
            WHERE  woi.order_item_id = woim.order_item_id
            AND woi.order_id = p.ID
            AND p.post_status IN ( $orders_statuses )
            AND woim.meta_key IN ( '_product_id', '_variation_id' )
            AND woim.meta_value LIKE '$product_id'
            ORDER BY woi.order_item_id DESC"
    );
}
$orders_ids_array = get_orders_ids_by_product_id($product_id);
// $orders_ids_array = json_encode($orders_ids_array);
// var_dump($orders_ids_array);
// exit;

?>

<div class="sq_sd_table table-container">
    <table class="table is-bordered" id="product_order_details_table">
        <thead>
            <tr>
                <th>S.No.</th>
                <th>Date</th>
                <th>Order Id</th>
                <th>Issue/Return</th>
                <th>Copy Id</th>
                <th>Customer</th>
                <th>Pickup/Delivery</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $index = 1;
            foreach ($orders_ids_array as $oder_id) {
                $order = wc_get_order($oder_id);
                $date = date('d M, Y', strtotime($order->get_date_created()));
                $items = $order->get_items();
                $issue_or_return = "-";
                $copy_id = "-";
                foreach ($items as $item) {
                    $item_product_id = $item->get_product_id();
                    if ($product_id == $item_product_id) {
                        $issue_or_return = $item->get_meta("issue_or_return");
                        if ($issue_or_return == 'issue') {
                            $copy_post_id = $item->get_meta("booked_copy_post_id");
                            $copy_id = get_post_meta($copy_post_id,'wpcf-'.'copy-id',true);
                        } else if ($issue_or_return == 'return') {
                            $copy_post_id = $item->get_meta("return_copy_post_id");
                            $copy_id = get_post_meta($copy_post_id,'wpcf-'.'copy-id',true);
                        }
                        break;
                    }
                }
                $user_fn = $order->get_billing_first_name();
                $user_ln = $order->get_billing_last_name();
                $user_id = 0;


                $user = $order->get_user();
                if ($user) {
                    $user_fn = $user->first_name;
                    $user_ln = $user->last_name;
                    $user_id = $user->ID;
                }

                $method = $order->get_shipping_method();
                echo "<tr>
                    <td>" . $index . ".</td>
                    <td>" . $date . "</td>
                    <td><a href='/wp-admin/post.php?post=$oder_id&action=edit'>" . $oder_id . "</a></td>
                    <td>" . $issue_or_return . "</td>
                    <td>" . $copy_id . "</td>
                    <td><a href='/wp-admin/user-edit.php?user_id=$user_id'>" . $user_fn . " " . $user_ln . "</a></td>
                    <td>" . $method . "</td>
                    </tr>";
                $index++;
            }
            ?>

        </tbody>
    </table>
</div>