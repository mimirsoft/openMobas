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

class Calendar_Event{


    private $TZ_OFFSET;
    
    public static function add_entry($id, $old_id, $e_created_by, $e_date, $e_time, $now_ymd, 
                        $now_time, $e_duration, $e_priority, $e_access, $e_caltype, $name, $description)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("INSERT INTO webcal_entry ( 
                                            cal_id, ".( $old_id > 0 ? " cal_group_id, " : "" ) .
                                            "cal_create_by, cal_date, " .
                                            "cal_time, cal_mod_date, cal_mod_time, cal_duration, cal_priority, " .
                                            "cal_access, cal_type, cal_name, cal_description ) " .
                                            "VALUES (:1:, ".( $old_id > 0 ? " :2:, " : "" ) .":3:, :4:, :5:,
                                            :6:, :7:, :8:, :9:, :10:, :11:, :12:, :13:)");
        
        $stmt->execute($id, $old_id, $e_created_by, $e_date, $e_time, $now_ymd, 
                        $now_time, $e_duration, $e_priority, $e_access, $e_caltype, $name, $description);
        return mysql_insert_id();

    }
    function setTZOffset($a)
    {
        $this->TZ_OFFSET = $a;
    } 
    public static function get_next_id()
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT MAX(cal_id) FROM webcal_entry");
        if ( $stmt->execute() ) {
            $row = $stmt->fetch_row (  );
            $id = $row[0] + 1;
        } else {
            $id = 1;
        }
        return $id;
    }

    public static function add_entry_user($id, $participant, $status, $my_cat_id)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("INSERT INTO webcal_entry_user ( 
                cal_id, user_id, cal_status, cal_category ) 
                VALUES ( :1:, :2:, :3:, :4: )");
        $stmt->execute($id, $participant, $status, $my_cat_id);
    }
    
/**
 * Gets all the repeating events for the specified date.
 *
 * <b>Note:</b>
 * The global variable <var>$repeated_events</var> needs to be
 * set by calling {@link read_repeated_events()} first.
 *
 * @param string $user           Username
 * @param string $date           Date to get events for in YYYYMMDD format
 * @param bool   $get_unapproved Include unapproved events in results?
 *
 * @return mixed The query result resource on queries (which can then be
 *               passed to {@link dbi_fetch_row()} to obtain the results), or
 *               true/false on insert or delete queries.
 *
 * @global array Array of repeating events retreived using {@link read_repeated_events()}
 */
function get_repeating_entries ( $user, $dateYmd, $get_unapproved=true ) {
  global $repeated_events;
  $n = 0;
  $ret = array ();
  //echo count($repeated_events)."<br />\n";
  for ( $i = 0; $i < count ( $repeated_events ); $i++ ) {
    if ( $repeated_events[$i]['cal_status'] == 'A' || $get_unapproved ) {
      if ( Calendar::repeated_event_matches_date ( $repeated_events[$i], $dateYmd ) ) {
        // make sure this is not an exception date...
        $unixtime = Calendar::date_to_epoch ( $dateYmd );
        if ( ! Calendar::is_exception ( $unixtime, $repeated_events[$i]['cal_exceptions'] ) )
          $ret[$n++] = $repeated_events[$i];
      }
    }
  }
  return $ret;
}

/**
 * Determines whether the event passed in will fall on the date passed.
 *
 * @param array  $event   The event as an array
 * @param string $dateYmd Date to check in YYYYMMDD format
 *
 * @return bool Does <var>$event</var> occur on <var>$dateYmd</var>?
 */
function repeated_event_matches_date($event,$dateYmd) {
  global $days_per_month, $ldays_per_month, $ONE_DAY;
  // only repeat after the beginning, and if there is an end
  // before the end
  $date = Calendar::date_to_epoch ( $dateYmd );
  $thisyear = substr($dateYmd, 0, 4);
  $start = Calendar::date_to_epoch ( $event['cal_date'] );
  $end   = Calendar::date_to_epoch ( $event['cal_end'] );
  $freq = $event['cal_frequency'];
  $thismonth = substr($dateYmd, 4, 2);
  if ($event['cal_end'] && $dateYmd > date("Ymd",$end) )
    return false;
  if ( $dateYmd <= date("Ymd",$start) )
    return false;
  $id = $event['cal_id'];

  if ($event['cal_type'] == 'daily') {
    if ( (floor(($date - $start)/$ONE_DAY)%$freq) )
      return false;
    return true;
  } else if ($event['cal_type'] == 'weekly') {
    $dow  = date("w", $date);
    $dow1 = date("w", $start);
    $isDay = substr($event['cal_days'], $dow, 1);
    $wstart = $start - ($dow1 * $ONE_DAY);
    if (floor(($date - $wstart)/604800)%$freq)
      return false;
    return (strcmp($isDay,"y") == 0);
  } else if ($event['cal_type'] == 'monthlyByDay') {
    $dowS = date("w", $start);
    $dow  = date("w", $date);
    // do this comparison first in hopes of best performance
    if ( $dowS != $dow )
      return false;
    $mthS = date("m", $start);
    $yrS  = date("Y", $start);
    $dayS  = floor(date("d", $start));
    $dowS1 = ( date ( "w", $start - ( $ONE_DAY * ( $dayS - 1 ) ) ) + 35 ) % 7;
    $days_in_first_weekS = ( 7 - $dowS1 ) % 7;
    $whichWeekS = floor ( ( $dayS - $days_in_first_weekS ) / 7 );
    if ( $dowS >= $dowS1 && $days_in_first_weekS )
      $whichWeekS++;
    //echo "dayS=$dayS;dowS=$dowS;dowS1=$dowS1;wWS=$whichWeekS<br />\n";
    $mth  = date("m", $date);
    $yr   = date("Y", $date);
    $day  = date("d", $date);
    $dow1 = ( date ( "w", $date - ( $ONE_DAY * ( $day - 1 ) ) ) + 35 ) % 7;
    $days_in_first_week = ( 7 - $dow1 ) % 7;
    $whichWeek = floor ( ( $day - $days_in_first_week ) / 7 );
    if ( $dow >= $dow1 && $days_in_first_week )
      $whichWeek++;
    //echo "day=$day;dow=$dow;dow1=$dow1;wW=$whichWeek<br />\n";

    if ((($yr - $yrS)*12 + $mth - $mthS) % $freq)
      return false;

    return ( $whichWeek == $whichWeekS );
  } else if ($event['cal_type'] == 'monthlyByDayR') {
    $dowS = date("w", $start);
    $dow  = date("w", $date);
    // do this comparison first in hopes of best performance
    if ( $dowS != $dow )
      return false;

    $dayS = ceil(date("d", $start));
    $mthS = ceil(date("m", $start));
    $yrS  = date("Y", $start);
    $daysthismonthS = $mthS % 4 == 0 ? $ldays_per_month[$mthS] :
      $days_per_month[$mthS];
    $whichWeekS = floor ( ( $daysthismonthS - $dayS ) / 7 );

    $day = ceil(date("d", $date));
    $mth = ceil(date("m", $date));
    $yr  = date("Y", $date);
    $daysthismonth = $mth % 4 == 0 ? $ldays_per_month[$mth] :
      $days_per_month[$mth];
    $whichWeek = floor ( ( $daysthismonth - $day ) / 7 );

    if ((($yr - $yrS)*12 + $mth - $mthS) % $freq)
      return false;

    return ( $whichWeekS == $whichWeek );
  } else if ($event['cal_type'] == 'monthlyByDate') {
    $mthS = date("m", $start);
    $yrS  = date("Y", $start);

    $mth  = date("m", $date);
    $yr   = date("Y", $date);

    if ((($yr - $yrS)*12 + $mth - $mthS) % $freq)
      return false;

    return (date("d", $date) == date("d", $start));
  }
  else if ($event['cal_type'] == 'yearly') {
    $yrS = date("Y", $start);
    $yr  = date("Y", $date);

    if (($yr - $yrS)%$freq)
      return false;

    return (date("dm", $date) == date("dm", $start));
  } else {
    // unknown repeat type
    return false;
  }
  return false;
}

/**
 * Checks if a date is an exception for an event.
 *
 * @param string $date   Date in YYYYMMDD format
 * @param array  $exdays Array of dates in YYYYMMDD format
 *
 * @ignore
 */
function is_exception ( $date, $ex_days ) {
  $size = count ( $ex_days );
  $count = 0;
  $date = date ( "Ymd", $date );
  //echo "Exception $date check.. count is $size <br />\n";
  while ( $count < $size ) {
    //echo "Exception date: $ex_days[$count] <br />\n";
    if ( $date == $ex_days[$count++] )
      return true;
  }
  return false;
}

/**
 */
function read_events ( $calendar_id, $startdate, $enddate, $cat_id = ''  ) {
  
  $sy = substr ( $startdate, 0, 4 );
  $sm = substr ( $startdate, 4, 2 );
  $sd = substr ( $startdate, 6, 2 );
  $ey = substr ( $enddate, 0, 4 );
  $em = substr ( $enddate, 4, 2 );
  $ed = substr ( $enddate, 6, 2 );
  if ( $startdate == $enddate ) {
    if ( $this->TZ_OFFSET == 0 ) {
      $date_filter = " AND ce.calendar_entry_date = $startdate";
    } else if ( $this->TZ_OFFSET > 0 ) {
      $prev_day = mktime ( 3, 0, 0, $sm, $sd - 1, $sy );
      $cutoff = 24 - $this->TZ_OFFSET .  "0000";
      $date_filter = " AND ( ( ce.calendar_entry_date = $startdate AND " .
        "( ce.calendar_entry_time <= $cutoff OR " .
        "ce.calendar_entry_time = -1 ) ) OR " .
        "( ce.calendar_entry_date = " . date("Ymd", $prev_day ) .
        " AND ce.calendar_entry_time >= $cutoff ) )";
    } else {
      $next_day = mktime ( 3, 0, 0, $sm, $sd + 1, $sy );
      $cutoff = ( 0 - $this->TZ_OFFSET ) * 10000;
      $date_filter = " AND ( ( ce.calendar_entry_date = $startdate AND " .
        "( ce.calendar_entry_time > $cutoff OR " .
        "ce.calendar_entry_time = -1 ) ) OR " .
        "( ce.calendar_entry_date = " . date("Ymd", $next_day ) .
        " AND ce.calendar_entry_time <= $cutoff ) )";
    }
  } else {
    if ( $this->TZ_OFFSET == 0 ) {
      $date_filter = " AND ce.calendar_entry_date >= $startdate " .
        "AND ce.calendar_entry_date <= $enddate";
    } else if ( $this->TZ_OFFSET > 0 ) {
      $prev_day = date ( ( "Ymd" ), mktime ( 3, 0, 0, $sm, $sd - 1, $sy ) );
      $enddate_minus1 = date ( ( "Ymd" ), mktime ( 3, 0, 0, $em, $ed - 1, $ey ) );
      $cutoff = 24 - $this->TZ_OFFSET . "0000";
      $date_filter = " AND ( ( ce.calendar_entry_date >= $startdate " .
        "AND ce.calendar_entry_date <= $enddate AND " .
        "ce.calendar_entry_time = -1 ) OR " .
        "( ce.calendar_entry_date = $prev_day AND " .
        "ce.calendar_entry_time >= $cutoff ) OR " .
        "( calendar_entry.calendar_entry_date = $enddate AND " .
        "ce.calendar_entry_time < $cutoff ) OR " .
        "( ce.calendar_entry_date >= $startdate AND " .
        "ce.calendar_entry_date <= $enddate_minus1 ) )";
    } else {
      // TZ_OFFSET < 0
      $next_day = date ( ( "Ymd" ), mktime ( 3, 0, 0, $sm, $sd + 1, $sy ) );
      $enddate_plus1 =
        date ( ( "Ymd" ), mktime ( 3, 0, 0, $em, $ed + 1, $ey ) );
      $cutoff = ( 0 - $this->TZ_OFFSET ) * 10000;
      $date_filter = " AND ( ( ce.calendar_entry_date >= $startdate " .
        "AND ce.calendar_entry_date <= $enddate AND " .
        "ce.calendar_entry_time = -1 ) OR " .
        "( ce.calendar_entry_date = $startdate AND " .
        "ce.calendar_entry_time > $cutoff ) OR " .
        "( ce.calendar_entry_date = $enddate_plus1 AND " .
        "ce.calendar_entry_time <= $cutoff ) OR " .
        "( ce.calendar_entry_date > $startdate AND " .
        "ce.calendar_entry_date < $enddate_plus1 ) )";
    }
  }
  return $this->query_events ( $calendar_id, false, $date_filter, $cat_id  );
}

/**
 */
function read_repeated_events ( $user, $cat_id = '', $date = ''  ) {
      $filter = ($date != '') ? "AND (cer.calendar_entry_end >= $date OR cer.calendar_entry_end IS NULL) " : '';
      return $this->query_events ( $user, true, $filter, $cat_id );
}

/**
 * Returns all the dates a specific event will fall on accounting for the repeating.
 *
 * Any event with no end will be assigned one.
 *
 * @param string $date     Initial date in raw format
 * @param string $rpt_type Repeating type as stored in the database
 * @param string $end      End date
 * @param string $days     Days events occurs on (for weekly)
 * @param array  $ex_dates Array of exception dates for this event in YYYYMMDD format
 * @param int    $freq     Frequency of repetition
 *
 * @return array Array of dates (in UNIX time format)
 */
function get_all_dates ( $date, $rpt_type, $end, $days, $ex_days, $freq=1 ) {

  //echo "get_all_dates ( $date, '$rpt_type', $end, '$days', [array], $freq ) <br>\n";
  $currentdate = floor($date/$ONE_DAY)*$ONE_DAY;
  $realend = floor($end/$ONE_DAY)*$ONE_DAY;
  $dateYmd = date ( "Ymd", $date );
  if ($end=='NULL') {
    // Check for $conflict_repeat_months months into future for conflicts
    $thismonth = substr($dateYmd, 4, 2);
    $thisyear = substr($dateYmd, 0, 4);
    $thisday = substr($dateYmd, 6, 2);
    $thismonth += $conflict_repeat_months;
    if ($thismonth > 12) {
      $thisyear++;
      $thismonth -= 12;
    }
    $realend = mktime(3,0,0,$thismonth,$thisday,$thisyear);
  }
  $ret = array();
  $ret[0] = $date;
  //do iterative checking here.
  //I floored the $realend so I check it against the floored date
  if ($rpt_type && $currentdate < $realend) {
    $cdate = $date;
    if (!$freq) $freq = 1;
    $n = 1;
    if ($rpt_type == 'daily') {
      //we do inclusive counting on end dates.
      $cdate += $ONE_DAY * $freq;
      while ($cdate <= $realend+$ONE_DAY) {
        if ( ! is_exception ( $cdate, $ex_days ) )
          $ret[$n++]=$cdate;
        $cdate += $ONE_DAY * $freq;
      }
    } else if ($rpt_type == 'weekly') {
      $daysarray = array();
      $r=0;
      $dow = date("w",$date);
      $cdate = $date - ($dow * $ONE_DAY);
      for ($i = 0; $i < 7; $i++) {
        $isDay = substr($days, $i, 1);
        if (strcmp($isDay,"y")==0) {
          $daysarray[$r++]=$i * $ONE_DAY;
        }
      }
      //we do inclusive counting on end dates.
      while ($cdate <= $realend+$ONE_DAY) {
        //add all of the days of the week.
        for ($j=0; $j<$r;$j++) {
          $td = $cdate + $daysarray[$j];
          if ($td >= $date) {
            if ( ! is_exception ( $td, $ex_days ) )
              $ret[$n++] = $td;
          }
        }
        //skip to the next week in question.
        $cdate += ( $ONE_DAY * 7 ) * $freq;
      }
    } else if ($rpt_type == 'monthlyByDay') {
      $dow  = date('w', $date);
      $thismonth = substr($dateYmd, 4, 2);
      $thisyear  = substr($dateYmd, 0, 4);
      $week  = floor(date("d", $date)/7);
      $thismonth+=$freq;
      //dow1 is the weekday that the 1st of the month falls on
      $dow1 = date('w',mktime (3,0,0,$thismonth,1,$thisyear));
      $t = $dow - $dow1;
      if ($t < 0) $t += 7;
      $day = 7*$week + $t + 1;
      $cdate = mktime (3,0,0,$thismonth,$day,$thisyear);
      while ($cdate <= $realend+$ONE_DAY) {
        if ( ! is_exception ( $cdate, $ex_days ) )
          $ret[$n++] = $cdate;
        $thismonth+=$freq;
        //dow1 is the weekday that the 1st of the month falls on
        $dow1time = mktime ( 3, 0, 0, $thismonth, 1, $thisyear );
        $dow1 = date ( 'w', $dow1time );
        $t = $dow - $dow1;
        if ($t < 0) $t += 7;
        $day = 7*$week + $t + 1;
        $cdate = mktime (3,0,0,$thismonth,$day,$thisyear);
      }
    } else if ($rpt_type == 'monthlyByDayR') {
      // by weekday of month reversed (i.e., last Monday of month)
      $dow  = date('w', $date);
      $thisday = substr($dateYmd, 6, 2);
      $thismonth = substr($dateYmd, 4, 2);
      $thisyear  = substr($dateYmd, 0, 4);
      // get number of days in this month
      $daysthismonth = $thisyear % 4 == 0 ? $ldays_per_month[$thismonth] :
        $days_per_month[$thismonth];
      // how many weekdays like this one remain in the month?
      // 0=last one, 1=one more after this one, etc.
      $whichWeek = floor ( ( $daysthismonth - $thisday ) / 7 );
      // find first repeat date
      $thismonth += $freq;
      if ( $thismonth > 12 ) {
        $thisyear++;
        $thismonth -= 12;
      }
      // get weekday for last day of month
      $dowLast += date('w',mktime (3,0,0,$thismonth + 1, -1,$thisyear));
      if ( $dowLast >= $dow ) {
        // last weekday is in last week of this month
        $day = $daysthismonth - ( $dowLast - $dow ) -
          ( 7 * $whichWeek );
      } else {
        // last weekday is NOT in last week of this month
        $day = $daysthismonth - ( $dowLast - $dow ) -
          ( 7 * ( $whichWeek + 1 ) );
      }
      $cdate = mktime (3,0,0,$thismonth,$day,$thisyear);
      while ($cdate <= $realend+$ONE_DAY) {
        if ( ! is_exception ( $cdate, $ex_days ) )
          $ret[$n++] = $cdate;
        $thismonth += $freq;
        if ( $thismonth > 12 ) {
          $thisyear++;
          $thismonth -= 12;
        }
        // get weekday for last day of month
        $dowLast += date('w',mktime (3,0,0,$thismonth + 1, -1,$thisyear));
        if ( $dowLast >= $dow ) {
          // last weekday is in last week of this month
          $day = $daysthismonth - ( $dowLast - $dow ) -
            ( 7 * $whichWeek );
        } else {
          // last weekday is NOT in last week of this month
          $day = $daysthismonth - ( $dowLast - $dow ) -
            ( 7 * ( $whichWeek + 1 ) );
        }
        $cdate = mktime (3,0,0,$thismonth,$day,$thisyear);
      }
    } else if ($rpt_type == 'monthlyByDate') {
      $thismonth = substr($dateYmd, 4, 2);
      $thisyear  = substr($dateYmd, 0, 4);
      $thisday   = substr($dateYmd, 6, 2);
      $hour      = date('H',$date);
      $minute    = date('i',$date);

      $thismonth += $freq;
      $cdate = mktime (3,0,0,$thismonth,$thisday,$thisyear);
      while ($cdate <= $realend+$ONE_DAY) {
        if ( ! is_exception ( $cdate, $ex_days ) )
          $ret[$n++] = $cdate;
        $thismonth += $freq;
        $cdate = mktime (3,0,0,$thismonth,$thisday,$thisyear);
      }
    } else if ($rpt_type == 'yearly') {
      $thismonth = substr($dateYmd, 4, 2);
      $thisyear  = substr($dateYmd, 0, 4);
      $thisday   = substr($dateYmd, 6, 2);
      $hour      = date('H',$date);
      $minute    = date('i',$date);

      $thisyear += $freq;
      $cdate = mktime (3,0,0,$thismonth,$thisday,$thisyear);
      while ($cdate <= $realend+$ONE_DAY) {
        if ( ! is_exception ( $cdate, $ex_days ) )
          $ret[$n++] = $cdate;
        $thisyear += $freq;
        $cdate = mktime (3,0,0,$thismonth,$thisday,$thisyear);
      }
    }
  }
  return $ret;
}


/**
 * Gets all the events for a specific date.
 *
 * Events are retreived from the array of pre-loaded events (which was loaded
 * all at once to improve performance).
 *
 * The returned events will be sorted by time of day.
 *
 * @param string $user           Username
 * @param string $date           Date to get events for in YYYYMMDD format
 * @param bool   $get_unapproved Load unapproved events?
 *
 * @return array Array of events
 */
function get_entries ( $user, $date, $get_unapproved=true ) {

  $n = 0;
  $ret = array ();

  //echo "<br />\nChecking " . count ( $events ) . " events.  TZ_OFFSET = $TZ_OFFSET, get_unapproved=" . $get_unapproved . "<br />\n";

  //print_r ( $events );

  for ( $i = 0; $i < count ( $events ); $i++ ) {
    // In case of data corruption (or some other bug...)
    if ( empty ( $events[$i] ) || empty ( $events[$i]['cal_id'] ) )
      continue;
    if ( ( ! $get_unapproved ) && $events[$i]['cal_status'] == 'W' ) {
      // ignore this event
    //don't adjust anything  if  no TZ offset or ALL Day Event or Untimed
    } else if ( empty ( $this->TZ_OFFSET) ||  ( $events[$i]['cal_time'] <= 0 ) ) {
      if ( $events[$i]['cal_date'] == $date )
        $ret[$n++] = $events[$i];
    } else if ( $this->TZ_OFFSET > 0 ) {
      $cutoff = ( 24 - $this->TZ_OFFSET ) * 10000;
      //echo "<br /> cal_time " . $events[$i]['cal_time'] . "<br />\n";
      $sy = substr ( $date, 0, 4 );
      $sm = substr ( $date, 4, 2 );
      $sd = substr ( $date, 6, 2 );
      $prev_day = date ( ( "Ymd" ), mktime ( 3, 0, 0, $sm, $sd - 1, $sy ) );
        //echo "prev_date = $prev_day <br />\n";
      if ( $events[$i]['cal_date'] == $date &&
        $events[$i]['cal_time'] == -1 ) {
        $ret[$n++] = $events[$i];
        //echo "added event $events[$i][cal_id] <br />\n";
      } else if ( $events[$i]['cal_date'] == $date &&
        $events[$i]['cal_time'] < $cutoff ) {
        $ret[$n++] = $events[$i];
        //echo "added event {$events[$i][cal_id]} <br />\n";
      } else if ( $events[$i]['cal_date'] == $prev_day &&
        $events[$i]['cal_time'] >= $cutoff ) {
        $ret[$n++] = $events[$i];
        //echo "added event {$events[$i][cal_id]} <br />\n";
      }
    } else {
      //TZ < 0
      $cutoff = ( 0 - $this->TZ_OFFSET ) * 10000;
      //echo "<br />\ncal_time " . $events[$i]['cal_time'] . "<br />\n";
      $sy = substr ( $date, 0, 4 );
      $sm = substr ( $date, 4, 2 );
      $sd = substr ( $date, 6, 2 );
      $next_day = date ( ( "Ymd" ), mktime ( 3, 0, 0, $sm, $sd + 1, $sy ) );
      //echo "next_date = $next_day <br />\n";
      if ( $events[$i]['cal_time'] == -1 ) {
  if ( $events[$i]['cal_date'] == $date ) {
          $ret[$n++] = $events[$i];
          //echo "added event $events[$i][cal_id] <br />\n";
        }
      } else {
  if ( $events[$i]['cal_date'] == $date &&
          $events[$i]['cal_time'] > $cutoff ) {
          $ret[$n++] = $events[$i];
          //echo "added event $events[$i][cal_id] <br />\n";
        } else if ( $events[$i]['cal_date'] == $next_day &&
          $events[$i]['cal_time'] <= $cutoff ) {
          $ret[$n++] = $events[$i];
          //echo "added event $events[$i][cal_id] <br />\n";
        }
      }
    }
  }
  return $ret;
}

function queryEventsRepeat($calendar_id, $date_filter, $cat_id = ''){

    $dbh = new DB_Mysql();
    $sql = "SELECT ce.calendar_entry_name, ce.calendar_entry_description, ce.calendar_entry_date, 
				   ce.calendar_entry_time, ce.calendar_entry_id, ce.calendar_entry_ext_id, 
      			   ce.calendar_entry_priority, ce.calendar_entry_access, ce.calendar_entry_duration, 
      			   cec.calendar_entry_status, cec.calendar_entry_category, cec.calendar_id, 
                   cer.calendar_entry_type, cer.calendar_entry_end, 
          		   cer.calendar_entry_frequency, cer.calendar_entry_days 
          	  FROM calendar_entry AS ce, calendar_entry_repeats AS cer, calendar_entry_calendars AS cec 
             WHERE ce.calendar_entry_id = cer.calendar_entry_id AND ce.calendar_entry_id = cec.calendar_entry_id 
               AND cec.calendar_entry_status IN ('A','W') ";
    if ( $cat_id != '' ) {
        $sql .= "AND cec.calendar_entry_category LIKE '$cat_id' ";
    }
    if ( strlen ( $calendar_id ) > 0 ){
        $sql .= "AND (cec.calendar_id = '" . $calendar_id . "' ";
    }
    $sql .= $date_filter;
    $sql .= " ORDER BY ce.calendar_entry_time, ce.calendar_entry_id";
    
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    return $stmt->fetchall_row();
}

function queryEventsNoRepeat($calendar_id, $date_filter, $cat_id = ''){
    
    $dbh = new DB_Mysql();
    $sql = "SELECT ce.calendar_entry_name, ce.calendar_entry_description, ce.calendar_entry_date, 
  				   ce.calendar_entry_time, ce.calendar_entry_id, ce.calendar_entry_ext_id, 
  			       ce.calendar_entry_priority, ce.calendar_entry_access, ce.calendar_entry_duration, 
  			       cec.calendar_entry_status, cec.calendar_entry_category, cec.calendar_id
			  FROM calendar_entry AS ce, calendar_entry_calendars AS cec 
             WHERE ce.calendar_entry_id = cec.calendar_entry_id 
               AND cec.calendar_entry_status IN ('A','W') ";
    if ( $cat_id != '' ){
        $sql .= "AND cec.calendar_entry_category LIKE '$cat_id' ";
    }
    
    if ( strlen ( $calendar_id ) > 0 ){
        $sql .= "AND (cec.calendar_id = '" . $calendar_id . "' ";
    }
    $sql .= $date_filter;
    $sql .= " ORDER BY ce.calendar_entry_time, ce.calendar_entry_id";

    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    return $stmt->fetchall_row();
}
    
function eventRepeatsNot($calendar_entry_id){
    
    $dbh = new DB_Mysql();
    $sql = "SELECT calendar_entry_skipdate FROM calendar_entry_repeats_not WHERE calendar_entry_id = ".$calendar_entry_id;
    $dbh->prepare($sql);
    $stmt->execute();
    return $stmt->fetchall_row();
}

function query_events ( $calendar_id, $want_repeated, $date_filter, $cat_id = '' ) {
    $result = array ();
    $layers_byuser = array ();
    //do we want repeats
    if($want_repeated)
    {
      $events = $this->queryEventsRepeat($calendar_id, $date_filter, $cat_id);
    }
    else {
      $events = $this->queryEventsNoRepeat($calendar_id, $date_filter, $cat_id);
    }
    $i = 0;
    foreach ($events as $row ) 
    {
        if ($row[9] == 'R' || $row[9] == 'D') {
            continue;  // don't show rejected/deleted ones
        }
        $result[] = $row;
        $i++;
    }
    // add the exceptions to our result array
    if ( $want_repeated ) 
    {
        for ( $i = 0; $i < count ( $result ); $i++ ) 
        {
            if ( ! empty ( $result[$i]['calendar_entry_id'] ) ) 
            {
                $no_repeat = $this->eventRepeatsNot($result[$i]['calendar_entry_id']);
                foreach ( $no_repeat as $row ) 
                {
                    $result[$i]['calendar_entry_exceptions'][] = $row[0];
                }
            }
        }
    }
    return $result;
}


function isCreatorOrParticipant($user, $id) {
    
    // is this user a participant or the creator of the event?
    $sql = "SELECT webcal_entry.cal_id FROM webcal_entry, " .
    	"webcal_entry_user WHERE webcal_entry.cal_id = " .
    	"webcal_entry_user.cal_id AND webcal_entry.cal_id = :2: " .
    	"AND (webcal_entry.cal_create_by = :1: " .
    	"OR webcal_entry_user.user_id = :1:)";
    $dbh = new DB_Mysql();
    $stmt = $dbh->prepare($sql);
    $stmt->execute($user, $id);
    $row = $stmt->fetch_row ( );
    if ( $row && $row[0] > 0 ) 
    {
        return true;
    }
    return false;
    
}
    

/**
 * Gets the list of external users for an event from the webcal_entry_ext_user table in an HTML format.
 *
 * @param int $event_id   Event ID
 * @param int $use_mailto When set to 1, email address will contain an href
 *                        link with a mailto URL.
 *
 * @return string The list of external users for an event formatte in HTML.
 */
function event_get_external_users ( $event_id, $use_mailto=0 ) {
  global $error;
  $ret = "";

$dbh = new DB_Mysql();
$stmt = $dbh->prepare("SELECT cal_fullname, cal_email " .
    "FROM webcal_entry_ext_user " .
    "WHERE cal_id = $event_id " .
    "ORDER by cal_fullname");
$stmt->execute();
    while ( $row = dbi_fetch_row ( $res ) ) {
      if ( strlen ( $ret ) )
        $ret .= "\n";
      // Remove [\d] if duplicate name
      $trow = trim( preg_replace( '/\[[\d]]/' , "", $row[0] ) );
      $ret .= $trow;
      if ( strlen ( $row[1] ) ) {
        if ( $use_mailto ) {
          $ret .= " <a href=\"mailto:$row[1]\">&lt;" .
            htmlentities ( $row[1] ) . "&gt;</a>";
        } else {
          $ret .= " &lt;". htmlentities ( $row[1] ) . "&gt;";
        }
      }
    }
  return $ret;
}

/**
 * Checks for any unnaproved events.
 *
 * If any are found, display a link to the unapproved events (where they can be
 * approved).
 *
 * If the user is an admin user, also count up any public events.
 * If the user is a nonuser admin, count up events on the nonuser calendar.
 *
 * @param string $user Current user login
 */
function display_unapproved_events ( $user ) {
  global $public_access, $is_admin, $nonuser_enabled, $login;

  // Don't do this for public access login, admin user must approve public
  // events
  if ( $user == "__public__" )
    return;

  $sql = "SELECT COUNT(webcal_entry_user.cal_id) " .
    "FROM webcal_entry_user, webcal_entry " .
    "WHERE webcal_entry_user.cal_id = webcal_entry.cal_id " .
    "AND webcal_entry_user.cal_status = 'W' " .
    "AND ( webcal_entry.cal_ext_for_id IS NULL " .
    "OR webcal_entry.cal_ext_for_id = 0 ) " .
    "AND ( webcal_entry_user.user_id = '$user'";
  if ( $public_access == "Y" && $is_admin ) {
    $sql .= " OR webcal_entry_user.user_id = '__public__'";
  }
  if ( $nonuser_enabled == 'Y' ) {
    $admincals = Calendar::get_nonuser_cals ( $login );
    for ( $i = 0; $i < count ( $admincals ); $i++ ) {
      $sql .= " OR webcal_entry_user.user_id = '" .
        $admincals[$i]['cal_login'] . "'";
    }
  }
  $sql .= " )";
  //print "SQL: $sql<br />\n";
$dbh = new DB_Mysql();
$stmt = $dbh->prepare($sql);

  if ( $stmt->execute() ) {
    if ( $row = $stmt->fetch_row (  ) ) {
      if ( $row[0] > 0 ) {
 $str = translate ("You have XXX unapproved events");
 $str = str_replace ( "XXX", $row[0], $str );
        echo "<a class=\"nav\" href=\"list_unapproved.php";
        if ( $user != $login )
          echo "?user=$user\"";
        echo "\">" . $str .  "</a><br />\n";
      }
    }
  }
}


}
?>