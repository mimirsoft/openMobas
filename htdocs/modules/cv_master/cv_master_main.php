<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
require_once("../../../framework_masterinclude.php");
require_once("cv_master_include.php");

$FRAMEWORK->authenticate($objSession, $MODULE_NAME, $USER, $BASE_DIR);


if(!Rbac_User::IsAllowedTo($USER->GetUserID(), "access_module", "cv_master_module"))
{
    echo "Permission access Customer/Vendor Module";
    exit;
}

$ACTION = '';
$name = '';
$number = '';
$taxid = '';
$is_customer = 0;
$is_vendor = 0;
$warn = false;
$customers = array();
$sort = "name";
$search_tag = "none";
$SEARCH = "";
$WARNING['show'] = false;
if(isset($_POST['ACTION']))
{
    $ACTION = $_POST['ACTION'];
    $name = $_POST['name'];
    @$number = $_POST['number'];
    @$taxid = $_POST['taxid'];
    @$is_customer = $_POST['is_customer'];
    @$is_vendor = $_POST['is_vendor'];
    @$cv_default_address = $_POST['cv_default_address'];
    @$cv_default_city = $_POST['cv_default_city'];
    @$cv_default_state = $_POST['cv_default_state'];
    @$cv_default_zip = $_POST['cv_default_zip'];
    @$cv_default_email = $_POST['cv_default_email'];
    @$cv_default_phone = $_POST['cv_default_phone'];
    @$cv_category_id = $_POST['cv_category_id'];
    @$cv_id = $_POST['cv_id'];
    @$previous_search = $_POST['previous_search'];
    @$SEARCH = $_POST['SEARCH'];
    @$search_tag = $_POST['search_tag'];
}
if(isset($_GET['SEARCH']))
{
    @$name = $_GET['name'];
    @$SEARCH = $_GET['SEARCH'];
    @$sort = $_GET['sort'];
    @$search_tag = $_GET['search_tag'];
}    


switch($ACTION)
{
    case "Add Customer":
        if($name != "")
        {
            CV_Main::create_cv_object("NULL", $name, $number, $taxid, $is_customer, $is_vendor, $cv_default_address, $cv_default_city, $cv_default_state, $cv_default_zip, $cv_default_email, $cv_default_phone);
            $name = "";
            $number = "";
            $taxid = "";
            $is_customer = '';
            $is_vendor = "";
        }
        else
        {
            $WARNING['show'] = true;
            $WARNING['message'] .= "Must Have Name Set";
        }
    break;
    case "Remove Tag":
        CV_Main::delete_tag($cv_category_id, $cv_id);
        $SEARCH = $previous_search;
    break;
    case "Add Tag":
        foreach($cv_id as $cv_element)
        {
            try{
                 CV_Main::add_tag($cv_category_id, $cv_element);
            }
            catch(CVException $exception)
            {
                $WARNING['show'] = true;
                $WARNING['message'] .= $exception->message;
            }
        }
        $SEARCH = $previous_search;
        
    break;
    case "Remove Tags":
        foreach($cv_id as $cv_element)
        {
            try{
                CV_Main::delete_tag($cv_category_id, $cv_element);
            }
            catch(CVException $exception)
            {
                $WARNING['show'] = true;
                $WARNING['message'] .= $exception->message;
            }
        }
        $SEARCH = $previous_search;
        
    break;
    default:
    break;
}
    	    
switch($SEARCH)
{
    case "SearchVendors":
        if($search_tag != "none")
        {
            $customers = CV_Main::search_cv($name, $search_tag);
        }
        elseif($name != "")
        {
            $customers = CV_Main::search_vendors_by_name($name);
        }
        else
        {
            $customers = CV_Main::getall_vendors();
        }
    break;
    case "SearchNeither":
        if($search_tag != "none")
        {
            $customers = CV_Main::search_cv($name, $search_tag);
        }
        elseif($name != "")
        {
            $customers = CV_Main::search_neither_by_name($name);
        }
        else
        {
            $customers = CV_Main::getall_neither();
        }
    break;
    case "SearchCustomers":
        if($search_tag != "none")
        {
            $customers = CV_Main::search_cv($name, $search_tag);
        }
        elseif($name != "")
        {
            $customers = CV_Main::search_customers_by_name($name);
        }
        else
        {
            $customers = CV_Main::getall_customers();
        }
    break;
    case "SearchAll":
        if($search_tag != "none")
        {
            $customers = CV_Main::search_cv($name, $search_tag);
        }
        elseif($name != "")
        {
            $customers = CV_Main::search_cv_by_name($name);
        }
        else
        {
            $customers = CV_Main::getall_cv();
        }
    break;
    case "SearchItem":
        if($name != "")
        {
            $customers = Inventory_Item::search_item_by_name($name);
            $purchasers = array();
            
            foreach($customers as $row)
            {
                //find all customer who bought this 
                $tmp = Invoice::getall_customers_purchasing_item($row['inventory_id']);
                $purchasers = array_merge($purchasers, $tmp);
            }
        }
        else
        {
            $customers = CV_Main::getall_cv();
        }
    break;
}
   	
function cv_id( $row1,$row2 )
{
         if ( $row1["cv_id"] == $row2["cv_id"] )
        return 0;
        elseif ( $row1["cv_id"] < $row2["cv_id"] )
        return -1;
        else
        return 1;
}

switch($sort)
{
    case "id":
        uasort($customers,'cv_id');
    break;
    default:
    break;
}

$CV_Categories = CV_Category::get_all();
$tag_id_to_name = CV_Category::get_cvtag_to_name_array();


include("cv_master_main.phtml");

?>
