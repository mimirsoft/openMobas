<?php
require_once("../../../framework/framework_masterinclude.php");
require_once("purchasing_include.php");
$FRAMEWORK->authenticate($objSession, $MODULE_NAME, $USER, $BASE_DIR);

$ACTION =      '';
$WARNING['show'] = false;
if(isset($_POST['ACTION']))
{
    $ACTION = $_POST['ACTION'];
    @$pr_id = $_POST['pr_id'];

}
if(isset($_GET['ACTION']))
{
    $ACTION = $_GET['ACTION'];
   @$pr_id = $_GET['pr_id'];
}
if($pr_id == '')
{
    if($_SERVER['HTTPS'])
    {
        header("Location: https://".$_SERVER['HTTP_HOST'].$BASE_DIR."/purchasing/purchasing_main.php");
    }
    if(!$_SERVER['HTTPS'])
    {
        header("Location: http://".$_SERVER['HTTP_HOST'].$BASE_DIR."/purchasing/purchasing_main.php");
    }
}
$invoice_items = Purchase_Requisition::getall_purchaserequest_items($pr_id);

$invoice_info = Purchase_Requisition::get_purchase_requisition($pr_id);
$po_info = Purchase_Order::get_purchase_order_from_pr($pr_id);

include("purchasing_view.phtml");


?>
