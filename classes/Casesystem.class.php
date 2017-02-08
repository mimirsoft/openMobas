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
/*This is the casesystem functions file
**
*/
class CasesystemException extends Exception  {
    public $message;
    public function __construct($message)
    {
        $this->message = $message;
        
    }
}
class Casesystem{

    public static function create_case($ID, $user, $assignee, $comments, $hiddencomments, $case_title, $action_needed, $status_text)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("INSERT INTO casesystem_main 
                                       SET case_id=:1:, 
                                           case_title=:4:, 
                                           whenopen_date=NOW(), 
                                           whenupdated_date=NOW(), 
                                           whoopen_id=:2:,  
                                           whoupdated_id=:2:, 
                                           whoassigned_id=:3:,
                                           action_needed=:4:,
                                           status_text=:5:");
        $stmt->execute($ID, $user, $assignee, $case_title, $action_needed, $status_text);
        $caseID = mysql_insert_id();
        Casesystem::add_entry("NULL", $comments, $hiddencomments, $caseID, $user);
        return $caseID;
    }

    public static function get_case($ID)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * FROM casesystem_main WHERE case_id=:1: LIMIT 1");
        $stmt->execute($ID);
        return $stmt->fetch_assoc();
    }
    
    public static function add_entry($ID, $comments, $hiddencomments, $caseID, $user)
    {
        $comments = nl2br($comments);
        $hiddencomments = nl2br($hiddencomments);
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("INSERT INTO casesystem_entry 
                                       SET caseentry_id=:1:, 
                                           casesystem_txt=:2:, 
                                           hiddencasesystem_txt=:3:, 
                                           caseentry_date=NOW(), 
                                           user_id=:4:, 
                                           case_id=:5: ");
        $stmt->execute($ID, $comments, $hiddencomments, $user, $caseID);
    }
    public static function checkFollowUp()
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("UPDATE casesystem_main 
                                  SET action_needed=1                                           
                                WHERE NOW()>whenfollowup
                                AND closed_yn='NO'");
        $stmt->execute();
        
    }    
    public static function setFollowUp($caseID, $updater, $followup)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("UPDATE casesystem_main 
                                  SET whoupdated_id=:1:, 
                                      whenfollowup=:2:                                            
                                WHERE case_id=:3: ");
        $stmt->execute($updater, $followup, $caseID);
        
    }
    public static function update_case($caseID, $updater, $assignee, $title, $action_needed, $status_text)
    {
        @$updaterUser = New User($updater);
        @$assigneeUser = New User($assignee);
        //get the existing case
        $caseinfo = Casesystem::get_case($caseID);
        // if the current assignee is not the one we are assigning to, add an entry
        if($caseinfo['whoassigned_id'] !=  $assignee)
        {
            Casesystem::add_entry("NULL", "ASSIGNED TO ".$assigneeUser->GetUserName()." BY ".$updaterUser->GetUserName(), $hiddencomments, $caseID, $updater);
        }
        // if the current status_text is not the one we are setting, add an entry
        if($caseinfo['status_text'] !=  $status_text)
        {
            Casesystem::add_entry("NULL", "NEXT ACTION/WAITING FOR:".$status_text, $hiddencomments, $caseID, $updater);
            
        }        
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("UPDATE casesystem_main 
                                  SET whenupdated_date=NOW(), 
                                      whoupdated_id=:1:, 
                                      whoassigned_id=:2:,
                                      case_title=:4:,
                                      action_needed=:5:,
                                      status_text=:6:                                            
                                WHERE case_id=:3: ");
        $stmt->execute($updater, $assignee, $caseID, $title, $action_needed, $status_text);
        
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
                                      action_needed=0, 
                                      whoassigned_id=:3:,
                                      case_title=:4:                                            
                                WHERE case_id=:2: ");
        $stmt->execute($updater, $caseID, $assignee, $title);
    }
    
    public static function getAllCases()
    {   
        $dbh = new DB_Mysql();
        $query = "SELECT *,casesystem_main.case_id,whoop.username AS whoopen_username, whoup.username AS whoupdate_username, 
        		         whoass.username AS whoassign_username,
                         GROUP_CONCAT(cases_mtm_cv_id.cv_id) AS cv_tag_string
                    FROM casesystem_main
               LEFT JOIN user_main AS whoup
                      ON casesystem_main.whoupdated_id = whoup.user_id
               LEFT JOIN user_main AS whoop
                      ON casesystem_main.whoopen_id = whoop.user_id
               LEFT JOIN user_main AS whoass
                      ON casesystem_main.whoassigned_id = whoass.user_id
               LEFT JOIN cases_mtm_cv_id
                      ON casesystem_main.case_id  = cases_mtm_cv_id.case_id 
                GROUP BY casesystem_main.case_id 
                ORDER BY whenupdated_date";
        $stmt = $dbh->prepare($query);
        $stmt->execute();
        $cases = $stmt->fetchall_assoc();
        return $cases;
    }
    public static function getAllCasesAssignedToID($id)
    {   
        $dbh = new DB_Mysql();
        $query = "SELECT *,casesystem_main.case_id,whoop.username AS whoopen_username, whoup.username AS whoupdate_username, 
        		         whoass.username AS whoassign_username,
                         GROUP_CONCAT(cases_mtm_cv_id.cv_id) AS cv_tag_string
                    FROM casesystem_main
               LEFT JOIN user_main AS whoup
                      ON casesystem_main.whoupdated_id = whoup.user_id
               LEFT JOIN user_main AS whoop
                      ON casesystem_main.whoopen_id = whoop.user_id
               LEFT JOIN user_main AS whoass
                      ON casesystem_main.whoassigned_id = whoass.user_id
               LEFT JOIN cases_mtm_cv_id
                      ON casesystem_main.case_id  = cases_mtm_cv_id.case_id
                   WHERE whoassigned_id = :1: 
                GROUP BY casesystem_main.case_id 
                ORDER BY whenupdated_date";
        $stmt = $dbh->prepare($query);
        $stmt->execute($id);
        $cases = $stmt->fetchall_assoc();
        return $cases;
    }
    public static function getAllOpenCases()
    {   
        $dbh = new DB_Mysql();
        $query = "SELECT *,casesystem_main.case_id,whoop.username AS whoopen_username, whoup.username AS whoupdate_username, 
        		         whoass.username AS whoassign_username,
                         GROUP_CONCAT(cases_mtm_cv_id.cv_id) AS cv_tag_string
                    FROM casesystem_main
               LEFT JOIN user_main AS whoup
                      ON casesystem_main.whoupdated_id = whoup.user_id
               LEFT JOIN user_main AS whoop
                      ON casesystem_main.whoopen_id = whoop.user_id
               LEFT JOIN user_main AS whoass
                      ON casesystem_main.whoassigned_id = whoass.user_id
               LEFT JOIN cases_mtm_cv_id
                      ON casesystem_main.case_id  = cases_mtm_cv_id.case_id 
                   WHERE closed_yn='NO'
                GROUP BY casesystem_main.case_id 
                ORDER BY whenupdated_date";
        $stmt = $dbh->prepare($query);
        $stmt->execute();
        $cases = $stmt->fetchall_assoc();
        return $cases;
    }
    public static function getAllOpenCasesAssignedToID($id)
    {   
        $dbh = new DB_Mysql();
        $query = "SELECT *,casesystem_main.case_id,whoop.username AS whoopen_username, whoup.username AS whoupdate_username, 
        		         whoass.username AS whoassign_username,
                         GROUP_CONCAT(cases_mtm_cv_id.cv_id) AS cv_tag_string
                    FROM casesystem_main
               LEFT JOIN user_main AS whoup
                      ON casesystem_main.whoupdated_id = whoup.user_id
               LEFT JOIN user_main AS whoop
                      ON casesystem_main.whoopen_id = whoop.user_id
               LEFT JOIN user_main AS whoass
                      ON casesystem_main.whoassigned_id = whoass.user_id
               LEFT JOIN cases_mtm_cv_id
                      ON casesystem_main.case_id  = cases_mtm_cv_id.case_id 
                   WHERE closed_yn='NO'
                     AND whoassigned_id = :1: 
                GROUP BY casesystem_main.case_id 
                ORDER BY whenupdated_date";
        $stmt = $dbh->prepare($query);
        $stmt->execute($id);
        $cases = $stmt->fetchall_assoc();
        return $cases;
    }
    public static function getAllCasesWithCVTag($cv_id)
    {   
        $dbh = new DB_Mysql();
        $query = "SELECT *,casesystem_main.case_id ,whoop.username AS whoopen_username, whoup.username AS whoupdate_username, 
        				whoass.username AS whoassign_username,
                         GROUP_CONCAT(cases_mtm_cv_id.cv_id) AS cv_tag_string
                    FROM casesystem_main
               LEFT JOIN cases_mtm_cv_id
                      ON casesystem_main.case_id  = cases_mtm_cv_id.case_id 
               LEFT JOIN user_main AS whoup
                      ON casesystem_main.whoupdated_id = whoup.user_id
               LEFT JOIN user_main AS whoop
                      ON casesystem_main.whoopen_id = whoop.user_id
               LEFT JOIN user_main AS whoass
                      ON casesystem_main.whoassigned_id = whoass.user_id
                      WHERE cases_mtm_cv_id.cv_id = :1:
                GROUP BY casesystem_main.case_id 
                ORDER BY whenupdated_date";
        $stmt = $dbh->prepare($query);
        $stmt->execute($cv_id);
        $cases = $stmt->fetchall_assoc();
        return $cases;
    }
    
    public static function tagWithCVID($case, $cv_id)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("INSERT INTO cases_mtm_cv_id 
                                       SET case_id=:1:, 
                                           cv_id=:2: ");

        try{
             $stmt->execute($case, $cv_id);
        }
        catch(MysqlException $exception)
        {
            throw new CasesystemException("Case already tagged with customer ".$cv_id);
        }
    }
    public static function getAllCVTags($ID)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * FROM cases_mtm_cv_id 
        				   INNER JOIN cv_main 
        				   ON cv_main.cv_id = cases_mtm_cv_id.cv_id
        				   WHERE case_id=:1: ORDER BY cv_name");
        $stmt->execute($ID);
        return $stmt->fetchall_assoc();
    }
    public static function deleteCVTag($case_id, $cv_id)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("DELETE FROM cases_mtm_cv_id 
                                       WHERE case_id=:1:
                                       AND 
                                           cv_id=:2: ");

        $stmt->execute($case_id, $cv_id);
    }
    public static function getAllEntries($ID)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * FROM casesystem_entry WHERE case_id=:1: ");
        $stmt->execute($ID);
        return $stmt->fetchall_assoc();
    }
    
}
?>
