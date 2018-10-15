<?php
require_once '../core/init.php';

if(isset($_POST["id"])){
	$id = $_POST['id'];
	$id = (int)$id;
}else{
    $id = NULL;
}


$sql = "SELECT * FROM products WHERE id='$id'";
$result = $db ->query($sql);
$product = mysqli_fetch_assoc($result);

$brand_id = $product['brand'];
$sql = "SELECT brand FROM brand WHERE id = '$brand_id'";
$brandquery = $db ->query($sql);
$brand = mysqli_fetch_assoc($brandquery);

$sizestring = $product['sizes'];

$size_array = explode(',', $sizestring)
?>

<!-- Details Modal -->
<?php 
//better way to echo an entire page
ob_start();
?>
	<div class="modal fade details-1" id="details-modal" tabindex="-1" role="dialog" aria-labelledby="details-1" aria-hidden="true" >
		<div class="modal-dialog modal-lg">
		  <div class="modal-content">
			<div class="modal-header">
				<button class="close" type="button" onclick="closeModal()" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title text-center"><?= $product['title']; ?></h4>
			</div>
			<div class="modal-body">
				<div class="container-fluid">
					<div class="row">
					<span id="modal_errors" class="bg-danger"></span>
						<div class="col-sm-6 fotorama" data-nav="thumbs">
							<?php $photos = explode(',',$product['image']);
							foreach($photos as $photo): ?>
							
								<img src="<?= $photo ?>" alt="<?= $product['title']; ?>" class="details img-responsive"/>
							
							<?php endforeach; ?>
						</div>
						<div class="col-sm-6">
							<h4>Details</h4>
							<p><?= nl2br($product['description']); //nl2br is used to preserve line breaks  ?></p>
							<hr>
							<p>Price: <?= $product['price']; ?></p>
							<p>Brand: <?= $brand['brand']; ?></p>
							<form action="add_cart.php" method="post" id="add_product_form">
							<input type="hidden" name="available" id="available" value=""/>
							<!-- All Inputs Get sent to footer jQuery and Ajax script, which send to add_cart -->
							<input type="hidden" name="product_id" id="product_id" value="<?=$id ;?>"/>
								<div class="form-group">
									<label for="quantity">Quantity:</label>
									<input type="number" min="0" class="form-control" id="quantity" name="quantity"/>
								</div>
								<div class="form-group">
								
									<label for="size">Size:</label>
									<select class="form-control" id="size" name="size">
										<option value=""></option>
										<?php foreach($size_array as $string) {
											$string_array = explode(':',$string);
											$size = $string_array[0];
											$available = $string_array[1];
											if($available > 0){
											echo '<option value="'.$size.'" data-available="'. $available .'">'.$size.' ('.$available.' Avaiable)</option>';
											}
										} ?>
									</select>
								
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button class="btn btn-default" onclick="closeModal()">Close</button>
				<button class="btn btn-warning" onclick="add_to_cart(); return false;"<span class="glyphicon glyphicon-shopping-cart"></span> Add To Cart</button>
			</div>
		  </div>
		</div>
	</div>
<script>

$('input').on('input', function () {
    
    var value = $(this).val();
    
    if ((value !== '') && (value.indexOf('.') === -1)) {
        
        $(this).val(Math.max(Math.min(value, 50000), 0));
    }
});
	jQuery('#size').change(function(){
		var available = parseInt(jQuery('#available').val());
		var available = jQuery('#size option:selected').data("available");
		jQuery('#available').val(available);
	});
	$(function () {
		$('.fotorama').fotorama({'loop': true, 'autoplay' : true });
	});
	function closeModal(){
		jQuery('#details-modal').modal('hide');
		setTimeout(function(){
			jQuery('#details-modal').remove();
		},500);
		jQuery('.modal-backdrop').remove();
	}
</script>
<?php 
//cleans the buffer memory
echo ob_get_clean(); ?>