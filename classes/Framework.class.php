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

class Framework{

    
public  $modules;
public static $colors;
private $dbh;
private $user;
private $rbac_user;

public function __construct($dbh, $user, $rbac_user)
{

	$this->dbh = $dbh;
	$this->user = $user;
	$this->rbac_user = $rbac_user;
	
}

function setModules($a){
    
    $this->modules = $a;
}
function getModules(){
    
    return $this->modules;
}


public function authenticate($objSession, $MODULE_NAME, $USER, $BASE_DIR)
{
    $system_id = 1;
    $statement_object = Framework::get_system($system_id);
    $SYS_INFO = unserialize($statement_object['system_array']);
    if ($USER->IsLoggedIn() != true)//if USER is not logged in....
    {   
        if(isset($_POST['username']) & isset($_POST['passwd']))
        {
            $USER->Login($_POST['username'], $_POST['passwd']);//try to log in.
        }
        if ($USER->IsLoggedIn() != true)//if that fails, boot him out. 
        {
            $currentURL = $_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING'];
            $logged_out_message = $objSession->GetSessionDeath();
            include ("../interface/login.phtml");
            exit;
        }
    }
}

//no longer global, must pass variables
public function external_authenticate($objSession, $MODULE_NAME, $USER, $BASE_DIR, $kickto)//this where we kick on failure
{
    //global $objSession, $MODULE_NAME, $USER, $BASE_DIR;
    if ($USER->IsLoggedIn() != true)//if USER is not logged in....
    {   
        if(isset($_POST['username']) & isset($_POST['passwd']))
        {
            $USER->Login($_POST['username'], $_POST['passwd']);//try to log in.
        }
        if ($USER->IsLoggedIn() != true)//if that fails, boot him out. 
        {
            $currentURL = $_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING'];
            $logged_out_message = $objSession->GetSessionDeath();
            header ("Location:".$kickto."?currentURL=$currentURL");
            exit;
        }
    }
}


public function XML_authenticate($objSession, $MODULE_NAME, $USER)//this is the minimum permission level needed.
{
    if ($USER->IsLoggedIn() != true)//if USER is not logged in....
    {   
        if(isset($_POST['user']) & isset($_POST['passwd']))
        {
            $USER->Login($_POST['user'], $_POST['passwd']);//try to log in.
        }
        if ($USER->IsLoggedIn() != true)//if that fails, boot him out. 
        {
            header('Content-Type: text/xml');
            echo "<login>FAILED</login>";
            exit;
        }
    }
    /*if ($USER->IsLoggedIn() == true)//if USER IS logged in.... this won't work, because we don't want it to 100% kick back the login
    {
        header('Content-Type: text/xml');
        echo "<login>TRUE</login>";
        exit;
    }
    */
}


public function get_system($id=1)
{
    $stmt = $this->dbh->prepare("SELECT * FROM admin_system WHERE system_id=:1:");
    $stmt->execute($id);	
    $statement_object = $stmt->fetch_assoc();
    
    return $statement_object;
}


public function navbar($MODULE_NAME, $BASE_DIR, $USER)
{
    $user_ID = $USER->GetUserID();
    
    ?> 

<script language="JavaScript" type="text/javascript">
    qmu=true;
    var qm_li;
    var qm_lo;
    var qm_tt;
    var qp="parentNode";
    var qc="className";;
    function qm_create(sd,v,l)
    {
        if(!l)
        {
            l=1;sd=document.getElementById("qm"+sd);
            sd.onmouseover=function(e){x6(e)};
            document.onmouseover=x2;sd.style.zoom=1;
        }
        sd.style.zIndex=l;
        var lsp;
        var sp=sd.childNodes;
        for(var i=0;i<sp.length;i++)
        {
            var b=sp[i];
            if(b.tagName=="A")
            {
                lsp=b;
                b.onmouseover=x0;
                if(l==1&&v)
                {
                    b.style.styleFloat="none";
                    b.style.cssFloat="none";
                }
            }
            if(sp[i].tagName=="DIV")
            {
            	if(window.showHelp&&!window.XMLHttpRequest)
            	sp[i].insertAdjacentHTML("afterBegin","<span style='display:block;font-size:1px;height:0px;width:0px;visibility:hidden;'></span>");
            	x5("qmparent",lsp,1);
            	lsp.cdiv=b;
            	b.idiv=lsp;new qm_create(b,null,l+1);
        	}
        }
	}
	function x4(a,b)
	{
		return String.fromCharCode(a.charCodeAt(0)-1-(b-(parseInt(b/4)*4)));
	}
	function x2(e)
	{
		if(qm_li&&!qm_tt)qm_tt=setTimeout("x3()",500);
	}
	function x3()
	{
		var a;
		if((a=qm_li))
		{	
			do{x1(a);}while((a=a[qp])&&!qm_a(a))
		}
		qm_li=null;
	}
	function qm_a(a)
	{
		if(a[qc].indexOf("qmmc")+1)return 1;
	};
	function x1(a)
	{	
		a.style.display="none";x5("qmactive",a.idiv);
	}
	eval("jh#,\"ylreqz2rox$'(#,xkqhpy1ppedxjqq/#$,2uqOsxguGbuh,*0lreg{Sg*%luvs>#+.5*coisv+&RwlglOhrv\"pytv#ff\"syseketgg$gqu$jpwisphx!wvi/\"#,xyz2prhrdwei/erq*$,?".replace(/./g,x4));;
	function x0(e)
	{
		if(qm_tt)
		{
			clearTimeout(qm_tt);
			qm_tt=null;
		}
		var a=this;
		var go=true;
		while((a=a[qp])&&!qm_a(a))
		{
			if(a==qm_li)
			go=false;
		}
		if(qm_li&&go)
		{	
			a=this;
			if((!a.cdiv)||(a.cdiv&&a.cdiv!=qm_li))
			x1(qm_li);
			a=qm_li;
			while((a=a[qp])&&!qm_a(a))
			{
				if(a!=this[qp])
				x1(a);
				else break;
			}
		}
		var b=this;
		if(b.cdiv)
		{
			var aw=b.offsetWidth;
			var ah=b.offsetHeight;
			var ax=b.offsetLeft;
			var ay=b.offsetTop;
			if(qm_a(b[qp])&&b.style.styleFloat!="none"&&b.style.cssFloat!="none")
				aw=0;
			else ah=0;
			b.cdiv.style.left=(ax+aw)+"px";
			b.cdiv.style.top=(ay+ah)+"px";
			x5("qmactive",this,1);
			b.cdiv.style.display="block";
			qm_li=b.cdiv;
		}else  
		if(b[qp].className!="qmmc")
		qm_li=b[qp];x6(e);
	};
	function x5(name,b,add)
	{
		var a=b[qc];
		if(add)
		{
			if(a.indexOf(name)==-1)
			b[qc]+=(a?' ':'')+name;
		}else {
			b[qc]=a.replace(" "+name,"");
			b[qc]=b[qc].replace(name,"");
		}
	};
	function x6(e)
	{
		if(!e)e=event;e.cancelBubble=true;if(e.stopPropagation)e.stopPropagation();
	}
</script>
    <div id="qm0" class="qmmc">
    <a class="#">MODULES</a>
        <div>
        <?php
            foreach ($this->modules as $regMod)
            {   //We include the @ to suppress the errors for modules installed but not registered.
                if ($this->rbac_user->IsAllowedTo($USER->GetUserID(), "access_module", $regMod['mod_code']."_module"))
                {
                    if(isset($regMod['entry_script'])) 
                    {
                        
                        echo "<a class=\"#\" HREF=\"", $BASE_DIR,"/", $regMod['mod_code'], "/", $regMod['entry_script'], "\"><B>", strtoupper($regMod['mod_title']), "</B></a>"; 
                        echo "<div>";
        				include("../{$regMod['mod_code']}/{$regMod['mod_code']}_navbar.phtml");
        				echo "</div>";
                    	
                    }
                    else{
                    echo "<a class=\"#\" HREF=\"", $BASE_DIR,"/", $regMod['mod_code'], "/", $regMod['mod_code'], "_main.php\"><B>", strtoupper($regMod['mod_title']), "</B></a>";
                        echo "<div>";
        				include("../{$regMod['mod_code']}/{$regMod['mod_code']}_navbar.phtml");
        				echo "</div>";
                    
                    }
                }
            }
            ?>
        </div>    
        <a class="#">LOCAL</a>
        <div>
            <?php include("{$MODULE_NAME}_navbar.phtml");?>
        </div>
        <a class="#">ABOUT</a> 
        <div>
            Powered by <BR />
            EphemeralTech Inc<BR />
            NetPropMan Suite<BR />
        <a href="www.ephemeraltech.com">www.ephemeraltech.com</a>
        </div>
        <a HREF="<?php echo $BASE_DIR ?>/interface/logout.php"><B>LOG OUT <?php echo $USER->GetUsername()?></B></a>
    <!--%%%%%%%%%%%% QuickMenu Create Menu (false=horizontal true=veritcal) %%%%%%%%%%%*-->
        <script type="text/JavaScript">qm_create(0,false)</script>
</div>
      <div class="menuspacer" >&nbsp;</div>
      
     <?php
  

}


public function warning($WARNING)
{
      if($WARNING['show'])
        {
            ?><script language="JavaScript" type="text/javascript">
                function hidetip2(){
                var popUp;
                
                popUp = document.getElementById("warning_box");
                popUp.style.visibility="hidden";
                }

            </script>
            <div class="warning_box" id="warning_box">
                <div id='subad'>
                    <div style='width:max;text-align:right;clear:both;'><a href='javascript:hidetip2();' style='color:red;font-size:10pt;'><b>CLOSE</b>[X]</a>
                    </div>
                    <?php echo $WARNING['message']?>
                </div>
            </div>
            <?php

        }

}

public function cases_assigned($user_id)
{
    $query = "SELECT COUNT(*) FROM casesystem_main 
    WHERE closed_yn='NO'
    AND whoassigned_id = :1:";
    $stmt = $this->dbh->prepare($query);
    $stmt->execute($user_id);
    $cases = $stmt->fetch_assoc();
    return $cases['COUNT(*)'];
}

static function XML_Replace($string)
{
    return str_replace ( array ( '&', '"', "'", '<', '>' ), array ( '&amp;' , '&quot;', '&apos;' , '&lt;' , '&gt;' ), $string ); 
}

function Javascript_Escape($string)
{
    $string = str_replace('"', '\"', $string);
    return $string;
}


function mysql_enum_values($tableName,$fieldName)
{
    $result = my_db_query("DESCRIBE $tableName");
//then loop:
    while($row = framework::my_db_fetcharray($result))
    {
//# row is mysql type, in format "int(11) unsigned zerofill"
//# or "enum('cheese','salmon')" etc.

        ereg('^([^ (]+)(\((.+)\))?([ ](.+))?$',$row['Type'],$fieldTypeSplit);
//# split type up into array
        $ret_fieldName = $row['Field'];
        $fieldType = $fieldTypeSplit[1];// eg 'int' for integer.
        $fieldFlags = $fieldTypeSplit[5]; // eg 'binary' or 'unsigned zerofill'.
        $fieldLen = $fieldTypeSplit[3]; // eg 11, or 'cheese','salmon' for enum.
        if (($fieldType=='enum' || $fieldType=='set') && ($ret_fieldName==$fieldName) )
        {
            $fieldOptions = split("','",substr($fieldLen,1,-1));
            return $fieldOptions;
        }
    }
//if the function makes it this far, then it either
//did not find an enum/set field type, or it
//failed to find the the fieldname, so exit FALSE!
return FALSE;
}

public function numeral_to_word($num)
{
    $String = '';
    if($num == "1")
    {
        $String = "ONE ";
    }
    if($num == "2")
    {
        $String = "TWO ";
    }
    if($num == "3")
    {
        $String = "THREE ";
    }
    if($num == "4")
    {
        $String = "FOUR ";
    }
    if($num == "5")
    {
        $String = "FIVE ";
    }
    if($num == "6")
    {
        $String = "SIX ";
    }
    if($num == "7")
    {
        $String = "SEVEN ";
    }
    if($num == "8")
    {
        $String = "EIGHT ";
    }
    if($num == "9")
    {
        $String = "NINE ";
    }
    return $String;

}

public function tens_to_word($num, $ones)
{
    $String = '';
    if($num == "1")
    {
        if($ones == "0")
        {
            $String = "TEN ";
        }
        if($ones == "1")
        {
            $String = "ELEVEN ";
        }
        if($ones == "2")
        {
            $String = "TWELVE ";
        }
        if($ones == "3")
        {
            $String = "THIRTEEN ";
        }
        if($ones == "4")
        {
            $String = "FOURTEEN ";
        }
        if($ones == "5")
        {
            $String = "FIFTEEN ";
        }
        if($ones == "6")
        {
            $String = "SIXTEEN ";
        }
        if($ones == "7")
        {
            $String = "SEVENTEEN ";
        }
        if($ones == "8")
        {
            $String = "EIGHTTEEN ";
        }
        if($ones == "9")
        {
            $String = "NINETEEN ";
        }
    }
    if($num == "2")
    {
        $String = "TWENTY ";
    }
    if($num == "3")
    {
        $String = "THIRTY ";
    }
    if($num == "4")
    {
        $String = "FORTY ";
    }
    if($num == "5")
    {
        $String = "FIFTY ";
    }
    if($num == "6")
    {
        $String = "SIXTY ";
    }
    if($num == "7")
    {
        $String = "SEVENTY ";
    }
    if($num == "8")
    {
        $String = "EIGHTY ";
    }
    if($num == "9")
    {
        $String = "NINETY ";
    }
    return $String;

}
public function date_selectors($date_year, $date_month, $date_day, $name, $name2)
{
   echo "<input type=\"TEXT\" SIZE=\"3\" onchange=\"check_date(event, 'year')\" MAXLENGTH =\"4\" name=\"".$name."date_year".$name2."\" id=\"".$name."date_year".$name2."\" value=\"".$date_year."\">\n";
   echo "<input type=\"TEXT\" SIZE=\"1\" onchange=\"check_date(event, 'month')\" MAXLENGTH =\"2\" name=\"".$name."date_month".$name2."\"id=\"".$name."date_month".$name2."\" value=\"".$date_month."\">\n";
   echo "<input type=\"TEXT\" SIZE=\"1\" onchange=\"check_date(event, 'day')\" MAXLENGTH =\"2\" name=\"".$name."date_day".$name2."\" id=\"".$name."date_day".$name2."\" value=\"".$date_day."\">\n";

}
function br2nl($text)
{
    return  preg_replace('/<br\\s*?\/??>/i', '', $text);
}

function createRandomPassword() {
    $chars = "abcdefghijkmnopqrstuvwxyz023456789";
    srand((double)microtime()*1000000);
    $i = 0;
    $pass = '' ;
    while ($i <= 7) {
        $num = rand() % 33;
        $tmp = substr($chars, $num, 1);
        $pass = $pass . $tmp;
        $i++;
    }
    return $pass;
}

function getUserArray()
{
    $stmt = $this->dbh->prepare("SELECT * 
                            FROM user_main");
    $stmt->execute();
    while($dbRow = $stmt->fetch_assoc())
    {
        $userArray[$dbRow['user_id']] = $dbRow;
    }
    return $userArray;
}
/**
 * Convert an IP address from presentation to decimal(39,0) format suitable for storage in MySQL
 *
 * @param string $ip_address An IP address in IPv4, IPv6 or decimal notation
 * @return string The IP address in decimal notation
 */
static function inet_ptod($ip_address)
{
    // IPv4 address
    if (strpos($ip_address, ':') === false && strpos($ip_address, '.') !== false) {
        $ip_address = '::' . $ip_address;
    }

    // IPv6 address
    if (strpos($ip_address, ':') !== false) {
        $network = inet_pton($ip_address);
        $parts = unpack('N*', $network);

        foreach ($parts as &$part) {
                if ($part < 0) {
                        $part = bcadd((string) $part, '4294967296');
                }

                if (!is_string($part)) {
                        $part = (string) $part;
                }
        }

        $decimal = $parts[4];
        $decimal = bcadd($decimal, bcmul($parts[3], '4294967296'));
        $decimal = bcadd($decimal, bcmul($parts[2], '18446744073709551616'));
        $decimal = bcadd($decimal, bcmul($parts[1], '79228162514264337593543950336'));

        return $decimal;
    }

    // Decimal address
    return $ip_address;
}

/**
 * Convert an IP address from decimal format to presentation format
 *
 * @param string $decimal An IP address in IPv4, IPv6 or decimal notation
 * @return string The IP address in presentation format
 */
static function inet_dtop($decimal)
{
    // IPv4 or IPv6 format
    if (strpos($decimal, ':') !== false || strpos($decimal, '.') !== false) {
        return $decimal;
    }

    // Decimal format
    $parts = array();
    $parts[1] = bcdiv($decimal, '79228162514264337593543950336', 0);
    $decimal = bcsub($decimal, bcmul($parts[1], '79228162514264337593543950336'));
    $parts[2] = bcdiv($decimal, '18446744073709551616', 0);
    $decimal = bcsub($decimal, bcmul($parts[2], '18446744073709551616'));
    $parts[3] = bcdiv($decimal, '4294967296', 0);
    $decimal = bcsub($decimal, bcmul($parts[3], '4294967296'));
    $parts[4] = $decimal;

    foreach ($parts as &$part) {
        if (bccomp($part, '2147483647') == 1) {
                $part = bcsub($part, '4294967296');
        }

        $part = (int) $part;
    }

    $network = pack('N4', $parts[1], $parts[2], $parts[3], $parts[4]);
    $ip_address = inet_ntop($network);

    // Turn IPv6 to IPv4 if it's IPv4
    if (preg_match('/^::\d+.\d+.\d+.\d+$/', $ip_address)) {
        return substr($ip_address, 2);
    }

    return $ip_address;
}

    function do_redirect ( $url ) 
    {
        // Replace any '&amp;' with '&' since we don't want that in the HTTP
        //header.
        $url = str_replace ( '&amp;', '&', $url );
        Header ( "Location: $url" );
        echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n<!DOCTYPE html
        PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\"
        \"DTD/xhtml1-transitional.dtd\">
    	<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"en\" lang=\"en\">
    	<head>\n<title>Redirect</title>\n</head>\n<body>\n" .
		"Redirecting to ... <a href=\"" . $url . "\">here</a>.</body>\n</html>";
        exit;
    }
/**
 * Prints the HTML header and opening HTML body tag.
 * @param string $HeadX        Data to be printed inside the head tag (meta,
 * @param string $BodyX        Data to be printed inside the Body tag (onload
 *                             for example)
 */
function print_header($HeadX = '', $BodyX = '') {
    $lang = '';
    if ( ! empty ( $this->LANGUAGE ) )
    {
        $lang = Framework_helper::languageToAbbrev ( $this->LANGUAGE );
    }
    if ( empty ( $lang ) )
    {
        $lang = 'en';
    }
    // Start the header & specify the charset
    // The charset is defined in the translation file
    if ( ! empty ( $this->LANGUAGE ) ) 
    {
        $charset = translate ( "charset" );
        if ( $charset != "charset" ) 
        {
            echo "<?xml version=\"1.0\" encoding=\"$charset\"?>\n" .
                "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" " .
                "\"DTD/xhtml1-transitional.dtd\">\n" .
                "<html xmlns=\"http://www.w3.org/1999/xhtml\" " .
                "xml:lang=\"$lang\" lang=\"$lang\">\n" .
                "<head>\n" .
                "<meta http-equiv=\"Content-Type\" content=\"text/html; " .
                "charset=$charset\" />\n";
            echo "<title>".translate($this->application_name)."</title>\n";
        } else {
            echo "<?xml version=\"1.0\" encoding=\"iso-8859-1\"?>\n" .
            "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" " .
            "\"DTD/xhtml1-transitional.dtd\">\n" .
            "<html xmlns=\"http://www.w3.org/1999/xhtml\" " .
            "xml:lang=\"en\" lang=\"en\">\n" .
            "<head>\n" .
            "<title>".translate($application_name)."</title>\n";
        }
    }
    // Do we need anything else inside the header tag?
    if ($HeadX)
    {
        echo $HeadX."\n";
    }

    
}


    
    
/**
 * Print fatal error message to the user along with a link to a link
 * to a reference guide
 * 
 * Execution is aborted.
 *
 * @param string $error The error message to display
 * @param string $url The url to refer to
 *
 */
function die_badly( $error, $url )
{
  echo "<html><head><title>Framework: Fatal Error</title></head>\n" .
    "<body><h2>Error</h2>\n" .
    "<p>$error</p>\n<hr />" .
    "<p><a href=\"$url\" target=\"_blank\">" .
    "Troubleshooting Help</a></p></body></html>\n";
  exit;
}

    
/**
 * Gets the value resulting from an HTTP POST method.
 * 
 * <b>Note:</b> The return value will be affected by the value of
 * <var>magic_quotes_gpc</var> in the php.ini file.
 * 
 * @param string $name Name used in the HTML form
 *
 * @return string The value used in the HTML form
 *
 * @see getGetValue
 */
function getPostValue ( $name) {
      
    if ( isset ( $_POST ) && is_array ( $_POST ) && ! empty ( $_POST[$name] ) ) 
    {
        return $_POST[$name];
    }
	return null;
}

/**
 * Gets the value resulting from an HTTP GET method.
 *
 * <b>Note:</b> The return value will be affected by the value of
 * <var>magic_quotes_gpc</var> in the php.ini file.
 *
 * If you need to enforce a specific input format (such as numeric input), then
 * use the {@link getValue()} function.
 *
 * @param string $name Name used in the HTML form or found in the URL
 *
 * @return string The value used in the HTML form (or URL)
 *
 * @see getPostValue
 */
function getGetValue ( $name ) {

    if ( isset ( $_GET ) && is_array ( $_GET ) && ! empty ( $_GET[$name] ) ) 
    {
        return $_GET[$name];
    }
    return null;
}

/**
 * Gets the value resulting from either HTTP GET method or HTTP POST method.
 *
 * <b>Note:</b> The return value will be affected by the value of
 * <var>magic_quotes_gpc</var> in the php.ini file.
 *
 * <b>Note:</b> If you need to get an integer value, yuou can use the
 * getIntValue function.
 *
 * @param string $name   Name used in the HTML form or found in the URL
 * @param string $format A regular expression format that the input must match.
 *                       If the input does not match, an empty string is
 *                       returned and a warning is sent to the browser.  If The
 *                       <var>$fatal</var> parameter is true, then execution
 *                       will also stop when the input does not match the
 *                       format.
 * @param bool   $fatal  Is it considered a fatal error requiring execution to
 *                       stop if the value retrieved does not match the format
 *                       regular expression?
 *
 * @return string The value used in the HTML form (or URL)
 *
 * @uses getGetValue
 * @uses getPostValue
 */
function getValue ( $name, $format="", $fatal=false ) {
    $val = Framework::getPostValue ( $name);
    if ( ! isset ( $val ) )
    {
        $val = Framework::getGetValue ( $name);
    }
    if ( ! isset ( $val  ) )
    {
        return "";
    }
    if ( ! empty ( $format ) && ! preg_match ( "/^" . $format . "$/", $val ) ) 
    {
        // does not match
        if ( $fatal ) 
        {
            Framework::die_badly ( "Fatal Error: Invalid data format for $name" );
        }
        // ignore value
        return "";
    }
    return $val;
}




}

?>