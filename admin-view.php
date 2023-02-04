<?php
add_action('admin_enqueue_scripts', function () {
    if (isset($_GET['page']) == 'si_investment') {
        wp_enqueue_style('si-bootrap', '//cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css', [], time(), 'all');
    }
});

add_action('admin_menu', function () {
    add_menu_page(__('SI Investment', 'my-listing'), __('SI Investment', 'my-listing'), 'manage_options', 'si_investment', 'si_investment_callback', 'dashicons-money');
});


function sky_character_limi($x, $length)
{
    if (strlen($x) <= $length) {
        echo $x;
    } else {
        $y = substr($x, 0, $length) . '...';
        return $y;
    }
}


function si_investment_callback()
{

    global $wpdb;

    $table = $wpdb->prefix . 'sky_investment_user_info';
    $query = "SELECT * FROM $table ORDER BY submitted_at DESC LIMIT 50";
    $result = $wpdb->get_results($query);

    $page_action = isset($_GET['action']) ? $_GET['action'] : 'view';



    if ($page_action == 'view') :

?>
        <link href="//cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css" type="stylesheet">
        <script src="//cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
        <style>
            div#sky_invest_data_table_paginate a {
                margin-right: 15px;
                cursor: pointer;
            }

            table#sky_invest_data_table th a:last-child {
                background: red;
                color: #fff;
                text-decoration: none;
                font-size: 80%;
                padding: 4px 6px;
                border-radius: 3px;
            }
        </style>

        <script>
            jQuery(document).ready(function($) {
                $('#sky_invest_data_table').DataTable({
                    "bJQueryUI": true,
                    "bSort": true,
                    "bPaginate": true,
                    "sPaginationType": "full_numbers",
                    "iDisplayLength": 50
                });
            });
        </script>
        <div class="sky-invest-sec">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="sky-invest-content mt-3">
                            <h1 class="wp-heading-inline">Mange Investors</h1>



                        </div>
                    </div>
                </div>
                <div class="row">

                    <div class="col-lg-12">
                        <table id="sky_invest_data_table" class="table">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Date</th>
                                    <th scope="col">Name</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">Amount ($) </th>
                                    <th scope="col">List Name</th>
                                    <th scope="col">Place Name</th>
                                    <th scope="col">Listing total ($) </th>
                                    <th scope="col">Remaining total ($) </th>
                                    <th scope="col">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($result as $investor) : ?>
                                    <tr>
                                        <th scope="row">
                                            <a href="<?php echo admin_url('admin.php?page=si_investment&action=edit&investid=' . $investor->id); ?>">Edit </a>
                                            <a onclick="return confirm('Are you sure to delete?')" href="<?php echo admin_url('admin.php?page=si_investment&action=delete&investid=' . $investor->id); ?>"> Delete</a>
                                        </th>
                                        <td> <?php echo date('g:i a - m/d/Y', strtotime($investor->submitted_at)); ?></td>
                                        <td><?php echo $investor->fname . ' ' . $investor->lname; ?></td>
                                        <td><?php echo $investor->email; ?></td>
                                        <td><strong><?php echo number_format($investor->amount, 2); ?></strong></td>
                                        <td><a href="<?php echo get_the_permalink($investor->list_id); ?>"><?php echo sky_character_limi($investor->listname, 20) . '/' . $investor->id; ?></a></td>
                                        <td><?php echo sky_character_limi($investor->placename, 20); ?></td>
                                        <!-- <td><?php //echo number_format($investor->totamount, 2); 
                                                    ?></td> -->
                                        <td> <?php echo !empty(get_post_meta($investor->list_id, '_total_raised_amount', true)) ? get_post_meta($investor->list_id, '_total_raised_amount', true) : ''; ?></td>
                                        <td><?php echo number_format($investor->amountleft, 2); ?></td>
                                        <td>
                                            <span class="badge bg-info">
                                                <?php
                                                if (!empty($investor->status)) {
                                                    echo $investor->status;
                                                }
                                                ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>

                    </div>

                </div>
            </div>
        </div>

    <?php
    elseif ($page_action == 'edit') : ?>



        <div class="sky-invest-sec">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="sky-invest-content mt-3">
                            <h1 class="wp-heading-inline">Edit Investors</h1>



                        </div>
                    </div>
                </div>
                <div class="row">
                    <?php
                    $id = isset($_GET['investid']) ? $_GET['investid'] : '0';

                    $single_query = "SELECT * FROM $table WHERE id = $id";
                    $single_result = $wpdb->get_results($single_query);

                    foreach ($single_result as $investor) : ?>
                        <?php
                        if (isset($_POST['inv_submit'])) {
                            $fname = isset($_POST['fname']) ? sky_inv_user_input($_POST['fname']) : '';
                            $lname = isset($_POST['lname']) ? sky_inv_user_input($_POST['lname']) : '';
                            $email = isset($_POST['email']) ? sky_inv_user_input($_POST['email']) : '';
                            $listname = isset($_POST['listname']) ? sky_inv_user_input($_POST['listname']) : '';
                            $placename = isset($_POST['placename']) ? sky_inv_user_input($_POST['placename']) : '';
                            $amount = isset($_POST['amount']) ? sky_inv_user_input($_POST['amount']) : '';
                            $listing_total = isset($_POST['listing_total']) ? sky_inv_user_input($_POST['listing_total']) : '';
                            $remaining_total = isset($_POST['remaining_total']) ? sky_inv_user_input($_POST['remaining_total']) : '';
                            $status = isset($_POST['status']) ? sky_inv_user_input($_POST['status']) : '';


                            $data = [
                                'fname' => "$fname",
                                'lname' => "$lname",
                                'email' => "$email",
                                'listname' => $listname,
                                'placename' => $placename,
                                'amount' => $amount,
                                'totamount' => $listing_total,
                                'amountleft' => $remaining_total,
                                'status' => $status,
                            ];

                            $update_table = $wpdb->update($table, $data, ['id' => $id]);

                            if ($update_table) {
                                wp_redirect(admin_url('admin.php?page=si_investment'));
                            } else {
                                echo '<p>Could not update data, please try again';
                            }
                        }
                        ?>



                        <div class="col-lg-12">
                            <form action="" method="POST">
                                <div class="form-group mt-3">
                                    <label for="name">First Name</label>
                                    <input type="text" class="form-control" name="fname" value="<?php echo $investor->fname; ?>" id="name" placeholder="First Name">
                                </div>

                                <div class="form-group mt-3">
                                    <label for="name">Last Name</label>
                                    <input type="text" class="form-control" name="lname" value="<?php echo $investor->lname; ?>" id="name" placeholder="Last Name">
                                </div>

                                <div class="form-group mt-3">
                                    <label for="email">email</label>
                                    <input type="text" class="form-control" name="email" value="<?php echo $investor->email; ?>" id="email" placeholder="email">
                                </div>

                                <div class="form-group mt-3">
                                    <label for="listname">Listing Name</label>
                                    <input type="text" class="form-control" name="listname" value="<?php echo $investor->listname; ?>" id="listname" placeholder="Listing Name">
                                </div>

                                <div class="form-group mt-3">
                                    <label for="placename">Place Name</label>
                                    <input type="text" class="form-control" name="placename" value="<?php echo $investor->placename; ?>" id="placename" placeholder="Place Name">
                                </div>

                                <div class="form-group mt-3">
                                    <label for="amount">Amount</label>
                                    <input type="text" class="form-control" name="amount" value="<?php echo ($investor->amount); ?>" id="amount" placeholder="Amount">
                                </div>

                                <div class="form-group mt-3">
                                    <label for="listing_total">Listing Total</label>
                                    <input type="text" class="form-control" name="listing_total" value="<?php echo ($investor->totamount); ?>" id="listing_total" placeholder="Listing Total">
                                </div>

                                <div class="form-group mt-3">
                                    <label for="remaining_total">Remaining Total</label>
                                    <input type="text" class="form-control" name="remaining_total" value="<?php echo ($investor->amountleft); ?>" id="remaining_total" placeholder="Remaining Total">
                                </div>
                                <br />
                                <select name="status" class="form-control form-control-lg">
                                    <option disabled>Select</option>
                                    <?php
                                    if (!empty($investor->status)) {
                                        echo '<option selected>' .  ucfirst($investor->status) . '</option>';
                                    }

                                    ?>
                                    <option value="received">Received</option>
                                    <option value="clear">Clear</option>
                                    <option value="rejected">Rejected</option>
                                </select>

                                <input type="submit" name="inv_submit" class="btn btn-primary mt-5">

                            <?php endforeach; ?>
                            </form>
                        </div>
                </div>
            </div>
        </div>
<?php elseif ($page_action == 'delete') :

        $del_id = isset($_GET['investid']) ? $_GET['investid'] : '0';

        $delete = $wpdb->delete($table, ['id' => $del_id]);

        if ($delete) {
            wp_redirect(admin_url('admin.php?page=si_investment'));
        } else {
            echo '<p>Could not be deleted data, please try again';
        }


    endif;
}
