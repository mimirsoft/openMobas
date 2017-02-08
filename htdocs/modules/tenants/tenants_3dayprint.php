<?php

include("../../../framework/framework_masterinclude.php");
include("tenants_include.php");
$FRAMEWORK->authenticate($objSession, $MODULE_NAME, $USER, $BASE_DIR);
$system_id = 1;
$statement_object = Framework::get_system($system_id);
$SYS_INFO = unserialize($statement_object['system_array']);

$owed = '';
$net = 0;
$noticedate_month = '';
$noticedate_day = '';
$noticedate_year  = ''; 
$owners = '';
foreach ($_POST as $key => $value)
{
    $$key = $value;
}
foreach($transactions_list as $transaction_id)
{
    $row['date_year'] = ${$transaction_id.'date_year'};
    $row['date_month'] = ${$transaction_id.'date_month'};
    $row['date_day'] = ${$transaction_id.'date_day'};
    $row['enddate_year'] = ${$transaction_id.'enddate_year'};
    $row['enddate_month'] = ${$transaction_id.'enddate_month'};
    $row['enddate_day'] = ${$transaction_id.'enddate_day'};
    $row['transaction_comment'] = ${'transaction_comment'.$transaction_id};
    $row['transaction_amount'] = ${'transaction_amount'.$transaction_id};
    $net += ${'transaction_amount'.$transaction_id};

    $owed[] = $row;
}

include("tenants_3dayprint.phtml");

?>
