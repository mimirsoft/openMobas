<?php
/*
 * This file is part of openMobas
    
    Copyright (C) 2011, Kevin Milhoan

    openMobas is free software: you can redistribute it and/or modify
    it under the terms of the GNU Affero General Public License as
    published by the Free Software Foundation, either version 3 of the
    License, or (at your option) any later version.

    openMobas is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU Affero General Public License for more details.

    You should have received a copy of the GNU Affero General Public License
    along with WebPropMan.  If not, see <http://www.gnu.org/licenses/>.

   Contact MimirSoft at mimirsoft@gmail.com or www.mimirsoft.com

 * 
 * This is the test file that goes along with our basic android login system.
 * It returns a JSON object representing the login.  If the user trys to login with the correct info, he gets a response
 * with the username and userID. And the loggedIn variable is set to true.
 *
 * If not, he gets nothing.And the loggedIn variable is set to false
 *
 * We are not using the session variable to store any information about the state of the request.
 *
 * We only use it to maintain the users logged in state for future requests.  This s
 *
 */


/**************************************************
 * 
 * This script takes a GET with a "login" and "password" variable.
 * If they are correct, it returns a USER JSON object
 * 
 * "user": {
 * 
 * "username":
 * "userID" :
 * "loggedIn" :
 * "sessionID" :
 * }
 *  
 *  *************************************************/


//error_reporting(E_ALL);
//ini_set('display_errors', '1');
//this is our half assed way of enforcing HTTPS

if($_SERVER['SERVER_PORT']!=443)
{
    exit;
}
    
require_once("../../../openMobas/om_globalconstants.php");
require_once("../../../openMobas/classes/Database_Mysql.class.php");
require_once("../../../openMobas/classes/Framework.class.php");
require_once("../../../openMobas/classes/User_Session.class.php");
require_once("../../../openMobas/classes/User.class.php");
require_once("../../../openMobas/classes/Rbac_User.class.php");

$dbh = new DB_Mysql($DB_SETTINGS);
$objSession = new User_Session($dbh);
$objSession->Impress();
$USER = $objSession->GetUserObject();

if($USER->IsLoggedIn() != true)//if USER is not logged in....
{
    
    if(isset($_GET['login']) && isset($_GET['password']))
    {
        $USER->Login($_GET['login'], $_GET['password']);//try to log in.
    }
    if ($USER->IsLoggedIn() != true)//if that fails, boot him out.
    {
        //$currentURL = $_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING'];
        //$logged_out_message = $objSession->GetSessionDeath();
        //header ("Location:".$kickto."?currentURL=$currentURL");
        //exit;
    }
}
$response['username'] = $USER->GetUserName();
$response['userID'] = $USER->GetUserID();
$response['loggedIn'] = $USER->IsLoggedIn();
$response['sessionID'] = $objSession->GetSessionIdentifier();

print json_encode($response);

?>