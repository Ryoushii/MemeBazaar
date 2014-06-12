<?php
include "../../../connect.php";

//contains functions with prepared statements for connecting to the database
class DB
{
	//takes the page number of results to display and returns an array of items
	function getProducts($pageNum)
	{
		global $mysqli;
		$offset = (5*$pageNum)-5; //sets result limit based on page number
		if($stmt = $mysqli->prepare("SELECT ProductName, ProductDesc, Price, Quantity, ImagePath, SalePrice FROM Products WHERE SalePrice = 0 ORDER BY ProductName LIMIT $offset, 5"))
		{
			$results = array(); //to store all of the assoc_array results from the query
			$stmt->execute();
			$row = array();
			self::stmt_bind_assoc($stmt, $row); //bind results into assoc array
			while($stmt->fetch())
			{
				foreach($row as $key=>$value)
				{
					$row_tmb[$key] = $value;
				}//foreach 
				$results[] = $row_tmb;
			}//while
			$stmt->close();
			return $results;
		}//if
		else
		{
			return "stmt isn't working.";
		}//else
	}//getProducts
	
	//retrieves a list of products on sale and places them in an array
	function getSaleProducts()
	{
		global $mysqli;
		if($stmt = $mysqli->prepare("SELECT ProductName, ProductDesc, Price, Quantity, ImagePath, SalePrice FROM Products WHERE SalePrice > 0 ORDER BY ProductName"))
		{
			$results = array(); //to store all of the assoc_array results from the query
			$stmt->execute();
			$row = array();
			self::stmt_bind_assoc($stmt, $row); //bind results into assoc array
			while($stmt->fetch())
			{
				foreach($row as $key=>$value)
				{
					$row_tmb[$key] = $value;
				}//foreach 
				$results[] = $row_tmb;
			}//while
			$stmt->close();
			return $results;
		}//if
		else
		{
			return "stmt isn't working.";
		}//else
	}//getSaleProducts
	
	//returns an array of all products in the database
	function getAllProducts()
	{
		global $mysqli;
		if($stmt = $mysqli->prepare("SELECT ProductName, ProductDesc, Price, Quantity, ImagePath, SalePrice FROM Products ORDER BY ProductName"))
		{
			$results = array(); //to store all of the assoc_array results from the query
			$stmt->execute();
			$row = array();
			self::stmt_bind_assoc($stmt, $row); //bind results into assoc array
			while($stmt->fetch())
			{
				foreach($row as $key=>$value)
				{
					$row_tmb[$key] = $value;
				}//foreach 
				$results[] = $row_tmb;
			}//while
			$stmt->close();
			return $results;
		}//if
		else
		{
			return "stmt isn't working.";
		}//else
	}//getAllProducts
	
	//binds the results of $stmt to an associative array
	function stmt_bind_assoc(&$stmt, &$out)
	{
		$data = mysqli_stmt_result_metadata($stmt);
		$fields = array();
		$out = array();

		$fields[0] = $stmt;
		$count = 1;

		while($field = mysqli_fetch_field($data)) 
		{
			$fields[$count] = &$out[$field->name];
			$count++;
		}    
		call_user_func_array(mysqli_stmt_bind_result, $fields);
	}//stmt_bind_assoc
	
	//gets a single product from the Products table
	function getProduct($productName)
	{
		global $mysqli;
		$row = array();
		$stmt = $mysqli->prepare("SELECT ProductName, ProductDesc, Price, Quantity, ImagePath, SalePrice FROM Products WHERE ProductName = ?");
		$stmt->bind_param('s', $productName);
		$stmt->execute();
		self::stmt_bind_assoc($stmt, $row);
		$stmt->fetch();
		$stmt->close();
		return $row;
	}//getProduct
	
	//gets a single product from the Cart table
	function getCartProduct($productName)
	{
		global $mysqli;
		$row = array();
		$stmt = $mysqli->prepare("SELECT ProductName, ProductDesc, Price, Quantity, SalePrice FROM Cart WHERE ProductName = ?");
		$stmt->bind_param('s', $productName);
		$stmt->execute();
		self::stmt_bind_assoc($stmt, $row);
		$stmt->fetch();
		$stmt->close();
		return $row;
	}//getCartProduct
	
	//updates the quantity of a product in the given table
	function updateQty($productName, $qty, $table)
	{	
		global $mysqli;
		$stmt = $mysqli->prepare("UPDATE $table SET Quantity = ? WHERE ProductName = ?");
		$stmt->bind_param('is', $qty, $productName);
		$stmt->execute();
		$stmt->close();
	}//updateQty
	
	//adds an item to the Products table
	function addItem($name, $desc, $price, $qty, $img, $sale)
	{
		global $mysqli;
		$stmt = $mysqli->prepare("INSERT INTO Products(ProductName, ProductDesc, Price, Quantity, ImagePath, SalePrice) VALUES (?, ?, ?, ?, ?, ?)");
		$stmt->bind_param('ssdisd', $name, $desc, $price, $qty, $img, $sale);
		$stmt->execute();
		$stmt->close();
	}//addItem
	
	//removes an item from the specified table
	function removeItem($table, $productName)
	{
		global $mysqli;
		$stmt = $mysqli->prepare("DELETE FROM $table WHERE ProductName = ?");
		$stmt->bind_param('s', $productName);
		$stmt->execute();
		$stmt->close();
	}//removeItem
	
	//adds an item to the cart table
	function addToCart($item)
	{
		global $mysqli;
		$stmt = $mysqli->prepare("INSERT INTO Cart(ProductName, ProductDesc, Price, Quantity, SalePrice) VALUES (?, ?, ?, ?, ?)");
		$stmt->bind_param('ssdid', $item['ProductName'], $item['ProductDesc'], $item['Price'], $item['Quantity'], $item['SalePrice']);
		$stmt->execute();
		$stmt->close();
	}//addToCart
	
	//displays the cart
	function getCart()
	{
		global $mysqli;
		if($stmt = $mysqli->prepare("SELECT ProductName, ProductDesc, Price, Quantity, SalePrice FROM Cart ORDER BY ProductName"))
		{
			$results = array(); //to store all of the assoc_array results from the query 
			$stmt->execute();
			$row = array();
			self::stmt_bind_assoc($stmt, $row); //bind results into assoc array
			while($stmt->fetch())
			{
				foreach($row as $key=>$value)
				{
					$row_tmb[$key] = $value;
				}//foreach 
				$results[] = $row_tmb;
			}//while
			$stmt->close();
			return $results;
		}//if
		else
		{
			return "getCart isn't working";
		}//else
	}//getCart
	
	//checks the cart for a specific product
	function getNumCart($productName)
	{
		global $mysqli;
		$stmt = $mysqli->prepare("SELECT ProductName FROM Cart WHERE ProductName = ?");
		$stmt->bind_param('s', $productName);
		$stmt->execute();
		$stmt->store_result();
		$rows = $stmt->num_rows;
		$stmt->close();
		return $rows;
	}//getCartProduct
	
	//gets the number of results in the products table not on sale
	function getNumRows()
	{
		global $mysqli;
		$stmt = $mysqli->prepare("SELECT ProductName FROM Products WHERE SalePrice = ?");
		$noSale = 0; //parameter to check to see if item is on sale or not
		$stmt->bind_param('d', $noSale);
		$stmt->execute();
		$stmt->store_result();
		$rows = $stmt->num_rows;
		$stmt->close();
		return $rows;
	}//getNumRows
	
	//checks the P1User table for a user with the given username and password and returns the number of rows that match
	function checkLogin($username, $password)
	{
		global $mysqli;
		$stmt = $mysqli->prepare("SELECT UserName Password FROM P1User WHERE UserName = ? and Password = ?");
		$stmt->bind_param('ss', $username, $password);
		$stmt->execute();
		$stmt->store_result();
		$rows = $stmt->num_rows;
		$stmt->close();
		return $rows;
	}//checkLogin
	
	//updates a product in the db
	function updateItem($name, $desc, $price, $qty, $img, $sale)
	{
		global $mysqli;
		$img = $mysqli->real_escape_string($img);
		$stmt = $mysqli->prepare("UPDATE Products SET ProductName = ?, ProductDesc = ?, Price = ?, Quantity = ?, ImagePath = ?, SalePrice = ? WHERE ProductName = ?");
		$stmt->bind_param('ssdisds', $name, $desc, $price, $qty, $img, $sale, $name);
		$stmt->execute();
		$stmt->close();
	}//updateItem
	
	//gets the total sale price of the cart
	function totalCart()
	{
		global $mysqli;
		$stmt = $mysqli->prepare("SELECT SUM(Price * Quantity) FROM Cart");
		$stmt->execute();
		$stmt->bind_result($total);
		$stmt->close();
		return $total;
	}//totalCart
	
}//DB
?>