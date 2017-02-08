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

class Framework_helper{
    
private $browser;
private $views;
private $prefarray;
private $has_boss;
private $allow_color_customization;
private $layers;
private $LAYERS_STATUS;

private $is_assistant;
private $categories;
private $category_owners;
private $categories_enabled;
private $g_settings;

private $WEEKENDBG;
private $THFG;
private $THBG;
private $PHP_SELF;
private $TODAYCELLBG;
private $CELLBG;
private $TABLEBG;
private $TEXTCOLOR;
private $TABLECELLFG;
private $POPUP_FG;
private $BGCOLOR;
private $LANGUAGE;
private $CUSTOM_HEADER;
private $CUSTOM_SCRIPT;

private $cat_id;
function setCatId ($a){
    $this->cat_id = $a;
}
function getCatId()
{
    return $this->cat_id;
}

private $cat_url;
function setCatURL ($a){
    $this->cat_url = $a;
}
function getCatURL()
{
    return $this->cat_url;
}
private $PUBLIC_ACCESS_FULLNAME;


private $friendly;
function setFriendly ($a){
    $this->friendly = $a;
}
function getFriendly()
{
    return $this->friendly;
}
private $TZ_OFFSET;

private $self;//the full path to the script
function setSelf ($a){
    $this->self = $a;
}
function getSelf()
{
    return $this->self;
}
private $script;//the script, without the path e.g day.php, week.php, month.php
function setScript ($a){
    $this->script = $a;
}
function getScript()
{
    return $this->script;
}

private $validate_redirect;// first set in init.php
private $session_not_found;// first set in init.php
private $not_auth;// first set in init.php

public static $LOG_CREATE = "C"; 
public static $LOG_APPROVE = "A";
public static $LOG_REJECT = "X";
public static $LOG_UPDATE = "U";
public static $LOG_DELETE = "D";
public static $LOG_NOTIFICATION = "N";
public static $LOG_REMINDER = "R";
public $WORK_DAY_START_HOUR;
public $WORK_DAY_END_HOUR;
public $DISPLAY_UNAPPROVED;
public $DISPLAY_WEEKNUMBER;

private static $PROGRAM_VERSION = "v1.0";
private static $PROGRAM_DATE = "19 Sept 2011";
private static $PROGRAM_NAME = "NetPropMan";

function getProgramNameVersion()
{
    return $this->PROGRAM_NAME." ".$this->PROGRAM_VERSION." (".$this->PROGRAM_DATE.")";
}   
private static $PROGRAM_URL = "http://mimirsoft.com/";
private static $TROUBLE_URL = "youre_screwed.php";


// Language options  The first is the name presented to users while
// the second is the filename (without the ".txt") that must exist
// in the translations subdirectory.
private static $languages = array (
  "Browser-defined" =>"none",
  "English" =>"English-US",
  "Basque" => "Basque",
  "Bulgarian" => "Bulgarian",
  "Catalan" => "Catalan",
  "Chinese (Traditonal/Big5)" => "Chinese-Big5",
  "Chinese (Simplified/GB2312)" => "Chinese-GB2312",
  "Czech" => "Czech",
  "Danish" => "Danish",
  "Dutch" =>"Dutch",
  "Estonian" => "Estonian",
  "Finnish" =>"Finnish",
  "French" =>"French",
  "Galician" => "Galician",
  "German" =>"German",
  "Greek" =>"Greek",
  "Holo (Taiwanese)" => "Holo-Big5",
  "Hungarian" =>"Hungarian",
  "Icelandic" => "Icelandic",
  "Italian" => "Italian",
  "Japanese(SHIFT JIS)" => "Japanese",
  "Japanese(EUC-JP)" => "Japanese-eucjp",
  "Japanese(UTF-8)" => "Japanese-utf8",
  "Korean" =>"Korean",
  "Norwegian" => "Norwegian",
  "Polish" => "Polish",
  "Portuguese" =>"Portuguese",
  "Portuguese/Brazil" => "Portuguese_BR",
  "Romanian" => "Romanian",
  "Russian" => "Russian",
  "Spanish" =>"Spanish",
  "Swedish" =>"Swedish",
  "Turkish" =>"Turkish",
  "Welsh" =>"Welsh"
  // add new languages here!  (don't forget to add a comma at the end of
  // last line above.)
);

// If the user sets "Browser-defined" as their language setting, then
// use the $HTTP_ACCEPT_LANGUAGE settings to determine the language.
// The array below translates browser language abbreviations into
// our available language files.
// NOTE: These should all be lowercase on the left side even though
// the proper listing is like "en-US"!
private static $browser_languages = array (
  "eu" => "Basque",
  "bg" => "Bulgarian",
  "ca" => "Catalan",
  "zh" => "Chinese-GB2312",    // Simplified Chinese
  "zh-cn" => "Chinese-GB2312",
  "zh-tw" => "Chinese-Big5",   // Traditional Chinese
  "cs" => "Czech",
  "en" => "English-US",
  "en-us" => "English-US",
  "en-gb" => "English-US",
  "da" => "Danish",
  "nl" =>"Dutch",
  "ee" => "Estonian",
  "fi" =>"Finnish",
  "fr" =>"French",
  "fr-ch" =>"French", // French/Swiss
  "fr-ca" =>"French", // French/Canada
  "gl" => "Galician",
  "de" =>"German",
  "de-at" =>"German", // German/Austria
  "de-ch" =>"German", // German/Switzerland
  "de-de" =>"German", // German/German
  "el" =>"Greek", // German/German
  "hu" => "Hungarian",
  "zh-min-nan-tw" => "Holo-Big5",
  "is" => "Icelandic",
  "it" => "Italian",
  "it-ch" => "Italian", // Italian/Switzerland
  "ja" => "Japanese",
  "ko" =>"Korean",
  "no" => "Norwegian",
  "pl" => "Polish",
  "pt" =>"Portuguese",
  "pt-br" => "Portuguese_BR", // Portuguese/Brazil
  "ro" =>"Romanian",
  "ru" =>"Russian",
  "es" =>"Spanish",
  "sv" =>"Swedish",
  "tr" =>"Turkish",
  "cy" => "Welsh"
);

/** Maps page filenames to the id that page's <body> tag will have
 *
 * @global array $bodyid
 */
private $bodyid = array(
 "activity_log.php" => "activitylog",
 "add_entry.php" => "addentry",
 "admin.php" => "admin",
 "adminhome.php" => "adminhome",
 "approve_entry.php" => "approveentry",
 "assistant_edit.php" => "assistantedit",
 "category.php" => "category",
 "day.php" => "day",
 "del_entry.php" => "delentry",
 "del_layer.php" => "dellayer",
 "edit_entry.php" => "editentry",
 "edit_layer.php" => "editlayer",
 "edit_report.php" => "editreport",
 "edit_template.php" => "edittemplate",
 "edit_user.php" => "edituser",
 "edit_user_handler.php" => "edituserhandler",
 "export.php" => "export",
 "group_edit.php" => "groupedit",
 "group_edit_handler.php" => "groupedithandler",
 "groups.php" => "groups",
 "help_admin.php" => "helpadmin",
 "help_bug.php" => "helpbug",
 "help_edit_entry.php" => "helpeditentry",
 "help_import.php" => "helpimport",
 "help_index.php" => "helpindex",
 "help_layers.php" => "helplayers",
 "help_pref.php" => "helppref",
 "import.php" => "import",
 "index.php" => "index",
 "layers.php" => "layers",
 "layers_toggle.php" => "layerstoggle",
 "list_unapproved.php" => "listunapproved",
 "login.php" => "login",
 "month.php" => "month",
 "nonusers.php" => "nonusers",
 "pref.php" => "pref",
 "publish.php" => "publish",
 "purge.php" => "purge",
 "reject_entry.php" => "rejectentry",
 "report.php" => "report",
 "search.php" => "search",
 "select_user.php" => "selectuser",
 "set_entry_cat.php" => "setentrycat",
 "users.php" => "users",
 "usersel.php" => "usersel",
 "view_d.php" => "viewd",
 "view_entry.php" => "viewentry",
 "view_l.php" => "viewl",
 "view_m.php" => "viewm",
 "view_t.php" => "viewt",
 "view_v.php" => "viewv",
 "view_w.php" => "vieww",
 "views.php" => "views",
 "views_edit.php" => "viewsedit",
 "week.php" => "week",
 "week_details.php" => "weekdetails",
 "week_ssi.php" => "weekssi",
 "year.php" => "year"
);
public function getBodyId($id)
{
    return $this->bodyid[$id];
}

public function  getCategoriesEnabled()
{ 
    return $this->categories_enabled;
}

/**
 * Gets an integer value resulting from an HTTP GET or HTTP POST method.
 *
 * <b>Note:</b> The return value will be affected by the value of
 * <var>magic_quotes_gpc</var> in the php.ini file.
 *
 * @param string $name  Name used in the HTML form or found in the URL
 * @param bool   $fatal Is it considered a fatal error requiring execution to
 *                      stop if the value retrieved does not match the format
 *                      regular expression?
 *
 * @return string The value used in the HTML form (or URL)
 *
 * @uses getValue
 */
function getIntValue ( $name, $fatal=false ) {
  $val = getValue ( $name, "-?[0-9]+", $fatal );
  return $val;
}

/**
 * Gets the list of active plugins.
 *
 * Should be called after {@link load_global_settings()} and {@link load_user_preferences()}.
 *
 * @internal cek: ignored since I am not sure this will ever be used...
 *
 * @return array Active plugins
 *
 * @ignore
 */
function get_plugin_list ( $include_disabled=false ) {
  // first get list of available plugins
  $sql = "SELECT cal_setting FROM webcal_config " .
    "WHERE cal_setting LIKE '%.plugin_status'";
  if ( ! $include_disabled )
    $sql .= " AND cal_value = 'Y'";
  $sql .= " ORDER BY cal_setting";
$dbh = new DB_Mysql();
$stmt = $dbh->prepare($sql);
$stmt->execute();
$plugins = array ();
    while ( $row = $stmt->fetch_row())
    {
        $e = explode ( ".", $row[0] );
        if ( $e[0] != "" ) 
        {
            $plugins[] = $e[0];
        }
    }
    if ( count ( $plugins ) == 0 ) 
    {
        $plugins[] = "webcalendar";
    }
    return $plugins;
}

/**
 * Get plugins available to the current user.
 *
 * Do this by getting a list of all plugins that are not disabled by the
 * administrator and make sure this user has not disabled any of them.
 * 
 * It's done this was so that when an admin adds a new plugin, it shows up on
 * each users system automatically (until they disable it).
 *
 * @return array Plugins available to current user
 *
 * @ignore
 */
function get_user_plugin_list () {
  $ret = array ();
  $all_plugins = get_plugin_list ();
  for ( $i = 0; $i < count ( $all_plugins ); $i++ ) {
    if ( $GLOBALS[$all_plugins[$i] . ".disabled"] != "N" )
      $ret[] = $all_plugins[$i];
  }
  return $ret;
}



/**
 * Logs a debug message.
 *
 * Generally, we do not leave calls to this function in the code.  It is used
 * for debugging only.
 *
 * @param string $msg Text to be logged
 */
function do_debug ( $msg ) {
  // log to /tmp/webcal-debug.log
  //error_log ( date ( "Y-m-d H:i:s" ) .  "> $msg\n",
  //  3, "/tmp/webcal-debug.log" );
  //error_log ( date ( "Y-m-d H:i:s" ) .  "> $msg\n",
  //  2, "sockieman:2000" );
}

/**
 * Gets user's preferred view.
 *
 * The user's preferred view is stored in the $STARTVIEW global variable.  This
 * is loaded from the user preferences (or system settings if there are no user
 * prefererences.)
 *
 * @param string $indate Date to pass to preferred view in YYYYMMDD format
 * @param string $args   Arguments to include in the URL (such as "user=joe")
 *
 * @return string URL of the user's preferred view
 */
function get_preferred_view ( $indate="", $args="" ) {
  global $STARTVIEW, $thisdate;

  $url = empty ( $STARTVIEW ) ? "month.php" : $STARTVIEW;
  // We used to just store "month" in $STARTVIEW without the ".php"
  // This is just to prevent users from getting a "404 not found" if
  // they have not updated their preferences.
  if ( $url == "month" || $url == "day" || $url == "week" || $url == "year" )
    $url .= ".php";

  $url = str_replace ( '&amp;', '&', $url );
  $url = str_replace ( '&', '&amp;', $url );

  $xdate = empty ( $indate ) ? $thisdate : $indate;
  if ( ! empty ( $xdate ) ) {
    if ( strstr ( $url, "?" ) )
      $url .= '&amp;' . "date=$xdate";
    else
      $url .= '?' . "date=$xdate";
  }

  if ( ! empty ( $args ) ) {
    if ( strstr ( $url, "?" ) )
      $url .= '&amp;' . $args;
    else
      $url .= '?' . $args;
  }

  return $url;
}

/**
 * Sends a redirect to the user's preferred view.
 *
 * The user's preferred view is stored in the $STARTVIEW global variable.  This
 * is loaded from the user preferences (or system settings if there are no user
 * prefererences.)
 *
 * @param string $indate Date to pass to preferred view in YYYYMMDD format
 * @param string $args   Arguments to include in the URL (such as "user=joe")
 */
function send_to_preferred_view ( $indate="", $args="" ) {
  $url = Framework_helper::get_preferred_view ( $indate, $args );
  Framework_helper::do_redirect ( $url );
}



/**
 * Generates a cookie that saves the last calendar view.
 *
 * Cookie is based on the current <var>$REQUEST_URI</var>.
 *
 * We save this cookie so we can return to this same page after a user
 * edits/deletes/etc an event.
 *
 * @global string Request string
 */
function remember_this_view () {
  global $REQUEST_URI;
  if ( empty ( $REQUEST_URI ) )
    $REQUEST_URI = $_SERVER["REQUEST_URI"];

  // do not use anything with friendly in the URI
  if ( strstr ( $REQUEST_URI, "friendly=" ) )
    return;

  SetCookie ( "webcalendar_last_view", $REQUEST_URI );
}

/**
 * Gets the last page stored using {@link remember_this_view()}.
 *
 * @return string The URL of the last view or an empty string if it cannot be
 *                determined.
 *
 * @global array Cookies
 */
function get_last_view () {
  global $HTTP_COOKIE_VARS;
  $val = '';

  if ( isset ( $_COOKIE["webcalendar_last_view"] ) ) {
	  $HTTP_COOKIE_VARS["webcalendar_last_view"] = $_COOKIE["webcalendar_last_view"];
    $val = $_COOKIE["webcalendar_last_view"];
  } else if ( isset ( $HTTP_COOKIE_VARS["webcalendar_last_view"] ) ) {
    $val = $HTTP_COOKIE_VARS["webcalendar_last_view"];
	}
  $val =   str_replace ( "&", "&amp;", $val );
  return $val;
}

/**
 * Sends HTTP headers that tell the browser not to cache this page.
 *
 * Different browser use different mechanisms for this, so a series of HTTP
 * header directives are sent.
 *
 * <b>Note:</b> This function needs to be called before any HTML output is sent
 * to the browser.
 */
function send_no_cache_header () {
  header ( "Expires: Mon, 26 Jul 1997 05:00:00 GMT" );
  header ( "Last-Modified: " . gmdate ( "D, d M Y H:i:s" ) . " GMT" );
  header ( "Cache-Control: no-store, no-cache, must-revalidate" );
  header ( "Cache-Control: post-check=0, pre-check=0", false );
  header ( "Pragma: no-cache" );
}


/**
 * Adds something to the activity log for an event.
 *
 * The information will be saved to the webcal_entry_log table.
 *
 * @param int    $event_id Event ID
 * @param string $user     Username of user doing this
 * @param string $user_cal Username of user whose calendar is affected
 * @param string $type     Type of activity we are logging:
 *   - $LOG_CREATE
 *   - $LOG_APPROVE
 *   - $LOG_REJECT
 *   - $LOG_UPDATE
 *   - $LOG_DELETE
 *   - $LOG_NOTIFICATION
 *   - $LOG_REMINDER
 * @param string $text     Text comment to add with activity log entry
 */
function activity_log ( $event_id, $user, $user_cal, $type, $text ) {
  $next_id = 1;

  if ( empty ( $type ) ) {
    echo "Error: type not set for activity log!";
    // but don't exit since we may be in mid-transaction
    return;
  }
$dbh = new DB_Mysql();
$stmt = $dbh->prepare("SELECT MAX(cal_log_id) FROM webcal_entry_log");
$stmt->execute();

    if ( $row = $stmt->fetch_row ( ) ) {
      $next_id = $row[0] + 1;
    }

  $date = date ( "Ymd" );
  $time = date ( "Gis" );
  $sql_text = empty ( $text ) ? "NULL" : "'$text'";
  $sql_user_cal = empty ( $user_cal ) ? "NULL" : "'$user_cal'";

$stmt = $dbh->prepare("INSERT INTO webcal_entry_log ( " .
    "cal_log_id, cal_entry_id, user_id, cal_user_cal, cal_type, " .
    "cal_date, cal_time, cal_text ) VALUES ( $next_id, $event_id, " .
    "'$user', $sql_user_cal, '$type', $date, $time, $sql_text )");


  if ( ! $stmt->execute() ) {
    echo "Database error: " . dbi_error ();
    echo "<br />\nSQL:<br />\n$sql";
    exit;
  }
}

/**
 * Gets a list of users.
 *
 * If groups are enabled, this will restrict the list of users to only those
 * users who are in the same group(s) as the user (unless the user is an admin
 * user).  We allow admin users to see all users because they can also edit
 * someone else's events (so they may need access to users who are not in the
 * same groups that they are in).
 *
 * @return array Array of users, where each element in the array is an array
 *               with the following keys:
 *    - cal_login
 *    - cal_lastname
 *    - cal_firstname
 *    - cal_is_admin
 *    - cal_is_admin
 *    - cal_email
 *    - cal_password
 *    - cal_fullname
 */
function get_my_users () {
    global $login, $is_admin, $groups_enabled, $user_sees_only_his_groups;
    
    if ( $groups_enabled == "Y" && $user_sees_only_his_groups == "Y" &&
        ! $is_admin ) {
    // get groups that current user is in
    $dbh = new DB_Mysql();
    $stmt = $dbh->prepare("SELECT cal_group_id FROM webcal_group_user " .
        "WHERE `user_id` = '$login'");
    $stmt->execute();
    $groups = array ();
    while ( $row = $stmt->fetch_row () ) {
    $groups[] = $row[0];
    }
    
    $u = user_get_users ();
    $u_byname = array ();
    for ( $i = 0; $i < count ( $u ); $i++ ) {
      $name = $u[$i]['cal_login'];
      $u_byname[$name] = $u[$i];
    }
    $ret = array ();
    if ( count ( $groups ) == 0 ) {
      // Eek.  User is in no groups... Return only themselves
      $ret[] = $u_byname[$login];
      return $ret;
    }
    // get list of users in the same groups as current user
    $sql = "SELECT DISTINCT(webcal_group_user.user_id), cal_lastname, cal_firstname from webcal_group_user " .
      "LEFT JOIN user_main ON webcal_group_user.user_id = user_main.user_id " .
      "WHERE cal_group_id ";
    if ( count ( $groups ) == 1 )
      $sql .= "= " . $groups[0];
    else {
      $sql .= "IN ( " . implode ( ", ", $groups ) . " )";
    }
    $sql .= " ORDER BY user_lastname, user_firstname, username";
    //echo "SQL: $sql <br />\n";
    $stmt = $dbh->prepare($sql);
    if ( $stmt->execute() ) {
      while ( $row = $stmt->fetch_row ( ) ) {
        $ret[] = $u_byname[$row[0]];
      }
    }
    return $ret;
  } else {
    // groups not enabled... return all users
    //echo "No groups. ";
    return Framework_helper::user_get_users ();
  }
}



/**
 * Generates the HTML used in an event popup for the site_extras fields of an event.
 *
 * @param int $id Event ID
 *
 * @return string The HTML to be used within the event popup for any site_extra
 *                fields found for the specified event
 */
function site_extras_for_popup ( $id ) {
  global $site_extras_in_popup, $site_extras;
  // These are needed in case the site_extras.php file was already
  // included.
  global $EXTRA_TEXT, $EXTRA_MULTILINETEXT, $EXTRA_URL, $EXTRA_DATE,
    $EXTRA_EMAIL, $EXTRA_USER, $EXTRA_REMINDER, $EXTRA_SELECTLIST;
  global $EXTRA_REMINDER_WITH_DATE, $EXTRA_REMINDER_WITH_OFFSET,
    $EXTRA_REMINDER_DEFAULT_YES;

  $ret = '';

  if ( $site_extras_in_popup != 'Y' )
    return '';

  include_once 'includes/site_extras.php';

  $extras = get_site_extra_fields ( $id );
  for ( $i = 0; $i < count ( $site_extras ); $i++ ) {
    $extra_name = $site_extras[$i][0];
    $extra_type = $site_extras[$i][2];
    $extra_arg1 = $site_extras[$i][3];
    $extra_arg2 = $site_extras[$i][4];
    if ( ! empty ( $extras[$extra_name]['cal_name'] ) ) {
      $ret .= "<dt>" .  translate ( $site_extras[$i][1] ) . ":</dt>\n<dd>";
      if ( $extra_type == $EXTRA_DATE ) {
        if ( $extras[$extra_name]['cal_date'] > 0 )
          $ret .= date_to_str ( $extras[$extra_name]['cal_date'] );
      } else if ( $extra_type == $EXTRA_TEXT ||
        $extra_type == $EXTRA_MULTILINETEXT ) {
        $ret .= nl2br ( $extras[$extra_name]['cal_data'] );
      } else if ( $extra_type == $EXTRA_REMINDER ) {
        if ( $extras[$extra_name]['cal_remind'] <= 0 )
          $ret .= translate ( "No" );
        else {
          $ret .= translate ( "Yes" );
          if ( ( $extra_arg2 & $EXTRA_REMINDER_WITH_DATE ) > 0 ) {
            $ret .= "&nbsp;&nbsp;-&nbsp;&nbsp;";
            $ret .= date_to_str ( $extras[$extra_name]['cal_date'] );
          } else if ( ( $extra_arg2 & $EXTRA_REMINDER_WITH_OFFSET ) > 0 ) {
            $ret .= "&nbsp;&nbsp;-&nbsp;&nbsp;";
            $minutes = $extras[$extra_name]['cal_data'];
            $d = (int) ( $minutes / ( 24 * 60 ) );
            $minutes -= ( $d * 24 * 60 );
            $h = (int) ( $minutes / 60 );
            $minutes -= ( $h * 60 );
            if ( $d > 0 )
              $ret .= $d . "&nbsp;" . translate("days") . "&nbsp;";
            if ( $h > 0 )
              $ret .= $h . "&nbsp;" . translate("hours") . "&nbsp;";
            if ( $minutes > 0 )
              $ret .= $minutes . "&nbsp;" . translate("minutes");
            $ret .= "&nbsp;" . translate("before event" );
          }
        }
      } else {
        $ret .= $extras[$extra_name]['cal_data'];
      }
      $ret .= "</dd>\n";
    }
  }
  return $ret;
}

/**
 * Prints out a date selection box for use in a form.
 *
 * @param string $prefix Prefix to use in front of form element names
 * @param int    $date   Currently selected date (in YYYYMMDD format)
 *
 * @uses date_selection_html
 */
function print_date_selection ( $prefix, $date ) {
  print date_selection_html ( $prefix, $date );
}

/**
 * Generate HTML for a date selection for use in a form.
 *
 * @param string $prefix Prefix to use in front of form element names
 * @param int    $date   Currently selected date (in YYYYMMDD format)
 *
 * @return string HTML for the selection box
 */
function date_selection_html ( $prefix, $date ) {
  $ret = "";
  $num_years = 20;
  if ( strlen ( $date ) != 8 )
    $date = date ( "Ymd" );
  $thisyear = $year = substr ( $date, 0, 4 );
  $thismonth = $month = substr ( $date, 4, 2 );
  $thisday = $day = substr ( $date, 6, 2 );
  if ( $thisyear - date ( "Y" ) >= ( $num_years - 1 ) )
    $num_years = $thisyear - date ( "Y" ) + 2;
  $ret .= "<select name=\"" . $prefix . "day\">\n";
  for ( $i = 1; $i <= 31; $i++ )
    $ret .= "<option value=\"$i\"" .
      ( $i == $thisday ? " selected=\"selected\"" : "" ) . ">$i</option>\n";
  $ret .= "</select>\n<select name=\"" . $prefix . "month\">\n";
  for ( $i = 1; $i <= 12; $i++ ) {
    $m = month_short_name ( $i - 1 );
    $ret .= "<option value=\"$i\"" .
      ( $i == $thismonth ? " selected=\"selected\"" : "" ) . ">$m</option>\n";
  }
  $ret .= "</select>\n<select name=\"" . $prefix . "year\">\n";
  for ( $i = -10; $i < $num_years; $i++ ) {
    $y = $thisyear + $i;
    $ret .= "<option value=\"$y\"" .
      ( $y == $thisyear ? " selected=\"selected\"" : "" ) . ">$y</option>\n";
  }
  $ret .= "</select>\n";
  $ret .= "<input type=\"button\" onclick=\"selectDate( '" .
    $prefix . "day','" . $prefix . "month','" . $prefix . "year',$date, event)\" value=\"" .
    translate("Select") . "...\" />\n";

  return $ret;
}

/** 
 * Gets any site-specific fields for an entry that are stored in the database in the webcal_site_extras table.
 *
 * @param int $eventid Event ID
 *
 * @return array Array with the keys as follows:
 *    - <var>cal_name</var>
 *    - <var>cal_type</var>
 *    - <var>cal_date</var>
 *    - <var>cal_remind</var>
 *    - <var>cal_data</var>
 */
function get_site_extra_fields ( $eventid ) {
  $sql = "SELECT cal_name, cal_type, cal_date, cal_remind, cal_data " .
    "FROM webcal_site_extras " .
    "WHERE cal_id = $eventid";
    $dbh = new DB_Mysql();
    $stmt = $dbh->prepare( $sql);
    $extras = array ();
    if ( $stmt->execute() ) {
        while ( $row =$stmt->fetch_row (  ) ) {
        // save by cal_name (e.g. "URL")
        $extras[$row[0]] = array (
            "cal_name" => $row[0],
            "cal_type" => $row[1],
            "cal_date" => $row[2],
            "cal_remind" => $row[3],
            "cal_data" => $row[4]
        );
        }
    }
    return $extras;
}


/**
 * Generates the HTML for an add/edit/delete icon.
 *
 * This function is not yet used.  Some of the places that will call it have to
 * be updated to also get the event owner so we know if the current user has
 * access to edit and delete.
 *
 * @param int  $id         Event ID
 * @param bool $can_edit   Can this user edit this event?
 * @param bool $can_delete Can this user delete this event?
 *
 * @return HTML for add/edit/delete icon.
 *
 * @ignore
 */
function icon_text ( $id, $can_edit, $can_delete ) 
{
  global $is_admin;
  $ret = "<a title=\"" . 
  translate("View this entry") . "\" href=\"view_entry.php?id=$id\"><img src=\"image/view.gif\" alt=\"" . 
  translate("View this entry") . "\" style=\"border-width:0px; width:10px; height:10px;\" /></a>";
  if ( $can_edit && $readonly == "N" )
    $ret .= "<a title=\"" . 
  translate("Edit entry") . "\" href=\"edit_entry.php?id=$id\"><img src=\"image/edit.gif\" alt=\"" . 
  translate("Edit entry") . "\" style=\"border-width:0px; width:10px; height:10px;\" /></a>";
  if ( $can_delete && ( $readonly == "N" || $is_admin ) )
    $ret .= "<a title=\"" . 
      translate("Delete entry") . "\" href=\"del_entry.php?id=$id\" onclick=\"return confirm('" .
  translate("Are you sure you want to delete this entry?") . "\\n\\n" . 
  translate("This will delete this entry for all users.") . "');\"><img src=\"image/delete.gif\" alt=\"" . 
  translate("Delete entry") . "\" style=\"border-width:0px; width:10px; height:10px;\" /></a>";
  return $ret;
}

/**
 * Prints all the calendar entries for the specified user for the specified date.
 *
 * If we are displaying data from someone other than
 * the logged in user, then check the access permission of the entry.
 *
 * @param string $date Date in YYYYMMDD format
 * @param string $user Username
 * @param bool   $ssi  Is this being called from week_ssi.php?
 */
function print_date_entries ( $date, $user, $ssi ) {
  global $events, $readonly, $is_admin, $login,
    $public_access, $public_access_can_add, $cat_id;
  $cnt = 0;
  $get_unapproved = (@ $GLOBALS["DISPLAY_UNAPPROVED"] == "Y" );
  // public access events always must be approved before being displayed
  if ( $user == "__public__" )
    $get_unapproved = false;

  $year = substr ( $date, 0, 4 );
  $month = substr ( $date, 4, 2 );
  $day = substr ( $date, 6, 2 );
  $dateu = mktime ( 3, 0, 0, $month, $day, $year );
  $can_add = ( $readonly == "N" || $is_admin );
  if ( $public_access == "Y" && $public_access_can_add != "Y" &&
    $login == "__public__" )
    $can_add = false;
  if ( $readonly == 'Y' )
    $can_add = false;
  if ( ! $ssi && $can_add ) {
    print "<a title=\"" .
      translate("New Entry") . "\" href=\"edit_entry.php?";
    if ( strcmp ( $user, $GLOBALS["login"] ) )
      print "user=$user&amp;";
    if ( ! empty ( $cat_id ) )
      print "cat_id=$cat_id&amp;";
    print "date=$date\"><img src=\"image/new.gif\" alt=\"" .
      translate("New Entry") . "\" class=\"new\" /></a>";
    $cnt++;
  }
  if ( ! $ssi ) {
    echo "<a class=\"dayofmonth\" href=\"day.php?";
    if ( strcmp ( $user, $GLOBALS["login"] ) )
      echo "user=$user&amp;";
    if ( ! empty ( $cat_id ) )
      echo "cat_id=$cat_id&amp;";
    echo "date=$date\">$day</a>";
    if ( $this->DISPLAY_WEEKNUMBER == "Y" &&
      date ( "w", $dateu ) == $this->WEEK_START ) {
      echo "&nbsp;<a title=\"" .
        translate("Week") . "&nbsp;" . Calendar::week_number ( $dateu ) . "\" href=\"week.php?date=$date";
      if ( strcmp ( $user, $GLOBALS["login"] ) )
        echo "&amp;user=$user";
      if ( ! empty ( $cat_id ) )
      echo "&amp;cat_id=$cat_id";
       echo "\" class=\"weeknumber\">";
      echo "(" .
        translate("Week") . "&nbsp;" . Calendar::week_number ( $dateu ) . ")</a>";
    }
    print "<br />\n";
    $cnt++;
  }
  
  // get all the repeating events for this date and store in array $rep
  $rep = Calendar::get_repeating_entries ( $user, $date, $get_unapproved );
  $cur_rep = 0;

  // get all the non-repeating events for this date and store in $ev
  $ev = Calendar::get_entries ( $user, $date, $get_unapproved );

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
        $this->print_entry ( $viewid,
          $date, $rep[$cur_rep]['cal_time'], $rep[$cur_rep]['cal_duration'],
          $viewname, $rep[$cur_rep]['cal_description'],
          $rep[$cur_rep]['cal_status'], $rep[$cur_rep]['cal_priority'],
          $rep[$cur_rep]['cal_access'], $rep[$cur_rep]['cal_login'],
          $rep[$cur_rep]['cal_category'] );
        $cnt++;
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
      $this->print_entry ( $viewid,
        $date, $ev[$i]['cal_time'], $ev[$i]['cal_duration'],
        $viewname, $ev[$i]['cal_description'],
        $ev[$i]['cal_status'], $ev[$i]['cal_priority'],
        $ev[$i]['cal_access'], $ev[$i]['cal_login'],
        $ev[$i]['cal_category'] );
      $cnt++;
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
      $this->print_entry ( $viewid,
        $date, $rep[$cur_rep]['cal_time'], $rep[$cur_rep]['cal_duration'],
        $viewname, $rep[$cur_rep]['cal_description'],
        $rep[$cur_rep]['cal_status'], $rep[$cur_rep]['cal_priority'],
        $rep[$cur_rep]['cal_access'], $rep[$cur_rep]['cal_login'],
        $rep[$cur_rep]['cal_category'] );
      $cnt++;
    }
    $cur_rep++;
  }
  if ( $cnt == 0 )
    echo "&nbsp;"; // so the table cell has at least something
}

/**
 * Checks to see if two events overlap.
 *
 * @param string $time1 Time 1 in HHMMSS format
 * @param int    $duration1 Duration 1 in minutes
 * @param string $time2 Time 2 in HHMMSS format
 * @param int    $duration2 Duration 2 in minutes
 *
 * @return bool True if the two times overlap, false if they do not
 */
function times_overlap ( $time1, $duration1, $time2, $duration2 ) {
  //echo "times_overlap ( $time1, $duration1, $time2, $duration2 )<br />\n";
  $hour1 = (int) ( $time1 / 10000 );
  $min1 = ( $time1 / 100 ) % 100;
  $hour2 = (int) ( $time2 / 10000 );
  $min2 = ( $time2 / 100 ) % 100;
  // convert to minutes since midnight
  // remove 1 minute from duration so 9AM-10AM will not conflict with 10AM-11AM
  if ( $duration1 > 0 )
    $duration1 -= 1;
  if ( $duration2 > 0 )
    $duration2 -= 1;
  $tmins1start = $hour1 * 60 + $min1;
  $tmins1end = $tmins1start + $duration1;
  $tmins2start = $hour2 * 60 + $min2;
  $tmins2end = $tmins2start + $duration2;
  //echo "tmins1start=$tmins1start, tmins1end=$tmins1end, tmins2start=$tmins2start, tmins2end=$tmins2end<br />\n";
  if ( ( $tmins1start >= $tmins2end ) || ( $tmins2start >= $tmins1end ) )
    return false;
  return true;
}

/**
 * Checks for conflicts.
 *
 * Find overlaps between an array of dates and the other dates in the database.
 *
 * Limits on number of appointments: if enabled in System Settings
 * (<var>$limit_appts</var> global variable), too many appointments can also
 * generate a scheduling conflict.
 * 
 * @todo Update this to handle exceptions to repeating events
 *
 * @param array  $dates        Array of dates in YYYYMMDD format that is
 *                             checked for overlaps.
 * @param int    $duration     Event duration in minutes
 * @param int    $hour         Hour of event (0-23)
 * @param int    $minute       Minute of the event (0-59)
 * @param array  $participants Array of users whose calendars are to be checked
 * @param string $login        The current user name
 * @param int    $id           Current event id (this keeps overlaps from
 *                             wrongly checking an event against itself)
 *
 * @return Empty string for no conflicts or return the HTML of the
 *         conflicts when one or more are found.
 */
function check_for_conflicts ( $dates, $duration, $hour, $minute,
  $participants, $login, $id ) {
  global $single_user_login, $single_user;
  global $repeated_events, $limit_appts, $limit_appts_number;
  if (!count($dates)) return false;

  $evtcnt = array ();

  $sql = "SELECT distinct webcal_entry_user.user_id, webcal_entry.cal_time," .
    "webcal_entry.cal_duration, webcal_entry.cal_name, " .
    "webcal_entry.cal_id, webcal_entry.cal_ext_for_id, " .
    "webcal_entry.cal_access, " .
    "webcal_entry_user.cal_status, webcal_entry.cal_date " .
    "FROM webcal_entry, webcal_entry_user " .
    "WHERE webcal_entry.cal_id = webcal_entry_user.cal_id " .
    "AND (";
  for ($x = 0; $x < count($dates); $x++) {
    if ($x != 0) $sql .= " OR ";
    $sql.="webcal_entry.cal_date = " . date ( "Ymd", $dates[$x] );
  }
  $sql .=  ") AND webcal_entry.cal_time >= 0 " .
    "AND webcal_entry_user.cal_status IN ('A','W') AND ( ";
  if ( $single_user == "Y" ) {
     $participants[0] = $single_user_login;
  } else if ( strlen ( $participants[0] ) == 0 ) {
     // likely called from a form with 1 user
     $participants[0] = $login;
  }
  for ( $i = 0; $i < count ( $participants ); $i++ ) {
    if ( $i > 0 )
      $sql .= " OR ";
    $sql .= " webcal_entry_user.user_id = '" . $participants[$i] . "'";
  }
  $sql .= " )";
  // make sure we don't get something past the end date of the
  // event we are saving.
  //echo "SQL: $sql<br />\n";
  $conflicts = "";
    $dbh = new DB_Mysql();
    $stmt = $dbh->prepare($sql);
  $found = array();
  $count = 0;
  if ( $stmt->execute() ) {
    $time1 = sprintf ( "%d%02d00", $hour, $minute );
    $duration1 = sprintf ( "%d", $duration );
    while ( $row = $stmt->fetch_row ( ) ) {
      //Add to an array to see if it has been found already for the next part.
      $found[$count++] = $row[4];
      // see if either event overlaps one another
      if ( $row[4] != $id && ( empty ( $row[5] ) || $row[5] != $id ) ) {
        $time2 = $row[1];
        $duration2 = $row[2];
        $cntkey = $row[0] . "-" . $row[8];
        if ( empty ( $evtcnt[$cntkey] ) )
          $evtcnt[$cntkey] = 0;
        else
          $evtcnt[$cntkey]++;
        $over_limit = 0;
        if ( $limit_appts == "Y" && $limit_appts_number > 0
          && $evtcnt[$cntkey] >= $limit_appts_number ) {
          $over_limit = 1;
        }
        if ( $over_limit ||
          times_overlap ( $time1, $duration1, $time2, $duration2 ) ) {
          $conflicts .= "<li>";
          if ( $single_user != "Y" )
            $conflicts .= "$row[0]: ";
          if ( $row[6] == 'R' && $row[0] != $login )
            $conflicts .=  "(" . translate("Private") . ")";
          else {
            $conflicts .=  "<a href=\"view_entry.php?id=$row[4]";
            if ( $row[0] != $login )
              $conflicts .= "&amp;user=$row[0]";
            $conflicts .= "\">$row[3]</a>";
          }
          if ( $duration2 == ( 24 * 60 ) ) {
            $conflicts .= " (" . translate("All day event") . ")";
          } else {
            $conflicts .= " (" . display_time ( $time2 );
            if ( $duration2 > 0 )
              $conflicts .= "-" .
                display_time ( add_duration ( $time2, $duration2 ) );
            $conflicts .= ")";
          }
          $conflicts .= " on " . date_to_str( $row[8] );
          if ( $over_limit ) {
            $tmp = translate ( "exceeds limit of XXX events per day" );
            $tmp = str_replace ( "XXX", $limit_appts_number, $tmp );
            $conflicts .= " (" . $tmp . ")";
          }
          $conflicts .= "</li>\n";
        }
      }
    }
  } else {
    echo translate("Database error") . ": " . dbi_error (); exit;
  }
  
  //echo "<br />\nhello";
  for ($q=0;$q<count($participants);$q++) {
    $time1 = sprintf ( "%d%02d00", $hour, $minute );
    $duration1 = sprintf ( "%d", $duration );
    //This date filter is not necessary for functional reasons, but it eliminates some of the
    //events that couldn't possibly match.  This could be made much more complex to put more
    //of the searching work onto the database server, or it could be dropped all together to put
    //the searching work onto the client.
    $date_filter  = "AND (webcal_entry.cal_date <= " . date("Ymd",$dates[count($dates)-1]);
    $date_filter .= " AND (webcal_entry_repeats.cal_end IS NULL OR webcal_entry_repeats.cal_end >= " . date("Ymd",$dates[0]) . "))";
    //Read repeated events for the participants only once for a participant for
    //for performance reasons.
    $repeated_events=query_events($participants[$q],true,$date_filter);
    //for ($dd=0; $dd<count($repeated_events); $dd++) {
    //  echo $repeated_events[$dd]['cal_id'] . "<br />";
    //}
    for ($i=0; $i < count($dates); $i++) {
      $dateYmd = date ( "Ymd", $dates[$i] );
      $list = get_repeating_entries($participants[$q],$dateYmd);
      $thisyear = substr($dateYmd, 0, 4);
      $thismonth = substr($dateYmd, 4, 2);
      for ($j=0; $j < count($list);$j++) {
        //okay we've narrowed it down to a day, now I just gotta check the time...
        //I hope this is right...
        $row = $list[$j];
        if ( $row['cal_id'] != $id && ( empty ( $row['cal_ext_for_id'] ) || 
          $row['cal_ext_for_id'] != $id ) ) {
          $time2 = $row['cal_time'];
          $duration2 = $row['cal_duration'];
          if ( times_overlap ( $time1, $duration1, $time2, $duration2 ) ) {
            $conflicts .= "<li>";
            if ( $single_user != "Y" )
              $conflicts .= $row['cal_login'] . ": ";
            if ( $row['cal_access'] == 'R' && $row['cal_login'] != $login )
              $conflicts .=  "(" . translate("Private") . ")";
            else {
              $conflicts .=  "<a href=\"view_entry.php?id=" . $row['cal_id'];
              if ( ! empty ( $user ) && $user != $login )
                $conflicts .= "&amp;user=$user";
              $conflicts .= "\">" . $row['cal_name'] . "</a>";
            }
            $conflicts .= " (" . display_time ( $time2 );
            if ( $duration2 > 0 )
              $conflicts .= "-" .
                display_time ( add_duration ( $time2, $duration2 ) );
            $conflicts .= ")";
            $conflicts .= " on " . date("l, F j, Y", $dates[$i]);
            $conflicts .= "</li>\n";
          }
        }
      }
    }
  }
   
  return $conflicts;
}

/**
 * Converts a time format HHMMSS (like 130000 for 1PM) into number of minutes past midnight.
 *
 * @param string $time Input time in HHMMSS format
 *
 * @return int The number of minutes since midnight
 */
function time_to_minutes ( $time ) {
  $h = (int) ( $time / 10000 );
  $m = (int) ( $time / 100 ) % 100;
  $num = $h * 60 + $m;
  return $num;
}


/**
 * Looks for URLs in the given text, and makes them into links.
 *
 * @param string $text Input text
 *
 * @return string The text altered to have HTML links for any web links
 *                (http or https)
 */
function activate_urls ( $text ) {
  $str = eregi_replace ( "(http://[^[:space:]$]+)",
    "<a href=\"\\1\">\\1</a>", $text );
  $str = eregi_replace ( "(https://[^[:space:]$]+)",
    "<a href=\"\\1\">\\1</a>", $str );
  return $str;
}

/**
 * Displays a time in either 12 or 24 hour format.
 *
 * The global variable $this->TZ_OFFSET is used to adjust the time.  Note that this
 * is somewhat of a kludge for timezone support.  If an event is set for 11PM
 * server time and the user is 2 hours ahead, it will show up as 1AM, but the
 * date will not be adjusted to the next day.
 *
 * @param string $time          Input time in HHMMSS format
 * @param bool   $ignore_offset If true, then do not use the timezone offset
 *
 * @return string The time in the user's timezone and preferred format
 *
 * @global int The user's timezone offset from the server
 */
function display_time ( $time, $ignore_offset=0 ) {
  $hour = (int) ( $time / 10000 );
  if ( ! $ignore_offset )
    $hour += $this->TZ_OFFSET;
  $min = abs( ( $time / 100 ) % 100 );
  //Prevent goofy times like 8:00 9:30 9:00 10:30 10:00 
  if ( $time < 0 && $min > 0 ) $hour = $hour - 1;
  while ( $hour < 0 )
    $hour += 24;
  while ( $hour > 23 )
    $hour -= 24;
  if ($this->TIME_FORMAT == "12" ) {
    $ampm = ( $hour >= 12 ) ? translate("pm") : translate("am");
    $hour %= 12;
    if ( $hour == 0 )
      $hour = 12;
    $ret = sprintf ( "%d:%02d%s", $hour, $min, $ampm );
  } else {
    $ret = sprintf ( "%d:%02d", $hour, $min );
  }
  return $ret;
}


/**
 * Converts a hexadecimal digit to an integer.
 *
 * @param string $val Hexadecimal digit
 *
 * @return int Equivalent integer in base-10
 *
 * @ignore
 */
function hextoint ( $val ) {
  if ( empty ( $val ) )
    return 0;
  switch ( strtoupper ( $val ) ) {
    case "0": return 0;
    case "1": return 1;
    case "2": return 2;
    case "3": return 3;
    case "4": return 4;
    case "5": return 5;
    case "6": return 6;
    case "7": return 7;
    case "8": return 8;
    case "9": return 9;
    case "A": return 10;
    case "B": return 11;
    case "C": return 12;
    case "D": return 13;
    case "E": return 14;
    case "F": return 15;
  }
  return 0;
}
/**
 * Extracts a user's name from a session id.
 *
 * This prevents users from begin able to edit their cookies.txt file and set
 * the username in plain text.
 *
 * @param string $instr A hex-encoded string. "Hello" would be "678ea786a5".
 * 
 * @return string The decoded string
 *
 * @global array Array of offsets
 *
 * @see encode_string
 */
function decode_string ( $instr ) {
  global $offsets;
  //echo "<br />\nDECODE<br />\n";
  $orig = "";
  for ( $i = 0; $i < strlen ( $instr ); $i += 2 ) {
    //echo "<br />\n";
    $ch1 = substr ( $instr, $i, 1 );
    $ch2 = substr ( $instr, $i + 1, 1 );
    $val = hextoint ( $ch1 ) * 16 + hextoint ( $ch2 );
    //echo "decoding \"" . $ch1 . $ch2 . "\" = $val<br />\n";
    $j = ( $i / 2 ) % count ( $offsets );
    //echo "Using offsets $j = " . $offsets[$j] . "<br />\n";
    $newval = $val - $offsets[$j] + 256;
    $newval %= 256;
    //echo " neval \"$newval\"<br />\n";
    $dec_ch = chr ( $newval );
    //echo " which is \"$dec_ch\"<br />\n";
    $orig .= $dec_ch;
  }
  //echo "Decode string: '$orig' <br/>\n";
  return $orig;
}

/**
 * Takes an input string and encode it into a slightly encoded hexval that we
 * can use as a session cookie.
 *
 * @param string $instr Text to encode
 *
 * @return string The encoded text
 *
 * @global array Array of offsets
 *
 * @see decode_string
 */
function encode_string ( $instr ) {
  global $offsets;
  //echo "<br />\nENCODE<br />\n";
  $ret = "";
  for ( $i = 0; $i < strlen ( $instr ); $i++ ) {
    //echo "<br />\n";
    $ch1 = substr ( $instr, $i, 1 );
    $val = ord ( $ch1 );
    //echo "val = $val for \"$ch1\"<br />\n";
    $j = $i % count ( $offsets );
    //echo "Using offsets $j = $offsets[$j]<br />\n";
    $newval = $val + $offsets[$j];
    $newval %= 256;
    //echo "newval = $newval for \"$ch1\"<br />\n";
    $ret .= bin2hex ( chr ( $newval ) );
  }
  return $ret;
}

/**
 * An implementatin of array_splice() for PHP3.
 *
 * @param array $input       Array to be spliced into
 * @param int   $offset      Where to begin the splice
 * @param int   $length      How long the splice should be
 * @param array $replacement What to splice in
 *
 * @ignore
 */
function my_array_splice(&$input,$offset,$length,$replacement) {
  if ( floor(phpversion()) < 4 ) {
    // if offset is negative, then it starts at the end of array
    if ( $offset < 0 )
      $offset = count($input) + $offset;

    for ($i=0;$i<$offset;$i++) {
      $new_array[] = $input[$i];
    }

    // if we have a replacement, insert it
    for ($i=0;$i<count($replacement);$i++) {
      $new_array[] = $replacement[$i];
    }

    // now tack on the rest of the original array
    for ($i=$offset+$length;$i<count($input);$i++) {
      $new_array[] = $input[$i];
    }

    $input = $new_array;
  } else {
    array_splice($input,$offset,$length,$replacement);
  }
}



/**
 * Prints dropdown HTML for categories.
 *
 * @param string $form   The page to submit data to (without .php)
 * @param string $date   Date in YYYYMMDD format
 * @param int    $cat_id Category id that should be pre-selected
 */
function print_category_menu ( $form, $date = '', $cat_id = '' ) {
  global $categories, $category_owners, $user, $login;
  echo "<form action=\"{$form}.php\" method=\"get\" name=\"SelectCategory\" class=\"categories\">\n";
  if ( ! empty($date) ) echo "<input type=\"hidden\" name=\"date\" value=\"$date\" />\n";
  if ( ! empty ( $user ) && $user != $login )
    echo "<input type=\"hidden\" name=\"user\" value=\"$user\" />\n";
  echo translate ("Category") . ": <select name=\"cat_id\" onchange=\"document.SelectCategory.submit()\">\n";
  echo "<option value=\"\"";
  if ( $cat_id == '' ) echo " selected=\"selected\"";
  echo ">" . translate("All") . "</option>\n";
  $cat_owner =  ( ! empty ( $user ) && strlen ( $user ) ) ? $user : $login;
  if (  is_array ( $categories ) ) {
    foreach ( $categories as $K => $V ){
      if ( $cat_owner ||
        empty ( $category_owners[$K] ) ) {
        echo "<option value=\"$K\"";
        if ( $cat_id == $K ) echo " selected=\"selected\"";
        echo ">$V</option>\n";
      }
    }
  }
  echo "</select>\n";
  echo "</form>\n";
  echo "<span id=\"cat\">" . translate ("Category") . ": ";
  echo ( strlen ( $cat_id ) ? $categories[$cat_id] : translate ('All') ) . "</span>\n";
}

/**
 * Converts HTML entities in 8bit.
 *
 * <b>Note:</b> Only supported for PHP4 (not PHP3).
 *
 * @param string $html HTML text
 *
 * @return string The converted text
 */
function html_to_8bits ( $html ) {
  if ( floor(phpversion()) < 4 ) {
    return $html;
  } else {
    return strtr ( $html, array_flip (
      get_html_translation_table (HTML_ENTITIES) ) );
  }
}

// ***********************************************************************
// Functions for getting information about boss and their assistant.
// ***********************************************************************

/**
 * Gets a list of an assistant's boss from the webcal_asst table.
 *
 * @param string $assistant Login of assistant
 *
 * @return array Array of bosses, where each boss is an array with the following
 *               fields:
 * - <var>cal_login</var>
 * - <var>cal_fullname</var>
 */
function user_get_boss_list ( $assistant ) {
  global $bosstemp_fullname;

$dbh = new DB_Mysql();
$stmt = $dbh->prepare("SELECT cal_boss " .
    "FROM webcal_asst " .
    "WHERE cal_assistant = '$assistant'");
  $count = 0;
  $ret = array ();
  if ( $stmt->execute() ) {
    while ( $row = $stmt->fetch_row ( ) ) {
      $this->user_load_variables ( $row[0], "bosstemp_" );
      $ret[$count++] = array (
        "cal_login" => $row[0],
        "cal_fullname" => $bosstemp_fullname
      );
    }
  }
  return $ret;
}

/**
 * Is this user an assistant of this boss?
 *
 * @param string $assistant Login of potential assistant
 * @param string $boss      Login of potential boss
 * 
 * @return bool True or false
 */
function user_is_assistant ( $assistant, $boss ) {
  $ret = false;

  if ( empty ( $boss ) )
    return false;
$dbh = new DB_Mysql();
$stmt = $dbh->prepare("SELECT * FROM webcal_asst " . 
     "WHERE cal_assistant = '$assistant' AND cal_boss = '$boss'");

  if ( $stmt->execute() ) {
    if ( $stmt->fetch_row ( ) )
      $ret = true;
  }
  return $ret;
}


/**
 * Checks the boss user preferences to see if the boss wants to be notified via
 * email on changes to their calendar.
 *
 * @param string $assistant Assistant login
 * @param string $boss      Boss login
 *
 * @return bool True if the boss wants email notifications
 */
function boss_must_be_notified ( $assistant, $boss ) {
  if (user_is_assistant ( $assistant, $boss ) )
    return ( get_pref_setting ( $boss, "EMAIL_ASSISTANT_EVENTS" )=="Y" ? true : false );
  return true;
}

/**
 * Checks the boss user preferences to see if the boss must approve events
 * added to their calendar.
 *
 * @param string $assistant Assistant login
 * @param string $boss      Boss login
 *
 * @return bool True if the boss must approve new events
 */
function boss_must_approve_event ( $assistant, $boss ) {
  if (user_is_assistant ( $assistant, $boss ) )
    return ( get_pref_setting ( $boss, "APPROVE_ASSISTANT_EVENT" )=="Y" ? true : false );
  return true;
}

/**
 * Fakes an email for testing purposes.
 *
 * @param string $mailto Email address to send mail to
 * @param string $subj   Subject of email
 * @param string $text   Email body
 * @param string $hdrs   Other email headers
 *
 * @ignore
 */
function fake_mail ( $mailto, $subj, $text, $hdrs ) { 
  echo "To: $mailto <br />\n" .
    "Subject: $subj <br />\n" .
    nl2br ( $hdrs ) . "<br />\n" .
    nl2br ( $text );
}

/**
 * Prints all the entries in a time bar format for the specified user for the
 * specified date.
 *
 * If we are displaying data from someone other than the logged in user, then
 * check the access permission of the entry.
 *
 * @param string $date Date in YYYYMMDD format
 * @param string $user Username
 * @param bool   $ssi  Should we not include links to add new events?
 */
function print_date_entries_timebar ( $date, $user, $ssi ) {
  global $events, $is_admin,
    $public_access, $public_access_can_add;
  $cnt = 0;
  $get_unapproved = ( $GLOBALS["DISPLAY_UNAPPROVED"] == "Y" );
  // public access events always must be approved before being displayed
  if ( $GLOBALS["login"] == "__public__" )
    $get_unapproved = false;

  $year = substr ( $date, 0, 4 );
  $month = substr ( $date, 4, 2 );
  $day = substr ( $date, 6, 2 );
 
  $dateu = mktime ( 3, 0, 0, $month, $day, $year );

  $can_add = ( $readonly == "N" || $is_admin );
  if ( $public_access == "Y" && $public_access_can_add != "Y" &&
    $GLOBALS["login"] == "__public__" )
    $can_add = false;

  // get all the repeating events for this date and store in array $rep
  $rep = get_repeating_entries ( $user, $date ) ;
  $cur_rep = 0;

  // get all the non-repeating events for this date and store in $ev
  $ev = get_entries ( $user, $date, $get_unapproved );

  for ( $i = 0; $i < count ( $ev ); $i++ ) {
    // print out any repeating events that are before this one...
    while ( $cur_rep < count ( $rep ) &&
      $rep[$cur_rep]['cal_time'] < $ev[$i]['cal_time'] ) {
      if ( $get_unapproved || $rep[$cur_rep]['cal_status'] == 'A' ) {
        print_entry_timebar ( $rep[$cur_rep]['cal_id'],
          $date, $rep[$cur_rep]['cal_time'], $rep[$cur_rep]['cal_duration'],
          $rep[$cur_rep]['cal_name'], $rep[$cur_rep]['cal_description'],
          $rep[$cur_rep]['cal_status'], $rep[$cur_rep]['cal_priority'],
          $rep[$cur_rep]['cal_access'], $rep[$cur_rep]['cal_login'],
          $rep[$cur_rep]['cal_category'] );
        $cnt++;
      }
      $cur_rep++;
    }
    if ( $get_unapproved || $ev[$i]['cal_status'] == 'A' ) {
      print_entry_timebar ( $ev[$i]['cal_id'],
        $date, $ev[$i]['cal_time'], $ev[$i]['cal_duration'],
        $ev[$i]['cal_name'], $ev[$i]['cal_description'],
        $ev[$i]['cal_status'], $ev[$i]['cal_priority'],
        $ev[$i]['cal_access'], $ev[$i]['cal_login'],
        $ev[$i]['cal_category'] );
      $cnt++;
    }
  }
  // print out any remaining repeating events
  while ( $cur_rep < count ( $rep ) ) {
    if ( $get_unapproved || $rep[$cur_rep]['cal_status'] == 'A' ) {
      print_entry_timebar ( $rep[$cur_rep]['cal_id'],
        $date, $rep[$cur_rep]['cal_time'], $rep[$cur_rep]['cal_duration'],
        $rep[$cur_rep]['cal_name'], $rep[$cur_rep]['cal_description'],
        $rep[$cur_rep]['cal_status'], $rep[$cur_rep]['cal_priority'],
        $rep[$cur_rep]['cal_access'], $rep[$cur_rep]['cal_login'],
        $rep[$cur_rep]['cal_category'] );
      $cnt++;
    }
    $cur_rep++;
  }
  if ( $cnt == 0 )
    echo "&nbsp;"; // so the table cell has at least something
}

/**
 * Prints the HTML for an events with a timebar.
 *
 * @param int    $id             Event id
 * @param string $date           Date of event in YYYYMMDD format
 * @param string $time           Time of event in HHMM format
 * @param int    $duration       Duration of event in minutes
 * @param string $name           Brief description of event
 * @param string $description    Full description of event
 * @param string $status         Status of event ('A', 'W')
 * @param int    $pri            Priority of event
 * @param string $access         Access to event by others ('P', 'R')
 * @param string $event_owner    User who created event
 * @param int    $event_category Category id for event
 *
 * @staticvar int Used to ensure all event popups have a unique id
 */
function print_entry_timebar ( $id, $date, $time, $duration,
  $name, $description, $status,
  $pri, $access, $event_owner, $event_category=-1 ) {
  global $eventinfo, $login, $user, $PHP_SELF, $prefarray;
  static $key = 0;
  $insidespan = false;
  global $layers;

  // compute time offsets in % of total table width
  $day_start=$prefarray["WORK_DAY_START_HOUR"] * 60;
  if ( $day_start == 0 ) $day_start = 9*60;
  $day_end=$prefarray["WORK_DAY_END_HOUR"] * 60;
  if ( $day_end == 0 ) $day_end = 19*60;
  if ( $day_end <= $day_start ) $day_end = $day_start + 60; //avoid exceptions

  if ($time >= 0) {
  $bar_units= 100/(($day_end - $day_start)/60) ; // Percentage each hour occupies
  $ev_start = round((floor(($time/10000) - ($day_start/60)) + (($time/100)%100)/60) * $bar_units);
  }else{
    $ev_start= 0;
  }
  if ($ev_start < 0) $ev_start = 0;
  if ($duration > 0) {
    $ev_duration = round(100 * $duration / ($day_end - $day_start)) ;
    if ($ev_start + $ev_duration > 100 ) {
      $ev_duration = 100 - $ev_start;
    }
  } else {
    if ($time >= 0) {
      $ev_duration = 1;
    } else {
      $ev_duration=100-$ev_start;
    }
  }
  $ev_padding = 100 - $ev_start - $ev_duration;
  // choose where to position the text (pos=0->before,pos=1->on,pos=2->after)
  if ($ev_duration > 20)   { $pos = 1; }
   elseif ($ev_padding > 20)   { $pos = 2; }
   else        { $pos = 0; }
 
  echo "\n<!-- ENTRY BAR -->\n<table class=\"entrycont\" cellpadding=\"0\" cellspacing=\"0\">\n";
   echo "<tr>\n";
   echo ($ev_start > 0 ?  "<td style=\"text-align:right;  width:$ev_start%;\">" : "" );
   if ( $pos > 0 ) {
     echo ($ev_start > 0 ?  "&nbsp;</td>\n": "" ) ;
    echo "<td style=\"width:$ev_duration%;\">\n<table class=\"entrybar\">\n<tr>\n<td class=\"entry\">";
     if ( $pos > 1 ) {
       echo ($ev_padding > 0 ?  "&nbsp;</td>\n": "" ) . "</tr>\n</table></td>\n";
       echo ($ev_padding > 0 ?  "<td style=\"text-align:left; width:$ev_padding%;\">" : "");
    }
  };

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
  echo "<a class=\"$class\" href=\"view_entry.php?id=$id&amp;date=$date";
  if ( strlen ( $user ) > 0 )
    echo "&amp;user=" . $user;
  echo "\" onmouseover=\"window.status='" . 
    translate("View this entry") . "'; show(event, '$popupid'); return true;\" onmouseout=\"hide('$popupid'); return true;\">";

  if ( $login != $event_owner && strlen ( $event_owner ) ) {
    if ($layers) foreach ($layers as $layer) {
        if($layer['cal_layeruser'] == $event_owner) {
            $insidespan = true;
            echo("<span style=\"color:" . $layer['cal_color'] . ";\">");
        }
    }
  }

  echo "[$event_owner]&nbsp;";
  $timestr = "";
  if ( $duration == ( 24 * 60 ) ) {
    $timestr = translate("All day event");
  } else if ( $time >= 0 ) {
    $timestr = display_time ( $time );
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
      $timestr .= " - " . display_time ( $end_time );
    }
  }
  if ( $login != $user && $access == 'R' && strlen ( $user ) )
    echo "(" . translate("Private") . ")";
  else
  if ( $login != $event_owner && $access == 'R' && strlen ( $event_owner ) )
    echo "(" . translate("Private") . ")";
  else
  if ( $login != $event_owner && strlen ( $event_owner ) )
  {
    echo htmlspecialchars ( $name );
    if ( $insidespan ) { echo ("</span>"); } //end color span
  }
  else
    echo htmlspecialchars ( $name );
  echo "</a>";
  if ( $pri == 3 ) echo "</strong>"; //end font-weight span
  echo "</td>\n";
  if ( $pos < 2 ) {
    if ( $pos < 1 ) {
      echo "<td style=\"width:$ev_duration%;\"><table  class=\"entrybar\">\n<tr>\n<td class=\"entry\">&nbsp;</td>\n";
    }
    echo "</tr>\n</table></td>\n";
    echo ($ev_padding > 0 ? "<td style=\"text-align:left; width:$ev_padding%;\">&nbsp;</td>\n" : "" );
  }
  echo "</tr>\n</table>\n";
  if ( $login != $user && $access == 'R' && strlen ( $user ) )
    $eventinfo .= build_event_popup ( $popupid, $event_owner,
      translate("This event is confidential"), "" );
  else
  if ( $login != $event_owner && $access == 'R' && strlen ( $event_owner ) )
    $eventinfo .= build_event_popup ( $popupid, $event_owner,
      translate("This event is confidential"), "" );
  else
    $eventinfo .= build_event_popup ( $popupid, $event_owner,
      $description, $timestr, site_extras_for_popup ( $id ) );
}

/**
 * Prints the header for the timebar.
 *
 * @param int $start_hour Start hour
 * @param int $end_hour   End hour
 */
function print_header_timebar($start_hour, $end_hour) {
//      sh+1   ...   eh-1
// +------+----....----+------+
// |      |            |      |
// print hours
    if ( ($end_hour - $start_hour) == 0 )
    {
        $offset = 0;
    }
    else
    {
        $offset = round(100/($end_hour - $start_hour));
    }
    echo "\n<!-- TIMEBAR -->\n<table class=\"timebar\">\n<tr><td style=\"width:$offset%;\">&nbsp;</td>\n";
    for ($i = $start_hour+1; $i < $end_hour; $i++) 
    {
        //$prev_offset = $offset;
        //$offset = round(100/($end_hour - $start_hour)*($i - $start_hour + .5));
        $offset = round(100/($end_hour - $start_hour));
        $width = $offset;
        echo "<td style=\"width:$width%;text-align:left;\">$i</td>\n";
    }
    //   $width = 100 - $offset;
    //echo "<td style=\"width:$width%;\">&nbsp;</td>\n";
    echo "</tr>\n</table>\n<!-- /TIMEBAR -->\n";
    // print yardstick
    echo "\n<!-- YARDSTICK -->\n<table class=\"yardstick\">\n<tr>\n";
    $width = round(100/($end_hour - $start_hour));
    for ($i = $start_hour; $i < $end_hour; $i++) 
    {
        echo "<td style=\"width:$width%;\">&nbsp;</td>\n";
    }
    echo "</tr>\n</table>\n<!-- /YARDSTICK -->\n";
}

/**
 * Loads nonuser variables (login, firstname, etc.).
 *
 * The following variables will be set:
 * - <var>login</var>
 * - <var>firstname</var>
 * - <var>lastname</var>
 * - <var>fullname</var>
 * - <var>admin</var>
 * - <var>email</var>
 *
 * @param string $login  Login name of nonuser calendar
 * @param string $prefix Prefix to use for variables that will be set.
 *                       For example, if prefix is "temp", then the login will
 *                       be stored in the <var>$templogin</var> global variable.
 */
function nonuser_load_variables ( $login, $prefix ) {
  global $error,$nuloadtmp_email;
  $ret =  false;
$dbh = new DB_Mysql();
$stmt = $dbh->prepare("SELECT user_id, cal_lastname, cal_firstname, " .
    "cal_admin FROM webcal_nonuser_cals WHERE user_id = '$login'");

  if ($stmt->execute() ) {
    while ( $row = $stmt->fetch_row (  ) ) {
      if ( strlen ( $row[1] ) || strlen ( $row[2] ) )
        $fullname = "$row[2] $row[1]";
      else
        $fullname = $row[0];

        // We need the email address for the admin
        $this->user_load_variables ( $row[3], 'nuloadtmp_' );

        $GLOBALS[$prefix . "login"] = $row[0];
        $GLOBALS[$prefix . "firstname"] = $row[2];
        $GLOBALS[$prefix . "lastname"] = $row[1];
        $GLOBALS[$prefix . "fullname"] = $fullname;
        $GLOBALS[$prefix . "admin"] = $row[3];
        $GLOBALS[$prefix . "email"] = $nuloadtmp_email;
        $ret = true;
    }
  }
  return $ret;
}



/**
 * Replaces unsafe characters with HTML encoded equivalents.
 *
 * @param string $value Input text
 *
 * @return string The cleaned text
 */
function clean_html($value){
  $value = htmlspecialchars($value, ENT_QUOTES);
  $value = strtr($value, array(
    '('   => '&#40;',
    ')'   => '&#41;'
  ));
  return $value;
}

/**
 * Removes non-word characters from the specified text.
 *
 * @param string $data Input text
 *
 * @return string The converted text
 */
function clean_word($data) { 
  return preg_replace("/\W/", '', $data);
}

/**
 * Removes non-digits from the specified text.
 *
 * @param string $data Input text
 *
 * @return string The converted text
 */
function clean_int($data) { 
  return preg_replace("/\D/", '', $data);
}

/**
 * Removes whitespace from the specified text.
 *
 * @param string $data Input text
 * 
 * @return string The converted text
 */
function clean_whitespace($data) { 
  return preg_replace("/\s/", '', $data);
}

/**
 * Converts language names to their abbreviation.
 *
 * @param string $name Name of the language (such as "French")
 *
 * @return string The abbreviation ("fr" for "French")
 */
function languageToAbbrev ( $name ) {
  global $browser_languages;
  foreach ( $browser_languages as $abbrev => $langname ) {
    if ( $langname == $name )
      return $abbrev;
  }
  return false;
}

// Do a sanity check.  Make sure we can access webcal_config table.
// We call this right after the first call to dbi_connect() (from
// either connect.php or here in validate.php).
function doDbSanityCheck () {
  global $db_login, $db_host, $db_database;
$dbh = new DB_Mysql();
$stmt = $dbh->prepare("SELECT COUNT(cal_value) FROM webcal_config");
  if ( $stmt->execute() ) {
    if ( $row = $stmt->fetch_row ( ) ) {
      // Found database.  All is peachy.
    } else {
      // Error accessing table.
      // User has wrong db name or has not created tables.
      // Note: cannot translate this since we have not included
      // translate.php yet.
      die_miserable_death (
        "Error finding WebCalendar tables in database '$db_database' " .
        "using db login '$db_login' on db server '$db_host'.<br/><br/>\n" .
        "Have you created the database tables as specified in the " .
        "<a href=\"docs/WebCalendar-SysAdmin.html\" target=\"other\">WebCalendar " .
        "System Administrator's Guide</a>?" );
    }
  } else {
    // Error accessing table.
    // User has wrong db name or has not created tables.
    // Note: cannot translate this since we have not included translate.php yet.
    die_miserable_death (
      "Error finding WebCalendar tables in database '$db_database' " .
      "using db login '$db_login' on db server '$db_host'.<br/><br/>\n" .
      "Have you created the database tables as specified in the " .
      "<a href=\"docs/WebCalendar-SysAdmin.html\" target=\"other\">WebCalendar " .
      "System Administrator's Guide</a>?" );
  }
}



/**
 * Prints the common trailer.
 *
 * @param bool $include_nav_links Should the standard navigation links be
 *                               included in the trailer?
 * @param bool $closeDb           Close the database connection when finished?
 * @param bool $disableCustom     Disable the custom trailer the administrator
 *                                has setup?  (This is useful for small popup
 *                                windows and pages being used in an iframe.)
 */
function print_trailer ( $include_nav_links=true, $closeDb=true,
  $disableCustom=false )
{
 global $USER;
  global $CUSTOM_TRAILER, $c, $STARTVIEW;
  global $login, $user, $cat_id, $categories_enabled, $thisyear,
    $thismonth, $thisday, $DATE_FORMAT_MY, $DATE_FORMAT_MD,
    $is_admin, $public_access, $public_access_can_add,
    $single_user, $use_http_auth, $login_return_path, $require_approvals,
    $is_nonuser_admin, $public_access_others, $allow_view_other,
    $views, $reports_enabled, $LAYER_STATUS, $nonuser_enabled,
    $groups_enabled, $fullname, $has_boss;
  
  if ( $include_nav_links ) {
    include_once "includes/trailer.php";
  }

  // Add custom trailer if enabled
  if ( $CUSTOM_TRAILER == 'Y' && ! $disableCustom && isset ( $c ) ) {
    $res = dbi_query (
      "SELECT cal_template_text FROM webcal_report_template " .
      "WHERE cal_template_type = 'T' and cal_report_id = 0" );
    if ( $res ) {
      if ( $row = dbi_fetch_row ( $res ) ) {
        echo $row[0];
      }
      dbi_free_result ( $res );
    }
  }

  if ( $closeDb ) {
    if ( isset ( $c ) )
      dbi_close ( $c );
    unset ( $c );
  }
}


// Load info about a user (first name, last name, admin) and set
// globally.
// params:
//   $user - user login
//   $prefix - variable prefix to use
function user_load_variables ( $login, $prefix ) {
  
    
    $sql = "SELECT user_firstname, user_lastname, user_email, md5_pw " .
         "FROM user_main WHERE user_id=  :1:";
    $dbh = new DB_Mysql();
    $stmt = $dbh->prepare($sql);
    if ( $stmt->execute($login) ) 
    {
        if ( $row = $stmt->fetch_row (  ) )
        {
            $GLOBALS[$prefix . "login"] = $login;
            $GLOBALS[$prefix . "firstname"] = $row[0];
            $GLOBALS[$prefix . "lastname"] = $row[1];
            $GLOBALS[$prefix . "is_admin"] = "Y";
            $GLOBALS[$prefix . "email"] = $row[2];
            if ( strlen ( $row[0] ) && strlen ( $row[1] ) )
            {
              $GLOBALS[$prefix . "fullname"] = "$row[0] $row[1]";
            }
            else
            {
              $GLOBALS[$prefix . "fullname"] = $login;
            }
            $GLOBALS[$prefix . "password"] = $row[3];
        }
    }
    else {
        $error = translate ("Database error") . ": " . dbi_error ();
        return false;
    }
    return true;
}

// Get a list of users and return info in an array.
function user_get_users () {
  $ret = array ();
  $rows = Rbac_User::getAllAllowedTo("access_module", "calendar_module");
  $count = 0;
  if ( is_array($rows) ) {
    foreach ( $rows as $row ) {
      if ( strlen ( $row['user_lastname'] ) && strlen ( $row['user_firstname'] ) )
        $fullname = "$row[user_firstname] $row[user_lastname]";
      else
        $fullname = $row['username'];
      $ret[$count++] = array (
        "cal_login" => $row['username'],
        "cal_lastname" => $row['user_lastname'],
        "cal_firstname" => $row['user_firstname'],
        "cal_is_admin" => Rbac_User::IsAllowedTo($row['user_id'], "admin_module", "calendar_module"),
        "cal_email" => empty ( $row['user_email'] ) ? "" : $row['user_email'],
        "cal_password" => $row['md5_pw'],
        "cal_fullname" => $fullname,
        "user_id" => $row['user_id']
      );
    }
  }
  return $ret;
}
/**
 * Return the time in HHMMSS format of input time + duration
 *
 *
 * <b>Note:</b> The gd library module needs to be available to use gradient
 * images.  If it is not available, a single background color will be used
 * instead.
 *
 * @param string $time   format "235900"
 * @param int $duration  number of minutes
 *
 * @return string The time in HHMMSS format
 */
function add_duration ( $time, $duration ) {
  $hour = (int) ( $time / 10000 );
  $min = ( $time / 100 ) % 100;
  $minutes = $hour * 60 + $min + $duration;
  $h = $minutes / 60;
  $m = $minutes % 60;
  $ret = sprintf ( "%d%02d00", $h, $m );
  //echo "add_duration ( $time, $duration ) = $ret <br />\n";
  return $ret;
}

/**
 * Draws a daily outlook style availability grid showing events that are
 * approved and awaiting approval.
 *
 * @param string $date         Date to show the grid for
 * @param array  $participants Which users should be included in the grid
 * @param string $popup        Not used
 */
function daily_matrix ( $date, $participants, $popup = '' ) {
  global $CELLBG, $TODAYCELLBG, $THFG, $THBG, $TABLEBG;
  global $user_fullname, $repeated_events, $events;
  global $ignore_offset;

  $increment = 15;
  $interval = 4;
  $participant_pct = '20%'; //use percentage

  $first_hour = $this->WORK_DAY_START_HOUR;
  $last_hour = $WORK_DAY_END_HOUR;
  $hours = $last_hour - $first_hour;
  $cols = (($hours * $interval) + 1);
  $total_pct = '80%';
  $cell_pct =  80 /($hours * $interval);
  $master = array();

  // Build a master array containing all events for $participants
  for ( $i = 0; $i < count ( $participants ); $i++ ) {

    /* Pre-Load the repeated events for quckier access */
    $repeated_events = read_repeated_events ( $participants[$i], "", $date );
    /* Pre-load the non-repeating events for quicker access */
    $events = read_events ( $participants[$i], $date, $date );

    // get all the repeating events for this date and store in array $rep
    $rep = get_repeating_entries ( $participants[$i], $date );
    // get all the non-repeating events for this date and store in $ev
    $ev = get_entries ( $participants[$i], $date );

    // combine into a single array for easy processing
    $ALL = array_merge ( $rep, $ev );

    foreach ( $ALL as $E ) {
      if ($E['cal_time'] == 0) {
        $E['cal_time'] = $first_hour."0000";
        $E['cal_duration'] = 60 * ( $last_hour - $first_hour );
      } else {
        $E['cal_time'] = sprintf ( "%06d", $E['cal_time']);
      }

      $hour = substr($E['cal_time'], 0, 2 );
      $mins = substr($E['cal_time'], 2, 2 );
       
      // Timezone Offset
      if ( ! $ignore_offset ) $hour += $this->TZ_OFFSET;
      while ( $hour < 0 ) $hour += 24;
      while ( $hour > 23 ) $hour -= 24;

      // Make sure hour is 2 digits
      $hour = sprintf ( "%02d",$hour);

      // convert cal_time to slot
      if ($mins < 15) {
        $slot = $hour.'';
      } elseif ($mins >= 15 && $mins < 30) {
        $slot = $hour.'.25';
      } elseif ($mins >= 30 && $mins < 45) {
        $slot = $hour.'.5';
      } elseif ($mins >= 45) {
        $slot = $hour.'.75';
      }

      // convert cal_duration to bars
      $bars = $E['cal_duration'] / $increment;

      // never replace 'A' with 'W'
      for ($q = 0; $bars > $q; $q++) {
        $slot = sprintf ("%02.2f",$slot);
        if (strlen($slot) == 4) $slot = '0'.$slot; // add leading zeros
        $slot = $slot.''; // convert to a string
        if ( empty ( $master['_all_'][$slot] ) ||
          $master['_all_'][$slot]['stat'] != 'A') {
          $master['_all_'][$slot]['stat'] = $E['cal_status'];
        }
        if ( empty ( $master[$participants[$i]][$slot] ) ||
          $master[$participants[$i]][$slot]['stat'] != 'A' ) {
          $master[$participants[$i]][$slot]['stat'] = $E['cal_status'];
          $master[$participants[$i]][$slot]['ID'] = $E['cal_id'];
        }
        $slot = $slot + '0.25';
      }

    }
  }
?>
  <br />
  <table  align="center" class="matrixd" style="width:<?php echo $total_pct;?>;" cellspacing="0" cellpadding="0">
  <tr><td class="matrix" colspan="<?php echo $cols;?>"></td></tr>
  <tr><th style="width:<?php echo $participant_pct;?>;">
    <?php etranslate("Participants");?></th>
<?php
  $str = '';
  $MouseOut = "onmouseout=\"window.status=''; this.style.backgroundColor='".$THBG."';\"";
  $CC = 1;
  for($i=$first_hour;$i<$last_hour;$i++) {
    $hour = $i;
    if ( $GLOBALS["TIME_FORMAT"] == "12" ) {
      $hour %= 12;
      if ( $hour == 0 ) $hour = 12;
    }

     for($j=0;$j<$interval;$j++) {
        $str .= ' <td  id="C'.$CC.'" class="dailymatrix" ';
        $MouseDown = 'onmousedown="schedule_event('.$i.','.sprintf ("%02d",($increment * $j)).');"';
        switch($j) {
          case 1:
                  if($interval == 4) { $k = ($hour<=9?'0':substr($hour,0,1)); }
    $str .= 'style="width:'.$cell_pct.'%; text-align:right;"  '.$MouseDown." onmouseover=\"window.status='Schedule a ".$hour.':'.($increment * $j<=9?'0':'').($increment * $j)." appointment.'; this.style.backgroundColor='#CCFFCC'; return true;\" ".$MouseOut." title=\"Schedule an appointment for ".$hour.':'.($increment * $j<=9?'0':'').($increment * $j).".\">";
                  $str .= $k."</td>\n";
                  break;
          case 2:
                  if($interval == 4) { $k = ($hour<=9?substr($hour,0,1):substr($hour,1,2)); }
    $str .= 'style="width:'.$cell_pct.'%; text-align:left;" '.$MouseDown." onmouseover=\"window.status='Schedule a ".$hour.':'.($increment * $j)." appointment.'; this.style.backgroundColor='#CCFFCC'; return true;\" ".$MouseOut." title=\"Schedule an appointment for ".$hour.':'.($increment * $j<=9?'0':'').($increment * $j).".\">";
                  $str .= $k."</td>\n";
                  break;
          default:
    $str .= 'style="width:'.$cell_pct.'%;" '.$MouseDown." onmouseover=\"window.status='Schedule a ".$hour.':'.($increment * $j<=9?'0':'').($increment * $j)." appointment.'; this.style.backgroundColor='#CCFFCC'; return true;\" ".$MouseOut." title=\"Schedule an appointment for ".$hour.':'.($increment * $j<=9?'0':'').($increment * $j).".\">";
                  $str .= "&nbsp;&nbsp;</td>\n";
                  break;
        }
       $CC++;
     }
  }
  echo $str .
    "</tr>\n<tr><td class=\"matrix\" colspan=\"$cols\"></td></tr>\n";

  // Add user _all_ to beginning of $participants array
  array_unshift($participants, '_all_');

  // Javascript for cells
  $MouseOver = "onmouseover=\"this.style.backgroundColor='#CCFFCC';\"";
  $MouseOut = "onmouseout=\"this.style.backgroundColor='".$CELLBG."';\"";

  // Display each participant
  for ( $i = 0; $i < count ( $participants ); $i++ ) {
    if ($participants[$i] != '_all_') {
      // Load full name of user
      $this->user_load_variables ( $participants[$i], "user_" );
  
      // exchange space for &nbsp; to keep from breaking
      $user_nospace = preg_replace ( '/\s/', '&nbsp;', $user_fullname );
    } else {
      $user_nospace = translate("All Attendees");
      $user_nospace = preg_replace ( '/\s/', '&nbsp;', $user_nospace );
    }

    echo "<tr>\n<th class=\"row\" style=\"width:{$participant_pct};\">".$user_nospace."</th>\n";
    $col = 1;
    $viewMsg = translate ( "View this entry" );

    // check each timebar
    for ( $j = $first_hour; $j < $last_hour; $j++ ) {
       for ( $k = 0; $k < $interval; $k++ ) {
         $border = ($k == '0') ? ' border-left: 1px solid #000000;' : "";
         $MouseDown = 'onmousedown="schedule_event('.$j.','.sprintf ("%02d",($increment * $k)).');"';
        $RC = $CELLBG;
         //$space = '';
         $space = "&nbsp;";

         $r = sprintf ("%02d",$j) . '.' . sprintf ("%02d", (25 * $k)).'';
         if ( empty ( $master[$participants[$i]][$r] ) ) {
           // ignore this..
         } else if ( empty ( $master[$participants[$i]][$r]['ID'] ) ) {
           // This is the first line for 'all' users.  No event here.
           $space = "<span class=\"matrix\"><img src=\"image/pix.gif\" alt=\"\" style=\"height: 8px\" /></span>";
         } else if ($master[$participants[$i]][$r]['stat'] == "A") {
           $space = "<a class=\"matrix\" href=\"view_entry.php?id={$master[$participants[$i]][$r]['ID']}\"><img src=\"image/pix.gif\" title=\"$viewMsg\" alt=\"$viewMsg\" /></a>";
         } else if ($master[$participants[$i]][$r]['stat'] == "W") {
           $space = "<a class=\"matrix\" href=\"view_entry.php?id={$master[$participants[$i]][$r]['ID']}\"><img src=\"image/pixb.gif\" title=\"$viewMsg\" alt=\"$viewMsg\" /></a>";
         }

         echo "<td class=\"matrixappts\" style=\"width:{$cell_pct}%;$border\" ";
         if ($space == "&nbsp;") echo "$MouseDown $MouseOver $MouseOut";
         echo ">$space</td>\n";
         $col++;
      }
    }
    
    echo "</tr><tr>\n<td class=\"matrix\" colspan=\"$cols\">" .
      "<img src=\"image/pix.gif\" alt=\"-\" /></td></tr>\n";
  } // End foreach participant
  
  echo "</table><br />\n";
  $busy = translate ("Busy");
  $tentative = translate ("Tentative");
  echo "<table align=\"center\"><tr><td class=\"matrixlegend\" >\n";
  echo "<img src=\"image/pix.gif\" title=\"$busy\" alt=\"$busy\" /> $busy &nbsp; &nbsp; &nbsp;\n";
  echo "<img src=\"image/pixb.gif\" title=\"$tentative\" alt=\"$tentative\" /> $tentative\n";
  echo "</td></tr></table>\n";
}

}
?>