<?php 
  $message = "
  <p>Dear Member,</p>
  
  <p>We noticed some damages in the book you recently returned. To help maintain our collection for all members, a nominal charge will be applied, which will reflect in your next order (damage details below). We appreciate your understanding and continued support. Please feel free to reach out if you have any questions or concerns. Details of the damages are listed below. The details can also be viewed in the member 
  <a href='https://squigglesdev.bitss.in/my-account/view-subscription/" . esc_html($active_subscription_id) . "/'>subscription page</a>.</p>
  
  <p><strong>[Order #<a href='https://squigglesdev.bitss.in/my-account/view-order/" . esc_html($order_id) . "/'>" . esc_html($order_id) . "</a>] (" . date('Y-m-d') . ")</strong></p>
  
  <table border='1' cellpadding='10' cellspacing='0'>
      <thead>
          <tr>
              <th>Product</th>
              <th>Damage Type</th>
              <th>Damage Amount</th>
          </tr>
      </thead>
      <tbody>";

foreach ($sub_damages as $damage) {
  $subtotal += $damage['amt'];
  $product = wc_get_product($damage['added_order_id']);
  $credits = $product->get_price();
  $message .= "
      <tr>
          <td>
              <div>" . esc_html($damage['copys']) . "</div>
              <div><strong>Copy#:</strong> " . esc_html($damage['sq_copy_id']) . "</div>
              <div>Credits: " . esc_html($credits) . "</div>
          </td>
          <td>
              <div>" . esc_html($damage['type']) . "</div>
              <div>" . esc_html($damage['remark']) . "</div>
          </td>
          <td>
              <div>" . wc_price($damage['amt']) . "</div>
          </td>
      </tr>";
}

$message .= "
      </tbody>
      <tfoot>
          <tr>
              <td colspan='2'><strong>Subtotal:</strong></td>
              <td><strong>" . wc_price($subtotal) . "</strong></td>
          </tr>
      </tfoot>
  </table>
                  
  <p>For any questions or concerns please email us at <a href='mailto:info@worldofsquiggles.com'>info@worldofsquiggles.com</a> or call us on +91 9730799418.</p>
  
  <p>Thank you,<br>Squiggles</p>
";

?>
