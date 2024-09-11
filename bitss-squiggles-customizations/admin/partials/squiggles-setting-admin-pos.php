<div class="container is-max-desktop">
    <nav class="panel">
        <p class="panel-heading">
            Issue Books offline here
        </p>
        <div class="panel-block">
            <p class="control is-half has-icons-left">
                <input class="input" id="bt_sq_user_detail_input" type="text" placeholder="Search">
                <span class="icon is-left">
                    <i class="fas fa-search" aria-hidden="true"></i>
                </span>
            </p><button type="button" id="bt_sq_search_user" class="m-2 button is-info is-normal">Search</button>
        </div>
    </nav>

    <h3 class="bt_sq_rnf bt_sq_hide">Result Not Found!</h3>
    <table class="table bt_sq_user_details_tb bt_sq_hide">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Phone No.</th>
                <th>Plan</th>
                <th></th>
            </tr>
        </thead>
        <tbody class="bt_sq_user_searched_list table">

        </tbody>
    </table>
</div>

<style>
    .bt_sq_hide {
        display: none;
    }
</style>
