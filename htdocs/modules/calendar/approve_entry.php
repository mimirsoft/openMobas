<?php
include("../../../framework/framework_masterinclude.php");
Framework::authenticate('Unlimited');
include_once ("includes/calendar_include.php");
include_once 'includes/init.php';
load_user_categories();

$error = "";
$dbh = new DB_Mysql();

if ( $readonly == 'Y' ) {
  $error = translate("You are not authorized");
}

// Allow administrators to approve public events
if ( $public_access == "Y" && ! empty ( $public ) && $is_admin )
  $app_user = "__public__";
else
  $app_user = ( $is_assistant || $is_nonuser_admin ? $user : $login );

if ( empty ( $error ) && $id > 0 ) {


$sql = "UPDATE webcal_entry_user SET cal_status = 'A' " .
    "WHERE user_id = '$app_user' AND cal_id = $id";
$stmt = $dbh->prepare( $sql);
  if ( ! $stmt->execute ( ) ) {
    $error = translate("Error approving event") . ": " . dbi_error ();
  } else {
    activity_log ( $id, $login, $app_user, $LOG_APPROVE, "" );
  }
  // Update any extension events related to this one.
  $sql =  "SELECT cal_id FROM webcal_entry " .
    "WHERE cal_ext_for_id = $id";
$stmt =  $dbh->prepare( $sql);
 
    if (  $stmt->execute ( ) ) {
    if ( $row =  $stmt->fetch_row() ) {
      $ext_id = $row[0];
      $sql = "UPDATE webcal_entry_user SET cal_status = 'A' " .
        "WHERE cal_login = '$app_user' AND cal_id = $ext_id";
    $stmt =  $dbh->prepare( $sql);
    if ( !  $stmt->execute ( ) ) {
        $error = translate("Error approving event") . ": " . dbi_error ();
      }
    }
  }
}

if ( empty ( $error ) ) {
  if ( ! empty ( $ret ) && $ret == "list" )
    do_redirect ( "list_unapproved.php?user=$app_user" );
  else
    do_redirect ( "view_entry.php?id=$id&amp;user=$app_user" );
  exit;
}
print_header ();
echo "<h2>" . translate("Error") . "</h2>\n";
echo "<p>" . $error . "</p>\n";
print_trailer ();
?>
