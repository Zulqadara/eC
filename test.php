<?php	
require_once 'core/init.php';
include 'includes/head.php';

$pr = array();
$a = array();
$arr = array();
	$cartQ = $db->query("SELECT cart.items FROM transactions inner join cart on transactions.cart_id = cart.id");
while($result = mysqli_fetch_array($cartQ)){
$items = json_decode($result['items'], true);
//var_dump ($items);
$string = '';
$prod = 9;
	foreach ($items as $key => $value) {
	//var_dump ( $value );
		//$pds =  implode(" ",$value);
		//echo $pds . '<br>';
		//var_dump($value);

		$a[] = $value;
		//$string .= $value["id"] . "," . $value["quantity"] . ':' ;
		//echo $string;
  }
//echo $string;
}	

$arr = array_merge($a);

$final = array();
foreach($arr as $value) {
    $id = $value['id'];
    $filter = array_filter($arr, function($ar) {
        GLOBAL $id;
        $valueArr = ($ar['id'] == $id);
        return $valueArr;
    });
    $sum = array_sum(array_column($filter, 'quantity'));
    $final[$id] = array('id' => $id, 'quantity' => $sum);
}
 foreach($final as $f){
	 $sd = $f['id'];
	 if($f['id'] == $prod){
		 echo $f['quantity'];
	 }
 }
	
?>

<div class="col-md-12">
	<div class="row">
		<h2 class="text-center">Shopping Cart</h2><hr>
		
			<table class="table table-bordered table-condensed table-striped">
				<thead>
					<th>#</th>
					<th>Item</th>
					<th>Price</th>
					<th>Quantity</th>
					<th>Size</th>
					<th>Sub Total</th>
				</thead>
				<tbody>
				<?php
				while($result = mysqli_fetch_assoc($cartQ)){
					$items = json_decode($result['items'], true);
				foreach($items as $item){
					$product_id = $item['id'];
					$productQ = $db->query("SELECT * FROM products WHERE id='$product_id'");
					$product = mysqli_fetch_assoc($productQ);
					$sArray = explode(',',$product['sizes']);
					//$item['size'] comes from the $items, which comes from cart table json string id size
					foreach($sArray as $sizeString){
						$s = explode(':', $sizeString);
						if($s[0] == $item['size']){
							$available = $s[1];
						}
					}
				?>
					<tr>
						<td><?=$i ;?></td>
						<td><?=$product['title'] ;?></td>
						<td><?=money($product['price']) ;?></td>
						<td>
							<button class="btn btn-xs btn-default" onclick="update_cart('removeone', '<?=$product['id'] ;?>', '<?=$item['size'];?>');">-</button>
							<?=$item['quantity'] ;?>
							<?php if($item['quantity'] < $available) : ?>
							<button class="btn btn-xs btn-default" onclick="update_cart('addone', '<?=$product['id'] ;?>', '<?=$item['size']; ?>');">+</button>
							<?php else : ?>
							<span class="text-danger">Max</span>
							<?php endif; ?>
						</td>
						<td><?=$item['size'] ;?></td>
						<td><?= money($item['quantity'] * $product['price']) ;?></td>
					</tr>
				<?php
				$i++;
				$item_count += $item['quantity'];
				$sub_total += ($product['price'] * $item['quantity']);
				}
				}
				$tax = TAXRATE * $sub_total;
				$tax = number_format($tax, 2);
				$grand_total = $tax + $sub_total;
				$charge_amount = number_format($grand_total, 2) * 100;
				?>
				</tbody>
			</table>
			<table class="table table-bordered table-condensed table-striped text-right">
			<legend>Totals</legend>
				<thead class="totals-table-header">
					<th>Total Items</th>
				
				</thead>
				<tbody>
					<tr>
						<td class="text-center"><?=$item_count ;?></td>
						
					</tr>
				</tbody>
			</table>



	</div>
</div>

<?php
include 'includes/footer.php';
?>