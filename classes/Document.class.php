<?
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
require_once("Filehandler.class.php");

class Document{

    // This is the function to add or update a property
    
    public static function add_document_category($ID, $name)
    {
        $dbh = new DB_Mysql();
        if($ID == "NULL")
        {
            $stmt = $dbh->prepare("INSERT INTO documents_categories 
                                           SET category_id=:1:, 
                                               category_name=:2:");
        }
        else//if($ID != "NULL")
        {
            $stmt = $dbh->prepare("UPDATE documents_categories 
                                      SET category_name=:2:
                                    WHERE category_id=:1:");
        }
        $stmt->execute($ID, $name);
        if($ID == 'NULL')
        {
            $ID = mysql_insert_id();
        }
        return $ID;
    }
    function deleteDocumentCategory($ID)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("DELETE 
                                FROM documents_categories WHERE category_id=:1:");
        $stmt->execute($ID);
        return;
    }
    function getall_document_categories()
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * 
                                FROM documents_categories");
        $stmt->execute();
        $cat = $stmt->fetchall_assoc();
        return $cat;
    }
    function getall_active_document_categories()
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * 
                                FROM documents_categories
                                WHERE is_active !=0
                                ORDER BY category_priority DESC
                                ");
        $stmt->execute();
        $cat = $stmt->fetchall_assoc();
        return $cat;
    }
    function set_document_priority($ID, $name)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("UPDATE documents_categories 
                                      SET category_priority=:2:
                                    WHERE category_id=:1:");
        $stmt->execute($ID, $name);
        return;
    }
    function set_document_active($ID, $name)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("UPDATE documents_categories 
                                      SET is_active=:2:
                                    WHERE category_id=:1:");
        $stmt->execute($ID, $name);
        return;
    }
    function getall_documents($asc_desc='DESC')
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * 
                                FROM documents_main
                          INNER JOIN files_main
                                   ON documents_main.file_id=files_main.file_id
                                   ORDER BY document_id $asc_desc");
        $stmt->execute();
        $cat = $stmt->fetchall_assoc();
        return $cat;
    }
    function get_document($id)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * 
                                FROM documents_main
                          INNER JOIN files_main
                                   ON documents_main.file_id=files_main.file_id
                            WHERE documents_main.document_id=:1:");
        $stmt->execute($id);
        $doc = $stmt->fetch_assoc();
        return $doc;
    }
    function get_document_cat_by_id($ID)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * 
                                FROM documents_categories
                                WHERE category_id=:1:");
        $stmt->execute($ID);
        $cat = $stmt->fetch_assoc();
        return $cat;
    }
    function linkfile($ID, $date, $file, $doc_name)
    {
        $dbh = new DB_Mysql();
        if($ID == "NULL")
        {
            $stmt = $dbh->prepare("INSERT INTO documents_main 
                                           SET document_id=:1:, 
                                               document_date=:2:, 
                                               document_name=:3:, 
                                               file_id=:4:");
        }
        else//If it is an edit to an existing entry
        {
            $stmt = $dbh->prepare("UPDATE documents_main 
                                      SET document_date=:2:, 
                                          document_name=:3:, 
                                          file_id=:4: 
                                    WHERE document_id=:1:");
        }
        $stmt->execute($ID, $date, $doc_name, $file);
    }
    function update_document($ID, $doc_name, $date, $document_security)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("UPDATE documents_main 
                                      SET document_date=:3:, 
                                          document_security=:4:,
                                          document_name=:2:
                                    WHERE document_id=:1:");
        $stmt->execute($ID, $doc_name, $date, $document_security);
    }
    function tag_document($doc_id, $cat_id)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("INSERT INTO documents_multi
                                      SET category_id=:2:,
                                         document_id=:1:");
        $stmt->execute($doc_id, $cat_id);
    }
	public static function delete_tag($doc_id, $tag_id)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("DELETE FROM documents_multi 
                                       WHERE category_id=:1:
                                       AND 
                                           document_id=:2: ");

        $stmt->execute($tag_id, $doc_id);
    }   
    function getall_document_tags($doc_id)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * 
                                 FROM documents_multi
                          INNER JOIN  documents_categories
                                   ON documents_categories.category_id = documents_multi.category_id
                                WHERE document_id=:1:");
        $stmt->execute($doc_id);
        $cat = $stmt->fetchall_assoc();
        return $cat;
    }
    function getall_documents_with_tag($cat_id)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * 
                                 FROM documents_multi
                          INNER JOIN  documents_main
                                   ON documents_main.document_id = documents_multi.document_id
                                WHERE documents_multi.category_id=:1:");
        $stmt->execute($cat_id);
        $cat = $stmt->fetchall_assoc();
        return $cat;
    }
    function delete_document($ID)
    {
        $doc = documents::get_document($ID);
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("DELETE FROM documents_main 
                                    WHERE document_id=:1:");
        $stmt->execute($ID);
        $file = new filehandler($doc['file_id']);
        $file->delete_file();    
    }
    
}
?>
