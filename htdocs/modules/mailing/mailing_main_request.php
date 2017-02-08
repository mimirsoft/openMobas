<?php
//error_reporting(E_ALL);
//ini_set('display_errors', '1');
include("../../../framework/framework_masterinclude.php");
include("mailing_include.php");
Framework::XML_authenticate('');//the two includes must be before the authentica, to supply the needed module name for authentication


$ACTION = '';
$VALUE = '';
$VALUE2 = '';
foreach($_POST as $key => $value)
{
    $$key = $value;
}
foreach($_GET as $key => $value)
{
    $$key = $value;
}
switch($ACTION)
{
    case "target":
        switch($VALUE)
        {

            case "all_customers":
                $customers = CV_Main::getall_customers();
            	$render = "<addrow>";
                $render .= "<removerow>target_specific_row</removerow>";
                $render .= "</addrow>";
                break;
            case "select_customers":
                $customers = CV_Main::getall_customers();
                $render = "<addrow>";
                $render .= "<multiple>multiple</multiple>";
                $render .= "<removerow>target_specific_row</removerow>";
                $render .= "<previousrow>target_row</previousrow>";
                $render .= "<label>Select Address:</label>";
                $render .= "<rowname>target_specific_row</rowname>";
                $render .= "<rowformname>target_specific[]</rowformname>";
                foreach($customers as $row)
                {
                    $render .= "<id>".$row['cv_id']."</id><name>".framework::XML_Replace($row['cv_name'])." ".$row['cv_default_address']." ".$row['cv_default_city']." ".$row['cv_default_state']." ".$row['cv_default_zip']."</name>";
                }
                $render .= "</addrow>";
                break;
                case "fillin":
                $render = "<addrow>";
                $render .= "<removerow>target_specific_row</removerow>";
                $render .= "</addrow>";
                break;
            case "tagged":
                //$customers = CV_Main::getall_with_tag();
                $CV_Categories = CV_Category::get_all();
                $render = "<addrow>";
                $render .= "<multiple>multiple</multiple>";
                $render .= "<removerow>target_specific_row</removerow>";
                $render .= "<previousrow>target_row</previousrow>";
                $render .= "<label>With Tag:</label>";
                $render .= "<rowname>target_specific_row</rowname>";
                $render .= "<rowformname>tag</rowformname>";
                foreach($CV_Categories as $row)
                {
                    $render .= "<id>".$row['cv_category_id']."</id><name>".framework::XML_Replace($row['cv_category_name'])."</name>";
                }
                $render .= "</addrow>";
            break;
                case "fillin":
                $render = "<addrow>";
                $render .= "<removerow>target_specific_row</removerow>";
                $render .= "</addrow>";
                break;
                case "null":
                $render = "<addrow>";
                $render .= "<removerow>target_specific_row</removerow>";
                $render .= "</addrow>";
                break;
        }
        header('Content-Type: text/xml');
        echo $render;            
        break;
    case "load_mailing":
        $mailing = mailing::get_mailing_from_id($VALUE);
        $render = $mailing['mailing_body'];
        header('Content-Type: text/html');
        echo $render;            

}


?>
