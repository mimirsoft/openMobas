<?
include("../../../framework/framework_masterinclude.php");
include("contacts_include.php");
Framework::XML_authenticate();//the two includes must be before the authentica, to supply the needed module name for authentication


$ACTION = '';
foreach($_POST as $key => $value)
{
    $$key = $value;
}
foreach($_GET as $key => $value)
{
    $$key = $value;
}

    $contactSet = contacts::getall_contacts_of_type($ACTION);
    ob_start();
    echo "<table>";
    foreach($contactSet as $contact)
    {
    ?>
        <TR>
            
            <TD>
                    <a href="<? echo $BASE_DIR ?>/contacts/contacts_edit.php?contacts_id=<? echo $contact['contacts_id'] ?>" target="_blank" method=POST><? echo $contact['lastname']; 
                    if($contact['lastname'] ==""){ echo "NO LAST NAME"; } ?></a>     
            </TD>
            <TD>
                    <a href="<? echo $BASE_DIR ?>/contacts/contacts_edit.php?contacts_id=<? echo $contact['contacts_id'] ?>" target="_blank" method=POST><? echo $contact['firstname'] ?></a>
            </TD>
            <TD>
                    <a href="<? echo $BASE_DIR ?>/contacts/contacts_activityupdate.php?contacts_id=<? echo $contact['contacts_id'] ?>" target="_blank" method=POST>OPEN ACTIVITY LOG</a>
            </TD>
            
        </TR>

    <?
    }
    echo "</table>";
    $render = ob_get_contents();
    ob_end_clean();
    header('Content-Type: text/html');
    echo $render;            
                    
