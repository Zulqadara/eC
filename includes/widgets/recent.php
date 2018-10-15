<h3 class="">Popular Items</h3>
<?php
$transactionsq = $db->query("select * from cart where paid=1 order by id desc limit 5");
$results = array();
while($row = mysqli_fetch_assoc($transactionsq)){
	$results[] = $row;
}
$rowCount = $transactionsq->num_rows;
$used_ids = array();
for ($i=0; $i < $rowCount; $i++){
	$json_items = $results[$i]['items'];
	$items = json_decode($json_items, true);
	foreach($items as $item){
		if(!in_array($item['id'], $used_ids)){
			$used_ids[] = $item['id'];
		}
	}
}
?>

<div id="recent_widget">
	<table class="table table-condensed">
		<?php foreach($used_ids as $id): 
			$productq = $db->query("SELECT id, title from products where id='$id'");
			$product = mysqli_fetch_assoc($productq);
		?>
		<tr>

			<td>
				<?=substr($product['title'], 0, 15) ;?>
			</td>
			<td>
				<a class="text-primary" onclick="detailsmodal('<?= $id; ?>');">View</a>
			</td>
		</tr>
		<?php endforeach; ?>
	</table>
</div>