<?php
ob_start(); 
require_once $_SERVER['DOCUMENT_ROOT'].'/ecom/core/init.php';
if(!is_logged_in()){
	login_error_redirect();
}
include 'includes/head.php';
include 'includes/navigation.php';

//DELETE Product
if(isset($_GET['delete'])){
	$id = sanitize($_GET['delete']);
	$db->query("UPDATE products SET deleted='1', featured='0' where id='$id'");
	header ('Location: products.php');
}
///////////////
$dbPath = '';
if(isset($_GET['add']) || isset($_GET['edit'])){
	$brandQuery = $db->query("SELECT * FROM brand order by brand");
	$parentQuery = $db->query("SELECT * FROM categories WHERE parent= 0 order by category");
		
	$title = ((isset($_POST['title']) && $_POST['title'] !='')?sanitize($_POST['title']):'');
	$brand=((isset($_POST['brand']) && $_POST['brand'] !='')?sanitize($_POST['brand']):'');
	$parent=((isset($_POST['parent']) && $_POST['parent'] !='')?sanitize($_POST['parent']):'');
	$category=((isset($_POST['child']) && $_POST['child'] !='')?sanitize($_POST['child']):'');
	$price = ((isset($_POST['price']) && $_POST['price'] !='')?sanitize($_POST['price']):'');
	$list_price = ((isset($_POST['list_price']) && $_POST['list_price'] !='')?sanitize($_POST['list_price']):'');
	$description = ((isset($_POST['description']) && $_POST['description'] !='')?sanitize($_POST['description']):'');
	$sizes = ((isset($_POST['sizes']) && $_POST['sizes'] !='')?sanitize($_POST['sizes']):'');
	 $sizes=rtrim($sizes,',');
	$saved_image = '';
		if(isset($_GET['edit'])){
			$edit_id = (int)$_GET['edit'];
			$productResult = $db->query("SELECT * FROM products where id = '$edit_id'");
			$product = mysqli_fetch_assoc($productResult);
			//Used by both ADD and EDIT, ADD uses if post value exsists, if it doesnt then it is used by edit
			if(isset($_GET['delete_image'])){
				$imgi = (int)$_GET['imgi'] - 1;
				$images =  explode(',',$product['image']);
				$image_url = $_SERVER['DOCUMENT_ROOT'].$images[$imgi];
				unlink($image_url);
				unset($images[$imgi]);
				$imageString = implode(',',$images);
				$db->query("UPDATE products SET image='{$imageString}' WHERE id='$edit_id'");
				header ('Location: products.php?edit='.$edit_id);
			}
			$category = ((isset($_POST['child']) && $_POST['child'] !='')?sanitize($_POST['child']):$product['categories']);
			$title = ((isset($_POST['title']) && !empty($_POST['title']))?sanitize($_POST['title']):$product['title']);
			$brand = ((isset($_POST['brand']) && !empty($_POST['brand']))?sanitize($_POST['brand']):$product['brand']);
			 $parentQ = $db->query("SELECT * FROM categories WHERE id='$category'");
			 $parentResult = mysqli_fetch_assoc($parentQ);	
			$parent = ((isset($_POST['parent']) && !empty($_POST['parent']))?sanitize($_POST['parent']):$parentResult['parent']);
			$price = ((isset($_POST['price']) && !empty($_POST['price']))?sanitize($_POST['price']):$product['price']);
			$list_price = ((isset($_POST['list_price']))?sanitize($_POST['list_price']):$product['list_price']);
			$description = ((isset($_POST['description']))?sanitize($_POST['description']):$product['description']);
			$sizes = ((isset($_POST['sizes']) && !empty($_POST['sizes']))?sanitize($_POST['sizes']):$product['sizes']);
			 $sizes=rtrim($sizes,',');
			$saved_image = (($product['image'] != '')?$product['image']:'');
			$dbPath = $saved_image;
		}
		if (!empty($sizes)){
			$sizeString = sanitize($sizes);
			$sizeString = rtrim($sizeString,','); //USED to trim the comma from the end of preview generated in javascript
			$sizesArray = explode(',',$sizeString);
			
			$sArray = array();
			$qArray = array();
			$tArray = array();
			
			foreach ($sizesArray as $ss){
				$s = explode(':',$ss);
				$sArray[] = $s[0];
				$qArray[] = $s[1];
				$tArray[] = $s[2];
			}
			
		}else{
			$sizesArray = array();
			}
		
	if($_POST){
		
		$errors = array();
			
		$required = array('title', 'brand' , 'price', 'parent', 'child' ,'sizes');
		$allowed = array('png','jpg','jpeg','gif');
		$tmpLoc = array();
		$uploadPath = array();
		foreach ($required as $field){
			if($_POST[$field] == ''){
				$errors[] = 'All Fields With an Asterisk are Required!';
				break;
			}
		}
		
		//var_dump($_FILES['photo']); //die();
		$size_sum = array_sum($_FILES['photo']['size']);
		if ($size_sum > 0){
		
		$photoCount = count($_FILES['photo']['name']);
		//echo $photoCount;
		//var_dump($_FILES['photo']['name']); die();
		
		if($photoCount > 0){
			for($i = 0; $i < $photoCount; $i++){
					
				$name=$_FILES['photo']['name'][$i]; 
				$nameArray = explode('.',$name); 
				$filename= $nameArray[0];
				$fileExtension = $nameArray[1];
				$mime = explode('/', $_FILES['photo']['type'][$i]);
				$mimeType = $mime[0];
				$mimeExtension = $mime[1];
				$tmpLoc[] = $_FILES['photo']['tmp_name'][$i];
				$fileSize= $_FILES['photo']['size'][$i];
				
								
				$uploadName = md5(microtime().$i).'.'.$fileExtension;
				$uploadPath[] = BASEURL.'images/products/'.$uploadName;
				if($i != 0 ){
					$dbPath .= ',';
				}
				$dbPath .= '/ecom/images/products/'.$uploadName;
				
				if($mimeType != 'image'){
					$errors[] = 'The File Must be an Image.';
				}
				
				if(!in_array($fileExtension, $allowed)){
					$errors[] = 'The file extension must be a PNG, JPG, JPEG or GIF.';
				}
				
				//IF File Size is under 15MBs
				if($fileSize > 15000000){
					$errors[] = 'The file size must be under 15MBs.';
				}
				
				if($fileExtension != $mimeExtension && ($mimeExtension == 'jpeg' && $fileExtension != 'jpg')){
					$errors[] = 'File extension does not match the file';
				}
			
			}
		}
		}
		if(!empty($errors)){
			echo display_errors($errors);
		}else{
			//Upload file and insert into DB
			if($photoCount > 0){
				for($i=0; $i < $photoCount; $i++){
			move_uploaded_file($tmpLoc[$i], $uploadPath[$i]);
				}
			}
			$insertSql = "INSERT INTO products (`title`, `price`, `list_price`, `brand`, `categories`, `sizes`, `image`, `description`) 
			VALUES ('$title', '$price', '$list_price','$brand','$category','$sizes','$dbPath', '$description')";
			if(isset($_GET['edit'])){
				$insertSql = "UPDATE products SET title='$title', price='$price', list_price='$list_price', brand='$brand', categories='$category',
				sizes = '$sizes', image='$dbPath', description='$description' WHERE id='$edit_id'";
			}
			$db->query($insertSql);
			header('Location: products.php');
		}
	}
	
?>	 

	<h2 class="text-center"><?=((isset($_GET['edit']))?'Edit ':'Add ') ;?>Product</h2><hr>
	
	<form action="products.php?<?=((isset($_GET['edit']))?'edit='.$edit_id:'add=1') ;?>" method="post" enctype="multipart/form-data">
		
		<div class="form-group col-md-3">
			<label for="title">Title*:</label>
			<input type="text" class="form-control" name="title" id="title" value="<?=$title; ?>"/>
		</div>
		
		<div class="form-group col-md-3">
			<label for="brand">Brand*:</label>
			<select class="form-control" id="brand" name="brand">
				<option value=""<?=(( $brand == '')?' selected':'');?>></option>
				<?php while($b=mysqli_fetch_assoc($brandQuery)) : ?>
					<option value="<?= $b['id']; ?>"<?=(($brand== $b['id'])?' selected':'');?>><?= $b['brand'];?></option>
				<?php endwhile; ?>
			</select>
		</div>
		
		<div class="form-group col-md-3">
			<label for="parent">Parent Category*:</label>
			<select class="form-control" id="parent" name="parent">
				<option value=""<?=(($parent == '')?' selected':'');?>></option>
				<?php while($p=mysqli_fetch_assoc($parentQuery)) : ?>
					<option value="<?= $p['id']; ?>"<?=(($parent == $p['id'])?' selected':'');?>><?= $p['category'];?></option>
				<?php endwhile; ?>
			</select>
		</div>
		
		<div class="form-group col-md-3">
			<label for="child">Child Category*:</label>
			<select class="form-control" id="child" name="child">
			</select>
		</div>
		
		
		<div class="form-group col-md-3">
			<label for="price">Price*:</label>
			<input type="text" id="price" name="price" class="form-control" value="<?=$price; ?>" />
		</div>
		
		<div class="form-group col-md-3">
			<label for="list_price">List Price:</label>
			<input type="text" id="list_price" name="list_price" class="form-control" value="<?=$list_price; ?>" />
		</div>
		
		<div class="form-group col-md-3">
		<label for="">Quantity and Sizes*:</label>
			<input type="button" class="btn btn-default form-control" onClick="jQuery('#sizesmodal').modal('toggle');return false;" value="Quantity and Sizes"/>
		</div>
		
		<div class="form-group col-md-3">
		<label for="sizes">Quantity and Sizes Preview:</label>
		<input type="text" name="sizes" id="sizes" class="form-control"  value="<?=$sizes ; ?>" readonly />
		</div>
		
		
		<div class="form-group col-md-6">
		<?php if($saved_image != '') :
			$imgi = 1 ;
			$images=explode(',',$saved_image);
			 foreach($images as $image): ?>
			<div class="saved-image col-md-4">
				<img src="<?=$image; ?>" alt="saved image"/><br>
				<a href="products.php?delete_image=1&edit=<?= $edit_id ; ?>&imgi=<?=$imgi ;?>" class="text-danger">Delete Image</a>
			</div>
			<?php 
			$imgi++;
			endforeach; 
		else: ?>
		<label for="photo">Product Photo:</label>
		<input type="file" name="photo[]" id="photo[]" class="form-control" multiple />
		<?php endif; ?>
		</div>
		
		<div class="form-group col-md-6">
		<label for="description">Description:</label>
		<textarea name="description" id="description" class="form-control" rows="3"><?=$description ; ?></textarea>
		</div>
		
		<div class="form-group pull-right">
		<a href="products.php" class="btn btn-default">Cancel</a>
		<input type="submit" value="<?=((isset($_GET['edit']))?'Edit ':'Add ') ;?> Product" class="btn btn-success" />
		</div>
		<div class="clearfix">
		</div>
	</form>
	
	<!-- Modal -->
<div class="modal fade" id="sizesmodal" tabindex="-1" role="dialog" aria-labelledby="sizesmodalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="sizesmodalLabel">Size and Quantity</h4>
      </div>
      <div class="modal-body">
	<form>
		<div class="container-fluid">
			<?php for($i=1; $i <= 12; $i++): ?>
				<div class="form-group col-md-2">
					<label for="size<?= $i; ?>">Size:</label>
					<input type="text" name="size<?= $i; ?>" id="size<?= $i; ?>" value="<?=((!empty($sArray[$i - 1]))?$sArray[$i-1]:'') ;?>" class="form-control"/>
				</div>
				<div class="form-group col-md-2">
					<label for="qty<?= $i; ?>">Quanitity:</label>
					<input type="number" name="qty<?= $i; ?>" id="qty<?= $i; ?>" value="<?=((!empty($qArray[$i - 1]))?$qArray[$i-1]:'') ;?>" min="0" class="form-control"/>
				</div>
				<div class="form-group col-md-2">
					<label for="threshold<?= $i; ?>">Threshold:</label>
					<input type="number" name="threshold<?= $i; ?>" id="threshold<?= $i; ?>" value="<?=((!empty($tArray[$i - 1]))?$tArray[$i-1]:'') ;?>" min="0" class="form-control"/>
				</div>
			<?php endfor; ?>
		</div>
		
      </div>
      <div class="modal-footer">
		
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary"  onClick="updatesize(); jQuery('#sizesmodal').modal('toggle');return false;">Save changes</button>
		</form>
      </div>
    </div>
  </div>
</div>
<?php

//DISPLAY Products

}else{
	

$sql = "select * from products WHERE deleted = 0";
$presults = $db->query($sql);

if(isset($_GET['featured'])){
	$id= (int)$_GET['id'];
	$featured = (int)$_GET['featured'];
	
	$featuredsql = "UPDATE products SET featured ='$featured' WHERE id='$id'";
	$db->query($featuredsql);
	header ('Location: products.php');
}

?>
<h2 class="text-center">Products</h2>
<a href="products.php?add=1" class="btn btn-success pull-right" id="add-product-btn">Add Product</a>
<div class="clearfix"></div>
<hr>
<table class="table table-bordered table-condensed table-striped">
<thead>
	<th></th>
	<th>Product</th>
	<th>Price</th>
	<th>Category</th>
	<th>Featured</th>
	<th>Sold</th>
</thead>
<tbody>
	<?php while($product = mysqli_fetch_assoc($presults)) : 
		$childID = $product['categories'];
		$catSql = "SELECT * FROM categories WHERE id='$childID'";
		$result = $db->query($catSql);
		$child = mysqli_fetch_assoc($result);
		$parentID = $child['parent'];
		$psql = "SELECT * FROM categories where id='$parentID'";
		$presult = $db->query($psql);
		$parent = mysqli_fetch_assoc($presult);
		$category = $parent['category'].'-'.$child['category'];
	?>
	
	<tr>
		<td>
			<a href="products.php?edit=<?= $product['id'];?>" class="btn btn-xs btn-default"><span class="glyphicon glyphicon-pencil"></span></a>
			<a href="products.php?delete=<?= $product['id'];?>" class="btn btn-xs btn-default"><span class="glyphicon glyphicon-remove"></span></a>
		</td>
		<td><?= $product['title'];?></td>
		<td><?= money($product['price']);?></td>
		<td><?= $category;?></td>
		<td><a href="products.php?featured=<?=(($product['featured'] == 0)?'1':'0'); ?>&id=<?=$product['id']; ?>" class="btn btn-xs btn-default">
			<span class="glyphicon glyphicon-<?=(($product['featured'] == 1)?'minus':'plus'); ?>"></span>
			</a>
			&nbsp <?=(($product['featured'] == 1)?'Featured Product':'Feature Product'); ?></td>
		<?php
		$a = array();
		$arr = array();
		$cartQ = $db->query("SELECT cart.items FROM transactions inner join cart on transactions.cart_id = cart.id");
		while($result = mysqli_fetch_array($cartQ)){
		$items = json_decode($result['items'], true);
			foreach ($items as $key => $value) {
				$a[] = $value;
		  }
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
		
		if(array_key_exists($product['id'], $final)){
		 foreach($final as  $f){
		
			  if($f['id'] == $product['id']){
					?>
					<td><?=(($f['id'] != $product['id'])?'0':$f['quantity']); ?></td>
					<?php
				}
			 
			
		 }
		}else{
			echo '<td>0</td>';
		}
	 
		 ?>
		
	
	</tr>
	
	
	<?php endwhile; ?>
</tbody>
</table> 

<?php
} include 'includes/footer.php';

ob_flush();
?>

<script>
	jQuery('document').ready(function(){
		get_child_options('<?=$category ;?>');
	});
</script>
