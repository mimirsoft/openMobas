<?php
require_once("../../../framework/framework_masterinclude.php");
require_once("invoices_include.php");

$FRAMEWORK->authenticate($objSession, $MODULE_NAME, $USER, $BASE_DIR);

$ACTION =      '';
$WARNING['show'] = false;
$recorddate_year = date('Y');
$recorddate_month = date('m');
$recorddate_day = "01";

if(isset($_POST['ACTION']))
{
    $ACTION = $_POST['ACTION'];
    @$recurring_list = $_POST['recurring_list'];
}
switch($ACTION)
{
    case "Charge Recurring":
        $recorddate_year = @$_POST['recorddate_year'];
        $recorddate_month = @$_POST['recorddate_month'];
        $recorddate_day = @$_POST['recorddate_day'];
        $date = $recorddate_year."-".$recorddate_month."-".$recorddate_day;
        include("Invoice.php");
        foreach($recurring_list as $recurring_id)
        {
            Recurring_Invoice::charge_recurring($recurring_id, $date);
        }
        if($_SERVER['HTTPS'])
        {
            header("Location: https://".$_SERVER['HTTP_HOST'].$BASE_DIR."/invoices/invoices_main.php");
        }
        if(!$_SERVER['HTTPS'])
        {
            header("Location: http://".$_SERVER['HTTP_HOST'].$BASE_DIR."/invoices/invoices_main.php");
        }
    break;
}

$recurring_invoices = Recurring_Invoice::getall_invoices();

include("invoices_charge_recurring.phtml");


?>
