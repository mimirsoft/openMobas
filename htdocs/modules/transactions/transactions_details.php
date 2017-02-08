<?
include("../../../framework/framework_masterinclude.php");
include("transactions_include.php");
framework::authenticate("");//the two includes must be before the authentica, to supply the needed module name for authentication
if(!(Rbac_User::IsAllowedTo($USER->GetUserID(), "view_details", "transactions_module")))
{
    echo "PERMISSION DENIED TO VIEW TRANSACTION DETAILS IN TRANSACTIONS MODULE!!";
    exit;
}

$ACTION     =       '';     
$account_id =       '';

foreach ($_POST as $key => $value)
{
    $$key = $value;
    unset($_POST->{$key});
}
foreach ($_GET as $key => $value)
{
    $$key = $value;
    unset($_POST->{$key});
}

$accountIDtoFullnameArray = transactions::build_accountIDtoFullName_array(false);

switch($ACTION)
{
    case "Upload Files":        
                include("../../../framework/fileupload.class.php");
		$upload = new fileupload();
		// restrict filesize to 500000 bytes (500kb) or smaller
		$upload->set_max_filesize(800000);
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
                        transactions::linkfile("NULL", $filename, $transaction_id);
		} 
                else {
			echo $upload->get_error();
		}
        break;
}
$row = transactions::get_transaction($transaction_id);
$all_accounts = transactions::build_accountIDtoFullName_array(true);
$files = transactions::getall_files_of_transaction($transaction_id);
echo $_FILES['userfile']['type'];



include("transactions_details.phtml");


?>

