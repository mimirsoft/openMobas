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

class Filehandler {

    private $file_id;
    
    public function __construct($file_id) 
    {
        $this->file_id = $file_id;
    }

    public function update_desc($desc)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("UPDATE files_main 
                                  SET fileDesc=:1:
                                WHERE file_id=:2:");
        $stmt->execute($desc, $this->file_id);
    }
    public function get_info()
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * 
                                 FROM files_main 
                                WHERE file_id=:1:");
        $stmt->execute($this->file_id);
        return $stmt->fetch_assoc();
        
    }
    public function delete_file()
    {
        global $DB_SETTINGS;
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("DELETE  
                                 FROM files_main 
                                WHERE file_id=:1:");
        $stmt->execute($this->file_id);
        if (file_exists("../../../files/".$DB_SETTINGS['dbname']."/".$this->file_id ) )
        {
            unlink("../../../files/".$DB_SETTINGS['dbname']."/".$this->file_id);
        }
        if (file_exists("../../thumbnails/".$DB_SETTINGS['dbname']."/".$this->file_id )) 
        {
            unlink("../../thumbnails/".$DB_SETTINGS['dbname']."/".$this->file_id);
        }
    }
    public function log_file_access($user_id, $IP)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("INSERT INTO files_log
                                       SET file_id=:1:,
                                           user_id=:2:,
                                           user_ip=:3:,
                                           access_time=NOW()");
        $stmt->execute($this->file_id, $user_id, $IP);
    }

}

?>
