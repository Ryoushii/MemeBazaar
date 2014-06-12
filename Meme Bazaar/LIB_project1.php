<?php
include "DB.class.php";

//contains functions to return code for common page elements of the site
class Lib
{
	//names the page, sets the style and returns a string with the header code
	static function getHeader($pageName="Untitled", $pageStyle="")
	{
		$string = <<<HEADER
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" manifest="cache.manifest">
<head>
	<meta charset="utf-8" />
	<title>$pageName - The Meme Bazaar: Your One-Stop Shop for Memes!</title>
	<link rel="stylesheet" type="text/css" href="$pageStyle" />
	<link rel="shortcut icon" href="images/favicon.ico" />
</head>
<body>\n
<a href="index.php"><img src="images/banner.png" alt="The Meme Bazaar" /></a>\n
HEADER;
		$string .= self::getNav();
		return $string;
	}//getHeader
	
	//returns a string with the navigation code
	static function getNav()
	{
		$string = <<<NAV
<!--nav-->
<nav>
	<ul>
		<li><a href="index.php">Home</a></li>
		<li>
			<a href="products.php">Products <span class="caret"></span></a>
			<div>
				<ul>
					<li><a href="products.php#onsale">On Sale</a></li>
					<li><a href="products.php#memes">Memes</a></li>
				</ul>
			</div>
		</li>
		<li><a href="cart.php">Cart</a></li>
		<li><a href="admin.php">Admin</a></li>
	</ul>
</nav>\n
<div id="body">
NAV;
		return $string;
	}//getNav
	
	//returns a string with the footer code
	static function getFooter()
	{
		$string = <<<FOOTER
<!--footer-->
</div>
<div class="footer">
	<p>Site by Stephanie Kalnoske</p>
</div>
</body>
</html>
FOOTER;
		return $string;
	}//getFooter
	
	//gets the list of products from the db and displays them
	static function displayProducts($pageNum)
	{
		$string = "<ul>";
		$results = DB::getProducts($pageNum);
		foreach($results as $row)
		{
			$string .= <<<LIST
			<li>
				<form name="{$row['ProductName']}" action="cart.php" method="post">
					<a href="{$row['ImagePath']}"><img src="{$row['ImagePath']}" alt="{$row['ProductName']}" height="100" /></a>
					<h3>{$row['ProductName']}</h3>
					<p>{$row['ProductDesc']}</p><br />
					<span>Price:\${$row['Price']}</span>
					<span>Number available: {$row['Quantity']}</span>
LIST;
			//checks to make sure item is not sold out
			if($row['Quantity'] > 0)
			{
				$string .= <<<END
					<input name="quantity" type="number" min="0" max="{$row['Quantity']}" />&nbsp;<input type="submit" value="Add to Cart" />
						<input type="hidden" name="productName" value="{$row['ProductName']}" />
						<input type="hidden" name="action" value="add" />
					</form>
				</li>
END;
			}//if
			//if item is sold out
			else
			{
				$string .= <<<END
					<span style="color: red">SOLD OUT</span>&nbsp;<input type="submit" value="Add to Cart" disabled/>
						<input type="hidden" name="productName" value="{$row['ProductName']}" />
						<input type="hidden" name="action" value="add" />
					</form>
				</li>
END;
			}//else
		}
		$string .= "</ul>\n";
		return $string;
	}//displayProducts
	
	//gets the list of products on sale from the db and displays them
	static function displaySaleProducts()
	{
		$string = "<ul>";
		$results = DB::getSaleProducts();
		foreach($results as $row)
		{
			$string .= <<<LIST
			<li>
				<form name="{$row['ProductName']}" action="cart.php" method="post">
					<a href="{$row['ImagePath']}"><img src="{$row['ImagePath']}" alt="{$row['ProductName']}" height="100" /></a>
					<h3>{$row['ProductName']}</h3>
					<p>{$row['ProductDesc']}</p><br />
					<span>Price:</span>&nbsp;<span style="text-decoration: line-through;">\${$row['Price']}</span> <span style="color: red;">\${$row['SalePrice']}</span>
					<span>Number available: {$row['Quantity']}</span>&nbsp;
LIST;
			//checks to make sure item is not sold out
			if($row['Quantity'] > 0)
			{
				$string .= <<<END
					<input name="quantity" type="number" min="0" max="{$row['Quantity']}" />&nbsp;<input type="submit" value="Add to Cart" />
						<input type="hidden" name="productName" value="{$row['ProductName']}" />
						<input type="hidden" name="action" value="add" />
					</form>
				</li>
END;
			}//if
			//if item is sold out
			else
			{
				$string .= <<<END
					<span style="color: red">SOLD OUT</span>&nbsp<input type="submit" value="Add to Cart" disabled/>
						<input type="hidden" name="productName" value="{$row['ProductName']}" />
						<input type="hidden" name="action" value="add" />
					</form>
				</li>
END;
			}//else
		}
		$string .= "</ul>\n";
		return $string;
	}//displayProducts
	
	//displays the products currently in the cart
	static function displayCart()
	{
		$results = DB::getCart();
		$total; //total cost of the cart
		if($results == NULL)
		{
			$string = "<p>You have no items in your cart.  Maybe you should buy something!</p>";
		}//if
		else
		{
			$string = "<ul>\n";
			foreach($results as $row) 
			{
				$price;
				if($row['SalePrice'] > 0)
				{
					$price = $row['SalePrice'];
				}//if
				else
				{
					$price = $row['Price'];
				}//else
				$total += ($price * doubleval($row['Quantity']));
				$string .= <<<LIST
				<li class="cart">
					<form name="{$row['ProductName']}" action="cart.php" method="post">
						<p><strong>{$row['ProductName']}</strong> Quantity:{$row['Quantity']} \$$price</p>
						<input type="submit" value="Remove" />
						<input type="hidden" name="productName" value="{$row['ProductName']}" />
						<input type="hidden" name="quantity" value="{$row['Quantity']}" />
						<input type="hidden" name="action" value="remove" />
					</form>
				</li>
LIST;
			}
			$string .= "</ul>\n";
			$string .= "<div><p>Total: \$$total</p></div>";
		}//else
		return $string;
	}//displayCart
	
	//adds the specified quantity of an item to the cart table
	static function addToCart($productName, $qty)
	{
		//checks for valid quantity
		if($qty > 0)
		{
			$numCart = DB::getNumCart($productName); //checks to see if product is in cart
			//if product is not already in the cart
			if($numCart == 0)
			{
				$item = DB::getProduct($productName);
				$newQty = $item['Quantity'] - $qty; //the new quantity in inventory to update in the products table
				$item['Quantity'] = $qty; //the quantity of the item to send to the cart
				DB::addToCart($item);
				DB::updateQty($productName, $newQty, "Products");
			}//if
			//if product is already in the cart
			else
			{
				$item = DB::getProduct($productName);
				$cartItem = DB::getCartProduct($productName);
				$newQty = $item['Quantity'] - $qty; //the new quantity in inventory to update in the products table
				$newCartQty = $cartItem['Quantity'] + $qty; //the new quantity in the cart to update to the cart table
				DB::updateQty($productName, $newQty, "Products"); //updates the products table
				DB::updateQty($productName, $newCartQty, "Cart"); //updates the cart table
			}//else
		}//if
		else
		{
			echo "<p>Invalid quantity</p>";
		}//else
	}//addToCart
	
	//removes the item from the cart table
	static function removeFromCart($productName, $qty)
	{
		$item = DB::getProduct($productName);
		$newQty = $item['Quantity'] + $qty; //adds the quantity removed from cart back into inventory
		DB::removeItem("Cart", $productName); //removes item from the cart table
		DB::updateQty($productName, $newQty, "Products"); //updates the items quantity in the products table
	}//removeFromCart
	
	//gets the number of rows in the products table and determines the max number of pages we should have to list products
	static function getNumPages()
	{
		$rows = DB::getNumRows();
		$numPages = ceil($rows/5); //the max pages of our results with 5 results per page
		return $numPages;
	}//getNumPages
	
	//displays links for the product pages based on the current page number and the maximum number of pages
	static function displayPages($numPage, $maxPages)
	{
		$path = "products.php?pageNum=";
		$string = "<span class=\"pages\">";
		for($i = 1; $i <= $maxPages; $i++)
		{
			$string .= <<<PAGES
				<a href="$path$i#memes">$i</a>&nbsp;
PAGES;
		}//for
		$string .= "</span>";
		return $string;
	}//displayPages
	
	//checks entered username and password with the database
	static function checkLogin($username, $password)
	{
		//sanitizes inputs before going into the db
		$username = stripslashes($username);
		$password = stripslashes($password);
		$rows = DB::checkLogin($username, $password);
		//there is a password match
		if($rows > 0)
		{
			//register username and password and redirect the admin.php page
			session_start();
			$_SESSION["username"] = $username;
			$_SESSION["password"] = $password;
			header('Refresh: 5; URL=admin.php');
			echo "<div><p>Logged in.  Redirecting...</p></div>";
		}//if
		else
		{
			echo "<div><p>Wrong username or password.</p></div>";
		}//else
	}//checkLogin
	
	//forms a dropdown list of the products in the database
	static function displayProductOptions()
	{
		$string;
		$results = DB::getAllProducts(); //gets the array of all the products in the database
		//makes an option element for each item in the array
		foreach($results as $row)
		{
			$string .= "<option value=\"{$row['ProductName']}\">{$row['ProductName']}</option>\n";
		}//foreach
		return $string;
	}
	
	//displays a form to edit an item in the db and autofills the current information
	static function displayEditForm($productName)
	{
		$string;
		$item = DB::getProduct($productName);
		$string .= <<<FORM
		<form name="edit" method="post" action="admin.php" enctype="multipart/form-data">
		<table>
			<tr>
				<td><strong>Edit:</strong></td>
			</tr>
FORM;
		//makes a table element for each attribute of the product
		foreach($item as $key=>$value)
		{
			$string .= <<<INPUT
			<tr>
				<td>$key</td>
				<td><input name="$key" type="text" value="$value" /></td>
			</tr>	
INPUT;
		}//foreach
		$string .= <<<END
			<tr>
				<td>New Image:</td>
				<td><input type="file" accept="image/*" name="Image" /></td>
			</tr>
			<tr><td><input type="submit" value="Submit Changes" /><input type="hidden" name="edit" value="yes" /></td></tr>
		</table>
		</form>
END;
		return $string;
	}//displayEditForm
	
	static function updateProduct($productName, $productDesc, $price, $qty, $imagePath, $salePrice)
	{
		$productName = self::sanitizeInput($productName);
		$productDesc = self::sanitizeInput($productDesc);
		$price = doubleval(self::sanitizeInput($price));
		$qty = intval(self::sanitizeInput($qty));
		$imagePath = self::sanitizeInput($imagePath);
		$salePrice = doubleval(self::sanitizeInput($salePrice));
		$currItem = DB::getProduct($productName);
		$currSalePrice = $currItem['SalePrice']; //gets the current sale price of the item
		$numSale = count(DB::getSaleProducts()); //checks the number of products currently on sale
		//checks to make sure price, quantity and sale price are not negative
		if($price >= 0 && $qty >= 0 && $salePrice >= 0)
		{
			//checks to make sure there are at least 3 and at most 5 products on sale
			if(($salePrice > 0 && $currSalePrice > 0) || ($salePrice == 0 && $currSalePrice == 0)) //item hasn't changed sale status, so no checks need to be made
			{
				DB::updateItem($productName, $productDesc, $price, $qty, $imagePath, $salePrice);
			}//if
			else if(($salePrice > 0 && $numSale < 5) || ($salePrice == 0 && $numSale > 3))
			{
				DB::updateItem($productName, $productDesc, $price, $qty, $imagePath, $salePrice);
			}
			else
			{
				echo "<p>Error: Not enough or too many products on sale</p>";
			}//else
		}//if
		else
		{
			echo "<p>Error: Invalid price, quantity, or sale price</p>";
		}//else
	}//updateProduct
	
	//adds a new product to the products table
	static function addProduct($productName, $productDesc, $price, $qty, $imagePath, $salePrice)
	{
		$productName = self::sanitizeInput($productName);
		$productDesc = self::sanitizeInput($productDesc);
		$price = doubleval(self::sanitizeInput($price));
		$qty = intval(self::sanitizeInput($qty));
		$imagePath = self::sanitizeInput($imagePath);
		$salePrice = doubleval(self::sanitizeInput($salePrice));
		$numSale = count(DB::getSaleProducts()); //checks the number of products currently on sale
		if($price >= 0 && $qty >= 0 && $salePrice >= 0)
		{
			if($salePrice > 0 && $numSale < 5)
			{
				DB::addItem($productName, $productDesc, $price, $qty, $imagePath, $salePrice);
			}//if
			else if($salePrice == 0)
			{
				DB::addItem($productName, $productDesc, $price, $qty, $imagePath, $salePrice);
			}
			else
			{
				echo "<p>Error: Too many products on sale</p>";
			}//else
		}//if
		else
		{
			echo "<p>Error: Invalid price, quantity or sale price</p>";
		}//else
	}//addProduct
	
	//cleans the passed input
	static function sanitizeInput($input)
	{
		$input = strip_tags($input); // Remove HTML
		$input = htmlspecialchars($input); // Convert characters
		$input = trim(rtrim(ltrim($input))); // Remove spaces
		return $input;
	}//sanitizeInput
}//Lib
?>