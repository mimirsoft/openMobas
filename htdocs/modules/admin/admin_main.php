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
$user_id = '';
$user_firstname =   '';
$user_lastname =    '';
$username =         '';
$user_email =         '';
$user_default_phone =         '';
$md5_pw =           '';
$SORTBY = 'username';
$ACTION = '';

if(isset($_POST['ACTION']))
{
    @$user_firstname =   $_POST['user_firstname'];
    @$user_lastname =    $_POST['user_lastname'];
    @$username =         $_POST['username'];
    @$user_email =         $_POST['user_email'];
    @$user_default_phone =        $_POST['user_default_phone'];
    @$confirm_code =        $_POST['confirm_code'];
    @$md5_pw =           $_POST['md5_pw'];
    $ACTION = $_POST['ACTION'];
}
if(isset($_GET['SORTBY']))
{
    $SORTBY = $_GET['SORTBY'];
}
    
switch($ACTION)
{
    case "Add User":
        if ($username != "")
        {
            if($rbac_user->IsAllowedTo($USER->GetUserID(), "create_users", "admin_module"))
            {
                $md5_pw = md5($md5_pw);
                $USER->create_user($user_id, $username, $md5_pw, $user_lastname, $user_firstname, $user_email, $user_default_phone);
            }
        }
    break;
    case "Confirm":
        if($rbac_user->IsAllowedTo($USER->GetUserID(), "create_users", "admin_module"))
        {
            $stmt = $dbh->prepare("SELECT * FROM temp_members_db WHERE confirm_code=:1:");
            $stmt->execute($confirm_code);
            $rows=$stmt->fetch_assoc();
            $new_user_id = $USER->create_user("", $rows['username'], $rows['hashed_password'], $rows['lastname'], $rows['firstname'], $rows['email'], $rows['phone'] );
            $USER->update_interested_item($new_user_id, $rows['interested_item']);
            $stmt = $dbh->prepare("DELETE FROM temp_members_db WHERE confirm_code=:1:");
            $stmt->execute($confirm_code);
         }
    break;
    case "Delete Unconfirmed User":
        if($rbac_user->IsAllowedTo($USER->GetUserID(), "create_users", "admin_module"))
        {
            $stmt = $dbh->prepare("DELETE FROM temp_members_db WHERE confirm_code=:1:");
            $stmt->execute($confirm_code);
        }
    break;
}

$users = $USER->get_users($SORTBY);
$unconfirmed_users = $USER->get_temp_users();


include("admin_main.phtml");

?>