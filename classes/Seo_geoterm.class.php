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
class Seo_Geoterm {

	private $seo_geoterm_id;
    private $seo_geoterm_content;
    private $seo_geoterm_title;
    private $seo_geoterm_url;
    private $seo_geoterm;
    
    public function __construct($id="NULL") 
    {
        if($id != "NULL" && $id != "")
        {
	    	$dbh = new DB_Mysql();
			$stmt = $dbh->prepare("SELECT * FROM seo_geoterms WHERE seo_geoterm_id = :1:");
			$stmt->execute($id);
			$row = $stmt->fetch_assoc();
	
	        $this->seo_geoterm_id = $id;
	        $this->seo_geoterm = $row['seo_geoterm'];
	        $this->seo_geoterm_url = $row['seo_geoterm_url'];
	        $this->seo_geoterm_content = $row['seo_geoterm_content'];
	        $this->seo_geoterm_title = $row['seo_geoterm_title'];
        }
        else {
        	
        	$this->seo_geoterm_id = 'NULL';
	        
        }
        
    }

    public function getSeoTermFromURL($url) {
            $dbh = new DB_Mysql();
			$stmt = $dbh->prepare("SELECT * FROM seo_geoterms WHERE seo_geoterm_url = :1:");
			$stmt->execute($url);
			$row = $stmt->fetch_assoc();
	
	        $this->seo_geoterm_id = $row['seo_geoterm_id'];
	        $this->seo_geoterm = $row['seo_geoterm'];
	        $this->seo_geoterm_url = $url;
	        $this->seo_geoterm_content = $row['seo_geoterm_content'];
	        $this->seo_geoterm_title = $row['seo_geoterm_title'];
    }
    public function getSeoGeoterm() {
    	return($this->seo_geoterm);
    }
    public function getSeoGeotermURL() {
    	return($this->seo_geoterm_url);
    }
    public function getSeoGeotermTitle() {
    	return($this->seo_geoterm_title);
    }
    public function getSeoGeotermContent() {
    	return($this->seo_geoterm_content);
    }
    public function setSeoGeotermURL($a) {
    	$this->seo_geoterm_url = $a;
    }
    public function setSeoGeoterm($a) {
    	$this->seo_geoterm = $a;
    }
    public function setSeoGeotermTitle($a) {
    	$this->seo_geoterm_title = $a;
    }
    public function setSeoGeotermContent($a) {
    	$this->seo_geoterm_content = $a;
    }
    
    public function save() {
        
        if($this->seo_geoterm_id == "NULL")
        {
            $this->seo_geoterm_id = $this->create_page();
        }
        else {
            $this->update_page();
            
        }
    	return $this->seo_geoterm_id;
    }
    public function delete() {
        
        if($this->seo_geoterm_id != "NULL" || $this->seo_geoterm_id != "")
        {
            $dbh = new DB_Mysql();
			$stmt = $dbh->prepare("DELETE FROM seo_geoterms WHERE seo_geoterm_id = :1:");
			$stmt->execute($this->seo_geoterm_id);
	
        }
        else {
            return;
        }
    }
        
    
    public function update_page()
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("UPDATE seo_geoterms 
                                       SET seo_geoterm_url=:2:, 
                                           seo_geoterm_title=:3:, 
                                           seo_geoterm_content=:4:,
                                           seo_geoterm=:5: 
                                     WHERE seo_geoterm_id=:1:");
        $stmt->execute($this->seo_geoterm_id, $this->seo_geoterm_url, $this->seo_geoterm_title, $this->seo_geoterm_content, $this->seo_geoterm);
    }
    public function create_page()
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("INSERT INTO seo_geoterms 
                                       SET seo_geoterm_id=:1:, 
                                           seo_geoterm_url=:2:, 
                                           seo_geoterm_title=:3:, 
                                           seo_geoterm_content=:4:,
                                           seo_geoterm=:5:");
        $stmt->execute($this->seo_geoterm_id, $this->seo_geoterm_url, $this->seo_geoterm_title, $this->seo_geoterm_content, $this->seo_geoterm);
        return mysql_insert_id();

    }
    
    public function getAllSeoGeoterms() {
        
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * 
        						 FROM seo_geoterms 
        				     ORDER BY seo_geoterm");
        $stmt->execute();
        $array = $stmt->fetchall_assoc();
        return $array;
    }
    
    
}	
	
?>