<?php	
require_once 'core/init.php';
include 'includes/head.php';
include 'includes/navigation.php';
include 'includes/headerpartial.php';

if($cart_id != ''){
	$cartQ = $db->query("SELECT * FROM cart WHERE id='{$cart_id}'");
	$result = mysqli_fetch_assoc($cartQ);
	$items = json_decode($result['items'], true);
	$i = 1;
	$sub_total=0;
	$item_count = 0;
}
if(isset($_POST['clear_cart'])){
		$domain = (($_SERVER['HTTP_HOST'] != 'localhost')?'.'.$_SERVER['HTTP_HOST']:false);
		$db->query("DELETE FROM cart WHERE id = '{$cart_id}'");
		setcookie(CART_COOKIE, '', 1, "/", $domain, false);
		header ('Location: cart.php');
		//exit();
}
?>

<div class="col-md-12">
	<div class="row">
		<h2 class="text-center">Shopping Cart</h2><hr>
		<?php if($cart_id == '') : ?>
			<div class="bg-danger">
				<h4><p class="text-center text-danger">Your Cart Is Empty! <a href="index.php" class="text-center text-success">'Return Home'</a></p></h4>
			</div>
		<?php else: ?>
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
					<th>Sub Total</th>
					<th>Tax</th>
					<th>Grand Total</th>
				</thead>
				<tbody>
					<tr>
						<td><?=$item_count ;?></td>
						<td><?=money($sub_total) ;?></td>
						<td><?= money($tax); ?></td>
						<td class="bg-success"><?=money($grand_total) ;?></td>
					</tr>
				</tbody>
			</table>
			<!-- Checkout Button -->
				<button type="button" class="btn btn-primary pull-right" data-toggle="modal" data-target="#checkoutModal">
				  <span class="glyphicon glyphicon-shopping-cart"></span> Checkout >>
				</button>
			<!-- Clear Cart Button -->	
				<form method="post" action="cart.php">
					<button type="submit" id="clear_cart" name="clear_cart" class="btn btn-danger pull-left"> Clear Cart	</button>
				</form>

					<!-- Modal -->
					<div class="modal fade" id="checkoutModal" tabindex="-1" role="dialog" aria-labelledby="checkoutModalLabel">
					  <div class="modal-dialog modal-lg" role="document">
						<div class="modal-content">
						  <div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							<h4 class="modal-title" id="checkoutModalLabel">Shipping Address</h4>
						  </div>
						  <div class="modal-body">
						  <div class="row"> 
							<form action="thankyou.php" method="post" id="payment-form">
							<span class="bg-danger" id="payment-errors"></span>
							<input type="hidden" name="tax" value="<?=$tax ;?>" />
							<input type="hidden" name="sub_total" value="<?=$sub_total ;?>" />
							<input type="hidden" name="grand_total" value="<?=$grand_total ;?>" />
							<input type="hidden" name="cart_id" value="<?=$cart_id ;?>" />
							<input type="hidden" name="description" value="<?=$item_count.' item'.(($item_count > 1)?'s':'').' ordered' ;?>" />
								<div id="step1" style="display:block;">
									<div class="form-group col-md-6">
										<label for="full_name">Full Name:</label>
										<input class="form-control" id="full_name" name="full_name" type="text"/>
									</div>
									<div class="form-group col-md-6">
										<label for="email">Email:</label>
										<input class="form-control" id="email" name="email" type="email"/>
									</div>
									<div class="form-group col-md-6">
										<label for="street">Street Address:</label>
										<input class="form-control" id="street" name="street" data-stripe="address_line1" type="text"/>
									</div>
									<div class="form-group col-md-6">
										<label for="street2">Street Address 2:</label>
										<input class="form-control" id="street2" name="street2" data-stripe="address_line2" type="text"/>
									</div>
									<div class="form-group col-md-6">
										<label for="city">City:</label>
										<input class="form-control" id="city" name="city" data-stripe="address_city" type="text"/>
									</div>
									<div class="form-group col-md-6">
										<label for="state">State:</label>
										<input class="form-control" id="state" name="state" data-stripe="address_state" type="text"/>
									</div>
									<div class="form-group col-md-6">
										<label for="zip">Zip Code:</label>
										<input class="form-control" id="zip" name="zip" type="text" data-stripe="address_zip"/>
									</div>
									<div class="form-group col-md-6">
										<label for="country">Country:</label>
										<input class="form-control" id="country" name="country" data-stripe="address_country" type="text"/>
									</div>
								</div>
								
								
								<div id="step2" style="display:none;">
								<div class="col-md-6">
									<script src="https://checkout.stripe.com/checkout.js" class="stripe-button"
									  data-key="<?php echo $stripe['publishable_key']; ?>"
									  data-description="Ecom Payment"
									  data-amount="<?=$charge_amount ;?>"
									  data-locale="auto"
									  data-currency="KES"
									  data-email="<?php echo $email; ?>"
									  >
									 </script>
								</div>
								</div>
							
							</div>
						  </div>
						  <div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
							<button type="button" class="btn btn-primary" id="next_button" onclick="check_address();">Next >></button>
							<button type="button" class="btn btn-primary" id="back_button" style="display:none" onclick="back_address();"><< Back</button>
							<button type="" class="btn btn-primary" id="checkout_btn" style="display:none" >Checkout</button>
							</form>
						  </div>
						</div>
					  </div>
					</div>
		<?php endif; ?>
	</div>
</div>

<script>
function back_address(){
	jQuery('#payment-errors').html('');
	jQuery('#step1').css("display","block");
	jQuery('#step2').css("display","none");
	jQuery('#next_button').css("display","inline-block");
	jQuery('#back_button').css("display","none");
	jQuery('#checkout_btn').css("display","none");
	jQuery('#checkoutModalLabel').html('Shipping Address');
};

function check_address(){
	var data = {
		'full_name' : jQuery('#full_name').val(),
		'email' : jQuery('#email').val(),
		'street' : jQuery('#street').val(),
		'street2' : jQuery('#street2').val(),
		'city' : jQuery('#city').val(),
		'state' : jQuery('#state').val(),
		'zip' : jQuery('#zip').val(),
		'country' : jQuery('#country').val(),
	};
	jQuery.ajax({
		url : '/ecom/admin/parsers/check_address.php',
		method : 'POST',
		data : data,
		//function'(data)' isnt the variable data, its what gets returned from the parsers file
		success : function(data){
			if(data != 'passed'){
				jQuery('#payment-errors').html(data);
			}
			
			if(data == 'passed'){
				jQuery('#payment-errors').html('');
				jQuery('#step1').css("display","none");
				jQuery('#step2').css("display","block");
				jQuery('#next_button').css("display","none");
				jQuery('#back_button').css("display","inline-block");
				jQuery('#checkout_btn').css("display","inline-block");
				jQuery('#checkoutModalLabel').html('Select payment method');
				}
		},
		error : function(){alert("Something went wrong");},
	});
};

//Stripe
var stripe = Stripe('<?= $stripe['secret_key']; ?>');
var elements = stripe.elements();
// Custom Styling
var style = {
    base: {
        color: '#32325d',
        lineHeight: '24px',
        fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
        fontSmoothing: 'antialiased',
        fontSize: '16px',
        '::placeholder': {
            color: '#aab7c4'
        }
    },
    invalid: {
        color: '#fa755a',
        iconColor: '#fa755a'
    }
};
// Create an instance of the card Element
var card = elements.create('card', {style: style});
// Add an instance of the card Element into the `card-element` <div>
card.mount('#card-element');
// Handle real-time validation errors from the card Element.
card.addEventListener('change', function(event) {
    var displayError = document.getElementById('card-errors');
if (event.error) {
        displayError.textContent = event.error.message;
    } else {
        displayError.textContent = '';
    }
});
// Handle form submission
var form = document.getElementById('payment-form');
form.addEventListener('submit', function(event) {
    event.preventDefault();
stripe.createToken(card).then(function(result) {
        if (result.error) {
            // Inform the user if there was an error
            var errorElement = document.getElementById('card-errors');
            errorElement.textContent = result.error.message;
        } else {
            stripeTokenHandler(result.token);
        }
    });
});
// Send Stripe Token to Server
function stripeTokenHandler(token) {
    // Insert the token ID into the form so it gets submitted to the server
    var form = document.getElementById('payment-form');
// Add Stripe Token to hidden input
    var hiddenInput = document.createElement('input');
    hiddenInput.setAttribute('type', 'hidden');
    hiddenInput.setAttribute('name', 'stripeToken');
    hiddenInput.setAttribute('value', token.id);
    form.appendChild(hiddenInput);
// Submit form
    form.submit();
}
</script>
<?php
include 'includes/footer.php';
?>