<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
include("../../../framework/framework_masterinclude.php");
include("../../../framework/classes/Filehandler.class.php");

$filename = $_GET['file'];
$file = new filehandler($filename);
$file_info = $file->get_info();    
$permission = $_GET['permission'];
$can_download = false; 
switch($permission)
{
    case "transaction":
        framework::authenticate();
       //get all trans to files for file
        include_once("../transactions/transactions_functions.php");
        $transactions = transactions::getall_transactions_of_file($filename);
        //get all accounts for trans
        foreach($transactions as $row)
        {
            $accounts[$row['transaction_accountD']] = true;
            $accounts[$row['transaction_accountW']] = true;
        }
        //check accounts for user
        if(Rbac_User::IsAllowedTo($USER->GetUserID(), "view_statement", "all_accounts"))
        {
            $can_download = true; 
        }
        elseif(Rbac_User::IsAllowedTo($USER->GetUserID(), "view_statement", "own_accounts"))
        {
            $user_mtm_account = transactions::getall_users_mtm_accounts_by_user_id($USER->GetUserID());
            foreach($user_mtm_account as $row)
            {
                if(@$accounts[$row['account_id']])
                {
                    $can_download = true; 
                }
            } 
        }
    break;
    case "property":
        include("../../../framework/classes/Property.class.php");
    	   $row = Property::get_property_file($filename);
            if($row['COUNT'] > 0)
            {
                $can_download = true;
                $SYSTEM_SETTINGS['log_files'] = false;
            }
    break;
    case "document":
        if(Rbac_User::IsAllowedTo($USER->GetUserID(), "access_documents", "all_documents"))
        {
            $can_download = true; 
        }
    break;
    case "invoice":
        if(Rbac_User::IsAllowedTo($USER->GetUserID(), "access_invoices", "all_invoices"))
        {
            $can_download = true; 
        }
        elseif(Rbac_User::IsAllowedTo($USER->GetUserID(), "access_invoices", "my_invoices"))
        {
            //is this one of MY invoices?
            //get invoice from File ID
            $can_download = false; 
        }
    break;
     
}

if($can_download)
{
    if($SYSTEM_SETTINGS['log_files'])
    {
        $file->log_file_access($USER->GetUserID(), $_SERVER['REMOTE_ADDR']);
    }
    // required for IE, otherwise Content-disposition is ignored
    if(ini_get('zlib.output_compression'))
    ini_set('zlib.output_compression', 'Off');
    if( $file_info == "" ) 
    {
    echo "<html><title>eLouai's Download Script</title><body>ERROR: download file NOT SPECIFIED. USE force-download.php?file=filepath</body></html>";
    exit;
    } elseif ( ! file_exists("../../../files/".$DB_SETTINGS['dbname']."/".$filename ) ) 
    {
    echo "<html><title>eLouai's Download Script</title><body>ERROR: File not found. USE force-download.php?file=filepath</body></html>";
    exit;
    };
    header("Pragma: public"); // required
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Cache-Control: private",false); // required for certain browsers 
    header("Content-Type: ".$file_info['mimeType']);
    if($file_info['mimeType'] == "image/jpeg" )
    {
        header("Content-Disposition: inline; filename=\"".$file_info['fileName'] ."\";" );
    }
    else{
        header("Content-Disposition: attachment; filename=\"".$file_info['fileName'] ."\";" );
    }
    header("Content-Transfer-Encoding: binary");
    header("Content-Length: ".filesize("../../../files/".$DB_SETTINGS['dbname']."/".$filename ));
    readfile("../../../files/".$DB_SETTINGS['dbname']."/".$filename );
    exit();

}

?>
    