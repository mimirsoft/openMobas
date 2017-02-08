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


/*************************************************
 * 
 * This script takes a POST with a PHPSESSION value set, to confirm the session is active.
 * If it is, it returns as JSON OBJECT containing a list of serves that we served for along with our normal five messaging values
 * (message, username, userID,loggedIn,sessionID)
 * If the user is not logged in when the session is check, the userID is set to null, and loggedIn is set to false
 *
 * "ProcessServeResponse": {
 *
 message:
 username:
 userID:
 loggedIn:
 sessionID:

 resuls: { array of serves that the user searched for }
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

require_once("../../../openMobas/globalconstants.php");
require_once("../../../openMobas/classes/Database_Mysql.class.php");
require_once("../../../openMobas/classes/Framework.class.php");
require_once("../../../openMobas/classes/User_Session.class.php");
require_once("../../../openMobas/classes/User.class.php");
require_once("../../../openMobas/classes/Rbac_User.class.php");
require_once("../../../openMobas/classes/Process_Serve.class.php");

$dbh = new DB_Mysql($DB_SETTINGS);
$objSession = new User_Session($dbh);
$objSession->Impress();
$USER = $objSession->GetUserObject();

$rbac_user = new Rbac_User($dbh, $USER);

$FRAMEWORK = new Framework($dbh, $USER, $rbac_user);
$FRAMEWORK->setModules($INSTALLED_MODULES);


//this must be checked first, because if it is not logged in, there is no way to check permissions
if($USER->IsLoggedIn() != true)//if USER is not logged in....
{
    $response['message'] = "user not logged in";
    $response['username'] = $USER->GetUserName();
    $response['userID'] = null;
    $response['loggedIn'] = false;
    $response['sessionID'] = $objSession->GetSessionIdentifier();
    $response['results'] = [];

    print json_encode($response);
    exit;
}

if(!$rbac_user->IsAllowedTo($USER->GetUserID(), "access_module", "serves_module"))
{
    
    $response['message'] = "permission denied to access module serves_module";
    $response['username'] = $USER->GetUserName();
    $response['userID'] = $USER->GetUserID();
    $response['loggedIn'] = $USER->IsLoggedIn();
    $response['sessionID'] = $objSession->GetSessionIdentifier();
    $response['results'] = [];
    print json_encode($response);
    exit;
}
$INCLUDE_COMPLETED = 0;

if(isset($_GET['INCLUDE_COMPLETED']) )
{
    $INCLUDE_COMPLETED = $_GET['INCLUDE_COMPLETED']; // see if we include the complete ones in our list.
}

$serve = new Process_Serve($dbh, $USER, $rbac_user);
$SORTBY = 'default';

$response['message'] = "";
$response['username'] = $USER->GetUserName();
$response['userID'] = $USER->GetUserID();
$response['loggedIn'] = $USER->IsLoggedIn();
$response['sessionID'] = $objSession->GetSessionIdentifier();
$response['results'] = $serve->getAll($SORTBY, $INCLUDE_COMPLETED);

print json_encode($response);

?>