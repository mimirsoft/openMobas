<?php
/*
	NOTE:
	There are THREE components that make up the functionality of users.php.
	1. users.php
		- contains the tabs
		- lists users
		- has an iframe for adding/editing users
		- include statements for groups.php and nonusers.php
	2. edit_user.php
		- the contents of the iframe (i.e. a form for adding/editing users)
	3. edit_user_handler.php
		- handles form submittal from edit_user.php
		- provides user with confirmation of successful operation
		- refreshes the parent frame (users.php)

	This structure is mirrored for groups & nonusers
*/

/* $Id $ */

include("../../../framework/framework_masterinclude.php");
Framework::authenticate('Unlimited');
include_once ("includes/calendar_include.php");
include_once 'includes/init.php';

$dbh = new DB_Mysql();


if ( empty ( $login) || $login == "__public__" ) {
  // do not allow public access
  do_redirect ( empty ( $STARTVIEW ) ? "month.php" : "$STARTVIEW" );
  exit;
}

$INC = array('js/users.php','js/visible.php');
print_header($INC);
include("../../../framework/theme.css");
framework::navbar();

//if ( ! $is_admin ) {
//echo "<h2>" . translate("Error") . "</h2>\n" . 
//  translate("You are not authorized") . ".\n";
//  echo "</body>\n</html>";
//  exit;
//}
?>
<a title="<?php etranslate("Admin") ?>" class="nav" href="adminhome.php">&laquo;&nbsp;<?php etranslate("Admin") ?></a><br /><br />

<!-- TABS -->
<div id="tabs">
	<span class="tabfor" id="tab_users"><a href="#tabusers" onclick="return showTab('users')"><?php 
		if ($is_admin) {
			echo translate("Users");
		} else {
			echo translate("Account");
		}
	?></a></span>
	<?php if ($groups_enabled == "Y" && $is_admin) { ?>
		<span class="tabbak" id="tab_groups"><a href="#tabgroups" onclick="return showTab('groups')"><?php etranslate("Groups")?></a></span>
	<?php } 
	if ($nonuser_enabled == 'Y' && $is_admin) { ?>
		<span class="tabbak" id="tab_nonusers"><a href="#tabnonusers" onclick="return showTab('nonusers')"><?php etranslate("NonUser Calendars")?></a></span>
	<?php } ?>
</div>

<!-- TABS BODY -->
<div id="tabscontent">
	<!-- USERS -->
	<a name="tabusers"></a>
	<div id="tabscontent_users">
	<?php if ( Rbac_User::IsAllowedTo($USER->GetUserID(), "admin_module", "calendar_module") ) { ?>
		
		<ul>
			<?php
				$userlist = user_get_users ();
				for ( $i = 0; $i < count ( $userlist ); $i++ ) {
					if ( $userlist[$i]['cal_login'] != '__public__' ) {
						echo "<li title=\"".$userlist[$i]['cal_fullname']."\">";
						echo $userlist[$i]['cal_fullname'];
						if (  $userlist[$i]["cal_is_admin"] == 'U' )
							echo "&nbsp;<abbr title=\"" . translate("denotes administrative user") . "\">*</abbr>";
						echo "</li>\n";
					}
				}
			?>
		</ul>
	*&nbsp;<?php etranslate("denotes administrative user")?><br />

		<?php 
			echo "<iframe " .
				" name=\"useriframe\" id=\"useriframe\" style=\"width:90%;border-width:0px; height:280px;\"></iframe>";
		?>
<?php } else { } ?>
</div>

<?php 
	if ($groups_enabled == "Y" && $is_admin) { 
		include_once 'groups.php';
	} 
	if ($nonuser_enabled == 'Y' && $is_admin) {
		include_once 'nonusers.php';
	}
?>
</div>

<?php print_trailer(); ?>
</body>
</html>
