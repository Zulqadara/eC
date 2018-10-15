<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/ecom/core/init.php';

$mode = sanitize($_POST['mode']);
$edit_id = sanitize($_POST['edit_id']);
$edit_size = sanitize($_POST['edit_size']);

$cartQ = $db->query("SELECT * FROM cart WHERE id='{$cart_id}'");
$result = mysqli_fetch_assoc($cartQ);
$items = json_decode($result['items'], true);

$updatedItems = array();

$domain = (($_SERVER['HTTP_HOST'] != 'localhost')?'.'.$_SERVER['HTTP_HOST']:false);

if($mode == 'removeone'){
	foreach($items as $item){
		if($item['id'] == $edit_id && $item['size'] == $edit_size){
			$item['quantity'] = $item['quantity'] - 1;
		}
		if($item['quantity'] > 0){
			$updatedItems[] = $item;
		}
	}
}

if($mode == 'addone'){
	foreach($items as $item){
		if($item['id'] == $edit_id && $item['size'] == $edit_size){
			$item['quantity'] = $item['quantity'] + 1;
		}
		$updatedItems[] = $item;
		}
}

if(!empty($updatedItems)){
	$json_updated = json_encode($updatedItems);
	$db->query("UPDATE cart SET items='{$json_updated}' WHERE id='{$cart_id}'");
	$_SESSION['success_flash'] = 'Your cart has been updated!';
}

//Get rid of cart items from db, can keep for analytics
if(empty($updatedItems)){
	$db->query("DELETE FROM cart WHERE id = '{$cart_id}'");
	setcookie(CART_COOKIE, '', 1, "/", $domain, false);
}
?>