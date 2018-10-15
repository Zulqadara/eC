<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/ecom/core/init.php';
$name = sanitize($_POST['full_name']);
$email = sanitize($_POST['email']);
$street = sanitize($_POST['street']);
$street2 = sanitize($_POST['street2']);
$city = sanitize($_POST['city']);
$state = sanitize($_POST['state']);
$zip = sanitize($_POST['zip']);
$country = sanitize($_POST['country']);

$errors = array();
//Assosiative array
$required = array(
	'full_name' => 'Full Name',
	'email' => 'Email',
	'street' => 'Street',
	'city' => 'City',
	'state' => 'State',
	'zip' => 'Zip Code',
	'country' => 'Country',
);

//Check if all required fields are filled out
foreach($required as $f => $d){
	if(empty($_POST[$f]) || $_POST[$f] == ''){
		$errors[] = $d.' is required';
	}
}

//Check if valid email address
if(!FILTER_VAR($email, FILTER_VALIDATE_EMAIL)){
	$errors[] = 'Please enter a Valid Email';
}

if(!empty($errors)){
	echo display_errors($errors);
}else{
	echo 'passed';
}

?>