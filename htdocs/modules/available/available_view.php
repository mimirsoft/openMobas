<?php
include("../../../framework/framework_masterinclude.php");
include("available_include.php");

$dbh = new DB_Mysql();
$stmt = $dbh->prepare("SELECT * 
                         FROM properties_main 
                        WHERE property_id=:1:");
$stmt->execute($_GET['id']);
$dbRow = $stmt->fetch_assoc();
foreach ($dbRow as $key => $value)
{
    $key2 = substr($key, 9);
    $$key2 = $value;
}
$property_id = $dbRow['property_id'];
$propertytype_id = $dbRow['propertytype_id'];
$property_avail = $dbRow['property_avail'];

$stmt = $dbh->prepare("SELECT * 
                         FROM properties_files as pf, files_main as fm
                        WHERE pf.property_id=:1: 
                          AND pf.file_id = fm.file_id");
$stmt->execute($property_id);
$files = $stmt->fetchall_assoc();


include("available_view.phtml");

