<?php
include("../../../framework/framework_masterinclude.php");
Framework::authenticate('Unlimited');
include_once ("includes/calendar_include.php");
include_once 'includes/init.php';


load_user_categories();

$error = "";

// Only proceed if id was passed
if ( $id > 0 ) {
$dbh = new DB_Mysql();

  // double check to make sure user doesn't already have the event
  $is_my_event = false;
  $sql = "SELECT cal_id FROM webcal_entry_user " .
    "WHERE user_id = '$login' AND cal_id = $id";
$stmt = $dbh->prepare($sql);
  if ( $stmt->execute() ) {
    $row = $stmt->fetch_row ( );
    if ( $row[0] == $id ) {
      $is_my_event = true;
      echo "Event # " . $id . " is already on your calendar.";
      exit;
    }
  }

  // Now lets make sure the user is allowed to add the event (not private)

  $sql = "SELECT cal_access FROM webcal_entry WHERE cal_id = " . $id;
$stmt = $dbh->prepare($sql);


  if ( ! $stmt->execute() ) {
    echo translate("Invalid entry id") . ": $id";
    exit;
  }
  $row = $stmt->fetch_row ( );

  if ( $row[0] == "R" && ! $is_my_event ) {
    $is_private = true;
    etranslate("This is a private event and may not be added to your calendar.");
    exit;
  } else {
    $is_private = false;
  }

  // add the event
  if ( $readonly == "N" && ! $is_my_event && ! $is_private )  {
    
$stmt = $dbh->prepare("INSERT INTO webcal_entry_user ( cal_id, user_id, cal_status ) VALUES ( $id, '$login', 'A' )"); 
if ( ! $stmt->execute() ) {
      $error = translate("Error adding event") . ": " . dbi_error ();
    }
  }
}

send_to_preferred_view ();
exit;
?>
