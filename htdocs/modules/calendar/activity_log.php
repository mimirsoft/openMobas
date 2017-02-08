<?php
include("../../../framework/framework_masterinclude.php");
Framework::authenticate('Unlimited');
include_once ("includes/calendar_include.php");
include_once 'includes/init.php';

include_once 'includes/site_extras.php';


$dbh = new DB_Mysql();
$sql = "SELECT webcal_entry_log.user_id, webcal_entry_log.cal_user_cal, " .
  "webcal_entry_log.cal_type, webcal_entry_log.cal_date, " .
  "webcal_entry_log.cal_time, webcal_entry.cal_id, " .
  "webcal_entry.cal_name, webcal_entry_log.cal_log_id " .
  "FROM webcal_entry_log, webcal_entry " .
  "WHERE webcal_entry_log.cal_entry_id = webcal_entry.cal_id ";
$startid = getIntValue ( 'startid', true );
if ( ! empty ( $startid ) )
  $sql .= "AND webcal_entry_log.cal_log_id <= $startid ";
$sql .= "ORDER BY webcal_entry_log.cal_log_id DESC";

$stmt = $dbh->prepare($sql);
$stmt->execute();
$result1 =  $stmt->fetchall_row();

include_once 'activity_log.phtml';
?>