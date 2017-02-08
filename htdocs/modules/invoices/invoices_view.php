<?php
require_once("../../../framework/framework_masterinclude.php");
require_once("invoices_include.php");
require_once("../../../framework/classes/Invoice.class.php");

$FRAMEWORK->authenticate($objSession, $MODULE_NAME, $USER, $BASE_DIR);

$ACTION =      '';
$WARNING['show'] = false;
if(isset($_POST['ACTION']))
{
    $ACTION = $_POST['ACTION'];
    @$invoice_id = $_POST['invoice_id'];
    @$invoice_description = $_POST['invoice_description'];
    @$userfile = $_POST['userfile'];
    @$file_id = $_POST['file_id'];
}
if(isset($_GET['ACTION']))
{
    $ACTION = $_GET['ACTION'];
    @$invoice_id = $_GET['invoice_id'];
}

switch($ACTION)
{
    case "Upload Files":        
        require_once("../../../framework/classes/Fileupload.class.php");
		$upload = new fileupload();
		
		// restrict filesize to 800000 bytes (800kb) or smaller
		$upload->set_max_filesize(800000);
		
		// limit types of files based on MIME type
		// 'image' - accept any MIME type containing 'image' (.gif, .jpg, .png, .tif, etc...)
		// 'image/gif' - only accept gif images
		// 'image/gif, image/png' - only accept gif and png images
		// 'image/gif, text' - accept gif images and any MIME type containing 'text'
		$upload->set_acceptable_types('application/pdf'); // comma separated string, or array
		
                
		// Set mode to manage identically named files 
		// 
		// user uploads 'foo.gif', but that file already exists in the upload directory:
		//    1 = (overwrite) overwrite existing file with uploaded foo.gif
		//    2 = (rename) keep original foo.gif, upload new file but call it 'foo_copy1.gif'
		//    3 = (do nothing) keep original foo.gif, do nothing with uploaded file, raise error
		$upload->set_overwrite_mode(1);
		
		$filename = $upload->upload("userfile");
		if ($filename) 
        {
            if(is_array($filename))
            {
                foreach($filename as $file)
                {
                    Invoice::linkFile($file, $invoice_id);
                }
            }
            else
            {
                Invoice::linkFile($filename, $invoice_id);
            }
		} 
        else {
			echo $upload->getError();
		}
        break;
    case "Delete File":        
            include("../../../framework/classes/Filehandler.class.php");
            $file = new filehandler($file_id);
            $file->delete_file();    
    break;
    case "Update":
        Invoice::update_description($invoice_id, $invoice_description);
    break;
    case "PRINT":
        $invoice_info = Invoice::get_invoice($invoice_id);
        $invoice_items = Invoice::getall_invoiceitems($invoice_id);

        $system_id = 1;
        $statement_object = Framework::get_system($system_id);
        $SYS_INFO = unserialize($statement_object['system_array']);
        require_once("invoices_print_invoice.css");
        require_once("invoices_print_invoice.phtml");
        exit;
    break;
        case "PRINT ESTIMATE":
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


if($invoice_id == '')
{
    if($_SERVER['HTTPS'])
    {
        header("Location: https://".$_SERVER['HTTP_HOST'].$BASE_DIR."/invoices/invoices_main.php");
    }
    if(!$_SERVER['HTTPS'])
    {
        header("Location: http://".$_SERVER['HTTP_HOST'].$BASE_DIR."/invoices/invoices_main.php");
    }
}

$files = Invoice::getAllFilesOfInvoice($invoice_id);

$invoice_items = Invoice::getall_invoiceitems($invoice_id);
$invoice_info = Invoice::get_invoice($invoice_id);
$invoice_requisitions = Invoice::get_requisitions_from_invoice($invoice_id);

//get all associated requisitions



include("invoices_view.phtml");


?>
