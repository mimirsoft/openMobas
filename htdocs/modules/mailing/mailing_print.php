<?php
include("../../../framework/framework_masterinclude.php");
include("mailing_include.php");
$FRAMEWORK->authenticate($objSession, $MODULE_NAME, $USER, $BASE_DIR);

$target_specific = '';

foreach ($_POST as $key => $value)
{
    $$key = $value;
    unset($_POST->{$key});
}
$date = $date_year."-".$date_month."-".$date_day;

$header = nl2br($header);
$body = nl2br($body);
$dbh = new DB_Mysql();
include("mailing_letter.css");
$careof = '';

if($target=="all_customers")
{
    $customers = CV_Main::getall_customers();
    $body_orig = $body;
    foreach($customers as $row)
    {
        $name = $row['cv_name'];
       	//Set Appropriate Vars
        $careof =   "";
        $address =  $row['cv_default_address'];
        $aptnum =    '';
        $city =     $row['cv_default_city'];
        $state =    $row['cv_default_state'];
        $zip =      $row['cv_default_zip'];
        $email =      $customer_info['cv_default_email'];
        $phone =      $customer_info['cv_default_phone'];
        $cv_users = CV_Main::getall_cv_users($target['cv_id']);
            $login_block = "<table>
                    <tr>
                        <td> Username
                        </td>
                        <td> Email
                        </td>
                    </tr>";   
            foreach($cv_users as $row)
            {
                $login_block .= "<tr>
                        <td>".$row['username']."</td>
                        <td>".$row['user_email']."</td></tr>";
            }
            $login_block .= "</table>";
        $body = $body_orig;
        $body = ereg_replace("::([a-zA-Z0-9_]+)::", "$\\1", $body);
        eval("\$body = \"$body\";");
        include("mailing_letter.phtml");
    }
}
if($target=="select_customers")
{
    $body_orig = $body;
    if(is_array($target_specific))
    {
    	$body_orig = $body;
    	foreach($target_specific as $target)
    	{
	        $customer_info = CV_Main::get_cv_from_id($target);
    		$name = $customer_info['cv_name'];
	        //Set Appropriate Vars
           	$careof =   $customer_info['cv_default_careof'];
            $address =  $customer_info['cv_default_address'];
            $aptnum =    '';
            $city =     $customer_info['cv_default_city'];
            $state =    $customer_info['cv_default_state'];
            $zip =      $customer_info['cv_default_zip'];
            $email =      $customer_info['cv_default_email'];
            $phone =      $customer_info['cv_default_phone'];
            $cv_users = CV_Main::getall_cv_users($target['cv_id']);
            $login_block = "<table>
                    <tr>
                        <td> Username
                        </td>
                        <td> Email
                        </td>
                    </tr>";   
            foreach($cv_users as $row)
            {
                $login_block .= "<tr>
                        <td>".$row['username']."</td>
                        <td>".$row['user_email']."</td></tr>";
            }
            $login_block .= "</table>";
            $body = $body_orig;
            $body = ereg_replace("::([a-zA-Z0-9_]+)::", "$\\1", $body);
            eval("\$body = \"$body\";");
            include("mailing_letter.phtml");
    	}
	}
}	
if($target=="tagged")
{
    $body_orig = $body;
    $target_specific = CV_Main::getall_with_tag($tag);
    if(is_array($target_specific))
    {
        $body_orig = $body;
        foreach($target_specific as $target)
        {
            $customer_info = CV_Main::get_cv_from_id($target['cv_id']);
            $name = $customer_info['cv_name'];
            //Set Appropriate Vars
            $careof =   $customer_info['cv_default_careof'];
            $address =  $customer_info['cv_default_address'];
            $aptnum =    '';
            $city =     $customer_info['cv_default_city'];
            $state =    $customer_info['cv_default_state'];
            $zip =      $customer_info['cv_default_zip'];
            $email =      $customer_info['cv_default_email'];
            $phone =      $customer_info['cv_default_phone'];
            $cv_users = CV_Main::getall_cv_users($target['cv_id']);
            $login_block = "<table>
                    <tr>
                        <td> Username
                        </td>
                        <td> Email
                        </td>
                    </tr>";   
            foreach($cv_users as $row)
            {
                $login_block .= "<tr>
                        <td>".$row['username']."</td>
                        <td>".$row['user_email']."</td></tr>";
            }
            $login_block .= "</table>";
            $body = $body_orig;
            $body = ereg_replace("::([a-zA-Z0-9_]+)::", "$\\1", $body);
            eval("\$body = \"$body\";");
            include("mailing_letter.phtml");
        }
    }
}   
if($target=="fillin")
{
    $aptnum =    '';
    $body = ereg_replace("::([a-zA-Z0-9_]+)::", "$\\1", $body);
    eval("\$body = \"$body\";");
    include("mailing_letter.phtml");
}

?>
