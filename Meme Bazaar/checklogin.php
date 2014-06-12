<?php
if(isset($_POST['username']) && isset($_POST['password']))
{
	include "LIB_project1.php";
	Lib::checkLogin($_POST['username'], $_POST['password']);
}//if
else
{
	include "LIB_project1.php";
}//else
$pageName = "Login";
echo Lib::getHeader($pageName, "default.css");
?>

<?php
echo Lib::getFooter();
?>