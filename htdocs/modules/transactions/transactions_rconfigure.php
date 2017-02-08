<?
include("../../../framework/framework_masterinclude.php");
include("transactions_include.php");
Framework::authenticate('Unlimited');//the two includes must be before the authentica, to supply the needed module name for authentication

$ACTION = '';
$recurringtype_id = '';
$recurringtype_name = '';

foreach ($_POST as $key => $value)
{
    $$key = $value;
    unset($_POST->{$key});
}

$dbh = new DB_Mysql();
if ($ACTION == "Save Type")
{
    recurring::add_recurring_type($recurringtype_id, $recurringtype_name);
}
if ($ACTION == "Update Recurring")
{
    $all_recurring = recurring::getall_recurring();
    foreach($all_recurring as $row)
    {
        $rec_debs = recurring::getall_debits_of_recurring($row['recurring_id']);
        $total = 0;
        foreach($rec_debs as $rec_deb)
        {
            $total = $total + $rec_deb['recurring_dc_amount'];
        }
        recurring::update_recurring_amount($row['recurring_id'], $total);
        unset($rec_debs);
        
    }
}
if ($ACTION == "Edit Type")
{
    $stmt = $dbh->prepare("SELECT * FROM recurring_type WHERE recurringtype_id=:1");
    $stmt->execute($recurring_type_edit);
    $dbRow = $stmt->fetch_assoc();
    $recurringtype_id = $dbRow['recurringtype_id'];
    $recurringtype_name = $dbRow['recurringtype_name'];
}
else
{
    $recurringtype_id = "NULL";
}
$stmt = $dbh->prepare("SELECT * FROM recurring_type ORDER BY recurringtype_name");
$stmt->execute();
$types = $stmt->fetchall_assoc();
$stmt = $dbh->prepare("SELECT SUM(recurring_amount) AS total, recurringtype_name
                         FROM recurring_type, recurring_main
                        WHERE recurring_main.recurringtype_id = recurring_type.recurringtype_id
                    GROUP BY recurringtype_name");
$stmt->execute();
$summary = $stmt->fetchall_assoc();


$stmt->execute();


include("transactions_rconfigure.phtml");


?>
