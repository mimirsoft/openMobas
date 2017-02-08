<?php
include("../../../framework/framework_masterinclude.php");
Framework::authenticate('Unlimited');
include_once ("includes/calendar_include.php");
include_once 'includes/init.php';

function print_color_sample ( $color ) {
  echo "<table style=\"border-width:0px;\"><tr><td style=\"background-color:$color;\">&nbsp;&nbsp;</td></tr></table>";
}


// I know we've already loaded the global settings above, but read them
// in again and store them in a different place because they may have
// been superceded by local user preferences.
// We will store value in the array $s[].
$dbh = new DB_Mysql();
$stmt = $dbh->prepare( "SELECT cal_setting, cal_value FROM webcal_config");

$s = array ();
if ( $stmt->execute() ) {
  while ( $row = $stmt->fetch_row (  ) ) {
    $setting = $row[0];
    $value = $row[1];
    $s[$setting] = $value;
    //echo "Setting '$setting' to '$value' <br />\n";
  }
}

$BodyX = 'onload="public_handler(); eu_handler(); email_handler();"';
$INC = array('js/admin.php','js/visible.php');


include_once 'admin.phtml';
