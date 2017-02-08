<?php
require_once("../../../framework/framework_masterinclude.php");
require_once("clients_include.php");
require_once("../../../framework/classes/Invoice.class.php");
$FRAMEWORK->authenticate($objSession, $MODULE_NAME, $USER, $BASE_DIR);

$ACTION =      '';
$WARNING['show'] = false;
if(isset($_POST['ACTION']))
{
    $ACTION = $_POST['ACTION'];
    @$invoice_id = $_POST['invoice_id'];
}
switch($ACTION)
{
    case "Uncharge":
        Invoice::uncharge($invoice_id);
    break;
    case "VIEW/PRINT":
        $invoice_info = Invoice::get_invoice($invoice_id);
        $invoice_items = Invoice::getall_invoiceitems($invoice_id);

        $system_id = 1;
        $statement_object = Framework::get_system($system_id);
        $SYS_INFO = unserialize($statement_object['system_array']);
        require_once("../invoices/invoices_print_invoice.css");
        require_once("../invoices/invoices_print_invoice.phtml");
        exit;
    break;
}



?>
