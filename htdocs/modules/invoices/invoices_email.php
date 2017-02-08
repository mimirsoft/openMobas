<?
include("../../../framework/framework_masterinclude.php");
Framework::authenticate();//the two includes must be before the authentica, to supply the needed module name for authentication
include("invoices_include.php");

include("Invoice.php");
require_once("../mailing/mailing_functions.php");
    	
$ACTION =      '';
$WARNING['show'] = false;
if(isset($_POST['ACTION']))
{
    $ACTION = $_POST['ACTION'];
    @$invoice_id = $_POST['invoice_id'];
}

switch($ACTION)
{
    case "EMAIL":
        $invoice_info = Invoice::get_invoice($invoice_id);
        $invoice_items = Invoice::getall_invoiceitems($invoice_id);
        $customer_info = CV_Main::get_cv_from_id($invoice_info['customer_id']);
			
        $system_id = 1;
        $statement_object = Framework::get_system($system_id);
        $SYS_INFO = unserialize($statement_object['system_array']);
        ob_start();
	    require_once("invoices_print_invoice.css");
        require_once("invoices_print_invoice.phtml");
        $msg_body = ob_get_contents();
        ob_end_clean();
        mailing::email_stuff($SYS_INFO['COMPANY_NAME'],$SYS_INFO['COMPANY_GENERALEMAIL'],$customer_info['cv_default_email'],"Invoice ".$invoice_info['invoice_id'],$msg_body);
        echo "Sent to ".$customer_info['cv_default_email']."<BR/>";        
        exit;
    break;
        case "EMAIL ESTIMATE":
        $invoice_info = Invoice::get_invoice($invoice_id);
        $invoice_items = Invoice::getall_invoiceitems($invoice_id);

        $system_id = 1;
        $statement_object = Framework::get_system($system_id);
        $SYS_INFO = unserialize($statement_object['system_array']);
        require_once("invoices_print_estimate.css");
        require_once("invoices_print_estimate.phtml");
        exit;
    break;
}




?>
