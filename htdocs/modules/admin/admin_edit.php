<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once("../../../../openMobas/framework_masterinclude.php");
require_once("admin_include.php");
$FRAMEWORK->authenticate($objSession, $MODULE_NAME, $USER, $BASE_DIR);


if(!$rbac_user->IsAllowedTo($USER->GetUserID(), "access_module", "admin_module"))
{
    echo "Permission Denied to access admin module.";
    exit;
}

//We initialize all our variables.
$user_firstname =   '';
$user_lastname =    '';
$username =         '';
$user_default_phone =         '';
$md5_pw =           '';

@$ACTION = $_POST['ACTION'];
@$user_id = $_POST['user_id'];
@$username = $_POST['username'];
@$user_lastname = $_POST['user_lastname'];
@$user_firstname = $_POST['user_firstname'];
@$user_email = $_POST['user_email'];
@$user_default_phone = $_POST['user_default_phone'];
@$md5_pw = $_POST['md5_pw'];
@$md5_pw2 = $_POST['md5_pw2'];
@$update_pass = $_POST['update_pass'];
$WARNING['show'] = false;


switch ($ACTION)
{
    case "Save User":
        $USER->update_user($user_id, $username, $user_lastname, $user_firstname , $user_email, $user_default_phone);
        if($update_pass)
        {
            if($md5_pw == $md5_pw2)
            {
                if($md5_pw != "")
                {
                    echo "WOOT, updating password";
                	$USER->set_password($user_id, $md5_pw);
                }
                else
                {
                    $WARNING['show'] = true;
                    $WARNING['message'] = "PASSWORDS CANNOT BE EMPTY";
                }
                
            }
            else
            {
                $WARNING['show'] = true;
                $WARNING['message'] = "PASSWORDS DO NOT MATCH";
            }
        }
        break;
    case "Delete User":
        $USER->delete_user($user_id);
        break;
}
$user = new User($dbh, $user_id, '', '');
    

include("admin_edit.phtml");

?>
