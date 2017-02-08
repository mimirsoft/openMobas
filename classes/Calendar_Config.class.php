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
class calendar_config{
    
    private $FONTS;
    private $application_name;
    private $server_url;
    private $default_view;
    private $WEEK_START;
    private $TZOffset;
    

    public function __construct() 
    {
        $this->load_global_settings();
    }
  
    function getDefaultView()
    {
        return $this->default_view;
    }
  
    function getWeekStart()
    {
        return $this->WEEK_START;
    } 
    function getTZOffset()
    {
        return $this->TZOffset;
    } 
    function getFonts()
    {
        return $this->FONTS;
    }

/**
 * Loads default system settings (which can be updated via admin.php).
 *
 * System settings are stored in the webcal_config table.
 *
 * <b>Note:</b> If the setting for <var>server_url</var> is not set, the value
 * will be calculated and stored in the database.
 *
 * @global string User's login name
 * @global bool   Readonly
 * @global string HTTP hostname
 * @global int    Server's port number
 * @global string Request string
 * @global array  Server variables
 */
function load_global_settings () 
{
    $dbh = new DB_Mysql();
    $stmt = $dbh->prepare( "SELECT cal_setting, cal_value FROM calendar_config" );
    $stmt->execute();
    while ( $row =  $stmt->fetch_row()  ) {
      $setting = $row[0];
      $value = $row[1];
      //echo "Setting '$setting' to '$value' <br />\n";
      $this->$setting = $value;
    }

    // If $server_url not set, then calculate one for them, then store it
    // in the database.
    if ( empty ( $this->server_url ) ) 
    {
        if ( ! empty ( $HTTP_HOST ) && ! empty ( $REQUEST_URI ) ) 
        {
            $ptr = strrpos ( $REQUEST_URI, "/" );
            if ( $ptr > 0 ) 
            {
                $uri = substr ( $REQUEST_URI, 0, $ptr + 1 );
                $server_url = "http://" . $HTTP_HOST;
                if ( ! empty ( $SERVER_PORT ) && $SERVER_PORT != 80 )
                {
                    $server_url .= ":" . $SERVER_PORT;
                }
                $server_url .= $uri;
                
                $stmt = $dbh->prepare( "INSERT INTO webcal_config ( cal_setting, cal_value ) ".
                  "VALUES ( 'server_url', '$server_url' )" );
                $stmt->execute();
                $this->server_url = $server_url;
            }
        }
    }

    // If no font settings, then set some
    if ( empty ( $this->FONTS ) ) 
    {
        if ( @$this->LANGUAGE == "Japanese" )
        {
            $this->FONTS = "Osaka, Arial, Helvetica, sans-serif";
        }
        else
        {
            $this->FONTS = "Arial, Helvetica, sans-serif";
        }
    }
    return;
  
}

/**
 * Loads current user's category info and stuff it into category global
 * variable.
 *
 * @param string $ex_global Don't include global categories ('' or '1')
 */
function load_user_categories ($ex_global = '', $user, $login, $is_assistant, $is_admin) {

    $cat_owner =  ( ( ! empty ( $user ) && strlen ( $user ) ) &&  ( $is_assistant  ||
        $is_admin ) ) ? $user : $login;  
    $this->categories = array ();
    $this->category_owners = array ();
    if ( $this->categories_enabled == "Y" ) 
    {
        $sql = "SELECT cat_id, cat_name, cat_owner 
    	          FROM webcal_categories 
    	         WHERE ";
        $sql .=  ($ex_global == '') ? " (cat_owner = '$cat_owner') OR  (cat_owner IS NULL) ORDER BY cat_owner, cat_name" : " cat_owner = '$cat_owner' ORDER BY cat_name";

        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare($sql);
        if ( $stmt->execute() ) 
        {
            while ( $row = $stmt->fetch_row (  ) ) 
            {
                $cat_id = $row[0];
                $this->categories[$cat_id] = $row[1];
                $this->category_owners[$cat_id] = $row[2];
            }
        }
    } else {
    //echo "Categories disabled.";
    }
}



}

?>