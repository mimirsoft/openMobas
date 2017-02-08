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
 * This script takes a POST with a PHPSESSION value set, to confirm the session is active.
 * If it is, it returns as JSON OBJECT.
 * If they are correct, it returns a Process Serve JSON object, along with our normal five messaging values
 * (message, username, userID,loggedIn,sessionID)
 * If the user is not logged in when the session is check, the userID is set to null, and loggedIn is set to false
 *
 * "process_serve": {
 *
    message:
    username:
    userID:
    loggedIn:
    sessionID:

    serve_id:
    $firstname
    lastname
    $comments
    streetnumber
    $street
    $city
    state
    zip
    $type
    $date
    complete:

 * }
 *
 *  *************************************************/


//this is our half assed way of enforcing HTTPS
if($_SERVER['SERVER_PORT']!=443)
{
    exit;
}

//error_reporting(E_ALL);
//ini_set('display_errors', '1');

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


if($USER->IsLoggedIn() != true)//if USER is not logged in....
{
    $response['message'] = "user not logged in";
    $response['username'] = $USER->GetUserName();
    $response['userID'] = null;
    $response['loggedIn'] = false;
    $response['sessionID'] = $objSession->GetSessionIdentifier();
    //results must be array for json decode in app
    $response['results'] = [];

    $response['serve_id'] = null;

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
    //results must be array for json decode in app
    $response['results'] = [];
    
    $response['serve_id'] = null;
    
    print json_encode($response);
    exit;
}



//create variable
$serve_id = "";

if(isset($_GET['serve_id']) && !isset($_GET['browsertest']))
{
    $serve_id = $_GET['serve_id'];
    $serve = new Process_Serve($dbh, $USER, $rbac_user);
    
    $response['message'] = "";
    $response['username'] = $USER->GetUserName();
    $response['userID'] = $USER->GetUserID();
    $response['loggedIn'] = $USER->IsLoggedIn();
    $response['sessionID'] = $objSession->GetSessionIdentifier();
    $results = $serve->getFromId($serve_id);
    //echo $serve_id;
    //print_r($results);
    $merged = array_merge($response, $results);    
    print json_encode($merged);
    exit;
}

if(isset($_POST['_METHOD']) && $_POST['_METHOD'] == "DELETE" && isset($_POST['serve_id']))
{
    $serve_id = $_POST['serve_id'];

    $serve = new Process_Serve($dbh, $USER, $rbac_user);

    $serve_id = $serve->delete($serve_id);
    $response['message'] = "";
    $response['username'] = $USER->GetUserName();
    $response['userID'] = $USER->GetUserID();
    $response['loggedIn'] = $USER->IsLoggedIn();
    $response['sessionID'] = $objSession->GetSessionIdentifier();
    $response['serve_id'] = '';
    print json_encode($response);
    exit;
}
if(isset($_POST['serve_id']) && $_POST['_METHOD'] != "DELETE" && $_POST['_METHOD'] != "PUT")
{
    $serve_id = $_POST['serve_id'];
    @$firstname = $_POST['firstname'];
    @$lastname = $_POST['lastname'];
    @$comments = $_POST['comments'];
    @$streetnumber = $_POST['streetnumber'];
    @$street = $_POST['street'];
    @$city = $_POST['city'];
    @$state = $_POST['state'];
    @$zip = $_POST['zip'];
    @$type = '';
    @$date = $_POST['date'];

    $serve = new Process_Serve($dbh, $USER, $rbac_user);

    if($serve_id == "NEW")
    {
        $serve_id = $serve->create('NULL', $firstname, $lastname, $comments,$streetnumber,
            $street, $city, $state, $zip, $type, $date);
    }
    
    $response['message'] = "";
    $response['username'] = $USER->GetUserName();
    $response['userID'] = $USER->GetUserID();
    $response['loggedIn'] = $USER->IsLoggedIn();
    $response['sessionID'] = $objSession->GetSessionIdentifier();
    $results = $serve->getFromId($serve_id);
    //echo $serve_id;
    //print_r($results);
    $merged = array_merge($response, $results);
    print json_encode($merged);
    exit;
}

if(isset($_POST['serve_id']) && $_POST['_METHOD'] == "PUT" && $_POST['serve_id'] != "NEW")
{
    $serve_id = $_POST['serve_id'];
    @$firstname = $_POST['firstname'];
    @$lastname = $_POST['lastname'];
    @$comments = $_POST['comments'];
    @$streetnumber = $_POST['streetnumber'];
    @$street = $_POST['street'];
    @$city = $_POST['city'];
    @$state = $_POST['state'];
    @$zip = $_POST['zip'];
    @$type = '';
    @$date = $_POST['date'];
    @$complete = $_POST['complete'];
    
    $serve = new Process_Serve($dbh, $USER, $rbac_user);
    
    $serve->update($serve_id, $firstname, $lastname, $comments,$streetnumber,
            $street, $city, $state, $zip, $type, $date, $complete);

    $response['message'] = "";
    $response['username'] = $USER->GetUserName();
    $response['userID'] = $USER->GetUserID();
    $response['loggedIn'] = $USER->IsLoggedIn();
    $response['sessionID'] = $objSession->GetSessionIdentifier();
    $results = $serve->getFromId($serve_id);
    //echo $serve_id;
    //print_r($results);
    $merged = array_merge($response, $results);
    print json_encode($merged);
    exit;
}

if(isset($_GET['serve_id']) && isset($_GET['browsertest']) && $_GET['browsertest']=="update")
{
    $serve_id = $_GET['serve_id'];
    @$firstname = $_GET['firstname'];
    @$lastname = $_GET['lastname'];
    @$comments = $_GET['comments'];
    @$streetnumber = $_GET['streetnumber'];
    @$street = $_GET['street'];
    @$city = $_GET['city'];
    @$state = $_GET['state'];
    @$zip = $_GET['zip'];
    @$type = '';
    @$date = $_GET['date'];
    
    $serve = new Process_Serve($dbh, $USER, $rbac_user);
    
    $serve->update($serve_id, $firstname, $lastname, $comments,$streetnumber,
        $street, $city, $state, $zip, $type, $date);
    
    $response['message'] = "";
    $response['username'] = $USER->GetUserName();
    $response['userID'] = $USER->GetUserID();
    $response['loggedIn'] = $USER->IsLoggedIn();
    $response['sessionID'] = $objSession->GetSessionIdentifier();
    $results = $serve->getFromId($serve_id);
    echo $serve_id;
    //print_r($results);
    $merged = array_merge($response, $results);
    print json_encode($merged);
    exit;
}


if(isset($_GET['serve_id']) && $_GET['browsertest']=="delete")
{
    $serve_id = $_GET['serve_id'];

    $serve = new Process_Serve($dbh, $USER, $rbac_user);

    $serve_id = $serve->delete($serve_id);
    $response['message'] = "";
    $response['username'] = $USER->GetUserName();
    $response['userID'] = $USER->GetUserID();
    $response['loggedIn'] = $USER->IsLoggedIn();
    $response['sessionID'] = $objSession->GetSessionIdentifier();
    $response['serve_id'] = '';
    print json_encode($response);
    exit;
}
?>