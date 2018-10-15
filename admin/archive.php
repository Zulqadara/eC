<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/ecom/core/init.php';
if(!is_logged_in()){
	login_error_redirect();
}
include 'includes/head.php';
include 'includes/navigation.php';

if(isset($_GET['restore'])){
	$id = (int)$_GET['restore'];
	$id=sanitize($_GET['restore']);
	$rSql = "UPDATE products set deleted = 0 where id='$id'";
	$db->query($rSql);
	header ('Location: archive.php');
	
}
?>
<h2 class="text-center">Archived Products</h2>
<a href="products.php" class="btn btn-primary pull-right" id="add-product-btn">View Products</a>
<div class="clearfix"></div><hr>
<?php
$sql = "SELECT * FROM products WHERE deleted='1'";
$aResult = $db->query($sql);
$aRows = mysqli_num_rows($aResult);
?>

<?php if($aRows >= 1) : ?>
<table class="table table-bordered table-condensed table-striped">
	<thead>
		<th></th>
		<th>Product</th>
		<th>Price</th>
		<th>Category</th>
	</thead>
	<tbody>
	<?php while($archive = mysqli_fetch_assoc($aResult)) :
		$childID = $archive['categories'];
		$catSql = "SELECT * FROM categories WHERE id='$childID'";
		$result = $db->query($catSql);
		$child = mysqli_fetch_assoc($result);
		$parentID = $child['parent'];
		$psql = "SELECT * FROM categories where id='$parentID'";
		$presult = $db->query($psql);
		$parent = mysqli_fetch_assoc($presult);
		$category = $parent['category'].'-'.$child['category'];

	?>
		<tr>
			<td><a href="archive.php?restore=<?= $archive['id'];?>" class="btn btn-xs btn-default"><span class="glyphicon glyphicon-refresh"></span></a></td>
			<td><?= $archive['title'] ;?></td>
			<td><?= $archive['price'] ;?></td>
			<td><?= $category ;?></td>
		</tr>
	<?php endwhile; ?>
	</tbody>
</table>
<?php else: ?>
<h4 class="text-center">No Products</h4>
<?php endif; ?>
<?php
include 'includes/footer.php';
?>