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
class Seo_Page {

	private $seo_page_id;
    private $seo_page_content;
    private $seo_page_title;
    private $seo_page_url;
    private $meta_description;
    private $meta_keywords;
    private $dbh;
    private $user;
    private $rbac_user;
    
    public function __construct($id="NULL", $dbh, $user, $rbac_user) 
    {
    	$this->dbh = $dbh;
    	$this->user = $user;
    	$this->rbac_user = $rbac_user;

        if($id != "NULL" || $id != "")
        {
	    	$stmt = $this->dbh->prepare("SELECT * FROM seo_pages WHERE seo_page_id = :1:");
			$stmt->execute($id);
			$row = $stmt->fetch_assoc();
	
	        $this->seo_page_id = $id;
	        $this->seo_page_url = $row['seo_page_url'];
	        $this->seo_page_content = $row['seo_page_content'];
	        $this->seo_page_title = $row['seo_page_title'];
	        $this->meta_description = $row['meta_description'];
	        $this->meta_keywords = $row['meta_keywords'];
        }
        else {
        	
        	$this->seo_page_id = 'NULL';
	        
        }
        
    }

    public function getSeoPageFromURL($url) {
            $stmt = $this->dbh->prepare("SELECT * FROM seo_pages WHERE seo_page_url = :1:");
			$stmt->execute($url);
			$row = $stmt->fetch_assoc();
	
	        $this->seo_page_id = $row['seo_page_id'];
	        $this->seo_page_url = $url;
	        $this->seo_page_content = $row['seo_page_content'];
	        $this->seo_page_title = $row['seo_page_title'];
	        $this->meta_description = $row['meta_description'];
	        $this->meta_keywords = $row['meta_keywords'];
    }
	public function getSeoPageId() {
    	return($this->seo_page_id);
    }
	public function getSeoPageURL() {
    	return($this->seo_page_url);
    }
    public function getSeoPageTitle() {
    	return($this->seo_page_title);
    }
    public function getSeoPageContent() {
    	return($this->seo_page_content);
    }
    public function getMetaDescription() {
    	return($this->meta_description);
    }
    public function getMetaKeywords() {
    	return($this->meta_keywords);
    }
    public function setSeoPageURL($a) {
    	$this->seo_page_url = $a;
    }
    public function setSeoPageTitle($a) {
    	$this->seo_page_title = $a;
    }
    public function setSeoPageContent($a) {
    	$this->seo_page_content = $a;
    }
    public function setMetaDescription($a) {
    	$this->meta_description = $a;
    }
    public function setMetaKeywords($a) {
    	$this->meta_keywords = $a;
    }
    
    public function save() {
        
        if($this->seo_page_id == "NULL")
        {
            $this->seo_page_id = $this->create_page();
        }
        else {
            $this->update_page();
            
        }
    	return $this->seo_page_id;
    }
    public function delete() {
        
        if($this->seo_page_id != "NULL" || $this->seo_page_id != "")
        {
            $stmt = $this->dbh->prepare("DELETE FROM seo_pages WHERE seo_page_id = :1:");
			$stmt->execute($this->seo_page_id);
	
        }
        else {
            return;
        }
    }
        
    
    public function update_page()
    {
        $stmt = $this->dbh->prepare("UPDATE seo_pages 
                                       SET seo_page_url=:2:, 
                                           seo_page_title=:3:, 
                                           seo_page_content=:4:, 
                                           meta_keywords=:5:, 
                                           meta_description=:6: 
                                     WHERE seo_page_id=:1:");
        $stmt->execute($this->seo_page_id, $this->seo_page_url, $this->seo_page_title, $this->seo_page_content, $this->meta_keywords,  $this->meta_description);
    }
    public function create_page()
    {
        $stmt = $this->dbh->prepare("INSERT INTO seo_pages 
                                       SET seo_page_id=:1:, 
                                           seo_page_url=:2:, 
                                           seo_page_title=:3:, 
                                           seo_page_content=:4:, 
                                           meta_keywords=:5:, 
                                           meta_description=:6:");
        $stmt->execute($this->seo_page_id, $this->seo_page_url, $this->seo_page_title, $this->seo_page_content, $this->meta_keywords,  $this->meta_description);
        return $stmt->insert_id();

    }
    
    public function getAllSeoPages() {
        
        $stmt = $this->dbh->prepare("SELECT * 
        						 FROM seo_pages 
        				     ORDER BY seo_page_url");
        $stmt->execute();
        return $stmt->fetchall_assoc();
    }
    
    
}	
	
?>