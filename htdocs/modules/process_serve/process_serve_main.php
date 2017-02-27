<?php
/*
 *
 This file is part of OpenMobas
 Copyright (C) 2011, Kevin Milhoan

 OpenMobas is free software: you can redistribute it and/or modify
 it under the terms of the GNU Affero General Public License as
 published by the Free Software Foundation, either version 3 of the
 License, or (at your option) any later version.

 OpenMobas is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU Affero General Public License for more details.

 You should have received a copy of the GNU Affero General Public License
 along with WebPropMan.  If not, see <http://www.gnu.org/licenses/>.

 Contact MimirSoft at mimirsoft@gmail.com or www.mimirsoft.com

 *
 */

error_reporting(E_ALL);
ini_set('display_errors', '1');
require_once("../../../../openMobas/framework_masterinclude.php");
require_once("process_serve_include.php");
$FRAMEWORK->authenticate($objSession, $MODULE_NAME, $USER, $BASE_DIR);
$WARNING['show'] = false;

$ACTION = '';

$SORTBY = 'default';

$search_results = '';
$string = '';

$serve_date_year = date('Y');
$serve_date_month = date('m');
$serve_date_day = date('d');
$serve_date_hour =  date("h");
$serve_date_minute = date("i");
//$whenreturn_date_year = ;
//$whenreturn_date_month = date('m');
//$whenreturn_date_day = date('d');
//$whenreturn_date_hour = date("h");
//$whenreturn_date_minute = date("i");
$ampm = '';
$complete = '';
$INCLUDE_COMPLETED = 0;


$serve = new Process_Serve($dbh, $USER, $rbac_user);
foreach ($_POST as $key => $value)
{
    $$key = $value;
    unset($_POST->{$key});
}
foreach ($_GET as $key => $value)
{
    $$key = $value;
    unset($_GET->{$key});
}

if($serve_date_hour == 12)
{
	$serve_date_hour = 0;
}
if($ampm == "pm")
{
	$serve_date_hour = $serve_date_hour+12;
}
if($serve_date_hour >= 24)
{
	$serve_date_hour = 23;
}
$serve_date = $serve_date_year."-".$serve_date_month."-".$serve_date_day." ".$serve_date_hour.":".$serve_date_minute;


switch($ACTION)
{
	case "Record Serve":
		 
         $serve_id = $serve->create('NULL', $firstname, $lastname, $comments,$streetnumber, 
                                        $street, $city, $state, $zip, $type, $serve_date, $complete);
         $serve_info = $serve->getFromId($serve_id);
         $WARNING['show'] = true;
         $WARNING['message'] = "Serve for ".$firstname." ".$lastname." entered";
    break;
   	case "Search All":
		if($string != "")
		{
			$search_results = $serve->search($string);
		}
		break;
}

//$whenreturn_date = $whenreturn_date_year."-".$whenreturn_date_month."-".$whenreturn_date_day." ".$whenreturn_date_hour.":".$whenreturn_date_minute.":00";
//$whenreturn_date_hour = date("h");
//echo "<br/> usserarray";
//echo "after this";
$users = $rbac_user->getAllAllowedTo("access_module", "process_serve_module", $FRAMEWORK);
//print_r($users);
//echo "before this";
$userArray = $FRAMEWORK->getUserArray();	

	
//print_r($userArray);

$pad = array("user_id"=>0, "username"=>"UNASSIGNED");
array_unshift($users, $pad);                       
$userArray[0] = $pad; 

//$leadtypes = lead::getall_leadcats();
//$leadOrigin = lead::getall_leadorigins();

//$settings = lead::getall_settings();
//foreach($settings as $row)
//{
 //   $defaults[$row['setting_name']] = $row['setting_value'];
//}
//$SEARCH = $VIEW_ALL.",".$SORTBY.",".$VIEW_WHOSE;

//$tag_id_to_name = lead::get_leadtag_to_name_array();

//get all our process serves, replace line below for that
$results = $serve->getAll($SORTBY, $INCLUDE_COMPLETED);


include("process_serve_main.phtml");
//$whenreturn_date = date('Y-m-d H:i');
//echo $whenreturn_date;
?>
