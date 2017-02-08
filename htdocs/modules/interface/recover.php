<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once("../../../../openMobas/framework_masterinclude.php");
@$message = $_GET['message']; 
?>
<style type="text/css">
    div.loggin {
        position: absolute;
        left:40%;
        top:40%;
        border-top: 1px solid #035156;
        border-bottom: 1px solid #035156;
        border-left: 1px solid #035156;
        border-right: 1px solid #035156;
   }
</style>
<div class="loggin"> Username/ Password Recovery
<?php if($message = 'notfound')
{
    echo "<BR /> EMAIL ADDRESS NOT FOUND<BR /> TRY AGAIN";
}

?>
<form action="recover_ac.php"  method=POST>
Email Address:<input type="TEXT"  name="email"  value="" size="10">
<input type="SUBMIT"  name="RECOVER"  value="RECOVER" >
</form>
</div>