<?

include("../../../framework/framework_masterinclude.php");
include("tenants_include.php");
Framework::authenticate('Unlimited');//the two includes must be before the authenticate, to supply the needed module name for authentication
$system_id = 1;
$statement_object = Framework::get_system($system_id);
$SYS_INFO = unserialize($statement_object['system_array']);

$date_year = date('Y');
$date_month = date('m');
$date_day = date('d');
$ACTION = '';
$empty = '';
$names = "&#160; &#013; DOES 1-10";
$owners = "&#160; &#013; C/O ".$SYS_INFO['COMPANY_NAME']; 
$property_address = '';
$property_aptnum = '';
$property_city = '';
$property_county = '';
$property_state = '';
$property_zip = '';

foreach ($_POST as $key => $value)
{
    $$key = $value;
}

$dbh = new DB_Mysql();

if($ACTION == "Print")
{
    
}
else
{
   include("tenants_3dayfillin.phtml");
}
?>
