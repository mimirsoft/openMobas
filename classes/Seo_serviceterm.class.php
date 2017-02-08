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
class Seo_Serviceterm {

	private $seo_serviceterm_id;
    private $seo_serviceterm_content;
    private $seo_serviceterm_title;
    private $seo_serviceterm_url;
    private $seo_serviceterm;
    
    public function __construct($id="NULL") 
    {
        if($id != "NULL" || $id != "")
        {
	    	$dbh = new DB_Mysql();
			$stmt = $dbh->prepare("SELECT * FROM seo_serviceterms WHERE seo_serviceterm_id = :1:");
			$stmt->execute($id);
			$row = $stmt->fetch_assoc();
	
	        $this->seo_serviceterm_id = $id;
	        $this->seo_serviceterm = $row['seo_serviceterm'];
	        $this->seo_serviceterm_url = $row['seo_serviceterm_url'];
	        $this->seo_serviceterm_content = $row['seo_serviceterm_content'];
	        $this->seo_serviceterm_title = $row['seo_serviceterm_title'];
        }
        else {
        	
        	$this->seo_serviceterm_id = 'NULL';
	        
        }
        
    }

    public function getSeoTermFromURL($url) {
            $dbh = new DB_Mysql();
			$stmt = $dbh->prepare("SELECT * FROM seo_serviceterms WHERE seo_serviceterm_url = :1:");
			$stmt->execute($url);
			$row = $stmt->fetch_assoc();
	
	        $this->seo_serviceterm_id = $row['seo_serviceterm_id'];
	        $this->seo_serviceterm = $row['seo_serviceterm'];
	        $this->seo_serviceterm_url = $url;
	        $this->seo_serviceterm_content = $row['seo_serviceterm_content'];
	        $this->seo_serviceterm_title = $row['seo_serviceterm_title'];
    }
    public function getSeoServiceterm() {
    	return($this->seo_serviceterm);
    }
    public function getSeoServicetermURL() {
    	return($this->seo_serviceterm_url);
    }
    public function getSeoServicetermTitle() {
    	return($this->seo_serviceterm_title);
    }
    public function getSeoServicetermContent() {
    	return($this->seo_serviceterm_content);
    }
    public function setSeoServicetermURL($a) {
    	$this->seo_serviceterm_url = $a;
    }
    public function setSeoServiceterm($a) {
    	$this->seo_serviceterm = $a;
    }
    public function setSeoServicetermTitle($a) {
    	$this->seo_serviceterm_title = $a;
    }
    public function setSeoServicetermContent($a) {
    	$this->seo_serviceterm_content = $a;
    }
    
    public function save() {
        
        if($this->seo_serviceterm_id == "NULL")
        {
            $this->seo_serviceterm_id = $this->create_page();
        }
        else {
            $this->update_page();
            
        }
    	return $this->seo_serviceterm_id;
    }
    public function delete() {
        
        if($this->seo_serviceterm_id != "NULL" || $this->seo_serviceterm_id != "")
        {
            $dbh = new DB_Mysql();
			$stmt = $dbh->prepare("DELETE FROM seo_serviceterms WHERE seo_serviceterm_id = :1:");
			$stmt->execute($this->seo_serviceterm_id);
	
        }
        else {
            return;
        }
    }
        
    
    public function update_page()
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("UPDATE seo_serviceterms 
                                       SET seo_serviceterm_url=:2:, 
                                           seo_serviceterm_title=:3:, 
                                           seo_serviceterm_content=:4:,
                                           seo_serviceterm=:5: 
                                     WHERE seo_serviceterm_id=:1:");
        $stmt->execute($this->seo_serviceterm_id, $this->seo_serviceterm_url, $this->seo_serviceterm_title, $this->seo_serviceterm_content, $this->seo_serviceterm);
    }
    public function create_page()
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("INSERT INTO seo_serviceterms 
                                       SET seo_serviceterm_id=:1:, 
                                           seo_serviceterm_url=:2:, 
                                           seo_serviceterm_title=:3:, 
                                           seo_serviceterm_content=:4:,
                                           seo_serviceterm=:5:");
        $stmt->execute($this->seo_serviceterm_id, $this->seo_serviceterm_url, $this->seo_serviceterm_title, $this->seo_serviceterm_content, $this->seo_serviceterm);
        return mysql_insert_id();

    }
    
    public function getAllSeoServiceterms() {
        
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * 
        						 FROM seo_serviceterms 
        				     ORDER BY seo_serviceterm");
        $stmt->execute();
        return $stmt->fetchall_assoc();
    }
    
    
}	
	
?>