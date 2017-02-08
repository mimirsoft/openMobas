<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

include("../../../framework/framework_masterinclude.php");
Framework::authenticate('');
include_once ("calendar_include.php");


if(!(Rbac_User::IsAllowedTo($USER->GetUserID(), "access_module", "calendar_module")))
{
    echo "PERMISSION DENIED TO ACCESS MODULE CALENDAR!!";
    exit;
}

//if (($calendar->getUser() != $USER->GetUserID()) && $is_nonuser_admin) {
   $calendar_settings->load_user_layers ($calendar->getUser(), 0, $calendar->getLogin());
//} else {
//   $helper->load_user_layers ();
//}

if ($calendar->getUser() == "") {
    $cal_title_user = $USER->GetUserID();
} else {
    $cal_title_user = $calendar->getUser();
}

$calendar_settings->load_user_categories("", $calendar->getUser(), $calendar->getLogin(), $calendar->getIsAssistant(), $calendar->getIsAdmin());

$dbh = new DB_Mysql();
$users = Rbac_User::getAllAllowedTo("access_module", "calendar_module");
$user_Array = framework::getUserArray();	
$r2 = $calendar->get_nonuser_cals_array();
foreach($user_Array as $key=>$value)
{
    $userArray[$key]=$value;
}
foreach($r2 as $key=>$value)
{
    $userArray[$key]=$value;
}

$next = mktime ( 3, 0, 0, $calendar->getThisMonth(), $calendar->getThisDay() + 7, $calendar->getThisYear() );
$prev = mktime ( 3, 0, 0, $calendar->getThisMonth(), $calendar->getThisDay() - 7, $calendar->getThisYear() );

// We add 2 hours on to the time so that the switch to DST doesn't
// throw us off.  So, all our dates are 2AM for that day.
if ($calendar_settings->getWeekStart() == 1 ) {
   $wkstart = $calendar->get_monday_before ( $calendar->getThisYear(), $calendar->getThisMonth(), $calendar->getThisDay() );
} else {
   $wkstart = $calendar->get_sunday_before ( $calendar->getThisYear(), $calendar->getThisMonth(), $calendar->getThisDay() );
}

$wkend = $wkstart + ( 3600 * 24 * 6 );
 
$startdate = date ( "Ymd", $wkstart );
$enddate = date ( "Ymd", $wkend );

if ( ! empty ( $calendar_settings->DISPLAY_WEEKENDS ) && $calendar_settings->DISPLAY_WEEKENDS == "N" )
{
    if ( $calendar_settings->getWeekStart() == 1 ) {
        $start_ind = 0;
        $end_ind = 5;
    } else {
        $start_ind = 1;
        $end_ind = 6;
    }
} else {
    $start_ind = 0;
    $end_ind = 7;
}

$HeadX = '';
if ( ! empty ( $auto_refresh ) && $auto_refresh == "Y" && ! empty ( $auto_refresh_time ) ) 
{
    $refresh = $auto_refresh_time * 60; // convert to seconds
    $HeadX = "<meta http-equiv=\"refresh\" content=\"$refresh; url=week.php?$u_url" ."date=$startdate$caturl" . 
        ( ! empty ( $friendly ) ? "&amp;friendly=1" : "") . "\" />\n";
}

$INC = array('/js/popups.php');

/* Pre-Load the repeated events for quckier access */
$repeated_events = $calendar_event->read_repeated_events($calendar->getUser(), $cat_id, $startdate );

/* Pre-load the non-repeating events for quicker access */
$events = $calendar->read_events ( strlen ( $user ) ? $user : $USER->GetUserID(), $startdate, $enddate, $cat_id );

for ( $i = 0; $i < 7; $i++ ) 
{
    $days[$i] = $wkstart + ( 24 * 3600 ) * $i;
    $weekdays[$i] = $calendar->weekday_short_name ( ( $i + $helper->WEEK_START ) % 7 );
    $header[$i] = $weekdays[$i] . "<br />\n" . $calendar->date_to_str ( date ( "Ymd", $days[$i] ), $helper->DATE_FORMAT_MD, false );
}

include_once 'week.phtml';

?>