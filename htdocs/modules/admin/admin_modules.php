<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
require_once("../../../../openMobas/framework_masterinclude.php");
require_once("admin_include.php");
$FRAMEWORK->authenticate($objSession, $MODULE_NAME, $USER, $BASE_DIR); //the two includes must be before the authenticate, to supply the needed module name for authentication

$ACTION = '';
if(isset($_POST['ACTION']))
{
    $module_name =  $_POST['module_name'];
    $ACTION = $_POST['ACTION'];
}

$RBAC = new Rbac($dbh, $USER, $rbac_user, $objSession);

switch ($ACTION)
{
    case "Verify RBAC":
    
        echo "verify RBAC";        
        $RBAC->verify_rbac($module_name);
        $RBAC->cascade_session_permissions();
        
        break;
    //install module
    case "Install Module":
    	include("../$module_name/{$module_name}_rbac.php");
    	

    	
		break;
		
    case "Load Defaults":
    	//try to install
        include("../$module_name/{$module_name}_rbac.php");
    	        
    	break;

	case "Install Module2":
	    //try to install
	    try{
	        $system = new Admin_System();
	        $system->install_module($module_name);
	    }
	    //if failure, catch
	    catch(Admin_SystemException $e)
	    {
	        echo $e->message;
	    }
	     
	    break;
    case "Upgrade Module":
    	//check version
    	
    	//if lower run upgrade
    	
    	break;
        		
}


$tmp = $FRAMEWORK->getModules();
foreach($tmp as $a)
{
    $regMods[] = $a['mod_code'];
}


include("admin_modules.phtml");

?>