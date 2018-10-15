<?php
require_once '../core/init.php';
if(!is_logged_in()){
	login_error_redirect();
}
include 'includes/head.php';
include 'includes/navigation.php';
//get brands from DB
$sql = "SELECT * FROM brand order by brand ASC";
$results = $db->query($sql);

$errors = array();

//Edit Brand

if(isset($_GET['edit']) && !empty($_GET['edit'])){
	$editid = (int)$_GET['edit'];
	$editid = sanitize($editid);
	
	$sql2 = "SELECT * FROM brand WHERE id='$editid'";
	$editresult = $db ->query($sql2);
	$eBrand = mysqli_fetch_assoc($editresult);
}

//Delete brand
if(isset($_GET['delete']) && !empty($_GET['delete'])){
	$deleteid = (int)$_GET['delete'];
	$deleteid = sanitize($deleteid);
	
	$sql = "DELETE FROM brand WHERE id='$deleteid'";
	$db ->query($sql);
	header ('Location: brands.php');
}


//If add form is submitted
if(isset($_POST['add_submit'])){
	$brand = sanitize($_POST['brand']);
	//check if brand is blank
	if($_POST['brand'] == ''){
		$errors[] .= 'You must enter a brand';
	}
	// check if brand exists in DB
	$sql = "SELECT * FROM brand WHERE brand = '$brand'";
	if(isset($_GET['edit'])){
		$sql = "SELECT * FROM brand WHERE brand='$brand' AND id!= '$editid'";
	}
	$result = $db ->query($sql);
	$count = mysqli_num_rows($result);
	if($count > 0){
		$errors[] .= $brand.' already exists, choose another brand name';
	}
	//display errors
	if(!empty($errors)){
		echo display_errors($errors);
	}else{
	//Add brand to DB
	$sql = "INSERT INTO brand (brand) VALUES ('$brand')";
		if(isset($_GET['edit'])){
		$sql = "UPDATE brand SET brand='$brand' WHERE id= '$editid'";
	}
	$db ->query($sql);
	header('location: brands.php');
	}
}
?>
<h2 class="text-center">Brands</h2><hr>
<!-- Brand Form -->
<div class="text-center">
	<form class="form-inline" action="brands.php<?=((isset($_GET['edit'])))?'?edit='.$editid:''; ?>" method="post">
		<div class="form-group">
			<?php 
			
			$brandvalue ='';
			
			if(isset($_GET['edit'])){
				$brandvalue = $eBrand['brand'];
				
			}else{
				
				if(isset($_POST['brand'])){
					$brandvalue = sanitize($_POST['brand']);
				}
				
			}
			
			?>
			<label for="brand"><?=((isset($_GET['edit'])))?'Edit':'Add'; ?> Brand</label>
			<input type="text" name="brand" id="brand" class="form-control" value="<?= $brandvalue;  ?>"/>
			<?php if(isset($_GET['edit'])) : ; ?>
			<a href ="brands.php" class="btn btn-default">Cancel</a>
			<?php endif; ?>
			<input type="submit" name="add_submit" value="<?=((isset($_GET['edit'])))?'Edit':'Add'; ?> Brand" class="btn btn-success"/>
		</div>
	</form>
</div><hr>
<table class="table table-bordered stable-striped table-auto table-condensed">
	<thead>
		<th></th>
		<th>Brand</th>
		<th></th>
		
	</thead>
	<tbody>
	<?php while($brand = mysqli_fetch_assoc($results)) : ?>
		<tr>
			<td><a href="brands.php?edit=<?=$brand['id']; ?>" class="btn btn-xs btn-default"><span class="glyphicon glyphicon-pencil"></span></a></td>
			<td><?=$brand['brand']; ?></td>
			<td><a href="brands.php?delete=<?=$brand['id']; ?>" class="btn btn-xs btn-default"><span class="glyphicon glyphicon-remove-sign"></span></a></td>
			
		</tr>
		<?php endwhile; ?>
	</tbody>
</table>
<?php
include 'includes/footer.php';
?>