<?php
include("../../../framework/framework_masterinclude.php");
include_once ("calendar_include.php");
$FRAMEWORK->authenticate($objSession, $MODULE_NAME, $USER, $BASE_DIR);

if(!(Rbac_User::IsAllowedTo($USER->GetUserID(), "access_module", "calendar_module")))
{
    echo "PERMISSION DENIED FOR MODULE!!";
    exit;
}

//Get our calendar_user
$calendar_user = new Calendar_User($USER->GetUserID());
//Get user's settings
$calendar_config = new Calendar_config();

$calendar = new Calendar();
$calendar->setDate($FRAMEWORK->getValue ( "date", "[0-9]+" ) );
$calendar->setToday($calendar->getDate() );


$next = mktime ( 3, 0, 0, $calendar->getThisMonth() + 1, 1, $calendar->getThisYear() );
$nextyear = date ( "Y", $next );
$nextmonth = date ( "m", $next );
//$nextdate = date ( "Ymd" );

$prev = mktime ( 3, 0, 0, $calendar->getThisMonth() - 1, 1, $calendar->getThisYear() );
$prevyear = date ( "Y", $prev );
$prevmonth = date ( "m", $prev );
//$prevdate = date ( "Ymd" );

//I am not sure what $boldDays does
$bold_days_in_year = 'N';
if ( ! empty ( $bold_days_in_year ) && $bold_days_in_year == 'Y' ) {
  $boldDays = true;
  $startdate = sprintf ( "%04d%02d01", $prevyear, $prevmonth );
  $enddate = sprintf ( "%04d%02d31", $nextyear, $nextmonth );
} else {
  $boldDays = false;
  $startdate = sprintf ( "%04d%02d01", $calendar->getThisYear(), $calendar->getThisMonth() );
  $enddate = sprintf ( "%04d%02d31", $calendar->getThisYear(), $calendar->getThisMonth() );
}

$HeadX = '';
if (@ $auto_refresh == "Y" && ! empty ( $auto_refresh_time ) ) {
  $refresh = $auto_refresh_time * 60; // convert to seconds
  $HeadX = "<meta http-equiv=\"refresh\" content=\"$refresh; url=month.php?$u_url" .
    "year=$thisyear&amp;month=$thismonth$caturl" . 
    ( ! empty ( $friendly ) ? "&amp;friendly=1" : "") . "\" />\n";
}

$calendar_event = new Calendar_Event();
$calendar_event->setTZOffset($calendar_config->getTZOffset());

/* Pre-Load the repeated events for quicker access */
$repeated_events = $calendar_event->read_repeated_events ($calendar->getCalendarId(), $calendar->getCatId(), $startdate );

/* Pre-load the non-repeating events for quicker access */
$events = $calendar_event->read_events ( $calendar->getCalendarId(), $startdate, $enddate, $calendar->getCatId() );

include_once 'month.phtml';
