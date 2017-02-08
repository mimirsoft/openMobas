<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
require_once("../../../framework_masterinclude.php");
require_once("cv_master_include.php");
	
$FRAMEWORK->authenticate($objSession, $MODULE_NAME, $USER, $BASE_DIR);

if(!Rbac_User::IsAllowedTo($USER->GetUserID(), "access_module", "cv_master_module"))
{
    echo "Permission access Customer/Vendor Module";
    exit;
}

$ACTION =      '';
$cv_id = '';
$firstname = '';
$lastname = '';
$WARNING['show'] = false;
$startdate_year = date('Y');
$startdate_year = date('Y', strtotime("-1 month"));
$startdate_month = date('m', strtotime("-1 month"));
//$first_only = true;  why is this here?
$startdate_day = date('d');
$enddate_year = date('Y');
$enddate_month = date('m');
$enddate_day = date('d');

if(isset($_GET['cv_id']))
{
    $cv_id = $_GET['cv_id'];
}
if(isset($_POST['ACTION']))
{
    $ACTION = $_POST['ACTION'];
    $cv_id = $_POST['cv_id'];
    @$user_id = $_POST['user_id'];
    @$number = $_POST['number'];
    @$tax_id = $_POST['tax_id'];
    @$is_customer = $_POST['is_customer'];
    @$is_vendor = $_POST['is_vendor'];
    @$cv_name = $_POST['cv_name'];
    @$cv_default_careof = $_POST['cv_default_careof'];
    @$cv_default_address = $_POST['cv_default_address'];
    @$cv_default_city = $_POST['cv_default_city'];
    @$cv_default_state = $_POST['cv_default_state'];
    @$cv_default_zip = $_POST['cv_default_zip'];
    @$cv_default_email = $_POST['cv_default_email'];
    @$cv_default_phone = $_POST['cv_default_phone'];
    @$cv_default_statement_type = $_POST['cv_default_statement_type'];
    @$cv_default_invoice_type = $_POST['cv_default_invoice_type'];
    @$cv_default_payment_type = $_POST['cv_default_payment_type'];
    @$cv_notes = $_POST['cv_notes'];
    @$contacts_id = $_POST['contacts_id'];
    @$cv_contacts_id = $_POST['cv_contacts_id'];
    @$address_id = $_POST['address_id'];
    @$street = $_POST['street'];
    @$city = $_POST['city'];
    @$state = $_POST['state'];
    @$zip = $_POST['zip'];
    @$addresstype_id = $_POST['addresstype_id'];
    @$careof = $_POST['careof'];
    @$account_name = $_POST['account_name'];
    @$account_parent = $_POST['account_parent'];
    @$accounttype_id = $_POST['accounttype_id'];
    @$account_memo = $_POST['account_memo'];
    @$account_id = $_POST['account_id'];
    @$account_type = $_POST['account_type'];
    @$cv_account_id = $_POST['cv_account_id'];
    @$phone_id = $_POST['phone_id'];
    @$phone_num = $_POST['phone_num'];
    @$phonetype_id = $_POST['phonetype_id'];
    @$email_id = $_POST['email_id'];
    @$email_address = $_POST['email_address'];
    @$emailtype_id = $_POST['emailtype_id'];
    @$delete_address = $_POST['delete_address'];
    @$delete_email = $_POST['delete_email'];
    @$delete_phone = $_POST['delete_phone'];
    @$firstname = $_POST['firstname'];
    @$lastname = $_POST['lastname'];
    @$ssn = $_POST['ssn'];
    @$item_type = $_POST['item_type'];
    @$delete_inventory = $_POST['delete_inventory'];
    @$inventory_id = $_POST['inventory_id'];
    @$description = $_POST['description'];
    @$wholesale_price = $_POST['wholesale_price'];
    @$retail_price = $_POST['retail_price'];
    @$native_table_id = $_POST['native_table_id'];
    @$contact_notes = $_POST['contact_notes'];
    @$contacttype_id = $_POST['contacttype_id'];
    @$recurring_account_credit = $_POST['recurring_account_credit'];
    @$recurring_account_debit = $_POST['recurring_account_debit'];
    @$recurring_amount = $_POST['recurring_amount'];
    @$recurring_comment = $_POST['recurring_comment'];
    @$recurring_id = $_POST['recurring_id'];
    @$recurringtype_id = $_POST['recurringtype_id'];
    @$available = $_POST['available'];
    @$availabledate_date_year = $_POST['availabledate_date_year'];
    @$availabledate_date_month = $_POST['availabledate_date_month'];
    @$availabledate_date_day = $_POST['availabledate_date_day'];
    @$item_manager = $_POST['item_manager'];
    @$external_link = $_POST['external_link'];
    @$service_policy = $_POST['service_policy'];
    @$delete_cv = $_POST['delete_cv'];
    @$current = $_POST['current'];
    @$item_notes = $_POST['item_notes'];
    @$item_name = $_POST['item_name'];
    @$in_stock = $_POST['in_stock'];
    @$clear_with_customer = $_POST['clear_with_customer'];
    @$clear_with_vendor = $_POST['clear_with_vendor'];
    @$orders_accepted = $_POST['orders_accepted'];
    @$payments_accepted = $_POST['payments_accepted'];
    @$payments_accepted_note = $_POST['payments_accepted_note'];
    @$purchases_allowed = $_POST['purchases_allowed'];
    @$disbursements_allowed = $_POST['disbursements_allowed'];
    @$email_on_purchase = $_POST['email_on_purchase'];
    @$on_sale_auto_purchase = $_POST['on_sale_auto_purchase'];
    @$cv_category_id = $_POST['cv_category_id'];
    @$is_closed = $_POST['is_closed'];
    @$property_id = $_POST['property_id'];
    
}
switch($ACTION)
{
    case "Update GL Accounts":
        try{
            switch($account_type)
            {
                case "RECEIVABLE":
                    CV_Main::update_gl_account_receivable($cv_id, $account_id);
                break;
                case "PAYABLE":
                    CV_Main::update_gl_account_payable($cv_id, $account_id);
                break;
            }
        }
        catch(Exception $exception)
        {
            $WARNING['show'] = true;
            $WARNING['message'] = $exception->getMessage();
        }
        break;
    break;
    case "Update Contact":
        try{
            contact::add_contact($contacts_id, $lastname, $firstname, $ssn, $contacttype_id, $contact_notes);
        }
        catch(Exception $exception)
        {
            $WARNING['show'] = true;
            $WARNING['message'] = $exception->getMessage();
        }
        if(is_array($phone_id))
        {
            foreach($phone_id as $p_id)
            {
                contact::add_contactphonenum($p_id, $phone_num[$p_id], $phonetype_id[$p_id], $contacts_id);
            }
        }
        if(is_array($address_id))
        {
            foreach(@$address_id as $a_id)
            {
                contact::add_contactaddress($a_id, $street[$a_id], $city[$a_id], $state[$a_id], $zip[$a_id], $addresstype_id[$a_id], $contacts_id, $careof[$a_id]);
            }
        }
        if(is_array($email_id))
        {
            foreach(@$email_id as $e_id)
            {
                contact::add_contactemailaddy($e_id, $email_address[$e_id], $emailtype_id[$e_id], $contacts_id);
            }
        }
        if(is_array($delete_address))
        {
            foreach(@$delete_address as $d_addy)
            {
                contact::delete_address($d_addy);
            }
        }
        if(is_array($delete_phone))
        {
            foreach(@$delete_phone as $d_phone)
            {
                contact::delete_phonenum($d_phone);
            }
        }
        if(is_array($delete_email))
        {
            foreach(@$delete_email as $d_email)
            {
                contact::delete_email($d_email);
            }
        }
        break;
    case "Add Tag":
     	try{
             CV_Main::add_tag($cv_category_id, $cv_id);
        }
        catch(CVException $exception)
        {
            $WARNING['show'] = true;
            $WARNING['message'] = $exception->message;
        } 
    break;
    case "Remove Tag":
    	CV_Main::delete_tag($cv_category_id, $cv_id);
    break;
    case "Update":
        CV_Main::create_cv_object($cv_id, $cv_name, $number, $tax_id, $is_customer, $is_vendor, $cv_default_address, $cv_default_city, $cv_default_state, $cv_default_zip, $cv_default_email, $cv_default_phone);
        CV_Main::update_clear_with_customer($cv_id, $clear_with_customer);
        CV_Main::update_clear_with_vendor($cv_id, $clear_with_vendor);
        CV_Main::update_default_careof($cv_id, $cv_default_careof );
        CV_Main::update_orders_accepted($cv_id, $orders_accepted );
        CV_Main::update_payments_accepted($cv_id, $payments_accepted);
        CV_Main::updatePaymentsAcceptedNote($cv_id, $payments_accepted_note);
        CV_Main::update_purchases_allowed($cv_id, $purchases_allowed);
        CV_Main::update_disbursements_allowed($cv_id, $disbursements_allowed);
        CV_Main::update_default_statement_type($cv_id, $cv_default_statement_type);
        CV_Main::update_default_payment_type($cv_id, $cv_default_payment_type);
        CV_Main::update_default_invoice_type($cv_id, $cv_default_invoice_type);
        CV_Main::update_cv_notes($cv_id, $cv_notes);
        CV_Main::update_is_closed($cv_id, $is_closed);
        if($delete_cv == true )
        {

            CV_Main::delete_cv_object($cv_id);
        }
        break;
    case "Delete":
        CV_Main::create_cv_object($cv_id, $cv_name, $number, $tax_id, $is_customer, $is_vendor, $cv_default_address, $cv_default_city, $cv_default_state, $cv_default_zip, $cv_default_email, $cv_default_phone);
        break;
    case "Remove Contact":
        CV_Main::delete_cv_contacts_by_id($cv_contacts_id);
        break;
    case "Remove Account":
        CV_Main::delete_cv_accounts_by_id($cv_account_id);
        break;
    case "Add Address":
        contact::add_contactaddress($address_id, $street[$address_id], $city[$address_id], $state[$address_id], $zip[$address_id], $addresstype_id[$address_id], $contacts_id, $careof[$address_id]);
        break;
    case "Add Phone":
        contact::add_contactphonenum($phone_id, $phone_num[$phone_id], $phonetype_id[$phone_id], $contacts_id);
        break;
    case "Add Email":
        contact::add_contactemailaddy($email_id, $email_address[$email_id], $emailtype_id[$email_id], $contacts_id);
        break;
    case "Create and Attach Contact":
        try{
            $contacts_id = contact::add_contact($contacts_id, $lastname, $firstname, $ssn, $contacttype_id, $contact_notes);
            CV_Main::attach_contact('NULL', $cv_id, $contacts_id);
        }
        catch(Exception $exception)
        {
            $WARNING['show'] = true;
            $WARNING['message'] = $exception->getMessage();
        }
        break;
    case "Create Default Contact":
        try{
            $contacts_id = contact::add_contact($contacts_id, $cv_name, '', $tax_id, $contacttype_id, $contact_notes);
            CV_Main::attach_contact('NULL', $cv_id, $contacts_id);
            contact::add_contactaddress('NULL', $cv_default_address, $cv_default_city, $cv_default_state, $cv_default_zip, '1', $contacts_id, $cv_default_careof);
            contact::add_contactphonenum('NULL', $cv_default_phone, '1', $contacts_id);
            contact::add_contactemailaddy('NULL', $cv_default_email, '1', $contacts_id);
        }
        catch(Exception $exception)
        {
            $WARNING['show'] = true;
            $WARNING['message'] = $exception->getMessage();
        }
        break;
    case "Reset Password":
        $system_id = 1;
        $statement_object = Framework::get_system($system_id);
        $SYS_INFO = unserialize($statement_object['system_array']);
        $password = framework::createRandomPassword();
        $user = new User($user_id, '', '');
        // if successfully update the user table, email the new password to the user
        if(User::set_password($user->GetUserID_no_test(), $password))
        {
            // ---------------- SEND MAIL FORM ----------------
            // send e-mail to ...
            $to=$user->GetEmail();
            // Your subject
            $subject="Password Reset";
            // From
            $header="from: ".$SYS_INFO['COMPANY_GENERALEMAIL'];
            // Your message
            $message="Your password has been reset \r\n";
            $message.="your username is ".$user->GetUserName()."\r\n";
            $message.="your new password is $password\r\n";
             $message.="you may change your password in the user control section of the website";
            // send email
            $sentmail = mail($to,$subject,$message,$header);
        }
        else{
            $WARNING['show'] = true;
            @$WARNING['message'] .= "Password not reset";
            
        }
        // if your email succesfully sent
        if($sentmail){
            $WARNING['show'] = true;
            @$WARNING['message'] .= "An email containing a new password has been sent to their email address.<BR/>";
        }else {
            $WARNING['show'] = true;
            @$WARNING['message'] .= "Password reset but could not end Confirmation email to email address";
        }
        break;
    case "Attach Contact":
        try{
            CV_Main::attach_contact('NULL', $cv_id, $contacts_id);
        }
        catch(Exception $exception)
        {
            $WARNING['show'] = true;
            $WARNING['message'] = $exception->getMessage();
        }
        break;
    case "Attach User":
        try{
            CV_Main::attach_user('NULL', $cv_id, $user_id);
        }
        catch(Exception $exception)
        {
            $WARNING['show'] = true;
            $WARNING['message'] = $exception->getMessage();
        }
    break;
    case "Remove User":
        try{
            CV_Main::removeUser($cv_id, $user_id);
        }
        catch(Exception $exception)
        {
            $WARNING['show'] = true;
            $WARNING['message'] = $exception->getMessage();
        }
    break;
    case "Create Inventory Item":
        try{
            $type_info = Inventory_Type::get_inventory_type($item_type);
            switch($type_info['nativetable_name'])
            {

                case "properties_main":
                    $property_info = property::get_property($native_table_id);
                    $item_name = $property_info['property_address']." ".$property_info['property_aptnum'];
                    $description = $property_info['propertytype_name']." Rental";
                    $dateavail =  $property_info['property_dateavail'];
                    $rprice =  $property_info['property_rentdesired'];
                    $wprice =  $property_info['property_rentdesired'];
                    if($property_info['property_avail'] == 'Y')
                    {
                        $avail = 1;
                    }
                    else{
                        $avail = 0;
                    }
               break;
            }
            $i_id = Inventory_Item::create_inventory_item('NULL', $cv_id, $item_type, $native_table_id, 0.00, 0.00, $item_name, $description);
            Inventory_Item::set_inventory_item_prices($i_id, $wprice, $rprice);
            Inventory_Item::update_availability($i_id, $avail, $dateavail);
            Inventory_Item::update_service($i_id, $type_info['service_policy']);
            switch($type_info['nativetable_name'])
            {

                case "properties_main":
                    Inventory_Item::updateExtendedPropertyId($i_id, $native_table_id);
                break;
            }
            
        }
        catch(Exception $exception)
        {
            $WARNING['show'] = true;
            $WARNING['message'] = $exception->getMessage();
        }
        break;
    case "Verify AR/AP":
        $invoices =  Invoice::getall_invoices_of_customer($cv_id);
        $remittances = Cash_Receipts::getall_remittances_of_customer($cv_id);
        $purchases =  Purchase_Order::getall_purchaseorders_of_vendor($cv_id);
        $disbursements = Cash_Disbursements::getall_disbursements_to_vendor($cv_id);
        foreach($invoices as $i)
        {
            Invoice::update_total_remitted($i['invoice_id']);
        }
        foreach($remittances as $r)
        {
            Cash_Receipts::update_total_applied($r['remit_no']);
        }
        foreach($purchases as $p)
        {
            Purchase_Order::update_total_disbursed($p['po_id']);
        }
        foreach($disbursements as $d)
        {
            Cash_Disbursements::update_total_applied($d['cd_no']);
        }
    break;       
        
    case "Update Items":
        if(is_array($inventory_id))
        {
            foreach($inventory_id as $i_id)
            {
                try{
                    Inventory_Item::set_inventory_item_prices($i_id, $wholesale_price[$i_id], $retail_price[$i_id]);
                    Inventory_Item::update_description($i_id, $description[$i_id]);
                    $dateavail = $availabledate_date_year[$i_id]."-".$availabledate_date_month[$i_id]."-".$availabledate_date_day[$i_id];
                    Inventory_Item::update_availability($i_id, $available[$i_id], $dateavail);
                    Inventory_Item::update_manager($i_id, $item_manager[$i_id]);
                    Inventory_Item::update_service($i_id, $service_policy[$i_id]);
                    Inventory_Item::update_external_link($i_id, $external_link[$i_id]);
                    Inventory_Item::update_current($i_id, $current[$i_id]);
                    Inventory_Item::update_in_stock($i_id, $in_stock[$i_id]);
                    Inventory_Item::update_email_on_purchase($i_id, $email_on_purchase[$i_id]);
                    Inventory_Item::update_item_notes($i_id, $item_notes[$i_id]);
                    Inventory_Item::update_item_name($i_id, $item_name[$i_id]);
                    Inventory_Item::update_on_sale_auto_purchase($i_id, $on_sale_auto_purchase[$i_id]);
                    if($property_id[$i_id] != 'NULL')
                    {
                        Inventory_Item::updateExtendedPropertyId($i_id, $property_id[$i_id]);
                    }
                }
                catch(Exception $exception)
                {
                    $WARNING['show'] = true;
                    $WARNING['message'] = $exception->getMessage();
                }
           }
        }
        if(is_array($delete_inventory))
        {
            foreach($delete_inventory as $i_id)
            {
                try{
                    Inventory_Item::delete_inventory_item($i_id);
                }
                catch(Exception $exception)
                {
                    $WARNING['show'] = true;
                    $WARNING['message'] = $exception->getMessage();
                
                }
            }
        }

        break;

}
$contact_types = contact::getall_contacttypes();

$customer_info = CV_Main::get_cv_from_id($cv_id);
$addresstypes = contact::getall_contacts_addresstypes();
$phonetypes = contact::getall_contacts_phonetypes();
$emailtypes = contact::getall_contacts_emailtypes();

$contacts = CV_Main::getall_cv_contacts_from_id($cv_id);
$inventory_items = Inventory_Item::getall_inventory_items_from_cvid($cv_id);
$inventorytypes=Inventory_Type::getall_inventory_types();

$account_array = transaction::build_account_stack_all();
$accounttypes = transaction::getall_accounttypes();

$users = Rbac_User::getAllAllowedTo("manage_inventory", "inventory_items", $FRAMEWORK);
$users_all = User::get_users('username');

$cv_users = CV_Main::getall_cv_users($cv_id);


$CV_Categories = CV_Category::get_all();
$cv_tags = CV_Main::get_tags_of_cv($cv_id);
//need to decouple properties from inventory
//$properties = Property::getall_properties_by_status('CURRENT');
                            

if($customer_info['is_customer'])
{
	$invoices =  Invoice::getall_invoices_of_customer($cv_id);
    $recurring_invoices =  Recurring_Invoice::getall_invoices_of_customer($cv_id);
    $remittances = Cash_Receipts::getall_remittances_of_customer($cv_id);
	
	
}
if($customer_info['is_vendor'])
{
	$purchases =  Purchase_Order::getall_purchaseorders_of_vendor($cv_id);
	$disbursements = Cash_Disbursements::getall_disbursements_to_vendor($cv_id);
}


include("cv_master_edit.phtml");

?>
