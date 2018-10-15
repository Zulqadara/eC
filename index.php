<?php
require_once 'core/init.php';
include 'includes/head.php';
//navigation
include 'includes/navigation.php';
//header
include 'includes/headerfull.php';
//left bar
include 'includes/leftbar.php';

$sql = "SELECT * FROM products WHERE featured = 1";
$featured = $db ->query($sql);
?>

<!-- Mian Content -->
		<div class="col-md-8">
				<div class="row">
					<h2 class="text-center">Featured Products</h2>
					<?php while ($product = mysqli_fetch_assoc($featured)) : ?>
					<div class="col-md-3">
						  <style>.ellipsis { text-overflow: ellipsis; }</style>
						<h4 class="overflow ellipsis"><?= $product['title']; ?></h4><!--used to limit chars -->
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
	