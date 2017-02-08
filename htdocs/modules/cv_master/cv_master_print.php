<?
require_once("../../../framework/framework_masterinclude.php");
require_once("cv_master_include.php");
require_once("CV_Main.class.php");
require_once("CV_Category.class.php");

Framework::authenticate();//the two includes must be before the authentica, to supply the needed module name for authentication

$name = '';
$sort = "name";
$search_tag = "none";

if(isset($_GET['SEARCH']))
{
    @$name = $_GET['name'];
    @$SEARCH = $_GET['SEARCH'];
    @$sort = $_GET['sort'];
    @$search_tag = $_GET['search_tag'];
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
            require_once("../invoices/Invoice.php");
            
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

$tag_id_to_name = CV_Category::get_cvtag_to_name_array();

include("cv_master_print.phtml");

?>
