<!-- <div>
    <input type="text" id="bt_sq_user_detail_input"  placeholder="Enter the user details">
    <button type="button" id="bt_sq_search_user">Search</button>

    <table class="bt_sq_user_searched_list">
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Phone No.</th>
            <th>Button</th>
        </tr>
    </table>
</div> -->

<form action='' method='post'>
	<input type='text' name='bt_squggiles_copy_id' placeholder='Scan barcode or enter copy id'>
	<button type='submit'>Add to Cart</button> <a class="" style="float: right;vertical-align: middle;line-height: 50px;" href="?empty-cart=1">Clear Cart</a>
</form>

<script>
(function( $ ) {    
    'use strict';
    jQuery( document ).ready(function($) {
        var delay = 500; 
        setTimeout(function() {
            //open cart section
            jQuery(jQuery('.elementor-tab-title')[1]).trigger('click');

            setTimeout(function() {
                //focus copy id textbox
                jQuery('input[name=bt_squggiles_copy_id]').focus();
            }, 500);

        }, delay);
    
       
        function refresh_fragments() {
            console.log('fragments refreshed!');
            jQuery( "[name='update_cart']" ).removeAttr( 'disabled' );
            jQuery( "[name='update_cart']" ).trigger( 'click' );
        }
      //  setInterval(refresh_fragments, 5000);
    });
})(jQuery);
</script>