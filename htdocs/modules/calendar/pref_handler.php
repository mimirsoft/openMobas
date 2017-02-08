<?php
include("../../../framework/framework_masterinclude.php");
Framework::authenticate('Unlimited');
include_once ("includes/calendar_include.php");
include_once 'includes/init.php';
include_once 'includes/site_extras.php';

$error = "";
$dbh = new DB_Mysql();

$updating_public = false;;
if ( $is_admin && ! empty ( $public ) && $public_access == "Y" ) {
  $updating_public = true;
  $prefuser = "__public__";
} elseif (($user != $login) && ($is_admin || $is_nonuser_admin)) {
  $prefuser = "$user";
} else {
  $prefuser = "$login";
}

while ( list ( $key, $value ) = each ( $HTTP_POST_VARS ) ) {
  $setting = substr ( $key, 5 );
  $prefix = substr ( $key, 0, 5 );
  if ( $key == 'user' || $key == 'public'  )
    continue;
  // validate key name.  should start with "pref_" and not include
  // any unusual characters that might cause SQL injection
  if ( ! preg_match ( '/pref_[A-Za-z0-9_]+$/', $key ) ) {
    die_miserable_death ( 'Invalid admin setting name "' .
      $key . '"' );
  }
  //echo "Setting = $setting, key = $key, prefix = $prefix <br />\n";
  if ( strlen ( $setting ) > 0 && $prefix == "pref_" ) {
    $sql =
      "DELETE FROM webcal_user_pref WHERE user_id = '$prefuser' " .
      "AND cal_setting = '$setting'";
    $stmt = $dbh->prepare( $sql );
    $stmt->execute() ;
    if ( strlen ( $value ) > 0 ) {
      $sql = "INSERT INTO webcal_user_pref " .
        "( user_id, cal_setting, cal_value ) VALUES " .
        "( '$prefuser', '$setting', '$value' )";
      $stmt = $dbh->prepare( $sql );
    
    if ( ! $stmt->execute()  ) {
        $error = "Unable to update preference: " . dbi_error () .
	"<br /><br /><span style=\"font-weight:bold;\">SQL:</span> $sql";
        break;
      }
    }
  }
}

if ( empty ( $error ) ) {
  if ( $updating_public ) {
    do_redirect ( "pref.php?public=1" );
  } elseif (($is_admin || $is_nonuser_admin) && $login != $user ) {
    do_redirect ( "pref.php?user=$user" );
  } else {
    do_redirect ( "pref.php" );
  }
}
print_header();
?>

<h2><?php etranslate("Error")?></h2>

<?php etranslate("The following error occurred")?>:
<blockquote>
<?php echo $error; ?>
</blockquote>

<?php print_trailer(); ?>

</body>
</html>
