<?php
$pageName = "Home";
include "LIB_project1.php";
echo Lib::getHeader($pageName, "default.css");
?>
<div class="items">
	<h1>Welcome to Meme Bazaar, your one-stop shop for Memes!</h1>
	<p>Click on the Products tab in the menu to browse are wide selection of Memes!</p>
</div>
<?php
echo Lib::getFooter();
?>