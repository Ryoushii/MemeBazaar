<?php
session_start();
session_unset();
session_destroy();
header("Refresh: 5; URL=index.php");
$pageName = "Logout";
include "LIB_project1.php";
echo Lib::getHeader($pageName, "default.css");
?>
<div>
	<p>Logged out.  Redirecting...</p>
</div>
<?php
echo Lib::getFooter();
?>

