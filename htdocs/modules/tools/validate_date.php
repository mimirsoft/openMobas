<?

$target = $_GET['target'];
$type = $_GET['type'];
$num = $_GET['num'];
settype($num, "integer");
switch($type)
{
    case "year":
        if(($num < 1950) || ($num > 2050))
        {
            $num = date('Y');
            break;
        }
        break;
    case "month":
        if(($num < 10) && ($num > 0))
        {
            $num = "0".$num;
        }
        if(($num < 1) || ($num > 12))
        {
            $num = date('m');
        }
        break;
    case "day":
        if(($num < 10) && ($num > 0))
        {
            $num = "0".$num;
        }
        if(($num < 1) || ($num > 31))
        {
            $num = date('m');
        }
        break;
        
}
header('Content-Type: text/xml');
echo "<doc>
        <target>".$target."</target>
        <num>".$num."</num>
     </doc>";

?>
