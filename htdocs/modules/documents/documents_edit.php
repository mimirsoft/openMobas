<?
//error_reporting(E_ALL);
//ini_set('display_errors', '1');

include("../../../framework/framework_masterinclude.php");
include("properties_include.php");
Framework::authenticate("Unlimited");//the two includes must be before the authentica, to supply the needed module name for authentication

$ACTION = '';
$LINK = '';
$dateavail_date_year = date('Y');
$dateavail_date_month =  date('m');
$dateavail_date_day = date('d');
$empty = '';
$recurring_comment = '';
$accountpreselect = '';
$recurring_amount = '';
$accounts = '';
$recurring = '';
$file_id = '';
$fileDesc = '';
$property_id = '';
foreach ($_POST as $key => $value)
{
    $$key = $value;
}

$dateavail = $dateavail_date_year."-".$dateavail_date_month."-".$dateavail_date_day;

if ($property_id == '')
{
    header("Location: https://".$_SERVER['HTTP_HOST'].$BASE_DIR."/properties/properties_main.php");
}


if ($LINK == "Add Link" || $LINK == "Record")
{
    properties::add_link($propertyaccount_id, $property_id, $account_id);
}
if ($LINK == "Delete")
{
    properties::delete_link($propertyaccount_id);
}
if ($ACTION == "EDIT" && isset($recurring_list))
{
    recurring::build_recurringtypearray();
    $accountIDtoFullnameArray = transactions::build_accountIDtoFullName_array(false);
    recurring::getalladdresses();
    foreach($recurring_list as $recurring_id)
    {    
        recurring::recurring_address_api($recurring_id);
        include("../recurring/recurring_address.phtml");
    }
    exit;
}

switch($ACTION)
{
    case "Add Owner":
        if ($property_owner_id == "NULL")
        {
            $ownership_percentage = $ownership_percentage/100;
            properties::add_owner($property_owner_id, $property_id, $contacts_id, $ownership_percentage);
        }
        break;
    case "Add Recurring":
        if ($recurring_id == "NULL")
        {
            recurring::add_recurring($recurring_id, $recurring_comment, $recurring_accountD, $recurring_accountW, $recurring_amount, $recurringtype_id);
        }
        break;

    case "Save Recurring": 
        if(isset($recurring_list))
        {
            foreach($recurring_list as $recurring_id)
            {
                recurring::add_recurring($recurring_id, $recurring[$recurring_id]['recurring_comment'], $recurring[$recurring_id]['recurring_accountD'], $recurring[$recurring_id]['recurring_accountW'], $recurring[$recurring_id]['recurring_amount'], $recurring[$recurring_id]['recurringtype_id']);
            }
        }
        break;
    case "Delete Recurring":        
        if(isset($recurring_list))
        {
            foreach($recurring_list as $recurring_main_ID)
            {
                recurring::delete_recurring($recurring_main_ID);
            }
        }
        break;
    case "SAVE":        
        if ($address != "")
        {
            properties::add_property($property_id, $address, $aptnum, $propnum, $city, $state, $county, $zip,  $thomasguidenum, $complex, $area, $garagenum, $parknum, $mailboxnum, $sqft, $yrblt, $numrm, $numbed, $numbath, $refridge, $dishwasher, $stove, $stovetype, $microwave, $fireplace, $fireplacetype, $air, $heat, $heattype, $washdry, $washdrytype, $washdryhook, $washdryhooktype, $garage, $openers, $poolspa, $landscaping, $landscaper, $pets, $petdeposit, $smoking, $whopaygas, $whopayelec, $whopaywater, $whopaygarbage, $rentdesired, $rentmin, $leasedesired, $leasemin, $dateavail, $propertytype_id, $feepercent, $property_status, $property_avail, $property_sale, $shortterm, $comments);
        }
        break;
    case "DELETE":        
        if ($property_id != "")
        {
            properties::delete_property($property_id);
            header("Location: https://".$_SERVER['HTTP_HOST'].$BASE_DIR."/properties/properties_main.php");
        }
        break;
    case "Delete Owner":        
        if ($property_owner_id != "")
        {
            properties::delete_owner($property_owner_id);
        }
        break;
    case "Update Percent":        
        if ($property_owner_id != "")
        {
            $ownership_percentage = $ownership_percentage/100;
            properties::update_percentage($property_owner_id, $ownership_percentage);
        }
        break;
    case "Upload Files":        
                include("../../../framework/fileupload.class.php");
		$upload = new fileupload();
		
		// restrict filesize to 1500 bytes (1.5kb) or smaller
		$upload->set_max_filesize(500000);
		
		// limit types of files based on MIME type
		// 'image' - accept any MIME type containing 'image' (.gif, .jpg, .png, .tif, etc...)
		// 'image/gif' - only accept gif images
		// 'image/gif, image/png' - only accept gif and png images
		// 'image/gif, text' - accept gif images and any MIME type containing 'text'
		$upload->set_acceptable_types('image'); // comma separated string, or array
		
                
		// Set mode to manage identically named files 
		// 
		// user uploads 'foo.gif', but that file already exists in the upload directory:
		//    1 = (overwrite) overwrite existing file with uploaded foo.gif
		//    2 = (rename) keep original foo.gif, upload new file but call it 'foo_copy1.gif'
		//    3 = (do nothing) keep original foo.gif, do nothing with uploaded file, raise error
		$upload->set_overwrite_mode(1);
		
		$filename = $upload->upload("userfile", "../../files/uploads/");
		if ($filename) 
                {
                    if(is_array($filename))
                    {
                        foreach($filename as $file)
                        {
                            properties::linkfile("NULL", $file, $property_id);
                        }
                    }
                    else
                    {
                        properties::linkfile("NULL", $filename, $property_id);
                    }
		} 
                else {
			echo $upload->get_error();
		}
    case "Update Description":        
            include("../../../framework/filehandler.class.php");
            $file = new filehandler($file_id);
            $file->update_desc($fileDesc);    
        break;
}
$dbh = new DB_Mysql();
$properties = properties::select_properties(true, "CURRENT");

$prop = properties::get_property($property_id);

foreach ($prop as $key => $value)
{
    $key2 = substr($key, 9);
    $$key2 = $value;
}
$dateavail_date_year = substr($dateavail, 0 ,4);
$dateavail_date_month = substr($dateavail, 5 ,2);
$dateavail_date_day = substr($dateavail, 8 ,2);
$property_id = $prop['property_id'];
$propertytype_id = $prop['propertytype_id'];
$property_status = $prop['property_status'];
$property_avail = $prop['property_avail'];


$type_ID = contacts::get_contacttype_id_from_name('OWNER');
$owners1 = contacts::getall_contacts_of_type($type_ID);    
$type_ID = contacts::get_contacttype_id_from_name('OWNER(old)');
$owners2 = contacts::getall_contacts_of_type($type_ID);    
if(is_array($owners2))
{
    $owners = array_merge($owners1, $owners2);
}
else
{
    $owners = $owners1;
}
$stmt = $dbh->prepare("SELECT * 
                         FROM properties_type 
                     ORDER BY propertytype_name");
$stmt->execute();
$propTypes = $stmt->fetchall_assoc();

$stmt = $dbh->prepare("SELECT * 
                         FROM recurring_type 
                     ORDER BY recurringtype_name");
$stmt->execute();
$types = $stmt->fetchall_assoc();

$stmt = $dbh->prepare("SELECT * 
                         FROM properties_accounts 
                        WHERE property_id=:1: ");
$stmt->execute($property_id);
while($row = $stmt->fetch_assoc())
{
    $accounts[] = $row;
    $stmt2 = $dbh->prepare("SELECT * 
                              FROM recurring_main 
                             WHERE recurring_accountW=:1: 
                                OR recurring_accountD=:1:");
    $stmt2->execute($row['account_id']);
    $recurring = $stmt2->fetchall_assoc();
    $accountpreselect = $row['account_id'];
}
$stmt = $dbh->prepare("SELECT * 
                         FROM properties_files as pf, files_main as fm
                        WHERE pf.property_id=:1: 
                          AND pf.file_id = fm.file_id");
$stmt->execute($property_id);
$files = $stmt->fetchall_assoc();

$owner_set = properties::getall_owners($property_id);
$select_array = transactions::build_accountIDtoFullName_array(false);


include("properties_edit.phtml");

