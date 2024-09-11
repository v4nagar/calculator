(function( $ ) {
	'use strict';

	$( document ).on( 'click', '#resend_otp', function(event){

        event.preventDefault();
		$(this).hide();
		$('.lds-facebook').show();
        // Use ajax to do something...
        var postData = {
            action: 'otp_resend',
            mobno: $('#form-field-field_e2f9acb').val(),
        }

        //Ajax load more posts
        $.ajax({
            type: "POST",
            data: postData,
            dataType:"json",
            url: bitss_squiggles_vars.ajax_url,
            //This fires when the ajax 'comes back' and it is valid json
            success: function (response) {
				$('.lds-facebook').hide();
				$('#resend_otp').show();
                alert( response.msg );

            }
            //This fires when the ajax 'comes back' and it isn't valid json
        }).fail(function (data) {
            console.log(data);
        }); 

    });

    $(document).ready(function(){
        $('#order_review_heading').text('Review Cart');
        $('body').on('updated_wc_div',function() {
            //location.reload();
        });
        // $('body').on('click', '#bt_sq_search_user', function(e) {
		// 	e.preventDefault();  
        //     var value = $("#bt_sq_user_detail_input").val();
        //     // alert(value);
		// 	search_user(value);
		// });
    });

   
    // function search_user(value) {
		
	// 	var nonce = bitss_squiggles_vars.search_user_nonce;		
	// 	$.get(
	// 		bitss_squiggles_vars.ajax_url,
	// 		{ action: 'api_call_search_for_user', input: value, nonce: nonce },
	// 		function (res) {
    //             // alert(res.data);
    //             var array = res.data;
                
    //             array.forEach(el => {
    //                 $('.bt_sq_user_searched_list').append('<tr><td>'+ el.user_name +'</td><td>'+ el.user_email +'</td><td> '+ el.user_phone +'</td><td><a class="bt_sq_switch_to_this_user" href="'+ el.switch_url +'">Switch to this</a></td></tr>');
    //             });
	// 		}
	// 	).fail( function(err) {
    //         alert("error occured!");
	// 	});
	// }


})( jQuery );
