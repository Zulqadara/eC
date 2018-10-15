<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/ecom/core/init.php';
if(!is_logged_in()){
	login_error_redirect();
}
include 'includes/head.php';

$hashed = $user_data['password'];
$old = ((isset($_POST['old']))?sanitize($_POST['old']):'');
$old = trim($old);
$password = ((isset($_POST['password']))?sanitize($_POST['password']):'');
$password = trim($password);
$confirm = ((isset($_POST['confirm']))?sanitize($_POST['confirm']):'');
$confirm = trim($confirm);
$new_hashed = password_hash($password, PASSWORD_DEFAULT);
$userid = $user_data['id'];
$errors = array();
?>

<div id="login-form">
	<div>
	<?php
		if($_POST){
			//form validation
			if(empty($_POST['old']) || empty($_POST['password']) || empty($_POST['confirm'])){
				$errors[] = 'You must fill out fields';
			}
			
			//Password is more than 6 characters
			if(strlen($password)<6){
				$errors[] = 'The password must be atleast 6 charachetrs long.';
			}
			
			//New password matches confirm
			if($password != $confirm){
				$errors[] = 'The passwords do not match';
			}
			
			//Hash password review
			if(!password_verify($old, $hashed)){
				$errors[] = 'Your old password doesnt  match the records';
			}
			
			//Check for errors
			if(!empty($errors)){
				echo display_errors($errors);
			}else{
				//Change password
				$db->query("UPDATE users SET password = '$new_hashed' where id='$userid'");
				$_SESSION['success_flash']= 'Your password has been updated' ;
				header ('Location: index.php');

			}
		}
	?>
	</div>
	<h2 class="text-center">Change Password</h2><hr>
	<form action="change_password.php" method="post">
		<div class="form-group">
			<label for="old">Old Password:</label>
			<input type="password" class="form-control" name="old" id="old" value="<?=$old; ?>"/>
		</div>
		<div class="form-group">
			<label for="password">New Password:</label>
			<input type="password" class="form-control" name="password" id="password" value="<?=$password; ?>"/>
		</div>
		<div class="form-group">
			<label for="confirm">Confirm Password:</label>
			<input type="password" class="form-control" name="confirm" id="confirm" value="<?=$confirm; ?>"/>
		</div>
		<div class="form-group">
			<a href="index.php" class="btn btn-default">Cancel</a>
			<input type="submit" class="btn btn-primary" value="Login"/>
		</div>
	</form>
	<p class="text-right"><a href="/ecom/index.php" alt="home">Visit Site</a></p>
</div>

<?php
include 'includes/footer.php';
?>