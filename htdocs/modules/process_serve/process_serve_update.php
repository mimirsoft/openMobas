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

$serve_date_year = '';
$serve_date_month = '';
$serve_date_day = '';
$serve_date_hour =  '';
$serve_date_minute = '';
$ampm = '';
$complete = 0;

if(isset($_GET['lead_id']))
{
    @$lead_id = $_GET['lead_id'];
}    
foreach ($_POST as $key => $value)
{
    $$key = $value;
    unset($_POST->{$key});
}
$serve = new Process_Serve($dbh, $USER, $rbac_user);
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
	/*case "Create Lead":
		 
         $serve_id = $serve->create('NULL', $firstname, $lastname, $comments, 
                                        $street, $city, $state, $zip, $type, $serve_date);
        break;
    */
    case "Save":
        //if the lead has been assigned to someone else
        $serve->update($serve_id, $firstname, $lastname, $comments, $streetnumber,
        $street, $city, $state, $zip, $type, $serve_date, $complete);
        break;
    case "Delete":
            //delete it, and go back to main
        $serve->delete($serve_id);
        header ("Location: process_serve_main.php");
        break;
        
    default:
}

$serve_info = $serve->getFromId($serve_id);

$str_date = strtotime($serve_info['date']);
if($str_date == -62169955200)
{

}else {
	$serve_date_year = date('Y', $str_date);
	$serve_date_month = date('m', $str_date);
	$serve_date_day = date('d', $str_date);
	$serve_date_hour = date('h', $str_date);
	$serve_date_minute = date('i', $str_date);
}

include("process_serve_update.phtml");