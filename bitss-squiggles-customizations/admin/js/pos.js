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
        $("body").on("click", "#bt_sq_search_user", function (e) {
        e.preventDefault();
        var value = $("#bt_sq_user_detail_input").val();
        // alert(value);
        $(this).addClass("is-loading");
        search_user(value);

        });
    });

    function search_user(value) {
     var nonce = bitss_squiggles_pos_vars.search_user_nonce;
        $.get(
            bitss_squiggles_pos_vars.ajax_url,
        { action: "api_call_search_for_user", input: value, nonce: nonce },
        function (res) {
            // alert(res.data);
            $(".bt_sq_user_searched_list").html('');
            if (res.data.length > 0) {
                
                if ( !$(".bt_sq_rnf").hasClass("bt_sq_hide")) {
                    $(".bt_sq_rnf").addClass("bt_sq_hide");
                }
                if ( $(".bt_sq_user_details_tb").hasClass("bt_sq_hide")) {
                    $(".bt_sq_user_details_tb").removeClass("bt_sq_hide");
                }
                $("#bt_sq_search_user").removeClass("is-loading");

                var array = res.data;
                array.forEach((el) => {
                $(".bt_sq_user_searched_list").append(
                    "<tr><td>" +
                    el.user_name +
                    "</td><td>" +
                    el.user_email +
                    "</td><td> " +
                    el.user_phone +
                    "</td><td> " +
                    el.plan_name +
                    '</td><td><a class="bt_sq_switch_to_this_user" href="' +
                    el.switch_url +
                    '">Switch to</a></td></tr>'
                );
                });
            } else {
                $("#bt_sq_search_user").removeClass("is-loading");
                if ( $(".bt_sq_rnf").hasClass("bt_sq_hide")) {
                    $(".bt_sq_rnf").removeClass("bt_sq_hide");
                }
                if ( !$(".bt_sq_user_details_tb").hasClass("bt_sq_hide")) {
                    $(".bt_sq_user_details_tb").addClass("bt_sq_hide");
                }
            }
        }
        ).fail(function (err) {
            $("#bt_sq_search_user").removeClass("is-loading");
            alert("An error occured!");
        });
    }
})(jQuery);
