<?php

/* * ***************************************************************

 * Render the new nochex Buy now payment button creation interface

 * ************************************************************** */

add_action('swpm_create_new_button_for_nochex_buy_now', 'swpm_create_new_nochex_buy_now_button');



function swpm_create_new_nochex_buy_now_button() {

    ?>
	
    <div class="postbox">

        <h3 class="hndle"><label for="title"><?php echo SwpmUtils::_('Nochex - Buy Now Button Configuration'); ?></label></h3>

        <div class="inside">



            <form id="pp_button_config_form" method="post">

                <input type="hidden" name="button_type" value="<?php echo sanitize_text_field($_REQUEST['button_type']); ?>">

                <input type="hidden" name="swpm_button_type_selected" value="1">



                <table class="form-table" width="100%" border="0" cellspacing="0" cellpadding="6">



                    <tr valign="top">

                        <th scope="row"><?php echo SwpmUtils::_('Button Title'); ?></th>

                        <td>

                            <input type="text" size="50" name="button_name" value="" required />

                            <p class="description">Give this membership payment button a name related to the membership. Example: Gold membership payment</p>

                        </td>

                    </tr>



                    <tr valign="top">

                        <th scope="row"><?php echo SwpmUtils::_('Membership Level'); ?></th>

                        <td>

                            <select id="membership_level_id" name="membership_level_id">

                                <?php echo SwpmUtils::membership_level_dropdown(); ?>

                            </select>

                            <p class="description">Select the membership level this payment button is for.</p>

                        </td>

                    </tr>

					<tr valign="top">

                        <th scope="row"><?php echo SwpmUtils::_('Membership Type'); ?></th>

                        <td>

                            <select id="membership_type" name="membership_type">

                                <option selected="selected" value="0">New</option>
								
								<option selected="selected" value="1">Renewal</option>

                            </select>

                            <p class="description">Select the membership type</p>

                        </td>

                    </tr>


                    <tr valign="top">

                        <th scope="row"><?php echo SwpmUtils::_('Payment Amount'); ?></th>

                        <td>

                            <input type="text" size="6" name="payment_amount" value="" required />

                            <p class="description">Enter payment amount. Example values: 10.00 or 19.50 or 299.95 etc (do not put currency symbol).</p>

                        </td>

                    </tr>



                    <tr valign="top">

                        <th scope="row"><?php echo SwpmUtils::_('Payment Currency'); ?></th>

                        <td>

                            <select id="payment_currency" name="payment_currency">                               
                                <option selected="selected" value="GBP">Pounds Sterling (£)</option>
                            </select>

                        </td>

                    </tr>



                    <tr valign="top">

                        <th scope="row"><?php echo SwpmUtils::_('Return URL'); ?></th>

                        <td>

                            <input type="text" size="100" name="return_url" value="" />

                            <p class="description">This is the URL the user will be redirected to after a successful payment. Enter the URL of your Thank You page here.</p>

                        </td>

                    </tr>



                    <tr valign="top">

                        <th scope="row"><?php echo SwpmUtils::_('Nochex Email Address or Merchant ID'); ?></th>

                        <td>

                            <input type="text" size="50" name="nochex_email" value="" required />

                            <p class="description">Enter your Nochex email address or merchant id.</p>

                        </td>

                    </tr>                    



                    <tr valign="top">

                        <th scope="row"><?php echo SwpmUtils::_('Button Image URL'); ?></th>

                        <td>

                            <input type="text" size="100" name="button_image_url" value="" />

                            <p class="description">If you want to customize the look of the button using an image then enter the URL of the image.</p>
							
                        </td>

                    </tr> 



                </table>



                <p class="submit">

                    <input type="submit" name="swpm_nochex_buy_now_save_submit" class="button-primary" value="<?php echo SwpmUtils::_('Save Payment Data'); ?>" >

                </p>



            </form>



        </div>

    </div>

    <?php

}



/*

 * Process submission and save the new nochex Buy now payment button data

 */

add_action('swpm_create_new_button_process_submission', 'swpm_save_new_nochex_buy_now_button_data');



function swpm_save_new_nochex_buy_now_button_data() {

    if (isset($_REQUEST['swpm_nochex_buy_now_save_submit'])) {

        //This is a nochex buy now button save event. Process the submission.

        //TODO - Do some extra validation check?

        

        //Save the button data

        $button_id = wp_insert_post(

                array(

                    'post_title' => sanitize_text_field($_REQUEST['button_name']),

                    'post_type' => 'swpm_payment_button',

                    'post_content' => '',

                    'post_status' => 'publish'

                )

        );



        $button_type = sanitize_text_field($_REQUEST['button_type']);

        add_post_meta($button_id, 'button_type', $button_type);

        add_post_meta($button_id, 'membership_level_id', sanitize_text_field($_REQUEST['membership_level_id']));
		
        add_post_meta($button_id, 'membership_type', sanitize_text_field($_REQUEST['membership_type']));

        add_post_meta($button_id, 'payment_amount', trim(sanitize_text_field($_REQUEST['payment_amount'])));

        add_post_meta($button_id, 'payment_currency', sanitize_text_field($_REQUEST['payment_currency']));

        add_post_meta($button_id, 'return_url', trim(sanitize_text_field($_REQUEST['return_url'])));

        add_post_meta($button_id, 'nochex_email', sanitize_text_field($_REQUEST['nochex_email']));

        add_post_meta($button_id, 'button_image_url', trim(sanitize_text_field($_REQUEST['button_image_url'])));



        //Redirect to the edit interface of this button with $button_id        

        //$url = admin_url() . 'admin.php?page=simple_wp_membership_payments&tab=edit_button&button_id=' . $button_id . '&button_type=' . $button_type;

        //Redirect to the manage payment buttons interface

        $url = admin_url() . 'admin.php?page=simple_wp_membership_payments&tab=payment_buttons';

        SwpmMiscUtils::redirect_to_url($url);

    }

}



/* * **********************************************************************

 * End of new nochex Buy now payment button stuff

 * ********************************************************************** */





/* * ***************************************************************

 * Render edit nochex Buy now payment button interface

 * ************************************************************** */

add_action('swpm_edit_payment_button_for_nochex_buy_now', 'swpm_edit_nochex_buy_now_button');


function swpm_edit_nochex_buy_now_button() {



    //Retrieve the payment button data and present it for editing.    



    $button_id = sanitize_text_field($_REQUEST['button_id']);

    $button_id = absint($button_id);

    $button_type = sanitize_text_field($_REQUEST['button_type']);



    $button = get_post($button_id); //Retrieve the CPT for this button


    $membership_type = get_post_meta($button_id, 'membership_type', true);
	
    $membership_level_id = get_post_meta($button_id, 'membership_level_id', true);

    $payment_amount = get_post_meta($button_id, 'payment_amount', true);

    $payment_currency = get_post_meta($button_id, 'payment_currency', true);

    $return_url = get_post_meta($button_id, 'return_url', true);

    $nochex_email = get_post_meta($button_id, 'nochex_email', true);

    $button_image_url = get_post_meta($button_id, 'button_image_url', true);

    ?>

    <div class="postbox">

        <h3 class="hndle"><label for="title"><?php echo SwpmUtils::_('nochex Buy Now Button Configuration'); ?></label></h3>

        <div class="inside">



            <form id="pp_button_config_form" method="post">

                <input type="hidden" name="button_type" value="<?php echo $button_type; ?>">



                <table class="form-table" width="100%" border="0" cellspacing="0" cellpadding="6">



                    <tr valign="top">

                        <th scope="row"><?php echo SwpmUtils::_('Button ID'); ?></th>

                        <td>

                            <input type="text" size="10" name="button_id" value="<?php echo $button_id; ?>" readonly required />

                            <p class="description">This is the ID of this payment button. It is automatically generated for you and it cannot be changed.</p>

                        </td>

                    </tr>



                    <tr valign="top">

                        <th scope="row"><?php echo SwpmUtils::_('Button Title'); ?></th>

                        <td>

                            <input type="text" size="50" name="button_name" value="<?php echo $button->post_title; ?>" required />

                            <p class="description">Give this membership payment button a name. Example: Gold membership payment</p>

                        </td>

                    </tr>



                    <tr valign="top">

                        <th scope="row"><?php echo SwpmUtils::_('Membership Level'); ?></th>

                        <td>

                            <select id="membership_level_id" name="membership_level_id">

                                <?php echo SwpmUtils::membership_level_dropdown($membership_level_id); ?>

                            </select>

                            <p class="description">Select the membership level this payment button is for.</p>

                        </td>

                    </tr>

				<tr valign="top">

                        <th scope="row"><?php echo SwpmUtils::_('Membership Type'); ?></th>

                        <td>

                            <select id="membership_type" name="membership_type">
								
							<?php 

								 if($membership_type == 1){
								 
							 ?>
                                <option selected="selected" value="0">New</option>
								
								<option selected="selected" value="1" selected="selected">Renewal</option>

							<?php
							
								}else{
								
							?>
                                <option selected="selected" value="0" selected="selected">New</option>
								
								<option selected="selected" value="1">Renewal</option>

							 <?php								 
								 }								 
							 ?>
								
								
                                <!--?php echo SwpmUtils::membership_level_dropdown($membership_level_id); ?-->
								
                            </select>

                            <p class="description">Select the membership level this payment button is for.</p>

                        </td>

                    </tr>

                    <tr valign="top">

                        <th scope="row"><?php echo SwpmUtils::_('Payment Amount'); ?></th>

                        <td>

                            <input type="text" size="6" name="payment_amount" value="<?php echo $payment_amount; ?>" required />

                            <p class="description">Enter payment amount. Example values: 10.00 or 19.50 or 299.95 etc (do not put currency symbol).</p>

                        </td>

                    </tr>



                    <tr valign="top">

                        <th scope="row"><?php echo SwpmUtils::_('Payment Currency'); ?></th>

                        <td>                            

                            <select id="payment_currency" name="payment_currency">

                                <option value="USD" <?php echo ($payment_currency == 'USD') ? 'selected="selected"' : ''; ?>>US Dollars ($)</option>

                                <option value="EUR" <?php echo ($payment_currency == 'EUR') ? 'selected="selected"' : ''; ?>>Euros (€)</option>

                                <option value="GBP" <?php echo ($payment_currency == 'GBP') ? 'selected="selected"' : ''; ?>>Pounds Sterling (£)</option>

                                <option value="AUD" <?php echo ($payment_currency == 'AUD') ? 'selected="selected"' : ''; ?>>Australian Dollars ($)</option>

                                <option value="BRL" <?php echo ($payment_currency == 'BRL') ? 'selected="selected"' : ''; ?>>Brazilian Real (R$)</option>

                                <option value="CAD" <?php echo ($payment_currency == 'CAD') ? 'selected="selected"' : ''; ?>>Canadian Dollars ($)</option>

                                <option value="CNY" <?php echo ($payment_currency == 'CNY') ? 'selected="selected"' : ''; ?>>Chinese Yuan</option>

                                <option value="CZK" <?php echo ($payment_currency == 'CZK') ? 'selected="selected"' : ''; ?>>Czech Koruna</option>

                                <option value="DKK" <?php echo ($payment_currency == 'DKK') ? 'selected="selected"' : ''; ?>>Danish Krone</option>

                                <option value="HKD" <?php echo ($payment_currency == 'HKD') ? 'selected="selected"' : ''; ?>>Hong Kong Dollar ($)</option>

                                <option value="HUF" <?php echo ($payment_currency == 'HUF') ? 'selected="selected"' : ''; ?>>Hungarian Forint</option>

                                <option value="INR" <?php echo ($payment_currency == 'INR') ? 'selected="selected"' : ''; ?>>Indian Rupee</option>

                                <option value="IDR" <?php echo ($payment_currency == 'IDR') ? 'selected="selected"' : ''; ?>>Indonesia Rupiah</option>

                                <option value="ILS" <?php echo ($payment_currency == 'ILS') ? 'selected="selected"' : ''; ?>>Israeli Shekel</option>

                                <option value="JPY" <?php echo ($payment_currency == 'JPY') ? 'selected="selected"' : ''; ?>>Japanese Yen (¥)</option>

                                <option value="MYR" <?php echo ($payment_currency == 'MYR') ? 'selected="selected"' : ''; ?>>Malaysian Ringgits</option>

                                <option value="MXN" <?php echo ($payment_currency == 'MXN') ? 'selected="selected"' : ''; ?>>Mexican Peso ($)</option>

                                <option value="NZD" <?php echo ($payment_currency == 'NZD') ? 'selected="selected"' : ''; ?>>New Zealand Dollar ($)</option>

                                <option value="NOK" <?php echo ($payment_currency == 'NOK') ? 'selected="selected"' : ''; ?>>Norwegian Krone</option>

                                <option value="PHP" <?php echo ($payment_currency == 'PHP') ? 'selected="selected"' : ''; ?>>Philippine Pesos</option>

                                <option value="PLN" <?php echo ($payment_currency == 'PLN') ? 'selected="selected"' : ''; ?>>Polish Zloty</option>

                                <option value="SGD" <?php echo ($payment_currency == 'SGD') ? 'selected="selected"' : ''; ?>>Singapore Dollar ($)</option>

                                <option value="ZAR" <?php echo ($payment_currency == 'ZAR') ? 'selected="selected"' : ''; ?>>South African Rand (R)</option>

                                <option value="KRW" <?php echo ($payment_currency == 'KRW') ? 'selected="selected"' : ''; ?>>South Korean Won</option>

                                <option value="SEK" <?php echo ($payment_currency == 'SEK') ? 'selected="selected"' : ''; ?>>Swedish Krona</option>

                                <option value="CHF" <?php echo ($payment_currency == 'CHF') ? 'selected="selected"' : ''; ?>>Swiss Franc</option>

                                <option value="TWD" <?php echo ($payment_currency == 'TWD') ? 'selected="selected"' : ''; ?>>Taiwan New Dollars</option>

                                <option value="THB" <?php echo ($payment_currency == 'THB') ? 'selected="selected"' : ''; ?>>Thai Baht</option>

                                <option value="TRY" <?php echo ($payment_currency == 'TRY') ? 'selected="selected"' : ''; ?>>Turkish Lira</option>

                                <option value="VND" <?php echo ($payment_currency == 'VND') ? 'selected="selected"' : ''; ?>>Vietnamese Dong</option>

                            </select>

                            <p class="description">Select the currency for this payment button.</p>

                        </td>

                    </tr>



                    <tr valign="top">

                        <th scope="row"><?php echo SwpmUtils::_('Return URL'); ?></th>

                        <td>

                            <input type="text" size="100" name="return_url" value="<?php echo $return_url; ?>" />

                            <p class="description">This is the URL the user will be redirected to after a successful payment. Enter the URL of your Thank You page here.</p>

                        </td>

                    </tr>



                    <tr valign="top">

                        <th scope="row"><?php echo SwpmUtils::_('nochex Email'); ?></th>

                        <td>

                            <input type="text" size="50" name="nochex_email" value="<?php echo $nochex_email; ?>" required />

                            <p class="description">Enter your nochex email address. The payment will go to this nochex account.</p>

                        </td>

                    </tr>                    



                    <tr valign="top">

                        <th scope="row"><?php echo SwpmUtils::_('Button Image URL'); ?></th>

                        <td>

                            <input type="text" size="100" name="button_image_url" value="<?php echo $button_image_url; ?>" />

                            <p class="description">If you want to customize the look of the button using an image then enter the URL of the image.</p>

                        </td>

                    </tr> 



                </table>



                <p class="submit">

                    <input type="submit" name="swpm_nochex_buy_now_edit_submit" class="button-primary" value="<?php echo SwpmUtils::_('Save Payment Data'); ?>" >

                </p>



            </form>



        </div>

    </div>

    <?php

}



/*

 * Process submission and save the edited nochex Buy now payment button data

 */

add_action('swpm_edit_payment_button_process_submission', 'swpm_edit_nochex_buy_now_button_data');



function swpm_edit_nochex_buy_now_button_data() {

    if (isset($_REQUEST['swpm_nochex_buy_now_edit_submit'])) {

        //This is a nochex buy now button edit event. Process the submission.

        

        //Update and Save the edited payment button data

        $button_id = sanitize_text_field($_REQUEST['button_id']);

        $button_id = absint($button_id);

        $button_type = sanitize_text_field($_REQUEST['button_type']);

        $button_name = sanitize_text_field($_REQUEST['button_name']);



        $button_post = array(

            'ID' => $button_id,

            'post_title' => $button_name,

            'post_type' => 'swpm_payment_button',

        );

        wp_update_post($button_post);

        update_post_meta($button_id, 'button_type', $button_type);

        update_post_meta($button_id, 'membership_level_id', sanitize_text_field($_REQUEST['membership_level_id']));
		
        update_post_meta($button_id, 'membership_type', sanitize_text_field($_REQUEST['membership_type']));

        update_post_meta($button_id, 'payment_amount', trim(sanitize_text_field($_REQUEST['payment_amount'])));

        update_post_meta($button_id, 'payment_currency', sanitize_text_field($_REQUEST['payment_currency']));

        update_post_meta($button_id, 'return_url', trim(sanitize_text_field($_REQUEST['return_url'])));

        update_post_meta($button_id, 'nochex_email', trim(sanitize_text_field($_REQUEST['nochex_email'])));

        update_post_meta($button_id, 'button_image_url', trim(sanitize_text_field($_REQUEST['button_image_url'])));

        echo '<div id="message" class="updated fade"><p>Payment button data successfully updated!</p></div>';

    }

}



/************************************************************************

 * End of edit nochex Buy now payment button stuff

 ************************************************************************/