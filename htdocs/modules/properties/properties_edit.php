<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once("../../../framework/framework_masterinclude.php");
require_once("properties_include.php");

$FRAMEWORK->authenticate($objSession, $MODULE_NAME, $USER, $BASE_DIR);
$WARNING['show'] = false;

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
$firstname = '';
$lastname = '';
$ssn = '';
$contact_notes = '';
$account_name = '';
$account_memo = '';
$washer = '';
$dryer = '';
$garageinfo = '';
$parking = '';
$VIEW = '';
$hoa = '';
$hoa_number = '';

foreach ($_POST as $key => $value)
{
    $$key = $value;
}
foreach ($_GET as $key => $value)
{
    $$key = $value;
}

$dateavail = $dateavail_date_year."-".$dateavail_date_month."-".$dateavail_date_day;

if ($property_id == '')
{
    if($_SERVER['HTTPS'])
    {
    header("Location: https://".$_SERVER['HTTP_HOST'].$BASE_DIR."/properties/properties_main.php");
    }
    else{
    header("Location: http://".$_SERVER['HTTP_HOST'].$BASE_DIR."/properties/properties_main.php");
    }
}

switch($ACTION)
{
    case "SAVE":        
        
        if ($address != "")
        {
            property::add_property($property_id, $address, $aptnum, $propnum, $city, $state, $county, $zip,  $thomasguidenum, 
                $complex, $area, $garagenum, $parknum, $mailboxnum, $sqft, $yrblt, $numrm, $numbed, $numbath, $refridge, $dishwasher, 
                $stove, $microwave, $fireplace, $air, $heat, $garage, $openers, $pool, $landscaping, 
                $pets, $petdeposit, $smoking, $whopaygas, $whopayelec, $whopaywater, $whopaygarbage, $rentdesired, $rentmin, 
                $leasedesired, $leasemin, $dateavail, $propertytype_id, $feepercent, $property_status, $property_avail, 
                $property_sale, $shortterm, $comments, $description, $petdescription, $garageinfo, $maintenance, $washer, $dryer, $parking);
                property::updateHOA($property_id, $hoa);
                property::updateHOANumber($property_id, $hoa_number);
                property::updateHOAManager($property_id, $hoa_manager);
                property::updateSpa($property_id, $spa);
                property::updateCvID($property_id, $cv_id);
        }
        break;
    case "DELETE":        
        if ($property_id != "")
        {
            property::delete_property($property_id);
            header("Location: https://".$_SERVER['HTTP_HOST'].$BASE_DIR."/properties/properties_main.php");
        }
        break;
    case "Upload Files":        
        require_once("../../../framework/classes/Fileupload.class.php");
		$upload = new fileupload();
		
		// restrict filesize to 500000 bytes (500kb) or smaller
		$upload->set_max_filesize(800000);
		
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
		
		$filename = $upload->upload("userfile");
		if ($filename) 
                {
                    if(is_array($filename))
                    {
                        foreach($filename as $file)
                        {
                            property::linkfile("NULL", $file, $property_id);
                        }
                    }
                    else
                    {
                        property::linkfile("NULL", $filename, $property_id);
                    }
		} 
                else {
			echo $upload->getError();
		}
        break;
    case "Update Description":        
            include("../../../framework/classes/Filehandler.class.php");
            $file = new filehandler($file_id);
            $file->update_desc($fileDesc);    
        break;
    case "Delete File":        
            include("../../../framework/classes/Filehandler.class.php");
            $file = new filehandler($file_id);
            $file->delete_file();    
        break;
}
$dbh = new DB_Mysql();

$prop = property::get_property($property_id);

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


$propTypes = property::getall_property_types();

$files = property::getall_files_of_property($property_id);
$cat = CV_Category::getCvCatByName('Owner');
$owners = CV_Main::search_cv('', $cat['cv_category_id']);
switch($VIEW)
{
    case "test":        
		include("properties_infotable2.phtml");
            
        break;
	default:
		include("properties_edit.phtml");
	break;
}


