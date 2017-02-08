<?

/*This is the casesystem functions file
**
*/

class workorder{

    public static function create($ID, $user, $assignee, $comments, $title)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("INSERT INTO workorder_main 
                                       SET wo_id=:1:, 
                                           wo_title=:4:, 
                                           whenopen_date=NOW(), 
                                           whenupdated_date=NOW(), 
                                           whoopen_id=:2:,  
                                           whoupdated_id=:2:, 
                                           whoassigned_id=:3:");
        $stmt->execute($ID, $user, $assignee, $title);
        $caseID = mysql_insert_id();
        workorder::add_entry("NULL", $comments, $caseID, $user);
        return $caseID;
    }

    public static function add_entry($ID, $comments, $caseID, $user)
    {
        $comments = nl2br($comments);
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("INSERT INTO workorder_entry 
                                       SET woentry_id=:1:, 
                                           woentry_txt=:2:, 
                                           woentry_date=NOW(), 
                                           user_id=:3:, 
                                           wo_id=:4: ");
        $stmt->execute($ID, $comments, $user, $caseID);
    }
    public static function get_workorder($ID)
    {
        $dbh = new DB_Mysql();
		$stmt = $dbh->prepare("SELECT * FROM workorder_main WHERE wo_id=:1: LIMIT 1");
		$stmt->execute($ID);
		return $stmt->fetch_assoc();
		
    }
    public static function get_workorder_entries($ID)
    {
        $dbh = new DB_Mysql();
		$stmt = $dbh->prepare("SELECT * FROM workorder_entry WHERE wo_id=:1:");
		$stmt->execute($ID);
		return $stmt->fetchall_assoc();
		
    }
    
    public static function update_case($caseID, $updater, $assignee, $title)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("UPDATE workorder_main 
                                  SET whenupdated_date=NOW(), 
                                      whoupdated_id=:1:, 
                                      whoassigned_id=:2:,
                                      wo_title=:4:                                            
                                WHERE wo_id=:3: ");
        $stmt->execute($updater, $assignee, $caseID, $title);
    }
    public static function close_case($caseID, $updater, $assignee, $title)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("UPDATE casesystem_main 
                                  SET whenupdated_date=NOW(), 
                                      whoupdated_id=:1:, 
                                      whenclosed_date=NOW(), 
                                      whoclose_id=:1:, 
                                      closed_yn='YES', 
                                      whoassigned_id=:3:,
                                      case_title=:4:                                            
                                WHERE case_id=:2: ");
        $stmt->execute($updater, $caseID, $assignee, $title);
    }
    public static function getall($VIEW_CLOSED, $VIEW_ALL, $SORTBY, $USER)
    {
        $dbh = new DB_Mysql();
        $query = "SELECT * FROM workorder_main ";
        if($VIEW_CLOSED != 'YES' |
            $VIEW_ALL != 'YES')
        {
            $query .= "WHERE";
        }
        if($VIEW_CLOSED != 'YES')
        {
            $query .= " closed_yn='NO' ";
        }
        if($VIEW_CLOSED != 'YES' &&
            $VIEW_ALL != 'YES')
        {
            $query .= "AND";
        }
        if($VIEW_ALL != 'YES')
        {
            $query .= " whoassigned_id = :1:";
        }
            $query .= " ORDER BY ".$SORTBY;
        
        $stmt = $dbh->prepare($query);
        $stmt->execute($USER->GetUserID());
        return $stmt->fetchall_assoc();
    }

}
?>
