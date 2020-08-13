<?php

include(SIMPLE_WP_MEMBERSHIP_PATH . 'ipn/swpm_handle_subsc_ipn.php');

if ($_POST) {
if (isset($_POST["optional_2"])) {

// Get the POST information from Nochex server
$postvars = http_build_query($_POST);

// Set parameters for the email
$to = $_POST["email_address"];

ini_set("SMTP","mail.nochex.com" ); 
$header = "From: " . $_POST["merchant_id"];

// Set parameters for the email
	$url = "https://secure.nochex.com/callback/callback.aspx";

		$ch = curl_init(); // Initialise the curl tranfer
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_VERBOSE, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postvars); // Set POST fields
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		$output = curl_exec($ch); // Post back
		curl_close($ch);
		
// Put the variables in a printable format for the email
$debug = "IP -> " . $_SERVER['REMOTE_ADDR'] ."\r\n\r\n<br/>POST DATA:\r\n"; 
foreach($_POST as $Index => $Value) 
$debug .= "".$Index ."->". $Value."\r\n<br/>"; 
$debug .= "\r\n<br/>RESPONSE:\r\n$output";

//If statement
if ($output == "AUTHORISED") {  // searches response to see if AUTHORISED is present if it isn’t a failure message is displayed
    $msg = "APC was AUTHORISED.\r\n\r\n$debug"; // if AUTHORISED was found in the response then it was successful
} else { 
	  $msg = "APC was not AUTHORISED.\r\n\r\n$debug";  // displays debug message
}

           //Grab the transaction ID.
            $txn_id = $_POST["transaction_id"];//$charge->balance_transaction;
			
            //Create the $ipn_data array.			
			$membershipLevelID = $_POST["optional_2"];
			$renewal = $_POST["optional_3"];
			$membershipRef = $_POST["optional_4"];
			
			if($renewal == "Renewal"){
			$swpm_id = $membershipRef;
			}else{
			$custom = urldecode($_POST["optional_1"]);
			$customvariables = SwpmTransactions::parse_custom_var($custom);
            $swpm_id = isset($customvariables['swpm_id'])? $customvariables['swpm_id']: ''; 
			}
			
			$subsc_ref = $customvariables['subsc_ref'];
			
            $ipn_data = array();

            $ipn_data['mc_gross'] = $_POST["gross_amount"];

			$arr = explode(' ',trim($_POST["billing_fullname"]));

			$ipn_data['first_name'] = sanitize_text_field($arr[0]);
            $ipn_data['last_name'] = sanitize_text_field($arr[1]); 

            $ipn_data['payer_email'] = sanitize_text_field($_REQUEST['email_address']);
            $ipn_data['membership_level'] = $membershipRef;
            $ipn_data['txn_id'] = $txn_id;
            $ipn_data['subscr_id'] = $txn_id;
			
			$ipn_data['member_id'] = $membershipLevelID;		
			$ipn_data['account-status'] = "Nochex";
            $ipn_data['user_name'] = str_replace(" ", "", $_POST["billing_fullname"]);
            $ipn_data['gateway'] = 'nochex';
            $ipn_data['status'] = $output;     
		
            $ipn_data['address_street'] = $_POST["billing_address"];
            /*$ipn_data['address_city'] = $_POST["billing_city"];*/
            $ipn_data['address_zipcode'] = $_POST["billing_postcode"];

            $ipn_data['renewMember'] = $renewal;
			
			$ipn_data['custom'] = array("reference"=>$membershipRef,"subsc_ref"=>$membershipLevelID, "swpm_id"=>$swpm_id);
			
            //Handle the membership signup related tasks.
            swpm_handle_subsc_signup_stand_alone($ipn_data,$membershipLevelID,$txn_id,$swpm_id);
        
            //Save the transaction record
            SwpmTransactions::save_txn_record($ipn_data);
            SwpmLog::log_simple_debug('Transaction data saved.', true);


        $swpm_user = SwpmMemberUtils::get_user_by_user_name($ipn_data['user_name']);
        $swpm_id = $swpm_user->member_id;
        if (!empty($swpm_id)) {
            $password_hash = SwpmUtils::encrypt_password("nochex");
            global $wpdb;
            $wpdb->update($wpdb->prefix . "swpm_members_tbl", array('password' => $password_hash), array('member_id' => $swpm_id));
        }
    }
 }