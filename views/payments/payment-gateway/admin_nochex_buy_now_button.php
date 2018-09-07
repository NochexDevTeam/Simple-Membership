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

                                <option value="0">New</option>
								
								<option value="1">Renewal</option>

                            </select>

                            <p class="description">Select the membership type</p>

                        </td>

                    </tr>


                    <tr valign="top">

                        <th scope="row"><?php echo SwpmUtils::_('Payment Amount'); ?></th>

                        <td>

                            <input type="text" size="6" name="payment_amount" value="" required />

                            <p class="description">Enter payment amount. Example values: 10.00 or 19.50 or 299.95 etc <b>(do not put currency symbol)</b>.</p>

                        </td>

                    </tr>



                    <tr valign="top">

                        <th scope="row"><?php echo SwpmUtils::_('Payment Currency'); ?></th>

                        <td>

                            <select id="payment_currency" name="payment_currency">                               
                                <option selected="selected" value="GBP">Pounds Sterling (GBP)</option>
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

                        <th scope="row"><?php echo SwpmUtils::_('Nochex Email Address'); ?></th>

                        <td>

                            <input type="text" size="50" name="nochex_email" value="" required />

                            <p class="description">Enter your Nochex email address or merchant id.</p>

                        </td>

                    </tr>                    



                    <tr valign="top">

                        <th scope="row"><?php echo SwpmUtils::_('Button Image URL'); ?></th>

                        <td>
							<select name="button_image_url" onchange="showNcxButton(this)">							
								<option value="https://ssl.nochex.com/Downloads/Nochex Payment Button/nochex_pay.png"><h6>Option 1</h6></option>									
								<option value="https://ssl.nochex.com/Downloads/Nochex Payment Button/nochex_checkout.png"><h6>Option 2</h6></option>	
								<option value="custom"><h6>Custom Option</h6></option>	
							</select><br/><br/>
                            <!---->
							<script>
								function showNcxButton(val){	
									if(val.value == "custom"){
										document.getElementById("preNcxBtn").style = "display:none";
										document.getElementById("customBtn").style = "display:block";
									}else{
										document.getElementById("preNcxBtn").style = "display:block";
										document.getElementById("customBtn").style = "display:none";
										document.getElementById("preNcxBtn").src = val.value;
									}
								}
							</script>
							<img src="https://ssl.nochex.com/Downloads/Nochex Payment Button/payme4.gif" alt="" id="preNcxBtn" />
							
							<input type="text" size="100" name="customBtn" id="customBtn" style="display:none;" />
							
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
		
        add_post_meta($button_id, 'customBtn', trim(sanitize_text_field($_REQUEST['customBtn'])));

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
	
    $customBtn = get_post_meta($button_id, 'customBtn', true);
		
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
								if($membership_type == 0){  
							 ?>

                                <option selected="selected" value="0">New</option>
								
								<option value="1">Renewal</option>

							<?php
							
								}else{
								
							?>
                                <option value="0">New</option>
								
								<option selected="selected" value="1">Renewal</option>

							 <?php								 
								 }								 
							 ?> 
								
                            </select>

                            <p class="description">Select the membership level this payment button is for.</p>

                        </td>

                    </tr>

                    <tr valign="top">

                        <th scope="row"><?php echo SwpmUtils::_('Payment Amount'); ?></th>

                        <td>

                            <input type="text" size="6" name="payment_amount" value="<?php echo $payment_amount; ?>" required />

                            <p class="description">Enter payment amount. Example values: 10.00 or 19.50 or 299.95 etc <B>(do not put currency symbol)</B>.</p>

                        </td>

                    </tr>



                    <tr valign="top">

                        <th scope="row"><?php echo SwpmUtils::_('Payment Currency'); ?></th>

                        <td>                            

                            <select id="payment_currency" name="payment_currency">

                                <option value="GBP" <?php echo ($payment_currency == 'GBP') ? 'selected="selected"' : ''; ?>>Pounds Sterling (GBP)</option>

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

                        <th scope="row"><?php echo SwpmUtils::_('Nochex Email Address'); ?></th>

                        <td>

                            <input type="text" size="50" name="nochex_email" value="<?php echo $nochex_email; ?>" required />

                            <p class="description">Enter your nochex email address or merchant id.</p>

                        </td>

                    </tr>                    
 
                    <tr valign="top">

                        <th scope="row"><?php echo SwpmUtils::_('Button Image URL'); ?></th>

                        <td>
							<select name="button_image_url" onchange="showNcxButton(this)">		

								<?php 

								if($button_image_url == "https://ssl.nochex.com/Downloads/Nochex Payment Button/nochex_pay.png"){
								
								?>
								
								<option value="https://ssl.nochex.com/Downloads/Nochex Payment Button/nochex_pay.png" selected="selected"><h6>Option 1</h6></option>									
								<option value="https://ssl.nochex.com/Downloads/Nochex Payment Button/nochex_checkout.png"><h6>Option 2</h6></option>	
								<option value="custom"><h6>Custom Option</h6></option>	
								
								<?php 
								
								}else if($button_image_url == "https://ssl.nochex.com/Downloads/Nochex Payment Button/nochex_checkout.png"){
								
								?>
								
								<option value="https://ssl.nochex.com/Downloads/Nochex Payment Button/nochex_pay.png"><h6>Option 1</h6></option>									
								<option value="https://ssl.nochex.com/Downloads/Nochex Payment Button/nochex_checkout.png" selected="selected"><h6>Option 2</h6></option>	
								<option value="custom"><h6>Custom Option</h6></option>	
								
								<?php 
								
								}else{
								
								?>
								
								<option value="https://ssl.nochex.com/Downloads/Nochex Payment Button/nochex_pay.png"><h6>Option 1</h6></option>									
								<option value="https://ssl.nochex.com/Downloads/Nochex Payment Button/nochex_checkout.png"><h6>Option 2</h6></option>	
								<option value="custom" selected="selected"><h6>Custom Option</h6></option>	
								
								<?php 
								}
								 
								?>
							

							</select><br/><br/>
                            <!---->
							<script>
								window.onload = function(){								
									showNcxButtonCurr(<?php echo '"' . $button_image_url . '","' . $customBtn . '"'; ?>);
								};
								
								function showNcxButtonCurr(val, cust){	
									if(val == "custom"){
										document.getElementById("preNcxBtn").style = "display:none";
										document.getElementById("customBtn").style = "display:block";
										document.getElementById("customBtn").value = cust;
									}else{
										document.getElementById("preNcxBtn").style = "display:block";
										document.getElementById("customBtn").style = "display:none";
										document.getElementById("preNcxBtn").src = val;
									}
								}
							
								function showNcxButton(val){	
									if(val.value == "custom"){
										document.getElementById("preNcxBtn").style = "display:none";
										document.getElementById("customBtn").style = "display:block";
									}else{
										document.getElementById("preNcxBtn").style = "display:block";
										document.getElementById("customBtn").style = "display:none";
										document.getElementById("preNcxBtn").src = val.value;
									}
								}
							</script>
							<img src="https://ssl.nochex.com/Downloads/Nochex Payment Button/payme4.gif" alt="" id="preNcxBtn" />
							
							<input type="text" size="100" name="customBtn" id="customBtn" style="display:none;" />
							
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
		
        update_post_meta($button_id, 'customBtn', trim(sanitize_text_field($_REQUEST['customBtn'])));
 
        echo '<div id="message" class="updated fade"><p>Payment button data successfully updated!</p></div>';

    }

}



/************************************************************************

 * End of edit nochex Buy now payment button stuff

 ************************************************************************/