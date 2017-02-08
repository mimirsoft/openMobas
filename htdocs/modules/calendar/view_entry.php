<?php
/*
 * $Id: view_entry.php,v 1.68.2.2 2005/08/10 14:35:15 cknudsen Exp $
 *
 * Description:
 * Presents page to view an event with links to edit, delete
 * confirm, copy, add event
 *
 * Input Parameters:
 * id (*) - cal_id of requested event
 * date  - yyyymmdd format of requested event
 * user  - user to display
 * (*) required field
 */
error_reporting(E_ALL);
ini_set('display_errors', '1');

include("../../../framework/framework_masterinclude.php");
Framework::authenticate();
include_once ("includes/calendar_include.php");

include_once 'includes/site_extras.php';

// make sure this user is allowed to look at this calendar.
$can_view = false;
$is_my_event = false;

if ( $is_admin || $is_nonuser_admin || $is_assistant ) {
  $can_view = true;
} 

$error = '';
if ( empty ( $id ) || $id <= 0 || ! is_numeric ( $id ) ) 
{
  $error = translate ( "Invalid entry id" ) . "."; 
}

if ( empty ( $error ) ) {
    
    // is this user a participant or the creator of the event?
    if ($calendar->isCreatorOrParticipant($login,$id)) 
    {
        $can_view = true;
        $is_my_event = true;
    }
    if ( ($login != "__public__") && ($public_access_others == "Y") ) 
    {
        $can_view = true;
    }

    if ( ! $can_view ) 
    {
        $check_group = false;
        // if not a participant in the event, must be allowed to look at
        // other user's calendar.
        if ( $login == "__public__" ) 
        {
            if ( $public_access_others == "Y" ) {
                $check_group = true;
            }
        } 
        else {
            if ( $allow_view_other == "Y" ) {
                $check_group = true;
            }
        }
        // If $check_group is true now, it means this user can look at the
        // event only if they are in the same group as some of the people in
        // the event.
        // This gets kind of tricky.  If there is a participant from a different
        // group, do we still show it?  For now, the answer is no.
        // This could be configurable somehow, but how many lines of text would
        // it need in the admin page to describe this scenario?  Would confuse
        // 99.9% of users.
        // In summary, make sure at least one event participant is in one of
        // this user's groups.
        $my_users = get_my_users ();
        if ( is_array ( $my_users ) ) {
          $sql = "SELECT webcal_entry.cal_id FROM webcal_entry, " .
            "webcal_entry_user WHERE webcal_entry.cal_id = " .
            "webcal_entry_user.cal_id AND webcal_entry.cal_id = $id " .
            "AND webcal_entry_user.user_id IN ( ";
          for ( $i = 0; $i < count ( $my_users ); $i++ ) {
            if ( $i > 0 ) {
              $sql .= ", ";
            }
            $sql .= "'" . $my_users[$i]['user_id'] . "'";
            }
          $sql .= " )";
          $stmt = $dbh->prepare($sql);
          if ( $stmt->execute() ) {
            $row = $stmt->fetch_row ( );
            if ( $row && $row[0] > 0 ) {
              $can_view = true;
            }
          }
        }
        // If we didn't indicate we need to check groups, then this user
        // can't view this event.
        if ( ! $check_group ) 
        {
              $can_view = false;
        }
    }
}

// If they still cannot view, make sure they are not looking at a nonuser
// calendar event where the nonuser is the _only_ participant.
if ( empty ( $error ) && ! $can_view && ! empty ( $nonuser_enabled ) &&
  $nonuser_enabled == 'Y' ) {
  $nonusers = get_nonuser_cals ();
  $nonuser_lookup = array ();
  for ( $i = 0; $i < count ( $nonusers ); $i++ ) {
    $nonuser_lookup[$nonusers[$i]['user_id']] = 1;
  }
  $sql = "SELECT user_id FROM webcal_entry_user " .
    "WHERE cal_id = $id AND cal_status in ('A','W')";
  $stmt = $dbh->prepare($sql);
  $found_nonuser_cal = false;
  $found_reg_user = false;
  if ( $stmt->execute() ) {
    while ( $row =  $stmt->fetch_row ( ) ) {
      if ( ! empty ( $nonuser_lookup[$row[0]] ) ) {
        $found_nonuser_cal = true;
      } else {
        $found_reg_user = true;
      }
    }
  }
  // Does this event contain only nonuser calendars as participants?
  // If so, then grant access.
  if ( $found_nonuser_cal && ! $found_reg_user ) {
    $can_view = true;
  }
}
  
if ( empty ( $error ) && ! $can_view ) {
  $error = translate ( "You are not authorized" );
}

if ( ! empty ( $year ) ) {
  $thisyear = $year;
}
if ( ! empty ( $month ) ) {
  $thismonth = $month;
}
$pri[1] = translate("Low");
$pri[2] = translate("Medium");
$pri[3] = translate("High");

$unapproved = FALSE;

// Make sure this is not a continuation event.
// If it is, redirect the user to the original event.
$ext_id = -1;
if ( empty ( $error ) ) {

$dbh = new DB_Mysql();
$stmt = $dbh->prepare("SELECT cal_ext_for_id FROM webcal_entry " .
    "WHERE cal_id = $id");
  if ( $stmt->execute() ) {
    if ( $row = $stmt->fetch_row() ) {
      $ext_id = $row[0];
    }
  } else {
    // db error... ignore it, I guess.
  }
}

if ( $ext_id > 0 ) {
  $url = "view_entry.php?id=$ext_id";
  if ( $date != "" ) {
    $url .= "&amp;date=$date";
  }
  if ( $user != "" ) {
    $url .= "&amp;user=$user";
  }
  if ( $cat_id != "" ) {
    $url .= "&amp;cat_id=$cat_id";
  }
  do_redirect ( $url );
}


include_once 'view_entry.phtml';
