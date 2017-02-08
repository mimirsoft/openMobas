<?php
require_once("../../../framework/framework_masterinclude.php");
require_once("profile_include.php");
$FRAMEWORK->authenticate($objSession, $MODULE_NAME, $USER, $BASE_DIR);
if(!Rbac_User::IsAllowedTo($USER->GetUserID(), "access_module", "profile_module"))
{
    echo "Permission Denied to access check module.";
    exit;
}

//We initialize all our variables.
$user_firstname =   '';
$user_lastname =    '';
$ACTION = '';
$update_err = '';
$WARNING['show'] = false;
foreach ($_POST as $key => $value)
{
    $$key = $value;
    unset($_POST->{$key});
}
$user_id = $USER->GetUserID();

switch ($ACTION)
{
    case "Save User":
        if($old_pass != "")
        {
            $update_err = User::update_user_password($user_id, $old_pass, $new_pass1, $new_pass2);
            $WARNING['show'] = true;
            $WARNING['message'] = "PASSWORD CHANGED";
            
        }
        User::update_user_info($user_id, $user_lastname, $user_firstname);
        break;
}

$user = User::get_user_from_id($user_id);
    
include("profile_main.phtml");

?>
