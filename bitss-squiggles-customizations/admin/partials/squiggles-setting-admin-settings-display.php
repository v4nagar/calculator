<div class="container is-widescreen">
    <nav class="panel">
    <p class="panel-heading">
        Squggles Settings
    </p>

    <p class="panel-tabs">
        <div class="tabs tabs is-boxed is-medium">
            <ul>
                <li class="<?php echo $tab == "delivery" ? "is-active" : "" ?>"><a
                        href="<?php echo admin_url('admin.php?page=wc-admin-squiggles-settings&tab=delivery'); ?>">
                        Delivery Slots
                        </a>
                </li>
                <li class="<?php echo $tab == "books_import" ? "is-active" : "" ?>"><a
                        href="<?php echo admin_url('admin.php?page=wc-admin-squiggles-settings&tab=books_import'); ?>">
                        Books Import
                    </a>
                </li>
                <li class="<?php echo $tab == "books_inventory" ? "is-active" : "" ?>"><a
                        href="<?php echo admin_url('admin.php?page=wc-admin-squiggles-settings&tab=books_inventory'); ?>">
                        Books Inventory
                    </a>
                </li>
            </ul>
        </div>
    </p>
    <div class="panel-block is-active">
       
            <?php if ($tab == "delivery"):
                $data = get_option("squggles_delivery_slots");
                $data = json_encode($data);
                ?>
                <form method="POST" id="save_data_btn" action="">
                    <input type="hidden" value='<?= $data ?>' name="kaarot_store_slots_json" id="kaarot_store_slots_json">

                    <div class="columns">
                        <div class="column is-four-fifths ">
                            <div class=" section has-background-link-light">
                                <div class="field is-horizontal">
                                    <div class="field-label is-normal">
                                        <label class="label">Location :</label>
                                    </div>
                                    <div class="field-body">
                                        <div class="field">
                                            <p class="control is-expanded has-icons-left">
                                                <input class="input" id="sq_pincode" type="text" placeholder="Pincode">
                                                <span class="icon is-small is-left">
                                                    <i class="dashicons dashicons-edit"></i>
                                                </span>
                                            </p>
                                        </div>
                                        <div class="field">
                                            <p class="control is-expanded has-icons-left">
                                                <input class="input" id="sq_zone" type="text" placeholder="Zone">
                                                <span class="icon is-small is-left">
                                                    <i class="dashicons dashicons-edit"></i>
                                                </span>
                                            </p>
                                        </div>
                                        <div class="field">
                                            <p class="control is-expanded has-icons-left">
                                                <input class="input" id="sq_capacity" type="text" placeholder="Capacity">
                                                <span class="icon is-small is-left">
                                                    <i class="dashicons dashicons-edit"></i>
                                                </span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="field is-horizontal">
                                    <div class="field-label is-normal">
                                        <label class="label">Slot : </label>
                                    </div>
                                    <div class="field-body">
                                        <div class="field">
                                            <p class="control is-normal has-icons-left">
                                                <input class="input" id="sq_date" type="date" placeholder="Date">
                                                <span class="icon is-small is-left">
                                                    <i class="dashicons dashicons-edit"></i>
                                                </span>
                                            </p>
                                        </div>
                                        <div class="field">
                                            <p class="control is-expanded has-icons-left">
                                                <input class="input" id="sq_sl_from" type="time" placeholder="start">
                                                <span class="icon is-small is-left">
                                                    <i class="dashicons dashicons-edit"></i>
                                                </span>
                                            </p>
                                        </div>
                                        <div class="field-label is-normal">
                                            <label class="label">to</label>
                                        </div>
                                        <div class="field">
                                            <p class="control is-expanded has-icons-left">
                                                <input class="input" id="sq_sl_to" type="time" placeholder="end">
                                                <span class="icon is-small is-left">
                                                    <i class="dashicons dashicons-edit"></i>
                                                </span>
                                            </p>
                                        </div>
                                        <div class="field">
                                            <p class="control is-expanded has-icons-left">
                                                <button id="add_to_table" class="button is-dark is-align-self-flex-end"
                                                    type="button">Add</button>

                                            </p>
                                        </div>
                                    </div>
                                </div>
                        
                                <br><br>

                                <table style="width: 100%" id="slots_table"
                                    class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth" border="1"
                                    style="width:50%;">
                                    <thead>
                                        <tr>
                                            <th>S.no.</th>
                                            <th>Pincode</th>
                                            <th>Zone</th>
                                            <th>Capacity</th>
                                            <th>Date</th>
                                            <th>Start Time</th>
                                            <th>End Time</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="column">
                            <div class="tile  is-vertical">
                                <button class="button is-info " type="submit">Save</button>
                                <br>
                                <div id="saved_notification" class="notification is-primary" style="display: none;">
                                    <!-- <button class="delete"></button> -->
                                    <h6>Saved Successfully!</h6>
                                </div>
                            </div>
                        </div>
                    </div>

                </form>

            <?php elseif ($tab == "books_inventory"): ?>
            <?php 
                    

            ?>

            <a class="button is-primary is-medium" href="<?php echo admin_url('?bt_export_copy=1'); ?>" target="_black">
                <span class="icon is-small">
                    <i class="fas fa-download"></i>
                </span>   
                <span>Export Copies</span>
            </a>


            <?php elseif ($tab == "books_import"): ?>
            <?php 
                    require_once 'books-import.php';
            ?>

            

            <? endif ?>


     
    </div>

    </nav>
</div>

