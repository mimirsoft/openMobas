<?php
include("../../../framework/framework_masterinclude.php");
Framework::authenticate('Unlimited');
include_once ("includes/calendar_include.php");
include_once 'includes/init.php';

$error = "";
if ($user != $login)
  $user = ( ($is_admin || $is_nonuser_admin) && $user ) ? $user : $login;

$dbh = new DB_Mysql();
$stmt = $dbh->prepare( "DELETE FROM webcal_asst WHERE cal_boss = '$user'");
$stmt->execute();
# update user list

if ( ! empty ( $users ) ){
  for ( $i = 0; $i < count ( $users ); $i++ ) {
    $stmt = $dbh->prepare( "INSERT INTO webcal_asst ( cal_boss, cal_assistant ) " .
      "VALUES ( '$user', '$users[$i]' )" );
    $stmt->execute();
  }
}

$url = "assistant_edit.php";
if (($is_admin || $is_nonuser_admin) && $login != $user )
   $url = $url . (strpos($url, "?") === false ? "?" : "&amp;") . "user=$user";
do_redirect ( $url );

print_header();
?>
<h2><?php etranslate("Error")?></h2>

<blockquote>
<?php

echo $error;
//if ( $sql != "" )
//  echo "<br /><br /><span style=\"font-weight:bold;\">SQL:</span> $sql";
//?>
</blockquote>

<?php print_trailer(); ?>
</body>
</html>
