<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/ecom/core/init.php';
include 'includes/head.php';

$email = ((isset($_POST['email']))?sanitize($_POST['email']):'');
$email = trim($email);
$password = ((isset($_POST['password']))?sanitize($_POST['password']):'');
$password = trim($password);

$errors = array();
?>
<style>
	body{
		background-image:url("/ecom/images/headerlogo/background.png");
		background-size: 100vw 100vh;
		background-attachment: fixed;
	}
</style>
<div id="login-form">
	<div>
	<?php
		if($_POST){
			//form validation
			if(empty($_POST['email']) || empty($_POST['password'])){
				$errors[] = 'You must proivde email and password';
			}
			
			// Validate email
			if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
				$errors[] = 'Must enter a valid email';
			}
			
			//Password is more than 6 characters
			if(strlen($password)<6){
				$errors[] = 'The password must be atleast 6 charachetrs long.';
			}
			//If user exists in DB
			$query = $db->query("SELECT * FROM users WHERE email='$email'");
			$user = mysqli_fetch_assoc($query);
			$userCount = mysqli_num_rows($query);
			if($userCount < 1){
				$errors[] = 'Email doesnt exist in the system. (replace with email/passowrd inncorect)';
			}
			//Hash password review
			if(!password_verify($password, $user['password'])){
				$errors[] = 'The password doesnt match the records, try again. (replace with email/passowrd inncorect)';
			}
			
			//Check for errors
			if(!empty($errors)){
				echo display_errors($errors);
			}else{
				//Log user in
				$user_id = $user['id'];
				login($user_id);
			}
		}
	?>
	</div>
	<h2 class="text-center">Login</h2><hr>
	<form action="login.php" method="post">
		<div class="form-group">
			<label for="email">Email:</label>
			<input type="email" class="form-control" name="email" id="email" value="<?=$email; ?>"/>
		</div>
		<div class="form-group">
			<label for="password">Password:</label>
			<input type="password" class="form-control" name="password" id="password" value="<?=$password; ?>"/>
		</div>
		<div class="form-group">
			<input type="submit" class="btn btn-primary" value="Login"/>
		</div>
	</form>
	<p class="text-right"><a href="/ecom/index.php" alt="home">Visit Site</a></p>
</div>

<?php
include 'includes/footer.php';
?>