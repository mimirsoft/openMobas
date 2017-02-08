<?php
/*
 * $Id: edit_entry.php,v 1.91.2.2 2006/03/15 03:14:54 umcesrjones Exp $
 *
 * Description:
 * Presents page to edit/add an event
 *
 * Notes:
 * If htmlarea is installed, users can use WYSIWYG editing.
 * SysAdmin must enable HTML for event full descriptions.
 * The htmlarea files should be installed so that the htmlarea.php
 * file is in ../includes/htmlarea/htmlarea.php
 * The htmlarea code can be downloaded from:
 *  http://www.htmlarea.com
 * TODO
 * This file will not pass XHTML validation with HTMLArea enabled
 */
error_reporting(E_ALL);
ini_set('display_errors', '1');
include("../../../framework/framework_masterinclude.php");
Framework::authenticate('Unlimited');
include_once ("includes/calendar_include.php");
include_once 'includes/site_extras.php';

load_user_categories ();

// Default for using tabs is enabled
if ( empty ( $EVENT_EDIT_TABS ) )
  $EVENT_EDIT_TABS = 'Y'; // default
$useTabs = ( $EVENT_EDIT_TABS == 'Y' );

// make sure this is not a read-only calendar
$can_edit = false;

// Public access can only add events, not edit.
if ( $login == "__public__" && $id > 0 ) {
  $id = 0;
}

$external_users = "";
$participants = array ();
$dbh = new DB_Mysql();

if ( ! empty ( $id ) && $id > 0 ) {
  // first see who has access to edit this entry
  if ( $is_admin ) {
    $can_edit = true;
  } else {
    $can_edit = false;
    if ( $readonly == "N" || $is_admin ) {
      $sql = "SELECT webcal_entry.cal_id FROM webcal_entry, " .
        "webcal_entry_user WHERE webcal_entry.cal_id = " .
        "webcal_entry_user.cal_id AND webcal_entry.cal_id = $id " .
        "AND (webcal_entry.cal_create_by = '$login' " .
        "OR webcal_entry_user.user_id = '$login')";
        $stmt = $dbh->prepare( $sql);
      if( $stmt->execute() ) {
        $row = $stmt->fetch_row (  );
        if ( $row && $row[0] > 0 )
          $can_edit = true;
      }
    }
  }
  $sql = "SELECT cal_create_by, cal_date, cal_time, cal_mod_date, " .
    "cal_mod_time, cal_duration, cal_priority, cal_type, cal_access, " .
    "cal_name, cal_description, cal_group_id FROM webcal_entry WHERE cal_id = " . $id;
$stmt = $dbh->prepare( $sql);
  if ( $stmt->execute() ) {
    $row = $stmt->fetch_row (  );
    if ( ! empty ( $override ) && ! empty ( $date ) ) {
      // Leave $cal_date to what was set in URL with date=YYYYMMDD
      $cal_date = $date;
    } else {
      $cal_date = $row[1];
    }
    $create_by = $row[0];
    if (( $user == $create_by ) && ( $is_assistant || $is_nonuser_admin )) $can_edit = true;
    
    $year = (int) ( $cal_date / 10000 );
    $month = ( $cal_date / 100 ) % 100;
    $day = $cal_date % 100;
    $time = $row[2];
    // test for AllDay event, if so, don't adjust time
    if ( $time > 0  || ( $time == 0 &&  $row[5] != 1440 ) ) { /* -1 = no time specified */
      $time += ( ! empty ( $TZ_OFFSET )?$TZ_OFFSET : 0)  * 10000;
      if ( $time > 240000 ) {
        $time -= 240000;
        $gmt = mktime ( 3, 0, 0, $month, $day, $year );
        $gmt += $ONE_DAY;
        $month = date ( "m", $gmt );
        $day = date ( "d", $gmt );
        $year = date ( "Y", $gmt );
      } else if ( $time < 0 ) {
        $time += 240000;
        $gmt = mktime ( 3, 0, 0, $month, $day, $year );
        $gmt -= $ONE_DAY;
        $month = date ( "m", $gmt );
        $day = date ( "d", $gmt );
        $year = date ( "Y", $gmt );
      }
      // Set alterted date
      $cal_date = sprintf("%04d%02d%02d",$year,$month,$day);
    }
    if ( $time >= 0 ) {
      $hour = floor($time / 10000);
      $minute = ( $time / 100 ) % 100;
      $duration = $row[5];
    } else {
      $duration = "";
      $hour = -1;
    }
    $priority = $row[6];
    $type = $row[7];
    $access = $row[8];
    $name = $row[9];
    $description = $row[10];
    $parent = $row[11];
    // check for repeating event info...
    // but not if we are overriding a single entry of an already repeating
    // event... confusing, eh?
    if ( ! empty ( $override ) ) {
      $rpt_type = "none";
      $rpt_end = 0;
      $rpt_end_date = $cal_date;
      $rpt_freq = 1;
      $rpt_days = "nnnnnnn";
      $rpt_sun = $rpt_mon = $rpt_tue = $rpt_wed =
        $rpt_thu = $rpt_fri = $rpt_sat = false;
    } else {
$stmt = $dbh->prepare("SELECT cal_id, cal_type, cal_end, " .
        "cal_frequency, cal_days FROM webcal_entry_repeats " .
        "WHERE cal_id = $id");
      if ( $stmt->execute() ) {
        if ( $row = $stmt->fetch_row (  ) ) {
          $rpt_type = $row[1];
          if ( $row[2] > 0 )
            $rpt_end = date_to_epoch ( $row[2] );
          else
            $rpt_end = 0;
          $rpt_end_date = $row[2];
          $rpt_freq = $row[3];
          $rpt_days = $row[4];
          $rpt_sun  = ( substr ( $rpt_days, 0, 1 ) == 'y' );
          $rpt_mon  = ( substr ( $rpt_days, 1, 1 ) == 'y' );
          $rpt_tue  = ( substr ( $rpt_days, 2, 1 ) == 'y' );
          $rpt_wed  = ( substr ( $rpt_days, 3, 1 ) == 'y' );
          $rpt_thu  = ( substr ( $rpt_days, 4, 1 ) == 'y' );
          $rpt_fri  = ( substr ( $rpt_days, 5, 1 ) == 'y' );
          $rpt_sat  = ( substr ( $rpt_days, 6, 1 ) == 'y' );
        }
      }
    }
    
  }
  $sql = "SELECT user_id, cal_category FROM webcal_entry_user WHERE cal_id = $id";
$dbh = new DB_Mysql();
$stmt = $dbh->prepare($sql);
  if ( $stmt->execute()) {
    while ( $row = $stmt->fetch_row() ) {
      $participants[$row[0]] = 1;
      if ($login == $row[0]) $cat_id = $row[1];
      if ( ( $is_assistant  || $is_admin ) && $user == $row[0]) $cat_id = $row[1];
    }
  }
  if ( ! empty ( $allow_external_users ) && $allow_external_users == "Y" ) {
    $external_users = event_get_external_users ( $id );
  }
} else {
  // New event.
  $id = 0; // to avoid warnings below about use of undefined var
  // Anything other then testing for strlen breaks either hour=0 or no hour in URL
  if ( strlen ( $hour ) ) {
    $time = $hour * 100;
  } else {
    $time = -1;
    $hour = -1;
  }
  if ( ! empty ( $defusers ) ) {
    $tmp_ar = explode ( ",", $defusers );
    for ( $i = 0; $i < count ( $tmp_ar ); $i++ ) {
      $participants[$tmp_ar[$i]] = 1;
    }
  }
    $can_edit = true;

}
if ( ! empty ( $year ) && $year )
  $thisyear = $year;
if ( ! empty ( $month ) && $month )
  $thismonth = $month;
if ( ! empty ( $day ) && $day )
  $thisday = $day;
if ( empty ( $rpt_type ) || ! $rpt_type )
  $rpt_type = "none";

// avoid error for using undefined vars
if ( ! isset ( $hour ) )
  $hour = -1;
if ( empty ( $duration ) )
  $duration = 0;
if ( $duration == ( 24 * 60 ) ) {
  $hour = $minute = $duration = "";
  $allday = "Y";
} else
  $allday = "N";
if ( empty ( $name ) )
  $name = "";
if ( empty ( $description ) )
  $description = "";
if ( empty ( $priority ) )
  $priority = 0;
if ( empty ( $access ) )
  $access = "";
if ( empty ( $rpt_freq ) )
  $rpt_freq = 0;
if ( empty ( $rpt_end_date ) )
  $rpt_end_date = 0;

if ( ( empty ( $year ) || ! $year ) &&
  ( empty ( $month ) || ! $month ) &&
  ( ! empty ( $date ) && strlen ( $date ) ) ) {
  $thisyear = $year = substr ( $date, 0, 4 );
  $thismonth = $month = substr ( $date, 4, 2 );
  $thisday = $day = substr ( $date, 6, 2 );
  $cal_date = $date;
} else {
  if ( empty ( $cal_date ) )
    $cal_date = date ( "Ymd" );
}
if ( empty ( $thisyear ) )
  $thisdate = date ( "Ymd" );
else {
  $thisdate = sprintf ( "%04d%02d%02d",
    empty ( $thisyear ) ? date ( "Y" ) : $thisyear,
    empty ( $thismonth ) ? date ( "m" ) : $thismonth,
    empty ( $thisday ) ? date ( "d" ) : $thisday );
}
if ( empty ( $cal_date ) || ! $cal_date )
  $cal_date = $thisdate;

if ( $allow_html_description == "Y" ){
  // Allow HTML in description
  // If they have installed the htmlarea widget, make use of it
  $textareasize = 'rows="15" cols="50"';
  if ( file_exists ( "includes/htmlarea/htmlarea.php" ) ) {
    $BodyX = 'onload="initEditor();timetype_handler();rpttype_handler()"';
    $INC = array ( 'htmlarea/htmlarea.php', 'js/edit_entry.php',
      'js/visible.php', 'htmlarea/core.php' );
  } else {
    // No htmlarea files found...
    $BodyX = 'onload="timetype_handler();rpttype_handler()"';
    $INC = array ( 'js/edit_entry.php', 'js/visible.php' );
  }
} else {
  $textareasize = 'rows="5" cols="40"';
  $BodyX = 'onload="timetype_handler();rpttype_handler()"';
  $INC = array('js/edit_entry.php','js/visible.php');
}


include_once 'edit_entry.phtml';
