<?php $pageTitle = "Photo App - Treefunk"; ?>
<?php require_once '../../private/init.php'; ?>
<?php require_once PRIVATE_PATH.'/includes/header.php'; ?>


<?php
if($_POST){
    uploadFile($_FILES['photo'],$_POST);
}
?>

<?php if($errors): ?>
Errors:
<ul>
    <?php foreach($errors as $error): ?>
        <li><?=$error?></li>
    <?php endforeach; ?>
</ul>
<?php endif; ?>



<form action="" method="post" enctype="multipart/form-data">
    <label for="photo">Photo:</label><input type="file" name="photo" id="photo">
    <label for="caption">caption</label><input type="text" name="caption" id="caption">
    <button type="submit">Submit</button>
    <div class=""></div>
</form>

<?php require_once PRIVATE_PATH.'/includes/footer.php'; ?>