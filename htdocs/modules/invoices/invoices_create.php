<?php
require_once("../../../framework/framework_masterinclude.php");
require_once("invoices_include.php");
$FRAMEWORK->authenticate($objSession, $MODULE_NAME, $USER, $BASE_DIR);

$ACTION =      '';
$WARNING['show'] = false;
if(isset($_POST['ACTION']))
{
    $ACTION = $_POST['ACTION'];
    $customer_id = $_POST['customer_id'];
    $invoice_type = $_POST['invoice_type'];
}
switch($ACTION)
{
    case "Create Invoice":
        switch($invoice_type)
        {
            case "normal":
                $customer_info = CV_Main::get_cv_from_id($customer_id);
                $contacts = CV_Main::getall_cv_contacts_from_id($customer_id);
                $revenue_accounts=Invoice_System::getall_revenue_accounts();
                include("invoices_create.phtml");
            break;
            case "recurring":
                $customer_info = CV_Main::get_cv_from_id($customer_id);
                $contacts = CV_Main::getall_cv_contacts_from_id($customer_id);
                $revenue_accounts=Invoice_System::getall_revenue_accounts();
                include("invoices_create_recurring.phtml");
          break;
    
        }
        break;

}


?>
