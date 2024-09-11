<!-- Fixed issue of z-index on checkout page -->
<style>
    form.checkout.woocommerce-checkout {position:relative !important; z-index: 10;}
    main#main {z-index: 101;}
    input#billing_table {cursor: not-allowed;color: #999;background-color: #eaeded;}
</style>

<h5>
    <?php
    if($selected_shipping==="eathere"){
       // echo "<h2>Contactless Ordering</h2>";
    }elseif($selected_shipping==="local_pickup"){
       // echo "<h2>Pickup Details</h2>";
        //echo "When do you want to pickup?";
    } else{
      //  echo "<h2>Delivery Slot</h2>";
        //echo "When do we send it to you?";
    }
    ?>

</h5>

<div class="slot-unavailable">
    <div class="message"></div>
    <div class="next-availability"></div>
</div>

<p class="form-row form-row-wide validate-required">
    <span class="woocommerce-input-wrapper">
        <label for="later_dates" class=""><?php echo $selected_shipping==="local_pickup"?"Pickup":"Delivery"; ?> Date <abbr class="required" title="required">*</abbr></label>
        <select id ="later_dates" name="later_dates">
            <option value = "">Select Date </option>
        </select>
    </span>
</p>


<p class="form-row form-row-wide validate-required">
    <span id ="span_later_time" class="woocommerce-input-wrapper">
        <label for="later_time" class="">Time <abbr class="required" title="required">*</abbr></label>
        <select id ="later_time" name="later_time">
            <option value = "">Select Time </option>
        </select>
    </span>
</p>


<script>
    var FirstTimeChange = true;
    <?php
		$_ajax = admin_url('admin-ajax.php');
	?>
    jQuery(document).ready(function(){

        jQuery('#later_dates').html("<option selected>Please wait...</option>");
        jQuery('#later_dates').attr('disabled','disabled');
		jQuery('#later_time').html("<option selected>Select a date...</option>");
		jQuery('#later_time').attr('disabled','disabled');

        var data = {action: 'kaarot_get_schedule_date'};

        jQuery.post('<?php echo($_ajax); ?>', data, function(response) {
			jQuery('#later_dates').html("");
			if(response!=null){

				response= JSON.parse(response);
				//console.log(response.schedule_dates);

				//jQuery('#later_dates').append("<option value =''>Select a date</option>");
				jQuery.each( response.schedule_dates, function( key, value ) {
                    var option = "<option value ='"+key+"'>"+value+"</option>"

                    jQuery('#later_dates').append(option);
                    jQuery('#later_dates').removeAttr('disabled');
                });
                jQuery("#later_dates").trigger("change");
			}

		});

    });
    jQuery('#later_dates').change(function(){
		jQuery('#later_time').html("<option selected>Please wait...</option>");
        jQuery('#later_time').attr('disabled','disabled');
        var selected_date = jQuery('#later_dates').val();

		var data = {
			action: 'kaarot_get_schedule_time',
			selected_date: selected_date
		};

		jQuery.post('<?php echo($_ajax); ?>', data, function(response) {

			jQuery('#later_time').html("");
			if(response!=null)
			{
                response= JSON.parse(response);
                if(response.schedule_time.length<1){
                    var option = "<option value =''>No Slots Available</option>";
                    jQuery('#later_time').append(option);
                  
                }else{
                    jQuery.each( response.schedule_time, function( key, value ) {
                        var option = "<option value ='"+key+"'>"+value+"</option>";
                        jQuery('#later_time').append(option);
                        jQuery('#later_time').removeAttr('disabled');
                    });
                }
                FirstTimeChange = false;
			}
		});

	});

    function openModal(modalId) {
        var modal = document.getElementById(modalId);
        var span = document.getElementsByClassName("closeModal")[0];
        modal.style.display = "block";
        span.onclick = function() {
            modal.style.display = "none";
        };

        // When the user clicks anywhere outside of the modal, close it
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    }
</script>

<style>
    .ksetup-hide {
        display: none;
    }
    #messageContent {
        position: relative;
    }
    #messageContent .message-check {
        position: absolute;
        top: -30px;
        left: 50%;
        transform: translate(-50%, -50%);
    }
    #messageContent .message-check img {
        max-width:60px;
        box-shadow: 1px 2px 10px #666;
        border-radius: 50%!important;
    }
    #kstore-name {
        border-radius: 50%;
        font-weight: 500;
        width: 70px;
        display: inline-block;
        height: 70px;
        line-height: 70px;
        text-align: center;
        font-size: 14px;
        overflow: hidden;
        padding: 0 7px;
        box-shadow: 1px 2px 8px #666;
        color: #fff;
        background-color: #DF6423 !important;
    }
    .message-title {
        padding-top: 20px !important;
        margin: 20px 0 !important;
        font-size: 18px;
        font-weight: 300;
        line-height: 1.5;
        color: #444;
    }
    .kmodal-btn {
        display: block;
        width: 100%;
        padding: .25rem .5rem;
        font-size: 14px;
        line-height: 1.5;
        border-radius: .2rem;
        border: 1px solid transparent;
        text-align: center;
        vertical-align: middle;
        font-weight: 400;
        transition: color .15s ease-in-out,background-color .15s ease-in-out,border-color .15s ease-in-out,box-shadow .15s ease-in-out;
    }
    .kmodal-btn-success {
        color: #fff;
        background-color: #DF6423;
        border-color: #DF6423;
    }
    .kmodal-btn-success:hover {
        color: #fff;
        background-color: #e65d14;
        border-color: #e65d14;
    }
    .modal {transition: opacity .15s linear;display: none;position: fixed;z-index: 100;padding-top: 100px;left: 0;top: 0;width: 100%;height: 100%;overflow: auto;background-color: rgb(0,0,0);background-color: rgba(0,0,0,0.5);}
    .modal-content {position: relative;background-color: #fefefe;margin: auto;width: 80%;padding: 0;border: 1px solid #888;    box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2), 0 6px 20px 0 rgba(0,0,0,0.19);
        -webkit-animation-name: animatetop;
        -webkit-animation-duration: 0.4s;
        animation-name: animatetop;
        animation-duration: 0.4s;
        background-color: #fff;
        background-clip: padding-box;
        border: 1px solid rgba(0,0,0,.2);
        border-radius: .3rem;
        outline: 0;
        color: #212529;
    }
    .modal-body {
        font-size: 16px;
        line-height: 1.6;
        min-height: 100px;
        padding: 10px;
    }
    .next_time {
        margin-top: 20px;
    }

    @media (min-width: 992px)  {
        .modal-content {max-width: 25%}
    }
    @-webkit-keyframes animatetop {
        from {top:-600px; opacity:0}
        to {top:0; opacity:1}
    }
    @keyframes animatetop {
        from {top:-600px; opacity:0}
        to {top:0; opacity:1}
    }
    .cart-timeslots select {
        max-width: 200px;
        margin-left: auto !important;
    }
</style>
