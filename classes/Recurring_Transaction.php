<?php
/*
 *
This file is part of WebPropMan
Copyright (C) 2011, Kevin Milhoan

WebPropMan is free software: you can redistribute it and/or modify
it under the terms of the GNU Affero General Public License as
published by the Free Software Foundation, either version 3 of the
License, or (at your option) any later version.

WebPropMan is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU Affero General Public License for more details.

You should have received a copy of the GNU Affero General Public License
along with WebPropMan.  If not, see <http://www.gnu.org/licenses/>.

Contact MimirSoft at mimirsoft@gmail.com or www.mimirsoft.com

*
*/
/*This is the Recurrntion_Transaction class file
 **
*/

class Recurring_Transaction{

    public static function print_check($recurring_list, $recurring, $date)
    {
        include("../checks/checks_functions.php");
        include("../checks/check.css");
        $accountIDtoFullnameArray = transaction::build_accountIDtoFullName_array(false);

        $stmt = $this->dbh->prepare("SELECT recurringtype_id FROM recurring_type WHERE recurringtype_name = 'ELECTRONIC'");
        $stmt->execute();
        $row = $stmt->fetch_assoc();
        $electronic = $row["recurringtype_id"];
        reset($recurring_list);
        foreach($recurring_list as $recurring_id)
        {
            $NOTLAST = next($recurring_list);
            $stmt = $this->dbh->prepare("SELECT *
                FROM recurring_main AS rm
                WHERE rm.recurring_id='$recurring_id'");
            $stmt->execute();
            $dbRow = $stmt->fetch_assoc();
            if(!(is_array($dbRow)))
            {
                throw new StatementException("Recurring not found");
            }
            if($dbRow['recurringtype_id'] == $electronic)
            {

                $net = $recurring[$recurring_id]['recurring_amount'];
                if($net == 0)
                {
                    $net = transaction::account_total_date($dbRow['recurring_accountW'], "", $date);
                }
                $dbRow['net'] = $net;
                $electronicTrans[] = $dbRow;
            }
            else
            {
                $stmt = $this->dbh->prepare("SELECT *
                    FROM recurring_main AS rm, recurring_check AS rc
                    WHERE rm.recurring_id=rc.recurring_id
                    AND rm.recurring_id='$recurring_id'");
                $stmt->execute();
                $dbRow = $stmt->fetch_assoc();
                if(!(is_array($dbRow)))
                {
                    throw new StatementException("Address not set for recurring transaction. Please set either CONTACT or UNIQUE");

                }
                if ($dbRow['recurringcheck_type'] == 'CONTACT')
                {
                    $stmt = $this->dbh->prepare("SELECT address_id, check_memo FROM recurring_checkcontact WHERE recurringcheck_id=$dbRow[recurringcheck_id]");
                    $stmt->execute();
                    $recurring_checkcontact = $stmt->fetch_assoc();
                    $stmt = $this->dbh->prepare("SELECT * FROM contacts_address WHERE address_id=$recurring_checkcontact[address_id]");
                    $stmt->execute();
                    $contacts_address = $stmt->fetch_assoc();
                    $stmt = $this->dbh->prepare("SELECT firstname, lastname FROM contacts_main WHERE contacts_id=$contacts_address[contacts_id] ");
                    $stmt->execute();
                    $contacts_main = $stmt->fetch_assoc();
                    $checkdate = 	$date;
                    $checkname =    $contacts_main['firstname']." ".$contacts_main['lastname'];
                    $checkname2 =   $contacts_main['firstname']." ".$contacts_main['lastname'];
                    $careof = 	    $contacts_address['careof'];
                    $address = 	    $contacts_address['street'];
                    $city = 	    $contacts_address['city'];
                    $state = 	    $contacts_address['state'];
                    $zip = 		    $contacts_address['zip'];
                    $memo =         $recurring_checkcontact['check_memo'];

                }
                elseif ($dbRow['recurringcheck_type'] == 'UNIQUE')
                {
                    $stmt = $this->dbh->prepare("SELECT * FROM recurring_checkunique WHERE recurringcheck_id=$dbRow[recurringcheck_id]");
                    $stmt->execute();
                    $recurring_checkunique = $stmt->fetch_assoc();
                    $checkdate = 	$date;
                    $checkdate = 	$date;
                    $checkname = $recurring_checkunique['checkunique_name'];
                    $checkname2 = "";
                    $careof = 	 $recurring_checkunique['checkunique_careof'];
                    $address = 	 $recurring_checkunique['checkunique_street'];
                    $city = 	 $recurring_checkunique['checkunique_city'];
                    $state = 	 $recurring_checkunique['checkunique_state'];
                    $zip = 		 $recurring_checkunique['checkunique_zip'];
                    $memo = 	 $recurring_checkunique['checkunique_memo'];

                }
                $net = $recurring[$recurring_id]['recurring_amount'];
                if($net == 0)
                {
                    @$net = transaction::account_total_date_incsub($dbRow['recurring_accountW'], $NULL, $date);
                }
                $account_name = $accountIDtoFullnameArray[$dbRow['recurring_accountW']];

                $check_amount = checks::check_amount($net);
                $startdate = 	"....-..-..";

                include("../checks/checks_body.phtml");
            }
        }
        if(@(is_array($electronicTrans)))
        {
            echo "<p class=\"tiny\">";
            foreach($electronicTrans as $electronicTran)
            {
                echo "Transfer $".$electronicTran['net']." from ".$accountIDtoFullnameArray[$electronicTran['recurring_accountW']]." to ".$accountIDtoFullnameArray[$electronicTran['recurring_accountD']]."<BR>";
            }

        }
    }


    /*
     This function takes an array of account numbers, and a type of account-> element relations,
     either property, tenant, vendor, etc.

     */

    public static function print_statement($invoice_list, $contactORproperty, $contact_array, $addresstype, $dates, $statement_id, $BASE_DIR)
    {
        global $SYS_INFO;
        $accountIDtoNameArray = transaction::build_accountIDtoName_array();
        $accountIDtoFullNameArray = transaction::build_accountIDtoFullName_array(true);
        //We make the start date one month earlier, and one day later
        if($invoice_list == '')
        {
            throw new StatementException("No Accounts chosen for making Statements.  Please choose an account.");
        }
        @reset($invoice_list);

        $stmt = $this->dbh->prepare("SELECT * FROM statements_main WHERE statement_id=:1:");
        $stmt->execute($statement_id);
        $statement_object = $stmt->fetch_assoc();
        $statement_format = unserialize($statement_object['statement_array']);

        foreach($invoice_list as $account_id)
        {
            //check to see if it is last, to disable page break after, so no paper is wasted.
            $NOTLAST = @next($invoice_list) or $NOTLAST = FALSE;
            if($contactORproperty == "CONTACT")
            {
                $contacts_set = $contact_array[$account_id];
                if($contacts_set == '')
                {
                    throw new StatementException("No contact assigned to account $account_id");
                }
                if(is_array($contacts_set))
                {
                    foreach($contacts_set as $contacts_id)
                    {
                        $NOTLAST2 = @next($contacts_set) or $NOTLAST2 = FALSE;
                        $address = contacts::get_address_from_type_and_id($contacts_id, $addresstype);
                        $statement = statements::build_statement($statement_format, $account_id, $dates, $address, $accountIDtoNameArray, $accountIDtoFullNameArray, $BASE_DIR);
                        $statement['date'] = $dates['end_year']."-".$dates['end_month']."-".$dates['end_day'];
                        include("../statements/statements_render.phtml");
                    }
                }
                else
                {
                    $contacts_id = $contacts_set;
                    $address = contacts::get_address_from_type_and_id($contacts_id, $addresstype);
                    $statement = statements::build_statement($statement_format, $account_id, $dates, $address, $accountIDtoNameArray, $accountIDtoFullNameArray, $BASE_DIR);
                    $statement['date'] = $dates['end_year']."-".$dates['end_month']."-".$dates['end_day'];
                    include("../statements/statements_render.phtml");
                }

            }
            if($contactORproperty == "PROPERTY")
            {
                $query = "SELECT tm.tenant_id
                FROM tenants_main AS tm, tenants_accounts AS ta
                WHERE account_id='$account_id'
                AND tm.tenant_id = ta.tenant_id
                AND tm.tenant_current = 'Y'";
                $stmt = $this->dbh->prepare($query);
                $stmt->execute();
                $dbRow = $stmt->fetch_assoc();
                $tenant_id = $dbRow['tenant_id'];
                $query = "SELECT property_id FROM tenants_main WHERE tenant_id='$tenant_id'";
                $stmt = $this->dbh->prepare($query);
                $stmt->execute();
                $dbRow = $stmt->fetch_assoc();
                $propID = $dbRow['property_id'];
                $query = "SELECT property_address,
                property_aptnum,
                property_city,
                property_state,
                property_zip
                FROM properties_main
                WHERE property_id='$propID'";
                $stmt = $this->dbh->prepare($query);
                $stmt->execute();
                $address_prop = $stmt->fetch_assoc();
                $address['street'] = $address_prop['property_address'];
                $address['aptnum'] = $address_prop['property_aptnum'];
                $address['city'] = $address_prop['property_city'];
                $address['state'] = $address_prop['property_state'];
                $address['zip'] = $address_prop['property_zip'];
                $firstname = "";
                $query = "SELECT * FROM tenants_multi WHERE tenant_id='$tenant_id'";
                $stmt = $this->dbh->prepare($query);
                $stmt->execute();
                while($dbRow = $stmt->fetch_assoc())
                {
                    $query3 = "SELECT CONCAT(firstname,' ',lastname) AS full_name
                    FROM contacts_main
                    WHERE contacts_id='$dbRow[contacts_id]'
                    ORDER BY full_name";
                    $stmt2 = $this->dbh->prepare($query3);
                    $stmt2->execute();
                    $dbRow2 = $stmt2->fetch_assoc();
                    $firstname .= $dbRow2['full_name'].";";
                }
                $address['firstname'] = $firstname;
                $address['lastname'] = '';
                $address['careof'] = '';
                $statement = statements::build_statement($statement_format, $account_id, $dates['startdate'], $dates['enddate'], $dates['prestartdate'], $address, $accountIDtoNameArray, $accountIDtoFullNameArray, $BASE_DIR);
                $statement['date'] = $dates['end_date'];
                include("../statements/statements_render.phtml");

            }

        }
    }
    public static function email_statement($invoice_list, $contactORproperty, $contact_array, $addresstype, $dates, $statement_id, $BASE_DIR)
    {
        global $SYS_INFO;
        $accountIDtoNameArray = transaction::build_accountIDtoName_array();
        $accountIDtoFullNameArray = transaction::build_accountIDtoFullName_array(true);
        if($invoice_list == '')
        {
            throw new StatementException("No Accounts chosen for making Statements.  Please choose an account.");
        }
        @reset($invoice_list);

        $stmt = $this->dbh->prepare("SELECT * FROM statements_main WHERE statement_id=:1:");
        $stmt->execute($statement_id);
        $statement_object = $stmt->fetch_assoc();
        $statement_format = unserialize($statement_object['statement_array']);
        echo "<div style=\"clear: both;\">EMAILED STATEMENTS</div>";
        foreach($invoice_list as $account_id)
        {
            if($contactORproperty == "CONTACT")
            {
                $contacts_set = $contact_array[$account_id];
                if($contacts_set == '')
                {
                    throw new StatementException("No contact assigned to account $account_id");
                }
                if(is_array($contacts_set))
                {
                    foreach($contacts_set as $contacts_id)
                    {
                        $NOTLAST2 = @next($contacts_set) or $NOTLAST2 = FALSE;
                        $address = contacts::get_address_from_type_and_id($contacts_id, $addresstype);
                        $email_addys = contacts::getall_emailaddys_from_contact_id($contacts_id);
                        print_r($email_addys);
                        $statement = statements::build_statement($statement_format, $account_id, $dates, $address, $accountIDtoNameArray, $accountIDtoFullNameArray, $BASE_DIR);
                        $statement['date'] = $dates['end_year']."-".$dates['end_month']."-".$dates['end_day'];
                        ob_start();
                        echo "<html><title></title><body>";
                        include("../statements/statements_render.css");
                        include("../statements/statements_render.phtml");
                        echo "</body></html>";
                        $msg_body = ob_get_contents();
                        ob_end_clean();
                        foreach($email_addys as $email)
                        {
                            mailing::email_stuff($SYS_INFO['COMPANY_NAME'],$SYS_INFO['COMPANY_GENERALEMAIL'],$email['email_address'],"Statement for ".$accountIDtoNameArray[$account_id],$msg_body);
                            echo "Sent to ".$email['email_address']."<BR/>";
                        }
                    }
                }
                else
                {
                    $contacts_id = $contacts_set;
                    $address = contacts::get_address_from_type_and_id($contacts_id, $addresstype);
                    $email_addys = contacts::getall_emailaddys_from_contact_id($contacts_id);
                    $statement = statements::build_statement($statement_format, $account_id, $dates, $address, $accountIDtoNameArray, $accountIDtoFullNameArray, $BASE_DIR);
                    $statement['date'] = $dates['end_year']."-".$dates['end_month']."-".$dates['end_day'];
                    ob_start();
                    echo "<html><title></title><body>";
                    include("../statements/statements_render.css");
                    include("../statements/statements_render.phtml");
                    echo "</body></html>";
                    $msg_body = ob_get_contents();
                    ob_end_clean();
                    mailing::email_stuff($SYS_INFO['COMPANY_NAME'],$SYS_INFO['COMPANY_GENERALEMAIL'],$email['email_address'],"Statement for ".$accountIDtoNameArray[$account_id],$msg_body);
                    echo "Sent to ".$email['email_address']."<BR/>";
                }

            }
            if($contactORproperty == "PROPERTY")
            {
                $query = "SELECT tm.tenant_id
                FROM tenants_main AS tm, tenants_accounts AS ta
                WHERE account_id='$account_id'
                AND tm.tenant_id = ta.tenant_id
                AND tm.tenant_current = 'Y'";
                $stmt = $this->dbh->prepare($query);
                $stmt->execute();
                $dbRow = $stmt->fetch_assoc();
                $tenant_id = $dbRow['tenant_id'];
                $query = "SELECT property_id FROM tenants_main WHERE tenant_id='$tenant_id'";
                $stmt = $this->dbh->prepare($query);
                $stmt->execute();
                $dbRow = $stmt->fetch_assoc();
                $propID = $dbRow['property_id'];
                $query = "SELECT property_address,
                property_aptnum,
                property_city,
                property_state,
                property_zip
                FROM properties_main
                WHERE property_id='$propID'";
                $stmt = $this->dbh->prepare($query);
                $stmt->execute();
                $address_prop = $stmt->fetch_assoc();
                $address['street'] = $address_prop['property_address'];
                $address['aptnum'] = $address_prop['property_aptnum'];
                $address['city'] = $address_prop['property_city'];
                $address['state'] = $address_prop['property_state'];
                $address['zip'] = $address_prop['property_zip'];
                $firstname = "";
                $query = "SELECT * FROM tenants_multi WHERE tenant_id='$tenant_id'";
                $stmt = $this->dbh->prepare($query);
                $stmt->execute();
                while($dbRow = $stmt->fetch_assoc())
                {
                    $query3 = "SELECT CONCAT(firstname,' ',lastname) AS full_name
                    FROM contacts_main
                    WHERE contacts_id='$dbRow[contacts_id]'
                    ORDER BY full_name";
                    $stmt2 = $this->dbh->prepare($query3);
                    $stmt2->execute();
                    $dbRow2 = $stmt2->fetch_assoc();
                    $firstname .= $dbRow2['full_name'].";";
                }
                $address['firstname'] = $firstname;
                $dates['startdate'] = "0000-00-00";
                $dates['prestartdate'] = "0000-00-00";
                $statement = statements::build_statement($statement_format, $account_id, $dates, $address, $accountIDtoNameArray, $BASE_DIR);
                ob_start();
                echo "<html><title></title><body>";
                include("../statements/statements_render.css");
                include("../statements/statements_render.phtml");
                echo "</body></html>";
                $msg_body = ob_get_contents();
                ob_end_clean();
                mailing::email_stuff($SYS_INFO['COMPANY_NAME'],$SYS_INFO['COMPANY_GENERALEMAIL'],$to_add,"Statement for ".$accountIDtoNameArray[$account_id],$msg_body);
            }
        }
    }

    public static function statement_date_checker($date_year, $date_month, $date_day)
    {
        if(($date_month-1) == 0)
        {
            $prestartdate = ($date_year-1)."-12-".$date_day;
            if($date_day == 31)
            {
                $startdate = ($date_year-1)."-12-".($date_day);
            }
            else
            {
                $startdate = ($date_year-1)."-12-".($date_day+1);
            }
            $startdate = ($date_year-1)."-12-".($date_day+1);
            $enddate = $date_year."-".$date_month."-".$date_day;
            $checkdate = $enddate;
        }
        else
        {
            $prestartdate = $date_year."-".($date_month-1)."-".$date_day;
            if($date_day == 31)
            {
                $startdate = $date_year."-".($date_month-1)."-".($date_day);
            }
            else
            {
                $startdate = $date_year."-".($date_month-1)."-".($date_day+1);
            }
            $enddate = $date_year."-".$date_month."-".$date_day;
            $checkdate = $enddate;
        }
        $ar['checkdate'] = $checkdate;
        $ar['enddate'] = $enddate;
        $ar['startdate'] = $startdate;
        $ar['prestartdate'] = $prestartdate;
        return $ar;
    }

    public static function add_recurring($ID, $comment, $type, $statement_type, $DC_array)
    {

        if($ID == "NULL")// If it is a new entry.
        {
            $stmt = $this->dbh->prepare("INSERT INTO recurring_main
                                           SET recurring_id=:1:,
                                               recurring_comment=:2:,
                                               recurringtype_id=:3:,
                                               statement_type=:4:");
            $stmt->execute($ID, $comment, $type, $statement_type);
            $ID = mysql_insert_id();
        }
        else//If it is an edit to an existing entry
        {
            $stmt = $this->dbh->prepare("UPDATE recurring_main
                                      SET recurring_comment=:2:,
                                          recurringtype_id=:3:,
                                          statement_type=:4:
                                    WHERE recurring_id=:1:");
            $stmt->execute($ID, $comment, $type, $statement_type);
            //delete the old debits and credits
            recurring::delete_debitcredit($ID);
        }
        foreach($DC_array as $DC_line)
        {
            recurring::add_recurring_debit_credit('NULL', $DC_line['account'], $ID, $DC_line['amount'], $DC_line['dc']);
        }
    }
    function delete_debitcredit($ID)
    {

        $stmt = $this->dbh->prepare("DELETE FROM recurring_debit_credit WHERE recurring_id=:1:");
        $stmt->execute($ID);
    }

    public static function getall_recurring_on_account($ID)
    {

        $stmt = $this->dbh->prepare("SELECT tdc.recurring_id, tdc.recurring_dc AS tdc_dc, tdc.recurring_dc_amount AS amount, tdc.recurring_account AS tdc_account, odc.recurring_account AS odc_account, rm.recurringtype_id, rm.recurring_comment
                             FROM recurring_debit_credit AS tdc
                        LEFT JOIN recurring_debit_credit AS odc
                                ON tdc.recurring_id=odc.recurring_id
                        INNER JOIN recurring_main AS rm
                                ON rm.recurring_id=tdc.recurring_id
                        INNER JOIN recurring_type AS rt
                                ON rt.recurringtype_id=rm.recurringtype_id
                            WHERE tdc.recurring_account=:1: AND odc.recurring_account != :1:
                            ORDER BY recurringtype_name");

        $stmt->execute($ID);
        return $stmt->fetchall_assoc();

    }
    public static function get_recurring_by_ID($ID)
    {

        $stmt = $this->dbh->prepare("SELECT *
                                  FROM recurring_main
                            INNER JOIN recurring_type
                                    ON recurring_main.recurringtype_id = recurring_type.recurringtype_id
                                 WHERE recurring_main.recurring_id=:1:");
        $stmt->execute($ID);
        return $stmt->fetch_assoc();

    }
    public static function update_recurring_amount($ID, $amount)
    {

        $stmt = $this->dbh->prepare("UPDATE recurring_main
                                SET recurring_amount=:2:
                                 WHERE recurring_id=:1:");
        $stmt->execute($ID, $amount);
    }

    public static function getall_recurring_of_type($ID)
    {

        $stmt = $this->dbh->prepare("SELECT *
                                  FROM recurring_main
                            INNER JOIN recurring_type
                                    ON recurring_main.recurringtype_id = recurring_type.recurringtype_id
                                 WHERE recurring_main.recurringtype_id=:1:");
        $stmt->execute($ID);
        return $stmt->fetchall_assoc();

    }
    public static function getall_recurring()
    {

        $stmt = $this->dbh->prepare("SELECT *
                                  FROM recurring_main
                            INNER JOIN recurring_type
                                    ON recurring_main.recurringtype_id = recurring_type.recurringtype_id");
        $stmt->execute();
        return $stmt->fetchall_assoc();

    }
    public static function getall_debits_of_recurring($ID)
    {

        $stmt = $this->dbh->prepare("SELECT *
                                  FROM recurring_debit_credit
                                 WHERE recurring_debit_credit.recurring_id=:1:
                                   AND recurring_dc='DEBIT'");
        $stmt->execute($ID);
        return $stmt->fetchall_assoc();
    }
    public static function getall_credits_of_recurring($ID)
    {

        $stmt = $this->dbh->prepare("SELECT *
                                  FROM recurring_debit_credit
                                 WHERE recurring_debit_credit.recurring_id=:1:
                                   AND recurring_dc='CREDIT'");
        $stmt->execute($ID);
        return $stmt->fetchall_assoc();
    }

    public static function add_recurring_debit_credit($ID, $account, $TID, $amount, $DebCred)
    {

        $stmt = $this->dbh->prepare("INSERT INTO recurring_debit_credit
                                    SET recurring_dc_id=:1:,
                                recurring_account=:2:,
                                recurring_id=:3:,
                                recurring_dc_amount=:4:,
                                recurring_dc=:5:");
        $stmt->execute($ID, $account, $TID, $amount, $DebCred);

    }

    public static function add_recurring_type($ID, $name)
    {

        if($ID == "NULL")// If it is a new entry.
        {
            $stmt = $this->dbh->prepare("INSERT INTO recurring_type
                                           SET recurringtype_id=:1:,
                                               recurringtype_name=:2:");
        }
        else//If it is an edit to an existing entry
        {
            $stmt = $this->dbh->prepare("UPDATE recurring_type
                                      SET recurringtype_name=:2:
                                    WHERE recurringtype_id=:1:");
        }
        $stmt->execute($ID, $name);
    }

    public static function getall_recurring_types()
    {

        $stmt = $this->dbh->prepare("SELECT * FROM recurring_type ORDER BY recurringtype_name");
        $stmt->execute();
        $types = $stmt->fetchall_assoc();
        return $types;

    }
    public static function get_recurringtype_by_id($ID)
    {

        $stmt = $this->dbh->prepare("SELECT * FROM recurring_type WHERE recurringtype_id=:1:");
        $stmt->execute($ID);
        $types = $stmt->fetch_assoc();
        return $types;

    }

    public static function add_recurring_check($ID, $name, $memo, $street, $city, $state, $zip, $careof, $main_ID)
    {

        if($ID == "NULL")// If it is a new entry.
        {
            $stmt = $this->dbh->prepare("INSERT INTO recurring_checkunique
                                           SET checkunique_id=:1:,
                                               checkunique_name=:2:,
                                               checkunique_memo=:3:,
                                               checkunique_street=:4:,
                                               checkunique_city=:5:,
                                               checkunique_state=:6:,
                                               checkunique_zip=:7:,
                                               checkunique_careof=:8:,
                                               recurringcheck_id=:9:");
        }
        else//If it is an edit to an existing entry
        {
            $stmt = $this->dbh->prepare("UPDATE recurring_checkunique
                                      SET checkunique_name=:2:,
                                          checkunique_memo=:3:,
                                          checkunique_street=:4:,
                                          checkunique_city=:5:,
                                          checkunique_state=:6:,
                                          checkunique_zip=:7:,
                                          checkunique_careof=:8:,
                                          recurringcheck_id=:9:
                                    WHERE checkunique_id=:1:");
        }
        $stmt->execute($ID, $name, $memo, $street, $city, $state, $zip, $careof, $main_ID);
    }
    public static function set_recurring_check($ID, $type, $main_ID)
    {

        if($ID == "NULL")// If it is a new entry.
        {
            $stmt = $this->dbh->prepare("INSERT INTO recurring_check
                                           SET recurringcheck_id=:1:,
                                               recurringcheck_type=:2:,
                                               recurring_id=:3:");
        }
        else//If it is an edit to an existing entry
        {
            $stmt = $this->dbh->prepare("UPDATE recurring_check
                                      SET recurringcheck_type=:2:,
                                          recurring_id=:3:
                                    WHERE recurringcheck_id=:1:");
        }
        $stmt->execute($ID, $type, $main_ID);
    }

    public static function set_check_contact($ID, $memo, $address, $check_ID)
    {

        if($ID == "NULL")// If it is a new entry.
        {
            $stmt = $this->dbh->prepare("INSERT INTO recurring_checkcontact
                                           SET checkcontact_ID=:1:,
                                               check_memo=:2:,
                                               address_id=:3:,
                                               recurringcheck_id=:4:");
        }
        else//If it is an edit to an existing entry
        {
            $stmt = $this->dbh->prepare("UPDATE recurring_checkcontact
                                      SET check_memo=:2:,
                                          address_id=:3:,
                                          recurringcheck_id=:4:
                                    WHERE checkcontact_id=:1: ");
        }
        $stmt->execute($ID, $memo, $address, $check_ID);
    }

    public static function delete_recurring($ID)
    {

        $stmt = $this->dbh->prepare("DELETE FROM recurring_main WHERE recurring_id=:1:");
        $stmt->execute($ID);
    }

    public static function build_recurringtypearray()
    {
        global $recurringTypes;

        $stmt = $this->dbh->prepare("SELECT * FROM recurring_type ORDER BY recurringtype_name");
        $stmt->execute();
        while($row = $stmt->fetch_assoc())
        {
            $recurringTypes[$row['recurringtype_id']] = $row['recurringtype_name'];
        }
    }

    public static function recurring_address_api($recurring_id)
    {
        global $recurring;
        global $recurringCheck;
        global $recurringCheckContact;
        global $checkAddy;
        global $recurringCheckUnique;


        $stmt = $this->dbh->prepare("SELECT * FROM recurring_main WHERE recurring_id=:1:");
        $stmt->execute($recurring_id);
        $recurring = $stmt->fetch_assoc();
        $stmt = $this->dbh->prepare("SELECT * FROM recurring_check WHERE recurring_id=:1:");
        $stmt->execute($recurring_id);
        if ($recurringCheck = $stmt->fetch_assoc())
        {
            if ($recurringCheck['recurringcheck_type'] == "CONTACT") // Is the check a type CONTACT?
            {
                $stmt2 = $this->dbh->prepare("SELECT *
                    FROM recurring_checkcontact
                    WHERE recurringcheck_id='$recurringCheck[recurringcheck_id]'");
                $stmt2->execute();
                if ($recurringCheckContact = $stmt2->fetch_assoc()) // Does it have an address already set?
                {
                    $stmt3 = $this->dbh->prepare("SELECT *
                        FROM contacts_address AS ca, contacts_main as cm
                        WHERE address_id='$recurringCheckContact[address_id]'
                        AND ca.contacts_id = cm.contacts_id ");
                    $stmt3->execute();
                    $checkAddy = $stmt3->fetch_assoc();
                    $checkAddy['nameoncheck'] = $checkAddy['firstname']." ".$checkAddy['lastname'];
                    $checkAddy['addressee'] = $checkAddy['firstname']." ".$checkAddy['lastname'];
                    $checkAddy['memo'] = $recurringCheckContact['check_memo'];
                }
                else
                {
                    $recurringCheckContact['checkcontact_id']="NULL";
                }

            }
            if ($recurringCheck['recurringcheck_type'] == "UNIQUE")
            {
                $stmt2 = $this->dbh->prepare("SELECT * FROM recurring_checkunique WHERE recurringcheck_id='$recurringCheck[recurringcheck_id]'");
                $stmt2->execute();
                if ($dbRow = $stmt2->fetch_assoc())
                {
                    $recurringCheckUnique = $dbRow;
                    $checkAddy['nameoncheck'] = $dbRow['checkunique_name'];
                    $checkAddy['addressee'] = '';
                    $checkAddy['memo'] =        $dbRow['checkunique_memo'];
                    $checkAddy['careof'] =      $dbRow['checkunique_careof'];
                    $checkAddy['street'] =      $dbRow['checkunique_street'];
                    $checkAddy['city'] =        $dbRow['checkunique_city'];
                    $checkAddy['state'] =       $dbRow['checkunique_state'];
                    $checkAddy['zip'] =         $dbRow['checkunique_zip'];
                }
                else
                {
                    $recurringCheckUnique['checkunique_id']="NULL";
                }
            }
        }
        else
        {
            $recurringCheck['recurringcheck_id']="NULL";
            $recurringCheck['recurringcheck_type']="NULL";
            $recurringCheckContact['checkcontact_id']="NULL";
            $recurringCheckUnique['checkunique_id']="NULL";
        }
    }

}