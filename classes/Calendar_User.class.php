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

class Calendar_User{

    private $default_view;
    private $user_id;
    
    public function __construct($user_id, $cal=NULL) {

      $this->user_id = $user_id;
      $dbh = new DB_Mysql();
      $stmt = $dbh->prepare("SELECT * 
                              FROM calendar_users
                            WHERE user_id = :1: 
                            LIMIT 1");
      $stmt->execute($user_id);
      $row = $stmt->fetch_assoc();
      //$this->name = $row['user_id'];

      //get calender_preferences for $user
      $this->load_user_preferences();
      
  }
  
    function getDefaultView()
    {
        return $this->default_view;
    }
  

/**

 */
function load_user_preferences () {

    //$lang_found = false;
    //$this->browser = $this->get_web_browser ();
    //$this->browser_lang = $this->get_browser_language ();
    // Note: default values are set in config.php
    $dbh = new DB_Mysql();
    $stmt = $dbh->prepare("SELECT cal_setting, cal_value FROM calendar_user_pref " .
    	"WHERE user_id = ".$this->user_id);
    $stmt->execute();
    while ( $row = $stmt->fetch_row() ) 
    {
        $setting = $row[0];
        $value = $row[1];
        $this->$setting = $value;
        if ($setting == "LANGUAGE" )
        {
            $this->lang_found = true;
        }
    }
    // get views for this user and global views
    /*
     * 
     $stmt = $dbh->prepare("SELECT cal_view_id, cal_name, cal_view_type, cal_is_global " .
    						"FROM webcal_view " .
        	               "WHERE user_id = '$login' OR cal_is_global = 'Y' " .
    	                "ORDER BY cal_name");
    $stmt->execute();
    $views = array ();
    while ( $row = $stmt->fetch_row( ) ) 
    {
        if ( $row[2] == 'S' ){
            $url = "view_t.php?timeb=1&amp;id=$row[0]";
        }
        else if ( $row[2] == 'T' )
        {
            $url = "view_t.php?timeb=0&amp;id=$row[0]";
        }else
        {
            $url = "view_" . strtolower ( $row[2] ) . ".php?id=$row[0]";
        }
        $v = array (
            "cal_view_id" => $row[0],
            "cal_name" => $row[1],
            "cal_view_type" => $row[2],
            "cal_is_global" => $row[3],
            "url" => $url
            );
        $views[] = $v;
    }
    // If user has not set a language preference, then use their browser
    // settings to figure it out, and save it in the database for future
    // use (email reminders).
    if ( ! $this->lang_found && strlen ( $login ) && $login != "__public__" ) {
        $this->LANGUAGE = $this->browser_lang;
        $stmt = $dbh->prepare("INSERT INTO webcal_user_pref " .
              "( user_id, cal_setting, cal_value ) VALUES " .
              "( '$login', 'LANGUAGE', '$this->LANGUAGE' )");
        $stmt->execute();  
    }
    */
    if ( empty ( $this->DATE_FORMAT_MY ) )
    {
      $this->DATE_FORMAT_MY = "__month__ __yyyy__";
    }
    if ( empty ($this->DATE_FORMAT_MD ) )
    {
      $this->DATE_FORMAT_MD = "__month__ __dd__";
    }

}

/**
 * Loads current user's layer info into layer global variable.
 *
 * If the system setting <var>$allow_view_other</var> is not set to 'Y', then
 * we ignore all layer functionality.  If <var>$force</var> is 0, we only load
 * layers if the current user preferences have layers turned on.
 *
 * @param string $user  Username of user to load layers for
 * @param int    $force If set to 1, then load layers for this user even if
 *                      user preferences have layers turned off.
 */
function load_user_layers ($user="",$force=0,$login="") {
    if ( $user == "" )
    {
        $user = $login;
    }
    $this->layers = array ();
    if ( empty ( $this->allow_view_other ) || $this->allow_view_other != 'Y' )
    {
        return; // not allowed to view others' calendars, so cannot use layers
    }
    if ( $force || ( ! empty ( $this->LAYERS_STATUS ) && $this->LAYERS_STATUS != "N" ) ) 
    {
        $dbh = new DB_Mysql();
        $stmt = $dbh->prepare("SELECT cal_layerid, cal_layeruser, cal_color, cal_dups " .
      						    "FROM webcal_user_layers " .
                               "WHERE `user_id` = '$user' ORDER BY cal_layerid");
        if ( $stmt->execute() ) 
        {
            $count = 1;
            while ( $row = $stmt->fetch_row (  ) ) 
            {
                $this->layers[$row[0]] = array (
    				"cal_layerid" => $row[0],
                  	"cal_layeruser" => $row[1],
                  	"cal_color" => $row[2],
                  	"cal_dups" => $row[3]
                );
                $count++;
            }
        }
    }else{
        //echo "Not loading!";
    }
}
    


/**
 * Gets a preference setting for the specified user.
 *
 * If no value is found in the database, then the system default setting will
 * be returned.
 *
 * @param string $user    User login we are getting preference for
 * @param string $setting Name of the setting
 *
 * @return string The value found in the webcal_user_pref table for the
 *                specified setting or the sytem default if no user settings
 *                was found.
 */
function get_pref_setting ( $user, $setting ) {
  $ret = '';
  // set default
  if ( ! isset ( $GLOBALS["sys_" .$setting] ) ) {
    // this could happen if the current user has not saved any pref. yet
    if ( ! empty ( $GLOBALS[$setting] ) )
      $ret = $GLOBALS[$setting];
  } else {
    $ret = $GLOBALS["sys_" .$setting];
  }

  $sql = "SELECT cal_value FROM webcal_user_pref " .
    "WHERE user_id = '" . $user . "' AND " .
    "cal_setting = '" . $setting . "'";
  //echo "SQL: $sql <br />\n";
$dbh = new DB_Mysql();
$stmt = $dbh->prepare($sql);
  if ( $stmt->execute() ) {
    if ( $row = $stmt->fetch_row (  ) )
      $ret = $row[0];
  }
  return $ret;
}


}
  
  
?>