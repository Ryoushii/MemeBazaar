<?php
$pageName = "Login";
include "LIB_project1.php";
echo Lib::getHeader($pageName, "default.css");
?>
<div class="login">

	<form name="login" method="post" action="checklogin.php">
		<table>
		<tr>
			<td><strong>Login</strong></td>
		</tr>
		<tr>
			<td>Username</td>
			<td><input name="username" type="text" id="username" /></td>
		</tr>
		<tr>
			<td>Password</td>
			<td><input name="password" type="password" id="password" /></td>
		</tr>
		<tr>
			<td><input type="submit" name="Submit" value="Login" /></td>
		</tr>
		</table>
	</form>
</div>
<?php
echo Lib::getFooter();
?>