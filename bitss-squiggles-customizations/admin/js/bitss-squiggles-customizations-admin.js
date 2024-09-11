(function ($) {
	"use strict";
  
	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */
  	
	$(document).ready(function () {
	 
	  $("#sq_mt_save_to_table").click(function (e) {
		e.preventDefault();
		if(!confirm("Are you sure?")) return false;
		$("#sq_spinner").addClass("is-active");
		sq_mt_save_to_table();
	  });
  
	  $("#add_to_table").click(function () {
		add_data_arr();
	  });
  
	  $("#slots_table tbody").on("click", ".delete-hour", function (e) {
		e.preventDefault();
		var index = $(this).attr("data-index");
		var slots = get_arr_slots();
		slots.splice(index, 1);
		$("#kaarot_store_slots_json").val(JSON.stringify(slots));
		load_slots_table();
	  });
  
	  $("#save_data_btn").on("submit", function (e) {
		e.preventDefault();
		if(!confirm("Are you sure?")) return false;
		alert("saved");
		save_data(e);
	  });
		
	  $('body').on('click', '.edit_issue_copy_id', (function (e) {
		e.preventDefault();
		var id = $(this).attr("data-item-id");
		var product_id = $(this).attr("data-product-id");
		//alert("copy id" + id);
		$(this).css("display", "none");

		var order_id = $('#post_ID').val();		

		var select = $("<select>");
		select.append($("<option>")
			.prop('value', '')
			.text("Loading..."));
		select.attr('id', 'select-'+id);
		show_all_copy_id_dropdown(product_id,select);               

        //Load the dynamically created dropdown in container
        var container=$(".item-"+id);
		var right = $("<span>");
		  right.addClass("dashicons dashicons-yes-alt save_copy_id");
		  right.attr('id', 'right-'+id);
		  right.attr('data-item-id', id);
		  
		var no = $("<span>");
		  no.addClass("dashicons dashicons-dismiss cancle_to_save");
		  no.attr('id', 'no-'+id);
		  no.attr('data-item-id', id);
		  
        container.append(" ").append(select).append(" ").append(right).append(" ").append(no);
	  }));

	  $('body').on('click', '.save_copy_id', function (e) {
		if(!confirm('Are you sure?')) return;
	  	var item_id = $(this).attr("data-item-id");
		var order_id = $('#post_ID').val();
		var copy_post_id = $('#select-'+item_id).children('option:selected').val();
		if (item_id && item_id.trim()!="" && copy_post_id && copy_post_id.trim()!="") {
			save_issue_copy_id(copy_post_id, item_id, order_id);
		}
		else {
			alert("Please select a valid copy.");
		}
	  });

	  $('body').on('click', '.cancle_to_save', function (e) {
		var id = $(this).attr("data-item-id");
		//alert(id);
		$(this).css("display", "none");
		$('#right-'+id).css("display", "none");
		$('#select-'+id).css("display", "none");	
		$('#edit-'+id).css("display", "inline");			 
	  });
  
	  load_slots_table();  
	  load_sub_damage_table();
	});
  
	function sq_mt_save_to_table() {
	  var date = $("#sq_mt_date").val();
	  var type = $("#sq_mt_type").val();
	  var amt = $("#sq_mt_amt").val();
	  var remark = $("#sq_mt_remark").val();
	  var added_id = $("#sq_mt_added_id").val();
	  // var paid_id = $("#sq_mt_paid_id").val("paid");
	  var order_id = $("#post_ID").val();
  
	  if (
		date == "" ||
		type == "" ||
		amt == "" ||
		remark == "" ||
		added_id == ""
	  ) {
		alert("All fields are required!");
		$("#sq_spinner").removeClass("is-active");
		return;
	  }
  
	  var obj = {
		date: date,
		type: type,
		amt: amt,
		remark: remark,
		added_order_id: order_id,
	  };
  
	  save_sub_damage(obj, "save_subscription_damage");
	}
	
	function save_issue_copy_id(copy_post_id, item_id, order_id) {
		var nonce = bitss_squiggles_vars.save_issue_copy_id_nonce;
		$.post( "/wp-admin/admin-ajax.php", { action: "save_issue_copy_id", copy_post_id: copy_post_id, item_id: item_id, order_id: order_id, nonce: nonce }, function (data) {
			if (data.status) {
				alert(data.message);
				location.reload(); 
			}else{
				if(data.message){
					alert(data.message);
				}
			}
		})
	}

	function show_all_copy_id_dropdown(product_id,select) {
		var nonce = bitss_squiggles_vars.show_all_copy_id_dropdown_nonce;		
		$.post( "/wp-admin/admin-ajax.php", { action: "get_all_perticular_book_id_arr", product_id: product_id, nonce: nonce }, function (data) {
			if (data.status) {
				select.html('');
				var copy_id_arr = data.data;
				if (copy_id_arr.length<1) {
					select.append($("<option>")
					.prop('value', '')
					.text("No available copies found"));
				}
				else {
					$.each(copy_id_arr, function(index, country) {
						select.append($("<option>")
						.prop('value', index)
						.text(country));
					}); 
				}
			}
		});
	}
  
	function save_sub_damage(obj, action_name) {
	  jQuery.post( "/wp-admin/admin-ajax.php", { action: action_name, data: obj }, function (data) {
		  $("#sq_spinner").removeClass("is-active");
		  alert(data.message);
	   
		  if (data.status) {
			  $("#sub_damage_table tbody").html("");
			  var arr = data.data;
			  $.each( arr, function( key, value ) {
				  var tbody =
				  "<tr><td class='td'>" +
				  (key + 1) +
				  ".</td><td>" +
				  value.date +
				  "</td><td>" +
				  value.type +
				  "</td><td>" +
				  value.amt +
				  "</td><td>" +
				  value.remark +
				  "</td><td>" +
				  "<a href ='/wp-admin/post.php?post="+value.added_order_id + "&action=edit'>"+value.added_order_id + "</a>"+
				  "</td><td>" +
				  "<a href ='/wp-admin/post.php?post="+value.paid_order_id + "&action=edit'>"+value.paid_order_id + "</a>"+
			
				  "</td><td>"
				  // <button type='button' class='delete-hour' data-index='" + key +	"'>Delete</button>
				  "</td></tr>";
  
				  $("#sub_damage_table tbody").append(tbody);
			  })
		  }
		}
	  );
	}
  
	function get_sub_dam_data() {
	  var arr_sub_damage_data = $("#sub_damages_table_data").val();
	  if (
		arr_sub_damage_data == "" ||
		arr_sub_damage_data == null ||
		arr_sub_damage_data == "false"
	  ) {
		return [];
	  }
	  try {
		var a = JSON.parse(arr_sub_damage_data);
		if (a == null || !Array.isArray(a)) return [];
		else return a;
	  } catch {
		return [];
	  }
	}
  
	function load_sub_damage_table() {
	  var arr = get_sub_dam_data();
  
	  $("#sub_damage_table tbody").html("");
  
	  $.each(arr, function (key, value) {
		var tbody =
		  "<tr><td class='td'>" +
		  (key + 1) +
		  ".</td><td>" +
		  value.date +
		  "</td><td>" +
		  value.type +
		  "</td><td>" +
		  value.amt +
		  "</td><td>" +
		  value.remark +
		  "</td><td>" +
		  "<a href ='/wp-admin/post.php?post="+value.added_order_id + "&action=edit'>"+value.added_order_id + "</a>"+
		  "</td><td>" +
		  "<a href ='/wp-admin/post.php?post="+value.paid_order_id + "&action=edit'>"+value.paid_order_id + "</a>"+
			
		  "</td><td>";
		// <button type='button' class='delete-hour' data-index='" + key +	"'>Delete</button>
		("</td></tr>");
  
		$("#sub_damage_table tbody").append(tbody);
	  });
	}
  
	function add_data_arr() {
	  // var ship = $("#sq_zone").find("option:selected").text();
  
	  var pincode = $("#sq_pincode").val();
	  var zone = $("#sq_zone").val();
	  var capacity = $("#sq_capacity").val();
	  var date = $("#sq_date").val();
	  var from = $("#sq_sl_from").val();
	  var to = $("#sq_sl_to").val();
  
	  if (
		pincode == "" ||
		zone == "" ||
		capacity == "" ||
		date == "" ||
		from == "" ||
		to == ""
	  ) {
		alert("All fields are required!");
		return;
	  }
  
	  var obj = {
		pincode: pincode,
		zone: zone,
		capacity: capacity,
		date: date,
		start: from,
		end: to,
	  };
  
	  var arr_slots = get_arr_slots();
  
	  arr_slots.push(obj);
  
	  $("#kaarot_store_slots_json").val(JSON.stringify(arr_slots));
  
	  load_slots_table();
	}
  
	function get_arr_slots() {
	  var arr_slots_json = $("#kaarot_store_slots_json").val();
	  if (
		arr_slots_json == "" ||
		arr_slots_json == null ||
		arr_slots_json == "false"
	  ) {
		return [];
	  }
	  try {
		var s = JSON.parse(arr_slots_json);
		if (s == null || !Array.isArray(s)) return [];
		else return s;
	  } catch {
		return [];
	  }
	}
  
	function load_slots_table() {
	  var slots = get_arr_slots();
	  $("#slots_table tbody").html("");
	  for (let index = 0; index < slots.length; index++) {
		const i = slots[index];
		var row =
		  "<tr><td class='td'>" +
		  (index + 1) +
		  "</td><td>" +
		  i.pincode +
		  "</td><td>" +
		  i.zone +
		  "</td><td>" +
		  i.capacity +
		  "</td><td>" +
		  i.date +
		  "</td><td>" +
		  i.start +
		  "</td><td>" +
		  i.end +
		  "</td><td><button type='button' class='delete-hour' data-index='" +
		  index +
		  "'>Delete</button></td></tr>";
  
		$("#slots_table tbody").append(row);
	  }
	}
  
	function save_data(e) {
	  e.preventDefault();
  
	  var arr_slots = get_arr_slots();
  
	  $.post(
		"/wp-admin/admin-ajax.php",
		{ action: "save_slots_data", slots: arr_slots },
		function (data, s) {
		  if (data.status) {
			display_notification();
		  } else if (data.message != null) {
			alert(data.message + "working");
		  } else {
			alert("An error occured, please try again.");
		  }
		}
	  );
  
	  function display_notification() {
		$("#saved_notification").css("display", "block");
		const myTimeout = setTimeout(display, 5000);
		function display() {
		  $("#saved_notification").css("display", "none");
		}
	  }
	}
  })(jQuery);