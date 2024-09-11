<?php 

    $sub_damages = get_post_meta( $active_subscription_id, 'sub_damages', true);
    if(empty($sub_damages)){
        $sub_damages =[];
    }
    $sub_damages = json_encode($sub_damages);
?>

<input id="sub_damages_table_data" type="hidden" value='<?= $sub_damages ?>'>

<div class="field is-horizontal">
    <div class="field-label is-normal">
        <label class="label">Date :</label>
    </div>
    <div class="field-body">
        <div class="field">
            <p class="control is-normal has-icons-left">
                <input class="input" id="sq_mt_date" type="date" placeholder="Date">
                <span class="icon is-small is-left">
                    <i class="dashicons dashicons-edit"></i>
                </span>
            </p>
        </div>
    </div>
    <div class="field-label is-normal">
        <label class="label">Type :</label>
    </div>
    <div class="field-body">
        <div class="field">
            <p class="control is-normal has-icons-left">
            <div class="select">
                <select id="sq_mt_type">
                    <option>Select dropdown</option>
                    <option value='Damage Fees'>Damage Fees</option>
                    <option value='Late Fees'>Late Fees</option>
                    <option value='Misc Charges'>Misc Charges</option>
                </select>
            </div>
            </p>
        </div>
    </div>
    <div class="field-label is-normal">
        <label class="label">Amount :</label>
    </div>
    <div class="field-body">
        <div class="field">
            <p class="control is-normal has-icons-left">
                <input class="input" id="sq_mt_amt" type="number" placeholder="₹ Amount">
                <span class="icon is-small is-left">
                    <i class="dashicons dashicons-edit"></i>
                </span>
            </p>
        </div>
    </div>
</div>
<div class="columns">
    <div class="column">
        <div class="field is-horizontal">
            <div class="field-label is-normal">
                <label class="label">Remark :</label>
            </div>
            <div class="field-body">
                <div class="field">
                    <p class="control is-normal has-icons-left">
                        <input class="input" id="sq_mt_remark" type="text" placeholder="Remarks">
                        <span class="icon is-small is-left">
                            <i class="dashicons dashicons-edit"></i>
                        </span>
                    </p>
                </div>
            </div>
        </div>
    </div>
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
                <th>Type</th>
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