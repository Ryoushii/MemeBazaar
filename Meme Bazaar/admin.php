<?php
session_start();
//checks to see if user is logged in and redirects to login if not
if(!isset($_SESSION['username']))
{
	header("location:login.php");
}//if
$pageName = "Admin";
include "LIB_project1.php";
echo Lib::getHeader($pageName, "default.css");
?>
<div class="items" id="edit">
<?php
//checks to see if a file has been uploaded and moves it
if(!($_FILES['Image']['name'] == ""))
{
	$target = "images/".$_FILES['Image']['name'];
	move_uploaded_file($_FILES['Image']['tmp_name'], $target);
	//checks if adding new product or updating old product
	if(isset($_POST['edit']))
	{
		Lib::updateProduct($_POST['ProductName'], $_POST['ProductDesc'], $_POST['Price'], $_POST['Quantity'], $target, $_POST['SalePrice']);
	}//if
	else if(isset($_POST['add']))
	{
		Lib::addProduct($_POST['ProductName'], $_POST['ProductDesc'], $_POST['Price'], $_POST['Quantity'], $target, $_POST['SalePrice']);
	}//else if
	else
	{
		echo "<p>Error: Unable to add/update product.</p>";
	}//else
}//if
//updates product if changes have been submitted but no image uploaded
else if(isset($_POST['ProductName']) && $_FILES['Image']['name'] == "")
{
	Lib::updateProduct($_POST['ProductName'], $_POST['ProductDesc'], $_POST['Price'], $_POST['Quantity'], $_POST['ImagePath'], $_POST['SalePrice']);
}//else if
?>
	<form name="selectItem" action="admin.php" method="post" enctype="multipart/form-data">
		<h3>Select an item to edit:</h3>
		<select name="product">
			<?php echo Lib::displayProductOptions() ?>
		</select>
		<input type="submit" value="Select" />
	</form>
<?php
//displays the edit form if a product has been selected
if(isset($_POST['product']))
{
	echo Lib::displayEditForm($_POST['product']);
}
?>
</div>
<div id="add" class="items">
	<h3>Add an item:</h3>
		<form name="addItem" action="admin.php" method="post" enctype="multipart/form-data">
		<table>		
			<tr>
				<td>ProductName</td>
				<td><input name="ProductName" type="text" id="ProductName" value="" /></td>
			</tr>				
			<tr>
				<td>ProductDesc</td>
				<td><input name="ProductDesc" type="text" id="ProductDesc" value="" /></td>
			</tr>				
			<tr>
				<td>Price</td>
				<td><input name="Price" type="text" id="Price" value="" /></td>
			</tr>				
			<tr>
				<td>Quantity</td>
				<td><input name="Quantity" type="number" id="Quantity" value="" /></td>
			</tr>				
			<tr>
				<td>Image</td>
				<td><input name="Image" accept="image/*" type="file" id="Image" /></td>
			</tr>				
			<tr>
				<td>SalePrice</td>
				<td><input name="SalePrice" type="text" id="SalePrice" value="" /></td>
			</tr>
			<tr>
				<td><input type="submit" value="Add Product" /><input type="hidden" name="add" value="yes" /></td>
			</tr>
		</table>
		</form>
</div>
<div class="logout">
	<form name="logout" action="logout.php" method="post">
		<input type="submit" value="Logout" />
	</form>
</div>
<?php
echo Lib::getFooter();
?>