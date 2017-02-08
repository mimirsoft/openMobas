<?php

    
$FRAMEWORK_COLORS = array(
            //"background_color"=>"#FFFFFF",
            "background_color"=>"#FFFFFF",
            "link_color"=>"#2D4AA1",
            "link_highlightcolor"=>"#AFB5C7",
            "divheader_color"=>"#2D4AA1",
            "divheader_background"=>"#436EEE",
            "navbar_item_color"=>"#2D4AA1",
            "navbar_background"=>"#436EEE",
            "warningtext_color"=>"#AA0000",
            );

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
                        array("mod_code"=>"yearend", "mod_title"=>"yearend")
                    
                        );

$BASE_DIR = "/modules";
$EXTERNAL_BASE_DIR = "/";
$API_KEY = "ABQIAAAADSdPHpuX50A8lp76BmwryxTLaf0C0QKhPJmuhK8xgbxEIeNCQhSzXRwYNQgNa0kQel4i9DJ-MXTKIQ";

$SYSTEM_SETTINGS = array(
    "secure"=>false, 
   "log_files"=>true);


$DB_SETTINGS = array(
		"database"=>"mysql",
		"user"=>"twpizza",
		"pass"=>"mysql!abi2c",
		"dbhost"=>"localhost",
		"dbname"=>"twpizza");

$ARRAY = array("FRAMEWORK_COLORS" => $FRAMEWORK_COLORS,
		"INSTALLED_MODULES" => $INSTALLED_MODULES,
		"BASE_DIR" => $BASE_DIR,
		"EXTERNAL_BASE_DIR" => $EXTERNAL_BASE_DIR,
		"DB_SETTINGS" => $DB_SETTINGS,
		"API_KEY" => $API_KEY,
		"SYSTEM_SETTINGS" => $SYSTEM_SETTINGS);

$GLOBAL_CONSTANTS['twpizza'] = $ARRAY;

$DB_SETTINGS = array(
		"database"=>"mysql",
		"user"=>"rentalsbigbear",
		"pass"=>"NRzcsrC9zEJt2RPS",
		"dbhost"=>"localhost",
		"dbname"=>"rentalsbigbear");

$ARRAY = array("FRAMEWORK_COLORS" => $FRAMEWORK_COLORS,
		"INSTALLED_MODULES" => $INSTALLED_MODULES,
		"BASE_DIR" => $BASE_DIR,
		"EXTERNAL_BASE_DIR" => $EXTERNAL_BASE_DIR,
		"DB_SETTINGS" => $DB_SETTINGS,
		"API_KEY" => $API_KEY,
		"SYSTEM_SETTINGS" => $SYSTEM_SETTINGS);

$GLOBAL_CONSTANTS['ampm'] = $ARRAY;

$DB_SETTINGS = array(
		"database"=>"mysql",
		"user"=>"pmsandiego",
		"pass"=>"NRzcsrC9zEJt2RPS",
		"dbhost"=>"localhost",
		"dbname"=>"pmsandiego");

$ARRAY = array("FRAMEWORK_COLORS" => $FRAMEWORK_COLORS,
		"INSTALLED_MODULES" => $INSTALLED_MODULES,
		"BASE_DIR" => $BASE_DIR,
		"EXTERNAL_BASE_DIR" => $EXTERNAL_BASE_DIR,
		"DB_SETTINGS" => $DB_SETTINGS,
		"API_KEY" => $API_KEY,
		"SYSTEM_SETTINGS" => $SYSTEM_SETTINGS);

$GLOBAL_CONSTANTS['pmsd'] = $ARRAY;



$DB_SETTINGS = array(
		"database"=>"mysql",
		"user"=>"db535709380",
		"pass"=>"theworks!Abi2c",
		"dbhost"=>"localhost",
		"dbname"=>"dbo535709380");

$ARRAY = array("FRAMEWORK_COLORS" => $FRAMEWORK_COLORS,
		"INSTALLED_MODULES" => $INSTALLED_MODULES,
		"BASE_DIR" => $BASE_DIR,
		"EXTERNAL_BASE_DIR" => $EXTERNAL_BASE_DIR,
		"DB_SETTINGS" => $DB_SETTINGS,
		"API_KEY" => $API_KEY,
		"SYSTEM_SETTINGS" => $SYSTEM_SETTINGS);

$GLOBAL_CONSTANTS['lsetc'] = $ARRAY;


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

$BASE_DIR = "/modules";
$EXTERNAL_BASE_DIR = "";

$DB_SETTINGS = array(
		"database"=>"mysql",
		"user"=>"pmsandiego",
		"pass"=>"NRzcsrC9zEJt2RPS",
		"dbhost"=>"localhost",
		"dbname"=>"pmsandiego");

$ARRAY = array("FRAMEWORK_COLORS" => $FRAMEWORK_COLORS,
		"INSTALLED_MODULES" => $INSTALLED_MODULES,
		"BASE_DIR" => $BASE_DIR,
		"EXTERNAL_BASE_DIR" => $EXTERNAL_BASE_DIR,
		"DB_SETTINGS" => $DB_SETTINGS,
		"API_KEY" => "ABQIAAAADSdPHpuX50A8lp76BmwryxTLaf0C0QKhPJmuhK8xgbxEIeNCQhSzXRwYNQgNa0kQel4i9DJ-MXTKIQ",
		"SYSTEM_SETTINGS" => $SYSTEM_SETTINGS);

$GLOBAL_CONSTANTS['pmsandiego.com'] = $ARRAY;
$GLOBAL_CONSTANTS['www.pmsandiego.com'] = $ARRAY;

$BASE_DIR = "/modules";
$EXTERNAL_BASE_DIR = "";

$DB_SETTINGS = array(
		"database"=>"mysql",
		"user"=>"lsetc_com",
		"pass"=>"NRzcsrC9zEJt2RPS",
		"dbhost"=>"localhost",
		"dbname"=>"lsetc_com");

$ARRAY = array("FRAMEWORK_COLORS" => $FRAMEWORK_COLORS,
		"INSTALLED_MODULES" => $INSTALLED_MODULES,
		"BASE_DIR" => $BASE_DIR,
		"EXTERNAL_BASE_DIR" => $EXTERNAL_BASE_DIR,
		"DB_SETTINGS" => $DB_SETTINGS,
		"API_KEY" => "ABQIAAAADSdPHpuX50A8lp76BmwryxTLaf0C0QKhPJmuhK8xgbxEIeNCQhSzXRwYNQgNa0kQel4i9DJ-MXTKIQ",
		"SYSTEM_SETTINGS" => $SYSTEM_SETTINGS);

$GLOBAL_CONSTANTS['lsetc.com'] = $ARRAY;
$GLOBAL_CONSTANTS['legalservicesetc.com'] = $ARRAY;
$GLOBAL_CONSTANTS['www.lsetc.com'] = $ARRAY;
$GLOBAL_CONSTANTS['www.legalservicesetc.com'] = $ARRAY;

$DB_SETTINGS = array(
		"database"=>"mysql",
		"user"=>"rentalsbigbear",
		"pass"=>"NRzcsrC9zEJt2RPS",
		"dbhost"=>"localhost",
		"dbname"=>"rentalsbigbear");

$ARRAY = array("FRAMEWORK_COLORS" => $FRAMEWORK_COLORS,
		"INSTALLED_MODULES" => $INSTALLED_MODULES,
		"BASE_DIR" => $BASE_DIR,
		"EXTERNAL_BASE_DIR" => $EXTERNAL_BASE_DIR,
		"DB_SETTINGS" => $DB_SETTINGS,
		"API_KEY" => "ABQIAAAADSdPHpuX50A8lp76BmwryxTLaf0C0QKhPJmuhK8xgbxEIeNCQhSzXRwYNQgNa0kQel4i9DJ-MXTKIQ",
		"SYSTEM_SETTINGS" => $SYSTEM_SETTINGS);

$GLOBAL_CONSTANTS['rentalsbigbear.com'] = $ARRAY;
$GLOBAL_CONSTANTS['www.rentalsbigbear.com'] = $ARRAY;

date_default_timezone_set('America/Los_Angeles');

$ARRAY = $GLOBAL_CONSTANTS[$_SERVER['SERVER_NAME']];

foreach ($ARRAY as $key => $value)
{
    $$key = $value;
}
//if($_SERVER['SERVER_PORT']==80 && $SYSTEM_SETTINGS['secure']){
 //   header("Location: https://".$_SERVER['HTTP_HOST'].$BASE_DIR."/interface/main.php");
//}

                             

?>