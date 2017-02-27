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

this has been updated 20170227
 *
 */

error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once("om_globalconstants.php");
require_once("classes/Database_Mysql.class.php");
require_once("classes/Framework.class.php");
require_once("classes/User_Session.class.php");
require_once("classes/User.class.php");
require_once("classes/Rbac_User.class.php");

if($SYSTEM_SETTINGS['secure'] && $_SERVER['SERVER_PORT']!=443){
    $redirect = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    header('HTTP/1.1 301 Moved Permanently');
    header('Location: ' . $redirect);
    exit();
}

$dbh = new DB_Mysql($DB_SETTINGS);
$objSession = new User_Session($dbh);
$objSession->Impress();
$USER = $objSession->GetUserObject();
$rbac_user = new Rbac_User($dbh, $USER);

$FRAMEWORK = new Framework($dbh, $USER, $rbac_user);
$FRAMEWORK->setModules($INSTALLED_MODULES);




?>
