<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/ecom/core/init.php';
if(!is_logged_in()){
	header ('Location: login.php');
}
include 'includes/head.php';
include 'includes/navigation.php';

//Compleet orders
if(isset($_GET['complete']) && $_GET['complete'] == 1){
	$cart_id = sanitize((int)$_GET['cart_id']);
	$db->query("UPDATE cart SET shipped = 1 where id='$cart_id'");
	$_SESSION['success_flash']= 'The order has been completed';
	header ('Location: index.php');
}

$txn_id = sanitize((int)$_GET['txn_id']);
$txnQ = $db->query("SELECT * FROM transactions WHERE id='$txn_id'");
$txn = mysqli_fetch_assoc($txnQ);
$cart_id = $txn['cart_id'];
$cartQ = $db->query("SELECT * FROM cart WHERE id='{$cart_id}'");
	$result = mysqli_fetch_assoc($cartQ);
	$items = json_decode($result['items'], true);
	$i = 1;
	$sub_total=0;
	$item_count = 0;

		
		
?>
<h2 class="text-center">Items Ordered</h2>
<table class="table table-condensed table-bordered table-striped">
	<thead>
		<th>Quantity</th>
		<th>Title</th>
		<th>Brand</th>
		<th>Category</th>
		<th>Size</th>
	</thead>
	<tbody>
	<?php
				foreach($items as $item){
					$product_id = $item['id'];
					$productQ = $db->query("SELECT i.id as 'id', i.title as 'title', b.brand, i.sizes, c.id as 'cid', c.category as 'child', p.category as 'parent'
		FROM products i
		LEFT JOIN brand b on i.brand = b.id
		LEFT JOIN categories c on i.categories = c.id
		LEFT JOIN categories p on c.parent = p.id
		WHERE i.id = '$product_id'");
					$product = mysqli_fetch_assoc($productQ);
					$sArray = explode(',',$product['sizes']);
					foreach($sArray as $sizeString){
						$s = explode(':', $sizeString);
						if($s[0] == $item['size']){
							$available = $s[1];
						}
					}
				?>
	<tr>
		<td><?=$item['quantity'] ;?></td>
		<td><?=$product['title'] ;?></td>
		<td><?=$product['brand'] ;?></td>
		<td><?=$product['parent'].' - '.$product['child'] ;?></td>
		<td><?=$item['size'] ;?></td>
	</tr>
	<?php
				//$i++;
				//$item_count += $item['quantity'];
				//$sub_total += ($product['price'] * $item['quantity']);
				}
				
				//$tax = TAXRATE * $sub_total;
				//$tax = number_format($tax, 2);
				//$grand_total = $tax + $sub_total;
				//$charge_amount = number_format($grand_total, 2) * 100;
				?>
	</tbody>
</table>

<div class="row">
	<div class="col-md-6">
		<h3 class="text-center">Order Details</h3>
		<table class="table table-condensed table-bordered table-striped">
			<tbody>
				<tr>
					<td>Sub Total</td>
					<td><?=money($txn['sub_total']) ;?></td>
				</tr>
				<tr>
					<td>Tax</td>
					<td><?=money($txn['tax']) ;?></td>
				</tr>
				<tr>
					<td>Grand Total</td>
					<td><?=money($txn['grand_total']) ;?></td>
				</tr>
				<tr>
					<td>Order Date</td>
					<td><?=pretty_date($txn['txn_date']) ;?></td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="col-md-6">
		<h3 class="text-center">Shipping Address</h3>
		<address >
		<?=$txn['full_name'] ;?><br>
		<?=$txn['email'] ;?><br>
		<?=$txn['street'] ;?><br>
		<?=(($txn['street2'] != '')?$txn['street2'].'<br>':'') ;?>
		<?=$txn['city'].', '.$txn['state'].' '.$txn['zip'] ;?><br>
		<?=$txn['country'] ;?><br>
		</address>
	</div>
</div>

<div class="pull-right">
	<a href="index.php" class="btn btn-lg btn-default">Cancel</a>
	<a href="orders.php?complete=1&cart_id=<?= $cart_id; ?>" class="btn btn-lg btn-primary">Complete Order</a>
</div>
<?php
include 'includes/footer.php';
?>