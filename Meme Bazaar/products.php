<?php
$pageName = "Products";
include "LIB_project1.php";
echo Lib::getHeader($pageName, "default.css");
//paging
//the maximum number of pages we should have based on number of records in the products table 
$numPages = Lib::getNumPages(); //gets the maximum number of pages with 5 results per page
//checks to see if a page number has been set
if(isset($_GET['pageNum']))
{
	//checks to see if pageNum is set lower than our first page and sets it to 1 if so
	if($_GET['pageNum'] < 1)
	{
		$pageNum = 1;
	}
	//checks to see if pageNum is set higher than our last page and sets it to the highest page if so
	else if($_GET['pageNum'] > $numPages)
	{
		$pageNum = $numPages;
	}//else if
	//sets the page number from the get if it is valid
	else
	{
		$pageNum = $_GET['pageNum']; //sets the page number 
	}//else
}//if
else
{
	$pageNum = 1; //if no page number is set, set to page 1
}//else
?>

<div class="items" id="sale">
	<a name="onsale"><h1>On Sale</h1></a>
	<?php echo Lib::displaySaleProducts(); ?>
</div>
<div class="items" id="memes">
	<a name="memes"><h1>Memes</h1></a>
	<?php echo Lib::displayProducts($pageNum); //displays a list of products based on current page number ?> 
	<div class="pages">
		<p><?php echo Lib::displayPages($pageNum, $numPages); //displays the page links ?></p>
	</div>
</div>

<?php
echo Lib::getFooter();
?>