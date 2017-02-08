<?php

$MODULE_NAME = "calendar";
require_once("../../../framework/classes/Calendar_Config.class.php");
require_once("../../../framework/classes/Calendar_Event.class.php");
require_once("../../../framework/classes/Calendar_User.class.php");
require_once("../../../framework/classes/Calendar.class.php");
//require_once("../../../framework/classes/Framework_helper.class.php");

/*
$calendar = new Calendar();
$helper = new Framework_helper();
$calendar_settings = new Calendar_settings();
$calendar_event = new Calendar_event();


$calendar->setLogin($USER->GetUserID());

// Get script name
$helper->setSelf($_SERVER['PHP_SELF']);
preg_match ( "/\/(\w+\.php)/", $helper->getSelf(), $match);
$helper->setScript($match[1]);

// Several files need a no-cache header and some of the same code
$special = array('month.php', 'day.php', 'week.php', 'week_details.php', 'year.php');
$DMW = in_array($helper->getScript(), $special);


//security check
if(Rbac_User::IsAllowedTo($USER->GetUserID(), "configure_module", "calendar_module"))
{
    $calendar->setIsAdmin(true);
}
else
{
    $calendar->setIsAdmin(false);
}
if(Rbac_User::IsAllowedTo($USER->GetUserID(), "access_module", "calendar_module"))
{
    echo "Permission access Calendar Module";
    exit;
}


//$helper->validate_redirect = false;
//$helper->session_not_found = false;

//we dont want to be able to access these, if user is not admin

if ( empty ( $helper->is_admin ) || ! $helper->is_admin ) {
  if ( strstr ( $helper->getSelf(), "admin.php" ) ||
    strstr ( $helper->getSelf(), "admin_handler.php" ) ||
    strstr ( $helper->getSelf(), "groups.php" ) ||
    strstr ( $helper->getSelf(), "group_edit.php" ) ||
    strstr ( $helper->getSelf(), "group_edit_handler.php" ) ||
    strstr ( $helper->getSelf(), "edit_template.php" ) ||
    strstr ( $helper->getSelf(), "activity_log.php" ) ) {
    $helper->not_auth = true;
  }
}

// error-check some commonly used form variable names
$helper->id = $helper->getValue ( "id", "[0-9]+", true);
$calendar->setDate($helper->getValue ( "date", "[0-9]+" ) );
$calendar->setYear($helper->getValue ( "year", "[0-9]+" ) );
$calendar->setMonth($helper->getValue ( "month", "[0-9]+" ) );
$calendar->setHour($helper->getValue ( "hour", "[0-9]+" ) );
$calendar->setMinute($helper->getValue ( "minute", "[0-9]+" ) );
$helper->setCatId($helper->getValue ( "cat_id", "[0-9]+" ) );
$helper->setFriendly($helper->getValue ( "friendly", "[01]" ) );
$calendar->setUser($helper->getValue ( "user", "[A-Za-z0-9_\.=@,\-]*",  true) );

if($calendar->getUser() == '')
{
    $calendar->setUser($calendar->getLogin() );
}
$calendar->checkBossAssistant();

$calendar_settings->load_global_settings ();
$calendar_settings->load_user_preferences ($calendar->getLogin(), $calendar->getUser());

$calendar->WEEK_START = $calendar_settings->getWeekStart();
$calendar->DATE_FORMAT = $calendar_settings->DATE_FORMAT;


require_once 'includes/translate.php';

// Load if $helper->getScript() is in $special array:
if ($DMW) {
  
    
    // Tell the browser not to cache
    $helper->send_no_cache_header();
    //is this person is not allowed to view other calendars, unset the user var for our calendar
    if ($calendar->getAllowViewOther() != 'Y' && ! $calendar->getIsAdmin() )
    {
        $calendar->setUser("");
    }
    $calendar->can_add = Rbac_User::IsAllowedTo($USER->GetUserID(), "create_event", "calendar_module");
    if ( $calendar->getPublicAccess() == "Y" && $calendar->getLogin() == "__public__" ) {
        if ( $calendar->public_access_can_add != "Y" )
        {
            $calendar->can_add = false;
        }
        if ( $calendar->public_access_others != "Y" )
        {
            $calendar->setUser("");; // security precaution
            $helper->user = ""; // security precaution
        }
    }

    if ($calendar_settings->groups_enabled == "Y" && $helper->user_sees_only_his_groups == "Y" &&
        ! $helper->is_admin ) 
    {
        $helper->valid_user = false;
        $helper->userlist = $helper->get_my_users();
        if ($helper->nonuser_enabled == "Y" ) 
        {
            $helper->nonusers = get_nonuser_cals ();
            $helper->userlist =  array_merge($helper->nonusers, $helper->userlist);
        }
        for ( $i = 0; $i < count ( $userlist ); $i++ ) 
        {
            if ($helper->user == $userlist[$i]['cal_login'] )
            {
                $helper->valid_user = true;
            }
        } 
        if ($helper->valid_user == false) 
        { 
            $helper->user = ""; // security precaution
        }
    }

    $whos_cal = $calendar->getUser();
    if ( ! empty ( $whos_cal ) ) 
    {
        $u_url = "user=".$calendar->getUser()."&amp;";
        $helper->user_load_variables ($calendar->getUser(), "user_", $calendar->getLogin() );
    } else {
        $u_url = "";
        $user_fullname = $USER->GetUserFullName();
    }
    unset($whos_cal);
    
    $calendar->setToday($calendar->getDate() );

    if ($helper->getCategoriesEnabled() ) 
    {
        if ( ! empty ( $cat_id ) ) 
        {
            $cat_id = $cat_id;
        }
        elseif ( ! empty ( $CATEGORY_VIEW ) ) 
        {
          $cat_id = $CATEGORY_VIEW;
        }
        else 
        {
          $cat_id = '';
        }
    }
    else
    {
        $cat_id = '';
    }
    if ( empty ( $cat_id ) )
    {
        $helper->setCatURL("");
    }
    else
    {
        $helper->setCatURL("&amp;cat_id=$cat_id");
    }
}


*/
?>
