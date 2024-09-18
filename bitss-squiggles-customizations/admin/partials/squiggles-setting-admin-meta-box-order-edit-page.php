<?php 

    $sub_damages = get_post_meta( $active_subscription_id, 'sub_damages', true);
    if(empty($sub_damages)){
        $sub_damages =[];
    }
// 	echo "<pre>"; print_r($sub_damages); die;
// 	  $data = [];
    foreach ($sub_damages as $k => $item) {
        
        if (!empty($item['sq_books_ids'])) {
			$image_urls = [];
            foreach ($item['sq_books_ids'] as $image_id) {
           		$image_url = wp_get_attachment_image_src($image_id, 'full'); 
// 				var_dump($image_url[0]);
                if ($image_url) {
                    $image_urls[] = $image_url[0];
                }
            }
			$sub_damages[$k]['sq_books_ids'] = $image_urls;
        }
	}
// echo "<pre>"; print_r($sub_damages);die;
    $sub_damages = json_encode($sub_damages);
?>

<input id="sub_damages_table_data" type="hidden" value='<?= $sub_damages ?>'>
<?php foreach($items as $item): ?>
    <?php if($item->get_meta("issue_or_return")=='return'): 
$booked_copy_post_id = $item->get_meta("return_copy_post_id");
?>
<input id="sq_copy_id" type="hidden" value='<?php echo get_post_meta($booked_copy_post_id, 'wpcf-copy-id', true) ?>'>
<input id="sq_product_id" type="hidden" value='<?php echo $item->get_product_id() ?>'>
<?php endif; endforeach; ?>

<style>
/*     .sq_fields_and_labels{
        display: flex;
        margin: 5px;
         width: 50%; 
    }
    .sq_fields_and_labels label{
        text-align: left
    }
    .sq_fields_and_labels select{
        padding: 5px;
        width: 150px;
    }
    .sq_fields_and_labels input{
        
        width: 150px;
    }
    .sq_fields_and_labels .field-label{
        width: 30%;
    }
    .sq_fields_and_labels .field-label{
        width: 30%;
    }
    .sq_squiggles_damage_inputs{
        display: flex;
    }
	@media (max-width: 768px) {
		.sq_squiggles_damage_inputs{
			display: unset;
		}
	} */
</style>

<div class="field is-horizontal" style="display:unset !important">
    <div class="sq_squiggles_damage_inputs">
        <div class="sq_fields_and_labels">
            <div class="field-label is-normal">
                <label class="label">Reason:</label>
            </div>
            <div class="field-body">
                <div class="field">
                    
                    <div class="">
                        <select class="sq_amount_auto_cal" id="sq_mt_type">
                            <option  value=''>Select dropdown</option>
                            <option value='Ripped Pages'>Ripped Pages</option>
                            <option value='Liquid Damage'>Liquid Damage</option>
                            <option value='Book Lost'>Book Lost</option>
                            <option value='Other Damages'>Other Damages</option>
                        </select>
                    </div>
                    
                </div>
            </div>
        </div>
        <div class="sq_fields_and_labels">
            <div class="field-label is-normal">
                <label class="label">Severity:</label>
            </div>
            <div class="field-body">
                <div class="field">
                    <div class="">
                        <select class="sq_amount_auto_cal" id="sq_mt_severity">
                            <option value=''>Select dropdown</option>
                            <option value='1'>1</option>
                            <option value='2'>2</option>
                            <option value='3'>3</option>
                            <option value='4'>4</option>
                            <option value='5'>5</option>
                        </select>
                    </div>
                    
                </div>
            </div>
        </div>
        <div class="sq_fields_and_labels">
            <div class="field-label is-normal">
                <label class="label">Copy:</label>
            </div>
            <div class="field-body">
                <div class="field">
                    
                    <div class="">
                        <select class="sq_amount_auto_cal" id="sq_mt_copy">
                            <option value="">Select dropdown</option>
                            <?php foreach($items as $item): ?>
                                <?php if($item->get_meta("issue_or_return")=='return'): 
                            $booked_copy_post_id = $item->get_meta("return_copy_post_id");

                            $product_id = $item->get_product_id();
							$product_price = get_post_meta($product_id,'wpcf-mrp',true);
							$copy_price = get_post_meta($product_id,'wpcf-copy-mrp',true);
							if($copy_price){
								$price = $copy_price;
							}else{
								$price =$product_price;
							}

                            ?>
                                    <option data-product-price='<?php echo $price; ?>' value='<?php echo $item->get_name() . ' (' . get_post_meta($booked_copy_post_id, 'wpcf-copy-id', true) . ')'; ?>'><?php echo $item->get_name() . ' (' . get_post_meta($booked_copy_post_id, 'wpcf-copy-id', true) . ')'; ?></option>
                            <?php endif; endforeach; ?>
                        </select>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
    <div class="sq_squiggles_damage_inputs">
        <div class="sq_fields_and_labels">
            <div class="field-label is-normal">
                <label class="label">Date:</label>
            </div>
            <div class="field-body">
                <div class="field">
                    
                        <input class="input" id="sq_mt_date" type="date" placeholder="Date">
                    
                </div>
            </div>
        </div>
        <div class="sq_fields_and_labels">
            <div class="field-label is-normal">
                <label class="label">Amount:</label>
            </div>
            <div class="field-body">
                <div class="field">
                    
                        <input class="input" id="sq_mt_amt" type="number" placeholder="₹ Amount">
                    
                </div>
            </div>
        </div>
        <div class="sq_fields_and_labels">
            <div class="field-label is-normal">
                <label class="label">Remark:</label>
            </div>
            <div class="field-body">
                <div class="field">
                    
                        <input class="input" id="sq_mt_remark" type="text" placeholder="Remarks">
                    
                </div>
            </div>    
        </div>
    </div>
	<div class="sq_squiggles_damage_inputs">
		<div class="sq_fields_and_labels">
			<div class="field-label is-normal">
				<label class="label">Upload Damage Book Images:</label>
			</div>
			<div class="field-body">
				<div class="field">
					<button type="button" class="" id="sq-upload-damage-images-button">Upload Images</button>
					<div class="sq_squiggles_damage_inputs" id="sq-damage-book-img">
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="columns">
    <div class="column is-one-quarter">
        <input class="button button is-info" id="sq_mt_save_to_table" type="submit" value="Save Data">
        <span id="sq_spinner" class="spinner"></span>
    </div>
</div>

<div class="sq_sd_table table-container">
    <table class="table is-bordered" id="sub_damage_table">
        <thead>
            <tr>
                <th>S.No.</th>
                <th>Date</th>
                <th>Reason</th>
                <th>Severity</th>
                <th>Copy</th>
                <th>Amount</th>
                <th>Remark</th>
                <th>Added Order Id</th>
                <th>Paid Order Id</th>
            </tr>
        </thead>
        <tbody>
            
        </tbody>
    </table>
</div>

<script type="text/javascript">
	jQuery(document).ready(function() {
// 		jQuery(document).on('change', '.sq_amount_auto_cal', function() {
//             var product_price = jQuery('#sq_mt_copy option:selected').attr('data-product-price');
// 			var reason = jQuery('#sq_mt_type').val();
// 			var severity = jQuery('#sq_mt_severity').val();
// 			if (product_price && reason && severity) {
// 				var damage_amount = calculate_damage_amount_auto(product_price, reason, severity);
// 				jQuery('#sq_mt_amt').val(damage_amount.toFixed(2));
// 			}
// 		});
// 		function calculate_damage_amount_auto(product_price, reason, severity) {
// 		if (product_price && reason && severity) {
// 			let ten_percent_amount = (10 / 100) * product_price;
// 			let thirty_percent_amount = (30 / 100) * product_price;
// 			let full_amt = parseFloat(product_price) + parseFloat(ten_percent_amount);
// 			let damage_amount = 0;
	
// 			if (reason === 'Ripped Pages') {
// 				if (severity > 0 && severity < 4) {
// 					damage_amount = Math.max(20, Math.min(ten_percent_amount, 50));
// 				} else {
// 					damage_amount = Math.max(50, Math.min(thirty_percent_amount, 300));
// 				}
// 			}
	
// 			else if (reason === 'Liquid Damage') {
// 				if (severity > 0 && severity < 4) {
// 					damage_amount = Math.max(20, Math.min(ten_percent_amount, 50));
// 				} else {
// 					damage_amount = Math.max(50, Math.min(thirty_percent_amount, 300));
// 				}
// 			}
	
// 			else if (reason === 'Book Lost') {
// 				if (severity > 0 && severity < 4) {
// 					damage_amount = Math.max(20, Math.min(ten_percent_amount, 50));
// 				} else {
// 					damage_amount = full_amt;
// 				}
// 			}
	
// 			else if (reason === 'Other Damages') {
				
// 			}
	
// 			return damage_amount;
// 		}
// 		return 0;
// 	}
	});
</script>
<script>
jQuery(document).ready(function($) {
    var frame;
    $('#sq-upload-damage-images-button').on('click', function(e) {
        e.preventDefault();
        if ( frame ) {
            frame.open();
            return;
        }

        // Open media library to upload/select multiple images
        frame = wp.media({
            title: 'Select or Upload Images',
            button: {
                text: 'Use these images'
            },
            multiple: true
        });

        frame.on('select', function() {
            var attachments = frame.state().get('selection').toJSON();
            attachments.forEach(function(attachment) {
                $('#sq-damage-book-img').append(
                    '<div><img src="' + attachment.url + '" style="max-width: 100px;" />' +
                    '<input type="hidden" class ="sq-damage-book-img" value="' + attachment.id + '" />' +
                    '<button type="button" class="remove-image-button">Remove</button></div>'
                );
            });
        });

        frame.open();
    });

    // Remove image functionality
    $(document).on('click', '.remove-image-button', function() {
        $(this).parent().remove();
    });
});
</script>

