<?php
require_once("../../../framework/framework_masterinclude.php");
require_once("cv_master_include.php");


$FRAMEWORK->authenticate($objSession, $MODULE_NAME, $USER, $BASE_DIR);

$ACTION = '';
$name = '';
$number = '';
$taxid = '';
$is_customer = 0;
$is_vendor = 0;
$warn = false;
$customers = array();

if(isset($_POST['ACTION']))
{
    $ACTION = $_POST['ACTION'];
    $name = $_POST['name'];
    $cv_list = $_POST['cv_list'];
    @$statementtype = $_POST['statementtype'];
}


switch($ACTION)
{
    case "Update GL Accounts":
 
    break;
    case "Search Vendors":
    	if($name != "")
    	{
	    	$customers = CV_Main::search_vendors_by_name($name);
    	}
    	else
    	{
    		$customers = CV_Main::getall_vendors();
    	}
    break;
    case "Search Neither":
    	if($name != "")
    	{
	    	$customers = CV_Main::search_neither_by_name($name);
    	}
    	else
    	{
	    	$customers = CV_Main::getall_neither();
    	}
    break;
    case "Search Customers":
    	if($name != "")
    	{
	    	$customers = CV_Main::search_customers_by_name($name);
    	}
    	else
    	{
	    	$customers = CV_Main::getall_customers();
    	}
    break;
    case "Search All":
    	if($name != "")
    	{
	    	$customers = CV_Main::search_cv_by_name($name);
    	}
    	else
    	{
	    	$customers = CV_Main::getall_cv();
    	}
    break;
    case "Email Statements":
		require_once("../purchasing/Purchase_Requisition.php");
		require_once("../invoices/Invoice.php");
    	require_once("../mailing/mailing_functions.php");
    	$system_id = 1;
		$statement_object = Framework::get_system($system_id);
		$SYS_INFO = unserialize($statement_object['system_array']);
		foreach($cv_list as $cv_id)
		{		
			//get the requisition
			//$invoice_info = Purchase_Requisition::get_purchase_requisition($pr_id);
			//get all disbursements applied against PO
			//$po_info = Purchase_Order::get_purchase_order($po_id);
			$customer_info = CV_Main::get_cv_from_id($cv_id);
			$po_set = Purchase_Order::getall_purchaseorders_of_vendor_last30days($cv_id);
			$disbursements = array();
			foreach($po_set as $po)
			{
				$disbursements = array_merge($disbursements, Cash_Disbursements::getall_disbursements_against_po($po['po_id']));
			}
			$inv_set = Invoice::getall_invoices_of_customer_last30days($cv_id);
			$payments = array();
			foreach($inv_set as $inv)
			{
				$payments = array_merge($payments, Cash_Receipts::getall_remittances_against_inv($inv['invoice_id']));
			}
			ob_start();
			include("cv_master_statement_summary.css");
            switch($statementtype)
            {
                case "summary":
                    require_once("cv_master_statement_summary.phtml");
                break;
                case "detailed_summary":
                    require_once("cv_master_statement_detailedsummary.phtml");
                break;
                default:
                    require_once("cv_master_statement_summary.phtml");
                break;
            }    			
            $msg_body = ob_get_contents();
            ob_end_clean();
            mailing::email_stuff($SYS_INFO['COMPANY_NAME'],$SYS_INFO['COMPANY_GENERALEMAIL'],$customer_info['cv_default_email'],"Statement for ".$customer_info['cv_name'],$msg_body);
            echo "Sent to ".$customer_info['cv_default_email']."<BR/>";                        
		}
		exit;
    break;
    default:
    break;
}
    	

//$customertypes = cv_manager::getall_customertypes();
//$users = Rbac_User::getAllAllowedTo("access_module", "cv_master_module");

//$users = $module->GetUsers();


include("cv_master_statement.phtml");

?>
