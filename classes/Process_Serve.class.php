<?php

/*This is the lead functions file
**
*/
class Process_ServeException extends Exception  {
    public $message;
    public function __construct($message)
    {
        $this->message = $message;
    }
}
class Process_Serve{

	private $dbh;
	private $user;
	private $rbac_user;
	private $serve_id;
	
	public function __construct($dbh, $user, $rbac_user)
	{
	
		$this->dbh = $dbh;
		$this->user = $user;
		$this->rbac_user = $rbac_user;
	
	}	
	
    public function create($ID, $firstname, $lastname, $comments,$streetnumber, 
    		$street, $city, $state, $zip, $type, $date, $complete)
    {
        $stmt = $this->dbh->prepare("INSERT INTO process_serve_main 
                                       SET serve_id=:1:, 
                                           firstname=:2:, 
                                           lastname=:3:, 
                                           comments=:4:, 
                                           streetnumber=:5:,
                                           street=:6:,
                                           city=:7:,
                                           state=:8:,
                                           zip=:9:,
    									   type=:10:,
    									   date=:11:,
                                           complete=:12:");
        $comments = nl2br($comments);
        $stmt->execute($ID, $firstname, $lastname, $comments, $streetnumber, 
                                        $street, $city, $state, $zip, $type, $date, $complete);
        $this->serve_id = $stmt->dbh->insert_id;
        return $this->serve_id;

    }
    public function update($ID, $firstname, $lastname, $comments, $streetnumber, 
    		$street, $city, $state, $zip, $type, $date, $complete)
    {
    	$stmt = $this->dbh->prepare("     UPDATE process_serve_main
                                       SET firstname=:2:,
                                           lastname=:3:,
                                           comments=:4:,
                                           streetnumber=:5:,
                                           street=:6:,
                                           city=:7:,
                                           state=:8:,
                                           zip=:9:,
    									   type=:10:,
    									   date=:11:,
                                           complete=:12:
                                     WHERE serve_id=:1:
    	                               LIMIT 1");
    	$comments = nl2br($comments);
    	$stmt->execute($ID, $firstname, $lastname,  $comments,$streetnumber, 
    			$street, $city, $state, $zip, $type, $date, $complete);
    } 
    public function delete($ID)
    {
        $stmt = $this->dbh->prepare("DELETE FROM process_serve_main
                                     WHERE serve_id=:1:
    	                               LIMIT 1");
        $stmt->execute($ID);
    }
    
    public function getAll($sortby, $include_complete)
    {
    	$query = "SELECT *
                    FROM process_serve_main";
    	    
    	switch($include_complete){
    	    case 1:
    	        $query .= "";
    	        break;
	        case 0:
	            $query .= " WHERE complete = 0 ";
	            break;
    	}
    	$query .= " ORDER BY ";
    	switch($sortby){
	        case "LASTNAME":
	    		$query .= "lastname";
				break;
	        case "FIRSTNAME":
	    		$query .= "firstname";
				break;
	        case "STREETNUMBER":
	        	$query .= "streetnumber";
				break;
			case "STREET":
				$query .= "street";
				break;
			case "CITY":
				$query .= "city";
				break;
			case "STATE":
				$query .= "state";
				break;
			case "ZIP":
				$query .= "zip";
				break;
			case "TYPE":
				$query .= "type";
				break;
			case "DATE":
				$query .= "date";
				break;
			default:
				$query .= "date";
				break;
    	}

        $stmt = $this->dbh->prepare($query);
    	$stmt->execute();
    	$result = $stmt->fetchall_assoc();
    	return $result;
    
    }
    public function getFromId($ID)
    {
    	$query = "SELECT *
                    FROM process_serve_main
                   WHERE serve_id = :1:
    			 	 LIMIT 1";
    	$stmt = $this->dbh->prepare($query);
    	$stmt->execute($ID);
    	$result = $stmt->fetch_assoc();
    	return $result;
    }
    public function search($string)
    {
    	$stmt = $this->dbh->prepare("SELECT *
    								   FROM process_serve_main
    			                      WHERE firstname LIKE '%$string%'
    									 OR lastname LIKE '%$string%'
    									 OR street LIKE '%$string%'
    							   ORDER BY date");
    	$stmt->execute();
    	return $stmt->fetchall_assoc();
    }
    /*
     * 
     * below was copied from the lead module
     * 


    public function add_entry($ID, $comments, $leadID, $user)
    {
        $comment = nl2br($comments);
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("INSERT INTO lead_entry 
                                       SET leadentry_id=:1:, 
                                           entry_txt=:2:, 
                                           entry_date=NOW(), 
                                           user_id=:3:, 
                                           lead_id=:4: ");
        $stmt->execute($ID, $comments, $user, $leadID);
        $stmt = $dbh->prepare("UPDATE lead_main 
                                  SET whenupdated_date=NOW(), 
                                      whoupdated_id=:1:
                                WHERE lead_id=:2: ");
        $stmt->execute($user, $leadID);
    }
	public  function delete_tag($leadcat_id, $leadID)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("DELETE FROM lead_tags 
                                       WHERE leadcat_id=:1:
                                       AND 
                                           lead_id=:2: ");

        $stmt->execute($leadcat_id, $leadID);
    }   
    public static function add_tag($leadcat_id, $leadID)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("INSERT INTO lead_tags 
                                       SET leadcat_id=:1:, 
                                           lead_id=:2: ");

        try{
             $stmt->execute($leadcat_id, $leadID);
        }
        catch(MysqlException $exception)
        {
            throw new LeadException("Lead already tagged with category ".$leadcat_id);
        }
    }   
    public static function update_case($caseID, $updater, $assignee, $title)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("UPDATE casesystem_main 
                                  SET whenupdated_date=NOW(), 
                                      whoupdated_id=:1:, 
                                      whoassigned_id=:2:,
                                      case_title=:4:                                            
                                WHERE case_id=:3: ");
        $stmt->execute($updater, $assignee, $caseID, $title);
    }
    public static function close_lead($caseID, $updater, $assignee)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("UPDATE lead_main 
                                  SET whenupdated_date=NOW(), 
                                      whoupdated_id=:1:, 
                                      whenclosed_date=NOW(), 
                                      whoclose_id=:1:, 
                                      closed_yn='YES', 
                                      whoassigned_id=:3:
                                WHERE lead_id=:2: ");
        $stmt->execute($updater, $caseID, $assignee);
    }
   public static function open_lead($caseID, $updater, $assignee)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("UPDATE lead_main 
                                  SET whenupdated_date=NOW(), 
                                      whoupdated_id=:1:, 
                                      whenclosed_date='0000-00-00 00:00:00', 
                                      whoclose_id=:1:, 
                                      closed_yn='NO', 
                                      whoassigned_id=:3:
                                WHERE lead_id=:2: ");
        $stmt->execute($updater, $caseID, $assignee);
    }
   public static function convert_lead($ID, $firstname, $lastname, $prop_street, $prop_city, $prop_state, $prop_zip,
                                       $comments, $street, $city, $state, $zip, $phone_num, $phone_num2, $email_address,
                                        $user, $assignee, $desc, $prop_unit)
    {
        $lead_info = leads::get_lead_from_id($ID);
        if($lead_info['converted_yn'] == 'YES')
        {
            return;
        }
            
        $contacttype_id = contacts::get_contacttype_id_from_name("OWNER");
	$contacts_id = contacts::add_contact("NULL", $lastname, $firstname, "", $contacttype_id, "");
	contacts::add_contactphonenum("NULL", $phone_num, "1", $contacts_id);
	contacts::add_contactphonenum("NULL", $phone_num2, "1", $contacts_id);
	contacts::add_contactemailaddy("NULL", $email_address, "1", $contacts_id);
	contacts::add_contactaddress("NULL", $street, $city, $state, $zip, "1", $contacts_id, "");
	$property_id = properties::add_property("NULL", $prop_street, $prop_unit, "", $prop_city, $prop_state, "", $prop_zip, "", "", "", "", "", "",
                                 "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", 
                                 "", "", "", "", "", "", "", "", "", "", "", "",  date("Y-m-d"), '1', "", "CURRENT", "Y", "N", "N", $comments, $desc);
        $ownership_percentage = 1.00;
        properties::add_owner('NULL', $property_id, $contacts_id, $ownership_percentage);
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("UPDATE lead_main 
                                  SET converted_yn='YES' 
                                WHERE lead_id=:1:"); 
        $stmt->execute($ID);


    }
    public static function search($string)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT *
                                FROM lead_main
                                WHERE comments LIKE '%$string%'  
                             ORDER BY whenupdated_date");
        $stmt->execute();
        return $stmt->fetchall_assoc();
    }
    public static function get_leads($open, $sort, $whose, $user_id)
    {
        $dbh = new DB_Mysql();
        $query = "SELECT *, lead_main.lead_id, GROUP_CONCAT(lead_tags.leadcat_id) AS leadtag_string
                    FROM lead_main 
                    LEFT JOIN lead_tags
                    ON  lead_main.lead_id = lead_tags.lead_id ";
        switch($open)
        {
            case "OPEN":
                $query .= "WHERE closed_yn = 'NO'";
                break;
            case "CLOSED":
                $query .= "WHERE closed_yn = 'YES'";
                break;
            case "ALL":
                $query .= "WHERE ((closed_yn = 'YES') OR (closed_yn = 'NO'))";
                break;
            default:
        }
        switch($whose)
        {
            case "OWN":
                $query .= "AND (whoassigned_id = :1: OR whoassigned_id=0)";
                break;
            case "ALL":
                break;
            default:
        }
        $query .= " GROUP BY lead_main.lead_id "; 
        
        switch($sort)
        {
            case "whoopen_id":
                $query .= " ORDER BY whoopen_id, whenupdated_date";
                break;
            case "whoupdated_id":
                $query .= " ORDER BY whoupdated_id, whenupdated_date";
                break;
            case "whoassigned_id":
                $query .= " ORDER BY whoassigned_id, whenupdated_date";
                break;
            case "lead_cat":
                $query .= " ORDER BY leadcat_name";
                break;
             case "whenopen_date":
                $query .= " ORDER BY whenopen_date";
                break;
            case "whenupdated_date":
                $query .= " ORDER BY whenupdated_date";
                break;
            case "whenclosed_date":
                $query .= " ORDER BY whenclosed_date";
                break;
            case "whenreturn_date":
                $query .= " ORDER BY whenreturn_date";
                break;
            default:
                $query .= " ORDER BY whenupdated_date";
                break;
        
        }
        $stmt = $dbh->prepare($query);
        $stmt->execute($user_id);
        $result = $stmt->fetchall_assoc();
        return $result;

    }
    
 
    public static function getall_lead_with_followup_between($start, $end)
    {
    	$dbh = new DB_Mysql();
    	$query = "SELECT *
                    FROM lead_main
                   WHERE whenreturn_date >= :1:
    			AND whenreturn_date <= :2:";
    	$stmt = $dbh->prepare($query);
    	$stmt->execute($start, $end);
    	$result = $stmt->fetchall_assoc();
    	return $result;
    }
    
    public static function getall_leadcats()
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * FROM lead_category ORDER BY leadcat_name  ");
        $stmt->execute();
        return $stmt->fetchall_assoc();
    }
    public static function get_leadtag_to_name_array()
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * FROM lead_category ORDER BY leadcat_name ");
        $stmt->execute();
        $set = $stmt->fetchall_assoc();
        $idarray = array();
        foreach($set as $row)
        {
                $idarray[$row['leadcat_id']]        = $row['leadcat_name'];
        }
        return $idarray;
        
    }
    public static function get_tags_of_lead($ID)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * FROM lead_tags 
        				   INNER JOIN lead_category 
        				   ON lead_category.leadcat_id = lead_tags.leadcat_id 
        				   WHERE lead_id=:1: ORDER BY leadcat_name");
        $stmt->execute($ID);
        return $stmt->fetchall_assoc();
    }
    public static function add_leadcat($ID, $name)
    {
        $dbh = new DB_Mysql();
        if($ID == "NULL")// If it is a new entry.
        {
            $stmt = $dbh->prepare("INSERT INTO lead_category 
                                           SET leadcat_id=:1:,  
                                               leadcat_name=:2:");
        }
        else//If it is an edit to an existing entry
        {
            $stmt = $dbh->prepare("UPDATE lead_category 
                                      SET leadcat_name=:2:
                                    WHERE leadcat_id=:1:");
        }
        $stmt->execute($ID, $name);
    }
    public static function get_leadcat_from_id($ID)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * FROM lead_category WHERE leadcat_id = :1:");
        $stmt->execute($ID);
        return $stmt->fetch_assoc();
    }
    public static function getall_leadorigins()
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * FROM lead_origin ORDER BY leadorigin_name  ");
        $stmt->execute();
        return $stmt->fetchall_assoc();
    }
    public static function add_leadorigin($ID, $name)
    {
        $dbh = new DB_Mysql();
        if($ID == "NULL")// If it is a new entry.
        {
            $stmt = $dbh->prepare("INSERT INTO lead_origin
                                           SET leadorigin_id=:1:,  
                                               leadorigin_name=:2:");
        }
        else//If it is an edit to an existing entry
        {
            $stmt = $dbh->prepare("UPDATE lead_origin 
                                      SET leadorigin_name=:2:
                                    WHERE leadorigin_id=:1:");
        }
        $stmt->execute($ID, $name);
    }
    public static function get_leadorigin_from_id($ID)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * FROM lead_origin WHERE leadorigin_id = :1:");
        $stmt->execute($ID);
        return $stmt->fetch_assoc();
    }
    public static function save_setting($name, $value)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("INSERT INTO lead_module_settings
                                       SET setting_name=:1:, 
                                           setting_value=:2:");
        $stmt->execute($name, $value);
    }
    public static function delete_setting()
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("DELETE FROM lead_module_settings");
        $stmt->execute();
    }
    public static function getall_settings()
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * FROM lead_module_settings ORDER BY setting_name  ");
        $stmt->execute();
        return $stmt->fetchall_assoc();
    }
public static function setColor($lead_id, $color)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("UPDATE lead_main
                                  SET color=:1: 
                                WHERE lead_id=:2:");
        $stmt->execute($color, $lead_id);
    }
    public static function reset_followups()
    {
    	$dbh = new DB_Mysql();
    	$stmt = $dbh->prepare("UPDATE lead_main
                                       SET  whenreturn_date=''");
    
    	$stmt->execute();
    }
    
    
    
    */
    
}
?>
