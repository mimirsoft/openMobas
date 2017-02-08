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

class CV_Settings{

    public static function update_defaults($receivable, $payable)
    {
        
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("DELETE FROM cv_settings");
        $stmt->execute();
        $stmt = $dbh->prepare("INSERT INTO cv_settings
                                        SET default_account_receivable=:1:, 
                                            default_account_payable=:2:");
        $stmt->execute($receivable, $payable);
    }

    public static function get_defaults()
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT * FROM cv_settings");
        $stmt->execute();
        return  $stmt->fetch_assoc();
    }

}



?>
