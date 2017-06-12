<?php
$c=$_GET["c"];
$d=$_GET["d"];

require_once('connect.ini.php');


if($d == "no"){
echo '<a href="javascript:pausep(\'' . $c. '\',\'yes\');"><img src="img/pause.png" width="125px" height="125px" alt="pause"></a></div>';
$conn->query("UPDATE $t_banners SET pause = 'yes' WHERE id = '".$c."'");
}
else
{
	echo '<a href="javascript:pausep(\'' . $c. '\',\'no\');"><img src="img/blank.png" width="125px" height="125px" alt="blank"></a></div>';
$conn->query("UPDATE $t_banners SET pause = 'no' WHERE id = '".$c."'");

}



?>