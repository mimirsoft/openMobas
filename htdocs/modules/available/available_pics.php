<?
include("../../../framework/framework_masterinclude.php");
include("available_include.php");

$dbh = new DB_Mysql();


$stmt = $dbh->prepare("SELECT * 
                         FROM properties_files as pf, files_main as fm
                        WHERE pf.property_id=:1: 
                          AND pf.file_id = fm.file_id");
$stmt->execute($_GET['id']);
$files = $stmt->fetchall_assoc();


include("available_pics.phtml");

