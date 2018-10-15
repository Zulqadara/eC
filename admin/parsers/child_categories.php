<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/ecom/core/init.php';

$parentID = (int)$_POST['parentID'];//comes from ajax post
$childQuery = $db->query("SELECT * FROM categories WHERE parent='$parentID' order by category");

$selected = sanitize($_POST['selected']);

ob_start();
?>

<option value=""></option>
<?php while($child = mysqli_fetch_assoc($childQuery)) : ?>
<option value="<?= $child['id']; ?>"<?=(($selected == $child['id'])?' selected':'') ;?>><?= $child['category']; ?></option>
<?php endwhile; ?>

<?php
echo ob_get_clean();
?>