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
    
$FRAMEWORK_COLORS = array(
            "background_color"=>"#FFFFFF",
            "link_color"=>"#2D4AA1",
            "link_highlightcolor"=>"#AFB5C7",
            "divheader_color"=>"#2D4AA1",
            "divheader_background"=>"#436EEE",
            "navbar_item_color"=>"#2D4AA1",
            "navbar_background"=>"#436EEE",
            "warningtext_color"=>"#AA0000",
            );
//this is the master list of modules, and how the installed modules are controlled at the lowest level

$INSTALLED_MODULES = array(
                        array("mod_code"=>"admin", "mod_title"=>"admin"), 
                        array("mod_code"=>"available", "mod_title"=>"available"),
                        array("mod_code"=>"casesystem", "mod_title"=>"casesystem"),
                        array("mod_code"=>"calendar", "mod_title"=>"calendar"), 
                        array("mod_code"=>"checks", "mod_title"=>"checks"), 
                        array("mod_code"=>"clients", "mod_title"=>"clients"), 
                        array("mod_code"=>"contacts", "mod_title"=>"contacts"), 
                        array("mod_code"=>"cv_master", "mod_title"=>"Customer/Vendor"), 
                        array("mod_code"=>"documents", "mod_title"=>"Document Manager"), 
                        array("mod_code"=>"invoices", "mod_title"=>"Invoices"), 
                        array("mod_code"=>"leads", "mod_title"=>"leads"), 
                        array("mod_code"=>"mailing", "mod_title"=>"mailing"), 
                        array("mod_code"=>"maintenance", "mod_title"=>"maintenance"), 
                        array("mod_code"=>"properties", "mod_title"=>"properties"), 
                        array("mod_code"=>"purchasing", "mod_title"=>"purchasing"), 
                        array("mod_code"=>"recurring", "mod_title"=>"recurring"), 
                        array("mod_code"=>"serves", "mod_title"=>"Process Serving"), 
                        array("mod_code"=>"seo_pages", "mod_title"=>"SEO Pages"), 
                        array("mod_code"=>"statements", "mod_title"=>"statements"), 
                        array("mod_code"=>"timecard", "mod_title"=>"Time Card"), 
                        array("mod_code"=>"tenants", "mod_title"=>"tenants"), 
                        array("mod_code"=>"transactions", "mod_title"=>"transactions"), 
                        array("mod_code"=>"profile", "mod_title"=>"User"), 
                        array("mod_code"=>"yearend", "mod_title"=>"Yearend")
                    
                        );

$BASE_DIR = "/modules";//this is the directory of the install
$EXTERNAL_BASE_DIR = "/";
$API_KEY = "your_private_google_apikey_here";

$SYSTEM_SETTINGS = array(
    "secure"=>false, //replace with true to force https
   "log_files"=>true);

//these are the database settings
$DB_SETTINGS = array(
		"database"=>"mysql",
		"user"=>"omobas",
		"pass"=>"password123",
		"dbhost"=>"localhost",
		"dbname"=>"openmobas");

$ARRAY = array("FRAMEWORK_COLORS" => $FRAMEWORK_COLORS,
		"INSTALLED_MODULES" => $INSTALLED_MODULES,
		"BASE_DIR" => $BASE_DIR,
		"EXTERNAL_BASE_DIR" => $EXTERNAL_BASE_DIR,
		"DB_SETTINGS" => $DB_SETTINGS,
		"API_KEY" => $API_KEY,
		"SYSTEM_SETTINGS" => $SYSTEM_SETTINGS);

$GLOBAL_CONSTANTS['openmobas'] = $ARRAY;

//////////these are the live settings

$DB_SETTINGS = array(
		"database"=>"mysql",
		"user"=>"dbuser",
		"pass"=>"ourpassword",
		"dbhost"=>"localhost",
		"dbname"=>"sample_com");

$ARRAY = array("FRAMEWORK_COLORS" => $FRAMEWORK_COLORS,
		"INSTALLED_MODULES" => $INSTALLED_MODULES,
		"BASE_DIR" => $BASE_DIR,
		"EXTERNAL_BASE_DIR" => $EXTERNAL_BASE_DIR,
		"DB_SETTINGS" => $DB_SETTINGS,
		"API_KEY" => "put google api here",
		"SYSTEM_SETTINGS" => $SYSTEM_SETTINGS);

$GLOBAL_CONSTANTS['example.com'] = $ARRAY; //replace with your url
$GLOBAL_CONSTANTS['www.example.com'] = $ARRAY;//replace with your url

date_default_timezone_set('America/Los_Angeles');

$ARRAY = $GLOBAL_CONSTANTS[$_SERVER['SERVER_NAME']];

foreach ($ARRAY as $key => $value)
{
    $$key = $value;
}
if($_SERVER['SERVER_PORT']==80 && $SYSTEM_SETTINGS['secure']){
   header("Location: https://".$_SERVER['HTTP_HOST'].$BASE_DIR."/interface/main.php");
}

                             

?>