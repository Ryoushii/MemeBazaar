<?php
$pageName = "Cart";
include "LIB_project1.php";
echo Lib::getHeader($pageName, "default.css");
if(isset($_POST['productName']) && isset($_POST['quantity']) && isset($_POST['action']))
{
	//adds item to cart
	if($_POST['action'] == "add")
	{
		$qty = intval($_POST['quantity']);
		Lib::addToCart($_POST['productName'], $qty);
	}//if
	//removes item from cart
	else if($_POST['action'] == "remove")
	{
		$qty = intval($_POST['quantity']);
		Lib::removeFromCart($_POST['productName'], $qty);
	}//else if
}
?>
<div>
	<div class="items" id="cart">
		<h3>Your Cart</h3>
		<?php echo Lib::displayCart(); ?>
	</div>
</div>
<?php
echo Lib::getFooter();
?>