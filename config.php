<?php
define('BASEURL',$_SERVER['DOCUMENT_ROOT'].'/ecom/');
define ('CART_COOKIE', 'EcO7n8hNm4hy7Nn');
define ('CART_COOKIE_EXPIRE', time() + (86400 * 30));
define ('TAXRATE', 0.087); //Sales text rate tax rate covreted from percentage no tax = 0

//Stripe
define ('CURRENCY', 'usd');
require_once('vendor/autoload.php');

$stripe = array(
  "secret_key"      => "sk_test_6YbbGVyR6ktNSwaBUF8ezDcB",
  "publishable_key" => "pk_test_Jd2FRQtNDir8c7VUp2FbH65F"
);


//END STRIPE

?>