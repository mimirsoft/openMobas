<?php
include("../../../framework/framework_masterinclude.php");
Framework::authenticate('Unlimited');
include_once ("includes/calendar_include.php");
include_once 'includes/init.php';
load_user_categories();

$error = "";

if ( $readonly == 'Y' ) {
  $error = translate("You are not authorized");
}

// Allow administrators to approve public events
if ( $public_access == "Y" && ! empty ( $public ) && $is_admin )
  $app_user = "__public__";
else
  $app_user = ( $is_assistant || $is_nonuser_admin ? $user : $login );

if ( empty ( $error ) && $id > 0 ) {

$dbh = new DB_Mysql();

 $sql = "UPDATE webcal_entry_user SET cal_status = 'R' " .
    "WHERE user_id = '$app_user' AND cal_id = $id";
$stmt = $dbh->prepare($sql);
  if ( ! $stmt->execute() ) {
    $error = translate("Error approving event") . ": " . dbi_error ();
  } else {
    activity_log ( $id, $login, $app_user, $LOG_REJECT, "" );
  }

  // Update any extension events related to this one.
 $sql = "SELECT cal_id FROM webcal_entry " .
    "WHERE cal_ext_for_id = $id";
$stmt = $dbh->prepare($sql);
  if ( $stmt->execute() ) {
    if ( $row =  $stmt->fetch_row() ) {
      $ext_id = $row[0];

        $sql ="UPDATE webcal_entry_user SET cal_status = 'R' " .
        "WHERE cal_login = '$app_user' AND cal_id = $ext_id" ;
        $stmt = $dbh->prepare($sql);

      if ( ! $stmt->execute() ) {
        $error = translate("Error approving event") . ": " . dbi_error ();
      } 
    }
  }

  // Email participants to notify that it was rejected.
  // Get list of participants
  $sql = "SELECT user_id FROM webcal_entry_user WHERE cal_id = $id and cal_status = 'A'";
    $stmt = $dbh->prepare($sql);

  //echo $sql."<br />";
  if ( $stmt->execute() ) {
    while ( $row =  $stmt->fetch_row() )
      $partlogin[] = $row[0];
  }

  // Get the name of the event
  $sql = "SELECT cal_name, cal_description, cal_date, cal_time FROM webcal_entry WHERE cal_id = $id";
    $stmt = $dbh->prepare($sql);
  if ( $stmt->execute() ) {
    $row =$stmt->fetch_row();
    $name = $row[0];
    $description = $row[1];
    $fmtdate = $row[2];
    $time = $row[3];
  }

  if ($time != '-1') {
    $hour = substr($time,0,2);
    $minute = substr($time,2,2);
  }

  for ( $i = 0; $i < count ( $partlogin ); $i++ ) {
    // does this user want email for this?
    $send_user_mail = get_pref_setting ( $partlogin[$i],
      "EMAIL_EVENT_REJECTED" );
     user_load_variables ( $partlogin[$i], "temp" );
    $user_language = get_pref_setting ( $partlogin[$i], "LANGUAGE" );
    if ( $send_user_mail == "Y" && strlen ( $tempemail ) &&
      $send_email != "N" ) {
        if (($GLOBALS['LANGUAGE'] != $user_language) && ! empty ( $user_language ) && ( $user_language != 'none' )){
          reset_language ( $user_language );
        }
        $msg = translate("Hello") . ", " . $tempfullname . ".\n\n" .
        translate("An appointment has been rejected by") .
        " " . $login_fullname .  ". " .
        translate("The subject was") . " \"" . $name . " \"\n" .
        translate("The description is") . " \"" . $description . "\"\n" .
        translate("Date") . ": " . date_to_str ( $fmtdate ) . "\n" .
        ( ( empty ( $hour ) && empty ( $minute ) ) ? "" :
        translate("Time") . ": " .
        display_time ( ( $hour * 10000 ) + ( $minute * 100 ) ) ) .
        "\n\n\n";
      if ( ! empty ( $server_url ) ) {
        $url = $server_url .  "view_entry.php?id=" .  $id;
        $msg .= "\n\n" . $url;
      }

      $from = $email_fallback_from;
      if ( strlen ( $login_email ) )
        $from = $login_email;

      $extra_hdrs = "From: $from\r\nX-Mailer: " . translate("Title");

      mail ( $tempemail,
        translate($application_name) . " " . translate("Notification") . ": " . $name,
        html_to_8bits ($msg), $extra_hdrs );
      activity_log ( $id, $login, $partlogin[$i], $LOG_NOTIFICATION,
        "Event rejected by $app_user" );
    }
  }
}

if ( empty ( $error ) ) {
  if ( @$ret == "list" )
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
