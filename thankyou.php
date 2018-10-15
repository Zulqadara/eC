<?php
require_once 'core/init.php';

//Stripe!
\Stripe\Stripe::setApiKey($stripe['secret_key']);
// Set your secret key: remember to change this to your live secret key in production
// See your keys here: https://dashboard.stripe.com/account/apikeys

			//GET THE REST OF THE POST DATA
$full_name = sanitize($_POST['full_name']);
$email = sanitize($_POST['email']);
$street = sanitize($_POST['street']);
$street2 = sanitize($_POST['street2']);
$city = sanitize($_POST['city']);
$state = sanitize($_POST['state']);
$zip = sanitize($_POST['zip']);
$country = sanitize($_POST['country']);
$tax = sanitize($_POST['tax']);
$sub_total = sanitize($_POST['sub_total']);
$grand_total = sanitize($_POST['grand_total']);
$cart_id = sanitize($_POST['cart_id']);
$description = sanitize($_POST['description']);
$charge_amount = number_format($grand_total, 2) * 100; //Amount of money in cents

$metadata = array(
	"cart_id" => $cart_id,
	"tax" => $tax,
	"sub_total" => $sub_total,
	
);

// Token is created using Checkout or Elements!
// Get the payment token ID submitted by the form:
$token = $_POST['stripeToken'];
	
 
  
try{
$customer = \Stripe\Customer::create(array(
	"email" => $email,
  "source" => $token,
));
  
  
// Charge the user's card:
$charge = \Stripe\Charge::create(array(
  
  "amount" => $charge_amount,
  "currency" => CURRENCY,
  //"source" => $token, //remove when tring not to store customers
  "description" => $description,
  "receipt_email" => $email, //wont work on test, only live
  "metadata" => $metadata,
  "customer" => $customer->id
));
//Adjust inventory
$itemq = $db->query("SELECT * FROM cart WHERE id='{$cart_id}'");
$itemResult = mysqli_fetch_assoc($itemq);
$items = json_decode($itemResult['items'], true);
foreach($items as $item){
	$newSizes = array();
	$itemId = $item['id'];
	$productQ = $db->query("SELECT sizes from products where id='{$itemId}'");
	$product = mysqli_fetch_assoc($productQ);
	$sizes = sizesToArray($product['sizes']);
	foreach($sizes as $size){
	
		if($size['size'] == $item['size']){
			$q = $size['quantity'] - $item['quantity'];
			$newSizes[] = array('size' => $size['size'], 'quantity' => $q, 'threshold' => $size['threshold']);
		}else{
			$newSizes[] = array('size' => $size['size'], 'quantity' => $size['quantity'], 'threshold' => $size['threshold']);
		}
	}
	$sizeString = sizeToString($newSizes);
	$db->query("UPDATE products SET sizes = '{$sizeString}' WHERE id='{$itemId}'");
}

//Update cart
$db->query("UPDATE cart Set paid = '1' where id = '{$cart_id}'");
$db->query("INSERT INTO transactions (charge_id, cart_id, full_name, email, street, street2, city, state, zip, country, sub_total, tax, grand_total, description, txn_type)
 VALUES ('$charge->id', '$cart_id', '$full_name', '$email', '$street', '$street2', '$city', '$state', '$zip', '$country', '$sub_total', '$tax', '$grand_total', '$description' , '$charge->object')");
 
 $domain = (($_SERVER['HTTP_HOST'] != 'localhost')? '.'.$_SERVER['HTTP_HOST']:false);
 setcookie(CART_COOKIE, '', 1, "/", $domain, false);
 
 include 'includes/head.php';
 include 'includes/navigation.php';
 include 'includes/headerpartial.php';
 include 'includes/head.php';
 ?>

	<h1 class="text-center text-success">Thank You!</h1>
	<p>Your card has been successully charged <?=money($grand_total) ;?>. You have been emailed a receipt. Please check your spam folder if it is not in your inbox</p>
	<p>Your receipt number is: <strong><?= $cart_id; ?></strong></p>
	<p>Your order will be shipped to the address below</p>
	<address>
		<?= $full_name; ?> <br>
		<?= $street; ?> <br>
		<?=(($street2 != '')?$street2. '<br>':'' ); ?>
		<?= $city.', '.$state. ', '.$zip ; ?> <br>
		<?= $country; ?> <br>
	</address>
 
 <?php
 include 'includes/footer.php';
}catch(\Stripe\Error\Card $e){
	// The card has been declined
	echo $e;
	/*$body = $e->getJsonBody();
  $err  = $body['error'];

  print('Status is:' . $e->getHttpStatus() . "\n");
  print('Type is:' . $err['type'] . "\n");
  print('Code is:' . $err['code'] . "\n");
  // param is '' in this case
  print('Param is:' . $err['param'] . "\n");
  print('Message is:' . $err['message'] . "\n");
} catch (\Stripe\Error\RateLimit $e) {
  // Too many requests made to the API too quickly
} catch (\Stripe\Error\InvalidRequest $e) {
  // Invalid parameters were supplied to Stripe's API
} catch (\Stripe\Error\Authentication $e) {
  // Authentication with Stripe's API failed
  // (maybe you changed API keys recently)
} catch (\Stripe\Error\ApiConnection $e) {
  // Network communication with Stripe failed
} catch (\Stripe\Error\Base $e) {
  // Display a very generic error to the user, and maybe send
  // yourself an email
} catch (Exception $e) {
  // Something else happened, completely unrelated to Stripe*/
}

?>
