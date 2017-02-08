<?php
/*
 * 
    This file is part of WebPropMan
    Copyright (C) 2011, Kevin Milhoan

    WebPropMan is free software: you can redistribute it and/or modify
    it under the terms of the GNU Affero General Public License as
    published by the Free Software Foundation, either version 3 of the
    License, or (at your option) any later version.

    WebPropMan is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU Affero General Public License for more details.

    You should have received a copy of the GNU Affero General Public License
    along with WebPropMan.  If not, see <http://www.gnu.org/licenses/>.

   Contact MimirSoft at mimirsoft@gmail.com or www.mimirsoft.com

*
*/
class calendar{

    public $WEEK_START;
    public $DATE_FORMAT;
    public $TZ_OFFSET;
    
    
    private $date;
    function setDate ($a)
    {
        $this->date = $a;
    }
    function getDate()
    {
        return $this->date;
    }
    
    private $thisyear;
    function getThisYear()
    {
        return $this->thisyear;
    }
    private $thisday;
    function getThisDay()
    {
        return $this->thisday;
    }
    private $thismonth;
    function getThisMonth()
    {
        return $this->thismonth;
    }
    private $calendarId;
    function getCalendarId()
    {
        return $this->calendarId;
    }
    private $catId;
    function getCatId()
    {
        return $this->catId;
    }
    private $thisdate;
    private $today;
    
/**
 * Determines what the day is after the <var>$this->TZ_OFFSET</var> and sets it globally.
 *
 * The following global variables will be set:
 * - <var>$thisyear</var>
 * - <var>$thismonth</var>
 * - <var>$thisday</var>
 * - <var>$thisdate</var>
 * - <var>$today</var>
 *
 * @param string $date The date in YYYYMMDD format
 */
function setToday($date) {
    // Adjust for TimeZone
    $this->today = time() + ($this->TZ_OFFSET * 60 * 60);

    if ( ! empty ( $date ) && ! empty ( $date ) ) 
    {
        $this->thisyear = substr ( $date, 0, 4 );
        $this->thismonth = substr ( $date, 4, 2 );
        $this->thisday = substr ( $date, 6, 2 );
    } else 
    {
        if ( empty ( $this->month ) || $this->month == 0 )
        {
            $this->thismonth = date("m", $this->today);
        }
        else
        {
            $this->thismonth = $this->month;
        }
        if ( empty ( $this->year ) || $this->year == 0 )
        {
            $this->thisyear = date("Y", $this->today);
        }
        else
        {
            $this->thisyear = $this->year;
        }
        if ( empty ( $this->day ) || $this->day == 0 )
        {
            $this->thisday = date("d", $this->today);
        }
        else
        {
            $this->thisday = $this->day;
        }
    }
    $thisdate = sprintf ( "%04d%02d%02d", $this->thisyear, $this->thismonth, $this->thisday );
}
    
    private $year;
    function setYear ($a)
    {
        $this->year = $a;
    }
    function getYear()
    {
        return $this->year;
    }
    
    private $month;
    function setMonth ($a)
    {
        $this->month = $a;
    }
    function getMonth()
    {
        return $this->month;
    }
    
    private $day;
    
    private $hour;
    function setHour ($a)
    {
        $this->hour = $a;
    }
    function getHour()
    {
        return $this->hour;
    }
    
    private $minute;
    function setMinute ($a){
        $this->minute = $a;
    }
    function getMinute()
    {
        return $this->minute;
    }
    
    private $is_admin;
    function setIsAdmin ($a){
        $this->is_admin = $a;
    }
    function getIsAdmin()
    {
        return $this->is_admin;
    }
    private $is_assistant;
    function setIsAssistant ($a){
        $this->is_assistant = $a;
    }
    function getIsAssistant()
    {
        return $this->is_assistant;
    }
    
    private $public_access;
    function setPublicAccess ($a){
        $this->public_access = $a;
    }
    function getPublicAccess()
    {
        return $this->public_access;
    }
    
    public $can_add;
    
    private $allow_view_other;
    function getAllowViewOther()
    {
        return $this->allow_view_other;
    }
    private $login; // this is the person viewing the calandear
    function setLogin ($a){
        $this->login = $a;
    }
    function getLogin()
    {
        return $this->login;
    }
    
    private $user; // this is the person who's calendar it is
    function setUser ($a){
        $this->user = $a;
    }
    function getUser()
    {
        return $this->user;
    }
    
    function checkBossAssistant()
    {
        $this->is_assistant = empty ( $user ) ? false : $this->user_is_assistant ($this->login, $this->$user );
        $this->has_boss = $this->user_has_boss ( $this->login );    
    }
    
/**
 * Is this user an assistant?
 *
 * @param string $assistant Login for user
 *
 * @return bool true if the user is an assistant to one or more bosses
 */
function user_has_boss ( $assistant ) {
    $ret = false;
    $dbh = new DB_Mysql();
    $stmt = $dbh->prepare("SELECT * FROM webcal_asst " .
        "WHERE cal_assistant = '$assistant'");
    if ( $stmt->execute()) {
        if ( $stmt->fetch_row (  ) )
        $ret = true;
    }
    return $ret;
}
    
    
    /**
     * Number of seconds in a day
     *
     * @global int $ONE_DAY
     */
    public $ONE_DAY = 86400;
    /**
     * Array containing the number of days in each month in a non-leap year
     *
     * @global array $days_per_month
     */
    public $days_per_month = array ( 0, 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31 );
    /**
     * Array containing the number of days in each month in a leap year
     *
     * @global array $ldays_per_month
     */
    public $ldays_per_month = array ( 0, 31, 29, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31 );
        
    /**
 * Converts a date in YYYYMMDD format into "Friday, December 31, 1999",
 * "Friday, 12-31-1999" or whatever format the user prefers.
 *
 * @param string $indate       Date in YYYYMMDD format
 * @param string $format       Format to use for date (default is "__month__
 *                             __dd__, __yyyy__")
 * @param bool   $show_weekday Should the day of week also be included?
 * @param bool   $short_months Should the abbreviated month names be used
 *                             instead of the full month names?
 * @param int    $server_time ???
 *
 * @return string Date in the specified format
 *
 * @global string Preferred date format
 * @global int    User's timezone offset from the server
 */
function date_to_str ( $indate, $format="", $show_weekday=true, $short_months=false, $server_time="" ) {
  
  if ( strlen ( $indate ) == 0 ) {
    $indate = date ( "Ymd" );
  }

  $newdate = $indate;
  if ( $server_time != "" && $server_time >= 0 ) {
    $y = substr ( $indate, 0, 4 );
    $m = substr ( $indate, 4, 2 );
    $d = substr ( $indate, 6, 2 );
    if ( $server_time + $this->TZ_OFFSET * 10000 > 240000 ) {
       $newdate = date ( "Ymd", mktime ( 3, 0, 0, $m, $d + 1, $y ) );
    } else if ( $server_time + $this->TZ_OFFSET * 10000 < 0 ) {
       $newdate = date ( "Ymd", mktime ( 3, 0, 0, $m, $d - 1, $y ) );
    }
  }

  // if they have not set a preference yet...
  if ( $this->DATE_FORMAT == "" )
    $this->DATE_FORMAT = "__month__ __dd__, __yyyy__";

  if ( empty ( $format ) )
    $format = $this->DATE_FORMAT;

  $y = (int) ( $newdate / 10000 );
  $m = (int) ( $newdate / 100 ) % 100;
  $d = $newdate % 100;
  $date = mktime ( 3, 0, 0, $m, $d, $y );
  $wday = strftime ( "%w", $date );

  if ( $short_months ) {
    $weekday = Calendar::weekday_short_name ( $wday );
    $month = Calendar::month_short_name ( $m - 1 );
  } else {
    $weekday = Calendar::weekday_name ( $wday );
    $month = Calendar::month_name ( $m - 1 );
  }
  $yyyy = $y;
  $yy = sprintf ( "%02d", $y %= 100 );

  $ret = $format;
  $ret = str_replace ( "__yyyy__", $yyyy, $ret );
  $ret = str_replace ( "__yy__", $yy, $ret );
  $ret = str_replace ( "__month__", $month, $ret );
  $ret = str_replace ( "__mon__", $month, $ret );
  $ret = str_replace ( "__dd__", $d, $ret );
  $ret = str_replace ( "__mm__", $m, $ret );

  if ( $show_weekday )
    return "$weekday, $ret";
  else
    return $ret;
}
    
/**
 * Converts from Gregorian Year-Month-Day to ISO YearNumber-WeekNumber-WeekDay.
 *
 * @internal JGH borrowed gregorianToISO from PEAR Date_Calc Class and added
 * $GLOBALS["WEEK_START"] (change noted)
 *
 * @param int $day   Day of month
 * @param int $month Number of month
 * @param int $year  Year
 *
 * @return string Date in ISO YearNumber-WeekNumber-WeekDay format
 *
 * @ignore
 */
function gregorianToISO($day,$month,$year) {
    $mnth = array (0,31,59,90,120,151,181,212,243,273,304,334);
    $y_isleap = Calendar::isLeapYear($year);
    $y_1_isleap = Calendar::isLeapYear($year - 1);
    $day_of_year_number = $day + $mnth[$month - 1];
    if ($y_isleap && $month > 2) {
        $day_of_year_number++;
    }
    // find Jan 1 weekday (monday = 1, sunday = 7)
    $yy = ($year - 1) % 100;
    $c = ($year - 1) - $yy;
    $g = $yy + intval($yy/4);
    $jan1_weekday = 1 + intval((((($c / 100) % 4) * 5) + $g) % 7);


    // JGH added next if/else to compensate for week begins on Sunday
    if (! $this->WEEK_START && $jan1_weekday < 7) {
      $jan1_weekday++;
    } elseif (! $this->WEEK_START && $jan1_weekday == 7) {
      $jan1_weekday=1;
    }

    // weekday for year-month-day
    $h = $day_of_year_number + ($jan1_weekday - 1);
    $weekday = 1 + intval(($h - 1) % 7);
    // find if Y M D falls in YearNumber Y-1, WeekNumber 52 or
    if ($day_of_year_number <= (8 - $jan1_weekday) && $jan1_weekday > 4){
        $yearnumber = $year - 1;
        if ($jan1_weekday == 5 || ($jan1_weekday == 6 && $y_1_isleap)) {
            $weeknumber = 53;
        } else {
            $weeknumber = 52;
        }
    } else {
        $yearnumber = $year;
    }
    // find if Y M D falls in YearNumber Y+1, WeekNumber 1
    if ($yearnumber == $year) {
        if ($y_isleap) {
            $i = 366;
        } else {
            $i = 365;
        }
        if (($i - $day_of_year_number) < (4 - $weekday)) {
            $yearnumber++;
            $weeknumber = 1;
        }
    }
    // find if Y M D falls in YearNumber Y, WeekNumber 1 through 53
    if ($yearnumber == $year) {
        $j = $day_of_year_number + (7 - $weekday) + ($jan1_weekday - 1);
        $weeknumber = intval($j / 7);
        if ($jan1_weekday > 4) {
            $weeknumber--;
        }
    }
    // put it all together
    if ($weeknumber < 10)
        $weeknumber = '0'.$weeknumber;
    return "{$yearnumber}-{$weeknumber}-{$weekday}";
}

/**
 * Is this a leap year?
 *
 * @internal JGH Borrowed isLeapYear from PEAR Date_Calc Class
 *
 * @param int $year Year
 *
 * @return bool True for a leap year, else false
 *
 * @ignore
 */
function isLeapYear($year='') {
  if (empty($year)) $year = strftime("%Y",time());
  if (strlen($year) != 4) return false;
  if (preg_match('/\D/',$year)) return false;
  return (($year % 4 == 0 && $year % 100 != 0) || $year % 400 == 0);
}


/**
 * Returns the week number for specified date.
 * 
 * Depends on week numbering settings.
 *
 * @param int $date Date in UNIX timestamp format
 *
 * @return string The week number of the specified date
 */
function week_number ( $date ) {
  $tmp = getdate($date);
  $iso = Calendar::gregorianToISO($tmp['mday'], $tmp['mon'], $tmp['year']);
  $parts = explode('-',$iso);
  $week_number = intval($parts[1]);
  return sprintf("%02d",$week_number);
}



/**
 * Converts a date to a timestamp.
 * 
 * @param string $d Date in YYYYMMDD format
 *
 * @return int Timestamp representing 3:00 (or 4:00 if during Daylight Saving
 *             Time) in the morning on that day
 */
function date_to_epoch ( $d ) {
  if ( $d == 0 )
    return 0;
  $T = mktime ( 3, 0, 0, substr ( $d, 4, 2 ), substr ( $d, 6, 2 ), substr ( $d, 0, 4 ) );
  $lt = localtime($T);
  if ($lt[8]) {
    return mktime ( 4, 0, 0, substr ( $d, 4, 2 ), substr ( $d, 6, 2 ), substr ( $d, 0, 4 ) );
  } else {
    return $T;
  }
}

/**
 * Gets the Sunday of the week that the specified date is in.
 *
 * If the date specified is a Sunday, then that date is returned.
 *
 * @param int $year  Year
 * @param int $month Month (1-12)
 * @param int $day   Day of the month
 *
 * @return int The date (in UNIX timestamp format)
 *
 * @see get_monday_before
 */
function get_sunday_before ( $year, $month, $day ) {
  $weekday = date ( "w", mktime ( 3, 0, 0, $month, $day, $year ) );
  $newdate = mktime ( 3, 0, 0, $month, $day - $weekday, $year );
  return $newdate;
}

/** 
 * Gets the Monday of the week that the specified date is in.
 *
 * If the date specified is a Monday, then that date is returned.
 *
 * @param int $year  Year
 * @param int $month Month (1-12)
 * @param int $day   Day of the month
 *
 * @return int The date (in UNIX timestamp format)
 *
 * @see get_sunday_before
 */
function get_monday_before ( $year, $month, $day ) {
  $weekday = date ( "w", mktime ( 3, 0, 0, $month, $day, $year ) );
  if ( $weekday == 0 )
    return mktime ( 3, 0, 0, $month, $day - 6, $year );
  if ( $weekday == 1 )
    return mktime ( 3, 0, 0, $month, $day, $year );
  return mktime ( 3, 0, 0, $month, $day - ( $weekday - 1 ), $year );
}


/**
 * Returns the full weekday name.
 *
 * Use {@link weekday_short_name()} to get the abbreviated weekday name.
 *
 * @param int $w Number of the day in the week (0=Sunday,...,6=Saturday)
 *
 * @return string The full weekday name ("Sunday")
 *
 * @see weekday_short_name
 */
function weekday_name ( $w ) {
  switch ( $w ) {
    case 0: return translate("Sunday");
    case 1: return translate("Monday");
    case 2: return translate("Tuesday");
    case 3: return translate("Wednesday");
    case 4: return translate("Thursday");
    case 5: return translate("Friday");
    case 6: return translate("Saturday");
  }
  return "unknown-weekday($w)";
}

/**
 * Returns the abbreviated weekday name.
 *
 * Use {@link weekday_name()} to get the full weekday name.
 *
 * @param int $w Number of the day in the week (0=Sunday,...,6=Saturday)
 *
 * @return string The abbreviated weekday name ("Sun")
 */
function weekday_short_name ( $w ) {
  switch ( $w ) {
    case 0: return translate("Sun");
    case 1: return translate("Mon");
    case 2: return translate("Tue");
    case 3: return translate("Wed");
    case 4: return translate("Thu");
    case 5: return translate("Fri");
    case 6: return translate("Sat");
  }
  return "unknown-weekday($w)";
}


/**
 * Returns the full name of the specified month.
 *
 * Use {@link month_short_name()} to get the abbreviated name of the month.
 *
 * @param int $m Number of the month (0-11)
 *
 * @return string The full name of the specified month
 *
 * @see month_short_name
 */
function month_name ( $m ) {
  switch ( $m ) {
    case 0: return translate("January");
    case 1: return translate("February");
    case 2: return translate("March");
    case 3: return translate("April");
    case 4: return translate("May_"); // needs to be different than "May"
    case 5: return translate("June");
    case 6: return translate("July");
    case 7: return translate("August");
    case 8: return translate("September");
    case 9: return translate("October");
    case 10: return translate("November");
    case 11: return translate("December");
  }
  return "unknown-month($m)";
}

/**
 * Returns the abbreviated name of the specified month (such as "Jan").
 *
 * Use {@link month_name()} to get the full name of the month.
 *
 * @param int $m Number of the month (0-11)
 *
 * @return string The abbreviated name of the specified month (example: "Jan")
 *
 * @see month_name
 */
function month_short_name ( $m ) {
  switch ( $m ) {
    case 0: return translate("Jan");
    case 1: return translate("Feb");
    case 2: return translate("Mar");
    case 3: return translate("Apr");
    case 4: return translate("May");
    case 5: return translate("Jun");
    case 6: return translate("Jul");
    case 7: return translate("Aug");
    case 8: return translate("Sep");
    case 9: return translate("Oct");
    case 10: return translate("Nov");
    case 11: return translate("Dec");
  }
  return "unknown-month($m)";
}


/**
 * Gets a list of nonuser calendars and return info in an array.
 *
 * @param string $user Login of admin of the nonuser calendars
 *
 * @return array Array of nonuser cals, where each is an array with the
 *               following fields:
 * - <var>cal_login</var>
 * - <var>cal_lastname</var>
 * - <var>cal_firstname</var>
 * - <var>cal_admin</var>
 * - <var>cal_fullname</var>
 */
function get_nonuser_cals ($user = '') {
  $count = 0;
  $ret = array ();
  $sql = "SELECT user_id, cal_lastname, cal_firstname, " .
    "cal_admin FROM webcal_nonuser_cals ";
  if ($user != '') $sql .= "WHERE cal_admin = '$user' ";
  $sql .= "ORDER BY cal_lastname, cal_firstname, user_id";
$dbh = new DB_Mysql();
$stmt = $dbh->prepare($sql);

  if ( $stmt->execute() ) {
    while ( $row = $stmt->fetch_row () ) {
      if ( strlen ( $row[1] ) || strlen ( $row[2] ) )
        $fullname = "$row[2] $row[1]";
      else
        $fullname = $row[0];
      $ret[$count++] = array (
        "user_id" => $row[0],
        "cal_lastname" => $row[1],
        "cal_firstname" => $row[2],
        "cal_admin" => $row[3],
        "cal_fullname" => $fullname
      );
    }
  }
  return $ret;
}
function get_nonuser_cals_array ($user = '') {
  $count = 0;
  $ret = array ();
  $sql = "SELECT user_id, cal_lastname, cal_firstname, " .
    "cal_admin FROM webcal_nonuser_cals ";
  if ($user != '') $sql .= "WHERE cal_admin = '$user' ";
  $sql .= "ORDER BY cal_lastname, cal_firstname, user_id";
$dbh = new DB_Mysql();
$stmt = $dbh->prepare($sql);

  if ( $stmt->execute() ) {
    while ( $row = $stmt->fetch_row () ) {
      if ( strlen ( $row[1] ) || strlen ( $row[2] ) )
        $fullname = "$row[2] $row[1]";
      else
        $fullname = $row[0];
      $ret[$row[0]] = array (
        "user_id" => $row[0],
        "username" => $row[0],
        "cal_lastname" => $row[1],
        "cal_firstname" => $row[2],
        "cal_admin" => $row[3],
        "cal_fullname" => $fullname
      );
    }
  }
  return $ret;
}


/**
 * Builds the HTML for the event popup.
 *
 * @param string $popupid     CSS id to use for event popup
 * @param string $user        Username of user the event pertains to
 * @param string $description Event description
 * @param string $time        Time of the event (already formatted in a display format)
 * @param string $site_extras HTML for any site_extras for this event
 *
 * @return string The HTML for the event popup
 */
function build_event_popup ( $popupid, $user, $description, $time, $site_extras='' ) {
  global $login, $popup_fullnames, $popuptemp_fullname;
  $ret = "<dl id=\"$popupid\" class=\"popup\">\n";

  if ( empty ( $popup_fullnames ) )
    $popup_fullnames = array ();
  
  if ( $user != $login ) {
    if ( empty ( $popup_fullnames[$user] ) ) {
      $this->user_load_variables ( $user, "popuptemp_" );
      $popup_fullnames[$user] = $popuptemp_fullname;
    }
    $ret .= "<dt>" . translate ("User") .
      ":</dt>\n<dd>$popup_fullnames[$user]</dd>\n";
  }
  if ( strlen ( $time ) )
    $ret .= "<dt>" . translate ("Time") . ":</dt>\n<dd>$time</dd>\n";
  $ret .= "<dt>" . translate ("Description") . ":</dt>\n<dd>";
  if ( ! empty ( $GLOBALS['allow_html_description'] ) &&
    $GLOBALS['allow_html_description'] == 'Y' ) {
    $str = str_replace ( "&", "&amp;", $description );
    $str = str_replace ( "&amp;amp;", "&amp;", $str );
    // If there is no html found, then go ahead and replace
    // the line breaks ("\n") with the html break.
    if ( strstr ( $str, "<" ) && strstr ( $str, ">" ) ) {
      // found some html...
      $ret .= $str;
    } else {
      // no html, replace line breaks
      $ret .= nl2br ( $str );
    }
  } else {
    // html not allowed in description, escape everything
    $ret .= nl2br ( htmlspecialchars ( $description ) );
  }
  $ret .= "</dd>\n";
  if ( ! empty ( $site_extras ) )
    $ret .= $site_extras;
  $ret .= "</dl>\n";
  return $ret;
}

/**
 * Prints out a minicalendar for a month.
 *
 * @todo Make day.php NOT be a special case
 *
 * @param int    $thismonth     Number of the month to print
 * @param int    $thisyear      Number of the year
 * @param bool   $showyear      Show the year in the calendar's title?
 * @param bool   $show_weeknums Show week numbers to the left of each row?
 * @param string $minical_id    id attribute for the minical table
 * @param string $month_link    URL and query string for month link that should
 *                              come before the date specification (e.g.
 *                              month.php?  or  view_l.php?id=7&amp;)
 */
function display_small_month ( $thismonth, $thisyear, $showyear,
  $show_weeknums=false, $minical_id='', $month_link='month.php?', $day_link='day.php?', $dateformat="Ymd" ) {
  
    global $user, $login, $boldDays, $get_unapproved;
    global $SCRIPT, $thisday; // Needed for day.php
    global $caturl, $today;
    
    if ( $user != $login && ! empty ( $user ) ) {
        $u_url = "user=$user" . "&amp;";
    } else {
        $u_url = '';
    }
    
    //start the minical table for each month
    echo "\n<table class=\"minical\"";
    if ( $minical_id != '' ) {
        echo " id=\"$minical_id\"";
    }
    echo ">\n";
    
    $monthstart = mktime(2,0,0,$thismonth,1,$thisyear);
    $monthend = mktime(2,0,0,$thismonth + 1,0,$thisyear);
    if ( $SCRIPT == 'day.php' ) 
    {
        $month_ago = date ( "Ymd",
        mktime ( 3, 0, 0, $thismonth - 1, $thisday, $thisyear ) );
        $month_ahead = date ( "Ymd",
        mktime ( 3, 0, 0, $thismonth + 1, $thisday, $thisyear ) );
    
        echo "<caption>$thisday</caption>\n";
        echo "<thead>\n";
        echo "<tr class=\"monthnav\"><th colspan=\"7\">\n";
        echo "<a title=\"" . 
        translate("Previous") . "\" class=\"prev\" href=\"".$day_link . $u_url  .
        "date=$month_ago$caturl\"><img src=\"image/leftarrowsmall.gif\" alt=\"" .
        translate("Previous") . "\" /></a>\n";
        echo "<a title=\"" . 
        translate("Next") . "\" class=\"next\" href=\"".$day_link  . $u_url .
        "date=$month_ahead$caturl\"><img src=\"image/rightarrowsmall.gif\" alt=\"" .
        translate("Next") . "\" /></a>\n";
        echo month_name ( $thismonth - 1 );
        if ( $showyear != '' ) {
            echo " $thisyear";
        }
        echo "</th></tr>\n<tr>\n";
    } 
    else 
    {  //not day script
        //print the month name
        echo "<caption><a href=\"{$month_link}{$u_url}year=$thisyear&amp;month=$thismonth\">";
        echo Calendar::month_name ( $thismonth - 1 ) .
        ( $showyear ? " $thisyear" : "" );
        echo "</a></caption>\n";
        echo "<thead>\n<tr>\n";
    }

  //determine if the week starts on sunday or monday
    if ( $this->WEEK_START == "1" ) {
        $wkstart = Calendar::get_monday_before ( $thisyear, $thismonth, 1 );
    } 
    else {
        $wkstart = Calendar::get_sunday_before ( $thisyear, $thismonth, 1 );
    }
  //print the headers to display the day of the week (sun, mon, tues, etc.)

  // if we're showing week numbers we need an extra column
  if ( $show_weeknums && $this->DISPLAY_WEEKNUMBER == 'Y' )
    echo "<th class=\"empty\">&nbsp;</th>\n";
  //if the week doesn't start on monday, print the day
  if ( $this->WEEK_START == 0 ) echo "<th>" .
    Calendar::weekday_short_name ( 0 ) . "</th>\n";
  //cycle through each day of the week until gone
  for ( $i = 1; $i < 7; $i++ ) {
    echo "<th>" .  Calendar::weekday_short_name ( $i ) .  "</th>\n";
  }
  //if the week DOES start on monday, print sunday
  if ( $this->WEEK_START == 1 )
    echo "<th>" .  Calendar::weekday_short_name ( 0 ) .  "</th>\n";
  //end the header row
  echo "</tr>\n</thead>\n<tbody>\n";
  for ($i = $wkstart; date("Ymd",$i) <= date ("Ymd",$monthend);
    $i += (24 * 3600 * 7) ) {
    echo "<tr>\n";
    if ( $show_weeknums && $this->DISPLAY_WEEKNUMBER == 'Y' ) {
      echo "<td class=\"weeknumber\"><a href=\"week.php?" . $u_url .
        "date=".date("Ymd", $i)."\">(" . Calendar::week_number($i) . ")</a></td>\n";
    }
    for ($j = 0; $j < 7; $j++) {
      $date = $i + ($j * 24 * 3600);
      $dateYmd = date ( $dateformat, $date );
      $hasEvents = false;
      if ( $boldDays ) {
        $ev = get_entries ( $user, $dateYmd, $get_unapproved );
        if ( count ( $ev ) > 0 ) {
          $hasEvents = true;
        } else {
          $rep = get_repeating_entries ( $user, $dateYmd, $get_unapproved );
          if ( count ( $rep ) > 0 )
            $hasEvents = true;
        }
      }
      if ( $dateYmd >= date ($dateformat,$monthstart) &&
        $dateYmd <= date ($dateformat,$monthend) ) {
        echo "<td";
        $wday = date ( 'w', $date );
        $class = '';
  //add class="weekend" if it's saturday or sunday
        if ( $wday == 0 || $wday == 6 ) {
          $class = "weekend";
        }
  //if the day being viewed is today's date AND script = day.php
        if ( $dateYmd == $thisyear . $thismonth . $thisday &&
          $SCRIPT == 'day.php'  ) {
    //if it's also a weekend, add a space between class names to combine styles
    if ( $class != '' ) {
            $class .= ' ';
          }
          $class .= "selectedday";
        }
        if ( $hasEvents ) {
          if ( $class != '' ) {
            $class .= ' ';
          }
          $class .= "hasevents";
        }
        if ( $class != '' ) {
          echo " class=\"$class\"";
        }
        if ( date ( "Ymd", $date  ) == date ( "Ymd", $today ) ){
          echo " id=\"today\"";
        }
        echo "><a href=\"".$day_link.$u_url  . "date=" .  $dateYmd . 
          "\">";
        echo date ( "d", $date ) . "</a></td>\n";
        } else {
          echo "<td class=\"empty\">&nbsp;</td>\n";
        }
      }                 // end for $j
      echo "</tr>\n";
    }                         // end for $i
  echo "</tbody>\n</table>\n";
}

/**
 * Prints the HTML for one day's events in the month view.
 *
 * @param int    $id          Event ID
 * @param int    $date        Date of event (relevant in repeating events) in
 *                            YYYYMMDD format
 * @param int    $time        Time (in HHMMSS format)
 * @param int    $duration    Event duration in minutes
 * @param string $name        Event name
 * @param string $description Long description of event
 * @param string $status      Event status
 * @param int    $pri         Event priority
 * @param string $access      Event access
 * @param string $event_owner Username of user associated with this event
 * @param int    $event_cat   Category of event for <var>$event_owner</var>
 *
 * @staticvar int Used to ensure all event popups have a unique id
 *
 * @uses build_event_popup
 */
function print_entry ( $id, $date, $time, $duration,
  $name, $description, $status,
  $pri, $access, $event_owner, $event_cat=-1 ) {
  global $eventinfo, $login, $user, $PHP_SELF;
  static $key = 0;
  
  global $layers;

  if ( $login != $event_owner && strlen ( $event_owner ) ) {
    $class = "layerentry";
  } else {
    $class = "entry";
    if ( $status == "W" ) $class = "unapprovedentry";
  }
  // if we are looking at a view, then always use "entry"
  if ( strstr ( $PHP_SELF, "view_m.php" ) ||
    strstr ( $PHP_SELF, "view_w.php" ) ||
    strstr ( $PHP_SELF, "view_v.php" ) ||
    strstr ( $PHP_SELF, "view_t.php" ) )
    $class = "entry";

  if ( $pri == 3 ) echo "<strong>";
  $popupid = "eventinfo-$id-$key";
  $key++;
  echo "<a title=\"" . 
    translate("View this entry") . "\" class=\"$class\" href=\"view_entry.php?id=$id&amp;date=$date";
  if ( strlen ( $user ) > 0 )
    echo "&amp;user=" . $user;
  echo "\" onmouseover=\"window.status='" . 
    translate("View this entry") .
    "'; show(event, '$popupid'); return true;\" onmouseout=\"window.status=''; hide('$popupid'); return true;\">";
  $icon = "image/circle.gif";
  $catIcon = '';
  if ( $event_cat > 0 ) {
    $catIcon = "icons/cat-" . $event_cat . ".gif";
    if ( ! file_exists ( $catIcon ) )
      $catIcon = '';
  }

  if ( empty ( $catIcon ) ) {
    echo "<img src=\"$icon\" class=\"bullet\" alt=\"" . 
      translate("View this entry") . "\" />";
  } else {
    // Use category icon
    echo "<img src=\"$catIcon\" alt=\"" . 
      translate("View this entry") . "\" /><br />";
  }

  if ( $login != $event_owner && strlen ( $event_owner ) ) {
    if ($layers) foreach ($layers as $layer) {
      if ($layer['cal_layeruser'] == $event_owner) {
        echo("<span style=\"color:" . $layer['cal_color'] . ";\">");
      }
    }
  }


  $timestr = "";
  if ( $duration == ( 24 * 60 ) ) {
    $timestr = translate("All day event");
  } else if ( $time != -1 ) {
    $timestr = $this->display_time ( $time );
    $time_short = preg_replace ("/(:00)/", '', $timestr);
    echo $time_short . "&raquo;&nbsp;";
    if ( $duration > 0 ) {
        // calc end time
        $h = (int) ( $time / 10000 );
        $m = ( $time / 100 ) % 100;
        $m += $duration;
        $d = $duration;
        while ( $m >= 60 ) {
          $h++;
          $m -= 60;
        }
        $end_time = sprintf ( "%02d%02d00", $h, $m );
        $timestr .= " - " . $this->display_time ( $end_time );
    }
  }
  if ( $login != $user && $access == 'R' && strlen ( $user ) ) {
    echo "(" . translate("Private") . ")";
  } else if ( $login != $event_owner && $access == 'R' &&
    strlen ( $event_owner ) ) {
    echo "(" . translate("Private") . ")";
  } else {
    echo htmlspecialchars ( $name );
  }

  if ( $login != $event_owner && strlen ( $event_owner ) ) {
    if ($layers) foreach ($layers as $layer) {
        if($layer['cal_layeruser'] == $event_owner) {
            echo "</span>";
        }
    }
  }
  echo "</a>\n";
  if ( $pri == 3 ) echo "</strong>\n"; //end font-weight span
  echo "<br />";
  if ( $login != $user && $access == 'R' && strlen ( $user ) )
    $eventinfo .= $this->build_event_popup ( $popupid, $event_owner,
      translate("This event is confidential"), "" );
  else
  if ( $login != $event_owner && $access == 'R' && strlen ( $event_owner ) )
    $eventinfo .= $this->build_event_popup ( $popupid, $event_owner,
      translate("This event is confidential"), "" );
  else
    $eventinfo .= $this->build_event_popup ( $popupid, $event_owner,
      $description, $timestr, $this->site_extras_for_popup ( $id ) );
}

/**
 * Calculates which row/slot this time represents.
 *
 * This is used in day and week views where hours of the time are separeted
 * into different cells in a table.
 *
 * <b>Note:</b> the global variable <var>$TIME_SLOTS</var> is used to determine
 * how many time slots there are and how many minutes each is.  This variable
 * is defined user preferences (or defaulted to admin system settings).
 *
 * @param string $time       Input time in HHMMSS format
 * @param bool   $round_down Should we change 1100 to 1059?
 *                           (This will make sure a 10AM-100AM appointment just
 *                           shows up in the 10AM slow and not in the 11AM slot
 *                           also.)
 *
 * @return int The time slot index
 */
function calc_time_slot ( $time, $round_down = false ) {
  global $TIME_SLOTS;

  $interval = ( 24 * 60 ) / $TIME_SLOTS;
  $mins_since_midnight = Framework_helper::time_to_minutes ( $time );
  $ret = (int) ( $mins_since_midnight / $interval );
  if ( $round_down ) {
    if ( $ret * $interval == $mins_since_midnight )
      $ret--;
  }
  //echo "$mins_since_midnight / $interval = $ret <br />\n";
  if ( $ret > $TIME_SLOTS )
    $ret = $TIME_SLOTS;

  //echo "<br />\ncalc_time_slot($time) = $ret <br />\nTIME_SLOTS = $TIME_SLOTS<br />\n";
  return $ret;
}

/**
 * Generates the HTML for an icon to add a new event.
 *
 * @param string $date   Date for new event in YYYYMMDD format
 * @param int    $hour   Hour of day (0-23)
 * @param int    $minute Minute of the hour (0-59)
 * @param string $user   Participant to initially select for new event
 *
 * @return string The HTML for the add event icon
 */
function html_for_add_icon ( $date=0,$hour="", $minute="", $user="" ) {
  global $login, $cat_id;
  $u_url = '';

  if ( $this->readonly == 'Y' )
    return '';

  if ( $minute < 0 ) {
   $minute = abs($minute);
   $hour = $hour -1;
  }
  if ( ! empty ( $user ) && $user != $login )
    $u_url = "user=$user&amp;";
  if ( isset ( $hour ) && $hour != NULL )
    $hour += $this->TZ_OFFSET;
  return "<a title=\"" . 
 translate("New Entry") . "\" href=\"edit_entry.php?" . $u_url .
    "date=$date" . ( isset ( $hour ) && $hour != NULL && $hour >= 0 ? "&amp;hour=$hour" : ""  ) .
    ( $minute > 0 ? "&amp;minute=$minute" : "" ) .
    ( empty ( $user ) ? "" :  "&amp;defusers=$user" ) .
    ( empty ( $cat_id ) ? "" :  "&amp;cat_id=$cat_id" ) .
    "\"><img src=\"image/new.gif\" class=\"new\" alt=\"" . 
 translate("New Entry") . "\" /></a>\n";
}

/**
 * Generates the HTML for an event to be viewed in the week-at-glance (week.php).
 *
 * The HTML will be stored in an array (global variable $hour_arr)
 * indexed on the event's starting hour.
 *
 * @param int    $id             Event id
 * @param string $date           Date of event in YYYYMMDD format
 * @param string $time           Time of event in HHMM format
 * @param string $name           Brief description of event
 * @param string $description    Full description of event
 * @param string $status         Status of event ('A', 'W')
 * @param int    $pri            Priority of event
 * @param string $access         Access to event by others ('P', 'R')
 * @param int    $duration       Duration of event in minutes
 * @param string $event_owner    User who created event
 * @param int    $event_category Category id for event
 */
function html_for_event_week_at_a_glance ( $id, $date, $time,
  $name, $description, $status, $pri, $access, $duration, $event_owner,
  $event_category=-1 ) {
  global $first_slot, $last_slot, $hour_arr, $rowspan_arr, $rowspan,
    $eventinfo, $login, $user;
  static $key = 0;
  global $DISPLAY_ICONS, $PHP_SELF, $TIME_SLOTS;
  global $layers;

  $popupid = "eventinfo-day-$id-$key";
  $key++;
  
  // Figure out which time slot it goes in.
  if ( $time >= 0 && $duration != ( 24 * 60 ) ) {
    $ind = Framework_helper::calc_time_slot ( $time );
    if ( $ind < $first_slot )
      $first_slot = $ind;
    if ( $ind > $last_slot )
      $last_slot = $ind;
  } else
    $ind = 9999;

  if ( $login != $event_owner && strlen ( $event_owner ) ) {
    $class = "layerentry";
  } else {
    $class = "entry";
    if ( $status == "W" ) $class = "unapprovedentry";
  }
  // if we are looking at a view, then always use "entry"
  if ( strstr ( $PHP_SELF, "view_m.php" ) ||
    strstr ( $PHP_SELF, "view_w.php" ) ||
    strstr ( $PHP_SELF, "view_v.php" ) ||
    strstr ( $PHP_SELF, "view_t.php" ) )
    $class = "entry";

  // avoid php warning for undefined array index
  if ( empty ( $hour_arr[$ind] ) )
    $hour_arr[$ind] = "";

  $catIcon = "icons/cat-" . $event_category . ".gif";
  if ( $event_category > 0 && file_exists ( $catIcon ) ) {
    $hour_arr[$ind] .= "<img src=\"$catIcon\" alt=\"$catIcon\" />";
  }

  $hour_arr[$ind] .= "<a title=\"" . 
  translate("View this entry") . "\" class=\"$class\" href=\"view_entry.php?id=$id&amp;date=$date";
  if ( strlen ( $GLOBALS["user"] ) > 0 )
    $hour_arr[$ind] .= "&amp;user=" . $GLOBALS["user"];
  $hour_arr[$ind] .= "\" onmouseover=\"window.status='" .
    translate("View this entry") . "'; show(event, '$popupid'); return true;\" onmouseout=\"hide('$popupid'); return true;\">";
  if ( $pri == 3 )
    $hour_arr[$ind] .= "<strong>";

  if ( $login != $event_owner && strlen ( $event_owner ) ) {
    if ($layers) foreach ($layers as $layer) {
      if ( $layer['cal_layeruser'] == $event_owner ) {
        $in_span = true;
        $hour_arr[$ind] .= "<span style=\"color:" . $layer['cal_color'] . ";\">";
      }
    }
  }
  if ( $duration == ( 24 * 60 ) ) {
    $timestr = translate("All day event");
  } else if ( $time >= 0 ) {
    $hour_arr[$ind] .= Framework_helper::display_time ( $time ) . "&raquo;&nbsp;";
    $timestr = Framework_helper::display_time ( $time );
    if ( $duration > 0 ) {
      // calc end time
      $h = (int) ( $time / 10000 );
      $m = ( $time / 100 ) % 100;
      $m += $duration;
      $d = $duration;
      while ( $m >= 60 ) {
        $h++;
        $m -= 60;
      }
      $end_time = sprintf ( "%02d%02d00", $h, $m );
      $timestr .= "-" . Framework_helper::display_time ( $end_time );
    } else {
      $end_time = 0;
    }
    if ( empty ( $rowspan_arr[$ind] ) )
      $rowspan_arr[$ind] = 0; // avoid warning below
    // which slot is end time in? take one off so we don't
    // show 11:00-12:00 as taking up both 11 and 12 slots.
    $endind = Framework_helper::calc_time_slot ( $end_time, true );
    if ( $endind == $ind )
      $rowspan = 0;
    else
      $rowspan = $endind - $ind + 1;
    if ( $rowspan > $rowspan_arr[$ind] && $rowspan > 1 )
      $rowspan_arr[$ind] = $rowspan;
  } else {
    $timestr = "";
  }

  // avoid php warning of undefined index when using .= below
  if ( empty ( $hour_arr[$ind] ) )
    $hour_arr[$ind] = "";

  if ( $login != $user && $access == 'R' && strlen ( $user ) ) {
    $hour_arr[$ind] .= "(" . translate("Private") . ")";
  } else if ( $login != $event_owner && $access == 'R' &&
    strlen ( $event_owner ) ) {
    $hour_arr[$ind] .= "(" . translate("Private") . ")";
  } else if ( $login != $event_owner && strlen ( $event_owner ) ) {
    $hour_arr[$ind] .= htmlspecialchars ( $name );
    if ( ! empty ( $in_span ) )
      $hour_arr[$ind] .= "</span>"; //end color span
  } else {
    $hour_arr[$ind] .= htmlspecialchars ( $name );
  }

  if ( $pri == 3 ) $hour_arr[$ind] .= "</strong>"; //end font-weight span
    $hour_arr[$ind] .= "</a>";
  //if ( $DISPLAY_ICONS == "Y" ) {
  //  $hour_arr[$ind] .= icon_text ( $id, true, true );
  //}
  $hour_arr[$ind] .= "<br />\n";
  if ( $login != $user && $access == 'R' && strlen ( $user ) ) {
    $eventinfo .= Framework_helper::build_event_popup ( $popupid, $event_owner,
      translate("This event is confidential"), "" );
  } else if ( $login != $event_owner && $access == 'R' &&
    strlen ( $event_owner ) ) {
    $eventinfo .= Framework_helper::build_event_popup ( $popupid, $event_owner,
      translate("This event is confidential"), "" );
  } else {
    $eventinfo .= Framework_helper::build_event_popup ( $popupid, $event_owner,
      $description, $timestr, Framework_helper::site_extras_for_popup ( $id ) );
  }
}

/**
 * Generates the HTML for an event to be viewed in the day-at-glance (day.php).
 *
 * The HTML will be stored in an array (global variable $hour_arr)
 * indexed on the event's starting hour.
 *
 * @param int    $id             Event id
 * @param string $date           Date of event in YYYYMMDD format
 * @param string $time           Time of event in HHMM format
 * @param string $name           Brief description of event
 * @param string $description    Full description of event
 * @param string $status         Status of event ('A', 'W')
 * @param int    $pri            Priority of event
 * @param string $access         Access to event by others ('P', 'R')
 * @param int    $duration       Duration of event in minutes
 * @param string $event_owner    User who created event
 * @param int    $event_category Category id for event
 */
function html_for_event_day_at_a_glance ( $id, $date, $time,
  $name, $description, $status, $pri, $access, $duration, $event_owner,
  $event_category=-1 ) {
  global $first_slot, $last_slot, $hour_arr, $rowspan_arr, $rowspan,
    $eventinfo, $login, $user;
  static $key = 0;
  global $layers, $PHP_SELF, $TIME_SLOTS;

  $popupid = "eventinfo-day-$id-$key";
  $key++;

  if ( $login != $user && $access == 'R' && strlen ( $user ) )
    $eventinfo .= build_event_popup ( $popupid, $event_owner,
      translate("This event is confidential"), "" );
  else if ( $login != $event_owner && $access == 'R' &&
    strlen ( $event_owner ) )
    $eventinfo .= build_event_popup ( $popupid, $event_owner,
      translate("This event is confidential"), "" );
  else
    $eventinfo .= build_event_popup ( $popupid, $event_owner, $description,
      "", site_extras_for_popup ( $id ) );

  // calculate slot length in minutes
  $interval = ( 60 * 24 ) / $TIME_SLOTS;

  // If TZ_OFFSET make this event before the start of the day or
  // after the end of the day, adjust the time slot accordingly.
  if ( $time >= 0 && $duration != ( 24 * 60 ) ) {
    if ( $time + ( $this->TZ_OFFSET * 10000 ) > 240000 )
      $time -= 240000;
    else if ( $time + ( $this->TZ_OFFSET * 10000 ) < 0 )
      $time += 240000;
    $ind = calc_time_slot ( $time );
    if ( $ind < $first_slot )
      $first_slot = $ind;
    if ( $ind > $last_slot )
      $last_slot = $ind;
  } else
    $ind = 9999;
  //echo "time = $time <br />\nind = $ind <br />\nfirst_slot = $first_slot<br />\n";

  if ( empty ( $hour_arr[$ind] ) )
    $hour_arr[$ind] = "";

  if ( $login != $event_owner && strlen ( $event_owner ) ) {
    $class = "layerentry";
  } else {
    $class = "entry";
    if ( $status == "W" )
      $class = "unapprovedentry";
  }
  // if we are looking at a view, then always use "entry"
  if ( strstr ( $PHP_SELF, "view_m.php" ) ||
    strstr ( $PHP_SELF, "view_w.php" )  || 
    strstr ( $PHP_SELF, "view_v.php" ) ||
    strstr ( $PHP_SELF, "view_t.php" ) )
    $class = "entry";

  $catIcon = "icons/cat-" . $event_category . ".gif";
  if ( $event_category > 0 && file_exists ( $catIcon ) ) {
    $hour_arr[$ind] .= "<img src=\"$catIcon\" alt=\"$catIcon\" />";
  }

  $hour_arr[$ind] .= "<a title=\"" .
    translate("View this entry") . "\" class=\"$class\" href=\"view_entry.php?id=$id&amp;date=$date";
  if ( strlen ( $GLOBALS["user"] ) > 0 )
    $hour_arr[$ind] .= "&amp;user=" . $GLOBALS["user"];
  $hour_arr[$ind] .= "\" onmouseover=\"window.status='" .
    translate("View this entry") . "'; show(event, '$popupid'); return true;\" onmouseout=\"hide('$popupid'); return true;\">";
  if ( $pri == 3 ) $hour_arr[$ind] .= "<strong>";

  if ( $login != $event_owner && strlen ( $event_owner ) ) {
    if ($layers) foreach ($layers as $layer) {
      if ( $layer['cal_layeruser'] == $event_owner) {
        $in_span = true;
        $hour_arr[$ind] .= "<span style=\"color:" . $layer['cal_color'] . ";\">";
      }
    }
  }

  if ( $duration == ( 24 * 60 ) ) {
    $hour_arr[$ind] .= "[" . translate("All day event") . "] ";
  } else if ( $time >= 0 ) {
    $hour_arr[$ind] .= "[" . display_time ( $time );
    if ( $duration > 0 ) {
      // calc end time
      $h = (int) ( $time / 10000 );
      $m = ( $time / 100 ) % 100;
      $m += $duration;
      $d = $duration;
      while ( $m >= 60 ) {
        $h++;
        $m -= 60;
      }
      $end_time = sprintf ( "%02d%02d00", $h, $m );
      $hour_arr[$ind] .= "-" . display_time ( $end_time );
      // which slot is end time in? take one off so we don't
      // show 11:00-12:00 as taking up both 11 and 12 slots.
      $endind = calc_time_slot ( $end_time, true );
      if ( $endind == $ind )
        $rowspan = 0;
      else
        $rowspan = $endind - $ind + 1;
      if ( ! isset ( $rowspan_arr[$ind] ) )
        $rowspan_arr[$ind] = 0;
      if ( $rowspan > $rowspan_arr[$ind] && $rowspan > 1 )
        $rowspan_arr[$ind] = $rowspan;
    }
    $hour_arr[$ind] .= "] ";
  }
  if ( $login != $user && $access == 'R' && strlen ( $user ) )
    $hour_arr[$ind] .= "(" . translate("Private") . ")";
  else
  if ( $login != $event_owner && $access == 'R' && strlen ( $event_owner ) )
    $hour_arr[$ind] .= "(" . translate("Private") . ")";
  else
  if ( $login != $event_owner && strlen ( $event_owner ) )
  {
    $hour_arr[$ind] .= htmlspecialchars ( $name );
    if ( ! empty ( $in_span ) )
      $hour_arr[$ind] .= "</span>"; //end color span
  }

  else
    $hour_arr[$ind] .= htmlspecialchars ( $name );
  if ( $pri == 3 ) $hour_arr[$ind] .= "</strong>"; //end font-weight span

  $hour_arr[$ind] .= "</a>";
  if ( $GLOBALS["DISPLAY_DESC_PRINT_DAY"] == "Y" ) {
    $hour_arr[$ind] .= "\n<dl class=\"desc\">\n";
    $hour_arr[$ind] .= "<dt>" . translate("Description") . ":</dt>\n<dd>";
    if ( ! empty ( $GLOBALS['allow_html_description'] ) &&
      $GLOBALS['allow_html_description'] == 'Y' ) {
      $str = str_replace ( "&", "&amp;", $description );
      $str = str_replace ( "&amp;amp;", "&amp;", $str );
      // If there is no html found, then go ahead and replace
      // the line breaks ("\n") with the html break.
      if ( strstr ( $str, "<" ) && strstr ( $str, ">" ) ) {
        // found some html...
        $hour_arr[$ind] .= $str;
      } else {
        // no html, replace line breaks
        $hour_arr[$ind] .= nl2br ( $str );
      }
    } else {
      // html not allowed in description, escape everything
      $hour_arr[$ind] .= nl2br ( htmlspecialchars ( $description ) );
    }
    $hour_arr[$ind] .= "</dd>\n</dl>\n";
  }

  $hour_arr[$ind] .= "<br />\n";
}

/**
 * Prints all the calendar entries for the specified user for the specified date in day-at-a-glance format.
 *
 * If we are displaying data from someone other than
 * the logged in user, then check the access permission of the entry.
 *
 * @param string $date Date in YYYYMMDD format
 * @param string $user Username of calendar
 */
function print_day_at_a_glance ( $date, $user, $can_add=0 ) {
  global $first_slot, $last_slot, $hour_arr, $rowspan_arr, $rowspan;
  global $TABLEBG, $CELLBG, $TODAYCELLBG, $THFG, $THBG, $TIME_SLOTS;
  global $repeated_events;

  $get_unapproved = (@ $GLOBALS["DISPLAY_UNAPPROVED"] == "Y" );
  if ( $user == "__public__" )
    $get_unapproved = false;
  if ( empty ( $TIME_SLOTS ) ) {
    echo "Error: TIME_SLOTS undefined!<br />\n";
    return;
  }

  // $interval is number of minutes per slot
  $interval = ( 24 * 60 ) / $TIME_SLOTS;
    
  $rowspan_arr = array ();
  for ( $i = 0; $i < $TIME_SLOTS; $i++ ) {
    $rowspan_arr[$i] = 0;
  }

  // get all the repeating events for this date and store in array $rep
  $rep = get_repeating_entries ( $user, $date );
  $cur_rep = 0;

  // Get static non-repeating events
  $ev = get_entries ( $user, $date, $get_unapproved );
  $hour_arr = array ();
  $interval = ( 24 * 60 ) / $TIME_SLOTS;
  $first_slot = (int) ( ( ( $this->WORK_DAY_START_HOUR - $this->TZ_OFFSET ) * 60 ) / $interval );
  $last_slot = (int) ( ( ( $this->WORK_DAY_END_HOUR - $this->TZ_OFFSET ) * 60 ) / $interval);
  //echo "first_slot = $first_slot<br />\nlast_slot = $last_slot<br />\ninterval = $interval<br />\nTIME_SLOTS = $TIME_SLOTS<br />\n";
  $rowspan_arr = array ();
  $all_day = 0;
  for ( $i = 0; $i < count ( $ev ); $i++ ) {
    // print out any repeating events that are before this one...
    while ( $cur_rep < count ( $rep ) &&
      $rep[$cur_rep]['cal_time'] < $ev[$i]['cal_time'] ) {
      if ( $get_unapproved || $rep[$cur_rep]['cal_status'] == 'A' ) {
        if ( ! empty ( $rep[$cur_rep]['cal_ext_for_id'] ) ) {
          $viewid = $rep[$cur_rep]['cal_ext_for_id'];
          $viewname = $rep[$cur_rep]['cal_name'] . " (" .
            translate("cont.") . ")";
        } else {
          $viewid = $rep[$cur_rep]['cal_id'];
          $viewname = $rep[$cur_rep]['cal_name'];
        }
        if ( $rep[$cur_rep]['cal_duration'] == ( 24 * 60 ) )
          $all_day = 1;
        html_for_event_day_at_a_glance ( $viewid,
          $date, $rep[$cur_rep]['cal_time'],
          $viewname, $rep[$cur_rep]['cal_description'],
          $rep[$cur_rep]['cal_status'], $rep[$cur_rep]['cal_priority'],
          $rep[$cur_rep]['cal_access'], $rep[$cur_rep]['cal_duration'],
          $rep[$cur_rep]['cal_login'], $rep[$cur_rep]['cal_category'] );
      }
      $cur_rep++;
    }
    if ( $get_unapproved || $ev[$i]['cal_status'] == 'A' ) {
      if ( ! empty ( $ev[$i]['cal_ext_for_id'] ) ) {
        $viewid = $ev[$i]['cal_ext_for_id'];
        $viewname = $ev[$i]['cal_name'] . " (" .
          translate("cont.") . ")";
      } else {
        $viewid = $ev[$i]['cal_id'];
        $viewname = $ev[$i]['cal_name'];
      }
      if ( $ev[$i]['cal_duration'] == ( 24 * 60 ) )
        $all_day = 1;
      html_for_event_day_at_a_glance ( $viewid,
        $date, $ev[$i]['cal_time'],
        $viewname, $ev[$i]['cal_description'],
        $ev[$i]['cal_status'], $ev[$i]['cal_priority'],
        $ev[$i]['cal_access'], $ev[$i]['cal_duration'],
        $ev[$i]['cal_login'], $ev[$i]['cal_category'] );
    }
  }
  // print out any remaining repeating events
  while ( $cur_rep < count ( $rep ) ) {
    if ( $get_unapproved || $rep[$cur_rep]['cal_status'] == 'A' ) {
      if ( ! empty ( $rep[$cur_rep]['cal_ext_for_id'] ) ) {
        $viewid = $rep[$cur_rep]['cal_ext_for_id'];
        $viewname = $rep[$cur_rep]['cal_name'] . " (" .
          translate("cont.") . ")";
      } else {
        $viewid = $rep[$cur_rep]['cal_id'];
        $viewname = $rep[$cur_rep]['cal_name'];
      }
      if ( $rep[$cur_rep]['cal_duration'] == ( 24 * 60 ) )
        $all_day = 1;
      html_for_event_day_at_a_glance ( $viewid,
        $date, $rep[$cur_rep]['cal_time'],
        $viewname, $rep[$cur_rep]['cal_description'],
        $rep[$cur_rep]['cal_status'], $rep[$cur_rep]['cal_priority'],
        $rep[$cur_rep]['cal_access'], $rep[$cur_rep]['cal_duration'],
        $rep[$cur_rep]['cal_login'], $rep[$cur_rep]['cal_category'] );
    }
    $cur_rep++;
  }

  // squish events that use the same cell into the same cell.
  // For example, an event from 8:00-9:15 and another from 9:30-9:45 both
  // want to show up in the 8:00-9:59 cell.
  $rowspan = 0;
  $last_row = -1;
  //echo "First SLot: $first_slot; Last Slot: $last_slot<br />\n";
  $i = 0;
  if ( $first_slot < 0 )
    $i = $first_slot;
  for ( ; $i < $TIME_SLOTS; $i++ ) {
    if ( $rowspan > 1 ) {
      if ( ! empty ( $hour_arr[$i] ) ) {
        $diff_start_time = $i - $last_row;
        if ( $rowspan_arr[$i] > 1 ) {
          if (  $rowspan_arr[$i] + ( $diff_start_time ) >  $rowspan_arr[$last_row]  ) {
            $rowspan_arr[$last_row] = ( $rowspan_arr[$i] + ( $diff_start_time ) );
          }
          $rowspan += ( $rowspan_arr[$i] - 1 );
        } else {
          $rowspan_arr[$last_row] += $rowspan_arr[$i];
        }
        // this will move entries apart that appear in one field,
        // yet start on different hours
        for ( $u = $diff_start_time ; $u > 0 ; $u-- ) {
          $hour_arr[$last_row] .= "<br />\n"; 
        }
        $hour_arr[$last_row] .= $hour_arr[$i];
        $hour_arr[$i] = "";
        $rowspan_arr[$i] = 0;
      }
      $rowspan--;
    } else if ( ! empty ( $rowspan_arr[$i] ) && $rowspan_arr[$i] > 1 ) {
      $rowspan = $rowspan_arr[$i];
      $last_row = $i;
    }
  }
  if ( ! empty ( $hour_arr[9999] ) ) {
    echo "<tr><th class=\"empty\">&nbsp;</th>\n" .
      "<td class=\"hasevents\">$hour_arr[9999]</td></tr>\n";
  }
  $rowspan = 0;
  //echo "first_slot = $first_slot<br />\nlast_slot = $last_slot<br />\ninterval = $interval<br />\n";
  for ( $i = $first_slot; $i <= $last_slot; $i++ ) {
    $time_h = (int) ( ( $i * $interval ) / 60 );
    $time_m = ( $i * $interval ) % 60;
    $time = display_time ( ( $time_h * 100 + $time_m ) * 100 );
    echo "<tr>\n<th class=\"row\">" . $time . "</th>\n";
    if ( $rowspan > 1 ) {
      // this might mean there's an overlap, or it could mean one event
      // ends at 11:15 and another starts at 11:30.
      if ( ! empty ( $hour_arr[$i] ) ) {
        echo "<td class=\"hasevents\">";
        if ( $can_add )
          echo html_for_add_icon ( $date, $time_h, $time_m, $user );
        echo "$hour_arr[$i]</td>\n";
      }
      $rowspan--;
    } else {
      if ( empty ( $hour_arr[$i] ) ) {
        echo "<td>";
        if ( $can_add ) {
          echo html_for_add_icon ( $date, $time_h, $time_m, $user ) . "</td>";
  } else {
    echo "&nbsp;</td>";
  }
        echo "</tr>\n";
      } else {
        if ( empty ( $rowspan_arr[$i] ) )
          $rowspan = '';
        else
          $rowspan = $rowspan_arr[$i];
        if ( $rowspan > 1 ) {
          echo "<td rowspan=\"$rowspan\" class=\"hasevents\">";
          if ( $can_add )
            echo html_for_add_icon ( $date, $time_h, $time_m, $user );
          echo "$hour_arr[$i]</td></tr>\n";
        } else {
          echo "<td class=\"hasevents\">";
          if ( $can_add )
            echo html_for_add_icon ( $date, $time_h, $time_m, $user );
          echo "$hour_arr[$i]</td></tr>\n";
        }
      }
    }
  }
}

}
?>