<h3 class="">Shopping Cart</h3>
<div>
<?php if(empty($cart_id)): ?>
	<p class="">Cart Empty</p>
<?php else: 

 $cartq = $db->query("SELECT * FROM cart WHERE id='$cart_id'"); ;
 $results = mysqli_fetch_assoc($cartq);
 $items = json_decode($results['items'], true);
 
 $sub_total = 0
 ?>
<table class="table table-condensed" id="cart_widget">
	<tbody>
		<?php foreach($items as $item) :
			$productq = $db->query("SELECT * FROM products where id = '{$item['id']}'");
			$product = mysqli_fetch_assoc($productq);
		?>
		<tr>
			<td><?=$item['quantity'] ;?></td>
			<td><?=substr($product['title'], 0, 15) ; //substr shortens the string length?></td>
			<td><?=money($item['quantity'] * $product['price']) ;?></td>
		</tr>
		<?php 
			$sub_total += ($item['quantity'] * $product['price']);
		endforeach; ?>
		<tr>
			<td></td>
			<td>Sub Total:</td>
			<td><?= money($sub_total); ?></td>
		</tr>
	</tbody>
</table>
<a href="cart.php" class="btn btn-xs btn-primary pull-right">View Cart</a>
<div class="clearfix"></div>
<?php endif; ?>
</div>



