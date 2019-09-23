<?php
/* * ************************************************

 * Nochex Buy Now button shortcode handler

 * *********************************************** */

add_filter('swpm_payment_button_shortcode_for_nochex_buy_now', 'swpm_render_nochex_buy_now_button_sc_output', 10, 2);
 

function swpm_render_nochex_buy_now_button_sc_output($button_code, $args) {
  global $wpdb;
    $button_id = isset($args['id']) ? $args['id'] : '';

    if (empty($button_id)) {

        return '<p class="swpm-red-box">Error! swpm_render_nochex_buy_now_button_sc_output() function requires the button ID value to be passed to it.</p>';

    }

    //Check new_window parameter

    $window_target = isset($args['new_window']) ? 'target="_blank"' : '';

    $settings = SwpmSettings::get_instance();

    $button_cpt = get_post($button_id); //Retrieve the CPT for this button

    $membership_level_id = get_post_meta($button_id, 'membership_level_id', true);

    //Verify that this membership level exists (to prevent user paying for a level that has been deleted)

    if(!SwpmUtils::membership_level_id_exists($membership_level_id)){

        return '<p class="swpm-red-box">Error! The membership level specified in this button does not exist. You may have deleted this membership level. Edit the button and use the correct membership level.</p>';

    }
    
    $Nochex_email = get_post_meta($button_id, 'nochex_email', true);
	$membership_type = get_post_meta($button_id, 'membership_type', true);
	
    $payment_amount = get_post_meta($button_id, 'payment_amount', true);

    if (!is_numeric($payment_amount)) {
		
		
        return '<p class="swpm-red-box">Error! The payment amount value of the button must be a numeric number. Example: 49.50 </p>';
		
    }

    $payment_amount = round($payment_amount, 2); //round the amount to 2 decimal place.  

    $payment_currency = get_post_meta($button_id, 'payment_currency', true);

    $sandbox_enabled = $settings->get_value('enable-sandbox-testing');
    $notify_url = SIMPLE_WP_MEMBERSHIP_SITE_HOME_URL . '/?swpm_process_nochex_buy_now=n1';
    $return_url = get_post_meta($button_id, 'return_url', true);

    if (empty($return_url)) {
        $return_url = SIMPLE_WP_MEMBERSHIP_SITE_HOME_URL;
    }

    $cancel_url = SIMPLE_WP_MEMBERSHIP_SITE_HOME_URL;
    $user_ip = SwpmUtils::get_user_ip_address();
    $_SESSION['swpm_payment_button_interaction'] = $user_ip;


    //Custom field data

$subDay = "";

    $custom_field_value = 'subsc_ref=' . $membership_level_id;    
    $custom_field_value .= '&user_ip=' . $user_ip;

if (SwpmMemberUtils::is_member_logged_in()) {

		$permission = SwpmPermission::get_instance($membership_level_id);
		$subDay = $permission->get('subscription_period');

		$permLevel = SwpmUtils::get_formatted_expiry_date($member->subscription_starts,$permission->get('subscription_period'), $permission->get('subscription_duration_type'));
		$expiry_timestamp = SwpmMemberUtils::get_expiry_date_timestamp_by_user_id($member_id);
		$member_id = SwpmMemberUtils::get_logged_in_members_id();
        $custom_field_value .= '&swpm_id=' . $member_id;
        $member_first_name = SwpmMemberUtils::get_member_field_by_id($member_id, 'first_name');
        $member_last_name = SwpmMemberUtils::get_member_field_by_id($member_id, 'last_name');
     
        $member_email = SwpmMemberUtils::get_member_field_by_id($member_id, 'email');

		$memberExp = SwpmUtils::get_expiration_timestamp($member_id);

    }else{
		$member_id = '';
		$custom_field_value .= 'swpm_id=';
		$member_first_name = '';
        $member_last_name = '';
        $member_email = '';
	}

   $member_full_name = $member_first_name . ", " . $member_last_name;
   
$all_level_ids = SwpmUtils::get_membership_level_row_by_id($membership_level_id); 

if ( is_numeric($all_level_ids->subscription_period)){
$subscriptionPeriod = SwpmUtils::calculate_subscription_period_days($all_level_ids->subscription_period, $all_level_ids->subscription_duration_type);
}else{
list($y, $m, $d) = explode("-", $all_level_ids->subscription_period);
/**/
if(checkdate($m, $d, $y)){
 
$start_date = date("Y-m-d");
$end_date = $all_level_ids->subscription_period;

$subscriptionPeriod = Get_Date_Difference($start_date, $end_date);

}else{

$subscriptionPeriod = SwpmUtils::calculate_subscription_period_days($all_level_ids->subscription_period, $all_level_ids->subscription_duration_type);

}
}

$subscriptionExpiryDate = SwpmUtils::get_formatted_expiry_date(date("Y-m-d H:i:s"),$all_level_ids->subscription_period, $all_level_ids->subscription_duration_type);

if($all_level_ids->subscription_duration_type == 1){
	$subsPeriod = "Days";
}else if($all_level_ids->subscription_duration_type == 2){
	$subsPeriod = "Weeks";
}else if($all_level_ids->subscription_duration_type == 3){
	$subsPeriod = "Months";
}else if($all_level_ids->subscription_duration_type == 4){
	$subsPeriod = "Years";
}else if($all_level_ids->subscription_duration_type == 5){
	$subsPeriod = "";
}

$xmlCollection = "<items><item><id></id><name>".$all_level_ids->alias."</name><description>Membership Duration: ".$all_level_ids->subscription_period." ".$subsPeriod.", Membership Expiration (if bought today): ".$subscriptionExpiryDate."</description><quantity>1</quantity><price>".$payment_amount."</price></item></items>";

$member = SwpmMemberUtils::get_user_by_id($member_id);

 $showInfo = "none";
 $Title = "Membership Details";
 $renewMsg = "";
  if (SwpmMemberUtils::is_member_logged_in()) {
  
   $Title = "Membership Details - Do you want an upgrade?";
  
 foreach ($all_level_ids as $key => $value){ 
 if($key == "id" AND $value == $member->membership_level){

if($member->account_state == "active"){
	$displayNoneForm = "style='display:none;'";
	$showInfo = "block";
	$dispMsg = "You are already a member";
	$Title = "Membership Details";
}else{ 
	$renewMsg = ",<br/>Renew or try an upgraded membership";
	$dispMsg = "Your Membership has expired";
	$displayNoneForm = "style='display:block;'";
	$showInfo = "block";
}

}

}
$subStart = $member->subscription_starts;

}else{
 
	$subStart = "";
	$permLevel = "";
	$dispMsg = ""; 
	$displayNoneForm = "style='display:block;'";
	$showInfo = "none";
}

$aliasLevel = $all_level_ids->alias;

    $output = '<style>input + button, input + input[type="button"], input + input[type="submit"] { padding: 0px;background-color: #fff;}</style>';
    $output .= '<div class="swpm-button-wrapper swpm-pp-buy-now-wrapper" style="max-width:445px;">';	
    $output .= '<h3 style="color:#08c;"><b>'.$Title.'</b></h3>';	
    $output .= '<ul style="list-style:none">
				<li><b>Amount</b>: &#163;'.$payment_amount.'</li>
				<li><b>Membership</b>: '.$aliasLevel.' </li> 
				<li><b>Membership Expiration</b>: '.$subscriptionExpiryDate.'</li>
				</ul>';	
	$output .= '<div style="padding:10px; background:#fafafa;border:1px solid #eee;display:'.$showInfo.'">
				<h3 style="color:#08c;"><b>'.$dispMsg.'</b>'.$renewMsg.'</h3>
				<ul style="list-style:none">
				<li>Membership Duration: '.$subDay.' Days</li>
				<li>Your Membership started: '.$subStart.'</li>
				<li>Your Membership expires: '.$permLevel.'</li>
				</ul>
				</div>';
    $output .= '<script>
				function checkValue(inPut){				
				if(inPut.value == ""){
				document.getElementById(inPut.id).style.border="1px solid red";
				}else{
				document.getElementById(inPut.id).style.border="1px solid green";				
				}				
				}
				
				function checkNotEmpty(idLevel){
				
				if (document.getElementById("billing_fullname"+idLevel).value == ""){	
								element = document.getElementById("billing_fullname"+idLevel);
								element.style.border = "red 0.5px solid";
								return false;
				}else{
								element = document.getElementById("billing_fullname"+idLevel);
								element.style.border = "green 0.5px solid";
				}
				
				if(document.getElementById("email_address"+idLevel).value == ""){
								element = document.getElementById("email_address"+idLevel);
								element.style.border = "red 0.5px solid";
								return false;
				}else{
								element = document.getElementById("email_address"+idLevel);
								element.style.border = "green 0.5px solid";
				}
				
				return true; 
				
				}
				</script>
				<form action="https://secure.nochex.com/default.aspx"'.$displayNoneForm.' onsubmit="return checkNotEmpty('.$membership_level_id.');" method="post" ' . $window_target . '>';
    $output .= '<input type="hidden" name="merchant_id" value="' . $Nochex_email . '" />';	
    $output .= '<input type="hidden" name="optional_2" value="' . $membership_level_id . '" />';
    $output .= '<input type="hidden" name="amount" value="' . $payment_amount . '" />';
    $output .= '<input type="hidden" name="xml_item_collection" value="' . $xmlCollection . '" />';
	
	if (SwpmMemberUtils::is_member_logged_in()) {	 
			$output .= '<script>document.getElementById("nonMember").style.display = "none";</script>';
		if($membership_type == 1){
			$output .= '<input type="hidden" name="optional_3" value="Renewal" /><br/>';
		}else{
			$output .= '<input type="hidden" name="optional_3" value="New" /><br/>';
		}
	}
	    
	if($member_full_name == "" or $member_email == ""){
		$output .= '<div><span style="width: 15%;text-align: left;min-width: 200px;float: left;margin-right:10px;">Full Name</span><input id="billing_fullname'.$membership_level_id.'" type="text" name="billing_fullname" value="" onchange="checkValue(this);" placeholder="e.g. John Smith" /></div><br/>';
		$output .= '<div><span style="width: 15%;text-align: left;min-width: 200px;float: left;margin-right:10px;">Email Address</span><input id="email_address'.$membership_level_id.'" type="text" name="email_address" value="" onchange="checkValue(this);" placeholder="e.g. JaneDoe@hotmail.co.uk" /></div><br/>';
	}else{
		$output .= '<input type="hidden" id="billing_fullname" name="billing_fullname" value="'.$member_full_name.'" />';
		$output .= '<input type="hidden" id="email_address" name="email_address" value="'.$member_email.'"  />';
	}
	
	$output .= '<input type="hidden" name="optional_4" value="'.$all_level_ids->alias.'" />';
	$output .= '<input type="hidden" name="customer_phone_number" value="" />';	
    $output .= '<input type="hidden" name="billing_address" value="" />';
    $output .= '<input type="hidden" name="billing_city" value="" />';
    $output .= '<input type="hidden" name="billing_postcode" value="" />';    
    $output .= '<input type="hidden" name="callback_url" value="' . $notify_url . '" />';
    $output .= '<input type="hidden" name="success_url" value="' . $return_url . '" />';
    $output .= '<input type="hidden" name="cancel_return" value="' . $cancel_url . '" />';
    $custom_field_value = urlencode($custom_field_value);//URL encode the custom field value so nothing gets lost when it is passed around.
	 
	$output .= '<input type="hidden" name="optional_1" value="' . $custom_field_value . '" />';

    $output .= apply_filters('swpm_nochex_payment_form_additional_fields', '');  
    $button_image_url = get_post_meta($button_id, 'button_image_url', true);
    $button_custom_url = get_post_meta($button_id, 'customBtn', true);
	
	if($button_image_url == "custom"){
		if($button_custom_url == ""){
		$output .= '<input type="submit" class="fusion-button button-flat button-round button-medium button-blue button-8 btn btn-lg btn-primary pull-right" value="' . $button_text . '" />';
		}else{
		$output .= '<button type="submit"><img src="'.$button_custom_url.'" /></button>';
		}
	}else{
		$output .= '<button type="submit"><img src="'.$button_image_url.'" /></button>';
	}

    $output .= '</form>'; //End .form
    $output .= '</div><br style="clear:both;" />'; //End .swpm_button_wrapper

    return $output;



}

 
 function Get_Date_Difference($start_date, $end_date)
    {
        $diff = abs(strtotime($end_date) - strtotime($start_date));
        $years = floor($diff / (365*60*60*24));
        $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
        $days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
        return $months.' Month '.$days.' Days';
    }
