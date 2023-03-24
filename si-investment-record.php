<?php

/*
Plugin Name: Sky Investment Investment Record
Plugin URI:  
Description: A plugin for investment tracking
Version:     1.0
Author:      Eddie Voe
Author URI:  https://www.almn.me/
*/
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


function _sky_input_form_validation($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
  }

 add_action('save_post_job_listing', function( $post_id ){

    if( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    $amount = isset( $_POST['amount-to-raise-from-investors'] ) ? _sky_input_form_validation( $_POST['amount-to-raise-from-investors'] ) : '';

    update_post_meta( $post_id, '_total_raised_amount', $amount );
 });


// Include admin view 
require_once __DIR__ . '/admin-view.php';
require_once __DIR__ . '/investor-form.php';

/**
 * Add a button to single job listing 
 * single investment page 
 */

add_action('si_single_page_custom_button', 'si_listing_page_button_hook_cb');
function si_listing_page_button_hook_cb(){
?>
    <?php
    if (is_user_logged_in()) : ?>
        <?php
        $amount = sky_get_our_own_data(get_current_user_id(), get_the_ID(), 'amountleft');
        if ($amount == 0 && null != $amount) : ?>

        <!-- If the project has been invested its all amount  -->
        <li>
            <button disabled type="button" class="btn btn-primary">
                Invested
            </button>
        </li>
        <?php else : ?>

            <!-- The project is open for investment  -->
            <li>
                <button id="sky-single-investment-popup-btn" type="button" class="btn btn-primary" data-toggle="modal" data-target="#sktyInvestContact">
                    Invest
                </button>
            </li>
            
        <?php
        endif;
        ?>

    <?php else : ?>
        <a class="btn brn-primary investment-login-btn" href="/sky/my-account-2/?register">Login to invest</a>
    <?php endif; ?>

    <?php
}

add_action('wp_head', function () {
    if (is_user_logged_in() && is_singular('job_listing')) {
    ?>
        <style>
            button#sky-single-investment-popup-btn,
            .single-job_listing .investment-login-btn {
                background: var(--accent);
                border: none;
                padding: 8px 25px;
                font-size: 20px;
                border-radius: 3px;
                font-weight: bold;
                text-transform: uppercase;
            }

            .single-job_listing .investment-login-btn {
                font-size: 14px;
                padding: 8px 10px;
                color: #fff;
                margin-left: 10px;
                line-height: 26px;
            }

            form#sly_invest_contact_form .modal-header button {
                position: absolute;
                top: -9px;
                right: 20px;
                color: #fff;
                opacity: 1;
                text-align: center;
                border-radius: 50%;
                height: 25px !important;
                width: 25px !important;
                background: transparent !important;
            }

            form#sly_invest_contact_form .modal-header button:after {
                content: '';
                position: absolute;
                left: 15px;
                top: 4px;
                background: red;
                height: 26px;
                width: 26px;
                z-index: -1;
                border-radius: 50%;
            }

            form#sly_invest_contact_form input#amount {
                padding: 8px 20px;
                font-size: 30px;
                margin-left: 5%;
                width: 90%;
            }

            h5#sktyInvestContact {
                font-size: 28px;
                margin-bottom: 20px;
            }

            div#sktyInvestContact .modal-dialog .modal-footer button {
                padding: 20px 50px;
                font-size: 20px;
            }

            form#sly_invest_contact_form input#amount::placeholder {
                color: #aaa;
            }

            label.sky-dollar-symbol {
                position: absolute;
                top: 34%;
                left: 20px;
                font-size: 25px;
            }

            div#sktyInvestContact .modal-dialog {
                    margin-top: 150px;
                }

                div#sktyInvestContact {
                    background: rgba(0, 0, 0, 0.8);
                }

                div#sktyInvestContact .modal-dialog .modal-content {
                    padding: 22px;
                }

                div#sktyInvestContact .modal-dialog .modal-content input {
                    background: #eee;
                    margin-top: 12px;
                    border: none;
                }

                div#sktyInvestContact .modal-dialog .modal-content button {
                    background: tomato;
                    padding: 8px 25px;
                    border: none;
                    margin-top: 21px;
                    margin-bottom: 5px;
                    text-transform: uppercase;
                    font-size: 18px;
                }
        </style>

        <?php

        // Bootstrap form in the head 

        $amount = sky_get_our_own_data(get_current_user_id(), get_the_ID(), 'amountleft');
        if ($amount > 0 || null == $amount) : ?>

            <!-- Modal -->
            <?php

            $fname = '';
            $lname = '';
            $dname = '';
            $email = '';
            $total_amount = get_post_meta(get_the_ID(), '_amount-to-raise-from-investors', true);

            if (is_user_logged_in()) {

                $user = get_user_by('id', get_current_user_id());
                $email = $user->user_email;


                if (!empty($user->first_name) && !empty($user->last_name)) {
                    $fname = $user->first_name;
                    $lname = $user->last_name;
                } else {
                    $dname = $user->display_name;
                }
            }

            function _sky_inv_listingname()
            {
                $categories = get_terms('region', array(
                    'orderby'    => 'parent',
                ));

                $numItems = count($categories);
                $i = 0;
                foreach ($categories as $place) {
                    echo $place->name;
                    if (++$i != $numItems) {
                        echo ", ";
                    }
                }
            }

            ?>

            <div class="modal fade" id="sktyInvestContact" tabindex="-1" role="dialog" aria-labelledby="sktyInvestContact" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <form id="sly_invest_contact_form" method="POST">
                            <input type="hidden" name="sky_inv">
                            <div class="modal-header">
                                <h5 class="modal-title" id="sktyInvestContact">Invest</h5>
                                <p id="inv-success" style="color:blue;display:none;">Congratulation! You have successfully invested</p>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <input id="item-id" type="hidden" name="item-id" value="<?php echo get_the_ID(); ?>">
                                <input id="user-id" type="hidden" name="user-id" value="<?php echo get_current_user_id(); ?>">
                                <input id="fname" type="hidden" name="fname" value="<?php echo $fname; ?>">
                                <input id="lname" type="hidden" name="lname" value="<?php echo $lname; ?>">
                                <input id="email" type="hidden" name="email" value="<?php echo $email; ?>">

                                <input id="list_name" type="hidden" name="list_name" value="<?php echo get_the_title(get_the_ID()); ?>">
                                <input id="place_name" type="hidden" name="place_name" value="<?php echo _sky_inv_listingname(); ?>">

                                <input id="tot_amount" type="hidden" value="<?php echo $total_amount; ?>" name="tot_amount">
                                <label class="sky-dollar-symbol" for="amount">$</label>
                                <input required id="amount" placeholder="50000" type="number" placeholder="Amount" min="100" max="<?php echo $total_amount; ?>" name="amount">
                                <label style="margin-top: 20px;" for="agreement"> <input type="checkbox" name="agreement" id="agreement" required>
                                    I agree to the Site's <a href="#" style="color: var(--accent);"> Terms and Conditions </a> and Accept all risks that are laid out in the Investment Risk Disclosure</label>
                                <input id="amount_left" type="hidden" name="amount_left" value="<?php echo $total_amount; ?>">
                            </div>
                            <div class="modal-footer">
                                <button type="submit" name="submit" class="btn btn-primary">Submit</button>
                                <!-- <input type="submit" name="submit" value="submit"> -->
                            </div>
                        </form>
                    </div>
                </div>
            </div>
<?php endif;
    }
});

/**
 * Create necessary database tables
 *
 * @return void
 */

add_action('admin_init', 'sky_invest_create_tables');

function sky_invest_create_tables(){
    global $wpdb;

    $charset_collate = $wpdb->get_charset_collate();

    $schema = "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}sky_investment_user_info` (
          `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
          `user_id` int(11) NOT NULL,
          `list_id` int(11) NOT NULL,
          `fname` varchar(100) NOT NULL DEFAULT '',
          `lname` varchar(100) NOT NULL DEFAULT '',
          `email` varchar(100) NOT NULL DEFAULT '',
          `listname` varchar(200) NOT NULL DEFAULT '',
          `placename` varchar(500) NOT NULL DEFAULT '',
          `totamount` int(50) NOT NULL,
          `amount` int(50) NOT NULL,
          `amountleft` int(50) NOT NULL,
          `submitted_at` datetime NOT NULL,
          `status` varchar(30) DEFAULT 'New',
          PRIMARY KEY (`id`)
        ) $charset_collate";

    if (!function_exists('dbDelta')) {
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    }

    dbDelta($schema);
}


add_action('wp_enqueue_scripts', function () {
    wp_enqueue_script('styylish_sa2', '//cdn.jsdelivr.net/npm/sweetalert2@11', ['jquery'], time(), true);
    wp_enqueue_script('styylish_tearsheet_template', plugin_dir_url( __FILE__ ) .  'assets/js/script.js', ['jquery', 'styylish_sa2'], time(), true);

    wp_localize_script('styylish_tearsheet_template', 'skyInvesContactFrom', [
        'url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('sky_nonce'),
    ]);
});


/**
 * Submit form using ajax
 * Form submit and save it to DB
 */

add_action("wp_ajax_sky_invest_cf", "sky_invest_contact_form_cb");
add_action("wp_ajax_nopriv_sky_invest_cf", "sky_invest_contact_form_cb");

function sky_invest_contact_form_cb()
{
    global $wpdb;
    $table = $wpdb->prefix . 'sky_investment_user_info';



    $fname = $_POST['fname'] ? sky_inv_user_input($_POST['fname']) : '';
    $lname = $_POST['lname'] ? sky_inv_user_input($_POST['lname']) : '';
    $email = $_POST['email'] ? sky_inv_user_input($_POST['email']) : '';
    $listing_name = $_POST['list_name'] ? sky_inv_user_input($_POST['list_name']) : '';
    $place_name = $_POST['place_name'] ? sky_inv_user_input($_POST['place_name']) : '';
    $amount = $_POST['amount'] ? sky_inv_user_input($_POST['amount']) : '';
    $amount_left = $_POST['amount_left'] ? sky_inv_user_input($_POST['amount_left']) : '';
    $tot_amount = $_POST['tot_amount'] ? sky_inv_user_input($_POST['tot_amount']) : '';
    $post_id = $_POST['item_id'] ? sky_inv_user_input($_POST['item_id']) : '';
    $user_id = $_POST['user_id'] ? sky_inv_user_input($_POST['user_id']) : '';



    // $success = wp_send_json_success( 'It works' );
    $total_amount = get_post_meta($post_id, '_amount-to-raise-from-investors', true);
    $remaining_amount = $total_amount - $amount;



    $data = [
        'user_id' => "$user_id",
        'list_id' => "$post_id",
        'fname' => "$fname",
        'lname' => "$lname",
        'email' => "$email",
        'listname' => "$listing_name",
        'placename' => "$place_name",
        'totamount' => "$tot_amount",
        'amount' => "$amount",
        'amountleft' => "$remaining_amount",
        'submitted_at' => current_time('mysql'),
        'status' => 'New',
    ];

    $format = [
        '%d',
        '%d',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%d',
        '%d',
        '%d',
        '%s',
        '%s',
    ];

    $user_alert = '';
    $user_alert = 'Congratulation! You have successfully invested';
    $insert = true;

    $email_template = file_get_contents( dirname( __FILE__ ) . '/email-template/email-inlined.php' );

    if (0 == $amount_left) {
        echo 'Sorry, this project has already been invested. Try another project.';
        return;
    } elseif ($amount > $amount_left) {
        echo 'Sorry, your amount exceeds the project remaining amount. Try lower than left amount show in the project';
        return;
    } else {
        $inserted = $wpdb->insert($table, $data, $format);

        if ($inserted == 1) {
            update_post_meta($post_id, '_amount-to-raise-from-investors', $remaining_amount);

            $mail_admin = wp_mail(get_option('admin_email'), 'New investor in' . $listing_name, 'Check admin dashboard for details');
            $mail_investor = wp_mail($email, 'Investment in ' . $listing_name, $email_template );
            $mail_builder = wp_mail(get_the_author_meta('user_email', $post_id), 'Investment in ' . $listing_name, 'Thank you for your investment, we will contact you for later information');

            if ($mail_admin == 1) {
                echo json_encode(['Mail sent to admin' => 'success']);
            }

            if ($mail_investor == 1) {
                echo json_encode(['Mail sent to investor' => 'success']);
            }

            if ($mail_builder == 1) {
                echo json_encode(['Mail sent to builder' => 'success']);
            }

            echo json_encode(['status' => 'success', 'message' => $user_alert]);
        } else {
            echo json_encode(['status' => 'fail', 'message' => $user_alert]);
        }
    }


    die();
}


/**
 * Utility function 
 * @return anyfield
 */

function sky_get_our_own_data( $uid, $lid, $field ){

    global $wpdb;

    $table = $wpdb->prefix . 'sky_investment_user_info';
    $sql = "SELECT $field FROM $table WHERE user_id = '$uid' and list_id = '$lid'";

    return $wpdb->query($sql);
}


/**
 * Validate sky investment form
 *
 * @param mixed $data
 * @return validated data
 */

function sky_inv_user_input( $data ){
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}



/**
 * Update user data 
 * From frontend as a investor 
 */

 add_action("wp_ajax_sky_update_item", "sky_invest_item_form_cb");
 add_action("wp_ajax_nopriv_sky_update_item", "sky_invest_item_form_cb");

 function sky_invest_item_form_cb( ) {
    global $wpdb;
    $table = $wpdb->prefix . 'sky_investment_user_info';
    $post_id = isset( $_POST['inv_item_ID'] ) ? $_POST['inv_item_ID'] : 0;

    
    $data = [
        'status' => 'Withdrawn',
    ];

    $update_table = $wpdb->update($table, $data, ['id' => $post_id]);

    echo $post_id;

    if ($update_table) {
        echo json_encode(['success' => true, 'message' => 'Investment Widthdrawn']);
        $wpdb->flush();
    } else {
        echo json_encode(['success' => false, 'message' => 'Investment could not be Widthdrawn']);
    }

    wp_die();
 }