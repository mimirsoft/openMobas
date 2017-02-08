<?php
/*
 * 
    This file is part of openMobas
    Copyright (C) 2011, Kevin Milhoan

    openMobas is free software: you can redistribute it and/or modify
    it under the terms of the GNU Affero General Public License as
    published by the Free Software Foundation, either version 3 of the
    License, or (at your option) any later version.

    openMobas is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU Affero General Public License for more details.

    You should have received a copy of the GNU Affero General Public License
    along with WebPropMan.  If not, see <http://www.gnu.org/licenses/>.

   Contact MimirSoft at mimirsoft@gmail.com or www.mimirsoft.com

*
*/

class CV_Category{
    public static function create_cv_cat($name)
    {
        $dbh = new DB_Mysql();
             $stmt = $dbh->prepare("INSERT INTO cv_category 
                                           SET cv_category_name=:1:");
        $stmt->execute($name);
    }
    public static function update_cv_cat($ID, $name)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("UPDATE cv_category 
                                      SET cv_category_name=:2:
                                    WHERE cv_category_id=:1:");
        $stmt->execute($ID, $name);
    }
    public static function get_all()
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * FROM cv_category ORDER BY cv_category_name  ");
        $stmt->execute();
        return $stmt->fetchall_assoc();
    }
    public static function get_cv_cat($id)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * FROM cv_category WHERE cv_category_id=:1: ");
        $stmt->execute($id);
        return $stmt->fetch_assoc();
    }
    public static function get_cvtag_to_name_array()
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * FROM cv_category ORDER BY cv_category_name  ");
        $stmt->execute();
        $set = $stmt->fetchall_assoc();
        $idarray = array();
        foreach($set as $row)
        {
                $idarray[$row['cv_category_id']]        = $row['cv_category_name'];
        }
        return $idarray;
        
    }
    public static function getCvCatByName($id)
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * FROM cv_category WHERE cv_category_name=:1: ");
        $stmt->execute($id);
        return $stmt->fetch_assoc();
    }
    
}
?>