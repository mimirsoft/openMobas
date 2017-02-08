<?php
require_once("../../../../openMobas/framework_masterinclude.php");
require_once("../admin/admin_include.php");
//create role admin, run the admin rbac
error_reporting(E_ALL);
ini_set('display_errors', '1');

$ADMINUSER = "admin";
$ADMINPASS = "cde3xsw2ott9gufo";
$ADMINPASS = md5($ADMINPASS);
$ADMINLASTNAME = "Kevin";
$ADMINFIRSTNAME = "Milhoan";
$NULL = '';
$user_id = User::create_user("NULL", $ADMINUSER, $ADMINPASS, $ADMINLASTNAME, $ADMINFIRSTNAME, "kevin@kmrealtysd.com", "");

Admin_System::verify_rbac('admin');
$admin_role_id = RBAC::create_roles("NULL", "admin", "System Wide Admin can do anything!", 100);
//get the all_actions id
$privilege_id = RBAC::get_privilege_by_name("all_actions");
//get the all_objects id
$domain_id = RBAC::get_domain_by_name("all_objects");
// give the roll the permission pair
RBAC::create_permission("NULL", $admin_role_id, $privilege_id['privilege_id'], $domain_id['domain_id'], 1);
// give the user the role
RBAC::create_users_mtm_roles("NULL", $user_id, $admin_role_id);

echo "it worked";

?>