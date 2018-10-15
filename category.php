<?php
require_once 'core/init.php';
include 'includes/head.php';
//navigation
include 'includes/navigation.php';
//header
include 'includes/headerpartial.php';
//left bar
include 'includes/leftbar.php';

if(isset($_GET['cat'])){
	$cat_ID = sanitize($_GET['cat']);
}else{
	$cat_ID = '';
}
$sql = "SELECT * FROM products WHERE categories = '$cat_ID'";
$productQ = $db ->query($sql);
$category = get_category($cat_ID);
?>

<!-- Mian Content -->
		<div class="col-md-8">
				<div class="row">
					<h2 class="text-center"><?=$category['parent']. ' ' .$category['child']; ?></h2>
					<?php while ($product = mysqli_fetch_assoc($productQ)) : ?>
					<div class="col-md-3">
						<h4><?= $product['title']; ?></h4>
						<?php $photos = explode(',', $product['image']); ?>
						<img src="<?= $photos[0]; ?>" alt="<?= $product['title']; ?>" class="img-thumb"/>
						<p class="list-price text-danger">List Price: <s>$<?= $product['list_price']; ?></s></p>
						<p class="price">Our Price: $<?= $product['price']; ?></p>
						<button type="button" class="btn btn-sm btn-success" onCLick="detailsmodal(<?= $product['id']; ?>)">Details</button>
					</div>
					<?php endwhile; ?>
				</div>
		</div>
		
<?php

//right bar
include 'includes/rightbar.php';
//footer
include 'includes/footer.php';

?>	
	